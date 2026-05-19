<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

function send_staff_notification($to_email, $to_name, $is_new, $password = null) {
    $smtp_from_name = get_setting('smtp_from_name') ?: 'Wilsolvewel Engineering';
    if ($is_new) {
        $subject = 'Welcome to Wilsolvewel Engineering Terminal';
        $html = email_template('Welcome to the Team', '<p>Hello ' . htmlspecialchars($to_name) . ',</p><p>Your staff account has been created on the <strong>Wilsolvewel Engineering Terminal</strong>.</p><p><strong>Temporary Password:</strong> <code style="background:#F1F5F9;padding:4px 8px;border-radius:4px;font-size:14px">' . htmlspecialchars($password ?: 'staff123') . '</code></p><p>Please login and change your password immediately.</p>');
    } else {
        $subject = 'Your Profile Has Been Updated';
        $html = email_template('Profile Updated', '<p>Hello ' . htmlspecialchars($to_name) . ',</p><p>Your profile on the <strong>Wilsolvewel Engineering Terminal</strong> has been updated by an administrator.</p><p>If you did not expect this change, please contact your administrator.</p>');
    }
    return send_email($to_email, $subject, $html);
}

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'save_staff') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'Staff');
        $dept_id = (int)($_POST['department_id'] ?? 0);
        $status = $_POST['status'] ?? 'Active';
        $raw_pass = trim($_POST['password'] ?? '');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrf_token)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or expired security token. Please reload the page.']);
            exit;
        }

        if (empty($name) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Name and email are required.']); exit;
        }

        if ($id > 0) {
            if (!empty($raw_pass)) {
                $pass = password_hash($raw_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, password=?, role=?, department_id=?, status=? WHERE id=?");
                $stmt->bind_param("ssssiii", $name, $email, $pass, $role, $dept_id, $status, $id);
            } else {
                $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, role=?, department_id=?, status=? WHERE id=?");
                $stmt->bind_param("sssiii", $name, $email, $role, $dept_id, $status, $id);
            }
            $stmt->execute();
            if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); exit; }
            $stmt->close();
            send_staff_notification($email, $name, false);
            log_audit($conn, 'Update', 'Staff', 'Admin', $admin_id, "Updated staff member: $name (ID: $id)");
            echo json_encode(['status' => 'success', 'message' => 'Staff member updated.']);
        } else {
            $tmp_pass = !empty($raw_pass) ? $raw_pass : 'staff123';
            $hashed = password_hash($tmp_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (name, email, password, role, department_id, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssis", $name, $email, $hashed, $role, $dept_id, $status);
            $stmt->execute();
            if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); exit; }
            $new_id = $stmt->insert_id;
            $stmt->close();
            send_staff_notification($email, $name, true, $tmp_pass);
            log_audit($conn, 'Create', 'Staff', 'Admin', $admin_id, "Created new staff member: $name (ID: $new_id)");
            echo json_encode(['status' => 'success', 'message' => 'Staff member created and notified.']);
        }
        exit;
    }

    if ($_GET['ajax_action'] == 'get_staff') {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res->fetch_assoc());
        exit;
    }

    if ($_POST['ajax_action'] == 'delete_staff') {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrf_token)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or expired security token.']);
            exit;
        }
        $id = (int)$_POST['id'];
        if ($id == 1) { echo json_encode(['status' => 'error', 'message' => 'Cannot delete main administrator.']); exit; }
        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        log_audit($conn, 'Delete', 'Staff', 'Admin', $admin_id, "Deleted staff member ID: $id");
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$depts = [];
$res = $conn->query("SELECT * FROM departments ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $depts[] = $row;

$staff = [];
$res = $conn->query("SELECT a.*, d.name as dept_name FROM admins a LEFT JOIN departments d ON a.department_id = d.id ORDER BY a.created_at DESC");
while ($row = $res->fetch_assoc()) $staff[] = $row;

$permissions = get_admin_permissions($admin_id);
$is_director = ($permissions['role'] ?? '') === 'Director';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Staff Management | Terminal</title>
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

<!-- Toast -->
<div id="toast" class="fixed top-6 right-6 z-[400] transform translate-x-[150%] transition-transform duration-300 pointer-events-none">
    <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span id="toastIcon" class="material-symbols-outlined">check_circle</span>
        <p id="toastMessage" class="text-xs font-bold"></p>
    </div>
</div>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Staff Management</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Internal Team Control</p>
        </div>
        <button onclick="openStaffModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">person_add</span> ADD NEW STAFF
        </button>
    </header>

    <main class="flex-1 overflow-y-auto custom-scrollbar p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($staff as $s): ?>
                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 group hover:border-primary/20 transition-all relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-primary/5 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-all"></div>
                    <div class="flex items-center gap-4 mb-6 relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-slate-900 flex items-center justify-center font-bold text-white text-xl uppercase">
                            <?php echo strtoupper(substr($s['name'], 0, 1) . (strpos($s['name'], ' ') ? substr($s['name'], strpos($s['name'],' ')+1, 1) : '')); ?>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 truncate"><?php echo htmlspecialchars($s['name']); ?></h3>
                            <p class="text-[10px] font-bold text-primary uppercase tracking-widest"><?php echo htmlspecialchars($s['role'] ?? 'Staff'); ?></p>
                        </div>
                    </div>
                    <div class="space-y-3 mb-8 relative z-10">
                        <div class="flex items-center gap-3 text-xs text-slate-500"><span class="material-symbols-outlined text-base text-slate-300">mail</span><?php echo htmlspecialchars($s['email']); ?></div>
                        <div class="flex items-center gap-3 text-xs text-slate-500"><span class="material-symbols-outlined text-base text-slate-300">corporate_fare</span><?php echo htmlspecialchars($s['dept_name'] ?: 'Unassigned'); ?></div>
                        <div class="flex items-center gap-3 text-xs font-bold <?php echo ($s['status']??'Active')=='Active'?'text-emerald-500':'text-red-400'; ?>">
                            <div class="w-1.5 h-1.5 rounded-full <?php echo ($s['status']??'Active')=='Active'?'bg-emerald-500':'bg-red-400'; ?>"></div>
                            <?php echo strtoupper($s['status'] ?? 'ACTIVE'); ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 relative z-10">
                        <a href="staff_overview.php?id=<?php echo $s['id']; ?>" class="col-span-2 py-2.5 rounded-xl bg-slate-900 text-[10px] font-bold text-white uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">visibility</span> Overview
                        </a>
                        <button onclick="editStaff(<?php echo $s['id']; ?>)" class="py-2.5 rounded-xl border border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:bg-slate-50 hover:text-slate-900 transition-all flex items-center justify-center">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                        <?php if (isset($is_director) && $is_director && $s['id'] != $admin_id): ?>
                        <form method="POST" action="login_as.php" class="inline">
                            <input type="hidden" name="action" value="login_as">
                            <input type="hidden" name="staff_id" value="<?php echo $s['id']; ?>">
                            <button type="submit" title="Login As" class="w-full py-2.5 rounded-xl bg-primary/10 text-primary border border-primary/20 text-[10px] font-bold uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm">login</span>
                            </button>
                        </form>
                        <?php else: ?>
                        <div></div>
                        <?php endif; ?>
                        <button onclick="deleteStaff(<?php echo $s['id']; ?>)" class="col-span-4 py-2 rounded-xl bg-red-50 text-red-400 text-[9px] font-bold uppercase tracking-[0.2em] hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100 mt-1">Delete Member</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($staff)): ?>
                <div class="col-span-3 py-20 text-center"><span class="material-symbols-outlined text-4xl text-slate-200">group_off</span><p class="text-xs font-bold text-slate-400 mt-3 uppercase tracking-widest">No staff found</p></div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Staff Modal — using class-based open/close, no hidden+flex conflict -->
<div id="staffModal" class="modal-overlay">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
            <div>
                <h3 id="modalTitle" class="font-bold text-xl text-slate-900 font-headline">Register Staff</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Internal Terminal Profile</p>
            </div>
            <button onclick="closeStaffModal()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="overflow-y-auto custom-scrollbar flex-1">
            <form id="staffForm" class="p-8 space-y-5">
                <?= get_csrf_field() ?>
                <input type="hidden" name="id" id="staffId">
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                        <input type="text" name="name" id="staffName" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Terminal Role</label>
                        <input type="text" name="role" id="staffRole" placeholder="e.g. Director" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                    <input type="email" name="email" id="staffEmail" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Department</label>
                        <select name="department_id" id="staffDept" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="0">Unassigned</option>
                            <?php foreach ($depts as $d): ?>
                                <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" id="staffStatus" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Active">Active</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password <span class="normal-case text-slate-300">(blank keeps existing)</span></label>
                    <input type="password" name="password" id="staffPass" placeholder="Min 6 characters" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeStaffModal()" class="flex-1 py-4 rounded-2xl border border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Cancel</button>
                    <button type="submit" id="staffSaveBtn" class="flex-1 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-bold uppercase tracking-[0.2em]">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN = '<?= generate_csrf_token() ?>';

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    document.getElementById('toastMessage').innerText = msg;
    document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'error';
    document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] ${type==='success'?'bg-slate-900 text-white':'bg-red-600 text-white'}`;
    t.style.transform = 'translateX(0)';
    setTimeout(() => t.style.transform = 'translateX(150%)', 4000);
}

function openStaffModal() {
    document.getElementById('modalTitle').innerText = 'Register Staff';
    document.getElementById('staffForm').reset();
    document.getElementById('staffId').value = '';
    document.getElementById('staffModal').classList.add('open');
}

function closeStaffModal() {
    document.getElementById('staffModal').classList.remove('open');
}

async function editStaff(id) {
    const res = await fetch(`?ajax_action=get_staff&id=${id}`);
    const data = await res.json();
    document.getElementById('modalTitle').innerText = 'Edit Staff Member';
    document.getElementById('staffId').value = data.id;
    document.getElementById('staffName').value = data.name;
    document.getElementById('staffEmail').value = data.email;
    document.getElementById('staffRole').value = data.role || '';
    document.getElementById('staffDept').value = data.department_id || 0;
    document.getElementById('staffStatus').value = data.status || 'Active';
    document.getElementById('staffPass').value = '';
    document.getElementById('staffModal').classList.add('open');
}

document.getElementById('staffForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('staffSaveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';
    try {
        const fd = new FormData(this);
        const res = await fetch('?ajax_action=save_staff', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') {
            closeStaffModal();
            showToast(result.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(result.message, 'error');
        }
    } catch(err) {
        showToast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Profile';
    }
});

async function deleteStaff(id) {
    if (!confirm('Delete staff member? This cannot be undone.')) return;
    const fd = new FormData();
    fd.append('ajax_action', 'delete_staff');
    fd.append('id', id);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('', { method: 'POST', body: fd });
    const result = await res.json();
    if (result.status === 'success') location.reload();
    else showToast(result.message, 'error');
}
</script>
</body>
</html>
