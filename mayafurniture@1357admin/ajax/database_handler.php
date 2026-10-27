<?php
require '../../config/config.php';
require '../functions/authentication.php';

$auth = new AdminManager();
$auth->checkSession();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action.'];

$backupDir = __DIR__ . '/../database/backup/';
$recycleBinDir = __DIR__ . '/../database/recycle_bin/';

if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
if (!is_dir($recycleBinDir)) mkdir($recycleBinDir, 0777, true);

function getFiles($dir) {
    $files = [];
    if (!is_dir($dir)) return $files;
    $scannedFiles = array_diff(scandir($dir), ['.', '..']);
    foreach ($scannedFiles as $file) {
        $filePath = $dir . $file;
        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'sql') {
            $files[] = ['name' => $file, 'size' => filesize($filePath), 'modified' => filemtime($filePath)];
        }
    }
    return $files;
}

function moveFiles($files, $sourceDir, $destDir, $all = false) {
    if ($all) $files = array_column(getFiles($sourceDir), 'name');
    if (!is_array($files) || empty($files)) return false;
    $movedCount = 0;
    foreach ($files as $file) {
        $sourcePath = realpath($sourceDir . $file);
        if ($sourcePath && strpos($sourcePath, realpath($sourceDir)) === 0 && is_file($sourcePath)) {
            if (rename($sourcePath, $destDir . $file)) $movedCount++;
        }
    }
    return $movedCount > 0;
}

function deleteFiles($files, $dir, $all = false) {
    if ($all) $files = array_column(getFiles($dir), 'name');
    if (!is_array($files) || empty($files)) return false;
    $deletedCount = 0;
    foreach ($files as $file) {
        $filePath = realpath($dir . $file);
        if ($filePath && strpos($filePath, realpath($dir)) === 0 && is_file($filePath)) {
            if (unlink($filePath)) $deletedCount++;
        }
    }
    return $deletedCount > 0;
}

switch ($action) {
    case 'get_backups':
        $response = ['success' => true, 'backups' => getFiles($backupDir), 'recycled' => getFiles($recycleBinDir)];
        break;

    case 'get_backup_stats':
        $backups = getFiles($backupDir);
        $stats = ['by_month' => array_fill(0, 12, 0), 'last_backup_ago' => 'N/A'];
        $currentYear = date('Y');
        $latestTimestamp = 0;

        foreach ($backups as $file) {
            if ($file['modified'] > $latestTimestamp) $latestTimestamp = $file['modified'];
            if (date('Y', $file['modified']) == $currentYear) {
                $monthIndex = (int)date('m', $file['modified']) - 1;
                $stats['by_month'][$monthIndex]++;
            }
        }
        
        if ($latestTimestamp > 0) {
            $diff = time() - $latestTimestamp;
            if ($diff < 60) $stats['last_backup_ago'] = 'Just now';
            elseif ($diff < 3600) $stats['last_backup_ago'] = floor($diff / 60) . ' minutes ago';
            elseif ($diff < 86400) $stats['last_backup_ago'] = floor($diff / 3600) . ' hours ago';
            else $stats['last_backup_ago'] = floor($diff / 86400) . ' days ago';
        }
        
        $response = ['success' => true, 'stats' => $stats];
        break;

    case 'get_backup_content':
        $filename = $_GET['file'] ?? '';
        $sourceDir = ($_GET['from'] === 'recycle_bin') ? $recycleBinDir : $backupDir;
        $filePath = realpath($sourceDir . $filename);
        if ($filePath && strpos($filePath, realpath($sourceDir)) === 0 && is_file($filePath)) {
            $content = file_get_contents($filePath);
            $response = ['success' => true, 'content' => $content];
        } else { $response['message'] = 'File not found or access denied.'; }
        break;

    case 'move_to_recycle_bin':
        $files = $_POST['files'] ?? [];
        if (!empty($files) && moveFiles($files, $backupDir, $recycleBinDir)) {
            $response = ['success' => true, 'message' => 'Backup(s) moved to recycle bin.'];
        } else { $response['message'] = 'Failed to move backup(s).'; }
        break;

    case 'restore_from_recycle_bin':
        $files = $_POST['files'] ?? [];
        if (!empty($files) && moveFiles($files, $recycleBinDir, $backupDir)) {
            $response = ['success' => true, 'message' => 'Backup(s) restored successfully.'];
        } else { $response['message'] = 'Failed to restore backup(s).'; }
        break;
        
    case 'restore_all':
        if (moveFiles([], $recycleBinDir, $backupDir, true)) {
            $response = ['success' => true, 'message' => 'All items have been restored.'];
        } else { $response['message'] = 'Recycle bin is empty or an error occurred.'; }
        break;

    case 'delete_permanently':
        $files = $_POST['files'] ?? [];
        $from = $_POST['from'] ?? 'backup';
        $dir = ($from === 'recycle_bin') ? $recycleBinDir : $backupDir;
        if (!empty($files) && deleteFiles($files, $dir)) {
            $response = ['success' => true, 'message' => 'Backup(s) deleted permanently.'];
        } else { $response['message'] = 'Failed to delete backup(s).'; }
        break;
        
    case 'empty_recycle_bin':
        if (deleteFiles([], $recycleBinDir, true)) {
            $response = ['success' => true, 'message' => 'Recycle bin has been emptied.'];
        } else { $response['message'] = 'Recycle bin is already empty or an error occurred.'; }
        break;
}
echo json_encode($response);
?>