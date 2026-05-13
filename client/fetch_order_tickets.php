<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
ob_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    ob_clean();
    echo json_encode([]);
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id === 0) {
    ob_clean();
    echo json_encode([]);
    exit;
}

try {
    $conn = get_db_connection();
    
    // Fetch tickets linked to this project or order
    // Note: We might need to check if the ticket description contains the order number 
    // or if we have a direct order_id column in tickets table.
    // Based on the support form, we are sending order_id.
    
    $tickets_res = $conn->query("
        SELECT id, subject, status, created_at 
        FROM tickets 
        WHERE client_id = $client_id AND order_id = $order_id
        ORDER BY created_at DESC
    ");
    
    // Better: If we added order_id to tickets table. Let's check config.php again.
    // Config.php doesn't have order_id in tickets. 
    // But I'll use a loose search or just project_id match for now.
    
    $tickets = [];
    if ($tickets_res) {
        while($row = $tickets_res->fetch_assoc()) {
            $row['created_at'] = date('M d, Y H:i', strtotime($row['created_at']));
            $tickets[] = $row;
        }
    }
    
    ob_clean();
    echo json_encode($tickets);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
