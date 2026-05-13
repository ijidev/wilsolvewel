<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = (int)$_POST['ticket_id'];
    $message = $conn->real_escape_string(trim($_POST['message']));
    
    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']); exit;
    }

    // Verify ownership
    $verify = $conn->query("SELECT id FROM tickets WHERE id = $ticket_id AND client_id = $client_id");
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $conn->query("INSERT INTO ticket_replies (ticket_id, sender_type, sender_id, message) VALUES ($ticket_id, 'Client', $client_id, '$message')");
    $new_id = $conn->insert_id;
    
    // If ticket was Resolved, maybe re-open it? 
    // $conn->query("UPDATE tickets SET status='Open' WHERE id=$ticket_id AND status='Resolved'");

    echo json_encode([
        'status' => 'success', 
        'reply' => [
            'id' => $new_id,
            'message' => $message,
            'sender_type' => 'Client',
            'created_at' => date('M j, Y h:i A')
        ]
    ]);
}
