<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: login.php");
    exit();
}
$conn = get_db_connection();

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'load_details') {
        $id = (int)$_GET['id'];
        
        // Project info
        $res = safe_query($conn, "SELECT p.*, c.name as client_name, d.name as dept_name FROM projects p JOIN clients c ON p.client_id = c.id LEFT JOIN departments d ON p.department_id = d.id WHERE p.id = ? AND p.client_id = ?", "ii", [$id, $client_id]);
        $proj = $res->fetch_assoc();
        
        if (!$proj) {
            echo json_encode(['header_html' => '', 'body_html' => '<div class="p-6">Project not found or access denied.</div>']);
            exit;
        }

        // Milestones
        $milestones = [];
        $res = safe_query($conn, "SELECT * FROM project_milestones WHERE project_id = ? ORDER BY order_index ASC, created_at ASC", "i", [$id]);
        while ($row = $res->fetch_assoc()) {
            $ms_id = $row['id'];
            $subs = [];
            $sub_res = safe_query($conn, "SELECT sm.*, a.name as assignee_name, d.name as dept_name FROM project_sub_milestones sm LEFT JOIN admins a ON sm.assigned_to_admin = a.id LEFT JOIN departments d ON sm.assigned_to_department = d.id WHERE sm.milestone_id = ? ORDER BY sm.created_at ASC", "i", [$ms_id]);
            while ($s = $sub_res->fetch_assoc()) $subs[] = $s;
            $row['sub_milestones'] = $subs;
            $milestones[] = $row;
        }

        $completed_count = 0;
        foreach ($milestones as $m) if ($m['status'] == 'Completed') $completed_count++;
        $progress = count($milestones) > 0 ? round(($completed_count / count($milestones)) * 100) : 0;
        
        // Assets
        $assigned_assets = [];
        $res = safe_query($conn, "SELECT a.* FROM assets a JOIN project_assets pa ON a.id = pa.asset_id WHERE pa.project_id = ?", "i", [$id]);
        while ($row = $res->fetch_assoc()) $assigned_assets[] = $row;

        ob_start();
        ?>
        <!-- Canvas Header -->
        <div class="p-6 border-b border-slate-100 bg-white relative overflow-hidden shrink-0">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-1 rounded-md border border-primary/20">Client Ledger</span>
                    <span class="text-[10px] font-bold text-slate-400">#PRJ-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></span>
                </div>
                <h2 class="text-2xl font-bold font-headline text-slate-900 mb-1"><?php echo htmlspecialchars($proj['name']); ?></h2>
                <?php if (!empty($proj['status'])): ?>
                    <span class="inline-block px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest <?php echo $proj['status']=='Completed'?'bg-emerald-50 text-emerald-600':($proj['status']=='Planning'?'bg-amber-50 text-amber-600':($proj['status']=='On Hold'?'bg-red-50 text-red-500':'bg-blue-50 text-blue-600')); ?>"><?php echo htmlspecialchars($proj['status']); ?></span>
                <?php endif; ?>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="material-symbols-outlined" style="font-size:14px">schedule</span>
                        <?php echo $proj['start_date'] ? date('M d, Y', strtotime($proj['start_date'])) : 'TBD'; ?> 
                        <?php if(!empty($proj['end_date'])) echo ' - ' . date('M d, Y', strtotime($proj['end_date'])); ?>
                    </div>
                    <?php if ($proj['dept_name']): ?>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="material-symbols-outlined" style="font-size:14px">corporate_fare</span>
                            <?php echo htmlspecialchars($proj['dept_name']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($proj['budget'])): ?>
                        <div class="flex items-center gap-1.5 text-xs text-emerald-600 font-bold">
                            <span class="material-symbols-outlined" style="font-size:14px">account_balance</span>
                            $<?php echo number_format($proj['budget'], 2); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($proj['description'])): ?>
                    <div class="mt-4 pt-4 border-t border-slate-50">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Description</p>
                        <p class="text-sm text-slate-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($proj['description'])); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Assets (Read Only) -->
                <?php if (count($assigned_assets) > 0): ?>
                    <div class="mt-4 pt-4 border-t border-slate-50 flex flex-wrap gap-2">
                        <?php foreach($assigned_assets as $asset): ?>
                            <a href="<?php echo htmlspecialchars($asset['file_path']); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg hover:bg-slate-100 transition-colors group">
                                <span class="material-symbols-outlined text-[14px] text-primary">description</span>
                                <span class="text-[10px] font-bold text-slate-600 group-hover:text-slate-900"><?php echo htmlspecialchars($asset['name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $header_html = ob_get_clean();

        ob_start();
        ?>
        <!-- Canvas Body (Roadmap) -->
        <div class="p-6 bg-slate-50 min-h-full">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h3 class="font-headline text-lg font-bold text-slate-900">Project Roadmap</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Execution timeline</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Progress</span>
                        <span class="text-xl font-bold text-primary"><?php echo $progress; ?>%</span>
                    </div>
                </div>

                <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden mb-10 relative z-10">
                    <div class="h-full bg-primary rounded-full transition-all duration-1000" style="width: <?php echo $progress; ?>%"></div>
                </div>

                <div class="space-y-6 relative z-10">
                    <!-- Timeline Line -->
                    <div class="absolute left-6 top-4 bottom-4 w-px bg-slate-100"></div>

                    <?php if (empty($milestones)): ?>
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <span class="material-symbols-outlined text-slate-300 text-3xl mb-2">linear_scale</span>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No Milestones Yet</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($milestones as $index => $m): 
                            $isDone = $m['status'] == 'Completed';
                            $isActive = $m['status'] == 'In Progress';
                        ?>
                        <div class="relative pl-14 group">
                            <!-- Timeline Dot -->
                            <div class="absolute left-1.5 top-2 w-9 h-9 rounded-full border-4 <?php echo $isDone ? 'bg-primary border-primary' : ($isActive ? 'bg-white border-primary shadow-sm' : 'bg-slate-50 border-white'); ?> flex items-center justify-center z-10 transition-colors">
                                <?php if ($isDone): ?>
                                    <span class="material-symbols-outlined text-on-primary text-[14px] font-bold">check</span>
                                <?php else: ?>
                                    <span class="text-[9px] font-bold <?php echo $isActive ? 'text-primary' : 'text-slate-400'; ?>"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="bg-white rounded-2xl p-4 border <?php echo $isActive ? 'border-primary/20 shadow-sm' : 'border-slate-100'; ?> transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900 leading-none"><?php echo htmlspecialchars($m['title']); ?></h4>
                                        <?php if ($m['due_date']): ?>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1.5 flex items-center gap-1">
                                                <span class="material-symbols-outlined" style="font-size:12px">event</span>
                                                <?php echo date('M d, Y', strtotime($m['due_date'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <button onclick="openMsChat(<?php echo $m['id']; ?>)" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-primary transition-colors tooltip" title="View Discussion">
                                        <span class="material-symbols-outlined" style="font-size:14px">forum</span>
                                    </button>
                                </div>

                                <?php if ($m['description']): ?>
                                    <p class="text-xs text-slate-500 mb-4 leading-relaxed"><?php echo nl2br(htmlspecialchars($m['description'])); ?></p>
                                <?php endif; ?>

                                <!-- Tasks -->
                                <?php if (count($m['sub_milestones']) > 0): ?>
                                <div class="bg-slate-50 rounded-xl p-3 space-y-2 mt-4 border border-slate-100/50">
                                    <?php foreach ($m['sub_milestones'] as $sub): 
                                        $subDone = $sub['is_completed'];
                                    ?>
                                    <div class="flex items-start gap-2 group/task">
                                        <div class="mt-0.5 shrink-0 w-4 h-4 rounded-full border <?php echo $subDone ? 'bg-primary border-primary flex items-center justify-center' : 'bg-white border-slate-200'; ?>">
                                            <?php if ($subDone): ?><span class="material-symbols-outlined text-on-primary" style="font-size:10px; font-weight:bold;">check</span><?php endif; ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-[11px] font-medium <?php echo $subDone ? 'text-slate-400 line-through' : 'text-slate-700'; ?> leading-tight block">
                                                <?php echo htmlspecialchars($sub['title']); ?>
                                            </span>
                                            <?php if ($sub['assignee_name'] || $sub['dept_name']): ?>
                                                <div class="flex items-center gap-1 mt-1">
                                                    <?php if ($sub['dept_name']): ?>
                                                        <span class="px-1.5 py-0.5 bg-slate-200/50 text-slate-500 rounded text-[8px] font-bold uppercase tracking-widest">
                                                            <?php echo htmlspecialchars($sub['dept_name']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($sub['assignee_name']): ?>
                                                        <span class="px-1.5 py-0.5 bg-primary/10 text-primary-700 rounded text-[8px] font-bold uppercase tracking-widest">
                                                            <?php echo htmlspecialchars($sub['assignee_name']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        echo json_encode([
            'header_html' => $header_html,
            'body_html' => ob_get_clean()
        ]);
        exit;
    }

    if ($_GET['ajax_action'] == 'get_milestone_reports') {
        $milestone_id = (int)$_GET['milestone_id'];
        
        // Ensure client owns the project this milestone belongs to
        $check = safe_query($conn, "SELECT p.client_id FROM project_milestones m JOIN projects p ON m.project_id = p.id WHERE m.id = ?", "i", [$milestone_id]);
        $ms = $check->fetch_assoc();
        if (!$ms || $ms['client_id'] != $client_id) {
            echo json_encode([]); exit;
        }

        $res = safe_query($conn, "SELECT pr.*, IF(pr.sender_type='Admin', a.name, c.name) as sender_name FROM project_reports pr LEFT JOIN admins a ON (pr.sender_type = 'Admin' AND pr.sender_id = a.id) LEFT JOIN clients c ON (pr.sender_type = 'Client' AND pr.sender_id = c.id) WHERE pr.milestone_id = ? ORDER BY pr.created_at ASC", "i", [$milestone_id]);
        $reports = [];
        while ($row = $res->fetch_assoc()) $reports[] = $row;
        echo json_encode($reports);
        exit;
    }

    if ($_GET['ajax_action'] == 'add_milestone_report') {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrf_token)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
            exit;
        }

        $milestone_id = (int)$_POST['milestone_id'];
        
        // Ensure client owns the project
        $check = safe_query($conn, "SELECT p.id as project_id, p.client_id FROM project_milestones m JOIN projects p ON m.project_id = p.id WHERE m.id = ?", "i", [$milestone_id]);
        $ms = $check->fetch_assoc();
        if (!$ms || $ms['client_id'] != $client_id) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']); exit;
        }
        $project_id = $ms['project_id'];

        $content = trim($_POST['content']);
        if (!empty($content)) {
            $stmt = $conn->prepare("INSERT INTO project_reports (project_id, milestone_id, sender_type, sender_id, content) VALUES (?, ?, 'Client', ?, ?)");
            $stmt->bind_param("iiis", $project_id, $milestone_id, $client_id, $content);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'propose_project') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
            exit;
        }
        $title = trim($_POST['asset_model'] ?? '') . ' - ' . trim($_POST['serial_number'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if (empty($title) || empty($description)) {
            echo json_encode(['status' => 'error', 'message' => 'Asset model, serial number, and description are required.']);
            exit;
        }
        $dept_id = get_auto_assigned_department($conn, 'project_proposal', $title . ' ' . $description);
        $stmt = $conn->prepare("INSERT INTO projects (client_id, department_id, name, description, status, budget, created_at) VALUES (?, ?, ?, ?, 'Planning', 0, NOW())");
        $dept_id_val = $dept_id ?: null;
        $stmt->bind_param("iiss", $client_id, $dept_id_val, $title, $description);
        if ($stmt->execute()) {
            $project_id = $stmt->insert_id;
            $stmt->close();
            log_audit($conn, 'Create', 'Projects', 'Client', $client_id, 'Proposed Project', ['title' => $title]);
            $client_name = $_SESSION['client_name'] ?? 'A client';
            notify_department_admins($conn, $dept_id, 'New project proposal', htmlspecialchars($title), 'admin/projects.php?id=' . $project_id, 'add_task', 'New Project Proposal: ' . htmlspecialchars($title), email_template('New Project Proposal', '<p>' . htmlspecialchars($client_name) . ' has proposed a new project:</p><p><strong>Project:</strong> ' . htmlspecialchars($title) . '</p><p style="margin-top:20px"><a href="' . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . '/admin/projects.php?id=' . $project_id . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px">View Proposal</a></p>'));
            echo json_encode(['status' => 'success', 'project_id' => $project_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'DB error: ' . $stmt->error]);
            $stmt->close();
        }
        exit;
    }

    if ($_GET['ajax_action'] == 'guide_dismiss') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid token']); exit;
        }
        set_setting('guide_dismissed_client_' . $client_id, '1');
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$guide_dismissed = get_setting('guide_dismissed_client_' . $client_id, '');

// Fetch all projects for the list view
$projects_list_res = safe_query($conn, "SELECT * FROM projects WHERE client_id = ? ORDER BY created_at DESC", "i", [$client_id]);
$projects = [];
while ($row = $projects_list_res->fetch_assoc()) $projects[] = $row;
$total_projects = count($projects);

$page_title = 'WILSOVLEWEL | Client Projects';
$page_h1 = 'System Ledger';
$page_h1_sub = 'Project Tracking';
$page_h1_action = '<button onclick="document.getElementById(\'proposeModal\').classList.remove(\'hidden\')" class="bg-primary text-on-primary px-3 sm:px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95 shrink-0 ml-2"><span class="material-symbols-outlined text-sm">add_circle</span> <span class="hidden sm:inline">NEW PROPOSAL</span></button>';
$page_styles = '
    .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(6px);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem}
    .modal-overlay.open{display:flex}
';

ob_start();
?>
<div class="flex flex-col h-full">
    <!-- Workflow Guide -->
    <div class="hidden lg:block absolute bottom-6 right-6 z-50">
        <button onclick="document.getElementById('clientWorkflowGuide').classList.toggle('hidden')" class="w-10 h-10 rounded-full bg-orange-500 border border-orange-400 shadow-lg flex items-center justify-center text-white hover:bg-orange-600 hover:border-orange-500 transition-all" title="Workflow Guide">
            <span class="material-symbols-outlined text-lg">help</span>
        </button>
    </div>
    <div id="clientWorkflowGuide" class="hidden fixed inset-0 z-[400] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" onclick="if(event.target===this)document.getElementById('clientWorkflowGuide').classList.add('hidden')">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full max-h-[85vh] overflow-y-auto custom-scrollbar p-8" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold font-headline text-slate-900">Project Workflow Guide</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Client Portal</p>
                </div>
                <button onclick="document.getElementById('clientWorkflowGuide').classList.add('hidden')" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200"><span class="material-symbols-outlined text-sm">close</span></button>
                <?= get_csrf_field() ?>
            </div>
            <div class="space-y-5">
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-sm font-bold">1</div>
                    <div><h4 class="text-sm font-bold text-slate-900">Project Proposed</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">You submit a project proposal or Wilsolvewel creates one on your behalf. The project enters <strong>Planning</strong> phase where milestones are defined.</p></div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-sm font-bold">2</div>
                    <div><h4 class="text-sm font-bold text-slate-900">Review Milestones</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Review the milestones created by the project team. Each milestone outlines a key deliverable or phase of work. Use the milestone discussion chat to ask questions or request changes.</p></div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center shrink-0 text-sm font-bold">3</div>
                    <div><h4 class="text-sm font-bold text-slate-900">Approve or Reject</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Approve milestones you agree with or reject those needing changes. The project can only proceed to the <strong>Development</strong> phase once all milestones are approved.</p></div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-sm font-bold">4</div>
                    <div><h4 class="text-sm font-bold text-slate-900">Track Progress</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Once the project is active, monitor milestone progress in real time. Each milestone shows its status — Pending, In Progress, or Completed.</p></div>
                </div>
                <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center shrink-0 text-sm font-bold">5</div>
                        <div><h4 class="text-sm font-bold text-slate-900">Communicate</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Use milestone discussion chats to communicate with the project team, ask for updates, or provide feedback throughout the project lifecycle.</p></div>
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
    </div>

    <script>
    function dismissGuide(permanent) {
        document.getElementById('clientWorkflowGuide').classList.add('hidden');
        if (permanent) {
            var fd = new FormData();
            fd.append('csrf_token', document.querySelector('#clientWorkflowGuide input[name=\'csrf_token\']')?.value || '<?= generate_csrf_token() ?>');
            fetch('?ajax_action=guide_dismiss', { method: 'POST', body: fd });
        }
    }

    <?php if (!$guide_dismissed): ?>
    setTimeout(function() {
        var el = document.getElementById('clientWorkflowGuide');
        if (el) el.classList.remove('hidden');
    }, 500);
    <?php endif; ?>
    </script>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" id="projectSearch" placeholder="Search projects..." class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>
        </div>
        <div class="p-4 lg:p-6" id="projectList">
            <?php if ($total_projects == 0): ?>
                <div class="text-center py-10">
                    <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">folder_open</span>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No Projects Found</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($projects as $p): ?>
                    <button onclick="loadProject(<?php echo $p['id']; ?>)" class="group relative bg-white border border-slate-100 rounded-3xl p-5 cursor-pointer hover:border-primary/50 transition-all hover:shadow-md text-left">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest <?php echo $p['status']=='Completed'?'bg-emerald-50 text-emerald-600':($p['status']=='Planning'?'bg-amber-50 text-amber-600':($p['status']=='On Hold'?'bg-red-50 text-red-500':'bg-blue-50 text-blue-600')); ?>"><?php echo $p['status']; ?></span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-primary transition-colors leading-tight mb-1 pr-4"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <div class="flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined" style="font-size:12px">calendar_today</span> <?php echo $p['start_date'] ? date('M d, Y', strtotime($p['start_date'])) : 'TBD'; ?></span>
                        </div>
                        <?php if (!empty($p['budget'])): ?>
                        <p class="text-[11px] font-bold text-emerald-600 mt-2">$<?php echo number_format($p['budget'], 2); ?></p>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="detailBackdrop" onclick="closeProjectDetail()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[300] opacity-0 pointer-events-none transition-all duration-500"></div>
    <div id="detailCanvas" class="fixed top-0 right-0 h-full w-full lg:w-[680px] bg-white z-[301] translate-x-full transition-transform duration-500 ease-in-out shadow-2xl flex flex-col">
        <div class="absolute top-4 right-4 z-[302]">
            <button onclick="closeProjectDetail()" class="w-10 h-10 rounded-2xl bg-white/90 backdrop-blur-sm border border-slate-200 shadow-lg flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-white transition-all active:scale-90">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        <div id="detailContent" class="flex-1 flex flex-col overflow-hidden bg-slate-50">
            <div id="projectHeader"></div>
            <div id="projectBody" class="flex-1 overflow-y-auto custom-scrollbar"></div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
$page_main_class = 'max-w-7xl mx-auto w-full px-4 sm:px-6 py-6';
$page_class = 'flex flex-col min-h-screen';

$page_after_main = '
    <div id="proposeModal" class="fixed inset-0 z-[300] flex items-center justify-center bg-black/50 hidden p-4" style="backdrop-filter:blur(4px)">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto custom-scrollbar p-6 sm:p-8" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold font-headline text-slate-900">Propose Project</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Submit a new project for review</p>
                </div>
                <button onclick="closeProposeModal()" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            <form id="proposeForm" class="space-y-5">
                ' . get_csrf_field() . '
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Machine Model</label>
                        <input type="text" name="asset_model" required placeholder="e.g. Caterpillar D11" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-primary/20">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Serial / Asset Number</label>
                        <input type="text" name="serial_number" required placeholder="e.g. SN-8829-XL" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Issue Description</label>
                    <textarea name="description" required rows="4" placeholder="Describe the mechanical anomalies, symptoms, or required service..." class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-primary/20 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeProposeModal()" class="px-6 py-3 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                    <button type="submit" id="proposeSubmitBtn" class="px-6 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-800 transition-all active:scale-95">Submit Proposal</button>
                </div>
            </form>
            <div id="proposeSuccess" class="hidden text-center py-8">
                <span class="material-symbols-outlined text-5xl text-emerald-500 mb-3">check_circle</span>
                <h4 class="text-lg font-bold font-headline text-slate-900">Proposal Submitted!</h4>
                <p class="text-xs text-slate-500 mt-1">Ref: <span id="proposeRef" class="font-bold text-primary"></span></p>
                <button onclick="closeProposeModal()" class="mt-6 px-6 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest">Done</button>
            </div>
        </div>
    </div>

    <div id="msChatModal" class="modal-overlay">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
            <div class="p-4 sm:p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                <div class="min-w-0">
                    <h3 class="font-headline font-bold text-slate-900 truncate" id="msChatTitle">Milestone Discussion</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Communicate with your team</p>
                </div>
                <button onclick="closeMsChat()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center transition-colors shrink-0 ml-2"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            <div id="msChatContent" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 custom-scrollbar bg-slate-50/30"></div>
            <div class="p-4 sm:p-6 border-t border-slate-100 bg-white shrink-0">
                <form id="msChatForm" class="relative group">
                    ' . get_csrf_field() . '
                    <input type="hidden" id="msChatId">
                    <textarea id="msChatInput" required placeholder="Add a comment or ask a question..." class="w-full bg-slate-50 border-slate-100 rounded-2xl px-5 py-3.5 text-xs focus:ring-2 focus:ring-primary/20 min-h-[60px] max-h-[120px] custom-scrollbar resize-none pr-12 transition-all"></textarea>
                    <button type="submit" class="absolute right-3 bottom-3 w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shadow-lg active:scale-95">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
';

$page_scripts = '
<script>
    const clientId = ' . $client_id . ';
    let currentMsId = null;

    const urlParams = new URLSearchParams(window.location.search);
    const projectIdFromUrl = urlParams.get("id");
    if (projectIdFromUrl) {
        setTimeout(() => loadProject(projectIdFromUrl), 300);
    }

    async function loadProject(id) {
        document.getElementById("detailCanvas").classList.remove("translate-x-full");
        document.getElementById("detailBackdrop").classList.remove("opacity-0", "pointer-events-none");
        document.getElementById("projectHeader").innerHTML = \'<div class="p-6 text-center text-slate-400"><span class="material-symbols-outlined animate-spin">refresh</span></div>\';
        document.getElementById("projectBody").innerHTML = "";
        const res = await fetch("?ajax_action=load_details&id=" + id);
        const data = await res.json();
        document.getElementById("projectHeader").innerHTML = data.header_html;
        document.getElementById("projectBody").innerHTML = data.body_html;
        document.querySelectorAll("#projectList button").forEach(b => b.classList.remove("bg-white", "shadow-sm", "border-slate-200"));
        const btn = document.querySelector("button[onclick=\\"loadProject(" + id + ")\\"]");
        if (btn) btn.classList.add("bg-white", "shadow-sm", "border-slate-200");
    }

    function closeProjectDetail() {
        document.getElementById("detailCanvas").classList.add("translate-x-full");
        document.getElementById("detailBackdrop").classList.add("opacity-0", "pointer-events-none");
    }

    document.getElementById("projectSearch").addEventListener("input", function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll("#projectList button").forEach(b => {
            const text = b.innerText.toLowerCase();
            b.style.display = text.includes(term) ? "block" : "none";
        });
    });

    function openMsChat(id) {
        currentMsId = id;
        document.getElementById("msChatId").value = id;
        document.getElementById("msChatModal").classList.add("open");
        loadMsChats();
    }

    function closeMsChat() {
        document.getElementById("msChatModal").classList.remove("open");
        currentMsId = null;
    }

    async function loadMsChats() {
        if (!currentMsId) return;
        const res = await fetch("?ajax_action=get_milestone_reports&milestone_id=" + currentMsId);
        const reports = await res.json();
        let html = "";
        reports.forEach(r => {
            const isMe = r.sender_type === "Client";
            html += \'<div class="flex flex-col \' + (isMe ? "items-end" : "items-start") + \' mb-4">\' + (!isMe ? \'<span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">\' + r.sender_name + \'</span>\' : "") + \'<div class="\' + (isMe ? "bg-primary text-on-primary rounded-tr-none" : "bg-white border border-slate-100 rounded-tl-none") + \' rounded-2xl px-4 py-2.5 max-w-[85%] shadow-sm"><p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">\' + r.content + \'</p><p class="text-[9px] font-bold opacity-60 mt-1 uppercase tracking-widest">\' + new Date(r.created_at).toLocaleTimeString([], {hour: "2-digit", minute:"2-digit"}) + \'</p></div></div>\';
        });
        const container = document.getElementById("msChatContent");
        container.innerHTML = html || \'<div class="text-center py-8 text-slate-400 text-xs">No discussion yet.</div>\';
        container.scrollTop = container.scrollHeight;
    }

    document.getElementById("msChatForm").addEventListener("submit", async function(e) {
        e.preventDefault();
        const input = document.getElementById("msChatInput");
        const content = input.value.trim();
        if (!content) return;
        const fd = new FormData();
        fd.append("csrf_token", document.querySelector("#msChatForm input[name=\'csrf_token\']")?.value || "");
        fd.append("milestone_id", currentMsId);
        fd.append("content", content);
        const container = document.getElementById("msChatContent");
        if (container.innerHTML.includes("No discussion yet")) container.innerHTML = "";
        container.innerHTML += \'<div class="flex flex-col items-end mb-4 opacity-50"><div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-4 py-2.5 max-w-[85%] shadow-sm"><p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">\' + content + \'</p><p class="text-[9px] font-bold opacity-60 mt-1 uppercase tracking-widest">Sending...</p></div></div>\';
        container.scrollTop = container.scrollHeight;
        input.value = "";
        const res = await fetch("?ajax_action=add_milestone_report", { method: "POST", body: fd });
        await loadMsChats();
    });

    // ── Propose Project Modal ──────────────────────────────────────────────────
    function closeProposeModal() {
        document.getElementById("proposeModal").classList.add("hidden");
        document.getElementById("proposeForm").reset();
        document.getElementById("proposeForm").classList.remove("hidden");
        document.getElementById("proposeSuccess").classList.add("hidden");
    }

    document.getElementById("proposeForm").addEventListener("submit", async function(e) {
        e.preventDefault();
        const btn = document.getElementById("proposeSubmitBtn");
        btn.disabled = true;
        btn.innerHTML = \'<span class="material-symbols-outlined animate-spin text-sm">sync</span> Submitting...\';
        const fd = new FormData(this);
        fd.append("ajax_action", "propose_project");
        try {
            const res = await fetch("?ajax_action=propose_project", { method: "POST", body: fd });
            const data = await res.json();
            if (data.status === "success") {
                document.getElementById("proposeForm").classList.add("hidden");
                document.getElementById("proposeRef").textContent = "#PROJ-" + data.project_id;
                document.getElementById("proposeSuccess").classList.remove("hidden");
                setTimeout(() => location.reload(), 2000);
            } else {
                alert(data.message || "Submission failed.");
                btn.disabled = false;
                btn.innerHTML = "Submit Proposal";
            }
        } catch(e) {
            alert("Network error.");
            btn.disabled = false;
            btn.innerHTML = "Submit Proposal";
        }
    });
</script>
';

require_once __DIR__ . '/../components/client_layout.php';
