<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];
$permissions = get_admin_permissions($admin_id);

$success_msg = '';
$error_msg = '';

// Handle Global Settings Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_global'])) {
    if ($permissions['role'] !== 'Director') {
        $error_msg = "Permission denied.";
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    } else {
        $fields = ['site_name', 'seo_description', 'currency', 'timezone'];
        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $v = $_POST[$f];
                $stmt = $conn->prepare("INSERT INTO global_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=?");
                $stmt->bind_param("sss", $f, $v, $v);
                $stmt->execute();
                $stmt->close();
            }
        }
        log_audit($conn, 'Update', 'Settings', 'Admin', $admin_id, "Updated Global Settings");
        $success_msg = "Global settings updated.";
    }
}

// Handle SMTP Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_smtp'])) {
    if ($permissions['role'] !== 'Director') {
        $error_msg = "Permission denied.";
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    } else {
        set_setting('smtp_host', $_POST['smtp_host']);
        set_setting('smtp_port', $_POST['smtp_port']);
        set_setting('smtp_user', $_POST['smtp_user']);
        if (!empty($_POST['smtp_pass'])) {
            set_setting('smtp_pass', $_POST['smtp_pass']);
        }
        set_setting('smtp_encryption', $_POST['smtp_encryption']);
        set_setting('smtp_from_email', $_POST['smtp_from_email']);
        set_setting('smtp_from_name', $_POST['smtp_from_name']);
        
        log_audit($conn, 'Update', 'Settings', 'Admin', $admin_id, "Updated SMTP Configuration");
        $success_msg = "SMTP settings updated.";
    }
}

// Handle Contact Info Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_contact'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $contact_fields = ['contact_address','contact_phone','contact_email','contact_procurement_email','hours_weekdays','hours_saturday','hours_sunday','map_latitude','map_longitude','google_maps_api_key'];
    foreach ($contact_fields as $f) {
        if (isset($_POST[$f])) {
            set_setting($f, trim($_POST[$f]));
        }
    }
    log_audit($conn, 'Update', 'Settings', 'Admin', $admin_id, "Updated Contact Information");
    $success_msg = "Contact information updated.";
}

// Handle Routing Rules CRUD
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_rule'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $source_type = $_POST['source_type'];
    $keyword = $_POST['match_keyword'];
    $dept_id = (int)$_POST['department_id'];
    $stmt = $conn->prepare("INSERT INTO routing_rules (source_type, match_keyword, department_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $source_type, $keyword, $dept_id);
    $stmt->execute();
    $stmt->close();
    log_audit($conn, 'Create', 'RoutingRule', 'Admin', $admin_id, "Added rule for $source_type");
    $success_msg = "Routing rule added.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_rule'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $id = (int)$_POST['delete_rule'];
    $stmt = $conn->prepare("DELETE FROM routing_rules WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    log_audit($conn, 'Delete', 'RoutingRule', 'Admin', $admin_id, "Deleted rule #$id");
    $success_msg = "Routing rule deleted.";
}

// Fetch Global Settings
$globals = [];
$res = $conn->query("SELECT setting_key, setting_value FROM global_settings");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $globals[$row['setting_key']] = $row['setting_value'];
    }
}

// Fetch Error Logs
$error_logs = [];
$res = $conn->query("SELECT * FROM system_errors ORDER BY created_at DESC LIMIT 100");
if ($res) {
    while ($row = $res->fetch_assoc()) $error_logs[] = $row;
}

// SMTP variables
$smtp_host = get_setting('smtp_host');
$smtp_port = get_setting('smtp_port');
$smtp_user = get_setting('smtp_user');
$smtp_encryption = get_setting('smtp_encryption');
$smtp_from_email = get_setting('smtp_from_email');
$smtp_from_name = get_setting('smtp_from_name');

// Fetch Departments for Rules
$depts_res = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");
$departments = [];
while ($row = $depts_res->fetch_assoc()) $departments[] = $row;

// Fetch Routing Rules
$rules_res = $conn->query("SELECT r.*, d.name as department_name FROM routing_rules r LEFT JOIN departments d ON r.department_id = d.id ORDER BY r.source_type ASC");
$routing_rules = [];
while ($row = $rules_res->fetch_assoc()) $routing_rules[] = $row;

$active_tab = $_GET['tab'] ?? 'global';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>System Settings | Terminal</title>
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

<!-- Toast -->
<div id="toast" class="fixed top-6 right-6 z-[400] transform <?php echo ($success_msg || $error_msg) ? 'translate-x-0' : 'translate-x-[150%]'; ?> transition-transform duration-300 pointer-events-none">
    <div class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span class="material-symbols-outlined <?php echo $error_msg ? 'text-red-500' : 'text-primary'; ?>"><?php echo $error_msg ? 'error' : 'check_circle'; ?></span>
        <p class="text-xs font-bold"><?php echo htmlspecialchars($success_msg ?: $error_msg); ?></p>
    </div>
</div>
<?php if ($success_msg || $error_msg): ?>
<script>setTimeout(() => document.getElementById('toast').style.transform = 'translateX(150%)', 4000);</script>
<?php endif; ?>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">System Settings</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Configuration & Logs</p>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Settings Nav Sidebar -->
        <div class="w-full md:w-64 bg-white border-r border-slate-100 overflow-y-auto custom-scrollbar shrink-0 hidden md:block">
            <nav class="p-4 space-y-1">
                <a href="?tab=global" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $active_tab=='global' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 font-medium'; ?>">
                    <span class="material-symbols-outlined text-lg">language</span>
                    <span class="text-sm">Global Settings</span>
                </a>
                <a href="?tab=smtp" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $active_tab=='smtp' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 font-medium'; ?>">
                    <span class="material-symbols-outlined text-lg">mail</span>
                    <span class="text-sm">SMTP Setup</span>
                </a>
                <a href="?tab=contacts" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $active_tab=='contacts' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 font-medium'; ?>">
                    <span class="material-symbols-outlined text-lg">contact_page</span>
                    <span class="text-sm">Contact Info</span>
                </a>
                <a href="?tab=rules" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $active_tab=='rules' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-500 hover:bg-slate-50 font-medium'; ?>">
                    <span class="material-symbols-outlined text-lg">alt_route</span>
                    <span class="text-sm">Routing Rules</span>
                </a>
                <a href="?tab=errors" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $active_tab=='errors' ? 'bg-red-50 text-red-600 font-bold' : 'text-slate-500 hover:bg-slate-50 font-medium'; ?>">
                    <span class="material-symbols-outlined text-lg">bug_report</span>
                    <span class="text-sm">System Errors</span>
                </a>
            </nav>
        </div>

        <!-- Main Content Pane -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6 lg:p-8 relative">
            <div class="max-w-3xl mx-auto">

                <!-- Mobile Tab Bar -->
                <div class="md:hidden overflow-x-auto -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="flex gap-1 pb-2 border-b border-slate-100 min-w-max">
                        <a href="?tab=global" class="whitespace-nowrap px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all <?php echo $active_tab=='global' ? 'bg-primary/10 text-primary' : 'text-slate-400 hover:text-slate-700'; ?>">
                            <span class="material-symbols-outlined text-base align-middle mr-1">language</span>Global
                        </a>
                        <a href="?tab=smtp" class="whitespace-nowrap px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all <?php echo $active_tab=='smtp' ? 'bg-primary/10 text-primary' : 'text-slate-400 hover:text-slate-700'; ?>">
                            <span class="material-symbols-outlined text-base align-middle mr-1">mail</span>SMTP
                        </a>
                        <a href="?tab=contacts" class="whitespace-nowrap px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all <?php echo $active_tab=='contacts' ? 'bg-primary/10 text-primary' : 'text-slate-400 hover:text-slate-700'; ?>">
                            <span class="material-symbols-outlined text-base align-middle mr-1">contact_page</span>Contact
                        </a>
                        <a href="?tab=rules" class="whitespace-nowrap px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all <?php echo $active_tab=='rules' ? 'bg-primary/10 text-primary' : 'text-slate-400 hover:text-slate-700'; ?>">
                            <span class="material-symbols-outlined text-base align-middle mr-1">alt_route</span>Rules
                        </a>
                        <a href="?tab=errors" class="whitespace-nowrap px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all <?php echo $active_tab=='errors' ? 'bg-red-50 text-red-600' : 'text-slate-400 hover:text-slate-700'; ?>">
                            <span class="material-symbols-outlined text-base align-middle mr-1">bug_report</span>Errors
                        </a>
                    </div>
                </div>

                <?php if ($active_tab === 'global'): ?>
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                        <h2 class="text-xl font-bold font-headline text-slate-900">Global Configuration</h2>
                        <p class="text-xs text-slate-500 mt-1">Manage core platform identity and localization.</p>
                    </div>
                    <form method="POST" class="p-8 space-y-6">
                        <input type="hidden" name="save_global" value="1">
                        <?= get_csrf_field() ?>
                        
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Platform Name</label>
                            <input type="text" name="site_name" value="<?php echo htmlspecialchars($globals['site_name'] ?? 'Wilsolvewel Engineering'); ?>" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">SEO Description / Meta</label>
                            <textarea name="seo_description" rows="3" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary custom-scrollbar"><?php echo htmlspecialchars($globals['seo_description'] ?? ''); ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Default Currency</label>
                                <select name="currency" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                    <option value="USD" <?php echo ($globals['currency']??'')=='USD'?'selected':''; ?>>USD ($)</option>
                                    <option value="EUR" <?php echo ($globals['currency']??'')=='EUR'?'selected':''; ?>>EUR (€)</option>
                                    <option value="GBP" <?php echo ($globals['currency']??'')=='GBP'?'selected':''; ?>>GBP (£)</option>
                                    <option value="NGN" <?php echo ($globals['currency']??'')=='NGN'?'selected':''; ?>>NGN (₦)</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Timezone</label>
                                <select name="timezone" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                    <option value="UTC" <?php echo ($globals['timezone']??'')=='UTC'?'selected':''; ?>>UTC</option>
                                    <option value="America/New_York" <?php echo ($globals['timezone']??'')=='America/New_York'?'selected':''; ?>>Eastern Time (US)</option>
                                    <option value="Europe/London" <?php echo ($globals['timezone']??'')=='Europe/London'?'selected':''; ?>>London</option>
                                    <option value="Africa/Lagos" <?php echo ($globals['timezone']??'')=='Africa/Lagos'?'selected':''; ?>>West Africa (Lagos)</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Save Configuration</button>
                        </div>
                    </form>
                </div>

                <?php elseif ($active_tab === 'smtp'): ?>
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                        <h2 class="text-xl font-bold font-headline text-slate-900">SMTP Server</h2>
                        <p class="text-xs text-slate-500 mt-1">Configure outbound email delivery for notifications.</p>
                    </div>
                    <form method="POST" class="p-8 space-y-6">
                        <input type="hidden" name="save_smtp" value="1">
                        <?= get_csrf_field() ?>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">SMTP Host</label>
                                <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($smtp_host); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">SMTP Port</label>
                                <input type="number" name="smtp_port" value="<?php echo htmlspecialchars($smtp_port); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                                <input type="text" name="smtp_user" value="<?php echo htmlspecialchars($smtp_user); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                                <input type="password" name="smtp_pass" placeholder="Leave blank to keep current" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Encryption</label>
                                <select name="smtp_encryption" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                    <option value="none" <?php echo $smtp_encryption=='none'?'selected':''; ?>>None</option>
                                    <option value="tls" <?php echo $smtp_encryption=='tls'?'selected':''; ?>>TLS</option>
                                    <option value="ssl" <?php echo $smtp_encryption=='ssl'?'selected':''; ?>>SSL</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">From Email</label>
                                <input type="email" name="smtp_from_email" value="<?php echo htmlspecialchars($smtp_from_email); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">From Name</label>
                                <input type="text" name="smtp_from_name" value="<?php echo htmlspecialchars($smtp_from_name); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Save Configuration</button>
                        </div>
                    </form>
                </div>

                <?php elseif ($active_tab === 'contacts'): ?>
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                        <h2 class="text-xl font-bold font-headline text-slate-900">Contact Information</h2>
                        <p class="text-xs text-slate-500 mt-1">Manage contact details displayed on the public website.</p>
                    </div>
                    <form method="POST" class="p-8 space-y-6">
                        <input type="hidden" name="save_contact" value="1">
                        <?= get_csrf_field() ?>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Address</label>
                                <input type="text" name="contact_address" value="<?php echo htmlspecialchars(get_setting('contact_address', 'Lagos, Nigeria')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Phone</label>
                                <input type="text" name="contact_phone" value="<?php echo htmlspecialchars(get_setting('contact_phone', '+234 (0) 800 945 768')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">General Email</label>
                                <input type="email" name="contact_email" value="<?php echo htmlspecialchars(get_setting('contact_email', 'info@wilsolvewel.com')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                            <div class="space-y-1.5 col-span-2 md:col-span-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Procurement Email</label>
                                <input type="email" name="contact_procurement_email" value="<?php echo htmlspecialchars(get_setting('contact_procurement_email', 'procurement@wilsolvewel.com')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            </div>
                        </div>
                        <div class="border-t border-slate-100 pt-6">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Business Hours</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Weekdays</label>
                                    <input type="text" name="hours_weekdays" value="<?php echo htmlspecialchars(get_setting('hours_weekdays', '8:00 AM - 5:00 PM')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Saturday</label>
                                    <input type="text" name="hours_saturday" value="<?php echo htmlspecialchars(get_setting('hours_saturday', 'By Appointment')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sunday</label>
                                    <input type="text" name="hours_sunday" value="<?php echo htmlspecialchars(get_setting('hours_sunday', 'Closed')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-slate-100 pt-6">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Google Maps</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Latitude</label>
                                    <input type="text" name="map_latitude" value="<?php echo htmlspecialchars(get_setting('map_latitude', '6.5244')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Longitude</label>
                                    <input type="text" name="map_longitude" value="<?php echo htmlspecialchars(get_setting('map_longitude', '3.3792')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Google Maps API Key</label>
                                    <input type="text" name="google_maps_api_key" value="<?php echo htmlspecialchars(get_setting('google_maps_api_key', '')); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Save Contact Info</button>
                        </div>
                    </form>
                </div>

                <?php elseif ($active_tab === 'rules'): ?>
                <div class="space-y-6">
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <div>
                                <h2 class="text-xl font-bold font-headline text-slate-900">Auto-Routing Rules</h2>
                                <p class="text-xs text-slate-500 mt-1">Define how incoming requests are assigned to departments.</p>
                            </div>
                        </div>
                        <div class="p-8">
                            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                <input type="hidden" name="add_rule" value="1">
                                <?= get_csrf_field() ?>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Request Type</label>
                                    <select name="source_type" required class="w-full bg-white border-slate-200 rounded-xl px-4 py-2 text-xs font-bold focus:ring-1 focus:ring-primary">
                                        <option value="ticket">Support Ticket</option>
                                        <option value="project_proposal">Project Proposal</option>
                                        <option value="inquiry">General Inquiry</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Keyword (Optional)</label>
                                    <input type="text" name="match_keyword" placeholder="e.g. 'Electrical'" class="w-full bg-white border-slate-200 rounded-xl px-4 py-2 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Assign To</label>
                                    <div class="flex gap-2">
                                        <select name="department_id" required class="w-full bg-white border-slate-200 rounded-xl px-4 py-2 text-xs font-bold focus:ring-1 focus:ring-primary">
                                            <?php foreach ($departments as $d): ?>
                                            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="bg-primary text-on-primary px-4 rounded-xl hover:shadow-md transition-all">
                                            <span class="material-symbols-outlined">add</span>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="space-y-3">
                                <?php if (empty($routing_rules)): ?>
                                    <p class="text-center py-10 text-slate-400 text-xs italic">No routing rules defined yet.</p>
                                <?php else: ?>
                                    <?php foreach ($routing_rules as $rule): ?>
                                    <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl hover:border-primary/30 transition-all group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                                <span class="material-symbols-outlined text-sm">
                                                    <?php 
                                                        if($rule['source_type'] == 'ticket') echo 'confirmation_number';
                                                        elseif($rule['source_type'] == 'project_proposal') echo 'add_task';
                                                        else echo 'chat';
                                                    ?>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-900 uppercase tracking-tight"><?= str_replace('_', ' ', $rule['source_type']) ?></span>
                                                    <?php if($rule['match_keyword']): ?>
                                                    <span class="text-[9px] bg-primary/10 text-primary px-1.5 py-0.5 rounded font-bold uppercase tracking-widest">Matches: "<?= htmlspecialchars($rule['match_keyword']) ?>"</span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Assigned to: <span class="text-primary font-bold"><?= htmlspecialchars($rule['department_name']) ?></span></p>
                                            </div>
                                        </div>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="delete_rule" value="<?= $rule['id'] ?>">
                                            <?= get_csrf_field() ?>
                                            <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100" onclick="return confirm('Delete this rule?')">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($active_tab === 'errors'): ?>
                <div class="space-y-4">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold font-headline text-slate-900">System Exceptions</h2>
                        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-[10px] font-bold uppercase tracking-widest"><?php echo count($error_logs); ?> Logs</span>
                    </div>

                    <?php if (empty($error_logs)): ?>
                        <div class="bg-white border border-slate-100 rounded-[2rem] p-12 text-center shadow-sm">
                            <span class="material-symbols-outlined text-6xl text-emerald-300 mb-4">check_circle</span>
                            <h3 class="font-bold text-slate-900 text-lg font-headline">All Systems Nominal</h3>
                            <p class="text-xs text-slate-400 mt-1">No system errors have been recorded.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($error_logs as $e): ?>
                        <div class="bg-white rounded-2xl p-5 border border-red-100 shadow-sm relative overflow-hidden group">
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-red-400"></div>
                            <div class="pl-3">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-md text-[9px] font-bold uppercase tracking-widest"><?php echo htmlspecialchars($e['module']); ?></span>
                                    <span class="text-[10px] font-bold text-slate-400 tracking-widest"><?php echo date('M j, Y h:i A', strtotime($e['created_at'])); ?></span>
                                </div>
                                <p class="text-sm font-bold text-slate-900 leading-relaxed mb-2"><?php echo htmlspecialchars($e['error_message']); ?></p>
                                
                                <?php if (!empty($e['context'])): ?>
                                    <div class="mt-4 bg-slate-900 rounded-xl p-4 overflow-x-auto custom-scrollbar">
                                        <pre class="text-[10px] text-slate-300 font-mono"><?php echo htmlspecialchars(json_encode(json_decode($e['context']), JSON_PRETTY_PRINT)); ?></pre>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</div>

</body>
</html>
