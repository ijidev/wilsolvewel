<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
ob_start();

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
    $verify_sql = "SELECT po.id FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE po.id = ? AND (p.client_id = ? OR po.client_id = ?)";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("iii", $order_id, $client_id, $client_id);
    $verify_stmt->execute();
    $verify_res = $verify_stmt->get_result();

    if ($verify_res && $verify_res->num_rows > 0) {
        $history_sql = "SELECT status, location, notes, created_at FROM procurement_history WHERE order_id = ? ORDER BY created_at DESC";
        $history_stmt = $conn->prepare($history_sql);
        $history_stmt->bind_param("i", $order_id);
        $history_stmt->execute();
        $history_res = $history_stmt->get_result();
        
        $history = [];
        if ($history_res) {
            while($row = $history_res->fetch_assoc()) {
                $row['created_at'] = date('M d, Y H:i', strtotime($row['created_at']));
                $history[] = $row;
            }
        }
        $history_stmt->close();
        $verify_stmt->close();
        ob_clean();
        echo json_encode($history);
    } else {
        $verify_stmt->close();
        ob_clean();
        echo json_encode([]);
    }
} catch (Exception $e) {
    ob_clean();
    echo json_encode([]);
}
