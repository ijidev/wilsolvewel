<?php
include '../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'save_project') {
        $id = (int)($_POST['id'] ?? 0);
        $client_id = (int)($_POST['client_id'] ?? 0);
        $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
        $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
        $status = $conn->real_escape_string($_POST['status'] ?? 'Planning');
        $start_date = $conn->real_escape_string($_POST['start_date'] ?? date('Y-m-d'));
        $end_date = !empty($_POST['end_date']) ? "'" . $conn->real_escape_string($_POST['end_date']) . "'" : "NULL";
        $budget = (float)($_POST['budget'] ?? 0);

        if (empty($name) || $client_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Project Name and Client are required.']); exit;
        }

        if ($id > 0) {
            $sql = "UPDATE projects SET client_id=$client_id, name='$name', description='$description', status='$status', start_date='$start_date', end_date=$end_date, budget=$budget WHERE id=$id";
            $conn->query($sql);
            if ($conn->error) { echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }
            log_audit($conn, 'Update', 'Project', 'Admin', $admin_id, "Updated project: $name (ID: $id)");
            echo json_encode(['status' => 'success', 'message' => 'Project updated.']);
        } else {
            $sql = "INSERT INTO projects (client_id, name, description, status, start_date, end_date, budget) VALUES ($client_id, '$name', '$description', '$status', '$start_date', $end_date, $budget)";
            $conn->query($sql);
            if ($conn->error) { echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }
            $new_id = $conn->insert_id;
            log_audit($conn, 'Create', 'Project', 'Admin', $admin_id, "Created new project: $name (ID: $new_id)");
            echo json_encode(['status' => 'success', 'message' => 'Project created.']);
        }
        exit;
    }

    if ($_GET['ajax_action'] == 'get_project') {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT * FROM projects WHERE id = $id");
        echo json_encode($res->fetch_assoc());
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_project') {
        $id = (int)$_GET['id'];
        $conn->query("DELETE FROM projects WHERE id = $id");
        log_audit($conn, 'Delete', 'Project', 'Admin', $admin_id, "Deleted project ID: $id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'add_report') {
        $project_id = (int)$_POST['project_id'];
        $content = $conn->real_escape_string(trim($_POST['content'] ?? ''));

        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']); exit;
        }

        $sql = "INSERT INTO project_reports (project_id, sender_type, sender_id, content) VALUES ($project_id, 'Admin', $admin_id, '$content')";
        $conn->query($sql);
        if ($conn->error) { echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }
        
        log_audit($conn, 'Create', 'Project', 'Admin', $admin_id, "Sent a message in project ID: $project_id");
        
        $new_id = $conn->insert_id;
        $res = $conn->query("SELECT pr.*, COALESCE(a.name, 'Unknown Admin') as sender_name FROM project_reports pr LEFT JOIN admins a ON pr.sender_id = a.id WHERE pr.id = $new_id");
        $report = $res->fetch_assoc();
        
        // Chat bubble style
        $html = '<div class="flex flex-col items-end mb-4">';
        $html .= '<div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-4 py-2.5 max-w-[85%] shadow-sm">';
        $html .= '<p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">' . htmlspecialchars($report['content']) . '</p>';
        $html .= '<p class="text-[9px] font-bold opacity-60 mt-1 uppercase tracking-widest">' . date('H:i', strtotime($report['created_at'])) . '</p>';
        $html .= '</div></div>';

        echo json_encode(['status' => 'success', 'html' => $html]);
        exit;
    }

    if ($_GET['ajax_action'] == 'assign_asset') {
        $project_id = (int)$_POST['project_id'];
        $asset_id = (int)$_POST['asset_id'];
        $conn->query("INSERT IGNORE INTO project_assets (project_id, asset_id) VALUES ($project_id, $asset_id)");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'remove_asset') {
        $project_id = (int)$_POST['project_id'];
        $asset_id = (int)$_POST['asset_id'];
        $conn->query("DELETE FROM project_assets WHERE project_id = $project_id AND asset_id = $asset_id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'confirm_project_active') {
        $id = (int)$_POST['id'];
        $conn->query("UPDATE projects SET status = 'Active' WHERE id = $id");
        log_audit($conn, 'Update', 'Project', 'Admin', $admin_id, "Project #$id transitioned to Active");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'toggle_project_hold') {
        $id = (int)$_POST['id'];
        $current = $_POST['current_status'];
        
        // If resuming, we need to know if it was Planning or Active.
        // For simplicity, let's check if it has any milestones.
        // Or better, let's just use a simple toggle.
        // If resuming from Hold:
        if ($current == 'On Hold') {
            // Check if it was confirmed active before? 
            // We can check if status was ever Active in audit logs, 
            // but for now let's just assume Active if it has approved milestones or just default to Active.
            $new = 'Active';
        } else {
            $new = 'On Hold';
        }
        
        $conn->query("UPDATE projects SET status = '$new' WHERE id = $id");
        echo json_encode(['status' => 'success', 'new_status' => $new]);
        exit;
    }

    if ($_GET['ajax_action'] == 'save_milestone') {
        $id = (int)($_POST['id'] ?? 0);
        $project_id = (int)($_POST['project_id'] ?? 0);
        $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
        $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
        $due_date = !empty($_POST['due_date']) ? "'" . $conn->real_escape_string($_POST['due_date']) . "'" : "NULL";

        // Check project status - cannot add/edit milestones if Active or Completed
        if ($project_id > 0) {
            $p_res = $conn->query("SELECT status FROM projects WHERE id = $project_id");
            $p = $p_res->fetch_assoc();
            if ($p['status'] !== 'Planning') {
                echo json_encode(['status' => 'error', 'message' => 'Cannot modify milestones once project is ' . $p['status']]);
                exit;
            }
        }

        if ($id > 0) {
            $conn->query("UPDATE project_milestones SET title='$title', description='$description', due_date=$due_date WHERE id=$id");
        } else {
            $res = $conn->query("SELECT MAX(order_index) as max_idx FROM project_milestones WHERE project_id = $project_id");
            $idx = (int)($res->fetch_assoc()['max_idx'] ?? -1) + 1;
            $conn->query("INSERT INTO project_milestones (project_id, title, description, due_date, order_index) VALUES ($project_id, '$title', '$description', $due_date, $idx)");
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_milestone') {
        $id = (int)$_GET['id'];
        $conn->query("DELETE FROM project_milestones WHERE id = $id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'get_milestone') {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT * FROM project_milestones WHERE id = $id");
        echo json_encode($res->fetch_assoc());
        exit;
    }

    if ($_GET['ajax_action'] == 'update_milestone_status') {
        $id = (int)$_POST['id'];
        $status = $conn->real_escape_string($_POST['status']);
        
        $conn->query("UPDATE project_milestones SET status='$status' WHERE id=$id");
        
        // Auto-complete project check
        $res = $conn->query("SELECT project_id FROM project_milestones WHERE id = $id");
        $ms = $res->fetch_assoc();
        $pid = $ms['project_id'];
        
        $res = $conn->query("SELECT COUNT(*) as total FROM project_milestones WHERE project_id = $pid");
        $total = $res->fetch_assoc()['total'];
        
        $res = $conn->query("SELECT COUNT(*) as done FROM project_milestones WHERE project_id = $pid AND status = 'Completed'");
        $done = $res->fetch_assoc()['done'];
        
        if ($total > 0 && $total == $done) {
            $conn->query("UPDATE projects SET status = 'Completed' WHERE id = $pid");
            log_audit($conn, 'Update', 'Project', 'Admin', $admin_id, "Project #$pid automatically marked Completed");
        } else if ($total > 0 && $done < $total) {
            // If it was Completed but we added/undid something, move back to Active
            $res = $conn->query("SELECT status FROM projects WHERE id = $pid");
            if ($res->fetch_assoc()['status'] == 'Completed') {
                $conn->query("UPDATE projects SET status = 'Active' WHERE id = $pid");
            }
        }
        
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'save_sub_milestone') {
        $milestone_id = (int)$_POST['milestone_id'];
        $title = $conn->real_escape_string(trim($_POST['title']));
        $conn->query("INSERT INTO project_sub_milestones (milestone_id, title) VALUES ($milestone_id, '$title')");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'toggle_sub_milestone') {
        $id = (int)$_POST['id'];
        $conn->query("UPDATE project_sub_milestones SET is_completed = NOT is_completed WHERE id = $id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_sub_milestone') {
        $id = (int)$_GET['id'];
        $conn->query("DELETE FROM project_sub_milestones WHERE id = $id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'get_milestone_reports') {
        $milestone_id = (int)$_GET['milestone_id'];
        $res = $conn->query("
            SELECT pr.*, IF(pr.sender_type='Admin', a.name, c.name) as sender_name 
            FROM project_reports pr 
            LEFT JOIN admins a ON (pr.sender_type = 'Admin' AND pr.sender_id = a.id)
            LEFT JOIN clients c ON (pr.sender_type = 'Client' AND pr.sender_id = c.id)
            WHERE pr.milestone_id = $milestone_id ORDER BY pr.created_at ASC
        ");
        $reports = [];
        while ($row = $res->fetch_assoc()) $reports[] = $row;
        echo json_encode($reports);
        exit;
    }

    if ($_GET['ajax_action'] == 'add_milestone_report') {
        $milestone_id = (int)$_POST['milestone_id'];
        $project_id = (int)$_POST['project_id'];
        $content = $conn->real_escape_string(trim($_POST['content']));
        $sql = "INSERT INTO project_reports (project_id, milestone_id, sender_type, sender_id, content) VALUES ($project_id, $milestone_id, 'Admin', $admin_id, '$content')";
        $conn->query($sql);
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'load_details') {
        $id = (int)$_GET['id'];
        
        // Project info
        $res = $conn->query("SELECT p.*, COALESCE(c.name, 'Deleted Client') as client_name, c.email as client_email FROM projects p LEFT JOIN clients c ON p.client_id = c.id WHERE p.id = $id");
        $proj = $res->fetch_assoc();
        
        // Milestones
        $milestones = [];
        $res = $conn->query("SELECT * FROM project_milestones WHERE project_id = $id ORDER BY order_index ASC, created_at ASC");
        while ($row = $res->fetch_assoc()) {
            $ms_id = $row['id'];
            $subs = [];
            $sub_res = $conn->query("SELECT * FROM project_sub_milestones WHERE milestone_id = $ms_id ORDER BY created_at ASC");
            while ($s = $sub_res->fetch_assoc()) $subs[] = $s;
            $row['sub_milestones'] = $subs;
            $milestones[] = $row;
        }

        // Assets
        $assigned_assets = [];
        $res = $conn->query("SELECT a.* FROM assets a JOIN project_assets pa ON a.id = pa.asset_id WHERE pa.project_id = $id");
        while ($row = $res->fetch_assoc()) $assigned_assets[] = $row;

        $all_assets = [];
        $res = $conn->query("SELECT id, name, type FROM assets ORDER BY name ASC");
        while ($row = $res->fetch_assoc()) $all_assets[] = $row;
        
        $completed_count = 0;
        foreach ($milestones as $m) if ($m['status'] == 'Completed') $completed_count++;
        $progress = count($milestones) > 0 ? round(($completed_count / count($milestones)) * 100) : 0;

        ob_start();
        ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Column (Milestones & Comms) -->
            <div class="flex-1 space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="flex items-center justify-between mb-8 relative z-10">
                        <div>
                            <h2 class="text-2xl font-bold font-headline text-slate-900"><?php echo htmlspecialchars($proj['name']); ?></h2>
                            <div class="flex items-center gap-3 mt-1">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Project ID: #PRJ-<?php echo $proj['id']; ?></p>
                                <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-widest <?php echo $proj['status']=='Active'?'bg-emerald-50 text-emerald-600':($proj['status']=='Planning'?'bg-amber-50 text-amber-600':'bg-slate-100 text-slate-500'); ?>">
                                    <?php echo $proj['status']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="toggleProjectHold(<?php echo $id; ?>, '<?php echo $proj['status']; ?>')" class="px-4 py-2 bg-white border border-slate-200 text-slate-500 rounded-xl text-[9px] font-bold uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-xs"><?php echo $proj['status'] == 'On Hold' ? 'play_arrow' : 'pause'; ?></span>
                                <?php echo $proj['status'] == 'On Hold' ? 'Resume' : 'Put on Hold'; ?>
                            </button>
                            <button onclick="editProject(<?php echo $id; ?>)" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-sm">settings</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-100 relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest">Aggregate Project Completion</p>
                            <span class="text-xs font-bold text-emerald-600"><?php echo $progress; ?>%</span>
                        </div>
                        <div class="h-2 bg-emerald-200/50 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Milestone Roadmap -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">route</span> Strategic Milestones
                        </h3>
                        <div class="flex gap-2">
                            <?php if ($proj['status'] == 'Planning'): ?>
                                <?php 
                                    $all_approved = true;
                                    if (empty($milestones)) $all_approved = false;
                                    foreach ($milestones as $m) if ($m['approval_status'] != 'Approved') $all_approved = false;
                                ?>
                                <?php if ($all_approved): ?>
                                    <button onclick="confirmProjectActive(<?php echo $id; ?>)" class="px-4 py-2 bg-primary text-on-primary rounded-xl text-[9px] font-bold uppercase tracking-widest hover:shadow-lg transition-all flex items-center gap-2">
                                        <span class="material-symbols-outlined text-xs">rocket_launch</span> Activate Project
                                    </button>
                                <?php else: ?>
                                    <div class="px-3 py-2 bg-slate-50 text-slate-400 rounded-xl text-[8px] font-bold uppercase tracking-widest border border-slate-100 flex items-center gap-2" title="All milestones must be approved by client">
                                        <span class="material-symbols-outlined text-xs">info</span> Awaiting Approval
                                    </div>
                                <?php endif; ?>

                                <button onclick="openMilestoneModal(<?php echo $id; ?>)" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[9px] font-bold uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center gap-2">
                                    <span class="material-symbols-outlined text-xs">add</span> Add Milestone
                                </button>
                            <?php else: ?>
                                <div class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[9px] font-bold uppercase tracking-widest border border-emerald-100 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-xs">lock</span> Roadmap Locked
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div id="milestoneList" class="space-y-4">
                        <?php if (empty($milestones)): ?>
                            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200">
                                <span class="material-symbols-outlined text-4xl text-slate-200">flag</span>
                                <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No milestones defined yet</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($milestones as $m): 
                                $statusClass = $m['status'] == 'Completed' ? 'bg-emerald-500 border-emerald-500' : ($m['status'] == 'In Progress' ? 'bg-amber-500 border-amber-500' : 'bg-slate-100 border-slate-200');
                                $approvalText = $m['approval_status'] == 'Approved' ? 'bg-emerald-50 text-emerald-600' : ($m['approval_status'] == 'Rejected' ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-500');
                            ?>
                            <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden transition-all hover:border-primary/20">
                                <div class="p-6 flex items-start gap-5">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 <?php echo $statusClass; ?> transition-all">
                                        <?php if ($m['status'] == 'Completed'): ?>
                                            <span class="material-symbols-outlined text-white text-sm font-bold">check</span>
                                        <?php else: ?>
                                            <span class="text-[10px] font-bold <?php echo $m['status'] == 'In Progress' ? 'text-white' : 'text-slate-400'; ?>"><?php echo $m['order_index'] + 1; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-bold text-slate-900 truncate"><?php echo htmlspecialchars($m['title']); ?></h4>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest <?php echo $approvalText; ?>"><?php echo $m['approval_status']; ?></span>
                                                <button onclick="toggleMilestoneActions(<?php echo $m['id']; ?>)" class="p-1 text-slate-300 hover:text-slate-600 transition-colors">
                                                    <span class="material-symbols-outlined text-sm">more_vert</span>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-500 mb-4"><?php echo nl2br(htmlspecialchars($m['description'])); ?></p>
                                        
                                        <!-- Sub-Milestones -->
                                        <div class="space-y-2 mb-4">
                                            <?php foreach ($m['sub_milestones'] as $sm): ?>
                                            <div class="flex items-center gap-3 group/sub">
                                                <button onclick="toggleSubMilestone(<?php echo $sm['id']; ?>, <?php echo $id; ?>)" class="w-4 h-4 rounded border <?php echo $sm['is_completed'] ? 'bg-primary border-primary text-on-primary' : 'border-slate-200 text-transparent'; ?> flex items-center justify-center transition-all">
                                                    <span class="material-symbols-outlined text-[10px] font-bold">check</span>
                                                </button>
                                                <span class="text-[11px] font-medium <?php echo $sm['is_completed'] ? 'text-slate-400 line-through' : 'text-slate-600'; ?>"><?php echo htmlspecialchars($sm['title']); ?></span>
                                                <button onclick="deleteSubMilestone(<?php echo $sm['id']; ?>, <?php echo $id; ?>)" class="opacity-0 group-hover/sub:opacity-100 text-red-300 hover:text-red-500 transition-all">
                                                    <span class="material-symbols-outlined text-xs">close</span>
                                                </button>
                                            </div>
                                            <?php endforeach; ?>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 pt-1">
                                                    <?php if ($proj['status'] == 'Planning' || $proj['status'] == 'Active'): ?>
                                                        <button onclick="showSubInput(<?php echo $m['id']; ?>)" class="text-[10px] font-bold text-primary flex items-center gap-1 hover:underline">
                                                            <span class="material-symbols-outlined text-xs">add</span> Add Task
                                                        </button>
                                                        <div id="subInput_<?php echo $m['id']; ?>" class="hidden flex-1 flex gap-2">
                                                            <input type="text" id="subText_<?php echo $m['id']; ?>" class="flex-1 bg-slate-50 border-none rounded-lg px-3 py-1 text-[10px] focus:ring-1 focus:ring-primary" placeholder="Task title...">
                                                            <button onclick="saveSubMilestone(<?php echo $m['id']; ?>, <?php echo $id; ?>)" class="px-3 bg-primary text-on-primary rounded-lg text-[10px] font-bold">Save</button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4 pt-4 border-t border-slate-50">
                                            <button onclick="openMilestoneChat(<?php echo $m['id']; ?>, '<?php echo addslashes($m['title']); ?>')" class="text-[10px] font-bold text-slate-400 flex items-center gap-1.5 hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-sm">forum</span> Milestone Logs
                                            </button>
                                            <div class="flex-1"></div>
                                            <div class="flex items-center gap-2">
                                                <?php if ($proj['status'] == 'Active'): ?>
                                                    <?php if ($m['status'] != 'Completed'): ?>
                                                        <button onclick="updateMilestoneStatus(<?php echo $m['id']; ?>, <?php echo $id; ?>, 'Completed')" class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all">Mark Done</button>
                                                    <?php endif; ?>
                                                    <?php if ($m['status'] == 'Pending'): ?>
                                                        <button onclick="updateMilestoneStatus(<?php echo $m['id']; ?>, <?php echo $id; ?>, 'In Progress')" class="px-4 py-1.5 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all">Start</button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-[9px] font-bold uppercase tracking-widest <?php echo $m['status']=='Completed'?'text-emerald-500':($m['status']=='In Progress'?'text-amber-500':'text-slate-300'); ?>">
                                                        <?php echo $m['status']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="milestoneActions_<?php echo $m['id']; ?>" class="hidden bg-slate-50 border-t border-slate-100 p-4 flex gap-4">
                                     <?php if ($proj['status'] == 'Planning'): ?>
                                         <button onclick="editMilestone(<?php echo $m['id']; ?>)" class="text-[10px] font-bold text-slate-400 flex items-center gap-1 hover:text-slate-600"><span class="material-symbols-outlined text-xs">edit</span> Edit</button>
                                         <button onclick="deleteMilestone(<?php echo $m['id']; ?>, <?php echo $id; ?>)" class="text-[10px] font-bold text-red-400 flex items-center gap-1 hover:text-red-600"><span class="material-symbols-outlined text-xs">delete</span> Delete</button>
                                     <?php else: ?>
                                         <span class="text-[10px] font-bold text-slate-300 flex items-center gap-1 italic"><span class="material-symbols-outlined text-xs">lock</span> Milestone Immutable</span>
                                     <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column (Settings & Assets) -->
            <div class="w-full lg:w-72 space-y-6 shrink-0">
                <!-- Status & Metadata -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Project Settings</h3>
                    
                    <div class="space-y-1">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Client Engagement</p>
                        <p class="text-xs font-bold text-primary"><?php echo htmlspecialchars($proj['client_name']); ?></p>
                        <p class="text-[9px] text-slate-400 lowercase"><?php echo htmlspecialchars($proj['client_email']); ?></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Start Date</p>
                            <p class="text-xs font-bold text-slate-900"><?php echo date('d M Y', strtotime($proj['start_date'])); ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Est. End</p>
                            <p class="text-xs font-bold text-slate-900"><?php echo $proj['end_date'] ? date('d M Y', strtotime($proj['end_date'])) : 'TBD'; ?></p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Assigned Client</p>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold">
                                <?php echo substr($proj['client_name'], 0, 1); ?>
                            </div>
                            <span class="text-xs font-bold text-slate-700 truncate"><?php echo htmlspecialchars($proj['client_name']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Asset Assignment -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Linked Assets</h3>
                        <span class="text-[9px] font-bold px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md"><?php echo count($assigned_assets); ?></span>
                    </div>

                    <div id="assignedAssets" class="space-y-2 mb-4">
                        <?php if (empty($assigned_assets)): ?>
                            <p class="text-[10px] font-medium text-slate-400 italic text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">No assets assigned</p>
                        <?php else: ?>
                            <?php foreach ($assigned_assets as $asset): ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 group">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-slate-900 truncate"><?php echo htmlspecialchars($asset['name']); ?></p>
                                        <p class="text-[9px] text-slate-400 uppercase tracking-widest truncate"><?php echo htmlspecialchars($asset['type']); ?></p>
                                    </div>
                                    <button onclick="removeAsset(<?php echo $id; ?>, <?php echo $asset['id']; ?>)" class="w-6 h-6 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-sm">remove_circle</span>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Quick Add Asset</label>
                        <div class="flex gap-2">
                            <select id="assetSearchSelect" class="flex-1 bg-slate-50 border-slate-100 rounded-xl px-3 py-2 text-[10px] font-bold focus:ring-1 focus:ring-primary text-slate-600 appearance-none">
                                <option value="">Select Asset...</option>
                                <?php foreach($all_assets as $as): ?>
                                    <option value="<?php echo $as['id']; ?>"><?php echo htmlspecialchars($as['name']); ?> (<?php echo htmlspecialchars($as['type']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <button onclick="assignAsset(<?php echo $id; ?>)" class="w-10 h-10 rounded-xl bg-primary text-on-primary flex items-center justify-center hover:shadow-lg transition-all active:scale-95">
                                <span class="material-symbols-outlined text-sm font-bold">add</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo json_encode(['html' => ob_get_clean()]);
        exit;
    }
}

// Fetch Projects with Milestone Progress
$projects = [];
$res = $conn->query("
    SELECT p.*, c.name as client_name,
           (SELECT COUNT(*) FROM project_milestones WHERE project_id = p.id) as total_ms,
           (SELECT COUNT(*) FROM project_milestones WHERE project_id = p.id AND status = 'Completed') as completed_ms
    FROM projects p 
    JOIN clients c ON p.client_id = c.id 
    ORDER BY p.created_at DESC
");
while ($row = $res->fetch_assoc()) {
    $row['progress'] = $row['total_ms'] > 0 ? round(($row['completed_ms'] / $row['total_ms']) * 100) : 0;
    $projects[] = $row;
}

// Fetch Clients for Assignment
$clients = [];
$res = $conn->query("SELECT id, name FROM clients ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $clients[] = $row;

$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Project Operations | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(6px);z-index:200;display:none;align-items:center;justify-content:center;padding:1rem}
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
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Project Operations</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Client Projects & Reporting</p>
        </div>
        <button onclick="openProjectModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">add_box</span> NEW PROJECT
        </button>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Master List -->
        <div class="w-full md:w-80 lg:w-96 bg-white border-r border-slate-100 overflow-y-auto custom-scrollbar flex flex-col shrink-0">
            <div class="p-6 space-y-4">
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
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3"><?php echo htmlspecialchars($p['client_name']); ?></p>
                        
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

        <!-- Detail View -->
        <div class="flex-1 bg-[#F8FAFC] overflow-y-auto custom-scrollbar relative">
            <div id="detailPane" class="p-8 lg:p-12 max-w-4xl mx-auto hidden">
                <!-- Content loaded via AJAX -->
            </div>
            <div id="emptyPane" class="absolute inset-0 flex flex-col items-center justify-center text-slate-300">
                <span class="material-symbols-outlined text-6xl mb-4">folder_special</span>
                <p class="text-sm font-bold uppercase tracking-widest">Select a project to view reports</p>
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
    // Persist to URL
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);

    document.querySelectorAll('.group').forEach(el => el.classList.remove('ring-2', 'ring-primary', 'border-transparent'));
    if (cardEl) cardEl.classList.add('ring-2', 'ring-primary', 'border-transparent');
    else {
        // Find card by ID if cardEl not provided (e.g. from URL load)
        const target = document.querySelector(`[onclick*="loadProject(${id}"]`);
        if (target) target.classList.add('ring-2', 'ring-primary', 'border-transparent');
    }
    
    document.getElementById('emptyPane').classList.add('hidden');
    const pane = document.getElementById('detailPane');
    pane.classList.remove('hidden');
    pane.innerHTML = '<div class="text-center py-20"><span class="material-symbols-outlined text-primary text-4xl animate-spin">sync</span></div>';
    
    const res = await fetch(`?ajax_action=load_details&id=${id}`);
    const data = await res.json();
    pane.innerHTML = data.html;
}

window.onload = () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    if (id) loadProject(id);
};

async function toggleProjectHold(id, current) {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('current_status', current);
    const res = await fetch('?ajax_action=toggle_project_hold', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.status === 'success') {
        showToast(`Project ${data.new_status}`);
        loadProject(id);
    }
}

document.getElementById('milestoneForm').onsubmit = async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const res = await fetch('?ajax_action=save_milestone', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.status === 'success') {
        showToast('Milestone saved');
        closeMilestoneModal();
        loadProject(currentProjectId);
    }
};

async function updateMilestoneStatus(id, pid, status) {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('status', status);
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
    const res = await fetch('?ajax_action=save_sub_milestone', { method: 'POST', body: fd });
    loadProject(pid);
}

async function deleteSubMilestone(id, pid) {
    if (!confirm('Delete this task?')) return;
    const res = await fetch(`?ajax_action=delete_sub_milestone&id=${id}`);
    loadProject(pid);
}

function toggleMilestoneActions(id) {
    document.getElementById(`milestoneActions_${id}`).classList.toggle('hidden');
}

async function deleteMilestone(id, pid) {
    if (!confirm('Delete milestone and all its tasks?')) return;
    const res = await fetch(`?ajax_action=delete_milestone&id=${id}`);
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
    
    const res = await fetch('?ajax_action=add_milestone_report', { method: 'POST', body: fd });
    input.value = '';
    openMilestoneChat(msId, document.getElementById('msChatTitle').innerText);
};

document.getElementById('projectForm').onsubmit = async (e) => {
    e.preventDefault();
    const btn = document.getElementById('projectSaveBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
        const fd = new FormData(this);
        const res = await fetch('?ajax_action=save_project', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') {
            closeProjectModal();
            showToast(result.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(result.message, 'error');
        }
    } catch(e) {
        showToast('Error', 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Project';
    }
};

async function deleteProject(id) {
    if (!confirm('Delete this project? All associated reports will be lost.')) return;
    const res = await fetch(`?ajax_action=delete_project&id=${id}`);
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
    const res = await fetch('?ajax_action=assign_asset', { method: 'POST', body: fd });
    loadProject(projectId);
}

async function removeAsset(projectId, assetId) {
    if (!confirm('Unlink this asset?')) return;
    const res = await fetch(`?ajax_action=remove_asset&project_id=${projectId}&asset_id=${assetId}`);
    loadProject(projectId);
}

    async function confirmProjectActive(id) {
        if (!confirm('Move project to Active stage? This will lock the milestone roadmap.')) return;
        const fd = new FormData();
        fd.append('id', id);
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

    document.getElementById('milestoneForm').onsubmit = async function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const pid = fd.get('project_id') || currentProjectId;
        const res = await fetch('?ajax_action=save_milestone', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') {
            closeMilestoneModal();
            loadProject(pid);
        } else {
            alert(result.message);
        }
    };

    async function updateMilestoneStatus(msId, projectId, status) {
        const fd = new FormData();
        fd.append('id', msId);
        fd.append('status', status);
        await fetch('?ajax_action=update_milestone_status', { method: 'POST', body: fd });
        loadProject(projectId);
    }

    async function deleteMilestone(msId, projectId) {
        if (!confirm('Delete this milestone and all its sub-tasks?')) return;
        await fetch(`?ajax_action=delete_milestone&id=${msId}`);
        loadProject(projectId);
    }

    async function saveSubMilestone(msId, projectId) {
        const input = document.getElementById(`subText_${msId}`);
        const fd = new FormData();
        fd.append('milestone_id', msId);
        fd.append('title', input.value);
        await fetch('?ajax_action=save_sub_milestone', { method: 'POST', body: fd });
        loadProject(projectId);
    }

    async function toggleSubMilestone(subId, projectId) {
        await fetch(`?ajax_action=toggle_sub_milestone&id=${subId}`);
        loadProject(projectId);
    }

    async function openMilestoneChat(msId, title) {
        document.getElementById('msChatId').value = msId;
        document.getElementById('msChatTitle').innerText = title;
        document.getElementById('msChatContent').innerHTML = '<div class="text-center py-10 animate-pulse text-slate-300">Loading...</div>';
        document.getElementById('msChatModal').classList.add('open');
        
        const res = await fetch(`?ajax_action=get_milestone_reports&milestone_id=${msId}`);
        const reports = await res.json();
        renderMsChat(reports);
    }

    function closeMsChat() { document.getElementById('msChatModal').classList.remove('open'); }

    function renderMsChat(reports) {
        const cont = document.getElementById('msChatContent');
        if (reports.length === 0) {
            cont.innerHTML = '<div class="text-center py-10 text-slate-300 italic text-xs">No logs for this milestone yet.</div>';
            return;
        }
        cont.innerHTML = reports.map(r => `
            <div class="flex flex-col ${r.sender_type === 'Admin' ? 'items-end' : 'items-start'}">
                <div class="flex items-center gap-2 mb-1 px-1">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">${r.sender_type === 'Admin' ? 'YOU' : r.sender_name}</span>
                    <span class="text-[8px] text-slate-300">${new Date(r.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                </div>
                <div class="${r.sender_type === 'Admin' ? 'bg-primary text-on-primary rounded-tr-none' : 'bg-white text-slate-600 rounded-tl-none border border-slate-100'} px-4 py-2.5 rounded-2xl shadow-sm text-xs leading-relaxed font-medium">
                    ${r.content.replace(/\n/g, '<br>')}
                </div>
            </div>
        `).join('');
        cont.scrollTop = cont.scrollHeight;
    }

    document.getElementById('msChatForm').onsubmit = async function(e) {
        e.preventDefault();
        const input = document.getElementById('msChatInput');
        const msId = document.getElementById('msChatId').value;
        const fd = new FormData();
        fd.append('milestone_id', msId);
        fd.append('project_id', currentProjectId);
        fd.append('content', input.value);
        
        await fetch('?ajax_action=add_milestone_report', { method: 'POST', body: fd });
        input.value = '';
        openMilestoneChat(msId, document.getElementById('msChatTitle').innerText);
    };

    async function editMilestone(id) {
        const res = await fetch(`?ajax_action=get_milestone&id=${id}`);
        const data = await res.json();
        openMilestoneModal(data.project_id, data.id);
        document.getElementById('msTitle').value = data.title;
        document.getElementById('msDesc').value = data.description;
        document.getElementById('msDue').value = data.due_date || '';
    }

    function showSubInput(id) {
        document.getElementById(`subInput_${id}`).classList.toggle('hidden');
        document.getElementById(`subText_${id}`).focus();
    }

    function toggleMilestoneActions(id) {
        document.getElementById(`milestoneActions_${id}`).classList.toggle('hidden');
    }
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
                    <textarea id="msChatInput" required placeholder="Add a log entry or internal note..." class="w-full bg-slate-50 border-slate-100 rounded-2xl px-5 py-3.5 text-xs focus:ring-2 focus:ring-primary/20 min-h-[60px] max-h-[120px] custom-scrollbar resize-none pr-12 transition-all"></textarea>
                    <button type="submit" class="absolute right-3 bottom-3 w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shadow-lg active:scale-95">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
