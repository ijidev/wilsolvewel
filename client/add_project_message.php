<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$conn = get_db_connection();

$action = $_GET['action'] ?? '';

if ($action == 'get_milestone_reports') {
    $milestone_id = (int)$_GET['milestone_id'];
    
    // Verify
    $verify = safe_query($conn, "SELECT pm.id FROM project_milestones pm JOIN projects p ON pm.project_id = p.id WHERE pm.id = ? AND p.client_id = ?", "ii", [$milestone_id, $client_id]);
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $res = safe_query($conn, "SELECT pr.*, IF(pr.sender_type='Admin', a.name, 'YOU') as sender_name FROM project_reports pr LEFT JOIN admins a ON (pr.sender_type = 'Admin' AND pr.sender_id = a.id) WHERE pr.milestone_id = ? ORDER BY pr.created_at ASC", "i", [$milestone_id]);
    $reports = [];
    while ($row = $res->fetch_assoc()) $reports[] = $row;
    echo json_encode($reports);
    exit;
}

if ($action == 'add_milestone_report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']); exit;
    }

    $milestone_id = (int)$_POST['milestone_id'];
    $project_id = (int)$_POST['project_id'];
    $content = trim($_POST['content']);
    
    // Verify
    $verify = safe_query($conn, "SELECT id FROM projects WHERE id = ? AND client_id = ?", "ii", [$project_id, $client_id]);
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $stmt = $conn->prepare("INSERT INTO project_reports (project_id, milestone_id, sender_type, sender_id, content) VALUES (?, ?, 'Client', ?, ?)");
    $stmt->bind_param("iiis", $project_id, $milestone_id, $client_id, $content);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']); exit;
    }

    $project_id = (int)$_POST['project_id'];
    $content = trim($_POST['content'] ?? '');

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']); exit;
    }

    // Verify ownership
    $verify = safe_query($conn, "SELECT id FROM projects WHERE id = ? AND client_id = ?", "ii", [$project_id, $client_id]);
    if ($verify->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']); exit;
    }

    $stmt = $conn->prepare("INSERT INTO project_reports (project_id, sender_type, sender_id, content) VALUES (?, 'Client', ?, ?)");
    $stmt->bind_param("iis", $project_id, $client_id, $content);
    $stmt->execute();
    $stmt->close();
    
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
