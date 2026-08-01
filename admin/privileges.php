<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();

// Define Modules
$modules = [
    'Dashboard' => '/dashboard',
    'Projects' => 'admin/project',
    'Assets' => 'admin/asset',
    'Procurement' => 'admin/procurement',
    'Inquiries' => 'admin/inquiries.php',
    'HSSE' => 'admin/hsse',
    'Departments' => 'admin/departments.php',
    'Privileges' => 'admin/privileges.php',
    'Settings' => 'admin/settings.php'
];

// Handle Actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            csrf_error_response();
        }
        if ($_POST['action'] == 'save_template') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $name = $_POST['name'];
            
            $perms = [];
            foreach ($modules as $mod => $path) {
                $perms[$mod] = [
                    'read' => isset($_POST["read_$mod"]),
                    'write' => isset($_POST["write_$mod"])
                ];
            }
            $perms_json = json_encode($perms);
            
            if ($id) {
                $stmt = $conn->prepare("UPDATE privilege_templates SET name = ?, permissions = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $perms_json, $id);
                $stmt->execute();
                $stmt->close();
                $message = "Template updated.";
            } else {
                $stmt = $conn->prepare("INSERT INTO privilege_templates (name, permissions) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $perms_json);
                $stmt->execute();
                $stmt->close();
                $message = "Template created.";
            }
        }
        if ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM privilege_templates WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $message = "Template removed.";
        }
    }
}

// Fetch Templates
$result = $conn->query("SELECT * FROM privilege_templates ORDER BY name ASC");
$templates = [];
while ($row = $result->fetch_assoc()) {
    $row['permissions'] = json_decode($row['permissions'], true);
    $templates[] = $row;
}

$page_title = 'Role Privileges';
$page_subtitle = '';
$page_header_actions = '';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Privilege Management | Terminal</title>
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
    
    <script src="../components/admin_sidenav.js?v=2" data-root="../"></script>

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

        <!-- Main Work Area -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-6">
            <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($templates as $t): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                        <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 text-sm"><?php echo htmlspecialchars($t['name']); ?></h3>
                            <div class="flex gap-2">
                                <button onclick='openEditor(<?php echo json_encode($t); ?>)' class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 rounded-lg text-slate-400 hover:text-primary transition-all">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                <form method="POST" onsubmit="return confirm('Delete this template?')">
                                    <?= get_csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center hover:bg-red-50 rounded-lg text-slate-400 hover:text-red-500 transition-all">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="p-5 flex-1 space-y-3">
                            <?php 
                            $count = 0;
                            foreach ($t['permissions'] as $mod => $p): 
                                if ($p['read'] || $p['write']):
                                    $count++;
                                    if ($count > 4) continue;
                            ?>
                                <div class="flex justify-between items-center text-[10px]">
                                    <span class="font-bold text-slate-500 uppercase tracking-tight"><?php echo $mod; ?></span>
                                    <div class="flex gap-2">
                                        <span class="px-2 py-0.5 rounded <?php echo $p['read'] ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-300'; ?> font-bold">R</span>
                                        <span class="px-2 py-0.5 rounded <?php echo $p['write'] ? 'bg-blue-50 text-blue-600' : 'bg-slate-50 text-slate-300'; ?> font-bold">W</span>
                                    </div>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            if ($count > 4) echo '<div class="text-[9px] text-slate-400 italic">+'.($count-4).' more modules...</div>';
                            if ($count == 0) echo '<div class="text-[9px] text-slate-400 italic">No privileges assigned.</div>';
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($templates)): ?>
                    <div class="col-span-full p-12 text-center text-slate-400 italic text-xs bg-white rounded-2xl border-2 border-dashed border-slate-100">No templates found. Create your first access protocol.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Template Editor Modal -->
    <div id="editorModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[110] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 id="modalTitle" class="font-bold text-slate-900">Configure Access Protocol</h3>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 rounded-full text-slate-400">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" class="flex flex-col flex-1 overflow-hidden">
                <?= get_csrf_field() ?>
                <input type="hidden" name="action" value="save_template">
                <input type="hidden" name="id" id="editId">
                
                <div class="p-6 space-y-6 flex-1 overflow-y-auto custom-scrollbar">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Protocol Name</label>
                        <input type="text" name="name" id="editName" required placeholder="e.g. Regional Manager" 
                               class="w-full bg-slate-50 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-1 focus:ring-primary transition-all">
                    </div>
                    
                    <div class="space-y-4">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Module Privileges</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($modules as $mod => $path): ?>
                                <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700"><?php echo $mod; ?></span>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" name="read_<?php echo $mod; ?>" id="read_<?php echo $mod; ?>" class="rounded border-slate-300 text-primary focus:ring-primary transition-all">
                                            <span class="text-[10px] font-bold text-slate-400 group-hover:text-emerald-600 transition-colors">READ</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" name="write_<?php echo $mod; ?>" id="write_<?php echo $mod; ?>" class="rounded border-slate-300 text-primary focus:ring-primary transition-all">
                                            <span class="text-[10px] font-bold text-slate-400 group-hover:text-blue-600 transition-colors">WRITE</span>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-50 flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">DISCARD</button>
                    <button type="submit" class="flex-1 py-3 rounded-2xl bg-primary text-on-primary text-xs font-bold hover:shadow-lg">SAVE PROTOCOL</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditor(template = null) {
            const modal = document.getElementById('editorModal');
            const title = document.getElementById('modalTitle');
            const idInput = document.getElementById('editId');
            const nameInput = document.getElementById('editName');
            
            // Reset all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            
            if (template) {
                title.innerText = "Edit Access Protocol";
                idInput.value = template.id;
                nameInput.value = template.name;
                
                for (const [mod, p] of Object.entries(template.permissions)) {
                    if (document.getElementById(`read_${mod}`)) document.getElementById(`read_${mod}`).checked = p.read;
                    if (document.getElementById(`write_${mod}`)) document.getElementById(`write_${mod}`).checked = p.write;
                }
            } else {
                title.innerText = "Configure Access Protocol";
                idInput.value = "";
                nameInput.value = "";
            }
            
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('editorModal').classList.add('hidden');
        }
    </script>
</body>
</html>
