<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$conn = get_db_connection();
$ticket_id = (int)($_GET['id'] ?? 0);

if ($ticket_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Ticket ID']);
    exit;
}

// Verify ownership
$ticket_res = $conn->query("SELECT t.*, d.name as dept_name FROM tickets t LEFT JOIN departments d ON t.department_id = d.id WHERE t.id = $ticket_id AND t.client_id = $client_id");
$ticket = $ticket_res->fetch_assoc();

if (!$ticket) {
    echo json_encode(['status' => 'error', 'message' => 'Ticket not found or access denied']);
    exit;
}

// Fetch Replies
$replies_res = $conn->query("
    SELECT tr.*, 
           IF(tr.sender_type='Admin', a.name, c.name) as sender_name 
    FROM ticket_replies tr 
    LEFT JOIN admins a ON (tr.sender_type = 'Admin' AND tr.sender_id = a.id)
    LEFT JOIN clients c ON (tr.sender_type = 'Client' AND tr.sender_id = c.id)
    WHERE tr.ticket_id = $ticket_id ORDER BY tr.created_at ASC
");

$replies = [];
while ($r = $replies_res->fetch_assoc()) {
    $r['created_at'] = date('M j, Y h:i A', strtotime($r['created_at']));
    $replies[] = $r;
}

echo json_encode([
    'status' => 'success',
    'ticket' => $ticket,
    'replies' => $replies
]);
