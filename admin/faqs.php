<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

$success_msg = $error_msg = '';

// ── Handle FAQ Save ─────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_faq'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) csrf_error_response();
    $id = (int)($_POST['id'] ?? 0);
    $question = trim($_POST['question'] ?? '');
    $answer = $_POST['answer'] ?? '';
    $category_id = (int)($_POST['category_id'] ?? 0);
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'Active';
    if (empty($question) || empty($answer) || $category_id < 1) {
        $error_msg = 'Question, answer, and category are required.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE faqs SET question=?, answer=?, category_id=?, sort_order=?, status=? WHERE id=?");
            $stmt->bind_param("ssiisi", $question, $answer, $category_id, $sort_order, $status, $id);
            $stmt->execute(); $stmt->close();
            log_audit($conn, 'Update', 'FAQ', 'Admin', $admin_id, "Updated FAQ: $question");
            $success_msg = 'FAQ updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO faqs (question, answer, category_id, sort_order, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiis", $question, $answer, $category_id, $sort_order, $status);
            $stmt->execute(); $stmt->close();
            log_audit($conn, 'Create', 'FAQ', 'Admin', $admin_id, "Created FAQ: $question");
            $success_msg = 'FAQ created.';
        }
    }
}

// ── Handle FAQ Delete ────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_faq'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) csrf_error_response();
    $id = (int)$_POST['delete_faq'];
    $stmt = $conn->prepare("DELETE FROM faqs WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();
    log_audit($conn, 'Delete', 'FAQ', 'Admin', $admin_id, "Deleted FAQ #$id");
    $success_msg = 'FAQ deleted.';
}

// ── Handle Category Save ─────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_category'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) csrf_error_response();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $icon = trim($_POST['icon'] ?? 'help');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    if (empty($name) || empty($slug)) {
        $error_msg = 'Name and slug are required.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE faq_categories SET name=?, slug=?, icon=?, sort_order=? WHERE id=?");
            $stmt->bind_param("sssii", $name, $slug, $icon, $sort_order, $id);
            $stmt->execute(); $stmt->close();
            $success_msg = 'Category updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO faq_categories (name, slug, icon, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $slug, $icon, $sort_order);
            $stmt->execute(); $stmt->close();
            $success_msg = 'Category created.';
        }
    }
}

// ── Handle Category Delete ───────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_category'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) csrf_error_response();
    $id = (int)$_POST['delete_category'];
    $stmt = $conn->prepare("DELETE FROM faq_categories WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();
    $success_msg = 'Category deleted.';
}

// ── Fetch Data ───────────────────────────────────────────
$faqs = [];
$res = $conn->query("SELECT f.*, c.name as category_name, c.icon as category_icon FROM faqs f LEFT JOIN faq_categories c ON f.category_id=c.id ORDER BY c.sort_order ASC, f.sort_order ASC, f.id DESC");
if ($res) { while ($row = $res->fetch_assoc()) $faqs[] = $row; }

$categories = [];
$res = $conn->query("SELECT * FROM faq_categories ORDER BY sort_order ASC, id ASC");
if ($res) { while ($row = $res->fetch_assoc()) $categories[] = $row; }

$edit_faq = null;
if (isset($_GET['edit_faq'])) {
    foreach ($faqs as $f) { if ($f['id'] == $_GET['edit_faq']) { $edit_faq = $f; break; } }
}

$edit_category = null;
if (isset($_GET['edit_cat'])) {
    foreach ($categories as $c) { if ($c['id'] == $_GET['edit_cat']) { $edit_category = $c; break; } }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>FAQ Manager | Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .modal-bg{background:rgba(15,23,42,0.5);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px)}
    </style>
    <script src="../components/wysiwyg.js"></script>
    <script>
    function openModal(id){document.getElementById(id).classList.remove('hidden')}
    function closeModal(id){document.getElementById(id).classList.add('hidden')}
    </script>
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
    <header class="h-16 bg-white border-b border-slate-100 flex items-center px-6 shrink-0 z-20">
        <div class="flex flex-col">
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">FAQ Manager</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?php echo count($faqs); ?> entries across <?php echo count($categories); ?> categories</p>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Toolbar -->
            <div class="flex items-center justify-end gap-3">
                <button onclick="openModal('categoryModal')" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-slate-50 hover:border-primary/30 transition-all uppercase tracking-widest">
                    <span class="material-symbols-outlined text-sm">folder</span> <span class="hidden sm:inline">Categories</span>
                </button>
                <button onclick="openModal('faqModal')" class="flex items-center gap-2 bg-slate-900 text-white px-4 py-2.5 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg">
                    <span class="material-symbols-outlined text-sm">add</span> <span class="hidden sm:inline">Add FAQ</span>
                </button>
            </div>

            <!-- Mobile Toggle (hidden on lg+) -->
            <div class="flex lg:hidden gap-2 bg-white rounded-2xl p-1 border border-slate-100 shadow-sm">
                <button onclick="switchFaqTab('faqs')" id="faq-tab-faqs-btn" class="flex-1 px-5 py-3 rounded-xl text-xs font-bold transition-all bg-slate-900 text-white shadow-sm">FAQs</button>
                <button onclick="switchFaqTab('categories')" id="faq-tab-cats-btn" class="flex-1 px-5 py-3 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-900">Categories</button>
            </div>

            <!-- 70/30 Grid -->
            <div class="lg:grid lg:grid-cols-[7fr_3fr] lg:gap-6">

                <!-- FAQs Panel -->
                <div class="faq-panel max-lg:block lg:block">
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                        <?php if (empty($faqs)): ?>
                        <div class="text-center py-16">
                            <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">quiz</span>
                            <p class="text-xs text-slate-400 font-bold">No FAQs yet. Click "Add FAQ" to get started.</p>
                        </div>
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">Question</th>
                                        <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">Category</th>
                                        <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 py-4">Order</th>
                                        <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 py-4">Status</th>
                                        <th class="text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faqs as $f): ?>
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($f['question']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/5 rounded-lg text-[10px] font-bold text-primary uppercase tracking-widest">
                                                <span class="material-symbols-outlined text-[12px]"><?php echo htmlspecialchars($f['category_icon'] ?? 'help'); ?></span>
                                                <?php echo htmlspecialchars($f['category_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center text-xs font-bold text-slate-500"><?php echo (int)$f['sort_order']; ?></td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-2.5 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest <?php echo $f['status']=='Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'; ?>"><?php echo $f['status']; ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <button onclick='openEditFaq(<?php echo json_encode($f); ?>)' class="p-2 text-slate-300 hover:text-primary transition-colors">
                                                    <span class="material-symbols-outlined text-sm">edit</span>
                                                </button>
                                                <form method="POST" class="inline" onsubmit="return confirm('Delete this FAQ?')">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                    <input type="hidden" name="delete_faq" value="<?php echo $f['id']; ?>">
                                                    <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                                        <span class="material-symbols-outlined text-sm">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Categories Panel -->
                <div class="cat-panel max-lg:hidden lg:block">
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden lg:sticky lg:top-6">
                        <div class="px-8 py-5 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <div>
                                <h2 class="text-base font-bold font-headline text-slate-900">Categories</h2>
                                <p class="text-[10px] text-slate-500"><?php echo count($categories); ?> categories</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (empty($categories)): ?>
                            <p class="text-center py-6 text-xs text-slate-400 italic">No categories yet.</p>
                            <?php else: ?>
                            <div class="flex flex-wrap gap-3">
                                <?php foreach ($categories as $c): ?>
                                <div class="group flex items-center gap-2 px-4 py-2.5 bg-slate-50 rounded-xl border border-slate-100 hover:border-primary/30 transition-all">
                                    <span class="material-symbols-outlined text-primary text-sm"><?php echo htmlspecialchars($c['icon']); ?></span>
                                    <span class="text-xs font-bold text-slate-700"><?php echo htmlspecialchars($c['name']); ?></span>
                                    <span class="text-[9px] text-slate-400 font-bold">/<?php echo htmlspecialchars($c['slug']); ?></span>
                                    <div class="flex items-center gap-0.5 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick='openEditCat(<?php echo json_encode($c); ?>)' class="p-1 text-slate-300 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                        </button>
                                        <form method="POST" class="inline" onsubmit="return confirm('Delete this category and all its FAQs?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="delete_category" value="<?php echo $c['id']; ?>">
                                            <button type="submit" class="p-1 text-slate-300 hover:text-red-500 transition-colors">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <div class="mt-4">
                                <button onclick="openModal('categoryModal')" class="flex items-center gap-1.5 text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">
                                    <span class="material-symbols-outlined text-sm">add</span> Add Category
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- ─── FAQ Modal ─── -->
<div id="faqModal" class="fixed inset-0 z-[500] hidden flex items-center justify-center p-4">
    <div class="modal-bg absolute inset-0" onclick="closeModal('faqModal')"></div>
    <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <form method="POST" class="p-8 space-y-6">
            <input type="hidden" name="save_faq" value="1">
            <?= get_csrf_field() ?>
            <input type="hidden" name="id" id="faq_id" value="0">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold font-headline text-slate-900" id="faqModalTitle">Add FAQ</h2>
                <button type="button" onclick="closeModal('faqModal')" class="p-2 text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Question *</label>
                <input type="text" name="question" id="faq_question" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Category *</label>
                <div class="relative">
                    <select name="category_id" id="faq_category_id" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary appearance-none cursor-pointer">
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['slug']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </span>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Answer *</label>
                <textarea name="answer" id="faq_answer" class="wysiwyg"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                    <input type="number" name="sort_order" id="faq_sort_order" value="0" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                    <select name="status" id="faq_status" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('faqModal')" class="px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Save FAQ</button>
            </div>
        </form>
    </div>
</div>

<!-- ─── Category Modal ─── -->
<div id="categoryModal" class="fixed inset-0 z-[500] hidden flex items-center justify-center p-4">
    <div class="modal-bg absolute inset-0" onclick="closeModal('categoryModal')"></div>
    <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg">
        <form method="POST" class="p-8 space-y-6">
            <input type="hidden" name="save_category" value="1">
            <?= get_csrf_field() ?>
            <input type="hidden" name="id" id="cat_id" value="0">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold font-headline text-slate-900" id="catModalTitle">Add Category</h2>
                <button type="button" onclick="closeModal('categoryModal')" class="p-2 text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Name *</label>
                    <input type="text" name="name" id="cat_name" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Slug *</label>
                    <input type="text" name="slug" id="cat_slug" required placeholder="e.g. reliability" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="space-y-1.5 col-span-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Icon</label>
                    <input type="hidden" name="icon" id="cat_icon" value="help">
                    <button type="button" onclick="document.getElementById('iconPicker').classList.toggle('hidden'); this.classList.toggle('bg-primary/10'); this.classList.toggle('text-primary')" class="flex items-center gap-2 text-[10px] font-bold text-slate-500 hover:text-primary uppercase tracking-widest transition-all px-3 py-2 rounded-xl hover:bg-slate-50">
                        <span class="material-symbols-outlined text-lg" id="iconPreview">help</span>
                        <span>Choose Icon</span>
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                    <div id="iconPicker" class="hidden grid grid-cols-8 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-100 max-h-[200px] overflow-y-auto mt-2">
                        <?php
                        $icons = ['help','precision_manufacturing','architecture','inventory_2','engineering','build','handyman','settings','safety_check','health_and_safety','local_shipping','warehouse','science','biotech','electrical_services','plumbing','oil_barrel','energy','water_drop','ac_unit','construction','foundation','crane','forklift','agriculture','recycling','analytics','monitoring','query_stats','quiz','tips_and_updates','lightbulb','verified','security','lock','shield','support_agent','headset_mic','contact_support','forum','groups','partner_exchange','handshake','description','article','book','school','workspace_premium','award','globe','map','pin_drop','location_on'];
                        foreach ($icons as $ic):
                        ?>
                        <button type="button" onclick="pickIcon(this, '<?php echo $ic; ?>')" class="icon-option aspect-square flex items-center justify-center rounded-xl border border-slate-200 bg-white hover:border-primary hover:text-primary hover:bg-primary/5 transition-all text-slate-500">
                            <span class="material-symbols-outlined text-lg"><?php echo $ic; ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                    <input type="number" name="sort_order" id="cat_sort_order" value="0" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('categoryModal')" class="px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Save Category</button>
            </div>
        </form>
    </div>
</div>

<script>
function pickIcon(btn, name) {
    document.getElementById('cat_icon').value = name;
    document.getElementById('iconPreview').textContent = name;
    document.querySelectorAll('.icon-option').forEach(function(el) {
        el.classList.remove('border-primary', 'text-primary', 'bg-primary/5', 'ring-2', 'ring-primary/20');
        el.classList.add('border-slate-200', 'text-slate-500');
    });
    btn.classList.remove('border-slate-200', 'text-slate-500');
    btn.classList.add('border-primary', 'text-primary', 'bg-primary/5', 'ring-2', 'ring-primary/20');
}

// Auto-generate slug from name
document.addEventListener('DOMContentLoaded', function() {
    var nameInput = document.getElementById('cat_name');
    var slugInput = document.getElementById('cat_slug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (slugInput.dataset.manuallyEdited !== 'true') {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            }
        });
        slugInput.addEventListener('input', function() {
            slugInput.dataset.manuallyEdited = 'true';
        });
        // Reset flag when modal opens for new category
        var observer = new MutationObserver(function() {
            var catId = document.getElementById('cat_id').value;
            if (catId === '0' || catId === '') {
                slugInput.dataset.manuallyEdited = '';
            }
        });
        observer.observe(document.getElementById('categoryModal'), { attributes: true, attributeFilter: ['class'] });
    }
});

function openEditFaq(f) {
    document.getElementById('faq_id').value = f.id;
    document.getElementById('faq_question').value = f.question;
    document.getElementById('faq_category_id').value = f.category_id;
    document.getElementById('faq_sort_order').value = f.sort_order;
    document.getElementById('faq_status').value = f.status;
    document.getElementById('faqModalTitle').textContent = 'Edit FAQ';
    WYSIWYG.setContent('faq_answer', f.answer || '');
    openModal('faqModal');
}

function openEditCat(c) {
    document.getElementById('cat_id').value = c.id;
    document.getElementById('cat_name').value = c.name;
    document.getElementById('cat_slug').value = c.slug;
    document.getElementById('cat_sort_order').value = c.sort_order;
    document.getElementById('catModalTitle').textContent = 'Edit Category';
    document.getElementById('cat_slug').dataset.manuallyEdited = 'true';
    // Set icon picker
    var iconName = c.icon || 'help';
    document.getElementById('cat_icon').value = iconName;
    document.getElementById('iconPreview').textContent = iconName;
    document.querySelectorAll('.icon-option').forEach(function(el) {
        var span = el.querySelector('.material-symbols-outlined');
        if (span && span.textContent === (c.icon || 'help')) {
            el.classList.remove('border-slate-200', 'text-slate-500');
            el.classList.add('border-primary', 'text-primary', 'bg-primary/5', 'ring-2', 'ring-primary/20');
        } else {
            el.classList.remove('border-primary', 'text-primary', 'bg-primary/5', 'ring-2', 'ring-primary/20');
            el.classList.add('border-slate-200', 'text-slate-500');
        }
    });
    openModal('categoryModal');
}

document.querySelectorAll('.modal-bg').forEach(function(el) {
    el.addEventListener('click', function() { this.parentElement.classList.add('hidden'); });
});

// Mobile FAQ/Categories toggle
function switchFaqTab(tab) {
    var faqPanel = document.querySelector('.faq-panel');
    var catPanel = document.querySelector('.cat-panel');
    var faqBtn = document.getElementById('faq-tab-faqs-btn');
    var catBtn = document.getElementById('faq-tab-cats-btn');
    if (tab === 'faqs') {
        faqPanel.classList.remove('max-lg:hidden');
        faqPanel.classList.add('max-lg:block');
        catPanel.classList.remove('max-lg:block');
        catPanel.classList.add('max-lg:hidden');
        faqBtn.className = 'flex-1 px-5 py-3 rounded-xl text-xs font-bold transition-all bg-slate-900 text-white shadow-sm';
        catBtn.className = 'flex-1 px-5 py-3 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-900';
    } else {
        catPanel.classList.remove('max-lg:hidden');
        catPanel.classList.add('max-lg:block');
        faqPanel.classList.remove('max-lg:block');
        faqPanel.classList.add('max-lg:hidden');
        catBtn.className = 'flex-1 px-5 py-3 rounded-xl text-xs font-bold transition-all bg-slate-900 text-white shadow-sm';
        faqBtn.className = 'flex-1 px-5 py-3 rounded-xl text-xs font-bold transition-all text-slate-500 hover:text-slate-900';
    }
}
</script>
</body>
</html>