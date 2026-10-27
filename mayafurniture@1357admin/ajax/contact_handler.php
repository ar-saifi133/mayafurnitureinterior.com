<?php
if (!isset($_SESSION)) {
    session_start();
}
require '../../config/config.php';
require '../functions/contact_queries.php';
require '../functions/authentication.php';

header('Content-Type: application/json');

$auth = new AdminManager();
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$action = $_POST['action'] ?? '';
$contactAdmin = new ContactQueries();

if ($action === 'delete_inquiry') {
    $ids = $_POST['ids'] ?? [];
    $result = $contactAdmin->deleteInquiry($ids);
    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
?>
