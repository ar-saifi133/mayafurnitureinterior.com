<?php
require '../../config/config.php';
require '../functions/fileUploader.php';
require '../functions/authentication.php';

$adminManager = new AdminManager();
$adminManager->checkSession(); 

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$adminId = $_SESSION['ADMIN_USER_ID'];
$response = ['success' => false, 'message' => 'Invalid action specified.'];

switch ($action) {
    case 'update_profile':
        $file = $_FILES['image'] ?? null;
        $response = $adminManager->updateProfileDetails($adminId, $_POST, $file);
        break;
        
    case 'change_password':
        $response = $adminManager->changePassword($adminId, $_POST['current_password'] ?? '', $_POST['new_password'] ?? '');
        break;
}

echo json_encode($response);
?>
