<?php
ob_start();
error_reporting(0);
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$order_ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
$order_ids = array_map('intval', $order_ids);
$order_ids = array_filter($order_ids);

if (empty($order_ids)) {
    echo json_encode(['status' => 'error', 'message' => 'No orders selected']);
    exit;
}

try {
    $conn = get_db_connection();

    // IDs are already int-validated, safe for direct SQL
    $id_list = implode(',', $order_ids);

    // Verify ownership and fetch orders
    $sql = "SELECT po.*, p.name as project_name FROM procurement_orders po
            LEFT JOIN projects p ON po.project_id = p.id
            WHERE po.id IN ($id_list) AND COALESCE(p.client_id, po.client_id, 0) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $orders_res = $stmt->get_result();

    $orders = [];
    while ($row = $orders_res->fetch_assoc()) {
        $oid = $row['id'];

        // Fetch tracking history
        $tracking = [];
        $t_stmt = $conn->prepare("SELECT status, location, notes, created_at FROM procurement_history WHERE order_id = ? ORDER BY created_at DESC");
        $t_stmt->bind_param("i", $oid);
        $t_stmt->execute();
        $t_res = $t_stmt->get_result();
        while ($t = $t_res->fetch_assoc()) {
            $t['created_at'] = date('M d, Y H:i', strtotime($t['created_at']));
            $tracking[] = $t;
        }
        $t_stmt->close();

        // Fetch associated tickets
        $tickets = [];
        $tk_stmt = $conn->prepare("SELECT id, subject, status, created_at FROM tickets WHERE client_id = ? AND order_id = ? ORDER BY created_at DESC");
        $tk_stmt->bind_param("ii", $client_id, $oid);
        $tk_stmt->execute();
        $tk_res = $tk_stmt->get_result();
        while ($tk = $tk_res->fetch_assoc()) {
            $tk['created_at_formatted'] = date('M d, Y H:i', strtotime($tk['created_at']));
            // Fetch replies for this ticket
            $replies = [];
            $r_stmt = $conn->prepare("SELECT sender_type, message, created_at FROM ticket_replies WHERE ticket_id = ? ORDER BY created_at ASC");
            $r_stmt->bind_param("i", $tk['id']);
            $r_stmt->execute();
            $r_res = $r_stmt->get_result();
            while ($r = $r_res->fetch_assoc()) {
                $r['created_at_formatted'] = date('M d, Y H:i', strtotime($r['created_at']));
                $replies[] = $r;
            }
            $r_stmt->close();
            $tk['replies'] = $replies;
            $tickets[] = $tk;
        }
        $tk_stmt->close();

        $row['tracking'] = $tracking;
        $row['tickets'] = $tickets;
        $orders[] = $row;
    }
    $stmt->close();

    ob_clean();
    echo json_encode(['status' => 'success', 'orders' => $orders]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
