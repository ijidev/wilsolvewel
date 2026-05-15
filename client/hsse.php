<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: ../client-login.php");
    exit();
}
$conn = get_db_connection();

// --- Fetch Statistics ---
$safe_days = get_setting('hsse_safe_days', 412);
$compliance_index = get_setting('hsse_compliance_index', 98.4);

$observations_res = $conn->query("
    SELECT o.*, COALESCE(p.name, 'General Site') as project_name 
    FROM hsse_observations o 
    LEFT JOIN projects p ON o.project_id = p.id
    WHERE (p.client_id = $client_id OR o.project_id IS NULL)
    ORDER BY o.created_at DESC 
    LIMIT 10
");

?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>HSSE Operations | Wilsolvewel</title>
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
        .blueprint-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.05; }
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
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-8">
                        <div class="mb-8">
                            <span class="font-headline text-[10px] uppercase tracking-widest text-primary font-black mb-2 block">System Status: Secure</span>
                            <h1 class="font-headline text-4xl font-extrabold tracking-tighter text-slate-900 mb-4 leading-none">HSSE Operations</h1>
                            <p class="font-body text-slate-500 max-w-xl text-sm">Real-time health, safety, security, and environmental monitoring for your active assets and terminal projects.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between h-44">
                                <div>
                                    <span class="font-headline text-[10px] uppercase tracking-widest text-slate-400 font-black">Incidence-Free Period</span>
                                    <h3 class="font-headline text-5xl font-black text-primary mt-2 leading-none"><?= $safe_days ?></h3>
                                    <p class="font-body text-[10px] font-black text-slate-400 mt-2 tracking-widest uppercase">SAFE DAYS RECORDED</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1 h-1.5 bg-slate-50 rounded-full overflow-hidden flex gap-1">
                                        <div class="h-full bg-primary rounded-full" style="width: 60%"></div>
                                    </div>
                                    <span class="font-headline text-[9px] font-black text-primary tracking-widest uppercase">60% OF GOAL</span>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between h-44">
                                <div>
                                    <span class="font-headline text-[10px] uppercase tracking-widest text-slate-400 font-black">Compliance Index</span>
                                    <div class="flex items-baseline gap-2 mt-2">
                                        <h3 class="font-headline text-5xl font-black text-slate-900 leading-none"><?= $compliance_index ?></h3>
                                        <span class="text-lg font-black text-slate-400">%</span>
                                    </div>
                                    <p class="font-body text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Tier 1 Safety Certification</p>
                                </div>
                                <div class="flex gap-2">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[8px] font-black uppercase tracking-widest border border-emerald-100">AUDIT PASS</span>
                                    <span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-[8px] font-black uppercase tracking-widest border border-slate-100">ISO 45001</span>
                                </div>
                            </div>

                            <div class="md:col-span-2 bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
                                    <h3 class="font-headline text-xl font-black text-slate-900">Hazard & Incident Stream</h3>
                                </div>
                                <div class="space-y-3">
                                    <?php if ($observations_res && $observations_res->num_rows > 0): 
                                        while($obs = $observations_res->fetch_assoc()):
                                            $severity_class = ($obs['severity'] == 'High') ? 'bg-red-50 border-red-100' : 'bg-slate-50 border-slate-100';
                                            $icon_color = ($obs['severity'] == 'High') ? 'text-red-500' : 'text-slate-400';
                                    ?>
                                    <div class="flex items-start gap-4 p-4 border rounded-3xl <?= $severity_class ?> transition-all hover:scale-[1.005]">
                                        <span class="material-symbols-outlined text-lg <?= $icon_color ?> shrink-0"><?= $obs['severity'] == 'High' ? 'warning' : 'info' ?></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-headline font-extrabold text-sm text-slate-900 truncate"><?= htmlspecialchars($obs['title']) ?></p>
                                            <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($obs['description']) ?></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[8px] font-black text-primary uppercase tracking-widest"><?= htmlspecialchars($obs['project_name']) ?></span>
                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest"><?= date('H:i', strtotime($obs['created_at'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; else: ?>
                                    <div class="text-center py-10">
                                        <span class="material-symbols-outlined text-3xl text-slate-200">check_circle</span>
                                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No recent safety reports</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex flex-col gap-6">
                        <div class="bg-slate-900 text-white p-8 rounded-[2.5rem] relative overflow-hidden shadow-2xl">
                            <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary/10 rounded-full blur-3xl"></div>
                            <div class="relative z-10">
                                <h3 class="font-headline text-2xl font-black mb-2">Emergency Hub</h3>
                                <p class="font-body text-[11px] text-slate-400 mb-8 leading-relaxed">Direct connection to terminal emergency services and automated containment protocols.</p>
                                <button class="w-full py-4 border-2 border-red-500/50 text-red-500 font-headline font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-red-500 hover:text-white transition-all active:scale-95 flex items-center justify-center gap-3">
                                    <span class="material-symbols-outlined text-lg">emergency</span>
                                    INITIATE SIGNAL
                                </button>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                            <h4 class="font-headline text-[9px] font-black tracking-widest text-primary mb-6 uppercase">UPCOMING TOOLBOX TALKS</h4>
                            <div class="space-y-5">
                                <div class="flex gap-4 group">
                                    <div class="flex-none w-10 h-10 bg-slate-50 border border-slate-100 rounded-xl flex flex-col items-center justify-center transition-all group-hover:border-primary/30 group-hover:bg-primary/5">
                                        <span class="text-[7px] font-black text-slate-400 uppercase">OCT</span>
                                        <span class="text-base font-black text-slate-900 leading-tight">24</span>
                                    </div>
                                    <div>
                                        <p class="font-headline font-bold text-xs text-slate-900">Working at Heights v3</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">08:30 AM • Site B Hub</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
