<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();

// Handle AJAX actions
if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');
    $id = (int)$_GET['id'];

    $requires_csrf = ['update_assignment', 'update_status', 'mark_viewed', 'forward', 'send_reply', 'delete'];
    if (in_array($_GET['ajax_action'], $requires_csrf)) {
        verify_csrf_token($_POST['csrf_token'] ?? '') || csrf_error_response();
    }

    if ($_GET['ajax_action'] == 'update_assignment') {
        $stmt = $conn->prepare("UPDATE inquiries SET assigned_to = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['assigned_to'], $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'update_status') {
        $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['status'], $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'mark_viewed') {
        $stmt = $conn->prepare("SELECT viewed_by FROM inquiries WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $viewers = json_decode($row['viewed_by'] ?? '[]', true);
        $viewer = $_POST['viewer'];
        if (!in_array($viewer, $viewers)) {
            $viewers[] = $viewer;
            $v_json = json_encode($viewers);
            $stmt = $conn->prepare("UPDATE inquiries SET viewed_by = ? WHERE id = ?");
            $stmt->bind_param("si", $v_json, $id);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(['status' => 'success', 'viewers' => $viewers]);
        exit;
    }

    if ($_GET['ajax_action'] == 'forward') {
        $smtp_status = test_smtp_connection();
        if (!$smtp_status['status']) {
            echo json_encode(['status' => 'error', 'message' => $smtp_status['message']]);
            exit;
        }
        $inquiry = safe_query($conn, "SELECT name, type, message FROM inquiries WHERE id = ?", "i", [$id])->fetch_assoc();
        $subject = 'Fwd: ' . $inquiry['type'] . ' inquiry from ' . $inquiry['name'];
        $html = email_template('Forwarded Inquiry', '<p>An inquiry has been forwarded to you:</p><p><strong>Type:</strong> ' . htmlspecialchars($inquiry['type']) . '</p><p><strong>From:</strong> ' . htmlspecialchars($inquiry['name']) . '</p><p><strong>Message:</strong></p><blockquote style="border-left:3px solid #EAB308;padding-left:1em;color:#475569">' . nl2br(htmlspecialchars($inquiry['message'])) . '</blockquote>');
        $sent = send_email($_POST['email'], $subject, $html);
        $stmt = $conn->prepare("UPDATE inquiries SET forwarded_to = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['email'], $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => ($sent ? 'Forwarded' : 'Failed to send') . ' successfully to ' . $_POST['email']]);
        exit;
    }

    if ($_GET['ajax_action'] == 'send_reply') {
        $smtp_status = test_smtp_connection();
        if (!$smtp_status['status']) {
            echo json_encode(['status' => 'error', 'message' => $smtp_status['message']]);
            exit;
        }
        $inquiry = safe_query($conn, "SELECT name, type, message FROM inquiries WHERE id = ?", "i", [$id])->fetch_assoc();
        $reply_body = $_POST['message'] ?? '';
        $subject = 'Re: ' . $inquiry['type'] . ' inquiry';
        $html = email_template('Reply to Your Inquiry', '<p>Hello ' . htmlspecialchars($inquiry['name']) . ',</p><p>Regarding your <strong>' . htmlspecialchars($inquiry['type']) . '</strong> inquiry:</p><div style="background:#F8FAFC;border-radius:12px;padding:20px;margin:16px 0">' . nl2br(htmlspecialchars($reply_body)) . '</div>');
        $sent = send_email($_POST['to'], $subject, $html);
        $stmt = $conn->prepare("UPDATE inquiries SET status = 'Replied' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => ($sent ? 'Reply sent' : 'Failed to send reply') . ' to ' . $_POST['to']]);
        exit;
    }

    if ($_GET['ajax_action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Fallback Action
if (isset($_POST['action']) && isset($_POST['id'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '') || csrf_error_response();
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
    $status = $_POST['status'] ?? 'New';
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: inquiries.php");
    exit;
}

// Session & Permissions
$admin_id = $_SESSION['admin_id'];

$stmt = $conn->prepare("SELECT name FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_res = $stmt->get_result();
$current_admin_name = ($admin_res && $row = $admin_res->fetch_assoc()) ? $row['name'] : 'Admin';
$stmt->close();
$admin_initials = strtoupper(substr($current_admin_name, 0, 1) . (strpos($current_admin_name, ' ') ? substr($current_admin_name, strpos($current_admin_name, ' ')+1, 1) : ''));

$permissions = get_admin_permissions($admin_id);

$stmt = $conn->prepare("SELECT name FROM departments ORDER BY name ASC");
$stmt->execute();
$dept_res = $stmt->get_result();
$departments = [];
while ($d = $dept_res->fetch_assoc()) $departments[] = $d['name'];
$stmt->close();

$status_filter = $_GET['filter'] ?? 'All';
if ($status_filter != 'All') {
    $stmt = $conn->prepare("SELECT * FROM inquiries WHERE status = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $status_filter);
} else {
    $stmt = $conn->prepare("SELECT * FROM inquiries ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
$inquiries = [];
while ($row = $result->fetch_assoc()) $inquiries[] = $row;
$stmt->close();

$page_title = 'Inquiry Hub';
$page_subtitle = 'Engineering Terminal';
ob_start();
?>
<div class="max-w-xs w-full relative">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
    <input type="text" placeholder="Search records..." class="w-full bg-slate-50 border-none rounded-xl pl-10 pr-4 py-2 text-xs focus:ring-1 focus:ring-primary transition-all">
</div>
<div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100">
    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
    <span class="text-[9px] font-bold text-emerald-600 uppercase">Live</span>
</div>
<div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-on-primary font-bold text-xs"><?php echo $admin_initials; ?></div>
<?php
$page_header_actions = ob_get_clean();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Inquiry Hub | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>; const CURRENT_ADMIN = '<?php echo $admin_initials; ?>'; const CSRF_TOKEN = '<?= generate_csrf_token() ?>';</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet" />
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                "primary": "#EAB308",
                "on-primary": "#000000",
                "surface": "#F8FAFC",
                "on-surface": "#0F172A"
            },
            "fontFamily": {
                "headline": ["Space Grotesk"],
                "body": ["Manrope"]
            }
          },
        },
      }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        .inquiry-item.active { border-left: 3px solid #EAB308 !important; background: #FEF9C3 !important; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20; }
        .dropdown-menu { display: none; }
        .dropdown-menu.show { display: block; }
        
        @media (max-width: 1023px) {
            .hidden-mobile { display: none !important; }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface overflow-hidden h-screen lg:pl-64 flex">
    
    <script src="../components/admin_sidenav.js" data-root="../"></script>

    <div id="toast" class="fixed top-6 right-6 z-[400] transform translate-x-[150%] transition-transform duration-300 pointer-events-none">
        <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[300px]">
            <span id="toastIcon" class="material-symbols-outlined">info</span>
            <p id="toastMessage" class="text-xs font-bold"></p>
        </div>
    </div>

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

        <div class="flex-1 flex overflow-hidden p-3 lg:p-4 gap-4 relative z-10">
            <!-- List View -->
            <div id="colList" class="w-full lg:w-[320px] flex flex-col gap-3 overflow-hidden shrink-0 transition-all duration-300">
                <div class="flex justify-between items-center px-1">
                    <div>
                        <h2 class="text-xl font-bold font-headline text-slate-800">Messages</h2>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?php echo count($inquiries); ?> records</p>
                    </div>
                    <div class="relative">
                        <button onclick="toggleDropdown('filterDropdown')" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg border border-slate-200 hover:bg-slate-50 transition-all">
                            <span class="material-symbols-outlined text-slate-500">tune</span>
                        </button>
                        <div id="filterDropdown" class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 py-2 overflow-hidden animate-in fade-in zoom-in duration-200">
                            <p class="px-4 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50 mb-1">Filter by Status</p>
                            <a href="?filter=All" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">All Records</a>
                            <a href="?filter=New" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">New</a>
                            <a href="?filter=Archived" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">Archived</a>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar space-y-2.5 pr-1.5 pb-20">
                    <?php if (empty($inquiries)): ?>
                        <div class="bg-white p-8 rounded-2xl border border-slate-100 text-center">
                            <span class="material-symbols-outlined text-3xl text-slate-200 mb-2">inbox</span>
                            <p class="text-xs font-bold text-slate-400">No records found</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($inquiries as $index => $item): ?>
                        <div onclick="selectInquiry(<?php echo htmlspecialchars(json_encode($item)); ?>, this)" 
                             class="inquiry-item bg-white p-4 rounded-2xl shadow-sm border border-slate-100 cursor-pointer hover:shadow transition-all group">
                            <div class="flex gap-3">
                                <div class="w-11 h-11 rounded-xl bg-slate-50 flex items-center justify-center font-bold text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors shrink-0 text-sm uppercase">
                                    <?php 
                                        $parts = explode(' ', $item['name']);
                                        echo strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                    ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <h4 class="font-bold text-xs text-slate-900 truncate"><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <span class="text-[9px] text-slate-400 font-bold"><?php echo date('H:i', strtotime($item['created_at'])); ?></span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 truncate mb-1.5 font-medium"><?php echo htmlspecialchars($item['subject']); ?></p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-1 h-1 rounded-full <?php echo $item['status'] == 'New' ? 'bg-red-500' : ($item['status'] == 'Replied' ? 'bg-blue-500' : 'bg-slate-300'); ?>"></div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight"><?php echo $item['status']; ?></span>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-300 text-base group-hover:text-primary transition-colors">chevron_right</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Detail View -->
            <div id="colDetail" class="flex-1 bg-white rounded-[2rem] shadow-sm border border-slate-100 flex flex-col overflow-hidden hidden-mobile lg:flex transition-all duration-300">
                <div id="detailContent" class="flex-1 flex flex-col overflow-hidden hidden">
                    <div class="p-4 lg:p-6 border-b border-slate-50 flex justify-between items-center bg-white sticky top-0 z-20">
                        <div class="flex gap-3 lg:gap-4 items-center min-w-0">
                            <button onclick="backToList()" class="lg:hidden w-8 h-8 flex items-center justify-center hover:bg-slate-50 rounded-lg shrink-0">
                                <span class="material-symbols-outlined">arrow_back</span>
                            </button>
                            <div id="detailAvatar" class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-slate-900 flex items-center justify-center font-bold text-white text-lg lg:text-xl shrink-0 uppercase">??</div>
                            <div class="min-w-0">
                                <h3 id="detailName" class="text-sm lg:text-lg font-bold font-headline text-slate-900 truncate">...</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span id="detailRef" class="text-[8px] lg:text-[9px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">REF: #TR-0000-X</span>
                                    <span id="detailEmail" class="text-[10px] lg:text-[11px] text-primary font-bold truncate">...</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1 lg:gap-2 shrink-0">
                            <button onclick="archiveCurrent()" class="w-8 h-8 lg:w-9 lg:h-9 flex items-center justify-center hover:bg-slate-50 rounded-lg lg:rounded-xl border border-slate-100 text-slate-400 hover:text-primary transition-all" title="Archive"><span class="material-symbols-outlined text-xl">archive</span></button>
                            <button onclick="deleteCurrent()" class="w-8 h-8 lg:w-9 lg:h-9 flex items-center justify-center hover:bg-red-50 rounded-lg lg:rounded-xl border border-slate-100 text-slate-400 hover:text-red-500 transition-all" title="Delete"><span class="material-symbols-outlined text-xl">delete</span></button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-5 lg:p-6 space-y-8">
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Project Subject</p>
                            <h4 id="detailSubject" class="text-lg lg:text-xl font-bold text-slate-900 leading-tight">...</h4>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Communication Brief</p>
                            <div class="bg-slate-50/50 p-4 lg:p-6 rounded-2xl border border-slate-100 relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary/20"></div>
                                <p id="detailMessage" class="text-xs lg:text-[13px] text-slate-600 leading-relaxed italic whitespace-pre-wrap">...</p>
                            </div>
                        </div>
                        <div id="technicalPayload" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>

                        <div class="pt-8 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Assignment</p>
                                    <div class="relative w-full group">
                                        <button onclick="toggleDropdown('deptDropdown')" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 flex justify-between items-center text-xs font-bold text-slate-700 hover:bg-white transition-all">
                                            <span id="assignDisplay">Assign to Dept</span>
                                            <span class="material-symbols-outlined text-slate-400">expand_more</span>
                                        </button>
                                        <div id="deptDropdown" class="dropdown-menu absolute bottom-full left-0 mb-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 z-[100] py-2 overflow-hidden animate-in slide-in-from-bottom-2 duration-200">
                                            <button onclick="updateAssignment('Unassigned')" class="w-full text-left px-4 py-2.5 text-xs font-bold text-slate-400 hover:bg-slate-50">Unassigned</button>
                                            <?php foreach ($departments as $dept): ?>
                                                <button onclick="updateAssignment('<?php echo htmlspecialchars($dept); ?>')" class="w-full text-left px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50"><?php echo htmlspecialchars($dept); ?></button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Audit Log</p>
                                    <div id="viewedByList" class="flex -space-x-2.5 items-center"></div>
                                </div>
                            </div>
                            <div class="space-y-6 pb-10">
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Forward</p>
                                    <div class="flex gap-2 relative z-30">
                                        <input id="forwardEmail" type="email" placeholder="internal@wilsolvewel.com" class="flex-1 bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-medium focus:ring-1 focus:ring-primary transition-all">
                                        <button onclick="forwardInquiry(event)" class="bg-slate-900 text-white px-5 rounded-xl hover:bg-black transition-all flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-base">send</span>
                                        </button>
                                    </div>
                                </div>
                                <button onclick="openReplyModal()" class="w-full bg-primary text-on-primary py-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:shadow-lg transition-all active:scale-[0.98]">
                                    <span class="material-symbols-outlined text-base">mail</span>
                                    COMPOSE RESPONSE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Initial Placeholder -->
                <div id="detailPlaceholder" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50/30">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                        <span class="material-symbols-outlined text-4xl text-slate-200">mark_email_unread</span>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1 font-headline text-lg">Inquiry Terminal</h3>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto">Select a project brief from the list to begin review.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Compose Response Modal -->
    <div id="replyModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[250] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-slate-900">Compose Response</h3>
                <button onclick="closeReplyModal()" class="text-slate-400"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form id="replyForm" class="p-6 space-y-4 overflow-y-auto custom-scrollbar">
                <?= get_csrf_field() ?>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">To</label>
                    <input type="text" id="replyTo" readonly class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold text-slate-500">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Subject</label>
                    <input type="text" id="replySubject" placeholder="RE: Inquiry Details" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Message</label>
                    <textarea id="replyMessage" rows="5" placeholder="Type your response here..." class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-medium focus:ring-1 focus:ring-primary resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2 shrink-0">
                    <button type="button" onclick="closeReplyModal()" class="flex-1 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600">DISCARD</button>
                    <button type="button" id="replySubmitBtn" onclick="sendReply()" class="flex-1 py-3 rounded-2xl bg-primary text-on-primary text-xs font-bold shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">send</span> SEND REPLY
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentInquiryId = null;
        let currentInquiryData = null;

        function toggleDropdown(id) {
            const el = document.getElementById(id);
            const isShown = el.classList.contains('show');
            document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.remove('show'));
            if (!isShown) el.classList.add('show');
        }

        window.onclick = function(e) { if (!e.target.closest('.relative')) document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.remove('show')); }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = message;
            document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'warning';
            document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[300px] ${type === 'success' ? 'bg-slate-900 text-white' : 'bg-red-600 text-white'}`;
            toast.style.transform = 'translateX(0)';
            setTimeout(() => { toast.style.transform = 'translateX(150%)'; }, 5000);
        }

        function selectInquiry(data, element) {
            currentInquiryId = data.id; currentInquiryData = data;
            
            // Mobile Transition Logic
            if (window.innerWidth < 1024) {
                document.getElementById('colList').classList.add('hidden-mobile');
                document.getElementById('colDetail').classList.remove('hidden-mobile');
                document.getElementById('colDetail').classList.add('flex');
            }

            document.querySelectorAll('.inquiry-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            
            document.getElementById('detailPlaceholder').classList.add('hidden');
            document.getElementById('detailContent').classList.remove('hidden');

            document.getElementById('detailName').innerText = data.name;
            document.getElementById('detailEmail').innerText = data.email;
            document.getElementById('detailSubject').innerText = data.subject || "General Inquiry";
            document.getElementById('detailMessage').innerText = data.message;
            document.getElementById('detailRef').innerText = `REF: #TR-${data.id.toString().padStart(4, '0')}-X`;
            document.getElementById('assignDisplay').innerText = data.assigned_to || "Assign to Dept";
            
            const initials = data.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
            document.getElementById('detailAvatar').innerText = initials;
            
            renderViewers(data.viewed_by);
            renderSpecs(data.technical_data);
            markViewed(CURRENT_ADMIN);
        }

        function backToList() {
            document.getElementById('colList').classList.remove('hidden-mobile');
            document.getElementById('colDetail').classList.add('hidden-mobile');
            document.getElementById('colDetail').classList.remove('flex');
        }

        function renderSpecs(json) {
            const container = document.getElementById('technicalPayload'); container.innerHTML = '';
            try {
                const specs = JSON.parse(json);
                for (const [k, v] of Object.entries(specs)) {
                    container.innerHTML += `<div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex flex-col gap-1"><span class="text-[8px] font-bold text-primary uppercase">${k.replace(/_/g, ' ')}</span><span class="text-[11px] font-bold text-slate-800">${v}</span></div>`;
                }
            } catch(e) {}
        }

        function renderViewers(json) {
            const viewers = JSON.parse(json || '[]');
            document.getElementById('viewedByList').innerHTML = viewers.length ? viewers.map(v => `<div title="Viewed by ${v}" class="w-8 h-8 rounded-lg bg-primary border-2 border-white flex items-center justify-center text-[9px] font-bold text-on-primary shadow-sm uppercase shrink-0">${v[0]}</div>`).join('') : '<span class="text-[9px] font-bold text-slate-300 uppercase italic">Unread</span>';
        }

        async function updateAssignment(val) {
            const fd = new FormData(); fd.append('assigned_to', val); fd.append('csrf_token', CSRF_TOKEN);
            const res = await fetch(`?ajax_action=update_assignment&id=${currentInquiryId}`, { method: 'POST', body: fd });
            if (res.ok) { document.getElementById('assignDisplay').innerText = val; showToast(`Assigned to ${val}`); }
        }

        async function updateQuickStatus(s) {
            const fd = new FormData(); fd.append('status', s); fd.append('csrf_token', CSRF_TOKEN);
            const res = await fetch(`?ajax_action=update_status&id=${currentInquiryId}`, { method: 'POST', body: fd });
            if (res.ok) { showToast(`Status: ${s}`); setTimeout(() => location.reload(), 1000); }
        }

        async function forwardInquiry(e) {
            if(e) e.preventDefault();
            const email = document.getElementById('forwardEmail').value;
            if (!email || !currentInquiryId) return;
            showToast("Verifying SMTP connection...", "success");
            const fd = new FormData(); fd.append('email', email); fd.append('csrf_token', CSRF_TOKEN);
            const res = await fetch(`?ajax_action=forward&id=${currentInquiryId}`, { method: 'POST', body: fd });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status === 'success') document.getElementById('forwardEmail').value = '';
        }

        function openReplyModal() {
            if (!currentInquiryData) return;
            document.getElementById('replyTo').value = currentInquiryData.email;
            document.getElementById('replySubject').value = `RE: ${currentInquiryData.subject || 'Inquiry'}`;
            document.getElementById('replyModal').classList.remove('hidden');
        }

        function closeReplyModal() { document.getElementById('replyModal').classList.add('hidden'); }

        async function sendReply() {
            const btn = document.getElementById('replySubmitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">sync</span> VERIFYING...';
            const fd = new FormData();
            fd.append('to', document.getElementById('replyTo').value);
            fd.append('subject', document.getElementById('replySubject').value);
            fd.append('message', document.getElementById('replyMessage').value);
            fd.append('csrf_token', CSRF_TOKEN);
            const res = await fetch(`?ajax_action=send_reply&id=${currentInquiryId}`, { method: 'POST', body: fd });
            const result = await res.json();
            showToast(result.message, result.status);
            if (result.status === 'success') { closeReplyModal(); setTimeout(() => location.reload(), 1000); } else {
                btn.disabled = false; btn.innerHTML = '<span class="material-symbols-outlined text-sm">send</span> SEND REPLY';
            }
        }

        async function markViewed(v) {
            if (!currentInquiryId) return;
            const fd = new FormData(); fd.append('viewer', v); fd.append('csrf_token', CSRF_TOKEN);
            const res = await fetch(`?ajax_action=mark_viewed&id=${currentInquiryId}`, { method: 'POST', body: fd });
            const result = await res.json();
            if (result.viewers) renderViewers(JSON.stringify(result.viewers));
        }

        async function archiveCurrent() { if (currentInquiryId) { const fd = new FormData(); fd.append('action', 'update'); fd.append('id', currentInquiryId); fd.append('status', 'Archived'); fd.append('csrf_token', CSRF_TOKEN); await fetch(window.location.pathname, { method: 'POST', body: fd }); location.reload(); } }
        async function deleteCurrent() { if (currentInquiryId && confirm('Delete?')) { const fd = new FormData(); fd.append('csrf_token', CSRF_TOKEN); await fetch(`?ajax_action=delete&id=${currentInquiryId}`, { method: 'POST', body: fd }); location.reload(); } }

        window.onload = () => { if (window.innerWidth >= 1024) document.querySelector('.inquiry-item')?.click(); }
    </script>
</body>
</html>
