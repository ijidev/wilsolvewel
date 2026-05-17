<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: login.php");
    exit();
}
$conn = get_db_connection();

// Ensure client_id column exists on hsse_observation_replies
ensure_column_exists($conn, 'hsse_observation_replies', 'client_id', 'INT(11) NULL AFTER admin_id');

// ── AJAX Handlers ─────────────────────────────────────────────────────────────

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] === 'submit_observation') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
            exit;
        }
        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? 'Routine';
        $severity = $_POST['severity'] ?? 'Low';
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $project_id = $_POST['project_id'] ? (int)$_POST['project_id'] : null;

        if (empty($title) || empty($description)) {
            echo json_encode(['status' => 'error', 'message' => 'Title and description are required.']);
            exit;
        }

        // Verify project belongs to client if project_id is set
        if ($project_id) {
            $chk = safe_query($conn, "SELECT id FROM projects WHERE id = ? AND client_id = ?", "ii", [$project_id, $client_id]);
            if (!$chk || $chk->num_rows === 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid project.']);
                exit;
            }
        }

        $stmt = $conn->prepare("INSERT INTO hsse_observations (title, type, severity, location, description, status, project_id) VALUES (?, ?, ?, ?, ?, 'Open', ?)");
        if ($stmt) {
            $stmt->bind_param("sssssi", $title, $type, $severity, $location, $description, $project_id);
            $ok = $stmt->execute();
            $stmt->close();
            echo json_encode($ok ? ['status' => 'success'] : ['status' => 'error', 'message' => 'DB error']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'DB prepare failed']);
        }
        exit;
    }

    if ($_GET['ajax_action'] === 'get_observation_replies') {
        $obs_id = (int)$_GET['observation_id'];
        $stmt = $conn->prepare("
            SELECT r.*,
                a.name as admin_name, a.avatar as admin_avatar,
                c.name as client_name
            FROM hsse_observation_replies r
            LEFT JOIN admins a ON r.admin_id IS NOT NULL AND r.admin_id = a.id
            LEFT JOIN clients c ON r.client_id IS NOT NULL AND r.client_id = c.id
            WHERE r.observation_id = ?
            ORDER BY r.created_at ASC
        ");
        $replies = [];
        if ($stmt) {
            $stmt->bind_param("i", $obs_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) $replies[] = $row;
            $stmt->close();
        }
        echo json_encode($replies);
        exit;
    }

    if ($_GET['ajax_action'] === 'add_observation_reply') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
            exit;
        }
        $obs_id = (int)$_POST['observation_id'];
        $message = trim($_POST['message'] ?? '');
        if (!empty($message)) {
            $stmt = $conn->prepare("INSERT INTO hsse_observation_replies (observation_id, client_id, message) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iis", $obs_id, $client_id, $message);
                $ok = $stmt->execute();
                $stmt->close();
                echo json_encode($ok ? ['status' => 'success'] : ['status' => 'error', 'message' => 'DB error']);
                exit;
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Message empty']);
        exit;
    }
}

// ── Fetch Data ────────────────────────────────────────────────────────────────

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

// Observations visible to this client (their projects + general site)
$observations_res = safe_query($conn, "
    SELECT o.*, COALESCE(p.name, 'General Site') as project_name
    FROM hsse_observations o
    LEFT JOIN projects p ON o.project_id = p.id
    WHERE (p.client_id = ? OR o.project_id IS NULL)
    ORDER BY o.created_at DESC
    LIMIT 20
", "i", [$client_id]);

// Client's projects (for observation form dropdown)
$projects_res = safe_query($conn, "SELECT id, name FROM projects WHERE client_id = ? ORDER BY name ASC", "i", [$client_id]);

// Upcoming audits (replaces static toolbox talks)
$audits_res = safe_query($conn, "SELECT * FROM hsse_audits WHERE status = 'Upcoming' ORDER BY audit_date ASC LIMIT 5");

// Stats for sidebar
$client_total = safe_query($conn, "SELECT COUNT(*) as c FROM hsse_observations o LEFT JOIN projects p ON o.project_id = p.id WHERE (p.client_id = ? OR o.project_id IS NULL)", "i", [$client_id])->fetch_assoc()['c'];
$client_open = safe_query($conn, "SELECT COUNT(*) as c FROM hsse_observations o LEFT JOIN projects p ON o.project_id = p.id WHERE (p.client_id = ? OR o.project_id IS NULL) AND o.status != 'Resolved'", "i", [$client_id])->fetch_assoc()['c'];

// ── Layout Vars ───────────────────────────────────────────────────────────────
$page_title = 'HSSE Operations | Wilsolvewel';
$page_h1 = 'HSSE Operations';
$page_h1_sub = 'Real-time health, safety, security, and environmental monitoring for your active assets and terminal projects.';
$page_h1_badge = $safe_days > 0 ? "{$safe_days} Days Safe • System Secure" : 'System Status: Secure';
$page_h1_action = '<button onclick="openObservationModal()" class="bg-primary text-on-primary px-4 sm:px-6 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-widest flex items-center gap-2 shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all"><span class="material-symbols-outlined text-sm">add_circle</span> <span class="hidden sm:inline">LOG OBSERVATION</span></button>';
ob_start();
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- Main Column -->
    <div class="lg:col-span-8 space-y-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1">
                    Safe Days
                    <span class="relative group/tip">
                        <span class="material-symbols-outlined text-[14px] text-slate-300 cursor-help">help</span>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-52 p-2.5 bg-slate-900 text-white text-[8px] font-bold leading-relaxed rounded-lg shadow-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-20 text-left normal-case tracking-normal">
                            Days since the last High-severity incident. Resets when a critical event occurs.
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                        </div>
                    </span>
                </span>
                <p class="text-2xl font-headline font-black text-primary mt-1"><?= $safe_days ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1">
                    Compliance
                    <span class="relative group/tip">
                        <span class="material-symbols-outlined text-[14px] text-slate-300 cursor-help">help</span>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-52 p-2.5 bg-slate-900 text-white text-[8px] font-bold leading-relaxed rounded-lg shadow-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-20 text-left normal-case tracking-normal">
                            Percentage of safety observations that have been resolved. Higher values indicate better safety culture.
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                        </div>
                    </span>
                </span>
                <p class="text-2xl font-headline font-black text-slate-900 mt-1"><?= $compliance_index ?>%</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1">
                    Open
                    <span class="relative group/tip">
                        <span class="material-symbols-outlined text-[14px] text-slate-300 cursor-help">help</span>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-52 p-2.5 bg-slate-900 text-white text-[8px] font-bold leading-relaxed rounded-lg shadow-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-20 text-left normal-case tracking-normal">
                            Observations still under review that have not yet been marked as resolved.
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                        </div>
                    </span>
                </span>
                <p class="text-2xl font-headline font-black text-amber-500 mt-1"><?= $client_open ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1">
                    Total
                    <span class="relative group/tip">
                        <span class="material-symbols-outlined text-[14px] text-slate-300 cursor-help">help</span>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-52 p-2.5 bg-slate-900 text-white text-[8px] font-bold leading-relaxed rounded-lg shadow-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-20 text-left normal-case tracking-normal">
                            All safety observations logged across your projects and general site areas.
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                        </div>
                    </span>
                </span>
                <p class="text-2xl font-headline font-black text-slate-900 mt-1"><?= $client_total ?></p>
            </div>
        </div>

        <!-- Observations Stream -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-headline font-bold text-slate-900 flex items-center gap-1.5">Hazard & Incident Stream
                    <span class="relative group/tip">
                        <span class="material-symbols-outlined text-[16px] text-slate-300 cursor-help">help</span>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 p-2.5 bg-slate-900 text-white text-[8px] font-bold leading-relaxed rounded-lg shadow-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-20 text-left normal-case tracking-normal">
                            Live feed of all safety observations, hazards, and incidents reported for your projects and general site areas.
                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                        </div>
                    </span>
                </h3>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Live Feed</span>
            </div>
            <div class="divide-y divide-slate-50">
                <?php if ($observations_res && $observations_res->num_rows > 0):
                    while ($obs = $observations_res->fetch_assoc()):
                        $sev_class = $obs['severity'] == 'High' ? 'bg-red-50 border-red-100 text-red-600' : ($obs['severity'] == 'Medium' ? 'bg-amber-50 border-amber-100 text-amber-600' : 'bg-slate-50 border-slate-100 text-slate-500');
                ?>
                <div class="p-5 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 <?= $sev_class ?> border">
                            <span class="material-symbols-outlined text-lg"><?= $obs['severity'] == 'High' ? 'warning' : 'info' ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="font-bold text-sm text-slate-900 truncate"><?= htmlspecialchars($obs['title']) ?></span>
                                <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 shrink-0"><?= htmlspecialchars($obs['type']) ?></span>
                                <?php if ($obs['status'] == 'Resolved'): ?>
                                <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 shrink-0">Resolved</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2"><?= htmlspecialchars($obs['description']) ?></p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[8px] font-bold text-primary uppercase tracking-widest"><?= htmlspecialchars($obs['project_name']) ?></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest"><?= date('M d, H:i', strtotime($obs['created_at'])) ?></span>
                                <button onclick="openChatModal(<?= $obs['id'] ?>, '<?= addslashes($obs['title']) ?>')" class="ml-auto text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">forum</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-4xl text-slate-200">check_circle</span>
                    <p class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest">No observations recorded</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Emergency Hub -->
        <div class="bg-slate-900 text-white p-6 rounded-2xl relative overflow-hidden shadow-xl">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <span class="material-symbols-outlined text-primary text-3xl mb-3">emergency</span>
                <h3 class="font-headline font-black text-xl mb-2">Emergency Hub</h3>
                <p class="text-xs text-slate-400 mb-6 leading-relaxed">Direct connection to terminal emergency services and automated containment protocols.</p>
                <button class="w-full py-3.5 border-2 border-red-500/50 text-red-500 font-headline font-black text-xs uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">emergency</span>
                    INITIATE SIGNAL
                </button>
            </div>
        </div>

        <!-- Upcoming Audits -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h4 class="font-headline text-[9px] font-black tracking-widest text-primary mb-4 uppercase flex items-center gap-1.5">Upcoming Audits & Talks
                <span class="relative group/tip">
                    <span class="material-symbols-outlined text-[14px] text-primary/50 cursor-help">help</span>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 p-2.5 bg-slate-900 text-white text-[8px] font-bold leading-relaxed rounded-lg shadow-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-20 text-left normal-case tracking-normal">
                        Scheduled safety audits and toolbox talks — formal assessments of safety protocols and procedures.
                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
                    </div>
                </span>
            </h4>
            <?php if ($audits_res && $audits_res->num_rows > 0):
                while ($audit = $audits_res->fetch_assoc()):
            ?>
            <div class="flex gap-4 group mb-4 last:mb-0">
                <div class="flex-none w-10 h-10 bg-slate-50 border border-slate-100 rounded-xl flex flex-col items-center justify-center">
                    <span class="text-[7px] font-black text-slate-400 uppercase"><?= date('M', strtotime($audit['audit_date'])) ?></span>
                    <span class="text-sm font-black text-slate-900 leading-tight"><?= date('d', strtotime($audit['audit_date'])) ?></span>
                </div>
                <div class="min-w-0">
                    <p class="font-headline font-bold text-xs text-slate-900 truncate"><?= htmlspecialchars($audit['title']) ?></p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate"><?= htmlspecialchars($audit['type']) ?> • <?= htmlspecialchars($audit['location'] ?: 'TBD') ?></p>
                </div>
            </div>
            <?php endwhile; else: ?>
            <p class="text-xs text-slate-400 italic text-center py-4">No upcoming audits scheduled</p>
            <?php endif; ?>
        </div>

        <!-- Milestone Card -->
        <div class="bg-gradient-to-br from-primary to-amber-600 p-6 rounded-2xl text-on-primary shadow-lg">
            <div class="flex items-center justify-between">
                <span class="material-symbols-outlined text-3xl mb-2">verified</span>
                <span class="relative group/tip self-start">
                    <span class="material-symbols-outlined text-lg text-white/50 cursor-help">help</span>
                    <div class="absolute bottom-full right-0 mb-2 w-52 p-2.5 bg-slate-900 text-white text-[8px] font-bold leading-relaxed rounded-lg shadow-lg opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-20 text-left normal-case tracking-normal">
                        Same as Safe Days — the current incidence-free period measured in consecutive days without a critical event.
                        <div class="absolute top-full right-4 border-4 border-transparent border-t-slate-900"></div>
                    </div>
                </span>
            </div>
            <p class="text-[9px] font-black uppercase tracking-widest opacity-80">Current Streak</p>
            <p class="text-3xl font-headline font-black mt-1"><?= $safe_days ?> <span class="text-sm font-bold opacity-80">days</span></p>
            <p class="text-[10px] font-bold mt-2 opacity-80">Incidence-Free Period</p>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();

// ── Modals & Scripts ──────────────────────────────────────────────────────────
$page_after_main = '
<!-- Observation Modal -->
<div id="observationModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center shrink-0">
            <div>
                <h3 class="font-headline font-bold text-lg text-slate-900">Log Observation</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Report a safety finding</p>
            </div>
            <button onclick="closeObservationModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <div class="overflow-y-auto p-6 custom-scrollbar">
            <form id="observationForm" class="space-y-5">
                ' . get_csrf_field() . '
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Type</label>
                        <select name="type" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Routine">Routine</option>
                            <option value="Hazard">Hazard</option>
                            <option value="Incident">Incident</option>
                            <option value="Audit">Audit</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Severity</label>
                        <select name="severity" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Title</label>
                    <input type="text" name="title" required placeholder="e.g. Unsecured railing" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Location</label>
                        <input type="text" name="location" placeholder="e.g. Zone 4" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Project</label>
                        <select name="project_id" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="">General Site</option>
                            ' . implode('', array_map(function($p) {
                                return '<option value="' . $p['id'] . '">' . htmlspecialchars($p['name']) . '</option>';
                            }, ($projects_res && $projects_res->num_rows > 0 ? $projects_res->fetch_all(MYSQLI_ASSOC) : []))) . '
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Description</label>
                    <textarea name="description" required rows="4" placeholder="Detail your findings..." class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-1 focus:ring-primary custom-scrollbar resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeObservationModal()" class="px-6 py-2.5 text-slate-400 font-bold text-xs uppercase tracking-widest hover:text-slate-900 transition-colors">Cancel</button>
                    <button type="submit" id="obsSubmitBtn" class="px-8 py-2.5 bg-primary text-on-primary font-bold text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chat Modal -->
<div id="chatModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] border border-slate-100">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center shrink-0">
            <div class="min-w-0">
                <h3 id="chatTitle" class="font-headline font-bold text-slate-900 truncate">Observation Chat</h3>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Discussion thread</p>
            </div>
            <button onclick="closeChatModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors shrink-0">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <div id="chatBody" class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar bg-slate-50/30"></div>
        <div class="p-5 border-t border-slate-100 bg-white shrink-0">
            <form id="chatForm" class="flex gap-3">
                <input type="hidden" id="chatObsId" name="observation_id">
                ' . get_csrf_field() . '
                <input type="text" name="message" required placeholder="Type a message..." class="flex-1 bg-slate-50 border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-1 focus:ring-primary outline-none">
                <button type="submit" class="w-10 h-10 bg-primary text-on-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all shrink-0">
                    <span class="material-symbols-outlined text-sm">send</span>
                </button>
            </form>
        </div>
    </div>
</div>
';

$page_scripts = '
<script>
    let currentObsId = null;
    let chatPoll = null;

    function openObservationModal() {
        document.getElementById("observationModal").classList.remove("hidden");
        document.getElementById("observationModal").classList.add("flex");
    }
    function closeObservationModal() {
        document.getElementById("observationModal").classList.remove("flex");
        document.getElementById("observationModal").classList.add("hidden");
    }

    document.getElementById("observationForm").onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById("obsSubmitBtn");
        btn.disabled = true;
        btn.innerText = "SUBMITTING...";
        fetch("?ajax_action=submit_observation", { method: "POST", body: new FormData(this) })
        .then(r => r.json())
        .then(data => {
            if (data.status === "success") window.location.reload();
            else { alert(data.message); btn.disabled = false; btn.innerText = "SUBMIT"; }
        })
        .catch(() => { btn.disabled = false; btn.innerText = "SUBMIT"; });
    };

    function openChatModal(id, title) {
        currentObsId = id;
        document.getElementById("chatObsId").value = id;
        document.getElementById("chatTitle").innerText = title;
        document.getElementById("chatModal").classList.remove("hidden");
        document.getElementById("chatModal").classList.add("flex");
        loadChat(id);
        if (chatPoll) clearInterval(chatPoll);
        chatPoll = setInterval(() => loadChat(id), 5000);
    }

    function closeChatModal() {
        document.getElementById("chatModal").classList.remove("flex");
        document.getElementById("chatModal").classList.add("hidden");
        if (chatPoll) { clearInterval(chatPoll); chatPoll = null; }
        currentObsId = null;
    }

    function loadChat(id) {
        fetch("?ajax_action=get_observation_replies&observation_id=" + id)
        .then(r => r.json())
        .then(data => {
            const body = document.getElementById("chatBody");
            let html = "";
            data.forEach(r => {
                const isClient = r.client_id !== null && r.client_id !== undefined;
                const senderName = isClient ? "You" : r.admin_name;
                const isMe = isClient || false;
                html += `<div class="flex flex-col ${isClient ? "items-end" : "items-start"}">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">${new Date(r.created_at).toLocaleTimeString([], {hour: "2-digit", minute: "2-digit"})}</span>
                        <span class="text-[10px] font-bold text-slate-900">${senderName}</span>
                    </div>
                    <div class="${isClient ? "bg-primary text-on-primary rounded-tr-none" : "bg-white text-slate-600 rounded-tl-none border border-slate-100"} rounded-xl px-4 py-2.5 max-w-[85%] shadow-sm">
                        <p class="text-xs font-medium leading-relaxed">${r.message}</p>
                    </div>
                </div>`;
            });
            const old = body.innerHTML;
            body.innerHTML = html || `<div class="text-center py-8 text-slate-400 text-xs">No discussion yet.</div>`;
            if (old !== html) body.scrollTop = body.scrollHeight;
        });
    }

    document.getElementById("chatForm").onsubmit = function(e) {
        e.preventDefault();
        const obsId = document.getElementById("chatObsId").value;
        if (!obsId) return;
        fetch("?ajax_action=add_observation_reply", { method: "POST", body: new FormData(this) })
        .then(r => r.json())
        .then(data => {
            if (data.status === "success") {
                this.reset();
                loadChat(obsId);
            }
        });
    };
</script>
';

require_once __DIR__ . '/../components/client_layout.php';
