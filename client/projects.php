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
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="material-symbols-outlined" style="font-size:14px">schedule</span>
                        <?php echo date('M d, Y', strtotime($proj['start_date'])); ?> 
                        <?php if($proj['end_date']) echo ' - ' . date('M d, Y', strtotime($proj['end_date'])); ?>
                    </div>
                    <?php if ($proj['dept_name']): ?>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="material-symbols-outlined" style="font-size:14px">corporate_fare</span>
                            <?php echo htmlspecialchars($proj['dept_name']); ?>
                        </div>
                    <?php endif; ?>
                </div>

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
}

// Fetch all projects for the list view
$projects_list_res = safe_query($conn, "SELECT * FROM projects WHERE client_id = ? ORDER BY created_at DESC", "i", [$client_id]);
$projects = [];
while ($row = $projects_list_res->fetch_assoc()) $projects[] = $row;
$total_projects = count($projects);

$page_title = 'WILSOVLEWEL | Client Projects';
$page_h1 = 'System Ledger';
$page_h1_sub = 'Project Tracking';
$page_h1_action = '<a href="propose_project.php" class="bg-primary text-on-primary px-3 sm:px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95 shrink-0 ml-2"><span class="material-symbols-outlined text-sm">add_circle</span> <span class="hidden sm:inline">NEW PROPOSAL</span></a>';
$page_styles = '
    .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(6px);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem}
    .modal-overlay.open{display:flex}
';

ob_start();
?>
<div class="flex-1 flex overflow-hidden h-[calc(100vh-8rem)]">
    <div class="w-full lg:w-[400px] bg-white border-r border-slate-100 flex flex-col shrink-0">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" id="projectSearch" placeholder="Search projects..." class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1" id="projectList">
            <?php if ($total_projects == 0): ?>
                <div class="text-center py-10">
                    <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">folder_open</span>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No Projects Found</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $p): ?>
                    <button onclick="loadProject(<?php echo $p['id']; ?>)" class="w-full text-left p-4 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group relative">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-sm text-slate-900 group-hover:text-primary transition-colors pr-8 leading-tight">
                                <?php echo htmlspecialchars($p['name']); ?>
                            </h3>
                            <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded-md text-[9px] font-bold uppercase tracking-widest shrink-0">
                                <?php echo htmlspecialchars($p['status']); ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined" style="font-size:12px">calendar_today</span> <?php echo date('M d, Y', strtotime($p['start_date'])); ?></span>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex-1 bg-slate-50 hidden lg:flex flex-col relative" id="detailCanvas">
        <div class="absolute inset-0 flex items-center justify-center text-center p-4 sm:p-8 z-0">
            <div class="px-4">
                <span class="material-symbols-outlined text-4xl sm:text-6xl text-slate-200 mb-4 block">space_dashboard</span>
                <h2 class="text-lg font-bold font-headline text-slate-900 mb-1">Select a Project</h2>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Choose a project from the ledger to view its roadmap, milestones, and documentation.</p>
            </div>
        </div>
        <div id="detailContent" class="absolute inset-0 flex flex-col z-10 bg-slate-50 hidden overflow-hidden">
            <div id="projectHeader"></div>
            <div id="projectBody" class="flex-1 overflow-y-auto custom-scrollbar"></div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
$page_main_class = 'flex-1 flex flex-col overflow-hidden';
$page_class = 'flex flex-col min-h-screen';

$page_after_main = '
    <div id="mobileDetailOverlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-[100] lg:hidden opacity-0 pointer-events-none transition-opacity duration-300"></div>
    <div id="mobileDetailDrawer" class="fixed inset-y-0 right-0 w-full max-w-md bg-slate-50 z-[110] lg:hidden transform translate-x-full transition-transform duration-300 shadow-2xl flex flex-col">
        <div class="h-16 border-b border-slate-100 bg-white px-4 flex items-center justify-between shrink-0">
            <h3 class="font-bold font-headline text-slate-900">Project Details</h3>
            <button onclick="closeMobileDetails()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100"><span class="material-symbols-outlined text-sm">close</span></button>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col relative" id="mobileDetailContent"></div>
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

    async function loadProject(id) {
        const detailCanvas = document.getElementById("detailContent");
        detailCanvas.classList.remove("hidden");
        document.getElementById("projectHeader").innerHTML = \'<div class="p-6 text-center text-slate-400"><span class="material-symbols-outlined animate-spin">refresh</span></div>\';
        document.getElementById("projectBody").innerHTML = "";
        if (window.innerWidth < 1024) {
            document.getElementById("mobileDetailOverlay").classList.remove("opacity-0", "pointer-events-none");
            document.getElementById("mobileDetailDrawer").classList.remove("translate-x-full");
            document.getElementById("mobileDetailContent").innerHTML = \'<div class="p-6 text-center text-slate-400"><span class="material-symbols-outlined animate-spin">refresh</span></div>\';
        }
        const res = await fetch("?ajax_action=load_details&id=" + id);
        const data = await res.json();
        document.getElementById("projectHeader").innerHTML = data.header_html;
        document.getElementById("projectBody").innerHTML = data.body_html;
        if (window.innerWidth < 1024) {
            document.getElementById("mobileDetailContent").innerHTML = data.header_html + data.body_html;
        }
        document.querySelectorAll("#projectList button").forEach(b => b.classList.remove("bg-white", "shadow-sm", "border-slate-200"));
        const btn = document.querySelector("button[onclick=\\"loadProject(" + id + ")\\"]");
        if (btn) btn.classList.add("bg-white", "shadow-sm", "border-slate-200");
    }

    function closeMobileDetails() {
        document.getElementById("mobileDetailOverlay").classList.add("opacity-0", "pointer-events-none");
        document.getElementById("mobileDetailDrawer").classList.add("translate-x-full");
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
</script>
';

require_once __DIR__ . '/../components/client_layout.php';
