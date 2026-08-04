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

$project_id = (int)($_GET['id'] ?? 0);
if ($project_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid project']);
    exit;
}

try {
    $conn = get_db_connection();

    // Ownership + completion confirmation is required before the report summary can be downloaded
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ? AND client_id = ?");
    $stmt->bind_param("ii", $project_id, $client_id);
    $stmt->execute();
    $proj = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$proj) {
        echo json_encode(['status' => 'error', 'message' => 'Project not found']);
        exit;
    }
    if (empty($proj['client_confirmed_at'])) {
        echo json_encode(['status' => 'error', 'message' => 'Completion has not been confirmed yet. Confirm the project before downloading the summary.']);
        exit;
    }

    $pid = $proj['id'];

    // Report logs (milestones)
    $milestones = [];
    $m_stmt = $conn->prepare("SELECT * FROM project_milestones WHERE project_id = ? ORDER BY order_index ASC, created_at ASC");
    $m_stmt->bind_param("i", $pid);
    $m_stmt->execute();
    $m_res = $m_stmt->get_result();
    while ($m = $m_res->fetch_assoc()) $milestones[] = $m;
    $m_stmt->close();

    // Assets
    $assets = [];
    $a_stmt = $conn->prepare("SELECT a.name, a.file_path FROM assets a JOIN project_assets pa ON a.id = pa.asset_id WHERE pa.project_id = ?");
    $a_stmt->bind_param("i", $pid);
    $a_stmt->execute();
    $a_res = $a_stmt->get_result();
    while ($a = $a_res->fetch_assoc()) $assets[] = $a;
    $a_stmt->close();

    // Tickets
    $tickets = [];
    $tk_stmt = $conn->prepare("SELECT id, subject, status, created_at FROM tickets WHERE client_id = ? AND project_id = ? ORDER BY created_at DESC");
    $tk_stmt->bind_param("ii", $client_id, $pid);
    $tk_stmt->execute();
    $tk_res = $tk_stmt->get_result();
    while ($tk = $tk_res->fetch_assoc()) {
        $tk['created_at_formatted'] = date('M d, Y H:i', strtotime($tk['created_at']));
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

    // Report log entries (discussion)
    $reports = [];
    $rep_stmt = $conn->prepare("SELECT pr.*, IF(pr.sender_type='Admin', a.name, c.name) as sender_name FROM project_reports pr LEFT JOIN admins a ON (pr.sender_type = 'Admin' AND pr.sender_id = a.id) LEFT JOIN clients c ON (pr.sender_type = 'Client' AND pr.sender_id = c.id) WHERE pr.project_id = ? ORDER BY pr.created_at ASC");
    $rep_stmt->bind_param("i", $pid);
    $rep_stmt->execute();
    $rep_res = $rep_stmt->get_result();
    while ($row = $rep_res->fetch_assoc()) $reports[] = $row;
    $rep_stmt->close();

    $proj['milestones'] = $milestones;
    $proj['assets'] = $assets;
    $proj['tickets'] = $tickets;
    $proj['reports'] = $reports;

    ob_clean();
    echo json_encode(['status' => 'success', 'project' => $proj]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
