<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

$success_msg = '';
$error_msg = '';

function ajax_response($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

$is_ajax = (($_POST['ajax'] ?? '') === '1');

// Handle Add/Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_project'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        if ($is_ajax) ajax_response('error', 'Invalid security token');
        csrf_error_response();
    }
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $client_name = trim($_POST['client_name'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = $_POST['content'] ?? '';
    $image_url = trim($_POST['image_url'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'Active';

    if (empty($title)) {
        if ($is_ajax) ajax_response('error', 'Project title is required.');
        $error_msg = 'Project title is required.';
    } else {
        // Handle image upload (replaces URL-based image)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/showcase/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $max_size = 5 * 1024 * 1024;

            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= $max_size) {
                $new_filename = 'showcase_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                    $image_url = 'uploads/showcase/' . $new_filename;
                }
            } elseif (!in_array($ext, $allowed)) {
                if ($is_ajax) ajax_response('error', 'Invalid image type. Allowed: JPG, PNG, WebP, GIF.');
                $error_msg = 'Invalid image type. Allowed: JPG, PNG, WebP, GIF.';
            } elseif ($_FILES['image']['size'] > $max_size) {
                if ($is_ajax) ajax_response('error', 'Image is too large. Max size is 5MB.');
                $error_msg = 'Image is too large. Max size is 5MB.';
            }
        }

        if (empty($error_msg)) {
            if ($id > 0) {
                $stmt = $conn->prepare("UPDATE showcase_projects SET title=?, category=?, client_name=?, year=?, description=?, content=?, image_url=?, sort_order=?, status=? WHERE id=?");
                $stmt->bind_param("sssssssssi", $title, $category, $client_name, $year, $description, $content, $image_url, $sort_order, $status, $id);
                $stmt->execute();
                $stmt->close();
                log_audit($conn, 'Update', 'ShowcaseProject', 'Admin', $admin_id, "Updated project: $title");
                if ($is_ajax) ajax_response('success', 'Project updated.');
                $success_msg = 'Project updated.';
            } else {
                $stmt = $conn->prepare("INSERT INTO showcase_projects (title, category, client_name, year, description, content, image_url, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", $title, $category, $client_name, $year, $description, $content, $image_url, $sort_order, $status);
                $stmt->execute();
                $stmt->close();
                log_audit($conn, 'Create', 'ShowcaseProject', 'Admin', $admin_id, "Created project: $title");
                if ($is_ajax) ajax_response('success', 'Project created.');
                $success_msg = 'Project created.';
            }
        }
    }
}

// Handle Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_project'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }
    $id = (int)$_POST['delete_project'];
    $stmt = $conn->prepare("DELETE FROM showcase_projects WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    log_audit($conn, 'Delete', 'ShowcaseProject', 'Admin', $admin_id, "Deleted project #$id");
    $success_msg = 'Project deleted.';
}

// Fetch all projects
$projects = [];
$res = $conn->query("SELECT * FROM showcase_projects ORDER BY sort_order ASC, id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $projects[] = $row;
}

$page_title = 'Showcase Projects';
$page_subtitle = 'Case Studies & Portfolio';
$page_header_actions = '<button onclick="openProjectModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
    <span class="material-symbols-outlined text-sm">add_box</span> NEW PROJECT
</button>';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Showcase Projects | Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(6px);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem}
        .modal-overlay.open{display:flex}
    </style>
    <script src="../components/wysiwyg.js"></script>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">
<script src="../components/admin_sidenav.js?v=2" data-root="../"></script>

<div id="toast" class="fixed top-6 right-6 z-[400] transform <?php echo ($success_msg || $error_msg) ? 'translate-x-0' : 'translate-x-[150%]'; ?> transition-transform duration-300 pointer-events-none">
    <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span id="toastIcon" class="material-symbols-outlined <?php echo $error_msg ? 'text-red-500' : 'text-primary'; ?>"><?php echo $error_msg ? 'error' : 'check_circle'; ?></span>
        <p id="toastMessage" class="text-xs font-bold"><?php echo htmlspecialchars($success_msg ?: $error_msg); ?></p>
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
                <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold font-headline text-slate-900">All Projects</h2>
                        <p class="text-xs text-slate-500 mt-1"><?php echo count($projects); ?> total projects</p>
                    </div>
                    <button onclick="openProjectModal()" class="lg:hidden px-4 py-2.5 bg-primary text-on-primary rounded-xl font-bold text-xs uppercase tracking-widest flex items-center gap-2 hover:shadow-lg transition-all">
                        <span class="material-symbols-outlined text-sm">add</span> Add
                    </button>
                </div>
                <div class="p-8">
                    <?php if (empty($projects)): ?>
                    <p class="text-center py-10 text-slate-400 text-xs italic">No projects yet. Click "New Project" to add your first case study.</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($projects as $p): ?>
                        <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl hover:border-primary/30 transition-all group">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <?php if ($p['image_url']): ?>
                                <div class="w-16 h-12 rounded-lg overflow-hidden shrink-0 bg-slate-100">
                                    <img src="<?php echo htmlspecialchars(preg_match('#^https?://#', $p['image_url']) ? $p['image_url'] : '../' . $p['image_url']); ?>" class="w-full h-full object-cover" alt="">
                                </div>
                                <?php endif; ?>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($p['title']); ?></span>
                                        <span class="px-2 py-0.5 bg-<?php echo $p['status']=='Active'?'emerald-100 text-emerald-700':'slate-100 text-slate-500'; ?> rounded-md text-[9px] font-bold uppercase tracking-widest"><?php echo $p['status']; ?></span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($p['category']); ?></span>
                                        <?php if ($p['year']): ?>
                                        <span class="text-[10px] text-slate-400">|</span>
                                        <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($p['year']); ?></span>
                                        <?php endif; ?>
                                        <span class="text-[10px] text-slate-400">|</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Order: <?php echo (int)$p['sort_order']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0 ml-4">
                                <button onclick="openProjectModal(<?php echo $p['id']; ?>)" class="p-2 text-slate-300 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this project?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="delete_project" value="<?php echo $p['id']; ?>">
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

<!-- Project Modal -->
<div id="projectModal" class="modal-overlay">
    <div class="bg-white w-full max-w-2xl rounded-[2rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-6 sm:p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
            <div>
                <h3 id="modalTitle" class="font-bold text-xl font-headline text-slate-900">Add New Project</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showcase Case Study</p>
            </div>
            <button onclick="closeProjectModal()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="overflow-y-auto custom-scrollbar flex-1">
            <form id="projectForm" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-5">
                <input type="hidden" name="save_project" value="1">
                <input type="hidden" name="ajax" value="1">
                <input type="hidden" name="id" id="projectId">
                <input type="hidden" name="image_url" id="existingImageUrl">
                <?= get_csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Project Title *</label>
                        <input type="text" name="title" id="projectTitle" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Category</label>
                        <input type="text" name="category" id="projectCategory" placeholder="e.g. Commissioning" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Client Name</label>
                        <input type="text" name="client_name" id="projectClient" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Year</label>
                        <input type="text" name="year" id="projectYear" placeholder="e.g. 2024" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sort Order</label>
                        <input type="number" name="sort_order" id="projectSort" value="0" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Project Image</label>
                    <div id="imagePreviewWrap" class="hidden mb-2">
                        <img id="imagePreview" class="w-full h-40 object-cover rounded-2xl border border-slate-100" alt="Preview">
                    </div>
                    <label class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed border-slate-200 rounded-2xl px-4 py-8 cursor-pointer hover:border-primary/40 transition-colors bg-slate-50/50">
                        <span class="material-symbols-outlined text-slate-300 text-3xl">image</span>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Upload Project Image</span>
                        <span class="text-[9px] text-slate-300">JPG, PNG, WebP, GIF — max 5MB</span>
                        <input type="file" name="image" id="projectImage" accept="image/*" class="hidden">
                    </label>
                    <p class="text-[9px] text-slate-300 ml-1">Leave empty to keep the current image when editing.</p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Description (Summary)</label>
                    <textarea name="description" id="projectDesc" rows="3" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Case Study Content</label>
                    <textarea name="content" id="showcaseContent" class="wysiwyg"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" id="projectStatus" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" onclick="closeProjectModal()" class="px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" id="projectSubmitBtn" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Add Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const SHOWCASE_PROJECTS = <?php echo json_encode($projects, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        document.getElementById('toastMessage').innerText = msg;
        document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'error';
        document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] ${type==='success'?'bg-slate-900 text-white':'bg-red-600 text-white'}`;
        t.style.transform = 'translateX(0)';
        setTimeout(() => t.style.transform = 'translateX(150%)', 4000);
    }

    function imgSrc(url) {
        if (!url) return '';
        if (/^https?:\/\//i.test(url)) return url;
        return '../' + url;
    }

    function openProjectModal(id) {
        const form = document.getElementById('projectForm');
        form.reset();
        document.getElementById('imagePreviewWrap').classList.add('hidden');
        document.getElementById('imagePreview').src = '';
        window.WYSIWYG.setContent('showcaseContent', '');

        if (id) {
            const p = SHOWCASE_PROJECTS.find(x => x.id === id);
            if (!p) return;
            document.getElementById('modalTitle').innerText = 'Edit Project';
            document.getElementById('projectId').value = p.id;
            document.getElementById('existingImageUrl').value = p.image_url || '';
            document.getElementById('projectTitle').value = p.title;
            document.getElementById('projectCategory').value = p.category || '';
            document.getElementById('projectClient').value = p.client_name || '';
            document.getElementById('projectYear').value = p.year || '';
            document.getElementById('projectSort').value = p.sort_order || 0;
            document.getElementById('projectDesc').value = p.description || '';
            document.getElementById('projectStatus').value = p.status || 'Active';
            window.WYSIWYG.setContent('showcaseContent', p.content || '');
            if (p.image_url) {
                document.getElementById('imagePreview').src = imgSrc(p.image_url);
                document.getElementById('imagePreviewWrap').classList.remove('hidden');
            }
            document.getElementById('projectSubmitBtn').innerText = 'Update Project';
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Project';
            document.getElementById('projectId').value = '';
            document.getElementById('existingImageUrl').value = '';
            document.getElementById('projectStatus').value = 'Active';
            document.getElementById('projectSubmitBtn').innerText = 'Add Project';
        }
        document.getElementById('projectModal').classList.add('open');
    }

    function closeProjectModal() {
        document.getElementById('projectModal').classList.remove('open');
    }

    document.getElementById('projectImage').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            document.getElementById('imagePreview').src = URL.createObjectURL(this.files[0]);
            document.getElementById('imagePreviewWrap').classList.remove('hidden');
        }
    });

    document.getElementById('projectForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        document.getElementById('showcaseContent').value = window.WYSIWYG.getContent('showcaseContent');
        const btn = document.getElementById('projectSubmitBtn');
        btn.disabled = true;
        const original = btn.innerText;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin align-middle">sync</span> Saving...';
        const fd = new FormData(this);
        try {
            const res = await fetch(window.location.pathname, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'success') {
                showToast(data.message, 'success');
                closeProjectModal();
                setTimeout(() => location.reload(), 900);
            } else {
                showToast(data.message, 'error');
                btn.disabled = false;
                btn.innerText = original;
            }
        } catch (err) {
            showToast('Request failed: ' + err.message, 'error');
            btn.disabled = false;
            btn.innerText = original;
        }
    });
</script>
</body>
</html>
