<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

$success_msg = '';
$error_msg = '';

// Handle Add/Edit
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
            $stmt->execute();
            $stmt->close();
            log_audit($conn, 'Update', 'FAQCategory', 'Admin', $admin_id, "Updated category: $name");
            $success_msg = 'Category updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO faq_categories (name, slug, icon, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $slug, $icon, $sort_order);
            $stmt->execute();
            $stmt->close();
            log_audit($conn, 'Create', 'FAQCategory', 'Admin', $admin_id, "Created category: $name");
            $success_msg = 'Category created.';
        }
    }
}

// Handle Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_category'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) csrf_error_response();
    $id = (int)$_POST['delete_category'];
    $stmt = $conn->prepare("DELETE FROM faq_categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    log_audit($conn, 'Delete', 'FAQCategory', 'Admin', $admin_id, "Deleted category #$id");
    $success_msg = 'Category deleted.';
}

$categories = [];
$res = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM faqs WHERE category_id=c.id) as faq_count FROM faq_categories c ORDER BY c.sort_order ASC, c.id ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) $categories[] = $row;
}

$edit_category = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    foreach ($categories as $c) {
        if ($c['id'] == $id) { $edit_category = $c; break; }
    }
}

$page_title = 'FAQ Categories';
$page_subtitle = 'Manage FAQ topic categories';
$page_header_actions = '';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>FAQ Categories | Terminal</title>
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

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

    <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-3xl mx-auto space-y-8">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                    <h2 class="text-xl font-bold font-headline text-slate-900"><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h2>
                </div>
                <form method="POST" class="p-8 space-y-6">
                    <input type="hidden" name="save_category" value="1">
                    <?= get_csrf_field() ?>
                    <?php if ($edit_category): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                    <?php endif; ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Slug *</label>
                            <input type="text" name="slug" value="<?php echo htmlspecialchars($edit_category['slug'] ?? ''); ?>" required placeholder="e.g. reliability" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Icon (Material Symbol)</label>
                            <input type="text" name="icon" value="<?php echo htmlspecialchars($edit_category['icon'] ?? 'help'); ?>" placeholder="precision_manufacturing" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                            <input type="number" name="sort_order" value="<?php echo (int)($edit_category['sort_order'] ?? 0); ?>" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <?php if ($edit_category): ?>
                        <a href="faq_categories.php" class="px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors">Cancel</a>
                        <?php endif; ?>
                        <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg"><?php echo $edit_category ? 'Update' : 'Add Category'; ?></button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold font-headline text-slate-900">All Categories</h2>
                        <p class="text-xs text-slate-500 mt-1"><?php echo count($categories); ?> categories</p>
                    </div>
                </div>
                <div class="p-8">
                    <?php if (empty($categories)): ?>
                    <p class="text-center py-10 text-slate-400 text-xs italic">No categories yet.</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($categories as $c): ?>
                        <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl hover:border-primary/30 transition-all group">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined"><?php echo htmlspecialchars($c['icon']); ?></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($c['name']); ?></span>
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[9px] font-bold uppercase tracking-widest"><?php echo (int)$c['faq_count']; ?> FAQs</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">/<?php echo htmlspecialchars($c['slug']); ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0 ml-4">
                                <a href="?edit=<?php echo $c['id']; ?>" class="p-2 text-slate-300 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </a>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this category and all its FAQs?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="delete_category" value="<?php echo $c['id']; ?>">
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