<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: ../client-login.php");
    exit();
}
$conn = get_db_connection();

// Check if a specific project is requested
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($project_id > 0) {
    // Fetch specific project details
    $project_res = $conn->query("SELECT * FROM projects WHERE id = $project_id AND client_id = $client_id");
    $project = $project_res->fetch_assoc();

    if (!$project) {
        $project_id = 0; // Reset if project not found or not owned by client
    } else {
        // Fetch reports for this project
        $reports_res = $conn->query("
            SELECT pr.*, a.name as author_name 
            FROM project_reports pr 
            LEFT JOIN admins a ON pr.admin_id = a.id 
            WHERE pr.project_id = $project_id 
            ORDER BY pr.created_at DESC
        ");
    }
}

// Fetch all projects for the list view
$projects_list_res = $conn->query("SELECT * FROM projects WHERE client_id = $client_id ORDER BY created_at DESC");
$total_projects = $projects_list_res->num_rows;
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>WILSOVLEWEL | Client Projects Ledger</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#EAB308",
                        "on-primary": "#000000",
                        "primary-container": "#FEF9C3",
                        "on-primary-container": "#422006",
                        "secondary": "#1A1A1A",
                        "on-secondary": "#FFFFFF",
                        "surface": "#FDFDFD",
                        "on-surface": "#1A1A1A",
                        "surface-variant": "#F5F5F5",
                        "on-surface-variant": "#4A4A4A",
                        "outline": "#79747E",
                        "outline-variant": "#CAC4D0",
                        "error": "#B00020",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container-low": "#F7F7F7",
                        "surface-container": "#F3F3F3",
                        "surface-container-high": "#EFEFEF",
                        "surface-container-highest": "#EBEBEB",
                    },
                    fontFamily: {
                        "headline": ["Space Grotesk"],
                        "body": ["Manrope"],
                        "label": ["Space Grotesk"]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .technical-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.05; }
        .site-gradient-bg { background: radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.05) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(0, 0, 0, 0.05) 0%, transparent 50%); background-attachment: fixed; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

    <!-- Main Content Canvas -->
    <main class="pt-20 pb-8 px-6 relative z-10 max-w-7xl mx-auto">
        <!-- TopNavBar -->
        <script src="../components/client_topnav.js" data-root="../"></script>

        <div class="max-w-7xl mx-auto space-y-10">
            <!-- Header & Action Section -->
            <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                <div class="space-y-1">
                    <span class="font-headline text-primary font-medium tracking-widest text-[9px] uppercase bg-primary/5 px-2 py-1 rounded-full">System Ledger // Project Logs</span>
                    <h1 class="text-3xl lg:text-4xl font-headline font-bold text-on-surface tracking-tight">
                        <?php if ($project_id > 0): ?>
                            <?= htmlspecialchars($project['name']) ?>
                        <?php else: ?>
                            My Projects
                        <?php endif; ?>
                    </h1>
                    <p class="text-on-surface-variant max-w-xl font-body text-xs leading-relaxed">
                        <?php if ($project_id > 0): ?>
                            <?= htmlspecialchars($project['description']) ?>
                        <?php else: ?>
                            Track every maintenance cycle and component repair in real-time.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="flex gap-4">
                    <?php if ($project_id > 0): ?>
                        <a href="projects.php" class="px-6 py-4 bg-surface-container-high text-on-surface rounded-lg font-headline font-medium flex items-center gap-2 hover:bg-surface-container transition-all">
                            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                            Back to Ledger
                        </a>
                    <?php endif; ?>
                    <a href="propose_project.php" class="px-8 py-4 bg-gradient-to-br from-primary to-amber-600 text-on-primary rounded-lg font-headline font-medium shadow-lg shadow-primary/20 hover:translate-y-[-2px] transition-all active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        Propose New Project
                    </a>
                </div>
            </header>

            <?php if ($project_id > 0): 
                // Fetch Milestones
                $milestones = [];
                $res = $conn->query("SELECT * FROM project_milestones WHERE project_id = $project_id ORDER BY order_index ASC, created_at ASC");
                while ($row = $res->fetch_assoc()) {
                    $ms_id = $row['id'];
                    $subs = [];
                    $sub_res = $conn->query("SELECT * FROM project_sub_milestones WHERE milestone_id = $ms_id ORDER BY created_at ASC");
                    while ($s = $sub_res->fetch_assoc()) $subs[] = $s;
                    $row['sub_milestones'] = $subs;
                    $milestones[] = $row;
                }

                $completed_count = 0;
                foreach ($milestones as $m) if ($m['status'] == 'Completed') $completed_count++;
                $progress = count($milestones) > 0 ? round(($completed_count / count($milestones)) * 100) : 0;

                // Assets
                $assigned_assets = [];
                $res = $conn->query("SELECT a.* FROM assets a JOIN project_assets pa ON a.id = pa.asset_id WHERE pa.project_id = $project_id");
                while ($row = $res->fetch_assoc()) $assigned_assets[] = $row;
            ?>
                <!-- Single Project View -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left: Details & Roadmap -->
                    <div class="lg:col-span-8 space-y-8">
                        <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <h3 class="font-headline text-xl font-bold">Project Roadmap</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Strategic milestones & execution timeline</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Overall Progress</p>
                                    <span class="text-xl font-bold text-primary"><?php echo $progress; ?>%</span>
                                </div>
                            </div>
                            
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden mb-12">
                                <div class="h-full bg-primary rounded-full transition-all duration-1000" style="width: <?php echo $progress; ?>%"></div>
                            </div>

                            <div class="space-y-12 relative">
                                <!-- Timeline Line -->
                                <div class="absolute left-[19px] top-4 bottom-4 w-0.5 bg-slate-100"></div>

                                <?php foreach ($milestones as $m): 
                                    $isDone = $m['status'] == 'Completed';
                                    $isActive = $m['status'] == 'In Progress';
                                ?>
                                <div class="relative pl-14">
                                    <!-- Timeline Dot -->
                                    <div class="absolute left-0 top-0 w-10 h-10 rounded-full border-4 <?php echo $isDone ? 'bg-primary border-primary' : ($isActive ? 'bg-white border-primary' : 'bg-white border-slate-100'); ?> flex items-center justify-center z-10">
                                        <?php if ($isDone): ?>
                                            <span class="material-symbols-outlined text-on-primary text-sm font-bold">check</span>
                                        <?php else: ?>
                                            <span class="text-[10px] font-bold <?php echo $isActive ? 'text-primary' : 'text-slate-300'; ?>"><?php echo $m['order_index'] + 1; ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                            <div>
                                                <h4 class="font-bold text-slate-900"><?php echo htmlspecialchars($m['title']); ?></h4>
                                                <?php if ($m['due_date']): ?>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Target: <?php echo date('M d, Y', strtotime($m['due_date'])); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <?php if ($m['approval_status'] == 'Pending'): ?>
                                                    <button onclick="approveMilestone(<?php echo $m['id']; ?>, 'Approved')" class="px-4 py-1.5 bg-primary text-on-primary rounded-lg text-[9px] font-bold uppercase tracking-widest hover:shadow-lg transition-all">Approve</button>
                                                    <button onclick="approveMilestone(<?php echo $m['id']; ?>, 'Rejected')" class="px-4 py-1.5 bg-white border border-slate-200 text-slate-400 rounded-lg text-[9px] font-bold uppercase tracking-widest hover:bg-red-50 hover:text-red-600 transition-all">Reject</button>
                                                <?php else: ?>
                                                    <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest <?php echo $m['approval_status'] == 'Approved' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'; ?>">
                                                        <?php echo $m['approval_status']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <p class="text-xs text-slate-600 leading-relaxed mb-6"><?php echo nl2br(htmlspecialchars($m['description'])); ?></p>

                                        <?php if (!empty($m['sub_milestones'])): ?>
                                        <div class="space-y-2 mb-6">
                                            <?php foreach ($m['sub_milestones'] as $sm): ?>
                                            <div class="flex items-center gap-3">
                                                <span class="material-symbols-outlined text-sm <?php echo $sm['is_completed'] ? 'text-primary' : 'text-slate-200'; ?>">
                                                    <?php echo $sm['is_completed'] ? 'check_circle' : 'radio_button_unchecked'; ?>
                                                </span>
                                                <span class="text-[11px] <?php echo $sm['is_completed'] ? 'text-slate-400 line-through' : 'text-slate-600 font-medium'; ?>"><?php echo htmlspecialchars($sm['title']); ?></span>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>

                                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                            <button onclick="openMilestoneChat(<?php echo $m['id']; ?>, '<?php echo addslashes($m['title']); ?>')" class="text-[10px] font-bold text-slate-400 flex items-center gap-1.5 hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-sm">forum</span> View Communications
                                            </button>
                                            <span class="text-[9px] font-bold uppercase tracking-widest <?php echo $isDone ? 'text-emerald-500' : ($isActive ? 'text-amber-500' : 'text-slate-300'); ?>">
                                                <?php echo $m['status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Project Chat -->
                        <div class="space-y-6">
                            <h3 class="font-headline text-xl font-bold flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">forum</span>
                                Project Communication Log
                            </h3>
                            <div id="projectChat" class="space-y-4 max-h-[400px] overflow-y-auto custom-scrollbar p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                                <?php if (empty($reports)): ?>
                                    <div class="text-center py-10">
                                        <span class="material-symbols-outlined text-4xl text-slate-200">history_edu</span>
                                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No activity recorded yet</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($reports as $r): 
                                        $isClient = ($r['sender_type'] === 'Client'); ?>
                                        <div class="flex flex-col <?php echo $isClient ? 'items-end' : 'items-start'; ?> w-full">
                                            <div class="max-w-[85%]">
                                                <div class="flex items-center gap-2 mb-1 <?php echo $isClient ? 'justify-end' : ''; ?>">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?php echo $isClient ? 'YOU' : htmlspecialchars($r['author_name']); ?></span>
                                                    <span class="text-[9px] text-slate-300"><?php echo date('H:i', strtotime($r['created_at'])); ?></span>
                                                </div>
                                                <div class="<?php echo $isClient ? 'bg-primary text-on-primary rounded-tr-none shadow-lg shadow-primary/10' : 'bg-white text-slate-700 rounded-tl-none border border-slate-100 shadow-sm'; ?> px-5 py-3 rounded-2xl">
                                                    <p class="text-xs leading-relaxed font-medium whitespace-pre-wrap"><?php echo htmlspecialchars($r['content']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <form id="projectReplyForm" class="relative group">
                                <input type="hidden" id="replyProjectId" value="<?= $project_id ?>">
                                <textarea id="replyContent" required placeholder="Send a message..." class="w-full bg-white border-slate-100 rounded-2xl px-6 py-5 text-sm focus:ring-2 focus:ring-primary/20 min-h-[100px] custom-scrollbar shadow-sm resize-none pr-16"></textarea>
                                <button type="submit" id="replyBtn" class="absolute right-4 bottom-4 w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shadow-lg active:scale-95">
                                    <span class="material-symbols-outlined">send</span>
                                </button>
                            </form>
                        </div>
                    </div>

                        </div>

                        <!-- Logistics Update -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-4 -bottom-4 opacity-5 pointer-events-none">
                                <span class="material-symbols-outlined text-8xl">local_shipping</span>
                            </div>
                            <h4 class="text-xs font-bold font-headline uppercase tracking-[0.2em] mb-4 text-slate-400">Logistics Status</h4>
                            <div class="space-y-4">
                                <?php
                                $po_res = $conn->query("SELECT * FROM procurement_orders WHERE project_id = $project_id ORDER BY updated_at DESC LIMIT 3");
                                if ($po_res->num_rows > 0):
                                    while($po = $po_res->fetch_assoc()):
                                ?>
                                <div class="p-4 bg-slate-50 rounded-xl border-l-4 border-primary shadow-sm">
                                    <span class="block text-xs font-bold text-slate-900"><?= htmlspecialchars($po['item_name']) ?></span>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-primary/10 text-primary text-[9px] font-bold uppercase tracking-widest rounded-md"><?= htmlspecialchars($po['status']) ?></span>
                                    <p class="text-[9px] text-slate-400 font-medium mt-2 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[10px]">location_on</span>
                                        <?= htmlspecialchars($po['current_location'] ?: 'Warehouse Origin') ?>
                                    </p>
                                </div>
                                <?php endwhile; else: ?>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">No active shipments</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Project List View -->
                <section class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-headline font-bold text-on-surface flex items-center gap-2">
                            <span class="w-2 h-2 bg-primary rounded-full"></span>
                            Active Project Ledger
                        </h2>
                    </div>

                    <div class="bg-surface-container-lowest rounded-lg overflow-hidden border border-outline-variant/10 shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-surface-container-low/50">
                                <tr>
                                    <th class="px-6 py-4 font-headline text-[10px] uppercase tracking-widest text-outline font-semibold">Project Title</th>
                                    <th class="px-6 py-4 font-headline text-[10px] uppercase tracking-widest text-outline font-semibold">Maintenance Stage</th>
                                    <th class="px-6 py-4 font-headline text-[10px] uppercase tracking-widest text-outline font-semibold text-center">Budget</th>
                                    <th class="px-6 py-4 font-headline text-[10px] uppercase tracking-widest text-outline font-semibold">Operational ETA</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container">
                                <?php if ($projects_list_res->num_rows > 0): 
                                    while($p = $projects_list_res->fetch_assoc()):
                                        $status_color = 'blue';
                                        if ($p['status'] == 'Diagnostic') $status_color = 'orange';
                                        if ($p['status'] == 'Testing') $status_color = 'purple';
                                        if ($p['status'] == 'Completed') $status_color = 'emerald';
                                ?>
                                <tr class="hover:bg-surface/50 transition-colors group cursor-pointer" onclick="location.href='projects.php?id=<?= $p['id'] ?>'">
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded bg-surface-container-low flex items-center justify-center border border-outline-variant/20">
                                                <span class="material-symbols-outlined text-primary">engineering</span>
                                            </div>
                                            <div>
                                                <div class="font-bold text-on-surface text-sm"><?= htmlspecialchars($p['name']) ?></div>
                                                <div class="text-[10px] font-headline text-outline uppercase">ID: #PROJ-<?= $p['id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-surface-container-high rounded-full h-1.5 max-w-[100px] overflow-hidden">
                                                <div class="bg-<?= $status_color ?>-600 h-full" style="width: 65%;"></div>
                                            </div>
                                            <span class="px-2 py-1 bg-<?= $status_color ?>-50 text-<?= $status_color ?>-700 text-[10px] font-bold uppercase tracking-wider rounded"><?= htmlspecialchars($p['status']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="font-headline font-bold text-on-surface">$<?= number_format($p['budget'], 0) ?></span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-2 text-sm text-on-surface-variant font-medium">
                                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                                            <?= htmlspecialchars($p['end_date'] ?: 'TBD') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-right">
                                        <button class="p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="material-symbols-outlined text-outline">chevron_right</span>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">No projects found in your ledger.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>
    <!-- Milestone Chat Modal -->
    <div id="msChatModal" class="modal-overlay fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[85vh] animate-in fade-in zoom-in duration-300">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                <div>
                    <h3 class="font-bold font-headline text-slate-900" id="msChatTitle">Milestone Logs</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Communication Timeline</p>
                </div>
                <button onclick="closeMsChat()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center transition-colors"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            <div id="msChatContent" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-slate-50/30">
                <!-- Loaded via AJAX -->
            </div>
            <div class="p-6 border-t border-slate-100 bg-white shrink-0">
                <form id="msChatForm" class="relative group">
                    <input type="hidden" id="msChatId">
                    <textarea id="msChatInput" required placeholder="Send a message regarding this milestone..." class="w-full bg-slate-50 border-slate-100 rounded-2xl px-5 py-3.5 text-xs focus:ring-2 focus:ring-primary/20 min-h-[60px] max-h-[120px] custom-scrollbar resize-none pr-12 transition-all"></textarea>
                    <button type="submit" class="absolute right-3 bottom-3 w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shadow-lg active:scale-95">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const projectId = <?php echo $project_id; ?>;

        async function approveMilestone(id, status) {
            if (!confirm(`Are you sure you want to mark this milestone as ${status}?`)) return;
            const fd = new FormData();
            fd.append('id', id);
            fd.append('status', status);
            
            // Reusing add_project_message logic but for approval
            const res = await fetch(`add_project_message.php?action=approve_milestone`, {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            if (data.status === 'success') location.reload();
            else alert(data.message);
        }

        async function openMilestoneChat(msId, title) {
            document.getElementById('msChatId').value = msId;
            document.getElementById('msChatTitle').innerText = title;
            document.getElementById('msChatContent').innerHTML = '<div class="text-center py-10 animate-pulse text-slate-300 uppercase text-[10px] font-bold tracking-widest">Loading logs...</div>';
            document.getElementById('msChatModal').classList.remove('hidden');
            
            const res = await fetch(`add_project_message.php?action=get_milestone_reports&milestone_id=${msId}`);
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
                <div class="flex flex-col ${r.sender_type === 'Client' ? 'items-end' : 'items-start'}">
                    <div class="flex items-center gap-2 mb-1 px-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">${r.sender_type === 'Client' ? 'YOU' : r.sender_name}</span>
                        <span class="text-[8px] text-slate-300 font-medium">${new Date(r.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                    <div class="${r.sender_type === 'Client' ? 'bg-primary text-on-primary rounded-tr-none' : 'bg-white text-slate-600 rounded-tl-none border border-slate-100'} px-4 py-2.5 rounded-2xl shadow-sm text-xs leading-relaxed font-medium">
                        ${r.content.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `).join('');
            cont.scrollTop = cont.scrollHeight;
        }

        function closeMsChat() { document.getElementById('msChatModal').classList.add('hidden'); }

        document.getElementById('msChatForm').onsubmit = async (e) => {
            e.preventDefault();
            const input = document.getElementById('msChatInput');
            const msId = document.getElementById('msChatId').value;
            const fd = new FormData();
            fd.append('milestone_id', msId);
            fd.append('project_id', projectId);
            fd.append('content', input.value);
            
            const res = await fetch('add_project_message.php?action=add_milestone_report', { method: 'POST', body: fd });
            input.value = '';
            openMilestoneChat(msId, document.getElementById('msChatTitle').innerText);
        };

        const replyForm = document.getElementById('projectReplyForm');
        if (replyForm) {
            replyForm.onsubmit = async (e) => {
                e.preventDefault();
                const btn = document.getElementById('replyBtn');
                const input = document.getElementById('replyContent');
                const pId = document.getElementById('replyProjectId').value;
                const content = input.value;

                btn.disabled = true;
                btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">sync</span>';

                const fd = new FormData();
                fd.append('project_id', pId);
                fd.append('content', content);

                try {
                    const res = await fetch('add_project_message.php', { method: 'POST', body: fd });
                    const data = await res.json();
                    if (data.status === 'success') {
                        input.value = '';
                        const chat = document.getElementById('projectChat');
                        if (chat.querySelector('.text-center')) chat.innerHTML = '';
                        chat.insertAdjacentHTML('beforeend', data.html);
                        chat.scrollTop = chat.scrollHeight;
                    } else { alert(data.message); }
                } catch(err) { alert('Network error'); }
                finally {
                    btn.disabled = false;
                    btn.innerHTML = '<span class="material-symbols-outlined">send</span>';
                }
            };
        }
    </script>
</body>
</html>
