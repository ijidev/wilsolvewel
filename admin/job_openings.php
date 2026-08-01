<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

$success_msg = '';
$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_opening'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $type = trim($_POST['type'] ?? 'Full-Time');
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'Open';

    if (empty($title)) {
        $error_msg = 'Job title is required.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE job_openings SET title=?, department=?, location=?, type=?, description=?, requirements=?, sort_order=?, status=? WHERE id=?");
            $stmt->bind_param("ssssssssi", $title, $department, $location, $type, $description, $requirements, $sort_order, $status, $id);
            $stmt->execute();
            $stmt->close();
            log_audit($conn, 'Update', 'JobOpening', 'Admin', $admin_id, "Updated job opening: $title");
            $success_msg = 'Job opening updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO job_openings (title, department, location, type, description, requirements, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $title, $department, $location, $type, $description, $requirements, $sort_order, $status);
            $stmt->execute();
            $stmt->close();
            log_audit($conn, 'Create', 'JobOpening', 'Admin', $admin_id, "Created job opening: $title");
            $success_msg = 'Job opening created.';
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_opening'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $id = (int)$_POST['delete_opening'];
    $stmt = $conn->prepare("DELETE FROM job_openings WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    log_audit($conn, 'Delete', 'JobOpening', 'Admin', $admin_id, "Deleted job opening #$id");
    $success_msg = 'Job opening deleted.';
}

$openings = [];
$res = $conn->query("SELECT * FROM job_openings ORDER BY sort_order ASC, id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $openings[] = $row;
}

$page_title = 'Job Openings';
$page_subtitle = '';
$page_header_actions = '';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Job Openings | Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}</style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">
<script src="../components/admin_sidenav.js?v=2" data-root="../"></script>

<div id="toast" class="fixed top-6 right-6 z-[400] transform <?php echo ($success_msg || $error_msg) ? 'translate-x-0' : 'translate-x-[150%]'; ?> transition-transform duration-300 pointer-events-none">
    <div class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span class="material-symbols-outlined <?php echo $error_msg ? 'text-red-500' : 'text-primary'; ?>"><?php echo $error_msg ? 'error' : 'check_circle'; ?></span>
        <p class="text-xs font-bold"><?php echo htmlspecialchars($success_msg ?: $error_msg); ?></p>
    </div>
</div>
<?php if ($success_msg || $error_msg): ?>
<script>setTimeout(() => document.getElementById('toast').style.transform = 'translateX(150%)', 4000);</script>
<?php endif; ?>

<!-- Form Modal -->
<div id="formModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative h-full flex items-start justify-center p-4 pt-12 lg:pt-20 overflow-y-auto">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl border border-slate-100">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center rounded-t-[2rem]">
                <h2 id="modalTitle" class="text-xl font-bold font-headline text-slate-900">Add New Job Opening</h2>
                <button onclick="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" class="p-8 space-y-6">
                <input type="hidden" name="save_opening" value="1">
                <input type="hidden" name="id" id="editId" value="0">
                <?= get_csrf_field() ?>
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Job Title *</label>
                        <input type="text" name="title" id="fieldTitle" value="" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Department</label>
                        <input type="text" name="department" id="fieldDepartment" value="" placeholder="e.g. Engineering" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Location</label>
                        <input type="text" name="location" id="fieldLocation" value="" placeholder="e.g. Lagos HQ" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Type</label>
                        <select name="type" id="fieldType" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Full-Time">Full-Time</option>
                            <option value="Part-Time">Part-Time</option>
                            <option value="Hybrid">Hybrid</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                        <input type="number" name="sort_order" id="fieldSortOrder" value="0" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Description</label>
                        <textarea name="description" id="fieldDescription" rows="3" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary"></textarea>
                    </div>
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Requirements</label>
                        <textarea name="requirements" id="fieldRequirements" rows="4" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary"></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" id="fieldStatus" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Open">Open</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" id="modalSubmit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Add Opening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

    <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold font-headline text-slate-900">All Job Openings</h2>
                        <p class="text-xs text-slate-500 mt-1"><?php echo count($openings); ?> total openings</p>
                    </div>
                    <button onclick="openAddModal()" class="bg-slate-900 text-white px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">add</span> Add Opening
                    </button>
                </div>
                <div class="p-8">
                    <?php if (empty($openings)): ?>
                    <p class="text-center py-10 text-slate-400 text-xs italic">No job openings yet. Add one now.</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($openings as $o): ?>
                        <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl hover:border-primary/30 transition-all group">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-slate-500">work</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($o['title']); ?></span>
                                        <span class="px-2 py-0.5 bg-<?php echo $o['status']=='Open'?'emerald-100 text-emerald-700':'slate-100 text-slate-500'; ?> rounded-md text-[9px] font-bold uppercase tracking-widest"><?php echo $o['status']; ?></span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($o['location']); ?></span>
                                        <span class="text-[10px] text-slate-400">|</span>
                                        <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($o['type']); ?></span>
                                        <?php if ($o['department']): ?>
                                        <span class="text-[10px] text-slate-400">|</span>
                                        <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($o['department']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0 ml-4">
                                <button onclick='openEditModal(<?php echo json_encode($o); ?>)' class="p-2 text-slate-300 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this job opening?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="delete_opening" value="<?php echo $o['id']; ?>">
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

<script>
function openAddModal() {
    document.getElementById('editId').value = '0';
    document.getElementById('fieldTitle').value = '';
    document.getElementById('fieldDepartment').value = '';
    document.getElementById('fieldLocation').value = '';
    document.getElementById('fieldType').value = 'Full-Time';
    document.getElementById('fieldSortOrder').value = '0';
    document.getElementById('fieldDescription').value = '';
    document.getElementById('fieldRequirements').value = '';
    document.getElementById('fieldStatus').value = 'Open';
    document.getElementById('modalTitle').textContent = 'Add New Job Opening';
    document.getElementById('modalSubmit').textContent = 'Add Opening';
    document.getElementById('formModal').classList.remove('hidden');
}

function openEditModal(opening) {
    document.getElementById('editId').value = opening.id || 0;
    document.getElementById('fieldTitle').value = opening.title || '';
    document.getElementById('fieldDepartment').value = opening.department || '';
    document.getElementById('fieldLocation').value = opening.location || '';
    document.getElementById('fieldType').value = opening.type || 'Full-Time';
    document.getElementById('fieldSortOrder').value = opening.sort_order || 0;
    document.getElementById('fieldDescription').value = opening.description || '';
    document.getElementById('fieldRequirements').value = opening.requirements || '';
    document.getElementById('fieldStatus').value = opening.status || 'Open';
    document.getElementById('modalTitle').textContent = 'Edit Job Opening';
    document.getElementById('modalSubmit').textContent = 'Update Opening';
    document.getElementById('formModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('formModal').classList.add('hidden');
}
</script>
</body>
</html>
