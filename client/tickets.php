<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: ../client-login.php");
    exit();
}
$conn = get_db_connection();

// Handle New Ticket Submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_ticket'])) {
    $subject = $conn->real_escape_string($_POST['subject']);
    $priority = $conn->real_escape_string($_POST['priority']);
    $description = $conn->real_escape_string($_POST['description']);
    $project_id = (int)($_POST['project_id'] ?? 0);

    $sql = "INSERT INTO tickets (client_id, project_id, subject, priority, description, status) 
            VALUES ($client_id, " . ($project_id ?: "NULL") . ", '$subject', '$priority', '$description', 'Open')";
    
    if ($conn->query($sql)) {
        $message = "Ticket created successfully.";
    } else {
        $message = "Error creating ticket: " . $conn->error;
    }
}

// Fetch Tickets
$tickets_res = $conn->query("
    SELECT t.*, p.name as project_name 
    FROM tickets t 
    LEFT JOIN projects p ON t.project_id = p.id 
    WHERE t.client_id = $client_id 
    ORDER BY t.created_at DESC
");
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>WILSOVLEWEL | Client Tickets Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#EAB308",
                        "on-primary": "#000000",
                        "surface": "#FDFDFD",
                        "on-surface": "#1A1A1A",
                        "error": "#B00020",
                        "error-container": "#FFDAD6",
                        "on-error-container": "#410002",
                    },
                    fontFamily: { "headline": ["Space Grotesk"], "body": ["Manrope"], "label": ["Space Grotesk"] }
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
<body class="bg-surface font-body text-on-surface site-gradient-bg">
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

    <main class="lg:ml-64 pt-20 pb-8 px-6 relative z-10">
        <!-- TopNavBar -->
        <script src="../components/client_topnav.js" data-root="../"></script>
        
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div class="space-y-1">
                    <span class="font-headline text-[9px] uppercase tracking-[0.2em] text-secondary">System Support Console</span>
                    <h1 class="font-headline text-3xl font-bold text-on-surface tracking-tight mt-1">Support Portal</h1>
                    <p class="text-on-surface-variant text-xs max-w-md">Open new tickets or track existing resolutions for your projects.</p>
                </div>
                <button onclick="document.getElementById('newTicketModal').classList.remove('hidden')" class="bg-gradient-to-br from-primary to-amber-600 text-on-primary px-6 py-3 rounded-full font-headline font-medium flex items-center gap-2 shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform active:opacity-80">
                    <span class="material-symbols-outlined">add</span> Create New Ticket
                </button>
            </div>

            <?php if ($message): ?>
            <div class="mb-8 p-4 bg-primary/10 border border-primary text-primary rounded-xl font-bold text-sm">
                <?= $message ?>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                <!-- Left Column: Tickets List -->
                <div class="xl:col-span-8 space-y-6">
                    <?php if ($tickets_res->num_rows > 0): 
                        while($t = $tickets_res->fetch_assoc()):
                            $priority_class = ($t['priority'] == 'Critical') ? 'bg-error-container text-on-error-container border-error' : (($t['priority'] == 'High') ? 'bg-orange-100 text-orange-700 border-orange-500' : 'bg-blue-100 text-blue-700 border-blue-500');
                    ?>
                    <div class="group bg-white rounded-2xl p-6 shadow-sm border-l-4 <?= ($t['priority'] == 'Critical' ? 'border-error' : ($t['priority'] == 'High' ? 'border-orange-500' : 'border-blue-500')) ?> hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-headline text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full <?= $priority_class ?>"><?= $t['priority'] ?></span>
                                    <span class="text-xs text-slate-400 font-headline">#TK-<?= $t['id'] ?></span>
                                </div>
                                <h3 class="text-xl font-bold text-on-surface font-headline"><?= htmlspecialchars($t['subject']) ?></h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1"><?= htmlspecialchars($t['project_name'] ?: 'Global Support') ?></p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 font-headline uppercase block">Status</span>
                                <span class="text-sm font-bold <?= $t['status'] == 'Resolved' ? 'text-emerald-500' : 'text-primary' ?>"><?= strtoupper($t['status']) ?></span>
                            </div>
                        </div>
                        <p class="text-slate-600 text-sm mb-6 max-w-2xl leading-relaxed"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
                        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-50">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-400 font-headline uppercase">Assigned To:</span>
                                <span class="text-sm font-bold"><?= htmlspecialchars($t['department'] ?: 'Unassigned') ?></span>
                            </div>
                            <div class="flex items-center gap-8">
                                <button class="text-primary hover:underline text-sm font-headline font-bold flex items-center gap-1">
                                    View Thread <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="p-12 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-4">confirmation_number</span>
                        <p class="text-slate-400 italic">No tickets found. Need help? Create a new ticket.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Stats -->
                <div class="xl:col-span-4 space-y-8">
                    <div class="bg-slate-900 rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 opacity-10">
                            <span class="material-symbols-outlined text-6xl">support_agent</span>
                        </div>
                        <h2 class="font-headline text-lg font-bold mb-4">Support Overview</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Open Tickets</span>
                                <span class="text-3xl font-bold font-headline"><?= $conn->query("SELECT COUNT(*) FROM tickets WHERE client_id = $client_id AND status != 'Resolved'")->fetch_row()[0] ?></span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Resolved</span>
                                <span class="text-3xl font-bold font-headline text-emerald-400"><?= $conn->query("SELECT COUNT(*) FROM tickets WHERE client_id = $client_id AND status = 'Resolved'")->fetch_row()[0] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="font-headline font-bold mb-4">Emergency Support</h3>
                        <p class="text-xs text-slate-500 mb-6">For critical mechanical failures requiring immediate intervention at Site 04-Alpha.</p>
                        <a href="tel:+1234567890" class="flex items-center justify-center gap-3 w-full py-4 bg-error text-white rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-error/90 transition-all">
                            <span class="material-symbols-outlined text-sm">call</span>
                            Emergency Hotline
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- New Ticket Modal -->
    <div id="newTicketModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-2xl p-8 w-full max-w-xl mx-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-headline font-bold">Create New Ticket</h2>
                <button onclick="document.getElementById('newTicketModal').classList.add('hidden')" class="material-symbols-outlined text-slate-400 hover:text-on-surface">close</button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="create_ticket" value="1">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Subject</label>
                    <input type="text" name="subject" required class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Priority</label>
                        <select name="priority" class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Project (Optional)</label>
                        <select name="project_id" class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20">
                            <option value="0">General Support</option>
                            <?php
                            $proj_list = $conn->query("SELECT id, name FROM projects WHERE client_id = $client_id");
                            while($p = $proj_list->fetch_assoc()):
                            ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Description</label>
                    <textarea name="description" rows="4" required class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20"></textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-primary text-on-primary rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-primary/90 transition-all">Submit Ticket</button>
            </form>
        </div>
    </div>
</body>
</html>
