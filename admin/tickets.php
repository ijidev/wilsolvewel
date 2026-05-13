<?php
include '../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'update_assignment') {
        $ticket_id = (int)$_POST['ticket_id'];
        $dept_id = (int)($_POST['department_id'] ?? 0);
        $assigned_to = (int)($_POST['assigned_admin_id'] ?? 0);
        
        $dept_sql = $dept_id > 0 ? $dept_id : "NULL";
        $assign_sql = $assigned_to > 0 ? $assigned_to : "NULL";

        $conn->query("UPDATE tickets SET department_id=$dept_sql, assigned_admin_id=$assign_sql WHERE id=$ticket_id");
        log_audit($conn, 'Update', 'Ticket', 'Admin', $admin_id, "Updated assignment for ticket ID: $ticket_id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'update_status') {
        $ticket_id = (int)$_POST['ticket_id'];
        $status = $conn->real_escape_string($_POST['status']);
        
        $conn->query("UPDATE tickets SET status='$status' WHERE id=$ticket_id");
        log_audit($conn, 'Update', 'Ticket', 'Admin', $admin_id, "Updated status to '$status' for ticket ID: $ticket_id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'add_reply') {
        $ticket_id = (int)$_POST['ticket_id'];
        $message = $conn->real_escape_string(trim($_POST['message']));
        
        if (empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Reply cannot be empty.']); exit;
        }

        $conn->query("INSERT INTO ticket_replies (ticket_id, sender_type, sender_id, message) VALUES ($ticket_id, 'Admin', $admin_id, '$message')");
        // Also update ticket status to In Progress if it was Open
        $conn->query("UPDATE tickets SET status='In Progress' WHERE id=$ticket_id AND status='Open'");
        
        log_audit($conn, 'Update', 'Ticket', 'Admin', $admin_id, "Replied to ticket ID: $ticket_id");
        
        $new_id = $conn->insert_id;
        $res = $conn->query("SELECT tr.*, a.name as admin_name FROM ticket_replies tr JOIN admins a ON tr.sender_id = a.id WHERE tr.id = $new_id");
        $reply = $res->fetch_assoc();
        
        $html = '<div class="flex flex-col items-end mb-6">';
        $html .= '<div class="flex items-center gap-2 mb-1">';
        $html .= '<span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">' . date('M j, Y h:i A', strtotime($reply['created_at'])) . '</span>';
        $html .= '<span class="text-xs font-bold text-slate-900">' . htmlspecialchars($reply['admin_name']) . '</span>';
        $html .= '</div>';
        $html .= '<div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-5 py-3 max-w-[80%]">';
        $html .= '<p class="text-sm leading-relaxed whitespace-pre-wrap font-medium">' . htmlspecialchars($reply['message']) . '</p>';
        $html .= '</div></div>';

        echo json_encode(['status' => 'success', 'html' => $html]);
        exit;
    }

    if ($_GET['ajax_action'] == 'load_details') {
        $id = (int)$_GET['id'];
        
        // Ticket info
        $res = $conn->query("SELECT t.*, c.name as client_name, c.email as client_email FROM tickets t JOIN clients c ON t.client_id = c.id WHERE t.id = $id");
        $ticket = $res->fetch_assoc();
        
        // Replies
        $replies = [];
        $res = $conn->query("
            SELECT tr.*, 
                   IF(tr.sender_type='Admin', a.name, c.name) as sender_name 
            FROM ticket_replies tr 
            LEFT JOIN admins a ON (tr.sender_type = 'Admin' AND tr.sender_id = a.id)
            LEFT JOIN clients c ON (tr.sender_type = 'Client' AND tr.sender_id = c.id)
            WHERE tr.ticket_id = $id ORDER BY tr.created_at ASC
        ");
        while ($row = $res->fetch_assoc()) $replies[] = $row;
        
        // Depts and Staff for dropdowns
        $depts = []; $staff = [];
        $resDepts = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");
        while ($r = $resDepts->fetch_assoc()) $depts[] = $r;
        $resStaff = $conn->query("SELECT id, name FROM admins ORDER BY name ASC");
        while ($r = $resStaff->fetch_assoc()) $staff[] = $r;

        ob_start();
        ?>
        <!-- Header & Meta -->
        <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm mb-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold font-headline text-slate-900 mb-1"><?php echo htmlspecialchars($ticket['subject']); ?></h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1"><span class="material-symbols-outlined text-sm">person</span> <?php echo htmlspecialchars($ticket['client_name']); ?> (<?php echo htmlspecialchars($ticket['client_email']); ?>)</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest <?php echo $ticket['status']=='Open'?'bg-amber-50 text-amber-600':($ticket['status']=='In Progress'?'bg-blue-50 text-blue-600':($ticket['status']=='Resolved'?'bg-emerald-50 text-emerald-600':'bg-slate-100 text-slate-500')); ?>"><?php echo $ticket['status']; ?></span>
                    <span class="px-2 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest border border-slate-100 <?php echo $ticket['priority']=='Urgent'?'text-red-500':($ticket['priority']=='High'?'text-orange-500':'text-slate-500'); ?>"><?php echo $ticket['priority']; ?> Priority</span>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                <div class="space-y-1">
                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Assign Department</label>
                    <select onchange="updateAssignment(<?php echo $id; ?>)" id="deptSelect" class="w-full bg-slate-50 border-transparent rounded-xl px-3 py-2 text-xs font-bold focus:ring-1 focus:ring-primary text-slate-700">
                        <option value="0">Unassigned</option>
                        <?php foreach($depts as $d): ?>
                            <option value="<?php echo $d['id']; ?>" <?php if($ticket['department_id']==$d['id']) echo 'selected'; ?>><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Assign Staff</label>
                    <select onchange="updateAssignment(<?php echo $id; ?>)" id="staffSelect" class="w-full bg-slate-50 border-transparent rounded-xl px-3 py-2 text-xs font-bold focus:ring-1 focus:ring-primary text-slate-700">
                        <option value="0">Unassigned</option>
                        <?php foreach($staff as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php if($ticket['assigned_admin_id']==$s['id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Update Status</label>
                    <select onchange="updateStatus(<?php echo $id; ?>, this.value)" class="w-full bg-slate-50 border-transparent rounded-xl px-3 py-2 text-xs font-bold focus:ring-1 focus:ring-primary text-slate-700">
                        <option value="Open" <?php if($ticket['status']=='Open') echo 'selected'; ?>>Open</option>
                        <option value="In Progress" <?php if($ticket['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                        <option value="Resolved" <?php if($ticket['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                        <option value="Closed" <?php if($ticket['status']=='Closed') echo 'selected'; ?>>Closed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Initial Ticket Description -->
        <div class="flex flex-col items-start mb-6">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-slate-900"><?php echo htmlspecialchars($ticket['client_name']); ?></span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo date('M j, Y h:i A', strtotime($ticket['created_at'])); ?></span>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-5 max-w-[80%] shadow-sm">
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($ticket['description']); ?></p>
            </div>
        </div>

        <!-- Replies Thread -->
        <div id="replyThread" class="space-y-6 mb-8">
            <?php foreach ($replies as $r): ?>
                <?php if ($r['sender_type'] === 'Admin'): ?>
                    <!-- Admin Reply (Right) -->
                    <div class="flex flex-col items-end">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo date('M j, Y h:i A', strtotime($r['created_at'])); ?></span>
                            <span class="text-xs font-bold text-slate-900"><?php echo htmlspecialchars($r['sender_name']); ?></span>
                        </div>
                        <div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-5 py-3 max-w-[80%] shadow-sm">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap font-medium"><?php echo htmlspecialchars($r['message']); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Client Reply (Left) -->
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-slate-900"><?php echo htmlspecialchars($r['sender_name']); ?></span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo date('M j, Y h:i A', strtotime($r['created_at'])); ?></span>
                        </div>
                        <div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-5 max-w-[80%] shadow-sm">
                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($r['message']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Reply Box -->
        <?php if ($ticket['status'] !== 'Closed'): ?>
        <form onsubmit="addReply(event, <?php echo $id; ?>)" class="bg-white rounded-3xl p-4 flex gap-4 items-end border border-slate-100 shadow-sm relative bottom-0">
            <div class="flex-1">
                <textarea id="replyMessage" rows="2" placeholder="Type your reply to the client..." required class="w-full border-0 focus:ring-0 p-2 text-sm text-slate-700 custom-scrollbar resize-none bg-transparent"></textarea>
            </div>
            <button type="submit" id="replyBtn" class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shrink-0">
                <span class="material-symbols-outlined">send</span>
            </button>
        </form>
        <?php else: ?>
        <div class="text-center py-6 bg-slate-50 rounded-2xl border border-slate-100">
            <span class="material-symbols-outlined text-slate-300 text-3xl mb-1">lock</span>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">This ticket is closed.</p>
        </div>
        <?php endif; ?>
        
        <?php
        echo json_encode(['html' => ob_get_clean()]);
        exit;
    }
}

// Fetch Tickets List
$tickets = [];
$res = $conn->query("SELECT t.id, t.subject, t.status, t.priority, t.created_at, c.name as client_name, d.name as dept_name, a.name as admin_name 
                     FROM tickets t 
                     JOIN clients c ON t.client_id = c.id 
                     LEFT JOIN departments d ON t.department_id = d.id 
                     LEFT JOIN admins a ON t.assigned_admin_id = a.id 
                     ORDER BY t.created_at DESC");
while ($row = $res->fetch_assoc()) $tickets[] = $row;

$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Support Tickets | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">

<script src="../components/admin_sidenav.js" data-root="../"></script>

<div id="toast" class="fixed top-6 right-6 z-[400] transform translate-x-[150%] transition-transform duration-300 pointer-events-none">
    <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span id="toastIcon" class="material-symbols-outlined">check_circle</span>
        <p id="toastMessage" class="text-xs font-bold"></p>
    </div>
</div>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Support Tickets</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Client Requests & Issues</p>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Master List -->
        <div class="w-full md:w-80 lg:w-96 bg-white border-r border-slate-100 overflow-y-auto custom-scrollbar flex flex-col shrink-0 p-4 space-y-3">
            <?php if (empty($tickets)): ?>
                <div class="text-center py-10"><span class="material-symbols-outlined text-4xl text-slate-200">mark_email_read</span><p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No Support Tickets</p></div>
            <?php endif; ?>
            <?php foreach ($tickets as $t): ?>
                <div class="group relative bg-white border border-slate-100 rounded-2xl p-4 cursor-pointer hover:border-primary/50 transition-all hover:shadow-sm" onclick="loadTicket(<?php echo $t['id']; ?>, this)">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest <?php echo $t['status']=='Open'?'bg-amber-50 text-amber-600':($t['status']=='In Progress'?'bg-blue-50 text-blue-600':($t['status']=='Resolved'?'bg-emerald-50 text-emerald-600':'bg-slate-100 text-slate-500')); ?>"><?php echo $t['status']; ?></span>
                        <span class="text-[9px] font-bold text-slate-300"><?php echo date('M j', strtotime($t['created_at'])); ?></span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm leading-tight mb-1 truncate"><?php echo htmlspecialchars($t['subject']); ?></h3>
                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest truncate mb-2"><?php echo htmlspecialchars($t['client_name']); ?></p>
                    
                    <div class="flex flex-wrap gap-1">
                        <?php if($t['priority']=='Urgent'): ?><span class="px-1.5 py-0.5 rounded bg-red-50 text-red-500 text-[8px] font-bold uppercase">Urgent</span><?php endif; ?>
                        <?php if($t['dept_name']): ?><span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[8px] font-bold uppercase"><span class="material-symbols-outlined text-[8px] align-middle">domain</span> <?php echo htmlspecialchars($t['dept_name']); ?></span><?php endif; ?>
                        <?php if($t['admin_name']): ?><span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[8px] font-bold uppercase"><span class="material-symbols-outlined text-[8px] align-middle">person</span> <?php echo htmlspecialchars($t['admin_name']); ?></span><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Detail View -->
        <div class="flex-1 bg-[#F8FAFC] overflow-y-auto custom-scrollbar relative flex flex-col">
            <div id="detailPane" class="flex-1 p-6 lg:p-10 max-w-4xl mx-auto w-full hidden flex flex-col">
                <!-- Content loaded via AJAX -->
            </div>
            <div id="emptyPane" class="absolute inset-0 flex flex-col items-center justify-center text-slate-300">
                <span class="material-symbols-outlined text-6xl mb-4">forum</span>
                <p class="text-sm font-bold uppercase tracking-widest">Select a ticket to view conversation</p>
            </div>
        </div>
    </div>
</div>

<script>
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    document.getElementById('toastMessage').innerText = msg;
    document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'error';
    document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] ${type==='success'?'bg-slate-900 text-white':'bg-red-600 text-white'}`;
    t.style.transform = 'translateX(0)';
    setTimeout(() => t.style.transform = 'translateX(150%)', 4000);
}

async function loadTicket(id, cardEl) {
    document.querySelectorAll('.group').forEach(el => el.classList.remove('ring-2', 'ring-primary', 'border-transparent'));
    if (cardEl) cardEl.classList.add('ring-2', 'ring-primary', 'border-transparent');
    
    document.getElementById('emptyPane').classList.add('hidden');
    const pane = document.getElementById('detailPane');
    pane.classList.remove('hidden');
    pane.innerHTML = '<div class="text-center py-20 flex-1"><span class="material-symbols-outlined text-primary text-4xl animate-spin">sync</span></div>';
    
    const res = await fetch(`?ajax_action=load_details&id=${id}`);
    const data = await res.json();
    pane.innerHTML = data.html;
    
    // scroll to bottom
    const detailContainer = pane.parentElement;
    detailContainer.scrollTop = detailContainer.scrollHeight;
}

async function updateAssignment(ticketId) {
    const deptId = document.getElementById('deptSelect').value;
    const adminId = document.getElementById('staffSelect').value;
    
    const fd = new FormData();
    fd.append('ticket_id', ticketId);
    fd.append('department_id', deptId);
    fd.append('assigned_admin_id', adminId);
    
    try {
        const res = await fetch('?ajax_action=update_assignment', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') showToast('Assignment updated');
    } catch(err) {
        showToast('Network error', 'error');
    }
}

async function updateStatus(ticketId, status) {
    const fd = new FormData();
    fd.append('ticket_id', ticketId);
    fd.append('status', status);
    
    try {
        const res = await fetch('?ajax_action=update_status', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') {
            showToast('Status updated');
            // Option to refresh the sidebar silently
        }
    } catch(err) {
        showToast('Network error', 'error');
    }
}

async function addReply(e, ticketId) {
    e.preventDefault();
    const btn = document.getElementById('replyBtn');
    const input = document.getElementById('replyMessage');
    const message = input.value;
    
    btn.disabled = true;
    
    const fd = new FormData();
    fd.append('ticket_id', ticketId);
    fd.append('message', message);
    
    try {
        const res = await fetch('?ajax_action=add_reply', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') {
            input.value = '';
            document.getElementById('replyThread').insertAdjacentHTML('beforeend', result.html);
            // scroll to bottom
            const detailContainer = document.getElementById('detailPane').parentElement;
            detailContainer.scrollTop = detailContainer.scrollHeight;
        } else {
            showToast(result.message, 'error');
        }
    } catch(err) {
        showToast('Network error', 'error');
    } finally {
        btn.disabled = false;
    }
}
</script>
</body>
</html>
