<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$conn = get_db_connection();

$admin_id = $_SESSION['admin_id'] ?? 1;
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_avatar = $_SESSION['admin_avatar'] ?? null;
// If admin has no department/template, treat as Director (root admin)
$permissions = get_admin_permissions($admin_id) ?: ['role' => 'Director'];

// --- Page-level Access Guard ---
$is_director = ($permissions['role'] ?? '') === 'Director';
$has_hsse_access = $is_director
    || !empty($permissions['HSSE']['read'])
    || !empty($permissions['HSSE']['write']);
$access_denied = !$has_hsse_access;


if (isset($_GET['ajax_action']) && $_GET['ajax_action'] === 'submit_observation') {
    header('Content-Type: application/json');
    $admin_id = $_SESSION['admin_id'] ?? 1;
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $type = $conn->real_escape_string($_POST['type'] ?? 'Routine');
    $severity = $conn->real_escape_string($_POST['severity'] ?? 'Low');
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');
    $project_id = $_POST['project_id'] ? (int)$_POST['project_id'] : 'NULL';
    
    if (empty($title) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'Title and Description are required']);
        exit;
    }

    // --- Safe Days Reset Logic ---
    if ($severity === 'High') {
        // Calculate current safe days before reset
        $last_incident_res = $conn->query("SELECT created_at FROM hsse_observations WHERE severity = 'High' ORDER BY created_at DESC LIMIT 1");
        $current_safe_days = 0;
        if ($last_incident_res && $last_incident_res->num_rows > 0) {
            $last_date = new DateTime($last_incident_res->fetch_assoc()['created_at']);
            $current_safe_days = (new DateTime())->diff($last_date)->days;
        } else {
            $current_safe_days = get_setting('hsse_base_safe_days', 412);
        }

        // Log the Milestone for record keeping
        $conn->query("INSERT INTO hsse_milestones (safe_days, reason) VALUES ($current_safe_days, 'High-Severity Incident: $title')");
    }

    $sql = "INSERT INTO hsse_observations (title, type, severity, location, description, inspector_id, status, project_id) 
            VALUES ('$title', '$type', '$severity', '$location', '$description', $admin_id, 'Open', $project_id)";
    
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Observation logged successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}
if (isset($_GET['ajax_action']) && $_GET['ajax_action'] === 'resolve_observation') {
    header('Content-Type: application/json');
    $id = (int)$_POST['id'];
    $sql = "UPDATE hsse_observations SET status = 'Resolved' WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}

if (isset($_GET['ajax_action']) && $_GET['ajax_action'] === 'submit_audit') {
    header('Content-Type: application/json');
    $id = (int)($_POST['id'] ?? 0);
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $type = $conn->real_escape_string($_POST['type'] ?? '');
    $audit_date = $conn->real_escape_string($_POST['audit_date'] ?? '');
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    
    if ($id > 0) {
        $sql = "UPDATE hsse_audits SET title='$title', type='$type', audit_date='$audit_date', location='$location' WHERE id=$id";
        $action = 'Updated';
    } else {
        $sql = "INSERT INTO hsse_audits (title, type, audit_date, location, status) VALUES ('$title', '$type', '$audit_date', '$location', 'Upcoming')";
        $action = 'Scheduled';
    }
    
    if ($conn->query($sql)) {
        log_audit($conn, $id > 0 ? 'Update' : 'Create', 'HSSE_Audit', 'Admin', $admin_id, "$action audit: $title");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}

if (isset($_GET['ajax_action']) && $_GET['ajax_action'] === 'delete_audit') {
    header('Content-Type: application/json');
    $id = (int)$_POST['id'];
    if ($conn->query("DELETE FROM hsse_audits WHERE id = $id")) {
        log_audit($conn, 'Delete', 'HSSE_Audit', 'Admin', $admin_id, "Deleted audit ID: $id");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}

if (isset($_GET['ajax_action']) && $_GET['ajax_action'] === 'get_observation_replies') {
    header('Content-Type: application/json');
    $obs_id = (int)$_GET['observation_id'];
    $res = $conn->query("
        SELECT r.*, a.name as admin_name, a.avatar as admin_avatar 
        FROM hsse_observation_replies r 
        JOIN admins a ON r.admin_id = a.id 
        WHERE r.observation_id = $obs_id 
        ORDER BY r.created_at ASC
    ");
    $replies = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) $replies[] = $row;
    }
    echo json_encode($replies);
    exit;
}

if (isset($_GET['ajax_action']) && $_GET['ajax_action'] === 'add_observation_reply') {
    header('Content-Type: application/json');
    $obs_id = (int)$_POST['observation_id'];
    $message = $conn->real_escape_string(trim($_POST['message']));
    if (!empty($message)) {
        if ($conn->query("INSERT INTO hsse_observation_replies (observation_id, admin_id, message) VALUES ($obs_id, $admin_id, '$message')")) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
    }
    exit;
}

// --- Fetch Statistics (Automated) ---

// 1. Calculate Safe Days: Days since the last 'High' severity observation (LTI equivalent)
$last_incident_res = $conn->query("SELECT created_at FROM hsse_observations WHERE severity = 'High' ORDER BY created_at DESC LIMIT 1");
$safe_days = 0;
if ($last_incident_res && $last_incident_res->num_rows > 0) {
    $last_date = new DateTime($last_incident_res->fetch_assoc()['created_at']);
    $now = new DateTime();
    $safe_days = $now->diff($last_date)->days;
} else {
    // If no incidents recorded, use base project start or a default
    $safe_days = get_setting('hsse_base_safe_days', 412); 
}

// 2. Compliance Index: (Resolved Observations / Total Observations) * 100
$total_obs_res = $conn->query("SELECT COUNT(*) as total FROM hsse_observations");
$resolved_obs_res = $conn->query("SELECT COUNT(*) as total FROM hsse_observations WHERE status = 'Resolved'");
$total_obs = $total_obs_res->fetch_assoc()['total'];
$resolved_obs = $resolved_obs_res->fetch_assoc()['total'];
$compliance_index = ($total_obs > 0) ? round(($resolved_obs / $total_obs) * 100, 1) : 100.0;

// 3. Footer Stats
$near_misses = 0;
$near_miss_res = $conn->query("SELECT COUNT(*) as total FROM hsse_observations WHERE type = 'Incident'");
if ($near_miss_res) $near_misses = $near_miss_res->fetch_assoc()['total'];

$breaches = 0;
$breach_res = $conn->query("SELECT COUNT(*) as total FROM hsse_observations WHERE severity = 'High' OR type = 'Hazard'");
if ($breach_res) $breaches = $breach_res->fetch_assoc()['total'];

// --- Fetch all for management ---
$all_observations_res = $conn->query("
    SELECT o.*, a.name as inspector_name 
    FROM hsse_observations o 
    LEFT JOIN admins a ON o.inspector_id = a.id 
    ORDER BY o.created_at DESC
");

// --- Handle Inline Updates (For manual overrides if needed) ---
if (isset($_POST['update_metric'])) {
    if ($permissions['role'] === 'Director' || $permissions['HSSE']['write']) {
        $key = $_POST['metric_key'];
        $val = $_POST['metric_value'];
        set_setting($key, $val);
        log_audit($conn, 'Update', 'HSSE_Metric', 'Admin', $admin_id, "Updated $key to $val");
        header("Location: hsse.php?updated=1");
        exit;
    }
}

$critical_count = 0;
$critical_res = $conn->query("SELECT COUNT(*) as count FROM hsse_observations WHERE severity = 'High' AND status != 'Resolved'");
if ($critical_res) $critical_count = $critical_res->fetch_assoc()['count'];

$observations_res = $conn->query("
    SELECT o.*, a.name as inspector_name, a.avatar as inspector_avatar 
    FROM hsse_observations o 
    LEFT JOIN admins a ON o.inspector_id = a.id 
    ORDER BY o.created_at DESC 
    LIMIT 10
");

$audits_res = $conn->query("SELECT * FROM hsse_audits WHERE status = 'Upcoming' ORDER BY audit_date ASC LIMIT 2");

$milestones_res = $conn->query("SELECT * FROM hsse_milestones ORDER BY reset_date DESC LIMIT 5");

$projects_res = $conn->query("SELECT id, name FROM projects ORDER BY name ASC");
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>HSSE Monitor | Terminal Operations</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>
        window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;
        window.WILSOLVEWEL_AVATAR = <?php echo json_encode($admin_avatar); ?>;
    </script>
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
        .blueprint-grid { background-image: radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size: 24px 24px; opacity: 0.1; }
        .modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(6px); z-index: 500; display: none; align-items: center; justify-content: center; padding: 1rem; }
        .modal-overlay.open { display: flex; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface h-screen overflow-hidden flex lg:pl-64">

<div class="fixed inset-0 blueprint-grid pointer-events-none z-0"></div>

<!-- SideNavBar -->
<script src="../components/admin_sidenav.js" data-root="../"></script>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative z-10">
    <!-- TopNavBar -->
    <script src="../components/admin_topnav.js" data-root="../"></script>

    <main class="flex-1 overflow-y-auto p-8 relative custom-scrollbar">
        <div class="w-full relative z-10">

<?php if ($access_denied): ?>
        <!-- ACCESS DENIED SCREEN -->
        <div class="min-h-[80vh] flex items-center justify-center">
            <div class="text-center max-w-lg mx-auto">
                <div class="w-24 h-24 bg-red-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-lg border border-red-100">
                    <span class="material-symbols-outlined text-5xl text-red-500">gpp_bad</span>
                </div>
                <span class="inline-block px-4 py-1.5 bg-red-50 text-red-600 text-[9px] font-black uppercase tracking-[0.3em] rounded-full border border-red-100 mb-6">Unauthorized Access</span>
                <h1 class="text-5xl font-headline font-black text-slate-900 leading-none mb-4">Access<br/>Restricted</h1>
                <p class="text-slate-400 text-sm font-medium leading-relaxed mb-10">
                    You do not have the clearance to access the <strong class="text-slate-600">HSSE Monitor</strong> module.<br/>
                    Contact your system administrator to request access.
                </p>
                <div class="flex items-center justify-center gap-4">
                    <a href="index.php" class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-all shadow-lg flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">arrow_back</span> Go to Dashboard
                    </a>
                    <a href="mailto:admin@wilsolvewel.com" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:border-red-200 hover:text-red-500 transition-all shadow-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">mail</span> Request Access
                    </a>
                </div>
            </div>
        </div>
<?php else: ?>
            <!-- Header Section -->
            <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                <div>
                    <h2 class="text-4xl font-headline font-extrabold tracking-tight text-slate-900 leading-none">HSSE Monitoring</h2>
                    <p class="font-bold uppercase tracking-[0.2em] text-[10px] text-slate-400 mt-2">Terminal Operational Environment • System Node 04</p>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <button class="flex-1 md:flex-none bg-white border border-slate-200 px-6 py-3 rounded-2xl font-bold text-xs tracking-wider hover:bg-slate-50 transition-all flex items-center justify-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-lg">download</span> EXPORT
                    </button>
                    <button onclick="openObservationModal()" class="flex-1 md:flex-none bg-primary text-on-primary px-6 py-3 rounded-2xl font-bold text-xs tracking-wider shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">add_circle</span> NEW OBSERVATION
                    </button>
                </div>
            </div>

            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-12 gap-6 items-start">
                <!-- Zero-Harm Target Counter -->
                <div class="col-span-12 md:col-span-4 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between h-[280px]">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest text-primary font-black">Performance Metric</span>
                        <h3 class="font-headline text-xl font-bold mt-2">Zero-Harm Target</h3>
                    </div>
                    <div class="text-center py-4 group/metric relative">
                        <span class="text-6xl font-headline font-extrabold tracking-tighter text-slate-900"><?= $safe_days ?></span>
                        <p class="font-bold uppercase text-[10px] tracking-widest text-slate-400 mt-2">Safe Days Since LTI</p>
                        <div class="mt-2 inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 rounded-full border border-emerald-100">
                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                            <span class="text-[8px] font-bold text-emerald-600 uppercase tracking-tighter">Automated Feed</span>
                        </div>
                    </div>
                    <div class="flex gap-1.5">
                        <div class="h-1.5 flex-1 bg-primary rounded-full"></div>
                        <div class="h-1.5 flex-1 bg-primary rounded-full"></div>
                        <div class="h-1.5 flex-1 bg-primary rounded-full"></div>
                        <div class="h-1.5 flex-1 bg-slate-100 rounded-full"></div>
                        <div class="h-1.5 flex-1 bg-slate-100 rounded-full"></div>
                    </div>
                </div>

                <!-- Compliance Index Gauge -->
                <div class="col-span-12 md:col-span-4 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 h-[280px] flex flex-col">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest text-primary font-black">Regulatory Health</span>
                        <h3 class="font-headline text-xl font-bold mt-2">Compliance Index</h3>
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center relative group/metric">
                        <svg class="w-36 h-36 transform -rotate-90">
                            <circle class="text-slate-50" cx="72" cy="72" fill="transparent" r="64" stroke="currentColor" stroke-width="10"></circle>
                            <circle class="text-primary" cx="72" cy="72" fill="transparent" r="64" stroke="currentColor" stroke-dasharray="402" stroke-dashoffset="<?= 402 * (1 - $compliance_index/100) ?>" stroke-width="10" stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute flex flex-col items-center">
                            <span class="text-3xl font-headline font-black text-slate-900"><?= $compliance_index ?>%</span>
                            <span class="font-bold text-[8px] text-slate-400 tracking-widest uppercase mt-0.5">Audit Score</span>
                        </div>
                        <button onclick="openMetricEdit('hsse_compliance_index', '<?= $compliance_index ?>')" class="absolute top-0 right-0 p-2 opacity-0 group-hover/metric:opacity-100 transition-all text-primary hover:scale-110">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                    </div>
                </div>

                <!-- Upcoming Audits -->
                <div class="col-span-12 md:col-span-4 bg-slate-900 text-white p-6 rounded-[2rem] h-[280px] flex flex-col shadow-2xl">
                    <h3 class="font-headline text-xl font-bold mb-4">Upcoming Audits</h3>
                    <div id="auditContainer" class="space-y-3">
                        <?php if ($audits_res && $audits_res->num_rows > 0): 
                            while ($audit = $audits_res->fetch_assoc()):
                        ?>
                        <div class="flex items-center gap-4 bg-white/5 p-3.5 rounded-2xl border border-white/5 backdrop-blur-md hover:bg-white/10 transition-all group/audit">
                            <div class="w-9 h-9 bg-primary/20 rounded-xl flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-lg"><?= stripos($audit['type'], 'Fire') !== false ? 'fire_truck' : 'verified_user' ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold truncate"><?= htmlspecialchars($audit['title']) ?></p>
                                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest"><?= htmlspecialchars($audit['type']) ?></p>
                            </div>
                            <div class="text-right shrink-0 flex items-center gap-3">
                                <div>
                                    <p class="text-[9px] font-black text-primary uppercase"><?= date('M d', strtotime($audit['audit_date'])) ?></p>
                                </div>
                                <div class="flex gap-1 opacity-0 group-hover/audit:opacity-100 transition-opacity">
                                    <button onclick='openAuditModal(<?= json_encode($audit) ?>)' class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center hover:bg-primary hover:text-on-primary transition-all">
                                        <span class="material-symbols-outlined text-xs">edit</span>
                                    </button>
                                    <button onclick="deleteAudit(<?= $audit['id'] ?>)" class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-xs">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; else: ?>
                        <div class="text-center py-8 opacity-50">
                            <p class="text-[9px] font-black uppercase tracking-widest">No pending audits</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button onclick="openAuditModal()" class="mt-auto text-[9px] font-black text-primary flex items-center gap-1 hover:gap-2 transition-all uppercase tracking-widest">
                        SCHEDULE AUDIT <span class="material-symbols-outlined text-sm">add_circle</span>
                    </button>
                </div>

                <!-- Recent Observations -->
                <div class="col-span-12 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="font-headline text-2xl font-black text-slate-900">Recent Observations</h3>
                        <div class="flex gap-4 items-center">
                            <button onclick="openManageModal()" class="px-3 py-1 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-full hover:bg-primary hover:text-on-primary transition-all">MANAGE ALL</button>
                            <?php if ($critical_count > 0): ?>
                            <span class="px-3 py-1 bg-red-50 text-red-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-red-100">CRITICAL (<?= $critical_count ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php 
                        if ($observations_res && $observations_res->num_rows > 0):
                            while ($obs = $observations_res->fetch_assoc()):
                                $severity_bg = 'bg-slate-100 text-slate-500';
                                if ($obs['severity'] == 'High') $severity_bg = 'bg-red-50 text-red-600';
                                elseif ($obs['severity'] == 'Medium') $severity_bg = 'bg-amber-50 text-amber-600';
                        ?>
                        <div class="group flex items-center gap-4 p-5 rounded-3xl border border-slate-50 hover:border-primary/30 hover:bg-slate-50/50 transition-all">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 <?= $severity_bg ?>">
                                <span class="material-symbols-outlined"><?= $obs['severity'] == 'High' ? 'warning' : 'info' ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-sm font-extrabold text-slate-900 truncate"><?= htmlspecialchars($obs['title']) ?></span>
                                    <span class="text-[8px] font-black uppercase px-2 py-0.5 bg-white border border-slate-100 text-slate-400 rounded-md shrink-0"><?= htmlspecialchars($obs['type']) ?></span>
                                    <?php if($obs['status'] == 'Resolved'): ?>
                                    <span class="text-[8px] font-black uppercase px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-md shrink-0">RESOLVED</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($obs['description']) ?></p>
                            </div>
                            <div class="flex items-center gap-4 shrink-0">
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-slate-300 uppercase"><?= date('H:i', strtotime($obs['created_at'])) ?></p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <button onclick='openChatModal(<?= $obs['id'] ?>, "<?= addslashes($obs['title']) ?>")' class="w-7 h-7 bg-slate-50 text-slate-300 rounded-lg flex items-center justify-center hover:bg-primary/10 hover:text-primary transition-all">
                                            <span class="material-symbols-outlined text-sm">forum</span>
                                        </button>
                                        <?php if ($obs['inspector_avatar']): ?>
                                        <img alt="Inspector" class="w-6 h-6 rounded-lg border-2 border-white shadow-sm inline-block" src="../<?= htmlspecialchars($obs['inspector_avatar']) ?>"/>
                                        <?php else: ?>
                                        <div class="w-6 h-6 rounded-lg bg-slate-900 text-white text-[8px] inline-flex items-center justify-center font-bold border-2 border-white shadow-sm"><?= substr($obs['inspector_name'] ?: 'A', 0, 1) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($obs['status'] != 'Resolved' && ($permissions['role'] === 'Director' || $permissions['HSSE']['write'])): ?>
                                <button onclick="resolveObservation(<?= $obs['id'] ?>)" class="w-8 h-8 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 transition-all">
                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <div class="col-span-2 text-center py-12">
                            <span class="material-symbols-outlined text-4xl text-slate-200">shield_with_heart</span>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No observations logged today</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer Metrics & Milestones -->
            <div class="mt-12 grid grid-cols-12 gap-8 items-start">
                <div class="col-span-12 lg:col-span-8 grid grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                                <span class="material-symbols-outlined">warning</span>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">NEAR MISSES</p>
                                <p class="text-2xl font-headline font-black text-slate-900"><?= $near_misses ?></p>
                            </div>
                        </div>
                        <span class="text-[8px] font-black text-emerald-500 uppercase tracking-tighter bg-emerald-50 px-2 py-1 rounded-md">LIVE LOG</span>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center">
                                <span class="material-symbols-outlined">security</span>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">BREACHES</p>
                                <p class="text-2xl font-headline font-black text-slate-900"><?= $breaches ?></p>
                            </div>
                        </div>
                        <span class="text-[8px] font-black text-red-500 uppercase tracking-tighter bg-red-50 px-2 py-1 rounded-md">CRITICAL</span>
                    </div>
                </div>

                <!-- Milestone History -->
                <div class="col-span-12 lg:col-span-4 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">history</span> Milestone Archive
                    </h4>
                    <div class="space-y-4">
                        <?php if ($milestones_res && $milestones_res->num_rows > 0): 
                            while($ms = $milestones_res->fetch_assoc()):
                        ?>
                        <div class="flex items-center justify-between border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                            <div>
                                <p class="text-[11px] font-extrabold text-slate-900 leading-tight"><?= htmlspecialchars($ms['reason']) ?></p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter"><?= date('M d, Y', strtotime($ms['reset_date'])) ?></p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black text-red-500"><?= $ms['safe_days'] ?>d</span>
                                <p class="text-[7px] font-bold text-slate-300 uppercase tracking-widest">LAST RECORD</p>
                            </div>
                        </div>
                        <?php endwhile; else: ?>
                        <div class="text-center py-6">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">No historical resets logged</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Observation Modal -->
<div id="observationModal" class="modal-overlay">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
            <div>
                <h3 class="font-headline font-extrabold text-xl text-slate-900">Log Observation</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Safety Protocol 4.02 • Sector Alpha</p>
            </div>
            <button onclick="closeObservationModal()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm hover:text-slate-900 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="overflow-y-auto p-8 custom-scrollbar">
            <form id="observationForm" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Observation Type</label>
                        <select name="type" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Routine">Routine Check</option>
                            <option value="Hazard">Hazard Detection</option>
                            <option value="Incident">Near Miss / Incident</option>
                            <option value="Audit">Safety Audit</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Severity Level</label>
                        <select name="severity" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Low">Low (Routine)</option>
                            <option value="Medium">Medium (Attention Required)</option>
                            <option value="High">High (Immediate Action)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Observation Title</label>
                    <input type="text" name="title" required placeholder="e.g., Unsecured scaffolding in Zone 4" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Location / Zone</label>
                        <input type="text" name="location" placeholder="e.g., Sector 7G" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Linked Project</label>
                        <select name="project_id" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="">-- General Site --</option>
                            <?php while($p = $projects_res->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Description / Findings</label>
                    <textarea name="description" required rows="4" placeholder="Detail the specific sequence of events or findings..." class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary custom-scrollbar"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-50 flex justify-end gap-3">
                    <button type="button" onclick="closeObservationModal()" class="px-8 py-3 text-slate-400 font-bold text-xs uppercase tracking-widest hover:text-slate-900 transition-colors">Cancel</button>
                    <button type="submit" id="submitBtn" class="px-10 py-3 bg-primary text-on-primary font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20 active:scale-95 transition-all">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Audit Modal -->
<div id="auditModal" class="modal-overlay">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 id="auditModalTitle" class="font-headline font-black text-xl text-slate-900">Schedule Audit</h3>
            <button onclick="closeAuditModal()" class="text-slate-400 hover:text-slate-900"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form id="auditForm" class="space-y-4">
            <input type="hidden" name="id" id="audit_id">
            <div class="space-y-1">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Audit Title</label>
                <input type="text" name="title" id="audit_title" required class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Type</label>
                    <input type="text" name="type" id="audit_type" placeholder="e.g. Safety" required class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Date</label>
                    <input type="date" name="audit_date" id="audit_date" required class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Location</label>
                <input type="text" name="location" id="audit_location" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold">
            </div>
            <button type="submit" id="auditSubmitBtn" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest mt-4">CONFIRM SCHEDULE</button>
        </form>
    </div>
</div>

<!-- Chat Modal -->
<div id="chatModal" class="modal-overlay">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col h-[600px] border border-slate-100">
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center shrink-0">
            <div>
                <h3 id="chatTitle" class="font-headline font-black text-lg text-slate-900 truncate max-w-[300px]">Observation Logs</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Real-time Administrative Feed</p>
            </div>
            <button onclick="closeChatModal()" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="chatBody" class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-slate-50/30 flex flex-col gap-4">
            <!-- Replies go here -->
        </div>
        <div class="p-6 bg-white border-t border-slate-100 shrink-0">
            <form id="chatForm" class="flex gap-3">
                <input type="hidden" id="chat_obs_id" name="observation_id">
                <input type="text" name="message" required placeholder="Type a response or directive..." class="flex-1 bg-slate-50 border-slate-100 rounded-2xl px-5 py-3 text-xs font-bold focus:ring-1 focus:ring-primary outline-none">
                <button type="submit" class="w-12 h-12 bg-primary text-on-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                    <span class="material-symbols-outlined">send</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Metric Edit Modal -->
<div id="metricModal" class="modal-overlay">
    <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-md shadow-2xl relative overflow-hidden border border-slate-100">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">settings_suggest</span>
                </div>
                <div>
                    <h3 class="font-headline text-xl font-black text-slate-900">Update Metric</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Adjust Live Performance Data</p>
                </div>
            </div>
            
            <form method="POST">
                <input type="hidden" name="update_metric" value="1">
                <input type="hidden" id="modal_metric_key" name="metric_key">
                <div class="space-y-6">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">New Value</label>
                        <input type="text" id="modal_metric_value" name="metric_value" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-1 focus:ring-primary outline-none">
                    </div>
                    
                    <div class="flex gap-4 pt-4">
                        <button type="button" onclick="closeMetricModal()" class="flex-1 py-4 bg-slate-50 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-100 transition-all">CANCEL</button>
                        <button type="submit" class="flex-1 py-4 bg-primary text-on-primary rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 active:scale-95 transition-all">SAVE CHANGES</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Manage Observations Modal -->
<div id="manageModal" class="modal-overlay">
    <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden flex flex-col h-[80vh]">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="font-headline font-black text-2xl text-slate-900">Observation Management</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">System Audit & Lifecycle Control</p>
            </div>
            <button onclick="closeManageModal()" class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <table class="w-full">
                <thead>
                    <tr class="text-left">
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 pl-4">Title / Inspector</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4">Severity</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4">Status</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4">Date</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-right pr-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if ($all_observations_res && $all_observations_res->num_rows > 0): 
                        while($row = $all_observations_res->fetch_assoc()):
                            $sev_color = $row['severity'] == 'High' ? 'text-red-600 bg-red-50' : ($row['severity'] == 'Medium' ? 'text-amber-600 bg-amber-50' : 'text-slate-600 bg-slate-50');
                            $stat_color = $row['status'] == 'Resolved' ? 'text-emerald-600 bg-emerald-50' : 'text-blue-600 bg-blue-50';
                    ?>
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="py-4 pl-4">
                            <p class="text-xs font-bold text-slate-900"><?= htmlspecialchars($row['title']) ?></p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">BY <?= htmlspecialchars($row['inspector_name'] ?: 'SYSTEM') ?></p>
                        </td>
                        <td class="py-4">
                            <span class="text-[8px] font-black px-2 py-0.5 rounded-md uppercase <?= $sev_color ?>"><?= $row['severity'] ?></span>
                        </td>
                        <td class="py-4">
                            <span class="text-[8px] font-black px-2 py-0.5 rounded-md uppercase <?= $stat_color ?>"><?= $row['status'] ?></span>
                        </td>
                        <td class="py-4">
                            <p class="text-[10px] font-bold text-slate-400 uppercase"><?= date('M d, Y', strtotime($row['created_at'])) ?></p>
                        </td>
                        <td class="py-4 text-right pr-4">
                            <div class="flex justify-end gap-2">
                                <?php if($row['status'] != 'Resolved'): ?>
                                <button onclick="resolveObservation(<?= $row['id'] ?>)" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openManageModal() { document.getElementById('manageModal').classList.add('open'); }
    function closeManageModal() { document.getElementById('manageModal').classList.remove('open'); }

    function openMetricEdit(key, currentVal) {
        document.getElementById('modal_metric_key').value = key;
        document.getElementById('modal_metric_value').value = currentVal;
        document.getElementById('metricModal').classList.add('open');
    }
    
    function closeMetricModal() {
        document.getElementById('metricModal').classList.remove('open');
    }

    function resolveObservation(id) {
        if(!confirm('Mark this observation as resolved?')) return;
        const formData = new FormData();
        formData.append('id', id);
        fetch('hsse.php?ajax_action=resolve_observation', { method: 'POST', body: formData })
        .then(r => r.json()).then(data => {
            if(data.status === 'success') window.location.reload();
            else alert(data.message);
        });
    }

    function openObservationModal() {
        document.getElementById('observationModal').classList.add('open');
    }
    function closeObservationModal() {
        document.getElementById('observationModal').classList.remove('open');
    }

    function openAuditModal(data = null) {
        const modal = document.getElementById('auditModal');
        const title = document.getElementById('auditModalTitle');
        const btn = document.getElementById('auditSubmitBtn');
        const form = document.getElementById('auditForm');
        
        form.reset();
        if (data) {
            title.innerText = 'Edit Audit';
            btn.innerText = 'UPDATE AUDIT';
            document.getElementById('audit_id').value = data.id;
            document.getElementById('audit_title').value = data.title;
            document.getElementById('audit_type').value = data.type;
            document.getElementById('audit_date').value = data.audit_date;
            document.getElementById('audit_location').value = data.location;
        } else {
            title.innerText = 'Schedule Audit';
            btn.innerText = 'CONFIRM SCHEDULE';
            document.getElementById('audit_id').value = '';
        }
        modal.classList.add('open');
    }
    
    function closeAuditModal() { document.getElementById('auditModal').classList.remove('open'); }

    function deleteAudit(id) {
        if (!confirm('Are you sure you want to delete this audit?')) return;
        fetch('hsse.php?ajax_action=delete_audit', {
            method: 'POST',
            body: new URLSearchParams({'id': id})
        }).then(r => r.json()).then(data => {
            if (data.status === 'success') window.location.reload();
            else alert(data.message);
        });
    }

    document.getElementById('auditForm').onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById('auditSubmitBtn');
        btn.innerText = 'PROCESSING...';
        btn.disabled = true;
        fetch('hsse.php?ajax_action=submit_audit', { method: 'POST', body: new FormData(this) })
        .then(r => r.json()).then(data => {
            if(data.status === 'success') window.location.reload();
            else { alert(data.message); btn.innerText = 'CONFIRM SCHEDULE'; btn.disabled = false; }
        });
    };

    let chatInterval;
    function openChatModal(id, title) {
        document.getElementById('chatTitle').innerText = title;
        document.getElementById('chat_obs_id').value = id;
        document.getElementById('chatModal').classList.add('open');
        loadChat(id);
        chatInterval = setInterval(() => loadChat(id), 3000);
    }

    function closeChatModal() {
        document.getElementById('chatModal').classList.remove('open');
        clearInterval(chatInterval);
    }

    function loadChat(obs_id) {
        fetch(`hsse.php?ajax_action=get_observation_replies&observation_id=${obs_id}`)
        .then(r => r.json()).then(data => {
            const body = document.getElementById('chatBody');
            let html = '';
            data.forEach(r => {
                const isMe = r.admin_id == <?= $admin_id ?>;
                html += `
                    <div class="flex flex-col ${isMe ? 'items-end' : 'items-start'}">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">${new Date(r.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            <span class="text-[10px] font-bold text-slate-900">${r.admin_name}</span>
                        </div>
                        <div class="${isMe ? 'bg-primary text-on-primary rounded-tr-none' : 'bg-white text-slate-600 rounded-tl-none border border-slate-100'} rounded-2xl px-4 py-2.5 max-w-[85%] shadow-sm">
                            <p class="text-xs font-medium leading-relaxed">${r.message}</p>
                        </div>
                    </div>
                `;
            });
            const oldHtml = body.innerHTML;
            body.innerHTML = html;
            if (oldHtml !== html) body.scrollTop = body.scrollHeight;
        });
    }

    document.getElementById('chatForm').onsubmit = function(e) {
        e.preventDefault();
        const form = this;
        const obs_id = document.getElementById('chat_obs_id').value;
        fetch('hsse.php?ajax_action=add_observation_reply', {
            method: 'POST',
            body: new FormData(this)
        }).then(r => r.json()).then(data => {
            if (data.status === 'success') {
                form.reset();
                loadChat(obs_id);
            }
        });
    };

    document.getElementById('observationForm').onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const originalText = btn.innerText;
        btn.innerText = 'PROCESSING...';
        btn.disabled = true;

        const formData = new FormData(this);
        fetch('hsse.php?ajax_action=submit_observation', {
            method: 'POST',
            body: formData
        })
        .then(r => {
            if(!r.ok) throw new Error('Server Error');
            return r.json();
        })
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.innerText = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            alert('Submission failed. Check database connection.');
            btn.innerText = originalText;
            btn.disabled = false;
        });
    };
</script>

</body>
</html>