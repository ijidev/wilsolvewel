<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

header('Content-Type: application/json');

$recipient_type = $_GET['type'] ?? '';
$recipient_id = (int)($_GET['id'] ?? 0);
$last_id = (int)($_GET['last_id'] ?? 0);

if (!$recipient_type || !$recipient_id) {
    if (isset($_SESSION['admin_id'])) {
        $recipient_type = 'admin';
        $recipient_id = (int)$_SESSION['admin_id'];
    } elseif (isset($_SESSION['client_id'])) {
        $recipient_type = 'client';
        $recipient_id = (int)$_SESSION['client_id'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
        exit;
    }
}

$conn = get_db_connection();

$unread = 0;
$r = safe_query($conn, "SELECT COUNT(*) as c FROM notifications WHERE recipient_type = ? AND recipient_id = ? AND is_read = 0", "si", [$recipient_type, $recipient_id]);
if ($r) $unread = (int)$r->fetch_assoc()['c'];

$notifications = [];
$q = "SELECT id, title, message, link, icon, is_read, created_at FROM notifications WHERE recipient_type = ? AND recipient_id = ? AND id > ? ORDER BY created_at DESC LIMIT 20";
$r = safe_query($conn, $q, "sii", [$recipient_type, $recipient_id, $last_id]);
if ($r) {
    while ($row = $r->fetch_assoc()) $notifications[] = $row;
}

echo json_encode([
    'status' => 'success',
    'unread' => $unread,
    'notifications' => $notifications
]);
