<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Fetch Client Data
$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: ../client-login.php");
    exit();
}

$conn = get_db_connection();

// Fetch Client Info
$client_res = $conn->query("SELECT * FROM clients WHERE id = $client_id");
if (!$client_res || $client_res->num_rows === 0) {
    session_destroy();
    header("Location: ../client-login.php");
    exit;
}
$client = $client_res->fetch_assoc();

// Stats calculation
$active_projects_count = 0;
$p_stats = $conn->query("SELECT COUNT(*) as count FROM projects WHERE client_id = $client_id AND status != 'Completed'");
if ($p_stats) $active_projects_count = $p_stats->fetch_assoc()['count'];

$open_tickets_count = 0;
$t_stats = $conn->query("SELECT COUNT(*) as count FROM tickets WHERE client_id = $client_id AND status != 'Resolved'");
if ($t_stats) $open_tickets_count = $t_stats->fetch_assoc()['count'];

// HSSE Stats
$safe_days = get_setting('hsse_safe_days', 412);
$compliance_index = get_setting('hsse_compliance_index', 98.4);

// Recent Project reports
$reports_res = $conn->query("
    SELECT pr.*, p.name as project_name 
    FROM project_reports pr 
    JOIN projects p ON pr.project_id = p.id 
    WHERE p.client_id = $client_id 
    ORDER BY pr.created_at DESC 
    LIMIT 3
");
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard | Wilsolvewel Client</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#EAB308",
                        "on-primary": "#000000",
                        surface: "#F8FAFC",
                        "on-surface": "#0F172A"
                    },
                    fontFamily: {
                        headline: ["Outfit", "Space Grotesk"],
                        body: ["Manrope"]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20; }
        .blueprint-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.03; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface h-screen overflow-hidden flex">
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <!-- TopNavBar -->
        <script src="../components/client_topnav.js" data-root="../"></script>

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 relative custom-scrollbar h-full">
            <div class="fixed inset-0 blueprint-grid pointer-events-none z-0"></div>
            
            <div class="w-full relative z-10">
                <!-- Welcome Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-6">
                    <div>
                        <span class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-2 block">Terminal Interface v2.0</span>
                        <h1 class="text-4xl font-headline font-extrabold text-slate-900 tracking-tighter leading-none">Welcome, <?= htmlspecialchars($client['name']) ?></h1>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest mt-2"><?= htmlspecialchars($client['company']) ?> • SECTOR ACCESS GRANTED</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="location.href='propose_project.php'" class="bg-primary text-on-primary px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">add_circle</span> PROPOSE PROJECT
                        </button>
                    </div>
                </div>

                <!-- Stats Bento -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
                    <!-- Projects List -->
                    <div class="lg:col-span-8 space-y-6">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-2xl font-headline font-black text-slate-900">Project Nodes</h3>
                            <button onclick="location.href='projects.php'" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">View All</button>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <?php
                            $projects_res = $conn->query("SELECT * FROM projects WHERE client_id = $client_id AND status != 'Completed' LIMIT 4");
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
                            <div class="bg-white p-12 rounded-[2.5rem] border border-dashed border-slate-200 text-center">
                                <span class="material-symbols-outlined text-4xl text-slate-200">folder_off</span>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">No active project nodes detected</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Side Activity -->
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

                        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden text-white group">
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
        </main>
    </div>

    <!-- Live Status Chip -->
    <div class="fixed bottom-8 right-8 z-50">
        <div class="bg-white/80 backdrop-blur-xl border border-slate-100 p-4 rounded-3xl shadow-2xl flex items-center gap-5">
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
</body>
</html>
