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
        $report_date = $conn->real_escape_string($_POST['report_date'] ?? date('Y-m-d'));
        $content = $conn->real_escape_string(trim($_POST['content'] ?? ''));

        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Report content cannot be empty.']); exit;
        }

        $sql = "INSERT INTO project_reports (project_id, admin_id, report_date, content) VALUES ($project_id, $admin_id, '$report_date', '$content')";
        $conn->query($sql);
        if ($conn->error) { echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }
        
        log_audit($conn, 'Create', 'Project', 'Admin', $admin_id, "Logged a report for project ID: $project_id");
        
        // Fetch it back to render
        $new_id = $conn->insert_id;
        $res = $conn->query("SELECT pr.*, COALESCE(a.name, 'Unknown Admin') as admin_name FROM project_reports pr LEFT JOIN admins a ON pr.admin_id = a.id WHERE pr.id = $new_id");
        $report = $res->fetch_assoc();
        
        $html = '<div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative">';
        $html .= '<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-sm">event</span> ' . date('M j, Y', strtotime($report['report_date'])) . ' &bull; Logged by ' . htmlspecialchars($report['admin_name']) . '</p>';
        $html .= '<p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">' . htmlspecialchars($report['content']) . '</p>';
        $html .= '</div>';

        echo json_encode(['status' => 'success', 'html' => $html]);
        exit;
    }

    if ($_GET['ajax_action'] == 'load_details') {
        $id = (int)$_GET['id'];
        
        // Project info
        $res = $conn->query("SELECT p.*, COALESCE(c.name, 'Deleted Client') as client_name, c.email as client_email FROM projects p LEFT JOIN clients c ON p.client_id = c.id WHERE p.id = $id");
        $proj = $res->fetch_assoc();
        
        // Reports
        $reports = [];
        $res = $conn->query("SELECT pr.*, COALESCE(a.name, 'Unknown Admin') as admin_name FROM project_reports pr LEFT JOIN admins a ON pr.admin_id = a.id WHERE pr.project_id = $id ORDER BY pr.created_at DESC");
        while ($row = $res->fetch_assoc()) $reports[] = $row;
        
        ob_start();
        ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold font-headline text-slate-900"><?php echo htmlspecialchars($proj['name']); ?></h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Client: <span class="text-primary"><?php echo htmlspecialchars($proj['client_name']); ?></span></p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest <?php echo $proj['status']=='Completed'?'bg-emerald-50 text-emerald-600':($proj['status']=='Planning'?'bg-amber-50 text-amber-600':($proj['status']=='On Hold'?'bg-red-50 text-red-500':'bg-blue-50 text-blue-600')); ?>"><?php echo $proj['status']; ?></span>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Start Date</p>
                <p class="text-sm font-bold text-slate-900"><?php echo date('M j, Y', strtotime($proj['start_date'])); ?></p>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">End Date (Est)</p>
                <p class="text-sm font-bold text-slate-900"><?php echo $proj['end_date'] ? date('M j, Y', strtotime($proj['end_date'])) : '—'; ?></p>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Budget</p>
                <p class="text-sm font-bold text-slate-900">$<?php echo number_format($proj['budget'], 2); ?></p>
            </div>
        </div>

        <?php if (!empty($proj['description'])): ?>
        <div class="mb-8">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Project Brief</p>
            <p class="text-sm text-slate-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($proj['description'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- Report Log Interface -->
        <div class="border-t border-slate-100 pt-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold font-headline text-slate-900">Project Log & Reports</h3>
            </div>
            
            <form id="addReportForm" class="bg-slate-50 rounded-[2rem] p-6 mb-8 border border-slate-100" onsubmit="addReport(event, <?php echo $id; ?>)">
                <div class="flex gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm shrink-0">
                        <?php echo substr($_SESSION['admin_name']??'A',0,1); ?>
                    </div>
                    <div class="flex-1 space-y-3">
                        <textarea id="reportContent" required placeholder="Write a project update, daily log, or momentory report..." class="w-full bg-white border-slate-200 rounded-2xl px-4 py-3 text-xs focus:ring-1 focus:ring-primary min-h-[100px] custom-scrollbar"></textarea>
                        <div class="flex justify-between items-center">
                            <input type="date" id="reportDate" required value="<?php echo date('Y-m-d'); ?>" class="bg-white border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-500 font-bold">
                            <button type="submit" id="reportSaveBtn" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors">POST REPORT</button>
                        </div>
                    </div>
                </div>
            </form>

            <div id="reportsList" class="space-y-4">
                <?php if (empty($reports)): ?>
                    <div class="text-center py-10 bg-slate-50/50 rounded-[2rem] border border-dashed border-slate-200"><span class="material-symbols-outlined text-4xl text-slate-300">history_edu</span><p class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest">No reports logged yet</p></div>
                <?php else: ?>
                    <?php foreach ($reports as $r): ?>
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative group">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-sm">event</span> <?php echo date('M j, Y', strtotime($r['report_date'])); ?> &bull; Logged by <?php echo htmlspecialchars($r['admin_name']); ?></p>
                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($r['content']); ?></p>
                            
                            <?php if (!empty($r['client_comment'])): ?>
                                <div class="mt-4 bg-primary/5 border border-primary/20 rounded-xl p-4">
                                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1 flex items-center gap-1"><span class="material-symbols-outlined text-[10px]">chat</span> Client Comment</p>
                                    <p class="text-xs text-slate-800 font-medium"><?php echo htmlspecialchars($r['client_comment']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        echo json_encode(['html' => ob_get_clean()]);
        exit;
    }
}

// Fetch Projects
$projects = [];
$res = $conn->query("SELECT p.*, c.name as client_name FROM projects p JOIN clients c ON p.client_id = c.id ORDER BY p.created_at DESC");
while ($row = $res->fetch_assoc()) $projects[] = $row;

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
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate"><?php echo htmlspecialchars($p['client_name']); ?></p>
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
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" id="projectStatus" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Planning">Planning</option>
                            <option value="Active">Active</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Completed">Completed</option>
                        </select>
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

async function deleteProject(id) {
    if (!confirm('Delete this project? All associated reports will be lost.')) return;
    const res = await fetch(`?ajax_action=delete_project&id=${id}`);
    const result = await res.json();
    if (result.status === 'success') location.reload();
    else showToast(result.message, 'error');
}

async function loadProject(id, cardEl) {
    document.querySelectorAll('.group').forEach(el => el.classList.remove('ring-2', 'ring-primary', 'border-transparent'));
    if (cardEl) cardEl.classList.add('ring-2', 'ring-primary', 'border-transparent');
    
    document.getElementById('emptyPane').classList.add('hidden');
    const pane = document.getElementById('detailPane');
    pane.classList.remove('hidden');
    pane.innerHTML = '<div class="text-center py-20"><span class="material-symbols-outlined text-primary text-4xl animate-spin">sync</span></div>';
    
    const res = await fetch(`?ajax_action=load_details&id=${id}`);
    const data = await res.json();
    pane.innerHTML = data.html;
}

async function addReport(e, projectId) {
    e.preventDefault();
    const btn = document.getElementById('reportSaveBtn');
    const content = document.getElementById('reportContent').value;
    const reportDate = document.getElementById('reportDate').value;
    
    btn.disabled = true; btn.textContent = 'POSTING...';
    
    const fd = new FormData();
    fd.append('project_id', projectId);
    fd.append('content', content);
    fd.append('report_date', reportDate);

    try {
        const res = await fetch('?ajax_action=add_report', { method: 'POST', body: fd });
        const text = await res.text();
        try {
            const result = JSON.parse(text);
            if (result.status === 'success') {
                document.getElementById('reportContent').value = '';
                const list = document.getElementById('reportsList');
                if (list.querySelector('.text-slate-300')) list.innerHTML = ''; // remove empty state
                list.insertAdjacentHTML('afterbegin', result.html);
                showToast('Report logged successfully');
            } else {
                showToast(result.message, 'error');
            }
        } catch(e) {
            showToast('Server Error: ' + text.substring(0, 100), 'error');
        }
    } catch(err) {
        showToast('Request failed: ' + err.message, 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'POST REPORT';
    }
}
</script>
</body>
</html>
