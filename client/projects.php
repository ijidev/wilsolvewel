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
            $milestones[] = $row;
        }
        
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
                    <span class="inline-block px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest <?php echo $proj['status']=='Completed'?'bg-emerald-50 text-emerald-600':($proj['status']=='On Hold'?'bg-red-50 text-red-500':(($proj['status']=='Active'||$proj['status']=='In Progress')?'bg-blue-50 text-blue-600':'bg-amber-50 text-amber-600')); ?>"><?php echo htmlspecialchars($proj['status']); ?></span>
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
            <?php
            $can_confirm = ($proj['status'] === 'Completed') && empty($proj['client_confirmed_at']);
            $can_download = !empty($proj['client_confirmed_at']);
            ?>
            <!-- Completion Panel -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-headline text-lg font-bold text-slate-900">Project Completion</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Final sign-off &amp; report summary</p>
                    </div>
                    <?php if (!empty($proj['client_confirmed_at'])): ?>
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[9px] font-bold uppercase tracking-widest flex items-center gap-1 shrink-0">
                            <span class="material-symbols-outlined" style="font-size:12px">verified</span> Confirmed <?php echo date('M d, Y', strtotime($proj['client_confirmed_at'])); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="confirmCompletion(<?php echo $id; ?>)" <?php echo $can_confirm ? '' : 'disabled'; ?>
                        class="flex-1 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2 <?php echo $can_confirm ? 'bg-emerald-600 text-white hover:bg-emerald-500 cursor-pointer active:scale-95' : 'bg-slate-100 text-slate-400 cursor-not-allowed'; ?>">
                        <span class="material-symbols-outlined text-sm">check_circle</span> Confirm Project Completed
                    </button>
                    <button onclick="downloadReportSummary(event, <?php echo $id; ?>)" <?php echo $can_download ? '' : 'disabled'; ?>
                        class="flex-1 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2 <?php echo $can_download ? 'bg-slate-900 text-white hover:bg-slate-700 cursor-pointer active:scale-95' : 'bg-slate-100 text-slate-400 cursor-not-allowed'; ?>">
                        <span class="material-symbols-outlined text-sm">download</span> Download Report Summary
                    </button>
                </div>
                <?php if ($proj['status'] === 'Completed' && empty($proj['client_confirmed_at'])): ?>
                    <p class="text-[10px] text-slate-400 mt-3">This project has been marked completed. Confirm completion to unlock the report summary download.</p>
                <?php elseif ($proj['status'] !== 'Completed'): ?>
                    <p class="text-[10px] text-slate-400 mt-3">Completion actions unlock when the project is marked <strong>Completed</strong> by the team.</p>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h3 class="font-headline text-lg font-bold text-slate-900">Project Reports</h3>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Report logs & updates</p>
                    </div>
                </div>

                <div class="space-y-6 relative z-10">
                    <!-- Timeline Line -->
                    <div class="absolute left-6 top-4 bottom-4 w-px bg-slate-100"></div>

                    <?php if (empty($milestones)): ?>
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <span class="material-symbols-outlined text-slate-300 text-3xl mb-2">linear_scale</span>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No Report Logs Yet</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($milestones as $index => $m): ?>
                        <div class="relative pl-14 group">
                            <!-- Timeline Dot -->
                            <div class="absolute left-1.5 top-2 w-9 h-9 rounded-full border-4 bg-slate-50 border-white flex items-center justify-center z-10 transition-colors">
                                <span class="text-[9px] font-bold text-slate-400"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                            </div>

                            <div class="bg-white rounded-2xl p-4 border border-slate-100 transition-all">
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
        $stmt = $conn->prepare("INSERT INTO projects (client_id, department_id, name, description, status, budget, created_at) VALUES (?, ?, ?, ?, 'Active', 0, NOW())");
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

    if ($_GET['ajax_action'] == 'confirm_completion') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
            exit;
        }
        $project_id = (int)($_POST['project_id'] ?? 0);
        $check = safe_query($conn, "SELECT id, status, client_confirmed_at FROM projects WHERE id = ? AND client_id = ?", "ii", [$project_id, $client_id]);
        $proj = $check->fetch_assoc();
        if (!$proj) {
            echo json_encode(['status' => 'error', 'message' => 'Project not found or access denied.']); exit;
        }
        if ($proj['status'] !== 'Completed') {
            echo json_encode(['status' => 'error', 'message' => 'This project has not been marked as completed yet.']); exit;
        }
        if (!empty($proj['client_confirmed_at'])) {
            echo json_encode(['status' => 'error', 'message' => 'You have already confirmed this project.']); exit;
        }
        $stmt = $conn->prepare("UPDATE projects SET client_confirmed_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $stmt->close();
        log_audit($conn, 'Update', 'Projects', 'Client', $client_id, "Confirmed project completion (ID: $project_id)");
        echo json_encode(['status' => 'success', 'message' => 'Project confirmed as completed.']);
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
$page_h1_action = '<div class="flex items-center gap-2"><button onclick="exportSelectedProjects()" class="bg-white border border-slate-200 text-on-surface px-3 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-slate-50 transition-all shrink-0"><span class="material-symbols-outlined text-sm">download</span> <span class="hidden sm:inline">Export</span></button><button onclick="document.getElementById(\'proposeModal\').classList.remove(\'hidden\')" class="bg-primary text-on-primary px-3 sm:px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95 shrink-0 ml-2"><span class="material-symbols-outlined text-sm">add_circle</span> <span class="hidden sm:inline">NEW PROPOSAL</span></button></div>';
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
                    <div><h4 class="text-sm font-bold text-slate-900">Proposal Submitted</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">You submit a project proposal or Wilsolvewel creates one on your behalf. The project enters the <strong>Active</strong> phase.</p></div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-sm font-bold">2</div>
                    <div><h4 class="text-sm font-bold text-slate-900">Project Tracking</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Your project status is set by the team — <strong>Active</strong>, <strong>On Hold</strong>, or <strong>Completed</strong>. Report logs list the key phases and updates for your project.</p></div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-sm font-bold">3</div>
                    <div><h4 class="text-sm font-bold text-slate-900">Report Logs</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Each report log is a report entry. Use the report log discussions to communicate with the project team, ask for updates, or provide feedback throughout the project lifecycle.</p></div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center shrink-0 text-sm font-bold">4</div>
                    <div><h4 class="text-sm font-bold text-slate-900">Completion</h4><p class="text-xs text-slate-500 mt-0.5 leading-relaxed">When the work is finished, the team sets the project to <strong>Completed</strong>. You then confirm the project is complete, which unlocks the <strong>Download Report Summary</strong> option.</p></div>
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
                    <div class="group relative bg-white border border-slate-100 rounded-3xl p-5 cursor-pointer hover:border-primary/50 transition-all hover:shadow-md text-left">
                        <div class="absolute top-3 right-3 z-10">
                            <input type="checkbox" class="project-export-checkbox rounded border-slate-300 text-primary focus:ring-primary/20" value="<?php echo $p['id']; ?>" onclick="event.stopPropagation()">
                        </div>
                        <div onclick="loadProject(<?php echo $p['id']; ?>)">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest <?php echo $p['status']=='Completed'?'bg-emerald-50 text-emerald-600':($p['status']=='On Hold'?'bg-red-50 text-red-500':(($p['status']=='Active'||$p['status']=='In Progress')?'bg-blue-50 text-blue-600':'bg-amber-50 text-amber-600')); ?>"><?php echo $p['status']; ?></span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-primary transition-colors leading-tight mb-1 pr-4"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <div class="flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined" style="font-size:12px">calendar_today</span> <?php echo $p['start_date'] ? date('M d, Y', strtotime($p['start_date'])) : 'TBD'; ?></span>
                        </div>
                        <?php if (!empty($p['budget'])): ?>
                        <p class="text-[11px] font-bold text-emerald-600 mt-2">$<?php echo number_format($p['budget'], 2); ?></p>
                        <?php endif; ?>
                    </div>
                    </div>
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
                    <h3 class="font-headline font-bold text-slate-900 truncate" id="msChatTitle">Report Log Discussion</h3>
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
        document.querySelectorAll("#projectList > div > div").forEach(function(b) { b.classList.remove("bg-white", "shadow-sm", "border-slate-200"); });
    }

    function closeProjectDetail() {
        document.getElementById("detailCanvas").classList.add("translate-x-full");
        document.getElementById("detailBackdrop").classList.add("opacity-0", "pointer-events-none");
    }

    // ── Completion Actions ────────────────────────────────────────────────────
    async function confirmCompletion(id) {
        if (!confirm("Confirm that this project is fully completed?")) return;
        const fd = new FormData();
        fd.append("project_id", id);
        fd.append("csrf_token", document.querySelector("#proposeForm input[name=\'csrf_token\']")?.value || "<?= generate_csrf_token() ?>");
        const res = await fetch("?ajax_action=confirm_completion", { method: "POST", body: fd });
        const data = await res.json();
        if (data.status === "success") {
            loadProject(id);
            setTimeout(function () { alert(data.message); }, 300);
        } else {
            alert(data.message || "Failed to confirm completion.");
        }
    }

    async function downloadReportSummary(e, id) {
        const btn = e.currentTarget;
        btn.disabled = true;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = \'<span class="material-symbols-outlined text-sm animate-spin">refresh</span>\';
        try {
            const res = await fetch("export_report_summary.php?id=" + id);
            const data = await res.json();
            if (data.status !== "success" || !data.project) {
                alert(data.message || "Failed to fetch report summary.");
                return;
            }
            generateProjectPDF([data.project]);
        } catch (err) {
            console.error("Export failed:", err);
            alert("Failed to export. Check console.");
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    // ── Export Selected Projects ───────────────────────────────────────────────
    async function exportSelectedProjects() {
        var checkboxes = document.querySelectorAll(".project-export-checkbox:checked");
        if (checkboxes.length === 0) {
            alert("Please select at least one project to export.");
            return;
        }
        var ids = Array.from(checkboxes).map(function(cb) { return cb.value; }).join(",");
        var btn = document.querySelector(\'button[onclick="exportSelectedProjects()"]\');
        var originalHTML = btn.innerHTML;
        btn.innerHTML = \'<span class="material-symbols-outlined text-sm animate-spin">refresh</span>\';
        btn.disabled = true;
        try {
            var res = await fetch("export_project_data.php?ids=" + ids);
            var data = await res.json();
            if (data.status !== "success" || !data.projects || data.projects.length === 0) {
                alert(data.message || "Failed to fetch project data.");
                return;
            }
            generateProjectPDF(data.projects);
        } catch (err) {
            console.error("Export failed:", err);
            alert("Failed to export. Check console.");
        } finally {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    }

    function escHtml(str) {
        if (!str) return "";
        var d = document.createElement("div");
        d.textContent = str;
        return d.innerHTML;
    }

    function generateProjectPDF(projects) {
        var win = window.open("", "_blank");
        if (!win) { alert("Please allow popups for this site to export."); return; }
        var html = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Project Export</title>
<style>
    @page { margin: 15mm; }
    body { font-family: "Segoe UI", Arial, sans-serif; color: #1a1a1a; padding: 0; margin: 0; }
    .page { page-break-after: always; padding: 20px; }
    .page:last-child { page-break-after: auto; }
    .header { border-bottom: 3px solid #EAB308; padding-bottom: 12px; margin-bottom: 20px; }
    .header h1 { font-size: 20px; font-weight: 800; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 1px; }
    .header .meta { font-size: 11px; color: #666; }
    .section { margin-bottom: 20px; }
    .section h2 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #EAB308; border-bottom: 1px solid #ddd; padding-bottom: 6px; margin: 0 0 10px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 20px; }
    .field { font-size: 11px; padding: 3px 0; }
    .field .label { color: #888; font-weight: 600; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; }
    .field .value { font-weight: 700; color: #1a1a1a; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    table th { background: #f5f5f5; text-align: left; padding: 6px 8px; font-weight: 700; text-transform: uppercase; font-size: 8px; letter-spacing: 0.5px; color: #666; border-bottom: 1px solid #ddd; }
    table td { padding: 6px 8px; border-bottom: 1px solid #eee; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-weight: 700; font-size: 9px; text-transform: uppercase; }
    .badge-completed { background: #d1fae5; color: #047857; }
    .badge-active { background: #dbeafe; color: #1d4ed8; }
    .badge-planning { background: #fef3c7; color: #b45309; }
    .badge-hold { background: #fee2e2; color: #b91c1c; }
    .badge-open { background: #fef3c7; color: #b45309; }
    .badge-resolved { background: #d1fae5; color: #047857; }
    .milestone { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .milestone:last-child { border-bottom: none; }
    .milestone-title { font-weight: 700; font-size: 11px; }
    .milestone-meta { font-size: 9px; color: #888; }
    .sub-task { font-size: 10px; padding-left: 16px; color: #555; }
    .footer { border-top: 1px solid #ddd; padding-top: 10px; margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style></head><body>`;
        projects.forEach(function(p, idx) {
            var statusClass = "active";
            if (p.status === "Completed") statusClass = "completed";
            else if (p.status === "On Hold") statusClass = "hold";
            html += `<div class="page"><div class="header"><h1>Project Manifest</h1><div class="meta">Project #PRJ-${String(p.id).padStart(4, "0")} | Listing ${idx + 1} of ${projects.length} | Generated ${new Date().toLocaleString()}</div></div>`;
            html += `<div class="section"><h2>Project Details</h2><div class="grid"><div class="field"><div class="label">Project Name</div><div class="value">${escHtml(p.name)}</div></div><div class="field"><div class="label">Status</div><div class="value"><span class="badge badge-${statusClass}">${p.status}</span></div></div><div class="field"><div class="label">Budget</div><div class="value">${p.budget ? "$" + parseFloat(p.budget).toLocaleString() : "N/A"}</div></div><div class="field"><div class="label">Start Date</div><div class="value">${p.start_date || "TBD"}</div></div><div class="field"><div class="label">End Date</div><div class="value">${p.end_date || "TBD"}</div></div><div class="field"><div class="label">Department</div><div class="value">${p.department_id || "Unassigned"}</div></div></div></div>`;

            if (p.description) {
                html += `<div class="section"><h2>Description</h2><p style="font-size:11px;color:#444;line-height:1.6">${escHtml(p.description)}</p></div>`;
            }

            if (p.milestones && p.milestones.length > 0) {
                html += `<div class="section"><h2>Report Logs (${p.milestones.length})</h2>`;
                p.milestones.forEach(function(m) {
                    html += `<div class="milestone"><div class="milestone-title">${escHtml(m.title)}</div>`;
                    if (m.due_date) html += `<div class="milestone-meta">Due: ${m.due_date}</div>`;
                    if (m.description) html += `<div style="font-size:10px;color:#555;margin:2px 0">${escHtml(m.description)}</div>`;
                    html += `</div>`;
                });
                html += `</div>`;
            } else {
                html += `<div class="section"><h2>Report Logs</h2><p style="font-size:10px;color:#888;font-style:italic">No report logs defined yet.</p></div>`;
            }

            if (p.assets && p.assets.length > 0) {
                html += `<div class="section"><h2>Assigned Assets (${p.assets.length})</h2><table><thead><tr><th>Asset</th></tr></thead><tbody>`;
                p.assets.forEach(function(a) {
                    html += `<tr><td>${escHtml(a.name)}</td></tr>`;
                });
                html += `</tbody></table></div>`;
            }

            if (p.tickets && p.tickets.length > 0) {
                html += `<div class="section"><h2>Associated Tickets (${p.tickets.length})</h2><table><thead><tr><th>Ticket</th><th>Subject</th><th>Status</th><th>Created</th></tr></thead><tbody>`;
                p.tickets.forEach(function(tk) {
                    var tkClass = tk.status === "Resolved" || tk.status === "Closed" ? "badge-resolved" : "badge-open";
                    html += `<tr><td>#TK-${tk.id}</td><td>${escHtml(tk.subject || "N/A")}</td><td><span class="badge ${tkClass}">${tk.status}</span></td><td>${tk.created_at_formatted || ""}</td></tr>`;
                    if (tk.replies && tk.replies.length > 0) {
                        html += `<tr><td colspan="4" style="padding:4px 8px 12px"><div style="font-size:9px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Conversation</div>`;
                        tk.replies.forEach(function(r) {
                            var senderLabel = r.sender_type === \'admin\' ? \'Staff\' : \'Client\';
                            html += `<div style="font-size:9px;padding:4px 8px;margin:2px 0;background:#f9f9f9;border-radius:4px;border-left:2px solid #EAB308"><strong>${senderLabel}</strong> <span style="color:#999">${r.created_at_formatted}</span><br>${escHtml(r.message)}</div>`;
                        });
                        html += `</td></tr>`;
                    }
                });
                html += `</tbody></table></div>`;
            }

            html += `<div class="footer">WilsOveWel Project Manifest | Confidential</div></div>`;
        });
        html += `</body></html>`;
        win.document.write(html);
        win.document.close();
        win.focus();
        win.print();
    }

    document.getElementById("projectSearch").addEventListener("input", function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll("#projectList > div > div").forEach(b => {
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
            html += \'<div class="flex flex-col \' + (isMe ? "items-end" : "items-start") + \' mb-4">\' + (!isMe ? \'<span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">\' + r.sender_name + \'</span>\' : "") + \'<div class="\' + (isMe ? "bg-primary text-on-primary rounded-tr-none" : "bg-white border border-slate-100 rounded-tl-none") + \' rounded-2xl px-4 py-2.5 max-w-[85%] shadow-sm"><p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">\' + (r.content || "") + \'</p>\' + (r.attachment ? \'<a href="../\' + r.attachment + \'" target="_blank" class="block mt-2"><img src="../\' + r.attachment + \'" class="rounded-xl border border-slate-100 max-h-48 object-cover" alt="attachment"></a>\' : "") + \'<p class="text-[9px] font-bold opacity-60 mt-1 uppercase tracking-widest">\' + new Date(r.created_at).toLocaleTimeString([], {hour: "2-digit", minute:"2-digit"}) + \'</p></div></div>\';
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
