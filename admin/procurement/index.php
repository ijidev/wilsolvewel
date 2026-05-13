<?php
include '../../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

// AJAX
if (isset($_GET['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax_action'] == 'get_history') {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT h.*, a.name as admin_name FROM procurement_history h LEFT JOIN admins a ON h.updated_by = a.id WHERE h.order_id = $id ORDER BY h.created_at DESC");
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode($rows);
        exit;
    }

    if ($_GET['ajax_action'] == 'save_update') {
        $order_id = (int)$_POST['order_id'];
        $status = $conn->real_escape_string($_POST['status']);
        $location = $conn->real_escape_string($_POST['location']);
        $tracking_id = $conn->real_escape_string($_POST['tracking_id']);
        $notes = $conn->real_escape_string($_POST['notes']);

        $conn->query("INSERT INTO procurement_history (order_id, admin_id, status, location, tracking_id, notes) 
                      VALUES ($order_id, $admin_id, '$status', '$location', '$tracking_id', '$notes')");
        $conn->query("UPDATE procurement_orders SET status='$status' WHERE id=$order_id");
        
        log_audit($conn, 'Update', 'Procurement', 'Admin', $admin_id, "Logged status update '$status' for order ID: $order_id");
        
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_GET['ajax_action'] == 'get_order') {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT o.*, a.name as requester_name FROM procurement_orders o LEFT JOIN admins a ON o.requested_by = a.id WHERE o.id = $id");
        echo json_encode($res->fetch_assoc());
        exit;
    }

    if ($_GET['ajax_action'] == 'delete_order') {
        $id = (int)$_GET['id'];
        $conn->query("DELETE FROM procurement_orders WHERE id = $id");
        log_audit($conn, 'Delete', 'Procurement', 'Admin', $admin_id, "Deleted procurement order ID: $id");
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$orders = [];
$res = $conn->query("SELECT o.*, a.name as requester_name FROM procurement_orders o LEFT JOIN admins a ON o.requested_by = a.id ORDER BY o.created_at DESC");
while ($row = $res->fetch_assoc()) $orders[] = $row;

$statuses = ['Pending', 'Order Confirmed', 'Processing', 'Dispatched', 'In Transit', 'Held by Customs', 'Awaiting Clearance', 'Out for Delivery', 'Delivered', 'Completed', 'Cancelled'];
$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Procurement Command | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .order-row{cursor:pointer;transition:all .2s}.order-row:hover{background:#FEFCE8}
        .order-row.active{background:#FEF9C3;border-left:3px solid #EAB308}
        .status-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:999px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em}
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex h-screen overflow-hidden">

<script src="../../components/admin_sidenav.js" data-root="../../"></script>

<div id="toast" class="fixed top-6 right-6 z-[400] transform translate-x-[150%] transition-transform duration-300 pointer-events-none">
    <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[280px]">
        <span id="toastIcon" class="material-symbols-outlined">check_circle</span>
        <p id="toastMessage" class="text-xs font-bold"></p>
    </div>
</div>

<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <!-- Header -->
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Procurement Command</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Global Supply Logistics</p>
        </div>
        <a href="create.php" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">add_shopping_cart</span> INITIATE ORDER
        </a>
    </header>

    <div class="flex-1 flex overflow-hidden gap-0">
        <!-- LEFT: Order List -->
        <div class="w-full lg:w-[420px] flex flex-col overflow-hidden border-r border-slate-100 shrink-0">
            <!-- Stats -->
            <div class="p-4 bg-white border-b border-slate-50 grid grid-cols-4 gap-3">
                <?php
                $pending = count(array_filter($orders, fn($o) => $o['status'] == 'Pending'));
                $intransit = count(array_filter($orders, fn($o) => in_array($o['status'], ['In Transit','Dispatched','Out for Delivery'])));
                $customs = count(array_filter($orders, fn($o) => in_array($o['status'], ['Held by Customs','Awaiting Clearance'])));
                $done = count(array_filter($orders, fn($o) => in_array($o['status'], ['Completed','Delivered'])));
                $total_val = array_sum(array_column($orders, 'total_price'));
                ?>
                <div class="bg-amber-50 p-3 rounded-2xl border border-amber-100">
                    <p class="text-[8px] font-bold text-amber-500 uppercase tracking-widest">Pending</p>
                    <p class="text-xl font-bold text-slate-900"><?php echo $pending; ?></p>
                </div>
                <div class="bg-blue-50 p-3 rounded-2xl border border-blue-100">
                    <p class="text-[8px] font-bold text-blue-500 uppercase tracking-widest">Transit</p>
                    <p class="text-xl font-bold text-slate-900"><?php echo $intransit; ?></p>
                </div>
                <div class="bg-orange-50 p-3 rounded-2xl border border-orange-100">
                    <p class="text-[8px] font-bold text-orange-500 uppercase tracking-widest">Customs</p>
                    <p class="text-xl font-bold text-slate-900"><?php echo $customs; ?></p>
                </div>
                <div class="bg-emerald-50 p-3 rounded-2xl border border-emerald-100">
                    <p class="text-[8px] font-bold text-emerald-500 uppercase tracking-widest">Cleared</p>
                    <p class="text-xl font-bold text-slate-900"><?php echo $done; ?></p>
                </div>
            </div>

            <!-- Order list -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <?php if (empty($orders)): ?>
                    <div class="p-12 text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-200">shopping_bag</span>
                        <p class="text-xs font-bold text-slate-400 mt-3 uppercase tracking-widest">No orders found</p>
                    </div>
                <?php endif; ?>
                <?php foreach ($orders as $o):
                    $sc = match(true) {
                        $o['status'] == 'Pending' => 'bg-amber-100 text-amber-700',
                        in_array($o['status'], ['In Transit','Dispatched']) => 'bg-blue-100 text-blue-700',
                        in_array($o['status'], ['Held by Customs','Awaiting Clearance']) => 'bg-orange-100 text-orange-700',
                        in_array($o['status'], ['Completed','Delivered']) => 'bg-emerald-100 text-emerald-700',
                        $o['status'] == 'Cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-slate-100 text-slate-500'
                    };
                ?>
                <div onclick="selectOrder(<?php echo $o['id']; ?>, this)"
                     class="order-row p-4 border-b border-slate-50 flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                        #<?php echo substr($o['order_number'], -4); ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-xs font-bold text-slate-900 truncate"><?php echo htmlspecialchars($o['item_name']); ?></p>
                            <span class="status-badge <?php echo $sc; ?> ml-2 shrink-0"><?php echo $o['status']; ?></span>
                        </div>
                        <p class="text-[10px] text-primary font-bold uppercase tracking-wider truncate"><?php echo htmlspecialchars($o['supplier']); ?></p>
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="text-[9px] text-slate-400 font-medium">$<?php echo number_format($o['total_price'], 2); ?></span>
                            <?php if ($o['tracking_id']): ?>
                                <span class="text-[9px] text-slate-400 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-xs">gps_fixed</span><?php echo $o['tracking_id']; ?></span>
                            <?php endif; ?>
                            <?php if ($o['current_location']): ?>
                                <span class="text-[9px] text-slate-400 flex items-center gap-1"><span class="material-symbols-outlined text-xs">location_on</span><?php echo htmlspecialchars($o['current_location']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RIGHT: Order Detail Panel -->
        <div id="detailPanel" class="flex-1 hidden lg:flex flex-col overflow-hidden bg-white">
            <!-- Placeholder -->
            <div id="detailPlaceholder" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl text-slate-200">local_shipping</span>
                </div>
                <h3 class="font-bold text-slate-900 font-headline text-lg">Logistics Command</h3>
                <p class="text-xs text-slate-400 max-w-xs mt-1">Select an order from the pipeline to view full tracking history and manage logistics.</p>
            </div>

            <!-- Detail Content -->
            <div id="detailContent" class="hidden flex-1 flex flex-col overflow-hidden">
                <!-- Detail Header -->
                <div class="p-6 border-b border-slate-50 bg-slate-50/50 shrink-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p id="d-order-num" class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">ORD-XXXX</p>
                            <h2 id="d-item" class="text-xl font-bold font-headline text-slate-900">Item Name</h2>
                            <p id="d-supplier" class="text-xs text-slate-400 font-medium mt-0.5">Supplier</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Value</p>
                            <p id="d-total" class="text-2xl font-bold text-slate-900 font-headline">$0.00</p>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="bg-white p-3 rounded-2xl border border-slate-100">
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Qty</p>
                            <p id="d-qty" class="text-sm font-bold text-slate-900">—</p>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-slate-100">
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Tracking ID</p>
                            <p id="d-tracking" class="text-sm font-bold text-slate-900 font-mono">—</p>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-slate-100">
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Location</p>
                            <p id="d-location" class="text-sm font-bold text-slate-900 truncate">—</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
                    <!-- Update Status Panel -->
                    <div class="bg-slate-50 rounded-[2rem] p-6 border border-slate-100">
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Log Logistics Update</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-1 block">Status</label>
                                <select id="newStatus" class="w-full bg-white border-slate-100 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:ring-1 focus:ring-primary">
                                    <?php foreach ($statuses as $s): ?>
                                        <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-1 block">Current Location</label>
                                    <input type="text" id="newLocation" placeholder="e.g. Rotterdam Port" class="w-full bg-white border-slate-100 rounded-xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-1 block">Tracking ID</label>
                                    <input type="text" id="newTracking" placeholder="e.g. MAEU123456" class="w-full bg-white border-slate-100 rounded-xl px-4 py-3 text-xs font-bold font-mono focus:ring-1 focus:ring-primary">
                                </div>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-1 block">Internal Note</label>
                                <textarea id="newNote" rows="2" placeholder="e.g. Shipment held pending HS code verification..." class="w-full bg-white border-slate-100 rounded-xl px-4 py-3 text-xs font-medium focus:ring-1 focus:ring-primary resize-none"></textarea>
                            </div>
                            <button onclick="submitUpdate()" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold text-[10px] uppercase tracking-[0.2em] hover:bg-black active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-sm">update</span> LOG UPDATE
                            </button>
                        </div>
                    </div>

                    <!-- History Timeline -->
                    <div>
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Shipment History</h3>
                        <div id="historyTimeline" class="space-y-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentOrderId = null;

const STATUS_COLORS = {
    'Pending': 'bg-amber-400',
    'Order Confirmed': 'bg-blue-400',
    'Processing': 'bg-indigo-400',
    'Dispatched': 'bg-cyan-500',
    'In Transit': 'bg-blue-500',
    'Held by Customs': 'bg-orange-500',
    'Awaiting Clearance': 'bg-orange-400',
    'Out for Delivery': 'bg-violet-500',
    'Delivered': 'bg-emerald-500',
    'Completed': 'bg-emerald-600',
    'Cancelled': 'bg-red-500'
};

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    document.getElementById('toastMessage').innerText = msg;
    document.getElementById('toastIcon').innerText = type === 'success' ? 'check_circle' : 'warning';
    document.getElementById('toastContent').className = `px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[280px] ${type === 'success' ? 'bg-slate-900 text-white' : 'bg-red-600 text-white'}`;
    toast.style.transform = 'translateX(0)';
    setTimeout(() => toast.style.transform = 'translateX(150%)', 4000);
}

async function selectOrder(id, el) {
    currentOrderId = id;
    document.querySelectorAll('.order-row').forEach(r => r.classList.remove('active'));
    el.classList.add('active');

    const [orderRes, histRes] = await Promise.all([
        fetch(`?ajax_action=get_order&id=${id}`),
        fetch(`?ajax_action=get_history&id=${id}`)
    ]);
    const order = await orderRes.json();
    const history = await histRes.json();

    document.getElementById('d-order-num').innerText = order.order_number;
    document.getElementById('d-item').innerText = order.item_name;
    document.getElementById('d-supplier').innerText = order.supplier;
    document.getElementById('d-total').innerText = '$' + parseFloat(order.total_price).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('d-qty').innerText = order.quantity + ' Units';
    document.getElementById('d-tracking').innerText = order.tracking_id || '—';
    document.getElementById('d-location').innerText = order.current_location || '—';

    // Pre-fill update form
    document.getElementById('newStatus').value = order.status;
    document.getElementById('newLocation').value = order.current_location || '';
    document.getElementById('newTracking').value = order.tracking_id || '';
    document.getElementById('newNote').value = '';

    renderHistory(history);
    document.getElementById('detailPlaceholder').classList.add('hidden');
    document.getElementById('detailContent').classList.remove('hidden');
    document.getElementById('detailContent').classList.add('flex');
}

function renderHistory(history) {
    const container = document.getElementById('historyTimeline');
    if (!history.length) {
        container.innerHTML = '<p class="text-[10px] text-slate-400 font-bold uppercase italic text-center py-6">No history logged yet</p>';
        return;
    }
    container.innerHTML = history.map((h, i) => `
        <div class="flex gap-4 ${i < history.length - 1 ? 'pb-6' : ''}">
            <div class="flex flex-col items-center shrink-0">
                <div class="w-3 h-3 rounded-full ${STATUS_COLORS[h.status] || 'bg-slate-300'} mt-1 shrink-0 ring-4 ring-white"></div>
                ${i < history.length - 1 ? '<div class="w-px flex-1 bg-slate-100 mt-1"></div>' : ''}
            </div>
            <div class="pb-1 min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] font-bold text-slate-900 uppercase tracking-widest">${h.status}</span>
                    ${h.location ? `<span class="text-[9px] text-slate-400 flex items-center gap-0.5"><span class="material-symbols-outlined" style="font-size:11px">location_on</span>${h.location}</span>` : ''}
                </div>
                ${h.note ? `<p class="text-[11px] text-slate-500 mt-1 italic">"${h.note}"</p>` : ''}
                <p class="text-[9px] text-slate-300 font-medium mt-1">${h.admin_name || 'System'} · ${new Date(h.created_at).toLocaleString()}</p>
            </div>
        </div>
    `).join('');
}

async function submitUpdate() {
    if (!currentOrderId) return;
    const fd = new FormData();
    fd.append('order_id', currentOrderId);
    fd.append('status', document.getElementById('newStatus').value);
    fd.append('location', document.getElementById('newLocation').value);
    fd.append('tracking_id', document.getElementById('newTracking').value);
    fd.append('note', document.getElementById('newNote').value);

    const res = await fetch('?ajax_action=update_status', { method: 'POST', body: fd });
    const result = await res.json();
    if (result.status === 'success') {
        showToast('Logistics update logged successfully.');
        setTimeout(() => location.reload(), 1200);
    } else {
        showToast('Failed to log update.', 'error');
    }
}

window.onload = () => {
    const firstRow = document.querySelector('.order-row');
    if (firstRow && window.innerWidth >= 1024) firstRow.click();
};
</script>
</body>
</html>
