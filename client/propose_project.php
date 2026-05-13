<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 1;
$conn = get_db_connection();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['asset_model'] . " - " . $_POST['serial_number']);
    $description = $conn->real_escape_string($_POST['description']);
    $priority = $conn->real_escape_string($_POST['priority'] ?? 'Standard');
    $service_type = $conn->real_escape_string($_POST['service_type']);
    $location = $conn->real_escape_string($_POST['location']);

    // Auto-Routing Logic
    $dept_id = get_auto_assigned_department($conn, 'project_proposal', $title . ' ' . $description);

    $sql = "INSERT INTO projects (client_id, department_id, name, description, status, budget, created_at) 
            VALUES ($client_id, " . ($dept_id ?: "NULL") . ", '$title', '$description', 'Diagnostic', 0, NOW())";
    
    if ($conn->query($sql)) {
        $project_id = $conn->insert_id;
        // Optionally create an audit log
        $conn->query("INSERT INTO audit_logs (user_type, user_id, action, details) VALUES ('client', $client_id, 'Proposed Project', 'New project proposal: $title')");
        
        $message = "Project proposal submitted successfully. Ref: #PROJ-$project_id";
    } else {
        $error = "Error submitting proposal: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Wilsovlewel | Propose Project</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
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

    <main class="pt-20 pb-8 px-6 relative z-10 max-w-7xl mx-auto">
        <!-- TopNavBar -->
        <script src="../components/client_topnav.js" data-root="../"></script>
        
        <div class="mb-12">
            <span class="font-headline text-[10px] uppercase tracking-[0.2em] text-primary font-bold">New Submittal</span>
            <h1 class="font-headline text-4xl md:text-5xl font-bold tracking-tight text-on-surface mt-2">Propose Project</h1>
            <p class="text-on-surface-variant mt-4 max-w-2xl leading-relaxed">
                Initiate a maintenance or overhaul request by detailing asset specifications.
            </p>
        </div>

        <?php if ($message): ?>
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm">
            <?= $message ?>
            <div class="mt-2"><a href="projects.php" class="underline">View in Ledger</a></div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-8 bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden">
                <form method="POST" class="space-y-8 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">Machine Model</label>
                            <div class="relative">
                                <input list="asset_models" name="asset_model" required placeholder="e.g. Caterpillar D11" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-primary/20 text-on-surface" />
                                <datalist id="asset_models">
                                    <option value="Caterpillar D11 Bulldozer">
                                    <option value="Komatsu PC2000 Excavator">
                                    <option value="Liebherr LTM 11200 Crane">
                                    <option value="Volvo A60H Articulated Hauler">
                                </datalist>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">Serial / Asset Number</label>
                            <input name="serial_number" required class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-primary/20 text-on-surface" placeholder="e.g. SN-8829-XL" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">Type of Service</label>
                            <input list="service_types" name="service_type" required placeholder="e.g. Major Overhaul" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-primary/20 text-on-surface" />
                            <datalist id="service_types">
                                <option value="Major Overhaul (Level 4)">
                                <option value="Component Repair">
                                <option value="Preventive Maintenance">
                                <option value="Emergency Breakdown">
                                <option value="Technical Inspection">
                            </datalist>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">Asset Location</label>
                            <input name="location" required class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-primary/20 text-on-surface" placeholder="Coordinates or Site Zone" type="text" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">Issue Description / Symptoms</label>
                        <textarea name="description" required class="w-full p-6 rounded-3xl bg-slate-50 border-none focus:ring-2 focus:ring-primary/20 text-on-surface resize-none" placeholder="Describe mechanical anomalies..." rows="4"></textarea>
                    </div>
                    <div class="space-y-4">
                        <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold ml-4">Priority Level</label>
                        <div class="flex flex-wrap gap-4 px-4">
                            <?php foreach(['Routine', 'Standard', 'Urgent', 'Critical'] as $prio): ?>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input class="hidden peer" name="priority" type="radio" value="<?= $prio ?>" <?= $prio == 'Standard' ? 'checked' : '' ?> />
                                <div class="px-6 py-2 rounded-full border border-slate-200 peer-checked:bg-primary peer-checked:border-transparent peer-checked:text-on-primary transition-all text-sm font-bold">
                                    <?= $prio ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="pt-6 flex justify-end gap-4">
                        <button class="px-10 py-4 rounded-xl font-headline font-bold bg-slate-900 text-white shadow-lg active:scale-95 transition-all uppercase tracking-widest text-xs" type="submit">Submit Proposal</button>
                    </div>
                </form>
            </div>
            
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-primary p-8 rounded-3xl shadow-lg text-on-primary relative overflow-hidden">
                    <span class="material-symbols-outlined text-4xl mb-4">info</span>
                    <h4 class="font-headline font-bold text-lg leading-tight mb-2">Submission Protocol</h4>
                    <p class="text-sm opacity-80 leading-relaxed">Major Overhaul projects require a minimum of 72 hours for initial diagnostic assessment and technician assignment.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
