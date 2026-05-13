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
    if (isset($_SESSION['client_id'])) {
        session_destroy();
    }
    header("Location: ../client-login.php");
    exit;
}
$client = $client_res->fetch_assoc();

// Fetch Stats
$active_projects_count = 0;
$projects_stats = $conn->query("SELECT COUNT(*) as count FROM projects WHERE client_id = $client_id AND status != 'Completed'");
if ($projects_stats) $active_projects_count = $projects_stats->fetch_assoc()['count'];

$open_tickets_count = 0;
$tickets_stats = $conn->query("SELECT COUNT(*) as count FROM tickets WHERE client_id = $client_id AND status != 'Resolved'");
if ($tickets_stats) $open_tickets_count = $tickets_stats->fetch_assoc()['count'];

// Fetch Recent Project Reports
$reports_res = $conn->query("
    SELECT pr.*, p.name as project_name, a.name as author_name 
    FROM project_reports pr 
    JOIN projects p ON pr.project_id = p.id 
    LEFT JOIN admins a ON pr.admin_id = a.id 
    WHERE p.client_id = $client_id 
    ORDER BY pr.created_at DESC 
    LIMIT 5
");

// Fetch Procurement Alerts (Recent updates for this client's projects)
$procurement_res = $conn->query("
    SELECT po.*, p.name as project_name 
    FROM procurement_orders po 
    LEFT JOIN projects p ON po.project_id = p.id 
    WHERE (p.client_id = $client_id OR po.requested_by = $client_id)
    AND po.status IN ('Pending', 'Processing', 'In Transit', 'Held by Customs')
    ORDER BY po.created_at DESC 
    LIMIT 2
");
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>WILSOVLEWEL | Client Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-variant": "#e1e2e9",
                        "on-tertiary-container": "#ffb991",
                        "on-secondary-fixed-variant": "#354764",
                        "surface-container-high": "#e7e8ef",
                        "on-primary-fixed-variant": "#004787",
                        "on-secondary": "#ffffff",
                        "error-container": "#ffdad6",
                        "primary": "#EAB308",
                        "on-tertiary-fixed": "#321200",
                        "on-secondary-fixed": "#061c36",
                        "on-surface": "#191c20",
                        "surface-dim": "#d9dae0",
                        "inverse-surface": "#2e3036",
                        "on-tertiary-fixed-variant": "#753400",
                        "on-primary": "#ffffff",
                        "surface-container-low": "#f3f3fa",
                        "secondary-container": "#c8dbfe",
                        "inverse-primary": "#a6c8ff",
                        "on-primary-container": "#aacaff",
                        "outline-variant": "#c2c6d3",
                        "error": "#ba1a1a",
                        "surface-container": "#ededf4",
                        "secondary-fixed-dim": "#b4c7ea",
                        "surface-container-lowest": "#ffffff",
                        "secondary": "#4c5f7d",
                        "surface-tint": "#1d5fa8",
                        "surface-container-highest": "#e1e2e9",
                        "tertiary": "#662c00",
                        "primary-container": "#06549d",
                        "on-secondary-container": "#4d607e",
                        "surface": "#f9f9ff",
                        "on-tertiary": "#ffffff",
                        "secondary-fixed": "#d5e3ff",
                        "primary-fixed-dim": "#a6c8ff",
                        "on-error": "#ffffff",
                        "surface-bright": "#f9f9ff",
                        "on-primary-fixed": "#001c3b",
                        "tertiary-fixed-dim": "#ffb68c",
                        "on-surface-variant": "#424751",
                        "inverse-on-surface": "#f0f0f7",
                        "background": "#f9f9ff",
                        "on-error-container": "#93000a",
                        "outline": "#727782",
                        "tertiary-fixed": "#ffdbc9",
                        "tertiary-container": "#8a3e00",
                        "on-background": "#191c20",
                        "primary-fixed": "#d5e3ff"
                    },
                    "borderRadius": { "DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px" },
                    "fontFamily": { "headline": ["Space Grotesk"], "body": ["Manrope"], "label": ["Space Grotesk"] },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .technical-grid { background-image: radial-gradient(circle, #c2c6d4 1px, transparent 1px); background-size: 24px 24px; opacity: 0.05; }
        .site-gradient-bg { background: radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.1) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(0, 0, 0, 0.1) 0%, transparent 50%); background-attachment: fixed; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface site-gradient-bg min-h-screen">
    <!-- TopNavBar -->
    <script src="../components/client_topnav.js" data-root="../"></script>
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>

    <main class="pt-20 pb-8 relative min-h-screen">
        <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <!-- Header Section -->
            <header class="flex justify-between items-end mb-8">
                <div>
                    <span class="font-headline text-[9px] uppercase tracking-[0.2em] text-primary font-bold">Terminal System Overview</span>
                    <h1 class="text-2xl font-headline font-bold text-on-surface tracking-tight mt-1">Hello, <?= htmlspecialchars($client['name']) ?></h1>
                    <p class="text-xs text-secondary"><?= htmlspecialchars($client['company']) ?></p>
                </div>
                <div class="flex gap-3">
                    <a href="propose_project.php" class="px-6 py-3 bg-gradient-to-br from-primary to-amber-600 text-on-primary rounded-full font-['Space_Grotesk'] font-medium text-sm flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Propose Project
                    </a>
                </div>
            </header>

            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-12 gap-6 items-start">
                <!-- Project Progress (Major Card) -->
                <section class="col-span-12 lg:col-span-8 bg-surface-container-lowest p-8 rounded-[1.5rem] shadow-[0_40px_60px_-15px_rgba(0,0,0,0.04)] relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <span class="material-symbols-outlined text-8xl">engineering</span>
                    </div>
                    <div class="flex justify-between items-start mb-8 relative z-10">
                        <div>
                            <h3 class="font-['Space_Grotesk'] text-xl font-bold tracking-tight">Active Projects</h3>
                            <p class="text-sm text-secondary">Ongoing engineering cycles</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <span class="block text-2xl font-bold text-primary font-['Space_Grotesk']"><?= $active_projects_count ?></span>
                                <span class="text-[10px] uppercase tracking-widest text-slate-400">In Progress</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <?php
                        $projects_res = $conn->query("SELECT * FROM projects WHERE client_id = $client_id AND status != 'Completed' LIMIT 3");
                        if ($projects_res->num_rows > 0):
                            while($p = $projects_res->fetch_assoc()):
                        ?>
                        <div class="p-5 bg-surface-container-low rounded-xl border border-transparent hover:border-outline-variant/20 transition-all">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined">analytics</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm"><?= htmlspecialchars($p['name']) ?></h4>
                                        <p class="text-xs text-slate-500">Status: <?= htmlspecialchars($p['status']) ?></p>
                                    </div>
                                </div>
                                <a href="projects.php?id=<?= $p['id'] ?>" class="text-xs font-bold text-primary hover:underline uppercase tracking-widest">Details</a>
                            </div>
                            <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-primary" style="width: 65%;"></div>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <p class="text-sm text-slate-400 italic">No active projects found.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Procurement Alert (Vertical Card) -->
                <section class="col-span-12 lg:col-span-4 bg-surface-container-low p-8 rounded-[1.5rem] relative">
                    <h3 class="font-['Space_Grotesk'] text-xl font-bold tracking-tight mb-6">Logistics Tracking</h3>
                    <div class="space-y-4">
                        <?php if ($procurement_res->num_rows > 0): 
                            while($po = $procurement_res->fetch_assoc()):
                                $status_class = ($po['status'] == 'Held by Customs') ? 'border-error' : 'border-primary';
                                $badge_class = ($po['status'] == 'Held by Customs') ? 'bg-error-container text-on-error-container' : 'bg-primary/20 text-primary';
                        ?>
                        <div class="p-4 bg-surface-container-lowest rounded-xl shadow-sm border-l-4 <?= $status_class ?>">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-bold text-sm"><?= htmlspecialchars($po['item_name']) ?></span>
                                <span class="material-symbols-outlined text-primary text-lg">local_shipping</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mb-1 uppercase font-bold"><?= htmlspecialchars($po['project_name']) ?></p>
                            <p class="text-xs text-slate-500 mb-3"><?= htmlspecialchars($po['current_location'] ?: 'Tracking initiated') ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold <?= $badge_class ?> px-2 py-1 rounded"><?= strtoupper($po['status']) ?></span>
                            </div>
                        </div>
                        <?php endwhile; else: ?>
                        <p class="text-xs text-slate-400 italic">No active shipments in transit.</p>
                        <?php endif; ?>
                    </div>
                    <a href="procurement.php" class="block text-center w-full mt-6 py-4 text-primary text-xs font-bold uppercase tracking-[0.1em] border border-dashed border-primary/30 rounded-xl hover:bg-primary/5 transition-colors">
                        View All Orders
                    </a>
                </section>

                <!-- Recent Activity (Horizontal Detail) -->
                <section class="col-span-12 lg:col-span-7 bg-surface-container-lowest p-8 rounded-[1.5rem] shadow-[0_40px_60px_-15px_rgba(0,0,0,0.04)]">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="font-['Space_Grotesk'] text-xl font-bold tracking-tight">Recent Updates</h3>
                            <p class="text-sm text-secondary">Latest project reports and logs</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <?php if ($reports_res->num_rows > 0): 
                            while($rep = $reports_res->fetch_assoc()):
                        ?>
                        <div class="group border-b border-slate-100 pb-4 last:border-0">
                            <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">
                                <span><?= htmlspecialchars($rep['project_name']) ?></span>
                                <span><?= date('M d, H:i', strtotime($rep['created_at'])) ?></span>
                            </div>
                            <p class="text-sm text-slate-700 line-clamp-2"><?= htmlspecialchars($rep['content']) ?></p>
                            <div class="mt-2 flex justify-between items-center">
                                <span class="text-[10px] text-primary font-bold">BY <?= strtoupper(htmlspecialchars($rep['author_name'] ?: 'ADMIN')) ?></span>
                                <a href="projects.php?id=<?= $rep['project_id'] ?>" class="text-[10px] text-slate-400 hover:text-primary transition-colors">VIEW FULL REPORT</a>
                            </div>
                        </div>
                        <?php endwhile; else: ?>
                        <p class="text-sm text-slate-400 italic">No recent reports found.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Support Center (Mini Card) -->
                <section class="col-span-12 lg:col-span-5 bg-surface-container p-8 rounded-[1.5rem] relative">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-['Space_Grotesk'] text-xl font-bold tracking-tight">Support Status</h3>
                        <span class="text-xs font-bold text-primary bg-white px-3 py-1 rounded-full"><?= $open_tickets_count ?> OPEN TICKETS</span>
                    </div>
                    <div class="space-y-3">
                        <?php
                        $tickets_res = $conn->query("SELECT * FROM tickets WHERE client_id = $client_id AND status != 'Resolved' ORDER BY created_at DESC LIMIT 3");
                        if ($tickets_res->num_rows > 0):
                            while($t = $tickets_res->fetch_assoc()):
                        ?>
                        <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all cursor-pointer" onclick="location.href='tickets.php?id=<?= $t['id'] ?>'">
                            <div class="w-1.5 h-8 bg-primary rounded-full"></div>
                            <div class="flex-1">
                                <span class="block text-[10px] font-['Space_Grotesk'] text-slate-400">#TK-<?= $t['id'] ?> • <?= strtoupper($t['status']) ?></span>
                                <span class="font-bold text-xs uppercase"><?= htmlspecialchars($t['subject']) ?></span>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 text-sm">chevron_right</span>
                        </div>
                        <?php endwhile; else: ?>
                        <p class="text-xs text-slate-400 italic">All caught up! No active tickets.</p>
                        <?php endif; ?>
                    </div>
                    <a href="tickets.php" class="block w-full mt-6 py-4 bg-slate-900 text-white text-center rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-colors">
                        Go to Support Center
                    </a>
                </section>
            </div>
        </div>
    </main>

    <!-- Floating Technical Info -->
    <div class="fixed bottom-8 right-8 z-50">
        <div class="bg-slate-900/90 backdrop-blur-md text-white p-6 rounded-2xl shadow-2xl flex items-center gap-6 border border-white/10">
            <div class="flex flex-col">
                <span class="text-[9px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-1">Terminal Status</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-['Space_Grotesk'] font-bold">ONLINE</span>
                    <span class="text-[8px] text-emerald-400">SECURE</span>
                </div>
            </div>
            <div class="h-10 w-[1px] bg-white/20"></div>
            <div class="flex flex-col">
                <span class="text-[9px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-1">Last Sync</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-['Space_Grotesk'] font-bold"><?= date('H:i') ?></span>
                    <span class="text-[8px] text-slate-400">UTC</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
