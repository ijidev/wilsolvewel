<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$conn = get_db_connection();

$action = $_GET['action'] ?? '';

if ($action == 'approve_milestone' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    // Verify milestone belongs to client
    $verify = $conn->query("SELECT pm.id FROM project_milestones pm JOIN projects p ON pm.project_id = p.id WHERE pm.id = $id AND p.client_id = $client_id");
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }
    
    $conn->query("UPDATE project_milestones SET approval_status='$status' WHERE id=$id");
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'get_milestone_reports') {
    $milestone_id = (int)$_GET['milestone_id'];
    
    // Verify
    $verify = $conn->query("SELECT pm.id FROM project_milestones pm JOIN projects p ON pm.project_id = p.id WHERE pm.id = $milestone_id AND p.client_id = $client_id");
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $res = $conn->query("
        SELECT pr.*, 
               IF(pr.sender_type='Admin', a.name, 'YOU') as sender_name 
        FROM project_reports pr 
        LEFT JOIN admins a ON (pr.sender_type = 'Admin' AND pr.sender_id = a.id)
        WHERE pr.milestone_id = $milestone_id ORDER BY pr.created_at ASC
    ");
    $reports = [];
    while ($row = $res->fetch_assoc()) $reports[] = $row;
    echo json_encode($reports);
    exit;
}

if ($action == 'add_milestone_report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $milestone_id = (int)$_POST['milestone_id'];
    $project_id = (int)$_POST['project_id'];
    $content = $conn->real_escape_string(trim($_POST['content']));
    
    // Verify
    $verify = $conn->query("SELECT id FROM projects WHERE id = $project_id AND client_id = $client_id");
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $sql = "INSERT INTO project_reports (project_id, milestone_id, sender_type, sender_id, content) VALUES ($project_id, $milestone_id, 'Client', $client_id, '$content')";
    $conn->query($sql);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = (int)$_POST['project_id'];
    $content = $conn->real_escape_string(trim($_POST['content'] ?? ''));

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']); exit;
    }

    // Verify ownership
    $verify = $conn->query("SELECT id FROM projects WHERE id = $project_id AND client_id = $client_id");
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $sql = "INSERT INTO project_reports (project_id, sender_type, sender_id, content) VALUES ($project_id, 'Client', $client_id, '$content')";
    $conn->query($sql);
    
    echo json_encode([
        'status' => 'success', 
        'html' => '
            <div class="flex flex-col items-end w-full mb-4">
                <div class="max-w-[85%]">
                    <div class="flex items-center justify-end gap-2 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">'.date('H:i').'</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-primary">YOU</span>
                    </div>
                    <div class="bg-primary text-on-primary p-4 rounded-2xl rounded-tr-none shadow-lg shadow-primary/10">
                        <p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">'.htmlspecialchars($content).'</p>
                    </div>
                </div>
            </div>
        '
    ]);
}
