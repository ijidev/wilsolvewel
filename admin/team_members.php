<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

$success_msg = '';
$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_member'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $photo_url = trim($_POST['photo_url'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'Active';

    if (empty($name)) {
        $error_msg = 'Member name is required.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE team_members SET name=?, position=?, bio=?, photo_url=?, department=?, sort_order=?, status=? WHERE id=?");
            $stmt->bind_param("sssssssi", $name, $position, $bio, $photo_url, $department, $sort_order, $status, $id);
            $stmt->execute();
            $stmt->close();
            log_audit($conn, 'Update', 'TeamMember', 'Admin', $admin_id, "Updated team member: $name");
            $success_msg = 'Team member updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO team_members (name, position, bio, photo_url, department, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $position, $bio, $photo_url, $department, $sort_order, $status);
            $stmt->execute();
            $stmt->close();
            log_audit($conn, 'Create', 'TeamMember', 'Admin', $admin_id, "Created team member: $name");
            $success_msg = 'Team member created.';
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_member'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $id = (int)$_POST['delete_member'];
    $stmt = $conn->prepare("DELETE FROM team_members WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    log_audit($conn, 'Delete', 'TeamMember', 'Admin', $admin_id, "Deleted team member #$id");
    $success_msg = 'Team member deleted.';
}

$members = [];
$res = $conn->query("SELECT * FROM team_members ORDER BY sort_order ASC, id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $members[] = $row;
}

$edit_member = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    foreach ($members as $m) {
        if ($m['id'] == $id) { $edit_member = $m; break; }
    }
}

$page_title = 'Team Members';
$page_subtitle = '';
$page_header_actions = '';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Team Members | Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}</style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">
<script src="../components/admin_sidenav.js" data-root="../"></script>

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
    <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

    <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                    <h2 class="text-xl font-bold font-headline text-slate-900"><?php echo $edit_member ? 'Edit Team Member' : 'Add New Team Member'; ?></h2>
                </div>
                <form method="POST" class="p-8 space-y-6">
                    <input type="hidden" name="save_member" value="1">
                    <?= get_csrf_field() ?>
                    <?php if ($edit_member): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_member['id']; ?>">
                    <?php endif; ?>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5 col-span-2 sm:col-span-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_member['name'] ?? ''); ?>" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5 col-span-2 sm:col-span-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Position</label>
                            <input type="text" name="position" value="<?php echo htmlspecialchars($edit_member['position'] ?? ''); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5 col-span-2 sm:col-span-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Department</label>
                            <input type="text" name="department" value="<?php echo htmlspecialchars($edit_member['department'] ?? ''); ?>" placeholder="e.g. Management" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5 col-span-2 sm:col-span-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                            <input type="number" name="sort_order" value="<?php echo (int)($edit_member['sort_order'] ?? 0); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Photo URL</label>
                            <input type="url" name="photo_url" value="<?php echo htmlspecialchars($edit_member['photo_url'] ?? ''); ?>" placeholder="https://..." class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5 col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Bio</label>
                            <textarea name="bio" rows="3" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary"><?php echo htmlspecialchars($edit_member['bio'] ?? ''); ?></textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                            <select name="status" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                <option value="Active" <?php echo ($edit_member['status']??'Active')=='Active'?'selected':''; ?>>Active</option>
                                <option value="Inactive" <?php echo ($edit_member['status']??'')=='Inactive'?'selected':''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <?php if ($edit_member): ?>
                        <a href="team_members.php" class="px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors">Cancel</a>
                        <?php endif; ?>
                        <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg"><?php echo $edit_member ? 'Update Member' : 'Add Member'; ?></button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold font-headline text-slate-900">All Team Members</h2>
                        <p class="text-xs text-slate-500 mt-1"><?php echo count($members); ?> total members</p>
                    </div>
                </div>
                <div class="p-8">
                    <?php if (empty($members)): ?>
                    <p class="text-center py-10 text-slate-400 text-xs italic">No team members yet. Add one above.</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($members as $m): ?>
                        <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl hover:border-primary/30 transition-all group">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <?php if ($m['photo_url']): ?>
                                <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 bg-slate-100">
                                    <img src="<?php echo htmlspecialchars($m['photo_url']); ?>" class="w-full h-full object-cover" alt="">
                                </div>
                                <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-slate-400">person</span>
                                </div>
                                <?php endif; ?>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($m['name']); ?></span>
                                        <span class="px-2 py-0.5 bg-<?php echo $m['status']=='Active'?'emerald-100 text-emerald-700':'slate-100 text-slate-500'; ?> rounded-md text-[9px] font-bold uppercase tracking-widest"><?php echo $m['status']; ?></span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($m['position']); ?></span>
                                        <?php if ($m['department']): ?>
                                        <span class="text-[10px] text-slate-400">|</span>
                                        <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($m['department']); ?></span>
                                        <?php endif; ?>
                                        <span class="text-[10px] text-slate-400">|</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Order: <?php echo (int)$m['sort_order']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0 ml-4">
                                <a href="?edit=<?php echo $m['id']; ?>" class="p-2 text-slate-300 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </a>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this team member?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="delete_member" value="<?php echo $m['id']; ?>">
                                    <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
