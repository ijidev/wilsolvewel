<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    echo json_encode([]);
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id === 0) {
    echo json_encode([]);
    exit;
}

try {
    $conn = get_db_connection();
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    // Security: Verify the order belongs to the client or their projects
    $verify_res = $conn->query("
        SELECT po.id 
        FROM procurement_orders po 
        LEFT JOIN projects p ON po.project_id = p.id 
        WHERE po.id = $order_id AND (p.client_id = $client_id OR po.requested_by = $client_id)
    ");

    if ($verify_res && $verify_res->num_rows > 0) {
        $history_res = $conn->query("
            SELECT status, location, tracking_id, notes, created_at 
            FROM procurement_history 
            WHERE order_id = $order_id 
            ORDER BY created_at DESC
        ");
        
        $history = [];
        if ($history_res) {
            while($row = $history_res->fetch_assoc()) {
                $row['created_at'] = date('M d, Y H:i', strtotime($row['created_at']));
                $history[] = $row;
            }
        }
        echo json_encode($history);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
