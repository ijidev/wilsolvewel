<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

function generate_setup_link($conn, $client_id) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+48 hours'));
    $stmt = $conn->prepare("INSERT INTO client_password_tokens (client_id, token, expires_at) VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE token=?, expires_at=?, used=0");
    $stmt->bind_param("issss", $client_id, $token, $expires, $token, $expires);
    $stmt->execute();
    $stmt->close();
    return 'http://' . $_SERVER['HTTP_HOST'] . '/client-setup.php?token=' . $token;
}

function send_client_setup_email($to_email, $to_name, $link) {
    $from_name = get_setting('smtp_from_name') ?: 'Wilsolvewel Engineering';
    $subject = 'Set Up Your Wilsolvewel Client Account';
    $html = email_template('Set Up Your Account', '<p>Hello ' . htmlspecialchars($to_name) . ',</p><p>Your client account has been created on the <strong>Wilsolvewel Engineering</strong> portal.</p><p>Click the button below to verify your email and set your password:</p><p style="margin-top:24px"><a href="' . $link . '" style="display:inline-block;background:#EAB308;color:#0F172A;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px">Set Up My Account</a></p><p style="margin-top:24px;color:#64748B;font-size:12px">This link expires in 48 hours. If you did not create this account, please ignore this email.</p>');
    return send_email($to_email, $subject, $html);
}

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'save_client') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) { echo json_encode(['status'=>'error','message'=>'Invalid CSRF token.']); exit; }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = $_POST['phone'] ?? '';
        $company = $_POST['company'] ?? '';
        $status = $_POST['status'] ?? 'Active';
        $pass_option = $_POST['pass_option'] ?? 'none';
        $raw_pass = trim($_POST['password'] ?? '');

        if (empty($name) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Name and email are required.']); exit;
        }

        if ($id > 0) {
            if ($pass_option === 'set' && !empty($raw_pass)) {
                $hashed = password_hash($raw_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE clients SET name=?, email=?, phone=?, company=?, status=?, password=? WHERE id=?");
                $stmt->bind_param("ssssssi", $name, $email, $phone, $company, $status, $hashed, $id);
            } else {
                $stmt = $conn->prepare("UPDATE clients SET name=?, email=?, phone=?, company=?, status=? WHERE id=?");
                $stmt->bind_param("sssssi", $name, $email, $phone, $company, $status, $id);
            }
            $stmt->execute();
            if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); exit; }
            $stmt->close();
            log_audit($conn, 'Update', 'Client', 'Admin', $admin_id, "Updated client record: $name (ID: $id)");
            send_email($email, 'Your profile has been updated', email_template('Profile Updated', '<p>Hello ' . htmlspecialchars($name) . ',</p><p>Your profile on the <strong>Wilsolvewel Engineering</strong> portal has been updated by an administrator.</p><p>If you did not expect this change, please contact support.</p>'));
            echo json_encode(['status' => 'success', 'message' => 'Client record updated.']);
        } else {
            $hashed = '';
            if ($pass_option === 'set' && !empty($raw_pass)) {
                $hashed = password_hash($raw_pass, PASSWORD_DEFAULT);
            }
            if (!empty($hashed)) {
                $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, company, status, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $name, $email, $phone, $company, $status, $hashed);
            } else {
                $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, company, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $phone, $company, $status);
            }
            $stmt->execute();
            if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); exit; }
            $new_id = $stmt->insert_id;
            $stmt->close();
            
            log_audit($conn, 'Create', 'Client', 'Admin', $admin_id, "Created new client record: $name (ID: $new_id)");

            if ($pass_option === 'link') {
                $link = generate_setup_link($conn, $new_id);
                $sent = send_client_setup_email($email, $name, $link);
                log_audit($conn, 'System', 'Client', 'Admin', $admin_id, "Sent setup link to client: $email");
                echo json_encode(['status' => 'success', 'message' => $sent ? 'Client created and setup email sent.' : 'Client created. Email failed — check SMTP settings.', 'setup_link' => $link]);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Client record created.']);
            }
        }
        exit;
    }

    if ($_GET['ajax_action'] == 'resend_link') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) { echo json_encode(['status'=>'error','message'=>'Invalid CSRF token.']); exit; }

        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $c = $res->fetch_assoc();
        $stmt->close();
        if (!$c) { echo json_encode(['status'=>'error','message'=>'Client not found.']); exit; }
        $link = generate_setup_link($conn, $id);
        $sent = send_client_setup_email($c['email'], $c['name'], $link);
        log_audit($conn, 'System', 'Client', 'Admin', $admin_id, "Resent setup link to client: " . $c['email']);
        echo json_encode(['status' => 'success', 'message' => $sent ? 'Setup link sent.' : 'Email failed — check SMTP.', 'setup_link' => $link]);
        exit;
    }

    if ($_GET['ajax_action'] == 'get_client') {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res->fetch_assoc());
        $stmt->close();
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_client') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) { echo json_encode(['status'=>'error','message'=>'Invalid CSRF token.']); exit; }

        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->error) { echo json_encode(['status'=>'error','message'=>$stmt->error]); exit; }
        $stmt->close();
        log_audit($conn, 'Delete', 'Client', 'Admin', $admin_id, "Deleted client record ID: $id");
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$clients = [];
$stmt = $conn->prepare("SELECT * FROM clients ORDER BY created_at DESC");
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $clients[] = $row;

$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Client Directory | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
    <script>window.CSRF_TOKEN = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES) ?>';</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(6px);z-index:200;display:none;align-items:center;justify-content:center;padding:1rem}
        .modal-overlay.open{display:flex}
        .pass-option{display:none}.pass-option.active{display:block}
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

<!-- Link copy toast -->
<div id="linkBox" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[400] bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl hidden max-w-sm w-full">
    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Setup Link (copy and share):</p>
    <p id="linkText" class="text-[10px] font-mono break-all text-primary"></p>
    <button onclick="document.getElementById('linkBox').classList.add('hidden')" class="mt-3 text-[10px] font-bold text-slate-400 hover:text-white">Dismiss</button>
</div>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Client Directory</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">External Partners & Leads</p>
        </div>
        <button onclick="openClientModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">person_add</span> REGISTER CLIENT
        </button>
    </header>

    <main class="flex-1 overflow-y-auto custom-scrollbar p-6">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Client / Company</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Contact</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Account</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($clients)): ?>
                        <tr><td colspan="5" class="px-6 py-20 text-center"><span class="material-symbols-outlined text-4xl text-slate-200">group_off</span><p class="text-xs font-bold text-slate-400 mt-2">No clients found</p></td></tr>
                    <?php endif; ?>
                    <?php foreach ($clients as $c): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm uppercase"><?php echo substr($c['name'], 0, 1); ?></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($c['name']); ?></p>
                                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest"><?php echo htmlspecialchars($c['company'] ?: 'Individual'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-medium text-slate-600"><?php echo htmlspecialchars($c['email']); ?></p>
                                <p class="text-xs font-medium text-slate-400"><?php echo htmlspecialchars($c['phone'] ?: '—'); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest <?php echo $c['status']=='Active'?'bg-emerald-50 text-emerald-600 border border-emerald-100':'bg-red-50 text-red-500 border border-red-100'; ?>"><?php echo $c['status']; ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($c['password']): ?>
                                    <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600"><span class="material-symbols-outlined text-sm">lock</span> Password Set</span>
                                <?php else: ?>
                                    <button onclick="resendLink(<?php echo $c['id']; ?>)" class="flex items-center gap-1 text-[10px] font-bold text-amber-500 hover:text-amber-700 transition-colors">
                                        <span class="material-symbols-outlined text-sm">link</span> Send Setup Link
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="client_overview.php?id=<?php echo $c['id']; ?>" class="w-8 h-8 rounded-lg border border-slate-100 text-slate-400 hover:text-blue-500 hover:border-blue-200 transition-all flex items-center justify-center" title="View Profile"><span class="material-symbols-outlined text-lg">visibility</span></a>
                                    <button onclick="editClient(<?php echo $c['id']; ?>)" class="w-8 h-8 rounded-lg border border-slate-100 text-slate-400 hover:text-primary hover:border-primary/30 transition-all flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-lg">edit</span></button>
                                    <button onclick="deleteClient(<?php echo $c['id']; ?>)" class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Delete"><span class="material-symbols-outlined text-lg">delete</span></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
                </div>
        </div>
    </main>
</div>

<!-- Client Modal -->
<div id="clientModal" class="modal-overlay">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
            <div>
                <h3 id="clientModalTitle" class="font-bold text-xl text-slate-900 font-headline">Register Client</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">External CRM Record</p>
            </div>
            <button onclick="closeClientModal()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="overflow-y-auto custom-scrollbar flex-1">
            <form id="clientForm" class="p-8 space-y-5">
                <?= get_csrf_field() ?>
                <input type="hidden" name="id" id="clientId">
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Client Name</label>
                        <input type="text" name="name" id="clientName" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Company</label>
                        <input type="text" name="company" id="clientCompany" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" name="email" id="clientEmail" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Phone</label>
                        <input type="text" name="phone" id="clientPhone" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                    <select name="status" id="clientStatus" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        <option value="Active">Active Partner</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Lead">Potential Lead</option>
                    </select>
                </div>

                <!-- Password Options (new clients only) -->
                <div id="passOptionBlock" class="space-y-3 pt-2 border-t border-slate-50">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Account Password</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="setPassOption('none')" id="opt-none" class="py-2.5 rounded-xl border-2 border-primary bg-primary/10 text-[9px] font-bold uppercase tracking-widest transition-all text-slate-700">Skip</button>
                        <button type="button" onclick="setPassOption('set')" id="opt-set" class="py-2.5 rounded-xl border-2 border-transparent bg-slate-50 text-[9px] font-bold uppercase tracking-widest transition-all text-slate-400 hover:border-slate-200">Set Password</button>
                        <button type="button" onclick="setPassOption('link')" id="opt-link" class="py-2.5 rounded-xl border-2 border-transparent bg-slate-50 text-[9px] font-bold uppercase tracking-widest transition-all text-slate-400 hover:border-slate-200">Send Link</button>
                    </div>
                    <input type="hidden" name="pass_option" id="passOption" value="none">
                    <div id="passSetBlock" class="pass-option space-y-1.5">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Set Password</label>
                        <input type="password" name="password" id="clientPass" placeholder="Min 6 characters" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div id="passLinkBlock" class="pass-option">
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-xs text-blue-600 font-medium">
                            <span class="material-symbols-outlined text-sm align-middle mr-1">mail</span>
                            A secure setup link will be emailed to the client. They verify their address and create their own password.
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeClientModal()" class="flex-1 py-4 rounded-2xl border border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Cancel</button>
                    <button type="submit" id="clientSaveBtn" class="flex-1 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-bold uppercase tracking-[0.2em]">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let isEditMode = false;

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    document.getElementById('toastMessage').innerText = msg;
    document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'error';
    document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] ${type==='success'?'bg-slate-900 text-white':'bg-red-600 text-white'}`;
    t.style.transform = 'translateX(0)';
    setTimeout(() => t.style.transform = 'translateX(150%)', 4000);
}

function showLink(link) {
    document.getElementById('linkText').innerText = link;
    document.getElementById('linkBox').classList.remove('hidden');
}

function setPassOption(opt) {
    document.getElementById('passOption').value = opt;
    ['none','set','link'].forEach(o => {
        const btn = document.getElementById('opt-' + o);
        btn.className = 'py-2.5 rounded-xl border-2 text-[9px] font-bold uppercase tracking-widest transition-all ' +
            (o === opt ? 'border-primary bg-primary/10 text-slate-900' : 'border-transparent bg-slate-50 text-slate-400 hover:border-slate-200');
    });
    document.getElementById('passSetBlock').className = 'pass-option space-y-1.5' + (opt === 'set' ? ' active' : '');
    document.getElementById('passLinkBlock').className = 'pass-option' + (opt === 'link' ? ' active' : '');
}

function openClientModal() {
    isEditMode = false;
    document.getElementById('clientModalTitle').innerText = 'Register Client';
    document.getElementById('clientForm').reset();
    document.getElementById('clientId').value = '';
    document.getElementById('passOptionBlock').style.display = 'block';
    setPassOption('none');
    document.getElementById('clientModal').classList.add('open');
}

function closeClientModal() { document.getElementById('clientModal').classList.remove('open'); }

async function editClient(id) {
    isEditMode = true;
    const res = await fetch(`?ajax_action=get_client&id=${id}`);
    const data = await res.json();
    document.getElementById('clientModalTitle').innerText = 'Edit Client Record';
    document.getElementById('clientId').value = data.id;
    document.getElementById('clientName').value = data.name;
    document.getElementById('clientEmail').value = data.email;
    document.getElementById('clientPhone').value = data.phone || '';
    document.getElementById('clientCompany').value = data.company || '';
    document.getElementById('clientStatus').value = data.status;
    document.getElementById('passOptionBlock').style.display = 'none';
    document.getElementById('clientModal').classList.add('open');
}

document.getElementById('clientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('clientSaveBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
        const fd = new FormData(this);
        const res = await fetch('?ajax_action=save_client', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') {
            closeClientModal();
            showToast(result.message);
            if (result.setup_link) showLink(result.setup_link);
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast(result.message, 'error');
        }
    } catch(err) {
        showToast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Record';
    }
});

async function resendLink(id) {
    showToast('Generating link...');
    const fd = new URLSearchParams();
    fd.append('id', id);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=resend_link', { method: 'POST', body: fd });
    const result = await res.json();
    showToast(result.message, result.status);
    if (result.setup_link) showLink(result.setup_link);
}

async function deleteClient(id) {
    if (!confirm('Delete client record?')) return;
    const fd = new URLSearchParams();
    fd.append('id', id);
    fd.append('csrf_token', CSRF_TOKEN);
    const res = await fetch('?ajax_action=delete_client', { method: 'POST', body: fd });
    const result = await res.json();
    if (result.status === 'success') location.reload();
    else showToast(result.message, 'error');
}
</script>
</body>
</html>
