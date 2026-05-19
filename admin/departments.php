<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();

// Handle Actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            csrf_error_response();
        }
        if ($_POST['action'] == 'add_dept') {
            $name = $_POST['name'];
            $template_id = !empty($_POST['template_id']) ? (int)$_POST['template_id'] : null;
            if ($template_id === null) {
                $stmt = $conn->prepare("INSERT IGNORE INTO departments (name, privilege_template_id) VALUES (?, NULL)");
                $stmt->bind_param("s", $name);
            } else {
                $stmt = $conn->prepare("INSERT IGNORE INTO departments (name, privilege_template_id) VALUES (?, ?)");
                $stmt->bind_param("si", $name, $template_id);
            }
            $stmt->execute();
            $stmt->close();
            $message = "Department registered.";
        }
        if ($_POST['action'] == 'update_dept') {
            $id = (int)$_POST['id'];
            $name = $_POST['name'];
            $template_id = !empty($_POST['template_id']) ? (int)$_POST['template_id'] : null;
            if ($template_id === null) {
                $stmt = $conn->prepare("UPDATE departments SET name = ?, privilege_template_id = NULL WHERE id = ?");
                $stmt->bind_param("si", $name, $id);
            } else {
                $stmt = $conn->prepare("UPDATE departments SET name = ?, privilege_template_id = ? WHERE id = ?");
                $stmt->bind_param("sii", $name, $template_id, $id);
            }
            $stmt->execute();
            $stmt->close();
            $message = "Infrastructure updated.";
        }
        if ($_POST['action'] == 'assign_staff') {
            $staff_id = (int)$_POST['staff_id'];
            $dept_id = (int)$_POST['dept_id'];
            $stmt = $conn->prepare("UPDATE admins SET department_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $dept_id, $staff_id);
            $stmt->execute();
            $stmt->close();
            $message = "Staff assignment synchronized.";
        }
    }
}

// Fetch Templates
$temp_res = $conn->query("SELECT id, name FROM privilege_templates ORDER BY name ASC");
$templates = [];
while ($t = $temp_res->fetch_assoc()) $templates[] = $t;

// Fetch Staff (Unassigned or All)
$staff_res = $conn->query("SELECT id, name, email FROM admins ORDER BY name ASC");
$all_staff = [];
while ($s = $staff_res->fetch_assoc()) $all_staff[] = $s;

// Fetch Departments with Template Names and Member Count
$dept_res = $conn->query("
    SELECT d.*, t.name as template_name, 
    (SELECT COUNT(*) FROM admins WHERE department_id = d.id) as member_count
    FROM departments d 
    LEFT JOIN privilege_templates t ON d.privilege_template_id = t.id
    ORDER BY d.name ASC
");
$departments = [];
while ($d = $dept_res->fetch_assoc()) {
    $d['members'] = [];
    $m_stmt = $conn->prepare("SELECT id, name, email FROM admins WHERE department_id = ?");
    $m_stmt->bind_param("i", $d['id']);
    $m_stmt->execute();
    $m_res = $m_stmt->get_result();
    $m_stmt->close();
    while ($m = $m_res->fetch_assoc()) $d['members'][] = $m;
    $departments[] = $d;
}

$page_title = 'Infrastructure Control';
$page_subtitle = 'Departments & Team Assignments';
ob_start(); ?>
<?php if ($message): ?>
    <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 uppercase"><?php echo $message; ?></span>
<?php endif; ?>
<button onclick="openAddModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs hover:shadow-lg transition-all flex items-center gap-2">
    <span class="material-symbols-outlined text-sm">corporate_fare</span>
    REGISTER DEPT
</button>
<?php $page_header_actions = ob_get_clean();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Departments & Team | Terminal</title>
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
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface overflow-hidden h-screen lg:pl-64 flex">
    
    <script src="../components/admin_sidenav.js" data-root="../"></script>

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($departments as $d): ?>
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                        <div class="p-6 border-b border-slate-50 flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base mb-1"><?php echo htmlspecialchars($d['name']); ?></h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Protocol:</span>
                                    <span class="text-[10px] font-bold text-primary uppercase"><?php echo $d['template_name'] ?: 'Custom Access'; ?></span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick='openEditModal(<?php echo json_encode($d); ?>)' class="w-9 h-9 flex items-center justify-center hover:bg-slate-50 rounded-xl text-slate-400 hover:text-primary transition-all">
                                    <span class="material-symbols-outlined text-base">settings</span>
                                </button>
                                <button onclick="openAssignModal(<?php echo $d['id']; ?>)" class="w-9 h-9 flex items-center justify-center bg-slate-50 rounded-xl text-primary hover:bg-primary hover:text-on-primary transition-all" title="Assign Staff">
                                    <span class="material-symbols-outlined text-base">person_add</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Members (<?php echo $d['member_count']; ?>)</p>
                            <div class="space-y-3">
                                <?php foreach ($d['members'] as $m): ?>
                                    <div class="flex items-center justify-between group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center font-bold text-slate-400 text-[10px] uppercase">
                                                <?php echo substr($m['name'], 0, 1); ?>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800"><?php echo htmlspecialchars($m['name']); ?></p>
                                                <p class="text-[9px] text-slate-400"><?php echo htmlspecialchars($m['email']); ?></p>
                                            </div>
                                        </div>
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-bold text-red-400 hover:text-red-600 uppercase">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($d['members'])): ?>
                                    <p class="text-xs text-slate-400 italic py-2">No staff assigned to this department.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Add/Edit Dept Modal -->
    <div id="deptModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[110] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 id="deptModalTitle" class="font-bold text-slate-900">Register Department</h3>
                <button onclick="closeModal('deptModal')" class="text-slate-400"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form method="POST" class="p-6 space-y-6">
                <?= get_csrf_field() ?>
                <input type="hidden" name="action" id="deptAction" value="add_dept">
                <input type="hidden" name="id" id="deptId">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Department Name</label>
                    <input type="text" name="name" id="deptName" required 
                           class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Access Protocol Template</label>
                    <select name="template_id" id="deptTemplate" class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary appearance-none">
                        <option value="">Manual / Custom Access</option>
                        <?php foreach ($templates as $t): ?>
                            <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('deptModal')" class="flex-1 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600">CANCEL</button>
                    <button type="submit" class="flex-1 py-3 rounded-2xl bg-primary text-on-primary text-xs font-bold shadow-lg shadow-primary/20">SAVE INFRASTRUCTURE</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Staff Modal -->
    <div id="assignModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[110] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Assign Team Member</h3>
            </div>
            <form method="POST" class="p-6 space-y-6">
                <?= get_csrf_field() ?>
                <input type="hidden" name="action" value="assign_staff">
                <input type="hidden" name="dept_id" id="assignDeptId">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Select Staff Member</label>
                    <select name="staff_id" required class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        <option value="">Choose Staff Member...</option>
                        <?php foreach ($all_staff as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?> (<?php echo htmlspecialchars($s['email']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('assignModal')" class="flex-1 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600">CANCEL</button>
                    <button type="submit" class="flex-1 py-3 rounded-2xl bg-slate-900 text-white text-xs font-bold">SYNCHRONIZE</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('deptModalTitle').innerText = "Register Department";
            document.getElementById('deptAction').value = "add_dept";
            document.getElementById('deptId').value = "";
            document.getElementById('deptName').value = "";
            document.getElementById('deptTemplate').value = "";
            document.getElementById('deptModal').classList.remove('hidden');
        }
        function openEditModal(d) {
            document.getElementById('deptModalTitle').innerText = "Edit Department";
            document.getElementById('deptAction').value = "update_dept";
            document.getElementById('deptId').value = d.id;
            document.getElementById('deptName').value = d.name;
            document.getElementById('deptTemplate').value = d.privilege_template_id || "";
            document.getElementById('deptModal').classList.remove('hidden');
        }
        function openAssignModal(deptId) {
            document.getElementById('assignDeptId').value = deptId;
            document.getElementById('assignModal').classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>
</body>
</html>
