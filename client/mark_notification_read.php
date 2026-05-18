<?php
require_once __DIR__ . '/../config.php';
secure_session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'POST required']);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
    exit;
}

$conn = get_db_connection();

$type = $_POST['type'] ?? '';
$id = (int)($_POST['id'] ?? 0);

if ($type === 'all') {
    if (isset($_SESSION['admin_id'])) {
        $conn->query("UPDATE notifications SET is_read = 1 WHERE recipient_type = 'admin' AND recipient_id = " . (int)$_SESSION['admin_id']);
    } elseif (isset($_SESSION['client_id'])) {
        $conn->query("UPDATE notifications SET is_read = 1 WHERE recipient_type = 'client' AND recipient_id = " . (int)$_SESSION['client_id']);
    }
    echo json_encode(['status' => 'success']);
    exit;
}

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid params']);
