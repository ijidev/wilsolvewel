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

            <?php if ($project_id > 0): ?>
                <!-- Single Project View -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left: Details & Progress -->
                    <div class="lg:col-span-8 space-y-8">
                        <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-8 opacity-5">
                                <span class="material-symbols-outlined text-8xl">engineering</span>
                            </div>
                            <h3 class="font-headline text-xl font-bold mb-6">Status Overview</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div>
                                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Current Stage</span>
                                    <span class="block font-bold text-sm text-primary uppercase"><?= htmlspecialchars($project['status']) ?></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Start Date</span>
                                    <span class="block font-bold text-sm"><?= date('M d, Y', strtotime($project['created_at'])) ?></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Est. Completion</span>
                                    <span class="block font-bold text-sm"><?= htmlspecialchars($project['end_date'] ?: 'TBD') ?></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Total Budget</span>
                                    <span class="block font-bold text-sm">$<?= number_format($project['budget'], 2) ?></span>
                                </div>
                            </div>
                            
                            <div class="mt-8">
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Project Completion</span>
                                    <span class="text-sm font-bold text-primary">65%</span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary" style="width: 65%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Reports / Timeline -->
                        <div class="space-y-6">
                            <h3 class="font-headline text-xl font-bold flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">history</span>
                                Maintenance Reports
                            </h3>
                            
                            <div class="space-y-6 relative before:content-[''] before:absolute before:left-[19px] before:top-4 before:bottom-0 before:w-0.5 before:bg-slate-100">
                                <?php if ($reports_res->num_rows > 0): 
                                    while($rep = $reports_res->fetch_assoc()):
                                ?>
                                <div class="relative pl-12 group">
                                    <div class="absolute left-0 top-1 w-10 h-10 rounded-full bg-white border-4 border-slate-50 flex items-center justify-center z-10 shadow-sm group-hover:border-primary/20 transition-all">
                                        <span class="material-symbols-outlined text-primary text-sm">edit_note</span>
                                    </div>
                                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm group-hover:border-primary/20 transition-all">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= date('M d, Y @ H:i', strtotime($rep['created_at'])) ?></span>
                                                <span class="text-xs font-bold text-primary uppercase">By <?= htmlspecialchars($rep['author_name'] ?: 'System Admin') ?></span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($rep['content'])) ?></p>
                                    </div>
                                </div>
                                <?php endwhile; else: ?>
                                <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                    <p class="text-sm text-slate-400 italic">No reports generated for this project yet.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Associated Assets -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h4 class="text-sm font-bold font-headline uppercase tracking-widest mb-4">Linked Assets</h4>
                            <div class="space-y-4">
                                <?php
                                $assets_res = $conn->query("SELECT * FROM assets WHERE project_id = $project_id");
                                if ($assets_res->num_rows > 0):
                                    while($a = $assets_res->fetch_assoc()):
                                ?>
                                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                                    <div class="w-10 h-10 rounded bg-white flex items-center justify-center border border-slate-200 text-primary">
                                        <span class="material-symbols-outlined">inventory_2</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold"><?= htmlspecialchars($a['name']) ?></span>
                                        <span class="block text-[10px] text-slate-400 uppercase font-bold"><?= htmlspecialchars($a['status']) ?></span>
                                    </div>
                                </div>
                                <?php endwhile; else: ?>
                                <p class="text-xs text-slate-400 italic">No assets assigned to this project.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Procurement Updates -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                            <h4 class="text-sm font-bold font-headline uppercase tracking-widest mb-4">Logistics Status</h4>
                            <div class="space-y-4">
                                <?php
                                $po_res = $conn->query("SELECT * FROM procurement_orders WHERE project_id = $project_id ORDER BY updated_at DESC LIMIT 3");
                                if ($po_res->num_rows > 0):
                                    while($po = $po_res->fetch_assoc()):
                                ?>
                                <div class="p-3 bg-slate-50 rounded-lg border-l-2 border-primary">
                                    <span class="block text-xs font-bold"><?= htmlspecialchars($po['item_name']) ?></span>
                                    <span class="block text-[10px] text-primary uppercase font-bold"><?= htmlspecialchars($po['status']) ?></span>
                                    <p class="text-[10px] text-slate-400 mt-1"><?= htmlspecialchars($po['current_location'] ?: 'Warehouse') ?></p>
                                </div>
                                <?php endwhile; else: ?>
                                <p class="text-xs text-slate-400 italic">No procurement orders tracked.</p>
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
</body>
</html>
