<?php
ob_start(); 
require '../config/config.php';
require 'functions/authentication.php';

$db = new dbClass();
$auth = new AdminManager();
$auth->checkSession();

header('Content-Type: application/json');

$conn = $db->conn; 

if (!$conn) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Database Connection Error.'));
    exit;
}

try {
    $db_name_stmt = $conn->query("SELECT DATABASE() as db_name");
    $db_name = isset($db_name_stmt->fetch(PDO::FETCH_ASSOC)['db_name']) ? $db_name_stmt->fetch(PDO::FETCH_ASSOC)['db_name'] : 'unknown_db';
    
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Error fetching database metadata: ' . $e->getMessage()));
    exit;
}

if (empty($tables)) {
    echo json_encode(array('success' => false, 'message' => 'No tables found in the database to backup.'));
    exit;
}

$sqlScript = "-- iRepair DB Backup\n-- Host: " . $_ENV['DB_HOST'] . "\n-- Database: " . $db_name . "\n-- Generation Time: " . date('Y-m-d H:i:s') . "\n\n";

foreach ($tables as $table) {
    try {
        $stmt = $conn->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n" . $row[1] . ";\n\n";

        $dataStmt = $conn->query("SELECT * FROM `$table`");
        $columnCount = $dataStmt->columnCount();
        
        while ($row = $dataStmt->fetch(PDO::FETCH_NUM)) {
            $sqlScript .= "INSERT INTO `$table` VALUES(";
            for ($j = 0; $j < $columnCount; $j++) {
                $sqlScript .= isset($row[$j]) ? $conn->quote($row[$j]) : 'NULL';
                if ($j < ($columnCount - 1)) $sqlScript .= ',';
            }
            $sqlScript .= ");\n";
        }
        $sqlScript .= "\n";
    } catch (PDOException $e) {
        error_log("Backup failed for table $table: " . $e->getMessage());
        continue;
    }
}

if (!empty($sqlScript)) {
    $now = date('Y-m-d_H-i-s');
    $sanitizedDbName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $db_name); 
    $filename = "{$now}-{$sanitizedDbName}.sql";

    $backupDir = __DIR__ . "/database/backup/";
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true); 
    }

    $backupFilePath = $backupDir . $filename;

    if (file_put_contents($backupFilePath, $sqlScript) === false) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Failed to write backup file to server. Check permissions.'));
        exit;
    }

    ob_end_clean();
    echo json_encode(array('success' => true, 'message' => 'Database backup created successfully!', 'filename' => $filename));
} else {
    echo json_encode(array('success' => false, 'message' => 'No data was backed up.'));
}
exit;
?>