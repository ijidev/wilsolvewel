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

$project_ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
$project_ids = array_map('intval', $project_ids);
$project_ids = array_filter($project_ids);

if (empty($project_ids)) {
    echo json_encode(['status' => 'error', 'message' => 'No projects selected']);
    exit;
}

try {
    $conn = get_db_connection();

    // IDs are already int-validated, safe for direct SQL
    $id_list = implode(',', $project_ids);

    $sql = "SELECT * FROM projects WHERE id IN ($id_list) AND client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $projects_res = $stmt->get_result();

    $projects = [];
    while ($row = $projects_res->fetch_assoc()) {
        $pid = $row['id'];

        // Milestones
        $milestones = [];
        $m_stmt = $conn->prepare("SELECT * FROM project_milestones WHERE project_id = ? ORDER BY order_index ASC, created_at ASC");
        $m_stmt->bind_param("i", $pid);
        $m_stmt->execute();
        $m_res = $m_stmt->get_result();
        while ($m = $m_res->fetch_assoc()) {
            $ms_id = $m['id'];
            $subs = [];
            $s_stmt = $conn->prepare("SELECT * FROM project_sub_milestones WHERE milestone_id = ? ORDER BY created_at ASC");
            $s_stmt->bind_param("i", $ms_id);
            $s_stmt->execute();
            $s_res = $s_stmt->get_result();
            while ($s = $s_res->fetch_assoc()) $subs[] = $s;
            $s_stmt->close();
            $m['sub_milestones'] = $subs;
            $milestones[] = $m;
        }
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

        $row['milestones'] = $milestones;
        $row['assets'] = $assets;
        $row['tickets'] = $tickets;
        $projects[] = $row;
    }
    $stmt->close();

    ob_clean();
    echo json_encode(['status' => 'success', 'projects' => $projects]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
