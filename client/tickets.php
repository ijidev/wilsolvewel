<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: login.php");
    exit();
}
$conn = get_db_connection();

// Handle New Ticket Submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['create_ticket']) || isset($_POST['ajax_ticket']))) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        if (isset($_POST['ajax_ticket'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
            exit;
        }
        $message = 'Invalid or expired CSRF token. Please reload the page and try again.';
    } else {
        $subject = $_POST['subject'];
        $priority = $_POST['priority'] ?? 'Normal';
        $description = $_POST['description'];
        $project_id = (int)($_POST['project_id'] ?? 0);
        $order_id = (int)($_POST['order_id'] ?? 0);

        // Auto-Routing Logic
        $dept_id = get_auto_assigned_department($conn, 'ticket', $subject . ' ' . $description);

        $stmt = $conn->prepare("INSERT INTO tickets (client_id, project_id, order_id, department_id, subject, priority, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Open')");
        if ($stmt) {
            $stmt->bind_param("iiiisss", $client_id, $project_id ?: null, $order_id ?: null, $dept_id ?: null, $subject, $priority, $description);
            if ($stmt->execute()) {
                $ticket_id = $conn->insert_id;
                $client_name = $_SESSION['client_name'] ?? 'A client';
                notify_department_admins($conn, $dept_id, 'New ticket from ' . $client_name, $subject, 'admin/tickets.php?ticket_id=' . $ticket_id, 'confirmation_number', 'New Support Ticket: ' . $subject, email_template('New Ticket #' . $ticket_id, '<p>' . htmlspecialchars($client_name) . ' has opened a new support ticket:</p><p><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p><p><strong>Priority:</strong> ' . htmlspecialchars($priority) . '</p><p style="margin-top:20px"><a href="' . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . '/admin/tickets.php?ticket_id=' . $ticket_id . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px">View Ticket</a></p>'));
                if (isset($_POST['ajax_ticket'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
                    exit;
                }
                $message = "Ticket created successfully.";
            } else {
                if (isset($_POST['ajax_ticket'])) {
                    header('Content-Type: application/json');
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
                    exit;
                }
                $message = "Error creating ticket: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch Tickets
$tickets_res = safe_query($conn, "SELECT t.*, p.name as project_name, d.name as department_name FROM tickets t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN departments d ON t.department_id = d.id WHERE t.client_id = ? ORDER BY t.created_at DESC", "i", [$client_id]);
$page_title = 'WILSOVLEWEL | Client Tickets Portal';
$page_h1 = 'Support Portal';
$page_h1_sub = 'Open new tickets or track existing resolutions for your projects.';
$page_h1_badge = 'System Support Console';
$page_h1_action = '<button onclick="document.getElementById(\'newTicketModal\').classList.remove(\'hidden\')" class="bg-gradient-to-br from-primary to-amber-600 text-on-primary px-4 sm:px-6 py-3 rounded-full font-headline font-medium flex items-center gap-2 shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform active:opacity-80"><span class="material-symbols-outlined">add</span> <span class="hidden sm:inline">Create New Ticket</span><span class="inline sm:hidden">New Ticket</span></button>';

ob_start();
?>

<?php if ($message): ?>
<div class="mb-8 p-4 bg-primary/10 border border-primary text-primary rounded-xl font-bold text-sm">
    <?= $message ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <div class="xl:col-span-8 space-y-6">
        <?php if ($tickets_res->num_rows > 0): 
            while($t = $tickets_res->fetch_assoc()):
                $priority_class = ($t['priority'] == 'Critical') ? 'bg-error-container text-on-error-container border-error' : (($t['priority'] == 'High') ? 'bg-orange-100 text-orange-700 border-orange-500' : 'bg-blue-100 text-blue-700 border-blue-500');
        ?>
        <div class="group bg-white rounded-2xl p-4 shadow-sm border-l-4 <?= ($t['priority'] == 'Critical' ? 'border-error' : ($t['priority'] == 'High' ? 'border-orange-500' : 'border-blue-500')) ?> hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded <?= $priority_class ?>"><?= $t['priority'] ?></span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">#TK-<?= $t['id'] ?></span>
                        <span class="text-[9px] text-primary font-bold uppercase tracking-widest"><?= htmlspecialchars($t['project_name'] ?: 'Global Support') ?></span>
                    </div>
                    <h3 class="text-sm font-bold text-on-surface font-headline truncate mb-1"><?= htmlspecialchars($t['subject']) ?></h3>
                    <p class="text-slate-500 text-xs truncate opacity-70"><?= htmlspecialchars($t['description']) ?></p>
                </div>
                <div class="flex items-center gap-6 shrink-0">
                    <div class="text-right">
                        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest leading-none mb-1">Status</p>
                        <p class="text-[10px] font-bold <?= $t['status'] == 'Resolved' ? 'text-emerald-500' : 'text-primary' ?>"><?= strtoupper($t['status']) ?></p>
                    </div>
                    <button onclick="openThread(<?= $t['id'] ?>)" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-primary hover:text-on-primary transition-all group/btn">
                        <span class="material-symbols-outlined text-lg">forum</span>
                    </button>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="p-6 sm:p-12 text-center bg-white rounded-2xl border border-dashed border-slate-200">
            <span class="material-symbols-outlined text-3xl sm:text-4xl text-slate-300 mb-4">confirmation_number</span>
            <p class="text-slate-400 italic text-xs sm:text-sm">No tickets found. Need help? Create a new ticket.</p>
        </div>
        <?php endif; ?>
    </div>

    <div class="xl:col-span-4 space-y-8">
        <div class="bg-slate-900 rounded-2xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <span class="material-symbols-outlined text-6xl">support_agent</span>
            </div>
            <h2 class="font-headline text-lg font-bold mb-4">Support Overview</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Open Tickets</span>
                    <span class="text-3xl font-bold font-headline"><?= safe_query($conn, "SELECT COUNT(*) FROM tickets WHERE client_id = ? AND status != 'Resolved'", "i", [$client_id])->fetch_row()[0] ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold">Resolved</span>
                    <span class="text-3xl font-bold font-headline text-emerald-400"><?= safe_query($conn, "SELECT COUNT(*) FROM tickets WHERE client_id = ? AND status = 'Resolved'", "i", [$client_id])->fetch_row()[0] ?></span>
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
<?php
$page_content = ob_get_clean();

$page_after_main = '
<div id="threadOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-[70] hidden transition-opacity duration-300 opacity-0" onclick="closeThread()"></div>
<div id="threadPanel" class="fixed top-0 right-0 h-full w-full max-w-xl bg-white z-[80] shadow-2xl transform translate-x-full transition-transform duration-500 ease-out flex flex-col">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
        <div>
            <span id="threadIdBadge" class="text-[10px] font-bold text-primary font-headline uppercase tracking-widest">#TK-000</span>
            <h2 id="threadSubject" class="text-xl font-bold font-headline text-slate-900 line-clamp-1">Ticket Subject</h2>
        </div>
        <button onclick="closeThread()" class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div id="threadContent" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-slate-50/50"></div>
    <div id="threadReplyArea" class="p-6 border-t border-slate-100 bg-white shrink-0">
        <form id="threadReplyForm" class="space-y-4">
            <input type="hidden" id="replyTicketId">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <textarea id="replyMessage" rows="2" placeholder="Type your message..." required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-xs focus:ring-2 focus:ring-primary/20 transition-all outline-none resize-none"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="file" id="replyAttachment" class="hidden" onchange="updateFileLabel(this)" />
                    <button type="button" onclick="document.getElementById(\'replyAttachment\').click()" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">attach_file</span>
                    </button>
                    <button type="submit" id="sendReplyBtn" class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors shrink-0 shadow-lg shadow-slate-200">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </div>
            </div>
            <div id="fileLabel" class="text-[10px] text-slate-400 font-bold hidden px-2"></div>
        </form>
    </div>
</div>
<div id="newTicketModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 hidden p-4">
    <div class="bg-white rounded-2xl p-4 sm:p-6 lg:p-8 w-full max-w-xl mx-auto max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl sm:text-2xl font-headline font-bold">Create New Ticket</h2>
            <button onclick="document.getElementById(\'newTicketModal\').classList.add(\'hidden\')" class="material-symbols-outlined text-slate-400 hover:text-on-surface">close</button>
        </div>
        <form method="POST" class="space-y-4">
            ' . get_csrf_field() . '
            <input type="hidden" name="create_ticket" value="1">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Subject</label>
                <input type="text" name="subject" required class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Priority</label>
                    <select name="priority" class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Project (Optional)</label>
                    <select name="project_id" class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20">
                        <option value="0">General Support</option>
                        ' . implode('', array_map(function($p) {
                            return '<option value="' . $p['id'] . '">' . htmlspecialchars($p['name']) . '</option>';
                        }, (function() use ($conn, $client_id) {
                            $res = safe_query($conn, "SELECT id, name FROM projects WHERE client_id = ?", "i", [$client_id]);
                            return $res->fetch_all(MYSQLI_ASSOC);
                        })())) . '
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Description</label>
                <textarea name="description" rows="4" required class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20" placeholder="Please provide details about your issue..."></textarea>
            </div>
            <button type="submit" class="w-full py-4 bg-primary text-on-primary rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Submit Ticket</button>
        </form>
    </div>
</div>
';

$page_scripts = '
<script>
    let currentTicketId = null;
    let pollInterval = null;

    async function openThread(id) {
        currentTicketId = id;
        document.getElementById("replyTicketId").value = id;
        const overlay = document.getElementById("threadOverlay");
        const panel = document.getElementById("threadPanel");
        const content = document.getElementById("threadContent");
        overlay.classList.remove("hidden");
        setTimeout(() => overlay.classList.replace("opacity-0", "opacity-100"), 10);
        panel.classList.remove("translate-x-full");
        content.innerHTML = \'<div class="h-full flex flex-col items-center justify-center space-y-4"><span class="material-symbols-outlined text-4xl text-primary animate-spin">sync</span><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Fetching Conversation...</p></div>\';
        try {
            const res = await fetch("fetch_ticket_thread.php?id=" + id);
            const data = await res.json();
            if (data.status === "success") {
                document.getElementById("threadIdBadge").innerText = "#TK-" + data.ticket.id;
                document.getElementById("threadSubject").innerText = data.ticket.subject;
                renderThread(data.ticket, data.replies);
                if (pollInterval) clearInterval(pollInterval);
                pollInterval = setInterval(refreshThread, 15000);
            }
        } catch(err) {
            content.innerHTML = \'<div class="text-center p-12"><p class="text-red-500 font-bold">Failed to load conversation.</p></div>\';
        }
    }

    function updateFileLabel(input) {
        const label = document.getElementById("fileLabel");
        if (input.files && input.files[0]) { label.innerText = "Attached: " + input.files[0].name; label.classList.remove("hidden"); }
        else { label.classList.add("hidden"); }
    }

    function closeThread() {
        const overlay = document.getElementById("threadOverlay");
        const panel = document.getElementById("threadPanel");
        overlay.classList.replace("opacity-100", "opacity-0");
        panel.classList.add("translate-x-full");
        setTimeout(() => overlay.classList.add("hidden"), 300);
        if (pollInterval) clearInterval(pollInterval);
        currentTicketId = null;
    }

    function renderThread(ticket, replies) {
        const content = document.getElementById("threadContent");
        let html = \'<div class="flex flex-col items-start mb-8"><div class="flex items-center gap-2 mb-1"><span class="text-xs font-bold text-slate-900">You</span><span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">\' + ticket.created_at + \'</span></div><div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-5 max-w-[85%] shadow-sm"><p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">\' + ticket.description + \'</p></div></div>\';
        replies.forEach(r => {
            const isAdmin = r.sender_type === "Admin";
            let attachHtml = "";
            if (r.attachment) {
                const path = "../uploads/tickets/" + r.attachment;
                const ext = r.attachment.split(".").pop().toLowerCase();
                const isImg = ["jpg","jpeg","png","gif","webp"].includes(ext);
                if (isImg) { attachHtml = \'<a href="\' + path + \'" target="_blank" class="block mt-2 rounded-lg overflow-hidden border border-slate-100"><img src="\' + path + \'" class="max-w-xs h-auto" /></a>\'; }
                else { attachHtml = \'<a href="\' + path + \'" target="_blank" class="flex items-center gap-2 mt-2 p-2 rounded-lg bg-slate-50 text-[10px] font-bold uppercase text-slate-500"><span class="material-symbols-outlined text-sm">description</span> View Attachment</a>\'; }
            }
            html += \'<div class="flex flex-col \' + (isAdmin ? "items-start" : "items-end") + \' mb-6"><div class="flex items-center gap-2 mb-1">\' + (isAdmin ? \'<span class="text-xs font-bold text-slate-900">\' + r.sender_name + \'</span>\' : "") + \'<span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">\' + r.created_at + \'</span>\' + (!isAdmin ? \'<span class="text-xs font-bold text-slate-900">You</span>\' : "") + \'</div><div class="\' + (isAdmin ? "bg-white border border-slate-100 rounded-tl-none" : "bg-primary text-on-primary rounded-tr-none shadow-lg shadow-primary/10") + \' rounded-2xl p-4 max-w-[85%]">\' + (r.message ? \'<p class="text-sm leading-relaxed whitespace-pre-wrap \' + (isAdmin ? "text-slate-700" : "font-medium") + \'">\' + r.message + \'</p>\' : "") + attachHtml + \'</div></div>\';
        });
        content.innerHTML = html;
        content.scrollTop = content.scrollHeight;
    }

    async function refreshThread() {
        if (!currentTicketId) return;
        const res = await fetch("fetch_ticket_thread.php?id=" + currentTicketId);
        const data = await res.json();
        if (data.status === "success") renderThread(data.ticket, data.replies);
    }

    document.getElementById("threadReplyForm").onsubmit = async function(e) {
        e.preventDefault();
        const msgInput = document.getElementById("replyMessage");
        const btn = document.getElementById("sendReplyBtn");
        btn.disabled = true;
        btn.innerHTML = \'<span class="material-symbols-outlined animate-spin text-sm">sync</span>\';
        const fd = new FormData();
        fd.append("ticket_id", currentTicketId);
        fd.append("message", msgInput.value);
        const fileInput = document.getElementById("replyAttachment");
        if (fileInput.files[0]) fd.append("attachment", fileInput.files[0]);
        try {
            const res = await fetch("add_ticket_reply.php", { method: "POST", body: fd });
            const data = await res.json();
            if (data.status === "success") {
                msgInput.value = "";
                document.getElementById("replyAttachment").value = "";
                document.getElementById("fileLabel").classList.add("hidden");
                refreshThread();
            }
        } catch(err) { console.error(err); }
        finally { btn.disabled = false; btn.innerHTML = \'<span class="material-symbols-outlined">send</span>\'; }
    };
</script>
';

require_once __DIR__ . '/../components/client_layout.php';
