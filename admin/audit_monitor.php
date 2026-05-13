<?php
include '../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

// Only Main Admin (ID 1) or specific role should ideally see this, but we'll use RBAC.
$permissions = get_admin_permissions($admin_id);

// Check if Admin has read/write permission (e.g., Director)
$can_view_details = ($permissions['role'] === 'Director' || isset($permissions['manage_audit']));

if (isset($_GET['ajax_action']) && $_GET['ajax_action'] == 'get_details') {
    header('Content-Type: application/json');
    if (!$can_view_details) {
        echo json_encode(['status' => 'error', 'message' => 'Permission denied. Read & Write access required.']); exit;
    }
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT al.*, a.name as admin_name FROM audit_logs al LEFT JOIN admins a ON (al.actor_type = 'Admin' AND al.actor_id = a.id) WHERE al.id = $id");
    if ($row = $res->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Log not found.']);
    }
    exit;
}

$logs = [];
// Join with admins to get actor names
$res = $conn->query("
    SELECT al.*, a.name as admin_name 
    FROM audit_logs al 
    LEFT JOIN admins a ON (al.actor_type = 'Admin' AND al.actor_id = a.id)
    ORDER BY al.created_at DESC 
    LIMIT 200
");
while ($row = $res->fetch_assoc()) $logs[] = $row;

// Helper for module styling
function getModuleIcon($module) {
    switch ($module) {
        case 'Project': return ['folder_special', 'bg-blue-50 text-blue-600'];
        case 'Asset': return ['inventory_2', 'bg-amber-50 text-amber-600'];
        case 'Ticket': return ['forum', 'bg-purple-50 text-purple-600'];
        case 'Staff': return ['badge', 'bg-emerald-50 text-emerald-600'];
        case 'Client': return ['person', 'bg-indigo-50 text-indigo-600'];
        case 'Procurement': return ['local_shipping', 'bg-orange-50 text-orange-600'];
        case 'Profile': return ['account_circle', 'bg-slate-100 text-slate-600'];
        default: return ['history', 'bg-slate-100 text-slate-500'];
    }
}
function getActionColor($action) {
    if ($action === 'Create') return 'text-emerald-500';
    if ($action === 'Update') return 'text-amber-500';
    if ($action === 'Delete') return 'text-red-500';
    return 'text-slate-500';
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Audit Monitor | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(6px);z-index:200;display:none;align-items:center;justify-content:center;padding:1rem}
        .modal-overlay.open{display:flex}
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">

<script src="../components/admin_sidenav.js" data-root="../"></script>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Global Audit Monitor</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">System Activity & Event Logs</p>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-10">
        <div class="max-w-4xl mx-auto relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
            
            <?php if (empty($logs)): ?>
                <div class="text-center py-20 relative z-10"><span class="material-symbols-outlined text-5xl text-slate-200">history_toggle_off</span><p class="text-xs font-bold text-slate-400 mt-3 uppercase tracking-widest">No Activity Logged</p></div>
            <?php endif; ?>

            <?php foreach ($logs as $log): 
                list($icon, $bg) = getModuleIcon($log['module']);
                $actor = $log['actor_type'] === 'System' ? 'System Process' : ($log['admin_name'] ?: 'Unknown Admin');
            ?>
                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group mb-8">
                    <!-- Timeline Dot -->
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-[#F8FAFC] <?php echo $bg; ?> shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                        <span class="material-symbols-outlined text-lg"><?php echo $icon; ?></span>
                    </div>
                    
                    <!-- Card -->
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[9px] font-bold uppercase tracking-widest <?php echo getActionColor($log['action_type']); ?>"><?php echo htmlspecialchars($log['action_type']); ?></span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?php echo date('M j, Y h:i A', strtotime($log['created_at'])); ?></span>
                        </div>
                        <p class="text-sm font-bold text-slate-900 leading-tight mb-2"><?php echo htmlspecialchars($log['description']); ?></p>
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px] text-slate-400">person</span>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo htmlspecialchars($actor); ?></span>
                            </div>
                            <?php if ($can_view_details): ?>
                            <button onclick="viewDetails(<?php echo $log['id']; ?>)" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:bg-primary hover:text-on-primary transition-colors flex items-center justify-center shadow-sm" title="View Details">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </main>
</div>

<!-- Details Modal -->
<div id="detailModal" class="modal-overlay">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden flex flex-col">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="font-bold text-xl text-slate-900 font-headline">Audit Record</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">System Event Details</p>
            </div>
            <button onclick="closeDetails()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="p-8 space-y-6">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Action Overview</p>
                <div class="bg-slate-50 rounded-2xl p-4">
                    <p id="detDesc" class="text-sm font-bold text-slate-900 leading-relaxed"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Module</p>
                    <p id="detModule" class="text-xs font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Actor</p>
                    <p id="detActor" class="text-xs font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Timestamp</p>
                    <p id="detTime" class="text-xs font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Action</p>
                    <p id="detAction" class="text-xs font-bold text-slate-700"></p>
                </div>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Extended JSON Details</p>
                <pre id="detJson" class="bg-slate-900 text-slate-300 text-[10px] p-4 rounded-2xl overflow-x-auto custom-scrollbar font-mono"></pre>
            </div>
        </div>
    </div>
</div>

<script>
async function viewDetails(id) {
    try {
        const res = await fetch(`?ajax_action=get_details&id=${id}`);
        const result = await res.json();
        if (result.status === 'success') {
            const data = result.data;
            document.getElementById('detDesc').innerText = data.description;
            document.getElementById('detModule').innerText = data.module;
            document.getElementById('detActor').innerText = data.actor_type === 'System' ? 'System Process' : (data.admin_name || 'Unknown Admin');
            document.getElementById('detTime').innerText = data.created_at;
            document.getElementById('detAction').innerText = data.action_type;
            
            try {
                const json = JSON.parse(data.details);
                document.getElementById('detJson').innerText = JSON.stringify(json, null, 2) || 'No extended details.';
            } catch(e) {
                document.getElementById('detJson').innerText = 'No extended details.';
            }
            
            document.getElementById('detailModal').classList.add('open');
        } else {
            alert(result.message);
        }
    } catch(err) {
        alert("Network error.");
    }
}
function closeDetails() { document.getElementById('detailModal').classList.remove('open'); }
</script>

</body>
</html>
