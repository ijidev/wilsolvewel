<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

// Fetch Client Data
$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: login.php");
    exit();
}

$conn = get_db_connection();

// Fetch Client Info
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$client_res = $stmt->get_result();
$stmt->close();
if (!$client_res || $client_res->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$client = $client_res->fetch_assoc();

// Stats calculation
$active_projects_count = 0;
$p_stats = safe_query($conn, "SELECT COUNT(*) as count FROM projects WHERE client_id = ? AND status != 'Completed'", "i", [$client_id]);
if ($p_stats) $active_projects_count = $p_stats->fetch_assoc()['count'];

$open_tickets_count = 0;
$t_stats = safe_query($conn, "SELECT COUNT(*) as count FROM tickets WHERE client_id = ? AND status != 'Resolved'", "i", [$client_id]);
if ($t_stats) $open_tickets_count = $t_stats->fetch_assoc()['count'];

// Safe Days: days since last High severity incident
$last_incident = safe_query($conn, "SELECT created_at FROM hsse_observations WHERE severity = 'High' ORDER BY created_at DESC LIMIT 1");
$safe_days = 0;
if ($last_incident && $last_incident->num_rows > 0) {
    $last_date = new DateTime($last_incident->fetch_assoc()['created_at']);
    $safe_days = (new DateTime())->diff($last_date)->days;
} else {
    $safe_days = (int)get_setting('hsse_base_safe_days', 412);
}

// Compliance Index: resolved / total observations
$total_obs_all = safe_query($conn, "SELECT COUNT(*) as c FROM hsse_observations")->fetch_assoc()['c'];
$resolved_obs_all = safe_query($conn, "SELECT COUNT(*) as c FROM hsse_observations WHERE status = 'Resolved'")->fetch_assoc()['c'];
$compliance_index = $total_obs_all > 0 ? round(($resolved_obs_all / $total_obs_all) * 100, 1) : 100.0;

$reports_res = safe_query($conn, "SELECT pr.*, p.name as project_name FROM project_reports pr JOIN projects p ON pr.project_id = p.id WHERE p.client_id = ? ORDER BY pr.created_at DESC LIMIT 3", "i", [$client_id]);

$page_title = 'Dashboard | Wilsolvewel Client';
$page_h1 = 'Welcome, ' . htmlspecialchars($client['name']);
$page_h1_sub = htmlspecialchars($client['company']) . ' • SECTOR ACCESS GRANTED';
$page_h1_badge = 'Terminal Interface v2.0';
$page_h1_action = '<button onclick="location.href=\'propose_project.php\'" class="bg-primary text-on-primary px-4 sm:px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2"><span class="material-symbols-outlined text-lg">add_circle</span> <span class="hidden sm:inline">PROPOSE PROJECT</span><span class="inline sm:hidden">NEW</span></button>';
$page_class = 'bg-[#F8FAFC]';

ob_start();
?>
<div class="flex flex-col min-h-screen">
    <div class="w-full relative z-10">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 mb-8">
            <div onclick="location.href='projects.php'" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between h-32 group hover:border-primary/30 transition-all cursor-pointer">
                <div class="flex justify-between items-start">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ACTIVE PROJECTS</span>
                    <span class="material-symbols-outlined text-primary group-hover:rotate-12 transition-transform text-xl">folder_open</span>
                </div>
                <span class="text-3xl font-headline font-black text-slate-900 leading-none"><?= $active_projects_count ?></span>
            </div>
            <div onclick="location.href='tickets.php'" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between h-32 group hover:border-primary/30 transition-all cursor-pointer">
                <div class="flex justify-between items-start">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">SUPPORT TICKETS</span>
                    <span class="material-symbols-outlined text-primary group-hover:rotate-12 transition-transform text-xl">forum</span>
                </div>
                <span class="text-3xl font-headline font-black text-slate-900 leading-none"><?= $open_tickets_count ?></span>
            </div>
            <div onclick="location.href='hsse.php'" class="bg-slate-900 text-white p-6 rounded-[2rem] flex flex-col justify-between h-32 shadow-2xl relative overflow-hidden group cursor-pointer border border-transparent hover:border-primary/30 transition-all">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-primary/10 rounded-full blur-2xl"></div>
                <div class="flex justify-between items-start relative z-10">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">SAFE DAYS</span>
                    <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform text-xl">verified_user</span>
                </div>
                <span class="text-3xl font-headline font-black text-white relative z-10 leading-none"><?= $safe_days ?></span>
            </div>
            <div onclick="location.href='hsse.php'" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between h-32 group hover:border-primary/30 transition-all cursor-pointer">
                <div class="flex justify-between items-start">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">COMPLIANCE</span>
                    <span class="material-symbols-outlined text-primary group-hover:rotate-12 transition-transform text-xl">health_and_safety</span>
                </div>
                <span class="text-3xl font-headline font-black text-slate-900 leading-none"><?= $compliance_index ?>%</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-8 space-y-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-2xl font-headline font-black text-slate-900">Project Nodes</h3>
                    <button onclick="location.href='projects.php'" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">View All</button>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <?php
                    $projects_res = safe_query($conn, "SELECT * FROM projects WHERE client_id = ? AND status != 'Completed' LIMIT 4", "i", [$client_id]);
                    if ($projects_res->num_rows > 0):
                        while($p = $projects_res->fetch_assoc()):
                    ?>
                    <div onclick="location.href='projects.php?id=<?= $p['id'] ?>'" class="bg-white p-5 rounded-[2rem] border border-slate-100 hover:border-primary/20 hover:bg-slate-50/50 transition-all cursor-pointer group flex items-center gap-6">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100 group-hover:border-primary/20 transition-all shrink-0">
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors">analytics</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-headline font-bold text-lg text-slate-900 truncate"><?= htmlspecialchars($p['name']) ?></h4>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[9px] font-black uppercase bg-primary/10 text-primary px-2.5 py-1 rounded-full"><?= htmlspecialchars($p['status']) ?></span>
                                <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ID: #<?= $p['id'] ?></span>
                            </div>
                        </div>
                        <div class="w-32 hidden md:block shrink-0">
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: 45%"></div>
                            </div>
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mt-1 text-center">45% COMPLETION</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-200 group-hover:text-primary transition-colors">arrow_forward</span>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="bg-white p-6 sm:p-12 rounded-[2.5rem] border border-dashed border-slate-200 text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-200">folder_off</span>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">No active project nodes detected</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-4 space-y-8">
                <div>
                    <h3 class="text-2xl font-headline font-black text-slate-900 mb-4">Recent Ledger</h3>
                    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 shadow-sm space-y-6">
                        <?php if ($reports_res && $reports_res->num_rows > 0): 
                            while($rep = $reports_res->fetch_assoc()):
                        ?>
                        <div class="relative pl-6 border-l-2 border-slate-100 last:border-0 pb-6 last:pb-0">
                            <div class="absolute -left-[5px] top-0 w-2 h-2 rounded-full bg-primary shadow-[0_0_8px_rgba(234,179,8,0.5)]"></div>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1"><?= date('M d, H:i', strtotime($rep['created_at'])) ?> • <?= htmlspecialchars($rep['project_name']) ?></p>
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed"><?= htmlspecialchars($rep['content']) ?></p>
                        </div>
                        <?php endwhile; else: ?>
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-center py-4 italic">No recent logs recorded</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bg-slate-900 p-4 sm:p-6 lg:p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden text-white group">
                    <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all"></div>
                    <div class="relative z-10">
                        <span class="material-symbols-outlined text-primary text-3xl mb-4">support_agent</span>
                        <h4 class="font-headline font-black text-xl mb-2">Technical Core</h4>
                        <p class="text-xs text-slate-400 leading-relaxed mb-6">Immediate access to your dedicated project managers and terminal logistics support.</p>
                        <button onclick="location.href='tickets.php'" class="w-full py-4 bg-white/10 hover:bg-white/20 border border-white/10 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">OPEN CHANNEL</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="fixed bottom-4 right-4 sm:bottom-8 sm:right-8 z-50">
    <div class="bg-white/80 backdrop-blur-xl border border-slate-100 p-3 sm:p-4 rounded-2xl shadow-2xl flex items-center gap-3 sm:gap-5">
        <div class="flex flex-col">
            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">TERMINAL</span>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-black text-slate-900 tracking-tighter">CONNECTED</span>
            </div>
        </div>
        <div class="w-px h-8 bg-slate-100"></div>
        <div class="flex flex-col">
            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">SYNC</span>
            <span class="text-xs font-black text-slate-900 tracking-tighter uppercase"><?= date('H:i') ?> UTC</span>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../components/client_layout.php';
