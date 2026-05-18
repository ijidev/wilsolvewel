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
    $attachment = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('ticket_cl_') . '.' . $ext;
        $upload_path = '../uploads/tickets/' . $filename;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
            $attachment = $filename;
        }
    }
    
    if (empty($message) && !$attachment) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']); exit;
    }

    // Verify ownership
    $verify = $conn->query("SELECT id FROM tickets WHERE id = $ticket_id AND client_id = $client_id");
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $attach_sql = $attachment ? "'$attachment'" : "NULL";
    $conn->query("INSERT INTO ticket_replies (ticket_id, sender_type, sender_id, message, attachment) VALUES ($ticket_id, 'Client', $client_id, '$message', $attach_sql)");
    $new_id = $conn->insert_id;

    // Notify all active admins
    $subject_res = $conn->query("SELECT subject FROM tickets WHERE id = $ticket_id");
    $t_subject = $subject_res ? $subject_res->fetch_assoc()['subject'] : 'Ticket';
    $admin_ids = $conn->query("SELECT id FROM admins WHERE status = 'Active'");
    if ($admin_ids) {
        while ($a = $admin_ids->fetch_assoc()) {
            create_notification($conn, 'admin', $a['id'], 'Client replied to ticket', htmlspecialchars($t_subject), 'admin/tickets.php?ticket_id=' . $ticket_id, 'forum');
        }
    }
    
    echo json_encode([
        'status' => 'success', 
        'reply' => [
            'id' => $new_id,
            'message' => $message,
            'attachment' => $attachment,
            'sender_type' => 'Client',
            'created_at' => date('M j, Y h:i A')
        ]
    ]);
}
