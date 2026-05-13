<?php
include '../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'save_asset') {
        $id = (int)($_POST['id'] ?? 0);
        $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
        $type = $conn->real_escape_string(trim($_POST['type'] ?? ''));
        $status = $conn->real_escape_string($_POST['status'] ?? 'Active');
        $location = $conn->real_escape_string($_POST['location'] ?? '');
        $value = (float)($_POST['value'] ?? 0);
        $purchase_date = $conn->real_escape_string($_POST['purchase_date'] ?? date('Y-m-d'));
        $project_id = (int)($_POST['project_id'] ?? 0);

        if (empty($name) || empty($type)) {
            echo json_encode(['status' => 'error', 'message' => 'Name and Type are required.']); exit;
        }

        $proj_sql = $project_id > 0 ? $project_id : "NULL";

        if ($id > 0) {
            $sql = "UPDATE assets SET name='$name', type='$type', status='$status', location='$location', value=$value, purchase_date='$purchase_date', project_id=$proj_sql WHERE id=$id";
            $conn->query($sql);
            if ($conn->error) { echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }
            log_audit($conn, 'Update', 'Asset', 'Admin', $admin_id, "Updated asset record: $name (ID: $id)");
            echo json_encode(['status' => 'success', 'message' => 'Asset updated.']);
        } else {
            $sql = "INSERT INTO assets (name, type, status, location, value, purchase_date, project_id) VALUES ('$name', '$type', '$status', '$location', $value, '$purchase_date', $proj_sql)";
            $conn->query($sql);
            if ($conn->error) { echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }
            $new_id = $conn->insert_id;
            log_audit($conn, 'Create', 'Asset', 'Admin', $admin_id, "Created new asset: $name (ID: $new_id)");
            echo json_encode(['status' => 'success', 'message' => 'Asset created.']);
        }
        exit;
    }

    if ($_GET['ajax_action'] == 'get_asset') {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT * FROM assets WHERE id = $id");
        echo json_encode($res->fetch_assoc());
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_asset') {
        $id = (int)$_GET['id'];
        $conn->query("DELETE FROM assets WHERE id = $id");
        log_audit($conn, 'Delete', 'Asset', 'Admin', $admin_id, "Deleted asset ID: $id");
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Fetch Assets
$assets = [];
$res = $conn->query("SELECT a.*, p.name as project_name FROM assets a LEFT JOIN projects p ON a.project_id = p.id ORDER BY a.created_at DESC");
while ($row = $res->fetch_assoc()) $assets[] = $row;

// Fetch Projects for Assignment
$projects = [];
$res = $conn->query("SELECT id, name FROM projects ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $projects[] = $row;

$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Asset Management | Terminal</title>
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
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Asset Register</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Company Equipment & Resources</p>
        </div>
        <button onclick="openAssetModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">add_box</span> LOG ASSET
        </button>
    </header>

    <main class="flex-1 overflow-y-auto custom-scrollbar p-6">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Asset Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Status & Location</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Assignment</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Value</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($assets)): ?>
                        <tr><td colspan="5" class="px-6 py-20 text-center"><span class="material-symbols-outlined text-4xl text-slate-200">inventory_2</span><p class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest">No assets logged</p></td></tr>
                    <?php endif; ?>
                    <?php foreach ($assets as $a): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($a['name']); ?></p>
                                <p class="text-[10px] font-bold text-primary uppercase tracking-widest"><?php echo htmlspecialchars($a['type']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-1.5 h-1.5 rounded-full <?php echo $a['status']=='Active'?'bg-emerald-500':($a['status']=='Maintenance'?'bg-amber-500':'bg-red-500'); ?>"></div>
                                    <span class="text-xs font-bold <?php echo $a['status']=='Active'?'text-emerald-600':($a['status']=='Maintenance'?'text-amber-600':'text-red-500'); ?>"><?php echo $a['status']; ?></span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium flex items-center gap-1"><span class="material-symbols-outlined text-sm">location_on</span> <?php echo htmlspecialchars($a['location'] ?: 'Unspecified'); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($a['project_id']): ?>
                                    <span class="px-3 py-1 rounded-full bg-slate-900 text-white text-[9px] font-bold tracking-widest flex items-center gap-1 inline-flex"><span class="material-symbols-outlined text-[10px]">folder_special</span> <?php echo htmlspecialchars($a['project_name']); ?></span>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-900">$<?php echo number_format($a['value'], 2); ?></p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Since <?php echo date('M Y', strtotime($a['purchase_date'])); ?></p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="editAsset(<?php echo $a['id']; ?>)" class="w-8 h-8 rounded-lg border border-slate-100 text-slate-400 hover:text-primary transition-all flex items-center justify-center"><span class="material-symbols-outlined text-lg">edit</span></button>
                                    <button onclick="deleteAsset(<?php echo $a['id']; ?>)" class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center"><span class="material-symbols-outlined text-lg">delete</span></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Asset Modal -->
<div id="assetModal" class="modal-overlay">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
            <div>
                <h3 id="modalTitle" class="font-bold text-xl text-slate-900 font-headline">Log Asset</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Company Resource</p>
            </div>
            <button onclick="closeAssetModal()" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="overflow-y-auto custom-scrollbar flex-1">
            <form id="assetForm" class="p-8 space-y-5">
                <input type="hidden" name="id" id="assetId">
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Asset Name</label>
                        <input type="text" name="name" id="assetName" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Type/Category</label>
                        <input type="text" name="type" id="assetType" placeholder="e.g. Heavy Machinery" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" id="assetStatus" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="Active">Active</option>
                            <option value="Maintenance">In Maintenance</option>
                            <option value="Retired">Retired / Sold</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Value ($)</label>
                        <input type="number" step="0.01" name="value" id="assetValue" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Location</label>
                    <input type="text" name="location" id="assetLocation" placeholder="e.g. Lagos Warehouse" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Purchase Date</label>
                        <input type="date" name="purchase_date" id="assetDate" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Assign to Project</label>
                        <select name="project_id" id="assetProject" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <option value="0">Unassigned (Independent)</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeAssetModal()" class="flex-1 py-4 rounded-2xl border border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Cancel</button>
                    <button type="submit" id="assetSaveBtn" class="flex-1 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-bold uppercase tracking-[0.2em]">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    document.getElementById('toastMessage').innerText = msg;
    document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'error';
    document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] ${type==='success'?'bg-slate-900 text-white':'bg-red-600 text-white'}`;
    t.style.transform = 'translateX(0)';
    setTimeout(() => t.style.transform = 'translateX(150%)', 4000);
}

function openAssetModal() {
    document.getElementById('modalTitle').innerText = 'Log Asset';
    document.getElementById('assetForm').reset();
    document.getElementById('assetId').value = '';
    document.getElementById('assetDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('assetModal').classList.add('open');
}

function closeAssetModal() { document.getElementById('assetModal').classList.remove('open'); }

async function editAsset(id) {
    const res = await fetch(`?ajax_action=get_asset&id=${id}`);
    const data = await res.json();
    document.getElementById('modalTitle').innerText = 'Edit Asset';
    document.getElementById('assetId').value = data.id;
    document.getElementById('assetName').value = data.name;
    document.getElementById('assetType').value = data.type;
    document.getElementById('assetStatus').value = data.status;
    document.getElementById('assetLocation').value = data.location || '';
    document.getElementById('assetValue').value = data.value;
    document.getElementById('assetDate').value = data.purchase_date;
    document.getElementById('assetProject').value = data.project_id || 0;
    document.getElementById('assetModal').classList.add('open');
}

document.getElementById('assetForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('assetSaveBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
        const fd = new FormData(this);
        const res = await fetch('?ajax_action=save_asset', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === 'success') {
            closeAssetModal();
            showToast(result.message);
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(result.message, 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Asset';
    }
});

async function deleteAsset(id) {
    if (!confirm('Delete this asset?')) return;
    const res = await fetch(`?ajax_action=delete_asset&id=${id}`);
    const result = await res.json();
    if (result.status === 'success') location.reload();
    else showToast(result.message, 'error');
}
</script>
</body>
</html>
