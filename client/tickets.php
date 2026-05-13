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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['create_ticket']) || isset($_POST['ajax_ticket']))) {
    $subject = $conn->real_escape_string($_POST['subject']);
    $priority = $conn->real_escape_string($_POST['priority'] ?? 'Normal');
    $description = $conn->real_escape_string($_POST['description']);
    $project_id = (int)($_POST['project_id'] ?? 0);
    $order_id = (int)($_POST['order_id'] ?? 0);

    // Auto-Routing Logic
    $dept_id = get_auto_assigned_department($conn, 'ticket', $subject . ' ' . $description);

    $sql = "INSERT INTO tickets (client_id, project_id, order_id, department_id, subject, priority, description, status) 
            VALUES ($client_id, " . ($project_id ?: "NULL") . ", " . ($order_id ?: "NULL") . ", " . ($dept_id ?: "NULL") . ", '$subject', '$priority', '$description', 'Open')";
    
    if ($conn->query($sql)) {
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
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
            exit;
        }
        $message = "Error creating ticket: " . $conn->error;
    }
}

// Fetch Tickets
$tickets_res = $conn->query("
    SELECT t.*, p.name as project_name, d.name as department_name
    FROM tickets t 
    LEFT JOIN projects p ON t.project_id = p.id 
    LEFT JOIN departments d ON t.department_id = d.id
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
    <!-- TopNavBar -->
    <script src="../components/client_topnav.js" data-root="../"></script>
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

    <main class="pt-20 pb-8 px-6 relative z-10">
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

    <!-- Thread Sliding Panel -->
    <div id="threadOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-[70] hidden transition-opacity duration-300 opacity-0" onclick="closeThread()"></div>
    <div id="threadPanel" class="fixed top-0 right-0 h-full w-full max-w-xl bg-white z-[80] shadow-2xl transform translate-x-full transition-transform duration-500 ease-out flex flex-col">
        <!-- Panel Header -->
        <div class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div>
                <span id="threadIdBadge" class="text-[10px] font-bold text-primary font-headline uppercase tracking-widest">#TK-000</span>
                <h2 id="threadSubject" class="text-xl font-bold font-headline text-slate-900 line-clamp-1">Ticket Subject</h2>
            </div>
            <button onclick="closeThread()" class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Panel Content (Chat Area) -->
        <div id="threadContent" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-slate-50/50">
            <!-- Messages load here -->
        </div>

        <!-- Reply Area -->
        <div id="threadReplyArea" class="p-6 border-t border-slate-100 bg-white shrink-0">
            <form id="threadReplyForm" class="space-y-4">
                <input type="hidden" id="replyTicketId">
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <textarea id="replyMessage" rows="2" placeholder="Type your message..." required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-xs focus:ring-2 focus:ring-primary/20 transition-all outline-none resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="file" id="replyAttachment" class="hidden" onchange="updateFileLabel(this)" />
                        <button type="button" onclick="document.getElementById('replyAttachment').click()" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 transition-colors">
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
                            <option value="Medium" selected>Medium</option>
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
                    <textarea name="description" rows="4" required class="w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-primary/20" placeholder="Please provide details about your issue..."></textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-primary text-on-primary rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Submit Ticket</button>
            </form>
        </div>
    </div>

    <script>
        let currentTicketId = null;
        let pollInterval = null;

        async function openThread(id) {
            currentTicketId = id;
            document.getElementById('replyTicketId').value = id;
            
            const overlay = document.getElementById('threadOverlay');
            const panel = document.getElementById('threadPanel');
            const content = document.getElementById('threadContent');
            
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.replace('opacity-0', 'opacity-100'), 10);
            panel.classList.remove('translate-x-full');
            
            content.innerHTML = '<div class="h-full flex flex-col items-center justify-center space-y-4"><span class="material-symbols-outlined text-4xl text-primary animate-spin">sync</span><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Fetching Conversation...</p></div>';
            
            try {
                const res = await fetch(`fetch_ticket_thread.php?id=${id}`);
                const data = await res.json();
                
                if (data.status === 'success') {
                    document.getElementById('threadIdBadge').innerText = `#TK-${data.ticket.id}`;
                    document.getElementById('threadSubject').innerText = data.ticket.subject;
                    
                    renderThread(data.ticket, data.replies);
                    
                    if (pollInterval) clearInterval(pollInterval);
                    pollInterval = setInterval(refreshThread, 15000);
                }
            } catch(err) {
                content.innerHTML = '<div class="text-center p-12"><p class="text-red-500 font-bold">Failed to load conversation.</p></div>';
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

        function closeThread() {
            const overlay = document.getElementById('threadOverlay');
            const panel = document.getElementById('threadPanel');
            
            overlay.classList.replace('opacity-100', 'opacity-0');
            panel.classList.add('translate-x-full');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            
            if (pollInterval) clearInterval(pollInterval);
            currentTicketId = null;
        }

        function renderThread(ticket, replies) {
            const content = document.getElementById('threadContent');
            let html = `
                <!-- Initial Message -->
                <div class="flex flex-col items-start mb-8">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-slate-900">You</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${ticket.created_at}</span>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-2xl rounded-tl-none p-5 max-w-[85%] shadow-sm">
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">${ticket.description}</p>
                    </div>
                </div>
            `;
            
            replies.forEach(r => {
                const isAdmin = r.sender_type === 'Admin';
                let attachHtml = '';
                if (r.attachment) {
                    const path = '../uploads/tickets/' + r.attachment;
                    const ext = r.attachment.split('.').pop().toLowerCase();
                    const isImg = ['jpg','jpeg','png','gif','webp'].includes(ext);
                    if (isImg) {
                        attachHtml = `<a href="${path}" target="_blank" class="block mt-2 rounded-lg overflow-hidden border border-slate-100"><img src="${path}" class="max-w-xs h-auto" /></a>`;
                    } else {
                        attachHtml = `<a href="${path}" target="_blank" class="flex items-center gap-2 mt-2 p-2 rounded-lg bg-slate-50 text-[10px] font-bold uppercase text-slate-500"><span class="material-symbols-outlined text-sm">description</span> View Attachment</a>`;
                    }
                }

                html += `
                    <div class="flex flex-col ${isAdmin ? 'items-start' : 'items-end'} mb-6">
                        <div class="flex items-center gap-2 mb-1">
                            ${isAdmin ? `<span class="text-xs font-bold text-slate-900">${r.sender_name}</span>` : ''}
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${r.created_at}</span>
                            ${!isAdmin ? `<span class="text-xs font-bold text-slate-900">You</span>` : ''}
                        </div>
                        <div class="${isAdmin ? 'bg-white border border-slate-100 rounded-tl-none' : 'bg-primary text-on-primary rounded-tr-none shadow-lg shadow-primary/10'} rounded-2xl p-4 max-w-[85%]">
                            ${r.message ? `<p class="text-sm leading-relaxed whitespace-pre-wrap ${isAdmin ? 'text-slate-700' : 'font-medium'}">${r.message}</p>` : ''}
                            ${attachHtml}
                        </div>
                    </div>
                `;
            });
            
            content.innerHTML = html;
            content.scrollTop = content.scrollHeight;
        }

        async function refreshThread() {
            if (!currentTicketId) return;
            const res = await fetch(`fetch_ticket_thread.php?id=${currentTicketId}`);
            const data = await res.json();
            if (data.status === 'success') {
                renderThread(data.ticket, data.replies);
            }
        }

        document.getElementById('threadReplyForm').onsubmit = async function(e) {
            e.preventDefault();
            const msgInput = document.getElementById('replyMessage');
            const btn = document.getElementById('sendReplyBtn');
            const message = msgInput.value;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">sync</span>';
            
            const fd = new FormData();
            fd.append('ticket_id', currentTicketId);
            fd.append('message', message);
            const fileInput = document.getElementById('replyAttachment');
            if (fileInput.files[0]) {
                fd.append('attachment', fileInput.files[0]);
            }
            
            try {
                const res = await fetch('add_ticket_reply.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.status === 'success') {
                    msgInput.value = '';
                    const fileInput = document.getElementById('replyAttachment');
                    fileInput.value = '';
                    document.getElementById('fileLabel').classList.add('hidden');
                    refreshThread();
                }
            } catch(err) {
                console.error(err);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined">send</span>';
            }
        };
    </script>
</body>
</html>
