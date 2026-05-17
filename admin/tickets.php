<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid security token. Please refresh the page.']);
            exit;
        }
    }

    if ($_GET['ajax_action'] == 'update_assignment') {
        $ticket_id = (int)$_POST['ticket_id'];
        $dept_id = (int)($_POST['department_id'] ?? 0);
        $assigned_to = (int)($_POST['assigned_admin_id'] ?? 0);

        if ($dept_id > 0 && $assigned_to > 0) {
            $stmt = $conn->prepare("UPDATE tickets SET department_id=?, assigned_admin_id=? WHERE id=?");
            $stmt->bind_param("iii", $dept_id, $assigned_to, $ticket_id);
        } elseif ($dept_id > 0) {
            $stmt = $conn->prepare("UPDATE tickets SET department_id=?, assigned_admin_id=NULL WHERE id=?");
            $stmt->bind_param("ii", $dept_id, $ticket_id);
        } elseif ($assigned_to > 0) {
            $stmt = $conn->prepare("UPDATE tickets SET department_id=NULL, assigned_admin_id=? WHERE id=?");
            $stmt->bind_param("ii", $assigned_to, $ticket_id);
        } else {
            $stmt = $conn->prepare("UPDATE tickets SET department_id=NULL, assigned_admin_id=NULL WHERE id=?");
            $stmt->bind_param("i", $ticket_id);
        }
        $stmt->execute();
        $stmt->close();
        log_audit($conn, 'Update', 'Ticket', 'Admin', $admin_id, "Updated assignment for ticket ID: $ticket_id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'update_status') {
        $ticket_id = (int)$_POST['ticket_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE tickets SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $ticket_id);
        $stmt->execute();
        $stmt->close();
        log_audit($conn, 'Update', 'Ticket', 'Admin', $admin_id, "Updated status to '$status' for ticket ID: $ticket_id");
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'add_reply') {
        $ticket_id = (int)$_POST['ticket_id'];
        $message = trim($_POST['message']);
        $attachment = null;

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('ticket_') . '.' . $ext;
            $upload_path = '../uploads/tickets/' . $filename;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                $attachment = $filename;
            }
        }
        
        if (empty($message) && !$attachment) {
            echo json_encode(['status' => 'error', 'message' => 'Reply cannot be empty.']); exit;
        }

        if ($attachment) {
            $stmt = $conn->prepare("INSERT INTO ticket_replies (ticket_id, sender_type, sender_id, message, attachment) VALUES (?, 'Admin', ?, ?, ?)");
            $stmt->bind_param("iiss", $ticket_id, $admin_id, $message, $attachment);
        } else {
            $stmt = $conn->prepare("INSERT INTO ticket_replies (ticket_id, sender_type, sender_id, message, attachment) VALUES (?, 'Admin', ?, ?, NULL)");
            $stmt->bind_param("iis", $ticket_id, $admin_id, $message);
        }
        $stmt->execute();
        $new_reply_id = $conn->insert_id;
        $stmt->close();
        
        // Also update ticket status to In Progress if it was Open
        $stmt2 = $conn->prepare("UPDATE tickets SET status='In Progress' WHERE id=? AND status='Open'");
        $stmt2->bind_param("i", $ticket_id);
        $stmt2->execute();
        $stmt2->close();
        
        log_audit($conn, 'Update', 'Ticket', 'Admin', $admin_id, "Replied to ticket ID: $ticket_id" . ($attachment ? " with attachment" : ""));
        
        $stmt3 = $conn->prepare("SELECT tr.*, a.name as admin_name FROM ticket_replies tr JOIN admins a ON tr.sender_id = a.id WHERE tr.id = ?");
        $stmt3->bind_param("i", $new_reply_id);
        $stmt3->execute();
        $res = $stmt3->get_result();
        $reply = $res->fetch_assoc();
        $stmt3->close();
        
        // Attachment HTML
        $attach_html = '';
        if ($reply['attachment']) {
            $path = '../uploads/tickets/' . $reply['attachment'];
            $is_img = in_array(strtolower(pathinfo($reply['attachment'], PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp']);
            if ($is_img) {
                $attach_html = '<a href="'.$path.'" target="_blank" class="block mt-2 rounded-lg overflow-hidden border border-white/20"><img src="'.$path.'" class="max-w-xs h-auto" /></a>';
            } else {
                $attach_html = '<a href="'.$path.'" target="_blank" class="flex items-center gap-2 mt-2 p-2 rounded-lg bg-black/10 text-[10px] font-bold uppercase"><span class="material-symbols-outlined text-sm">description</span> View Attachment</a>';
            }
        }

        $html = '<div class="flex flex-col items-end mb-6 animate-in slide-in-from-right-4 duration-300">';
        $html .= '<div class="flex items-center gap-2 mb-1">';
        $html .= '<span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">' . date('M j, Y h:i A', strtotime($reply['created_at'])) . '</span>';
        $html .= '<span class="text-xs font-bold text-slate-900">' . htmlspecialchars($reply['admin_name']) . '</span>';
        $html .= '</div>';
        $html .= '<div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-5 py-3 max-w-[80%] shadow-lg shadow-primary/10">';
        if (!empty($reply['message'])) {
            $html .= '<div class="text-sm leading-relaxed font-medium">' . $reply['message'] . '</div>';
        }
        $html .= $attach_html;
        $html .= '</div></div>';

        echo json_encode(['status' => 'success', 'html' => $html, 'reply_id' => $new_reply_id]);
        exit;
    }

    if ($_GET['ajax_action'] == 'poll_replies') {
        $ticket_id = (int)$_GET['ticket_id'];
        $last_id = (int)$_GET['last_id'];
        
        $stmt = $conn->prepare("
            SELECT tr.*, 
                   IF(tr.sender_type='Admin', a.name, c.name) as sender_name 
            FROM ticket_replies tr 
            LEFT JOIN admins a ON (tr.sender_type = 'Admin' AND tr.sender_id = a.id)
            LEFT JOIN clients c ON (tr.sender_type = 'Client' AND tr.sender_id = c.id)
            WHERE tr.ticket_id = ? AND tr.id > ? 
            ORDER BY tr.created_at ASC
        ");
        $stmt->bind_param("ii", $ticket_id, $last_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $replies = [];
        while ($r = $res->fetch_assoc()) {
            $attach_html = '';
            if ($r['attachment']) {
                $path = '../uploads/tickets/' . $r['attachment'];
                $is_img = in_array(strtolower(pathinfo($r['attachment'], PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp']);
                if ($is_img) {
                    $attach_html = '<a href="'.$path.'" target="_blank" class="block mt-2 rounded-lg overflow-hidden border border-white/20"><img src="'.$path.'" class="max-w-xs h-auto" /></a>';
                } else {
                    $attach_html = '<a href="'.$path.'" target="_blank" class="flex items-center gap-2 mt-2 p-2 rounded-lg bg-black/10 text-[10px] font-bold uppercase"><span class="material-symbols-outlined text-sm">description</span> View Attachment</a>';
                }
            }

            $html = '';
            if ($r['sender_type'] === 'Admin') {
                $html .= '<div class="flex flex-col items-end mb-6 animate-in slide-in-from-right-4 duration-300">';
                $html .= '<div class="flex items-center gap-2 mb-1">';
                $html .= '<span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">' . date('M j, Y h:i A', strtotime($r['created_at'])) . '</span>';
                $html .= '<span class="text-xs font-bold text-slate-900">' . htmlspecialchars($r['sender_name']) . '</span>';
                $html .= '</div>';
                $html .= '<div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-5 py-3 max-w-[80%] shadow-lg shadow-primary/10">';
                if (!empty($r['message'])) {
                    $html .= '<div class="text-sm leading-relaxed font-medium">' . $r['message'] . '</div>';
                }
                $html .= $attach_html;
                $html .= '</div></div>';
            } else {
                $html .= '<div class="flex flex-col items-start mb-6 animate-in slide-in-from-left-4 duration-300">';
                $html .= '<div class="flex items-center gap-2 mb-1">';
                $html .= '<span class="text-xs font-bold text-slate-900">' . htmlspecialchars($r['sender_name']) . '</span>';
                $html .= '<span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">' . date('M j, Y h:i A', strtotime($r['created_at'])) . '</span>';
                $html .= '</div>';
                $html .= '<div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-5 max-w-[80%] shadow-sm">';
                if (!empty($r['message'])) {
                    $html .= '<div class="text-sm text-slate-700 leading-relaxed">' . $r['message'] . '</div>';
                }
                $html .= str_replace('bg-black/10', 'bg-slate-50', $attach_html); // slight style diff for client attachment
                $html .= '</div></div>';
            }
            $replies[] = ['id' => $r['id'], 'html' => $html];
        }
        $stmt->close();
        echo json_encode(['status' => 'success', 'replies' => $replies]);
        exit;
    }

    if ($_GET['ajax_action'] == 'load_details') {
        $id = (int)$_GET['id'];
        
        // Ticket info
        $stmt = $conn->prepare("SELECT t.*, c.name as client_name, c.email as client_email FROM tickets t JOIN clients c ON t.client_id = c.id WHERE t.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $ticket = $res->fetch_assoc();
        $stmt->close();
        
        // Replies
        $replies = [];
        $stmt2 = $conn->prepare("
            SELECT tr.*, 
                   IF(tr.sender_type='Admin', a.name, c.name) as sender_name 
            FROM ticket_replies tr 
            LEFT JOIN admins a ON (tr.sender_type = 'Admin' AND tr.sender_id = a.id)
            LEFT JOIN clients c ON (tr.sender_type = 'Client' AND tr.sender_id = c.id)
            WHERE tr.ticket_id = ? ORDER BY tr.created_at ASC
        ");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $res = $stmt2->get_result();
        while ($row = $res->fetch_assoc()) $replies[] = $row;
        $stmt2->close();
        
        // Depts and Staff for dropdowns
        $depts = []; $staff = [];
        $resDepts = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");
        while ($r = $resDepts->fetch_assoc()) $depts[] = $r;
        $resStaff = $conn->query("SELECT id, name FROM admins ORDER BY name ASC");
        while ($r = $resStaff->fetch_assoc()) $staff[] = $r;

        ob_start();
        ?>
        <!-- Header & Meta -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm mb-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                    <h2 class="text-xl font-bold font-headline text-slate-900 mb-1"><?php echo htmlspecialchars($ticket['subject']); ?></h2>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm text-slate-400">person</span>
                        <p class="text-xs font-bold text-slate-600"><?php echo htmlspecialchars($ticket['client_name']); ?> <span class="text-slate-300 font-normal ml-1">(<?php echo htmlspecialchars($ticket['client_email']); ?>)</span></p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest <?php echo $ticket['status']=='Open'?'bg-amber-100 text-amber-700':($ticket['status']=='In Progress'?'bg-blue-100 text-blue-700':($ticket['status']=='Resolved'?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-600')); ?>"><?php echo $ticket['status']; ?></span>
                    <span class="px-2 py-0.5 rounded bg-slate-50 text-slate-400 text-[9px] font-bold uppercase tracking-tighter"><?php echo $ticket['priority']; ?> Priority</span>
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
                <?php 
                $attach_html = '';
                if (!empty($r['attachment'])) {
                    $path = '../uploads/tickets/' . $r['attachment'];
                    $is_img = in_array(strtolower(pathinfo($r['attachment'], PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp']);
                    if ($is_img) {
                        $attach_html = '<a href="'.$path.'" target="_blank" class="block mt-2 rounded-lg overflow-hidden border border-white/20"><img src="'.$path.'" class="max-w-xs h-auto" /></a>';
                    } else {
                        $attach_html = '<a href="'.$path.'" target="_blank" class="flex items-center gap-2 mt-2 p-2 rounded-lg bg-black/10 text-[10px] font-bold uppercase"><span class="material-symbols-outlined text-sm">description</span> View Attachment</a>';
                    }
                }
                ?>
                <?php if ($r['sender_type'] === 'Admin'): ?>
                    <!-- Admin Reply (Right) -->
                    <div class="flex flex-col items-end">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo date('M j, Y h:i A', strtotime($r['created_at'])); ?></span>
                            <span class="text-xs font-bold text-slate-900"><?php echo htmlspecialchars($r['sender_name']); ?></span>
                        </div>
                        <div class="bg-primary text-on-primary rounded-2xl rounded-tr-none px-5 py-3 max-w-[80%] shadow-sm">
                            <?php if (!empty($r['message'])): ?>
                                <div class="text-sm leading-relaxed font-medium"><?php echo $r['message']; ?></div>
                            <?php endif; ?>
                            <?php echo $attach_html; ?>
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
                            <?php if (!empty($r['message'])): ?>
                                <div class="text-sm text-slate-700 leading-relaxed"><?php echo $r['message']; ?></div>
                            <?php endif; ?>
                            <?php echo str_replace('bg-black/10', 'bg-slate-50', $attach_html); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Reply Box -->
        <?php if ($ticket['status'] !== 'Closed'): ?>
        <form id="replyForm" onsubmit="addReply(event, <?php echo $id; ?>)" class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm relative bottom-0">
            <?= get_csrf_field() ?>
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <textarea id="replyMessage" class="wysiwyg" placeholder="Type your reply to the client..."></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="file" id="replyAttachment" class="hidden" onchange="updateFileLabel(this)" />
                    <button type="button" onclick="document.getElementById('replyAttachment').click()" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">attach_file</span>
                    </button>
                    <button type="submit" id="replyBtn" class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shrink-0">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </div>
            </div>
            <div id="fileLabel" class="mt-2 text-[10px] text-slate-400 font-bold hidden px-2"></div>
        </form>
        <?php else: ?>
        <div class="text-center py-6 bg-slate-50 rounded-2xl border border-slate-100">
            <span class="material-symbols-outlined text-slate-300 text-3xl mb-1">lock</span>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">This ticket is closed.</p>
        </div>
        <?php endif; ?>
        
        <?php
        $html = ob_get_clean();
        $last_reply_id = !empty($replies) ? end($replies)['id'] : 0;
        echo json_encode(['html' => $html, 'last_reply_id' => $last_reply_id]);
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
    <script>const CSRF_TOKEN = '<?= generate_csrf_token() ?>';</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <script src="../components/wysiwyg.js"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface min-h-screen">

<script src="../components/admin_sidenav.js" data-root="../"></script>

<div id="toast" class="fixed top-6 right-6 z-[400] transform translate-x-[150%] transition-transform duration-300 pointer-events-none">
    <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span id="toastIcon" class="material-symbols-outlined">check_circle</span>
        <p id="toastMessage" class="text-xs font-bold"></p>
    </div>
</div>

<div class="lg:ml-64 min-h-screen flex flex-col bg-[#F8FAFC]">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center px-6 shrink-0 z-20 sticky top-0">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Support Tickets</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Client Requests & Issues</p>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden h-[calc(100vh-64px)]">
        <!-- Master List -->
        <div class="w-full md:w-64 lg:w-80 bg-white border-r border-slate-100 overflow-y-auto custom-scrollbar flex flex-col shrink-0 p-4 space-y-3">
            <?php if (empty($tickets)): ?>
                <div class="text-center py-10"><span class="material-symbols-outlined text-4xl text-slate-200">mark_email_read</span><p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest">No Support Tickets</p></div>
            <?php endif; ?>
            <?php foreach ($tickets as $t): ?>
                <div class="group relative bg-white border border-slate-100 rounded-xl p-3 cursor-pointer hover:border-primary/50 transition-all hover:shadow-sm" onclick="loadTicket(<?php echo $t['id']; ?>, this)">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="px-1.5 py-0.5 rounded text-[7px] font-bold uppercase tracking-tighter <?php echo $t['status']=='Open'?'bg-amber-50 text-amber-600':($t['status']=='In Progress'?'bg-blue-50 text-blue-600':($t['status']=='Resolved'?'bg-emerald-50 text-emerald-600':'bg-slate-100 text-slate-500')); ?>"><?php echo $t['status']; ?></span>
                        <span class="text-[8px] font-bold text-slate-300"><?php echo date('M j', strtotime($t['created_at'])); ?></span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-[13px] leading-none mb-1 truncate"><?php echo htmlspecialchars($t['subject']); ?></h3>
                    <p class="text-[9px] font-bold text-primary uppercase tracking-widest truncate mb-2"><?php echo htmlspecialchars($t['client_name']); ?></p>
                    
                    <div class="flex flex-wrap gap-1 opacity-70 group-hover:opacity-100 transition-opacity">
                        <?php if($t['priority']=='Urgent'): ?><span class="px-1 py-0.5 rounded bg-red-50 text-red-500 text-[7px] font-bold uppercase">Urgent</span><?php endif; ?>
                        <?php if($t['dept_name']): ?><span class="px-1 py-0.5 rounded bg-slate-50 text-slate-500 text-[7px] font-bold uppercase"><?php echo htmlspecialchars($d_name = (strlen($t['dept_name']) > 15 ? substr($t['dept_name'],0,12).'...' : $t['dept_name'])); ?></span><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Detail View -->
        <div class="flex-1 bg-[#F8FAFC] overflow-y-auto custom-scrollbar relative flex flex-col">
            <div id="detailPane" class="flex-1 p-4 lg:p-6 w-full hidden flex flex-col">
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

    let currentTicketId = null;
    let lastReplyId = 0;
    let pollInterval = null;

    async function loadTicket(id, cardEl) {
        currentTicketId = id;
        document.querySelectorAll('.group').forEach(el => el.classList.remove('ring-2', 'ring-primary', 'border-transparent', 'bg-slate-50'));
        if (cardEl) cardEl.classList.add('ring-2', 'ring-primary', 'border-transparent', 'bg-slate-50');
        
        document.getElementById('emptyPane').classList.add('hidden');
        const pane = document.getElementById('detailPane');
        pane.classList.remove('hidden');
        pane.innerHTML = '<div class="text-center py-20 flex-1 flex flex-col items-center justify-center"><span class="material-symbols-outlined text-primary text-4xl animate-spin mb-4">sync</span><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Loading Conversation...</p></div>';
        
        const res = await fetch(`?ajax_action=load_details&id=${id}`);
        const data = await res.json();
        pane.innerHTML = data.html;
        
        lastReplyId = data.last_reply_id;
        
        // scroll to bottom
        const detailContainer = pane.parentElement;
        detailContainer.scrollTop = detailContainer.scrollHeight;

        // Reset polling
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(pollNewReplies, 15000);
    }

    function updateLastReplyId() {
        // This is a bit hacky but works: find the last reply ID in the DOM if possible, 
        // or just rely on the server giving us everything. 
        // Actually, better to pass it in the load_details JSON.
        // For now, let's just use the server side count or similar.
    }

    async function pollNewReplies() {
        if (!currentTicketId) return;
        
        const res = await fetch(`?ajax_action=poll_replies&ticket_id=${currentTicketId}&last_id=${lastReplyId}`);
        const data = await res.json();
        
        if (data.status === 'success' && data.replies.length > 0) {
            const thread = document.getElementById('replyThread');
            data.replies.forEach(r => {
                thread.insertAdjacentHTML('beforeend', r.html);
                lastReplyId = Math.max(lastReplyId, r.id);
            });
            // scroll to bottom
            const detailContainer = document.getElementById('detailPane').parentElement;
            detailContainer.scrollTop = detailContainer.scrollHeight;
        }
    }

    async function updateAssignment(ticketId) {
        const deptId = document.getElementById('deptSelect').value;
        const adminId = document.getElementById('staffSelect').value;
        
        const fd = new FormData();
        fd.append('csrf_token', CSRF_TOKEN);
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
        fd.append('csrf_token', CSRF_TOKEN);
        fd.append('ticket_id', ticketId);
        fd.append('status', status);
        
        try {
            const res = await fetch('?ajax_action=update_status', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.status === 'success') {
                showToast('Status updated');
                // Refresh sidebar list item status
                const activeCard = document.querySelector('.ring-2.ring-primary');
                if (activeCard) {
                    const badge = activeCard.querySelector('span[class*="rounded"]');
                    if (badge) {
                        badge.innerText = status;
                        // update colors if needed
                    }
                }
            }
        } catch(err) {
            showToast('Network error', 'error');
        }
    }

    function updateFileLabel(input) {
        const label = document.getElementById('fileLabel');
        if (input.files && input.files[0]) {
            label.innerText = 'Attached: ' + input.files[0].name;
            label.classList.remove('hidden');
        } else {
            label.classList.add('hidden');
        }
    }

    async function addReply(e, ticketId) {
        e.preventDefault();
        const btn = document.getElementById('replyBtn');
        const fileInput = document.getElementById('replyAttachment');
        const message = WYSIWYG.getContent('replyMessage');
        
        btn.disabled = true;
        
        const fd = new FormData();
        fd.append('csrf_token', CSRF_TOKEN);
        fd.append('ticket_id', ticketId);
        fd.append('message', message);
        if (fileInput.files[0]) {
            fd.append('attachment', fileInput.files[0]);
        }
        try {
            const res = await fetch('?ajax_action=add_reply', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.status === 'success') {
                WYSIWYG.setContent('replyMessage', '');
                fileInput.value = '';
                document.getElementById('fileLabel').classList.add('hidden');
                document.getElementById('replyThread').insertAdjacentHTML('beforeend', result.html);
                lastReplyId = Math.max(lastReplyId, result.reply_id);
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
