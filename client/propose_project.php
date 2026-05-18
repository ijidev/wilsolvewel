<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: login.php");
    exit();
}
$conn = get_db_connection();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid or expired CSRF token. Please reload the page and try again.';
    } else {
        $title = $_POST['asset_model'] . " - " . $_POST['serial_number'];
        $description = $_POST['description'];
        $service_type = $_POST['service_type'];
        $location = $_POST['location'];

        // Auto-Routing Logic
        $dept_id = get_auto_assigned_department($conn, 'project_proposal', $title . ' ' . $description);

        $stmt = $conn->prepare("INSERT INTO projects (client_id, department_id, name, description, status, budget, created_at) VALUES (?, ?, ?, ?, 'Planning', 0, NOW())");
        $dept_id_val = $dept_id ?: null;
        $stmt->bind_param("iiss", $client_id, $dept_id_val, $title, $description);
        if ($stmt->execute()) {
            $project_id = $stmt->insert_id;
            log_audit($conn, 'Create', 'Projects', 'Client', $client_id, 'Proposed Project', ['title' => $title]);

            $message = "Project proposal submitted successfully. Ref: #PROJ-$project_id";
        } else {
            $error = "Error submitting proposal: " . $stmt->error;
        }
        $stmt->close();
    }
}

$page_title = 'Wilsovlewel | Propose Project';
$page_h1 = 'Propose Project';
$page_h1_sub = 'Initiate a maintenance or overhaul request by detailing asset specifications.';
$page_h1_badge = 'New Submittal';

ob_start();
?>

<?php if ($message): ?>
<div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm">
    <?= $message ?>
    <div class="mt-2"><a href="projects.php" class="underline">View in Ledger</a></div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <div class="lg:col-span-8 bg-white p-4 sm:p-6 lg:p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden">
        <form method="POST" class="space-y-8 relative z-10">
            <?= get_csrf_field() ?>
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
                <button class="px-6 sm:px-10 py-4 rounded-xl font-headline font-bold bg-slate-900 text-white shadow-lg active:scale-95 transition-all uppercase tracking-widest text-xs" type="submit">Submit Proposal</button>
            </div>
        </form>
    </div>
    
    <div class="lg:col-span-4 space-y-8">
        <div class="bg-primary p-4 sm:p-6 lg:p-8 rounded-3xl shadow-lg text-on-primary relative overflow-hidden">
            <span class="material-symbols-outlined text-4xl mb-4">info</span>
            <h4 class="font-headline font-bold text-lg leading-tight mb-2">Submission Protocol</h4>
            <p class="text-sm opacity-80 leading-relaxed">Major Overhaul projects require a minimum of 72 hours for initial diagnostic assessment and technician assignment.</p>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../components/client_layout.php';
