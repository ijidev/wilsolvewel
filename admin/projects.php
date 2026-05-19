<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

ensure_column_exists($conn, 'project_milestones', 'assigned_to_department', "INT(11) NULL");
ensure_column_exists($conn, 'project_sub_milestones', 'assigned_to_admin', "INT(11) NULL");
ensure_column_exists($conn, 'project_sub_milestones', 'assigned_to_department', "INT(11) NULL");
ensure_column_exists($conn, 'departments', 'leader_id', "INT(11) NULL");

function check_project_permission($conn, $project_id, $admin_id) {
    if (!$project_id) return true;
    if ($admin_id == 1) return true;
    $stmt = $conn->prepare("SELECT d.leader_id FROM projects p LEFT JOIN departments d ON p.department_id = d.id WHERE p.id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $proj = $res->fetch_assoc();
    $stmt->close();
    if (!$proj || !$proj['leader_id']) return true;
    return $proj['leader_id'] == $admin_id;
}

function check_task_permission($conn, $task_id, $admin_id) {
    if ($admin_id == 1) return true;
    $stmt = $conn->prepare("SELECT sm.assigned_to_admin, sm.assigned_to_department, p.id as project_id, d.leader_id FROM project_sub_milestones sm JOIN project_milestones m ON sm.milestone_id = m.id JOIN projects p ON m.project_id = p.id LEFT JOIN departments d ON sm.assigned_to_department = d.id WHERE sm.id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $task = $res->fetch_assoc();
    $stmt->close();
    if (!$task) return false;
    if (check_project_permission($conn, $task['project_id'], $admin_id)) return true;
    if ($task['assigned_to_admin'] == $admin_id) return true;
    return false;
}

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'save_project') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        
        if (!check_project_permission($conn, $id, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }

        $client_id = (int)($_POST['client_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'Planning';
        $start_date = $_POST['start_date'] ?? date('Y-m-d');
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $budget = (float)($_POST['budget'] ?? 0);

        if (empty($name) || $client_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Project Name and Client are required.']); exit;
        }

        if ($id > 0) {
            $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
            $stmt = $conn->prepare("UPDATE projects SET client_id=?, name=?, description=?, status=?, start_date=?, end_date=?, budget=?, department_id=? WHERE id=?");
            $stmt->bind_param("isssssdii", $client_id, $name, $description, $status, $start_date, $end_date, $budget, $department_id, $id);
            $stmt->execute();
            if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit; }
            $stmt->close();
            log_audit($conn, 'Update', 'Project', 'Admin', $admin_id, "Updated project: $name (ID: $id)");
            echo json_encode(['status' => 'success', 'message' => 'Project updated.']);
        } else {
            $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
            $stmt = $conn->prepare("INSERT INTO projects (client_id, name, description, status, start_date, end_date, budget, department_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssdi", $client_id, $name, $description, $status, $start_date, $end_date, $budget, $department_id);
            $stmt->execute();
            if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit; }
            $new_id = $conn->insert_id;
            $stmt->close();
            log_audit($conn, 'Create', 'Project', 'Admin', $admin_id, "Created new project: $name (ID: $new_id)");
            // Notify client
            $client_res = safe_query($conn, "SELECT email, name FROM clients WHERE id = ?", "i", [$client_id]);
            if ($client_res && $c = $client_res->fetch_assoc()) {
                send_email($c['email'], 'New Project: ' . htmlspecialchars($name), email_template('A new project has been created for you', '<p>Hello ' . htmlspecialchars($c['name']) . ',</p><p>A new project has been created for you on the <strong>Wilsolvewel Engineering</strong> portal:</p><p><strong>Project:</strong> ' . htmlspecialchars($name) . '</p><p><strong>Status:</strong> ' . htmlspecialchars($status) . '</p><p style="margin-top:20px"><a href="' . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . '/client/projects.php?id=' . $new_id . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px">View Project</a></p>'));
            }
            echo json_encode(['status' => 'success', 'message' => 'Project created.']);
        }
        exit;
    }

    if ($_GET['ajax_action'] == 'get_project') {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res->fetch_assoc());
        $stmt->close();
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_project') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)$_POST['id'];
        
        if (!check_project_permission($conn, $id, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }

        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        log_audit($conn, 'Delete', 'Project', 'Admin', $admin_id, "Deleted project ID: $id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'add_report') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $project_id = (int)$_POST['project_id'];
        $content = trim($_POST['content'] ?? '');

        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']); exit;
        }

        $stmt = $conn->prepare("INSERT INTO project_reports (project_id, sender_type, sender_id, content) VALUES (?, 'Admin', ?, ?)");
        $stmt->bind_param("iis", $project_id, $admin_id, $content);
        $stmt->execute();
        if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit; }
        $stmt->close();
        
        log_audit($conn, 'Create', 'Project', 'Admin', $admin_id, "Sent a message in project ID: $project_id");
        
        // Notify client
        $pinfo = safe_query($conn, "SELECT p.name, c.email, c.name as client_name FROM projects p JOIN clients c ON p.client_id = c.id WHERE p.id = ?", "i", [$project_id]);
        if ($pinfo && $p = $pinfo->fetch_assoc()) {
            send_email($p['email'], 'New message on project: ' . htmlspecialchars($p['name']), email_template('New message on your project', '<p>Hello ' . htmlspecialchars($p['client_name']) . ',</p><p>There is a new message on your project <strong>' . htmlspecialchars($p['name']) . '</strong>.</p><p style="margin-top:20px"><a href="' . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . '/client/projects.php?id=' . $project_id . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px">View Messages</a></p>'));
        }
        
        $new_id = $conn->insert_id;
        $stmt = $conn->prepare("SELECT pr.*, COALESCE(a.name, 'Unknown Admin') as sender_name FROM project_reports pr LEFT JOIN admins a ON pr.sender_id = a.id WHERE pr.id = ?");
        $stmt->bind_param("i", $new_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $report = $res->fetch_assoc();
        $stmt->close();
        
        $html = '<div class="flex flex-col items-end mb-4">';
        $html .= '<div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-4 py-2.5 max-w-[85%] shadow-sm">';
        $html .= '<p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">' . htmlspecialchars($report['content']) . '</p>';
        $html .= '<p class="text-[9px] font-bold opacity-60 mt-1 uppercase tracking-widest">' . date('H:i', strtotime($report['created_at'])) . '</p>';
        $html .= '</div></div>';

        echo json_encode(['status' => 'success', 'html' => $html]);
        exit;
    }

    if ($_GET['ajax_action'] == 'assign_asset') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $project_id = (int)$_POST['project_id'];
        $asset_id = (int)$_POST['asset_id'];
        $stmt = $conn->prepare("INSERT IGNORE INTO project_assets (project_id, asset_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $project_id, $asset_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'remove_asset') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $project_id = (int)$_POST['project_id'];
        $asset_id = (int)$_POST['asset_id'];
        $stmt = $conn->prepare("DELETE FROM project_assets WHERE project_id = ? AND asset_id = ?");
        $stmt->bind_param("ii", $project_id, $asset_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'assign_task') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $task_id = (int)$_POST['task_id'];
        
        $stmt = $conn->prepare("SELECT p.id as project_id FROM project_sub_milestones sm JOIN project_milestones m ON sm.milestone_id = m.id JOIN projects p ON m.project_id = p.id WHERE sm.id = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $proj_id = $res->fetch_assoc()['project_id'] ?? 0;
        $stmt->close();
        if (!check_project_permission($conn, $proj_id, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }

        $admin_id_assign = !empty($_POST['admin_id']) ? (int)$_POST['admin_id'] : null;
        $dept_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        $stmt = $conn->prepare("UPDATE project_sub_milestones SET assigned_to_admin = ?, assigned_to_department = ? WHERE id = ?");
        $stmt->bind_param("iii", $admin_id_assign, $dept_id, $task_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'assign_project_dept') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $project_id = (int)$_POST['project_id'];
        $dept_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        $stmt = $conn->prepare("UPDATE projects SET department_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $dept_id, $project_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'confirm_project_active') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE projects SET status = 'Active' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        log_audit($conn, 'Update', 'Project', 'Admin', $admin_id, "Project #$id transitioned to Active");
        // Notify client
        $pinfo = safe_query($conn, "SELECT p.name, c.email, c.name as client_name FROM projects p JOIN clients c ON p.client_id = c.id WHERE p.id = ?", "i", [$id]);
        if ($pinfo && $p = $pinfo->fetch_assoc()) {
            send_email($p['email'], 'Project Active: ' . htmlspecialchars($p['name']), email_template('Your project is now Active', '<p>Hello ' . htmlspecialchars($p['client_name']) . ',</p><p>Your project <strong>' . htmlspecialchars($p['name']) . '</strong> has been approved and is now <strong>Active</strong>.</p><p style="margin-top:20px"><a href="' . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . '/client/projects.php?id=' . $id . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px">View Project</a></p>'));
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'toggle_project_hold') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)$_POST['id'];
        $current = $_POST['current_status'];
        
        if ($current == 'On Hold') {
            $new = 'Active';
        } else {
            $new = 'On Hold';
        }
        
        $stmt = $conn->prepare("UPDATE projects SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new, $id);
        $stmt->execute();
        $stmt->close();
        // Notify client
        $pinfo = safe_query($conn, "SELECT p.name, c.email, c.name as client_name FROM projects p JOIN clients c ON p.client_id = c.id WHERE p.id = ?", "i", [$id]);
        if ($pinfo && $p = $pinfo->fetch_assoc()) {
            send_email($p['email'], 'Project ' . $new . ': ' . htmlspecialchars($p['name']), email_template('Your project status has changed', '<p>Hello ' . htmlspecialchars($p['client_name']) . ',</p><p>Your project <strong>' . htmlspecialchars($p['name']) . '</strong> has been updated to: <strong>' . $new . '</strong>.</p><p style="margin-top:20px"><a href="' . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . '/client/projects.php?id=' . $id . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px">View Project</a></p>'));
        }
        echo json_encode(['status' => 'success', 'new_status' => $new]);
        exit;
    }

    if ($_GET['ajax_action'] == 'save_milestone') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $project_id = (int)($_POST['project_id'] ?? 0);
        
        if (!check_project_permission($conn, $project_id, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if ($project_id > 0) {
            $stmt = $conn->prepare("SELECT status FROM projects WHERE id = ?");
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $p_res = $stmt->get_result();
            $p = $p_res->fetch_assoc();
            $stmt->close();
            if ($p['status'] !== 'Planning') {
                echo json_encode(['status' => 'error', 'message' => 'Cannot modify milestones once project is ' . $p['status']]);
                exit;
            }
        }

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE project_milestones SET title=?, description=?, due_date=? WHERE id=?");
            $stmt->bind_param("sssi", $title, $description, $due_date, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("SELECT MAX(order_index) as max_idx FROM project_milestones WHERE project_id = ?");
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $idx = (int)($res->fetch_assoc()['max_idx'] ?? -1) + 1;
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO project_milestones (project_id, title, description, due_date, order_index) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $project_id, $title, $description, $due_date, $idx);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_milestone') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM project_milestones WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'get_milestone') {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM project_milestones WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res->fetch_assoc());
        $stmt->close();
        exit;
    }

    if ($_GET['ajax_action'] == 'update_milestone_status') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)$_POST['id'];
        
        $stmt = $conn->prepare("SELECT project_id FROM project_milestones WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $pid = $res->fetch_assoc()['project_id'] ?? 0;
        $stmt->close();
        if (!check_project_permission($conn, $pid, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }

        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE project_milestones SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("SELECT project_id FROM project_milestones WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $ms = $res->fetch_assoc();
        $stmt->close();
        $pid = $ms['project_id'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM project_milestones WHERE project_id = ?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $res = $stmt->get_result();
        $total = $res->fetch_assoc()['total'];
        $stmt->close();
        
        $stmt = $conn->prepare("SELECT COUNT(*) as done FROM project_milestones WHERE project_id = ? AND status = 'Completed'");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $res = $stmt->get_result();
        $done = $res->fetch_assoc()['done'];
        $stmt->close();
        
        if ($total > 0 && $total == $done) {
            $stmt = $conn->prepare("UPDATE projects SET status = 'Completed' WHERE id = ?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $stmt->close();
            log_audit($conn, 'Update', 'Project', 'Admin', $admin_id, "Project #$pid automatically marked Completed");
            $pinfo = safe_query($conn, "SELECT p.name, c.email, c.name as client_name FROM projects p JOIN clients c ON p.client_id = c.id WHERE p.id = ?", "i", [$pid]);
            if ($pinfo && $p = $pinfo->fetch_assoc()) {
                send_email($p['email'], 'Project Completed: ' . htmlspecialchars($p['name']), email_template('Your project has been completed', '<p>Hello ' . htmlspecialchars($p['client_name']) . ',</p><p>Your project <strong>' . htmlspecialchars($p['name']) . '</strong> has been marked as <strong>Completed</strong>.</p><p>Thank you for working with Wilsolvewel Engineering.</p><p style="margin-top:20px"><a href="' . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . '/client/projects.php?id=' . $pid . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px">View Project</a></p>'));
            }
        } else if ($total > 0 && $done < $total) {
            $stmt = $conn->prepare("SELECT status FROM projects WHERE id = ?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->fetch_assoc()['status'] == 'Completed') {
                $stmt->close();
                $stmt = $conn->prepare("UPDATE projects SET status = 'Active' WHERE id = ?");
                $stmt->bind_param("i", $pid);
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt->close();
            }
        }
        
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'save_sub_milestone') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $milestone_id = (int)$_POST['milestone_id'];
        
        $stmt = $conn->prepare("SELECT project_id FROM project_milestones WHERE id = ?");
        $stmt->bind_param("i", $milestone_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $pid = $res->fetch_assoc()['project_id'] ?? 0;
        $stmt->close();
        if (!check_project_permission($conn, $pid, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }

        $title = trim($_POST['title']);
        $stmt = $conn->prepare("INSERT INTO project_sub_milestones (milestone_id, title) VALUES (?, ?)");
        $stmt->bind_param("is", $milestone_id, $title);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'toggle_sub_milestone') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)$_POST['id'];
        
        if (!check_task_permission($conn, $id, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }

        $stmt = $conn->prepare("UPDATE project_sub_milestones SET is_completed = NOT is_completed WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_sub_milestone') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $id = (int)$_POST['id'];
        
        if (!check_task_permission($conn, $id, $admin_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Permission denied']); exit;
        }

        $stmt = $conn->prepare("DELETE FROM project_sub_milestones WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'get_milestone_reports') {
        $milestone_id = (int)$_GET['milestone_id'];
        $stmt = $conn->prepare("SELECT pr.*, IF(pr.sender_type='Admin', a.name, c.name) as sender_name FROM project_reports pr LEFT JOIN admins a ON (pr.sender_type = 'Admin' AND pr.sender_id = a.id) LEFT JOIN clients c ON (pr.sender_type = 'Client' AND pr.sender_id = c.id) WHERE pr.milestone_id = ? ORDER BY pr.created_at ASC");
        $stmt->bind_param("i", $milestone_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $reports = [];
        while ($row = $res->fetch_assoc()) $reports[] = $row;
        $stmt->close();
        echo json_encode($reports);
        exit;
    }

    if ($_GET['ajax_action'] == 'add_milestone_report') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token']); exit;
        }

        $milestone_id = (int)$_POST['milestone_id'];
        $project_id = (int)$_POST['project_id'];
        $content = trim($_POST['content']);
        $stmt = $conn->prepare("INSERT INTO project_reports (project_id, milestone_id, sender_type, sender_id, content) VALUES (?, ?, 'Admin', ?, ?)");
        $stmt->bind_param("iiis", $project_id, $milestone_id, $admin_id, $content);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'load_details') {
        $id = (int)$_GET['id'];
        
        $stmt = $conn->prepare("SELECT p.*, COALESCE(c.name, 'Deleted Client') as client_name, c.email as client_email, d.name as dept_name FROM projects p LEFT JOIN clients c ON p.client_id = c.id LEFT JOIN departments d ON p.department_id = d.id WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $proj = $res->fetch_assoc();
        $stmt->close();
        
        $milestones = [];
        $stmt = $conn->prepare("SELECT m.*, d.name as dept_name FROM project_milestones m LEFT JOIN departments d ON m.assigned_to_department = d.id WHERE m.project_id = ? ORDER BY m.order_index ASC, m.created_at ASC");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ms_result = $stmt->get_result();
        while ($row = $ms_result->fetch_assoc()) {
            $ms_id = $row['id'];
            $subs = [];
            $sub_stmt = $conn->prepare("SELECT sm.*, a.name as assignee_name, dep.name as dept_name FROM project_sub_milestones sm LEFT JOIN admins a ON sm.assigned_to_admin = a.id LEFT JOIN departments dep ON sm.assigned_to_department = dep.id WHERE sm.milestone_id = ? ORDER BY sm.created_at ASC");
            $sub_stmt->bind_param("i", $ms_id);
            $sub_stmt->execute();
            $sub_result = $sub_stmt->get_result();
            while ($s = $sub_result->fetch_assoc()) $subs[] = $s;
            $sub_stmt->close();
            $row['sub_milestones'] = $subs;
            $milestones[] = $row;
        }
        $stmt->close();

        $assigned_assets = [];
        $stmt = $conn->prepare("SELECT a.* FROM assets a JOIN project_assets pa ON a.id = pa.asset_id WHERE pa.project_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) $assigned_assets[] = $row;
        $stmt->close();

        $all_assets = [];
        $res = $conn->query("SELECT id, name, type FROM assets ORDER BY name ASC");
        while ($row = $res->fetch_assoc()) $all_assets[] = $row;

        $departments = [];
        $res = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");
        while ($row = $res->fetch_assoc()) $departments[] = $row;
        $admins_list = [];
        $res = $conn->query("SELECT id, name FROM admins ORDER BY name ASC");
        while ($row = $res->fetch_assoc()) $admins_list[] = $row;
        
        $completed_count = 0;
        foreach ($milestones as $m) if ($m['status'] == 'Completed') $completed_count++;
        $progress = count($milestones) > 0 ? round(($completed_count / count($milestones)) * 100) : 0;

        // $progress = $proj['total_ms'] > 0 ? round(($proj['completed_ms'] / $proj['total_ms']) * 100) : 0;
        
        ob_start();
        ?>
        <!-- Canvas Header with embedded settings & assets -->
        <div class="p-6 border-b border-slate-100 bg-white relative overflow-hidden shrink-0">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-0.5 rounded-full text-[8px] font-black uppercase tracking-[0.1em] <?php echo $proj['status']=='Active'?'bg-emerald-600 text-white':($proj['status']=='Planning'?'bg-amber-600 text-white':($proj['status']=='On Hold'?'bg-red-600 text-white':'bg-slate-500 text-white')); ?>">
                        <?php echo $proj['status']; ?>
                    </span>
                    <div class="flex items-center gap-2">
                        <?php if ($proj['status'] == 'Active' || $proj['status'] == 'On Hold'): ?>
                            <button onclick="toggleProjectHold(<?php echo $id; ?>, '<?php echo $proj['status']; ?>')" class="h-8 px-3 bg-white border border-slate-200 text-slate-500 rounded-xl text-[9px] font-bold uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-[14px]"><?php echo $proj['status'] == 'On Hold' ? 'play_arrow' : 'pause'; ?></span>
                                <?php echo $proj['status'] == 'On Hold' ? 'Resume' : 'Hold'; ?>
                            </button>
                        <?php endif; ?>
                        <button onclick="editProject(<?php echo $id; ?>)" class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-all shadow-lg active:scale-95">
                            <span class="material-symbols-outlined text-sm">settings</span>
                        </button>
                        <button onclick="closeProjectDetail()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-200 transition-all">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold font-headline text-slate-900 tracking-tight leading-none mb-0.5"><?php echo htmlspecialchars($proj['name']); ?></h2>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] opacity-60 mb-4">REF: #PRJ-<?php echo $proj['id']; ?></p>

                <!-- Inline metadata grid -->
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Client</p>
                        <p class="text-[11px] font-bold text-primary truncate"><?php echo htmlspecialchars($proj['client_name']); ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Department</p>
                        <p class="text-[11px] font-bold text-slate-700 truncate"><?php echo $proj['dept_name'] ?: '—'; ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Budget</p>
                        <p class="text-[11px] font-bold text-emerald-600"><?php echo !empty($proj['budget']) ? '$' . number_format($proj['budget'], 2) : '—'; ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Start</p>
                        <p class="text-[11px] font-bold text-slate-700"><?php echo $proj['start_date'] ? date('d M Y', strtotime($proj['start_date'])) : 'TBD'; ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">End</p>
                        <p class="text-[11px] font-bold text-slate-700"><?php echo $proj['end_date'] ? date('d M Y', strtotime($proj['end_date'])) : 'TBD'; ?></p>
                    </div>
                </div>

                <!-- Linked Assets row -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest shrink-0">Assets:</span>
                    <?php if (empty($assigned_assets)): ?>
                        <span class="text-[9px] text-slate-300 italic">None</span>
                    <?php else: ?>
                        <?php foreach ($assigned_assets as $asset): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 rounded-md text-[9px] font-bold text-slate-600 group">
                                <?php echo htmlspecialchars($asset['name']); ?>
                                <button onclick="removeAsset(<?php echo $id; ?>, <?php echo $asset['id']; ?>)" class="text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined" style="font-size:10px">close</span>
                                </button>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="relative inline-block">
                        <select id="assetSearchSelect" onchange="if(this.value){assignAsset(<?php echo $id; ?>);this.value='';}" class="bg-transparent border border-dashed border-slate-200 rounded-md px-2 py-0.5 text-[9px] font-bold text-slate-400 cursor-pointer hover:border-primary transition-colors outline-none focus:ring-0">
                            <option value="">+ Add Asset</option>
                            <?php foreach($all_assets as $as): ?>
                                <option value="<?php echo $as['id']; ?>"><?php echo htmlspecialchars($as['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $header_html = ob_get_clean();


        ob_start();
        ?>
        <div class="space-y-6">
            <!-- Progress -->
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest">Completion</p>
                    <span class="text-xs font-bold text-emerald-600"><?php echo $progress; ?>%</span>
                </div>
                <div class="h-1.5 bg-emerald-50 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: <?php echo $progress; ?>%"></div>
                </div>
            </div>

            <!-- Milestones Header -->
            <div class="flex items-center justify-between px-1">
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">route</span> Milestones
                </h3>
                <div class="flex gap-2">
                        <?php if ($proj['status'] == 'Planning'): ?>
                            <?php if (!empty($milestones)): ?>
                                <button onclick="confirmProjectActive(<?php echo $id; ?>)" class="px-3 py-1.5 bg-primary text-on-primary rounded-lg text-[8px] font-bold uppercase tracking-widest hover:shadow-lg transition-all flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[14px]">rocket_launch</span> Activate
                                </button>
                            <?php else: ?>
                                <div class="px-3 py-1.5 bg-slate-50 text-slate-400 rounded-lg text-[7px] font-bold uppercase tracking-widest border border-slate-100 flex items-center gap-1.5" title="Add at least one milestone first">
                                    <span class="material-symbols-outlined text-[12px]">info</span> Add Milestones First
                                </div>
                            <?php endif; ?>

                            <button onclick="openMilestoneModal(<?php echo $id; ?>)" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-[8px] font-bold uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">add</span> Add Milestone
                            </button>
                        <?php else: ?>
                            <div class="px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[8px] font-bold uppercase tracking-widest border border-emerald-100 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">lock</span> Roadmap Locked
                            </div>
                        <?php endif; ?>
                </div>
            </div>
                
            <div id="milestoneList" class="space-y-3">
                <?php if (empty($milestones)): ?>
                    <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-slate-200">
                        <span class="material-symbols-outlined text-3xl text-slate-200">flag</span>
                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No milestones yet</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $ms_counter = 1;
                    foreach ($milestones as $m): 
                        $statusClass = $m['status'] == 'Completed' ? 'bg-emerald-500 border-emerald-500' : ($m['status'] == 'In Progress' ? 'bg-amber-500 border-amber-500' : 'bg-slate-100 border-slate-200');
                        $taskCount = count($m['sub_milestones']);
                        $doneCount = 0; foreach($m['sub_milestones'] as $sm) if($sm['is_completed']) $doneCount++;
                    ?>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden hover:border-primary/20 transition-all">
                        <div class="px-4 py-3 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center border-[3px] <?php echo $statusClass; ?> shrink-0">
                                <?php if ($m['status'] == 'Completed'): ?>
                                    <span class="material-symbols-outlined text-white" style="font-size:12px">check</span>
                                <?php else: ?>
                                    <span class="text-[9px] font-bold <?php echo $m['status'] == 'In Progress' ? 'text-white' : 'text-slate-400'; ?>"><?php echo $ms_counter++; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-[13px] font-bold text-slate-900 truncate"><?php echo htmlspecialchars($m['title']); ?></h4>
                                </div>
                                <?php if($m['due_date']): ?><p class="text-[9px] text-slate-400"><?php echo date('M d', strtotime($m['due_date'])); ?></p><?php endif; ?>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button onclick="toggleTasks(<?php echo $m['id']; ?>)" class="px-2 py-1 bg-slate-50 rounded-lg text-[9px] font-bold text-slate-500 hover:bg-slate-100 flex items-center gap-1 tooltip" title="Manage Tasks">
                                    <span class="material-symbols-outlined" style="font-size:12px">checklist</span><?php echo $taskCount > 0 ? "$doneCount/$taskCount" : "+"; ?>
                                </button>
                                <?php if ($proj['status'] == 'Active'): ?>
                                    <?php if ($m['status'] == 'Pending'): ?>
                                        <button onclick="updateMilestoneStatus(<?php echo $m['id']; ?>, <?php echo $id; ?>, 'In Progress')" class="px-2 py-1 bg-amber-50 text-amber-600 rounded-lg text-[8px] font-bold uppercase hover:bg-amber-500 hover:text-white transition-all">Start</button>
                                    <?php elseif ($m['status'] != 'Completed'): ?>
                                        <button onclick="updateMilestoneStatus(<?php echo $m['id']; ?>, <?php echo $id; ?>, 'Completed')" class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[8px] font-bold uppercase hover:bg-emerald-500 hover:text-white transition-all">Done</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <button onclick="openMilestoneChat(<?php echo $m['id']; ?>, '<?php echo addslashes($m['title']); ?>')" class="p-1 text-slate-300 hover:text-primary" title="Logs"><span class="material-symbols-outlined" style="font-size:16px">forum</span></button>
                                <button onclick="toggleMilestoneActions(<?php echo $m['id']; ?>)" class="p-1 text-slate-300 hover:text-slate-600"><span class="material-symbols-outlined" style="font-size:16px">more_vert</span></button>
                            </div>
                        </div>
                        <div id="tasks_<?php echo $m['id']; ?>" class="hidden border-t border-slate-50 px-4 py-2 bg-slate-50/50">
                            <div class="space-y-1">
                                <?php foreach ($m['sub_milestones'] as $sm): ?>
                                <div class="flex items-center gap-2 py-1 group">
                                    <button onclick="toggleSubMilestone(<?php echo $sm['id']; ?>, <?php echo $id; ?>)" class="w-3.5 h-3.5 rounded border <?php echo $sm['is_completed'] ? 'bg-primary border-primary text-on-primary' : 'border-slate-200 text-transparent'; ?> flex items-center justify-center shrink-0"><span class="material-symbols-outlined" style="font-size:9px">check</span></button>
                                    <span class="text-[10px] flex-1 <?php echo $sm['is_completed'] ? 'text-slate-400 line-through' : 'text-slate-600 font-medium'; ?>"><?php echo htmlspecialchars($sm['title']); ?></span>
                                    <?php if(!empty($sm['assignee_name'])): ?><span class="text-[8px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded font-bold"><?php echo htmlspecialchars($sm['assignee_name']); ?></span><?php elseif(!empty($sm['dept_name'])): ?><span class="text-[8px] bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded font-bold"><?php echo htmlspecialchars($sm['dept_name']); ?></span><?php endif; ?>
                                    <button onclick="openTaskAssign(<?php echo $sm['id']; ?>, <?php echo $id; ?>)" class="opacity-30 hover:opacity-100 text-slate-500 hover:text-primary transition-opacity" title="Assign"><span class="material-symbols-outlined" style="font-size:12px">person_add</span></button>
                                    <button onclick="deleteSubMilestone(<?php echo $sm['id']; ?>, <?php echo $id; ?>)" class="opacity-30 hover:opacity-100 text-red-300 hover:text-red-500 transition-opacity"><span class="material-symbols-outlined" style="font-size:12px">close</span></button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($proj['status'] == 'Planning' || $proj['status'] == 'Active'): ?>
                            <div class="flex items-center gap-2 pt-1 mt-1 border-t border-slate-100">
                                <button onclick="showSubInput(<?php echo $m['id']; ?>)" class="text-[9px] font-bold text-primary flex items-center gap-0.5 hover:underline"><span class="material-symbols-outlined" style="font-size:12px">add</span> Task</button>
                                <div id="subInput_<?php echo $m['id']; ?>" class="hidden flex-1 flex gap-1">
                                    <input type="text" id="subText_<?php echo $m['id']; ?>" class="flex-1 bg-white border-none rounded-lg px-2 py-1 text-[10px] focus:ring-1 focus:ring-primary" placeholder="Task...">
                                    <button onclick="saveSubMilestone(<?php echo $m['id']; ?>, <?php echo $id; ?>)" class="px-2 py-1 bg-primary text-on-primary rounded-lg text-[9px] font-bold">Save</button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div id="milestoneActions_<?php echo $m['id']; ?>" class="hidden bg-slate-50 border-t border-slate-100 px-4 py-2 flex gap-3">
                            <?php if ($proj['status'] == 'Planning'): ?>
                                <button onclick="editMilestone(<?php echo $m['id']; ?>)" class="text-[9px] font-bold text-slate-400 flex items-center gap-1 hover:text-slate-600"><span class="material-symbols-outlined" style="font-size:12px">edit</span> Edit</button>
                                <button onclick="deleteMilestone(<?php echo $m['id']; ?>, <?php echo $id; ?>)" class="text-[9px] font-bold text-red-400 flex items-center gap-1 hover:text-red-600"><span class="material-symbols-outlined" style="font-size:12px">delete</span> Delete</button>
                            <?php else: ?>
                                <span class="text-[9px] font-bold text-slate-300 italic">Locked</span>
                            <?php endif; ?>
                        </div>
                    </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php
        echo json_encode([
            'header_html' => $header_html,
            'body_html' => ob_get_clean()
        ]);
        exit;
    }

    if ($_GET['ajax_action'] == 'guide_dismiss') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid token']); exit;
        }
        set_setting('guide_dismissed_admin_' . $admin_id, '1');
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// ── Filters ────────────────────────────────────────────────────────────────────
$filter_search = trim($_GET['search'] ?? '');
$filter_client = (int)($_GET['client'] ?? 0);
$filter_dept = (int)($_GET['dept'] ?? 0);
$filter_budget_min = (float)($_GET['budget_min'] ?? 0);
$filter_budget_max = (float)($_GET['budget_max'] ?? 0);

$where = [];
$params = [];
$types = '';

if ($filter_search) {
    $where[] = "(p.name LIKE ? OR c.name LIKE ?)";
    $search_term = '%' . $filter_search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}
if ($filter_client > 0) {
    $where[] = "p.client_id = ?";
    $params[] = $filter_client;
    $types .= 'i';
}
if ($filter_dept > 0) {
    $where[] = "p.department_id = ?";
    $params[] = $filter_dept;
    $types .= 'i';
}
if ($filter_budget_min > 0) {
    $where[] = "p.budget >= ?";
    $params[] = $filter_budget_min;
    $types .= 'd';
}
if ($filter_budget_max > 0) {
    $where[] = "p.budget <= ?";
    $params[] = $filter_budget_max;
    $types .= 'd';
}

$where_clause = '';
if (!empty($where)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where);
}

// Fetch Projects with Milestone Progress
$projects = [];
$sql = "
    SELECT p.*, c.name as client_name,
           (SELECT COUNT(*) FROM project_milestones WHERE project_id = p.id) as total_ms,
           (SELECT COUNT(*) FROM project_milestones WHERE project_id = p.id AND status = 'Completed') as completed_ms
    FROM projects p 
    JOIN clients c ON p.client_id = c.id 
    $where_clause
    ORDER BY p.created_at DESC
";
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
} else {
    $res = $conn->query($sql);
}
while ($row = $res->fetch_assoc()) {
    $row['progress'] = $row['total_ms'] > 0 ? round(($row['completed_ms'] / $row['total_ms']) * 100) : 0;
    $projects[] = $row;
}

// Fetch Clients for Assignment
$clients = [];
$res = $conn->query("SELECT id, name FROM clients ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $clients[] = $row;

// Fetch Departments & Admins for assignment
$departments_list = [];
$res = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $departments_list[] = $row;
$admins_all = [];
$res = $conn->query("SELECT id, name FROM admins ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $admins_all[] = $row;

$permissions = get_admin_permissions($admin_id);
$guide_dismissed = get_setting('guide_dismissed_admin_' . $admin_id, '');

$page_title = 'Project Operations';
$page_subtitle = 'Client Projects & Reporting';
$page_header_actions = '<button onclick="openProjectModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
    <span class="material-symbols-outlined text-sm">add_box</span> NEW PROJECT
</button>';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Project Operations | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
    <script>window.CSRF_TOKEN = '<?= generate_csrf_token() ?>';</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Outfit", "Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(6px);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem}
        .modal-overlay.open{display:flex}
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">

<script src="../components/admin_sidenav.js" data-root="../"></script>

<!-- Toast -->
<div id="toast" class="fixed top-6 right-6 z-[400] transform translate-x-[150%] transition-transform duration-300 pointer-events-none">
    <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span id="toastIcon" class="material-symbols-outlined">check_circle</span>
        <p id="toastMessage" class="text-xs font-bold"></p>
    </div>
</div>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

    <div class="flex-1 flex overflow-hidden">
        <!-- Workflow Guide -->
        <div class="hidden lg:block absolute bottom-6 right-6 z-50">
<button onclick="document.getElementById('workflowGuide').classList.toggle('hidden')" class="w-10 h-10 rounded-full bg-orange-500 border border-orange-400 shadow-lg flex items-center justify-center text-white hover:bg-orange-600 hover:border-orange-500 transition-all" title="Workflow Guide">
            <span class="material-symbols-outlined text-lg">help</span>
        </button>
        </div>
        <div id="workflowGuide" class="hidden fixed inset-0 z-[400] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" onclick="if(event.target===this)document.getElementById('workflowGuide').classList.add('hidden')">
            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full max-h-[85vh] overflow-y-auto custom-scrollbar p-8" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold font-headline text-slate-900">Project Workflow Guide</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Admin Operations</p>
                    </div>
                    <button onclick="document.getElementById('workflowGuide').classList.add('hidden')" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200"><span class="material-symbols-outlined text-sm">close</span></button>
                    <?= get_csrf_field() ?>
                </div>
                <div class="space-y-5">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-sm font-bold">1</div>
                        <div><h4 class="text-sm font-bold text-slate-900">Planning Phase</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Create milestones for the project. Each milestone represents a major deliverable or phase. Add tasks (sub-milestones) under each milestone and assign them to individuals or departments. Milestones can only be created and edited during this phase.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center shrink-0 text-sm font-bold">2</div>
                        <div><h4 class="text-sm font-bold text-slate-900">Activation</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">When planning is complete, click the <strong>Activate</strong> button to move the project to <strong>Active (Development)</strong> phase. This locks the milestone roadmap — no further milestone additions or edits are allowed.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-sm font-bold">3</div>
                        <div><h4 class="text-sm font-bold text-slate-900">Execution</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Work through milestones — start them (In Progress), then mark them complete when finished. Use milestone chat logs for daily reports and client communication. Use the hold/resume toggle to pause work if needed.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center shrink-0 text-sm font-bold">4</div>
                        <div><h4 class="text-sm font-bold text-slate-900">Completion</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">When all milestones are marked complete, the project auto-transitions to <strong>Completed</strong> status. The project is now closed.</p></div>
                    </div>
                </div>
                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group text-xs text-slate-400 hover:text-slate-600 transition-colors">
                        <input type="checkbox" id="dontShowAgain" class="rounded border-slate-300 text-primary focus:ring-primary/20" />
                        <span>Don't show this again</span>
                    </label>
                    <div class="flex gap-2">
                        <button onclick="dismissGuide(document.getElementById('dontShowAgain').checked)" class="px-5 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-800 transition-all">Got It</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Master List -->
        <div class="flex-1 bg-white overflow-y-auto custom-scrollbar flex flex-col min-w-0">
            <!-- Filter Bar -->
            <form method="GET" class="p-4 pb-0 flex flex-wrap items-end gap-3 border-b border-slate-100 bg-slate-50/50">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Search</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                        <input type="text" name="search" value="<?= htmlspecialchars($filter_search) ?>" placeholder="Project or client name..." class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>
                <div>
                    <label class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Client</label>
                    <select name="client" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-primary/20">
                        <option value="0">All Clients</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $filter_client === $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Department</label>
                    <select name="dept" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-primary/20">
                        <option value="0">All Depts</option>
                        <?php foreach ($departments_list as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= $filter_dept === $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Budget Min</label>
                    <input type="number" step="0.01" name="budget_min" value="<?= $filter_budget_min ?: '' ?>" placeholder="$0" class="w-24 bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Budget Max</label>
                    <input type="number" step="0.01" name="budget_max" value="<?= $filter_budget_max ?: '' ?>" placeholder="$999k" class="w-24 bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div class="flex items-center gap-2 pb-0.5">
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-primary/90 transition-all">Filter</button>
                    <a href="?" class="px-4 py-2 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">Clear</a>
                </div>
            </form>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                <?php if (empty($projects)): ?>
                    <div class="text-center py-10"><span class="material-symbols-outlined text-4xl text-slate-200">folder_off</span><p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No Projects Found</p></div>
                <?php endif; ?>
                <?php foreach ($projects as $p): ?>
                    <div class="group relative bg-white border border-slate-100 rounded-3xl p-5 cursor-pointer hover:border-primary/50 transition-all hover:shadow-md" onclick="loadProject(<?php echo $p['id']; ?>, this)">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest <?php echo $p['status']=='Completed'?'bg-emerald-50 text-emerald-600':($p['status']=='Planning'?'bg-amber-50 text-amber-600':($p['status']=='On Hold'?'bg-red-50 text-red-500':'bg-blue-50 text-blue-600')); ?>"><?php echo $p['status']; ?></span>
                            <div class="flex gap-1" onclick="event.stopPropagation()">
                                <button onclick="editProject(<?php echo $p['id']; ?>)" class="w-6 h-6 rounded bg-slate-50 text-slate-400 hover:text-primary flex items-center justify-center"><span class="material-symbols-outlined text-sm">edit</span></button>
                                <button onclick="deleteProject(<?php echo $p['id']; ?>)" class="w-6 h-6 rounded bg-red-50 text-red-400 hover:text-red-600 flex items-center justify-center"><span class="material-symbols-outlined text-sm">delete</span></button>
                            </div>
                        </div>
                        <h3 class="font-bold text-slate-900 leading-tight mb-1 pr-4"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?php echo htmlspecialchars($p['client_name']); ?></p>
                        <?php if (!empty($p['budget'])): ?>
                        <p class="text-[11px] font-bold text-emerald-600 mt-1">$<?php echo number_format($p['budget'], 2); ?></p>
                        <?php endif; ?>
                        
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center text-[9px] font-bold uppercase tracking-widest text-slate-400">
                                <span>Progress</span>
                                <span><?php echo $p['progress']; ?>%</span>
                            </div>
                            <div class="h-1 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary" style="width: <?php echo $p['progress']; ?>%"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Detail Canvas Overlay -->
        <div id="detailBackdrop" onclick="closeProjectDetail()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[300] opacity-0 pointer-events-none transition-all duration-500"></div>
        <div id="detailCanvas" class="fixed top-0 right-0 h-full w-full lg:w-[680px] bg-white z-[301] translate-x-full transition-transform duration-500 ease-in-out shadow-2xl flex flex-col">
            <div id="canvasHeader" class="shrink-0">
                <!-- Loaded via AJAX -->
            </div>
            <div id="canvasBody" class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-slate-50/30">
                <div id="detailPane" class="space-y-8">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Project Modal -->
<div id="projectModal" class="modal-overlay">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
            <div>
                <h3 id="modalTitle" class="font-bold text-xl text-slate-900 font-headline">Register Project</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Client Operation</p>
            </div>
            <button onclick="closeProjectModal()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="overflow-y-auto custom-scrollbar flex-1">
            <form id="projectForm" class="p-8 space-y-5">
                <input type="hidden" name="id" id="projectId">
                <?= get_csrf_field() ?>
                
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Assign Client</label>
                    <select name="client_id" id="projectClient" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        <option value="">-- Select Client --</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Project Name</label>
                    <input type="text" name="name" id="projectName" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Current Status</label>
                        <div id="statusDisplayBadge" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-[10px] font-bold text-slate-600 uppercase tracking-widest">
                            <!-- Populated via JS -->
                        </div>
                        <input type="hidden" name="status" id="projectStatus">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Budget ($)</label>
                        <input type="number" step="0.01" name="budget" id="projectBudget" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Start Date</label>
                        <input type="date" name="start_date" id="projectStart" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">End Date (Est.)</label>
                        <input type="date" name="end_date" id="projectEnd" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Assign Department</label>
                    <select name="department_id" id="projectDept" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        <option value="">-- No Department --</option>
                        <?php foreach ($departments_list as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Brief / Description</label>
                    <textarea name="description" id="projectDesc" rows="3" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary custom-scrollbar"></textarea>
                </div>
                
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeProjectModal()" class="flex-1 py-4 rounded-2xl border border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Cancel</button>
                    <button type="submit" id="projectSaveBtn" class="flex-1 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-bold uppercase tracking-[0.2em]">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    document.getElementById('toastMessage').innerText = msg;
    document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'error';
    document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] ${type==='success'?'bg-slate-900 text-white':'bg-red-600 text-white'}`;
    t.style.transform = 'translateX(0)';
    setTimeout(() => t.style.transform = 'translateX(150%)', 4000);
}

function openProjectModal() {
    document.getElementById('modalTitle').innerText = 'Register Project';
    document.getElementById('projectForm').reset();
    document.getElementById('projectId').value = '';
    document.getElementById('projectStatus').value = 'Planning';
    document.getElementById('statusDisplayBadge').innerText = 'Planning';
    document.getElementById('projectStart').value = new Date().toISOString().split('T')[0];
    document.getElementById('projectModal').classList.add('open');
}

function closeProjectModal() { document.getElementById('projectModal').classList.remove('open'); }

async function editProject(id) {
    const res = await fetch(`?ajax_action=get_project&id=${id}`);
    const data = await res.json();
    document.getElementById('modalTitle').innerText = 'Edit Project';
    document.getElementById('projectId').value = data.id;
    document.getElementById('projectClient').value = data.client_id;
    document.getElementById('projectName').value = data.name;
    
    // Status Display
    document.getElementById('statusDisplayBadge').innerText = data.status;
    document.getElementById('projectStatus').value = data.status;
    
    document.getElementById('projectBudget').value = data.budget;
    document.getElementById('projectStart').value = data.start_date;
    document.getElementById('projectEnd').value = data.end_date || '';
    document.getElementById('projectDept').value = data.department_id || '';
    document.getElementById('projectDesc').value = data.description || '';
    document.getElementById('projectModal').classList.add('open');
}

document.getElementById('projectForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('projectSaveBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
        const fd = new FormData(this);
        const res = await fetch('?ajax_action=save_project', { method: 'POST', body: fd });
        const text = await res.text();
        try {
            const result = JSON.parse(text);
            if (result.status === 'success') {
                closeProjectModal();
                showToast(result.message);
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(result.message, 'error');
            }
        } catch(e) {
            showToast('Server Error: ' + text.substring(0, 100), 'error');
        }
    } catch(err) {
        showToast('Request failed: ' + err.message, 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Project';
    }
});

async function loadProject(id, cardEl) {
    currentProjectId = id;
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);

    document.querySelectorAll('.group').forEach(el => el.classList.remove('ring-2', 'ring-primary', 'border-transparent'));
    if (cardEl) cardEl.classList.add('ring-2', 'ring-primary', 'border-transparent');
    else {
        const target = document.querySelector(`[onclick*="loadProject(${id})"]`);
        if (target) target.classList.add('ring-2', 'ring-primary', 'border-transparent');
    }

    const res = await fetch(`?ajax_action=load_details&id=${id}`);
    const data = await res.json();
    
    document.getElementById('canvasHeader').innerHTML = data.header_html;
    document.getElementById('detailPane').innerHTML = data.body_html;
    
    // Open Canvas
    document.getElementById('detailBackdrop').classList.remove('pointer-events-none', 'opacity-0');
    document.getElementById('detailCanvas').classList.remove('translate-x-full');
}

function closeProjectDetail() {
    document.getElementById('detailBackdrop').classList.add('pointer-events-none', 'opacity-0');
    document.getElementById('detailCanvas').classList.add('translate-x-full');
    
    const url = new URL(window.location);
    url.searchParams.delete('id');
    window.history.pushState({}, '', url);
}

window.onload = () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    if (id) loadProject(id);
    
    initMilestoneLogic();

    <?php if (!$guide_dismissed): ?>
    setTimeout(() => {
        document.getElementById('workflowGuide').classList.remove('hidden');
    }, 500);
    <?php endif; ?>
};

function dismissGuide(permanent) {
    document.getElementById('workflowGuide').classList.add('hidden');
    if (permanent) {
        var token = document.querySelector('#workflowGuide input[name=\'csrf_token\']')?.value || CSRF_TOKEN;
        fetch('?ajax_action=guide_dismiss', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'csrf_token=' + token });
    }
}

async function toggleProjectHold(id, current) {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('current_status', current);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=toggle_project_hold', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.status === 'success') {
        showToast(`Project ${data.new_status}`);
        loadProject(id);
    }
}

// Consolidated Milestone Form logic
function initMilestoneLogic() {
    const form = document.getElementById('milestoneForm');
    if (!form) return;
    form.onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const pid = fd.get('project_id') || currentProjectId;
        const res = await fetch('?ajax_action=save_milestone', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
            showToast('Milestone saved');
            closeMilestoneModal();
            loadProject(pid);
        } else {
            showToast(data.message, 'error');
        }
    };
}


async function updateMilestoneStatus(id, pid, status) {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('status', status);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=update_milestone_status', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.status === 'success') {
        showToast(`Milestone marked as ${status}`);
        loadProject(pid);
    }
}

async function toggleSubMilestone(id, pid) {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=toggle_sub_milestone', { method: 'POST', body: fd });
    loadProject(pid);
}

function showSubInput(msId) {
    const div = document.getElementById(`subInput_${msId}`);
    div.classList.toggle('hidden');
    if (!div.classList.contains('hidden')) div.querySelector('input').focus();
}

async function saveSubMilestone(msId, pid) {
    const title = document.getElementById(`subText_${msId}`).value;
    if (!title) return;
    const fd = new FormData();
    fd.append('milestone_id', msId);
    fd.append('title', title);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=save_sub_milestone', { method: 'POST', body: fd });
    loadProject(pid);
}

async function deleteSubMilestone(id, pid) {
    if (!confirm('Delete this task?')) return;
    const fd = new FormData();
    fd.append('id', id);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=delete_sub_milestone', { method: 'POST', body: fd });
    loadProject(pid);
}

function toggleMilestoneActions(id) {
    document.getElementById(`milestoneActions_${id}`).classList.toggle('hidden');
}

async function deleteMilestone(id, pid) {
    if (!confirm('Delete milestone and all its tasks?')) return;
    const fd = new FormData();
    fd.append('id', id);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=delete_milestone', { method: 'POST', body: fd });
    loadProject(pid);
}

async function openMilestoneChat(msId, title) {
    document.getElementById('msChatId').value = msId;
    document.getElementById('msChatTitle').innerText = title;
    document.getElementById('msChatContent').innerHTML = '<div class="text-center py-10 animate-pulse text-slate-300 uppercase text-[10px] font-bold tracking-widest">Loading logs...</div>';
    document.getElementById('msChatModal').classList.add('open');
    
    const res = await fetch(`?ajax_action=get_milestone_reports&milestone_id=${msId}`);
    const reports = await res.json();
    renderMsChat(reports);
}

function renderMsChat(reports) {
    const cont = document.getElementById('msChatContent');
    if (reports.length === 0) {
        cont.innerHTML = '<div class="text-center py-20 text-slate-300 italic text-xs">No entries for this milestone.</div>';
        return;
    }
    cont.innerHTML = reports.map(r => `
        <div class="flex flex-col ${r.sender_type === 'Admin' ? 'items-end' : 'items-start'}">
            <div class="flex items-center gap-2 mb-1 px-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">${r.sender_name}</span>
                <span class="text-[8px] text-slate-300 font-medium">${new Date(r.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
            </div>
            <div class="${r.sender_type === 'Admin' ? 'bg-primary text-on-primary rounded-tr-none' : 'bg-white text-slate-600 rounded-tl-none border border-slate-100'} px-4 py-2.5 rounded-2xl shadow-sm text-xs leading-relaxed font-medium">
                ${r.content.replace(/\n/g, '<br>')}
            </div>
        </div>
    `).join('');
    cont.scrollTop = cont.scrollHeight;
}

function closeMsChat() { document.getElementById('msChatModal').classList.remove('open'); }

document.getElementById('msChatForm').onsubmit = async (e) => {
    e.preventDefault();
    const input = document.getElementById('msChatInput');
    const msId = document.getElementById('msChatId').value;
    const fd = new FormData();
    fd.append('milestone_id', msId);
    fd.append('project_id', currentProjectId);
    fd.append('content', input.value);
    fd.append('csrf_token', CSRF_TOKEN);
    
    const res = await fetch('?ajax_action=add_milestone_report', { method: 'POST', body: fd });
    input.value = '';
    openMilestoneChat(msId, document.getElementById('msChatTitle').innerText);
};

// Project Form consolidated logic is already handled by addEventListener at line 756

async function deleteProject(id) {
    if (!confirm('Delete this project? All associated reports will be lost.')) return;
    const fd = new FormData();
    fd.append('id', id);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=delete_project', { method: 'POST', body: fd });
    const result = await res.json();
    if (result.status === 'success') location.reload();
    else showToast(result.message, 'error');
}

async function assignAsset(projectId) {
    const assetId = document.getElementById('assetSearchSelect').value;
    if (!assetId) return;
    const fd = new FormData();
    fd.append('project_id', projectId);
    fd.append('asset_id', assetId);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=assign_asset', { method: 'POST', body: fd });
    loadProject(projectId);
}

async function removeAsset(projectId, assetId) {
    if (!confirm('Unlink this asset?')) return;
    const fd = new FormData();
    fd.append('project_id', projectId);
    fd.append('asset_id', assetId);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=remove_asset', { method: 'POST', body: fd });
    loadProject(projectId);
}

    async function confirmProjectActive(id) {
        if (!confirm('Move project to Active stage? This will lock the milestone roadmap.')) return;
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', CSRF_TOKEN);
        const res = await fetch('?ajax_action=confirm_project_active', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
            showToast('Project is now ACTIVE');
            loadProject(id);
        }
    }

    async function updateProjectField(id, field, value) {
        const fd = new FormData();
        fd.append('id', id);
        fd.append(field, value);
        fd.append('csrf_token', CSRF_TOKEN);
        const res = await fetch('?ajax_action=save_project', { method: 'POST', body: fd });
        showToast('Updated');
    }

    // MILESTONE LOGIC
    function openMilestoneModal(projectId, msId = null) {
        document.getElementById('milestoneForm').reset();
        document.getElementById('msProjectId').value = projectId;
        document.getElementById('msId').value = msId || '';
        document.getElementById('msModalTitle').innerText = msId ? 'Edit Milestone' : 'Add Project Milestone';
        document.getElementById('milestoneModal').classList.add('open');
    }

    function closeMilestoneModal() { document.getElementById('milestoneModal').classList.remove('open'); }

    // Milestone Form logic moved to consolidated section

    // TASK & UI COMPACT LOGIC
    function toggleTasks(id) {
        const el = document.getElementById('tasks_' + id);
        el.classList.toggle('hidden');
    }

    function openTaskAssign(taskId, projectId) {
        document.getElementById('assignTaskForm').reset();
        document.getElementById('assignTaskId').value = taskId;
        document.getElementById('assignTaskProjectId').value = projectId;
        document.getElementById('assignTaskModal').classList.add('open');
    }

    function closeTaskAssignModal() {
        document.getElementById('assignTaskModal').classList.remove('open');
    }

    document.getElementById('assignTaskForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const res = await fetch('?ajax_action=assign_task', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
            closeTaskAssignModal();
            loadProject(document.getElementById('assignTaskProjectId').value);
        }
    });

    </script>

    <!-- Milestone Modal -->
    <div id="milestoneModal" class="modal-overlay">
        <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold font-headline text-slate-900" id="msModalTitle">Add Project Milestone</h3>
                <button onclick="closeMilestoneModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            <form id="milestoneForm" class="p-6 space-y-4">
                <input type="hidden" name="id" id="msId">
                <input type="hidden" name="project_id" id="msProjectId">
                <?= get_csrf_field() ?>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Milestone Title</label>
                    <input type="text" name="title" id="msTitle" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Description / Goals</label>
                    <textarea name="description" id="msDesc" rows="3" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary custom-scrollbar"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Due Date (Optional)</label>
                    <input type="date" name="due_date" id="msDue" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeMilestoneModal()" class="flex-1 px-4 py-3 border border-slate-100 text-slate-400 rounded-2xl text-xs font-bold hover:bg-slate-50 transition-colors uppercase tracking-widest">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-primary text-on-primary rounded-2xl text-xs font-bold hover:shadow-lg transition-all uppercase tracking-widest">Save Milestone</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Milestone Chat Modal -->
    <div id="msChatModal" class="modal-overlay">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                <div>
                    <h3 class="font-bold font-headline text-slate-900" id="msChatTitle">Milestone Logs</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Contextual Communication Log</p>
                </div>
                <button onclick="closeMsChat()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            <div id="msChatContent" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-slate-50/30">
                <!-- Loaded via AJAX -->
            </div>
            <div class="p-6 border-t border-slate-100 bg-white shrink-0">
                <form id="msChatForm" class="relative group">
                    <input type="hidden" id="msChatId">
                    <?= get_csrf_field() ?>
                    <textarea id="msChatInput" required placeholder="Add a log entry or internal note..." class="w-full bg-slate-50 border-slate-100 rounded-2xl px-5 py-3.5 text-xs focus:ring-2 focus:ring-primary/20 min-h-[60px] max-h-[120px] custom-scrollbar resize-none pr-12 transition-all"></textarea>
                    <button type="submit" class="absolute right-3 bottom-3 w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shadow-lg active:scale-95">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Task Assign Modal -->
    <div id="assignTaskModal" class="modal-overlay">
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold font-headline text-slate-900">Assign Task</h3>
                <button onclick="closeTaskAssignModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            <form id="assignTaskForm" class="p-6 space-y-4">
                <input type="hidden" name="task_id" id="assignTaskId">
                <input type="hidden" id="assignTaskProjectId">
                <?= get_csrf_field() ?>
                
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">To Department</label>
                    <select name="department_id" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        <option value="">-- Select Department --</option>
                        <?php foreach ($departments_list as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">To Individual</label>
                    <select name="admin_id" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        <option value="">-- Select Person --</option>
                        <?php foreach ($admins_all as $a): ?>
                            <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="w-full py-3 bg-primary text-on-primary rounded-2xl text-xs font-bold hover:shadow-lg transition-all uppercase tracking-widest">Assign</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
