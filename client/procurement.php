<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: login.php");
    exit();
}

$conn = get_db_connection();

// Fetch orders linked to client or their projects
$orders_res = safe_query($conn, "SELECT po.*, p.name as project_name FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = ? OR po.client_id = ?) ORDER BY po.created_at DESC", "ii", [$client_id, $client_id]);

// Stats
$active_orders = safe_query($conn, "SELECT COUNT(*) FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = ? OR po.client_id = ?) AND po.status != 'Delivered'", "ii", [$client_id, $client_id])->fetch_row()[0];
$in_transit = safe_query($conn, "SELECT COUNT(*) FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = ? OR po.client_id = ?) AND po.status = 'In Transit'", "ii", [$client_id, $client_id])->fetch_row()[0];
$held_customs = safe_query($conn, "SELECT COUNT(*) FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = ? OR po.client_id = ?) AND po.status = 'Held by Customs'", "ii", [$client_id, $client_id])->fetch_row()[0];
$delivered_orders = safe_query($conn, "SELECT COUNT(*) FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = ? OR po.client_id = ?) AND po.status = 'Delivered'", "ii", [$client_id, $client_id])->fetch_row()[0];

$page_title = 'Procurement & Logistics | Terminal';
$page_h1 = 'Order Tracking';
$page_h1_sub = 'Procurement & Logistics Terminal';
$page_h1_badge = 'Supply Chain';
$page_h1_action = '<button onclick="exportSelected()" class="bg-white border border-slate-200 text-on-surface px-4 py-2 rounded-full font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 hover:bg-slate-50 transition-all shadow-sm"><span class="material-symbols-outlined text-sm">download</span> Export Selected</button>';
$page_cdn_heads = '<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>';
$page_styles = '
        #detail-panel { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        #detail-panel.hidden-panel { transform: translateX(100%); }
    ';

ob_start();
?>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined">inventory_2</span>
        </div>
        <div>
            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 font-headline block">Active</span>
            <span class="text-xl font-bold text-on-surface"><?= $active_orders ?></span>
        </div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
            <span class="material-symbols-outlined">local_shipping</span>
        </div>
        <div>
            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 font-headline block">In-Transit</span>
            <span class="text-xl font-bold text-on-surface"><?= $in_transit ?></span>
        </div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500">
            <span class="material-symbols-outlined">gavel</span>
        </div>
        <div>
            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 font-headline block">Customs</span>
            <span class="text-xl font-bold text-on-surface"><?= $held_customs ?></span>
        </div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
            <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 font-headline block">Delivered</span>
            <span class="text-xl font-bold text-on-surface"><?= $delivered_orders ?></span>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="px-3 sm:px-6 py-4 w-10">
                    <input type="checkbox" id="select-all-export" class="rounded border-slate-300 text-primary focus:ring-primary/20">
                </th>
                <th class="px-3 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Order Ref</th>
                <th class="px-3 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Item Description</th>
                <th class="px-3 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Project Context</th>
                <th class="px-3 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Status</th>
                <th class="px-3 sm:px-6 py-4 text-right"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <?php if ($orders_res->num_rows > 0): 
                while($po = $orders_res->fetch_assoc()):
                    $status_color = 'slate-500';
                    if ($po['status'] == 'In Transit') $status_color = 'blue-500';
                    if ($po['status'] == 'Held by Customs') $status_color = 'red-500';
                    if ($po['status'] == 'Delivered') $status_color = 'emerald-500';
                    if ($po['status'] == 'Processing') $status_color = 'amber-500';
            ?>
            <tr class="hover:bg-slate-50/50 transition-colors group">
                <td class="px-3 sm:px-6 py-4">
                    <input type="checkbox" class="export-checkbox rounded border-slate-300 text-primary focus:ring-primary/20" value="<?= $po['id'] ?>">
                </td>
                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                    <span class="font-mono text-xs font-bold text-slate-400">#<?= htmlspecialchars($po['order_number']) ?></span>
                </td>
                <td class="px-3 sm:px-6 py-4 min-w-[160px]">
                    <div class="font-bold text-sm text-on-surface"><?= htmlspecialchars($po['item_name']) ?></div>
                    <div class="text-[10px] text-slate-400"><?= (int)$po['quantity'] ?> Units</div>
                </td>
                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                    <span class="text-xs text-slate-600"><?= htmlspecialchars($po['project_name'] ?: 'Maintenance Independent') ?></span>
                </td>
                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-<?= $status_color ?>"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-<?= $status_color ?>"><?= $po['status'] ?></span>
                    </div>
                </td>
                <td class="px-3 sm:px-6 py-4 text-right whitespace-nowrap">
                    <button onclick="openDetail(<?= htmlspecialchars(json_encode($po)) ?>)" class="px-4 py-1.5 rounded-lg bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-all">
                        View Details
                    </button>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="6" class="px-3 sm:px-6 py-20 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-200 block mb-2">local_shipping</span>
                    <p class="text-xs text-slate-400 italic">No procurement records found.</p>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();

$page_after_main = '
    <div id="detail-overlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-[1px] z-[60] hidden transition-opacity" onclick="closeDetail()"></div>
    <div id="detail-panel" class="fixed top-0 right-0 h-full w-full max-w-xl bg-white shadow-2xl z-[70] hidden-panel flex flex-col">
        <header class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <span id="detail-ref" class="font-mono text-[10px] font-bold text-slate-400">#ORD-0000</span>
                <h2 id="detail-title" class="text-xl font-bold font-headline text-on-surface mt-1">Item Detail</h2>
            </div>
            <button onclick="closeDetail()" class="p-2 text-slate-400 hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </header>
        <div class="flex border-b border-slate-100 bg-white">
            <button onclick="switchTab(\'overview\')" id="tab-overview" class="flex-1 py-4 text-[10px] font-bold uppercase tracking-widest text-primary border-b-2 border-primary">Overview</button>
            <button onclick="switchTab(\'track\')" id="tab-track" class="flex-1 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-on-surface transition-all">Track</button>
            <button onclick="switchTab(\'support\')" id="tab-support" class="flex-1 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-on-surface transition-all">Support</button>
        </div>
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6 lg:p-8">
            <div id="content-overview" class="tab-content space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-2 font-headline">Status</span>
                        <div id="detail-status-badge" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-bold uppercase">PENDING</div>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-2 font-headline">Tracking ID</span>
                        <div id="detail-tracking-id" class="text-sm font-mono font-bold">N/A</div>
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-headline">Product Specifications</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Unit Quantity</span>
                            <span id="detail-qty" class="text-xs font-bold text-on-surface">0 Units</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Unit Price</span>
                            <span id="detail-unit-price" class="text-xs font-bold text-on-surface">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Total Investment</span>
                            <span id="detail-total-price" class="text-xs font-bold text-primary">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Supplier/Vendor</span>
                            <span id="detail-supplier" class="text-xs font-bold text-on-surface">OEM Partner</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-slate-900 text-white rounded-2xl shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl">location_on</span>
                    </div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-2 font-headline">Current Logistical Point</span>
                    <div id="detail-location" class="text-xl font-bold">Awaiting Dispatch</div>
                    <p class="text-[10px] text-slate-500 mt-4 pt-4 border-t border-white/10">Last updated at <span id="detail-updated">--/--/--</span></p>
                </div>
            </div>
            <div id="content-track" class="tab-content hidden space-y-6">
                <div id="tracking-timeline" class="relative pl-8 space-y-8 before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100"></div>
            </div>
            <div id="content-support" class="tab-content hidden space-y-6">
                <div id="order-tickets-list" class="space-y-3 mb-6"></div>
                <div id="support-help-box" class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-4">support_agent</span>
                    <h4 class="font-bold text-on-surface font-headline">Need assistance with this order?</h4>
                    <p class="text-xs text-slate-500 mt-2">Open a priority ticket and our logistics team will respond within 24 engineering hours.</p>
                </div>
                <div id="ticket-success" class="hidden p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-bold text-center">Ticket submitted successfully! Our team will review it.</div>
                <form id="support-form" class="space-y-4">
                    ' . get_csrf_field() . '
                    <input type="hidden" name="order_id" id="support-order-id">
                    <input type="hidden" name="subject" id="support-subject">
                    <input type="hidden" name="ajax_ticket" value="1">
                    <div class="space-y-1">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest font-headline">Message to Logistics</label>
                        <textarea name="description" id="support-description" required class="w-full bg-white border border-slate-200 rounded-xl p-4 text-xs focus:ring-2 focus:ring-primary/20 transition-all outline-none" rows="5" placeholder="Detail your inquiry..."></textarea>
                    </div>
                    <button type="submit" id="support-submit-btn" class="w-full py-4 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">Submit Priority Ticket</button>
                </form>
            </div>
        </div>
    </div>
';

$page_scripts = '
<script>
    let currentOrder = null;

    function openDetail(order) {
        currentOrder = order;
        document.getElementById("detail-ref").innerText = "#" + order.order_number;
        document.getElementById("detail-title").innerText = order.item_name;
        document.getElementById("detail-status-badge").innerText = order.status;
        document.getElementById("detail-tracking-id").innerText = order.tracking_id || "N/A";
        document.getElementById("detail-qty").innerText = parseInt(order.quantity) + " Units";
        document.getElementById("detail-unit-price").innerText = "$" + parseFloat(order.unit_price).toLocaleString();
        document.getElementById("detail-total-price").innerText = "$" + parseFloat(order.total_price).toLocaleString();
        document.getElementById("detail-supplier").innerText = order.supplier || "OEM Partner";
        document.getElementById("detail-location").innerText = order.current_location || "Tracking initiated";
        document.getElementById("detail-updated").innerText = order.updated_at || order.created_at;
        document.getElementById("ticket-success").classList.add("hidden");
        document.getElementById("support-form").classList.add("hidden");
        document.getElementById("support-help-box").classList.add("hidden");
        document.getElementById("support-description").value = "";
        document.getElementById("support-order-id").value = order.id;
        document.getElementById("support-subject").value = "Inquiry regarding Order #" + order.order_number;
        fetchTrackingHistory(order.id);
        fetchOrderTickets(order.id);
        const panel = document.getElementById("detail-panel");
        const overlay = document.getElementById("detail-overlay");
        panel.classList.remove("hidden-panel");
        overlay.classList.remove("hidden");
        document.body.style.overflow = "hidden";
        switchTab("overview");
    }

    function closeDetail() {
        const panel = document.getElementById("detail-panel");
        const overlay = document.getElementById("detail-overlay");
        panel.classList.add("hidden-panel");
        overlay.classList.add("hidden");
        document.body.style.overflow = "";
    }

    function switchTab(tab) {
        document.querySelectorAll(".tab-content").forEach(c => c.classList.add("hidden"));
        document.getElementById("content-" + tab).classList.remove("hidden");
        document.querySelectorAll(\'[id^="tab-"]\').forEach(t => {
            t.classList.remove("text-primary", "border-primary");
            t.classList.add("text-slate-400", "border-transparent");
        });
        document.getElementById("tab-" + tab).classList.add("text-primary", "border-primary");
        document.getElementById("tab-" + tab).classList.remove("text-slate-400", "border-transparent");
    }

    function fetchTrackingHistory(orderId) {
        const timeline = document.getElementById("tracking-timeline");
        timeline.innerHTML = \'<p class="text-xs text-slate-400 italic">Syncing logistical history...</p>\';
        fetch("fetch_tracking.php?order_id=" + orderId)
            .then(r => r.json())
            .then(data => {
                timeline.innerHTML = "";
                if (!Array.isArray(data) || data.error) {
                    timeline.innerHTML = \'<p class="text-xs text-slate-400 italic">No movement logs found yet.</p>\';
                    return;
                }
                if (data.length === 0) {
                    timeline.innerHTML = \'<p class="text-xs text-slate-400 italic">No movement logs found yet.</p>\';
                } else {
                    data.forEach(log => {
                        const item = document.createElement("div");
                        item.className = "relative";
                        item.innerHTML = \'<div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-white border-4 border-primary z-10 shadow-sm"></div><div><span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest font-headline">\' + log.created_at + \'</span><h5 class="text-xs font-bold text-on-surface uppercase mt-1">\' + log.status + \'</h5><p class="text-[10px] text-slate-500 mt-1">\' + (log.location ? "Location: " + log.location : "") + \'</p>\' + (log.notes ? \'<p class="text-[10px] p-2 bg-slate-50 rounded-lg mt-2 border border-slate-100 text-slate-600">\' + log.notes + \'</p>\' : \'\') + \'</div>\';
                        timeline.appendChild(item);
                    });
                }
            })
            .catch(err => {
                timeline.innerHTML = \'<p class="text-xs text-slate-400 italic">No movement logs found yet.</p>\';
                console.error(err);
            });
    }

    function fetchOrderTickets(orderId) {
        const list = document.getElementById("order-tickets-list");
        const form = document.getElementById("support-form");
        const helpBox = document.getElementById("support-help-box");
        list.innerHTML = \'<p class="text-[10px] text-slate-400 italic">Syncing active inquiries...</p>\';
        fetch("fetch_order_tickets.php?order_id=" + orderId)
            .then(r => r.json())
            .then(data => {
                list.innerHTML = "";
                if (data.length > 0) {
                    const title = document.createElement("h4");
                    title.className = "text-[10px] font-bold text-slate-400 uppercase tracking-widest font-headline mb-3";
                    title.innerText = "Tickets for this Order";
                    list.appendChild(title);
                    data.forEach(t => {
                        const statusColors = {"Open": "bg-amber-100 text-amber-700", "In Progress": "bg-blue-100 text-blue-700", "Resolved": "bg-emerald-100 text-emerald-700", "Closed": "bg-slate-100 text-slate-500"};
                        const statusClass = statusColors[t.status] || "bg-slate-100 text-slate-600";
                        const item = document.createElement("div");
                        item.className = "p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-shadow cursor-pointer";
                        item.innerHTML = \'<div class="flex justify-between items-start mb-2"><span class="text-[10px] font-bold text-primary font-headline">#TK-\' + t.id + \'</span><span class="text-[9px] font-bold px-2 py-0.5 rounded-full \' + statusClass + \' uppercase tracking-wider">\' + t.status + \'</span></div><p class="text-xs font-bold text-on-surface line-clamp-1">\' + (t.subject || "Support Inquiry") + \'</p><p class="text-[10px] text-slate-400 mt-1">\' + (t.created_at || "") + \'</p>\';
                        item.onclick = () => window.open("tickets.php?ticket_id=" + t.id, "_self");
                        list.appendChild(item);
                    });
                    helpBox.querySelector("h4").innerText = "Need further assistance?";
                    helpBox.querySelector("p").innerText = "You can open another ticket for this order if you have a separate inquiry.";
                } else {
                    helpBox.querySelector("h4").innerText = "Need assistance with this order?";
                    helpBox.querySelector("p").innerText = "Open a priority ticket and our logistics team will respond within 24 engineering hours.";
                }
                helpBox.classList.remove("hidden");
                form.classList.remove("hidden");
            })
            .catch(err => console.error(err));
    }

    document.getElementById("support-form").onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById("support-submit-btn");
        const form = document.getElementById("support-form");
        const success = document.getElementById("ticket-success");
        btn.disabled = true;
        btn.innerText = "Processing...";
        const formData = new FormData(form);
        if (!formData.has("csrf_token")) {
            formData.append("csrf_token", document.querySelector("#support-form input[name=\'csrf_token\']")?.value || "");
        }
        fetch("tickets.php", { method: "POST", body: formData })
        .then(r => r.json())
        .then(data => { form.classList.add("hidden"); success.classList.remove("hidden"); fetchOrderTickets(currentOrder.id); })
        .catch(err => { form.classList.add("hidden"); success.classList.remove("hidden"); fetchOrderTickets(currentOrder.id); });
    };

    // ── Select All checkbox ────────────────────────────────────────────────
    document.addEventListener("DOMContentLoaded", function() {
        var selectAll = document.getElementById("select-all-export");
        if (selectAll) {
            selectAll.addEventListener("change", function() {
                document.querySelectorAll(".export-checkbox").forEach(function(cb) {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });

    async function exportSelected() {
        var checkboxes = document.querySelectorAll(".export-checkbox:checked");
        if (checkboxes.length === 0) {
            alert("Please select at least one order to export.");
            return;
        }
        var ids = Array.from(checkboxes).map(function(cb) { return cb.value; }).join(",");
        var btn = document.querySelector(\'button[onclick="exportSelected()"]\');
        var originalText = btn.innerHTML;
        btn.innerHTML = \'<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Fetching data...\';
        btn.disabled = true;
        try {
            var res = await fetch("export_order_data.php?ids=" + ids);
            var data = await res.json();
            if (data.status !== "success" || !data.orders || data.orders.length === 0) {
                alert(data.message || "Failed to fetch order data.");
                return;
            }
            generateManifestPDF(data.orders);
        } catch (err) {
            console.error("Export failed:", err);
            alert("Failed to export manifest. Check console for details.");
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    function generateManifestPDF(orders) {
        var win = window.open("", "_blank");
        if (!win) { alert("Please allow popups for this site to export."); return; }
        var html = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Procurement Manifest</title>
<style>
    @page { margin: 15mm; }
    body { font-family: "Segoe UI", Arial, sans-serif; color: #1a1a1a; padding: 0; margin: 0; }
    .page { page-break-after: always; padding: 20px; }
    .page:last-child { page-break-after: auto; }
    .header { border-bottom: 3px solid #EAB308; padding-bottom: 12px; margin-bottom: 20px; }
    .header h1 { font-size: 20px; font-weight: 800; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 1px; }
    .header .meta { font-size: 11px; color: #666; }
    .section { margin-bottom: 20px; }
    .section h2 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #EAB308; border-bottom: 1px solid #ddd; padding-bottom: 6px; margin: 0 0 10px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 20px; }
    .field { font-size: 11px; padding: 3px 0; }
    .field .label { color: #888; font-weight: 600; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; }
    .field .value { font-weight: 700; color: #1a1a1a; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    table th { background: #f5f5f5; text-align: left; padding: 6px 8px; font-weight: 700; text-transform: uppercase; font-size: 8px; letter-spacing: 0.5px; color: #666; border-bottom: 1px solid #ddd; }
    table td { padding: 6px 8px; border-bottom: 1px solid #eee; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-weight: 700; font-size: 9px; text-transform: uppercase; }
    .badge-processing { background: #fef3c7; color: #b45309; }
    .badge-transit { background: #dbeafe; color: #1d4ed8; }
    .badge-customs { background: #fee2e2; color: #b91c1c; }
    .badge-delivered { background: #d1fae5; color: #047857; }
    .badge-open { background: #fef3c7; color: #b45309; }
    .badge-resolved { background: #d1fae5; color: #047857; }
    .timeline { padding-left: 16px; border-left: 2px solid #EAB308; }
    .timeline-item { margin-bottom: 10px; position: relative; }
    .timeline-item:before { content: ""; position: absolute; left: -21px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #EAB308; border: 2px solid #fff; box-shadow: 0 0 0 1px #EAB308; }
    .timeline-date { font-size: 9px; color: #888; font-weight: 600; }
    .timeline-status { font-weight: 700; font-size: 11px; color: #1a1a1a; }
    .timeline-location { font-size: 10px; color: #555; }
    .timeline-notes { font-size: 10px; color: #666; background: #f9f9f9; padding: 4px 8px; border-radius: 4px; margin-top: 2px; }
    .footer { border-top: 1px solid #ddd; padding-top: 10px; margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style></head><body>`;
        orders.forEach(function(o, idx) {
            var statusClass = "processing";
            if (o.status === "In Transit") statusClass = "transit";
            else if (o.status === "Held by Customs") statusClass = "customs";
            else if (o.status === "Delivered") statusClass = "delivered";
            var badgeClass = "badge-" + statusClass;
            html += `<div class="page"><div class="header"><h1>Procurement Manifest</h1><div class="meta">Order #${o.order_number} | Listing ${idx + 1} of ${orders.length} | Generated ${new Date().toLocaleString()}</div></div>`;
            html += `<div class="section"><h2>Order Details</h2><div class="grid"><div class="field"><div class="label">Item</div><div class="value">${escHtml(o.item_name)}</div></div><div class="field"><div class="label">Status</div><div class="value"><span class="badge ${badgeClass}">${o.status}</span></div></div><div class="field"><div class="label">Order Ref</div><div class="value">#${o.order_number}</div></div><div class="field"><div class="label">Project</div><div class="value">${escHtml(o.project_name || "Maintenance Independent")}</div></div><div class="field"><div class="label">Quantity</div><div class="value">${parseInt(o.quantity)} Units</div></div><div class="field"><div class="label">Unit Price</div><div class="value">$${parseFloat(o.unit_price || 0).toLocaleString()}</div></div><div class="field"><div class="label">Total</div><div class="value">$${parseFloat(o.total_price || 0).toLocaleString()}</div></div><div class="field"><div class="label">Supplier</div><div class="value">${escHtml(o.supplier || "OEM Partner")}</div></div><div class="field"><div class="label">Tracking ID</div><div class="value">${o.tracking_id || "N/A"}</div></div><div class="field"><div class="label">Current Location</div><div class="value">${escHtml(o.current_location || "Awaiting Dispatch")}</div></div><div class="field"><div class="label">Last Updated</div><div class="value">${o.updated_at || o.created_at || "N/A"}</div></div></div></div>`;

            if (o.tracking && o.tracking.length > 0) {
                html += `<div class="section"><h2>Tracking Timeline</h2><div class="timeline">`;
                o.tracking.forEach(function(t) {
                    html += `<div class="timeline-item"><div class="timeline-date">${t.created_at}</div><div class="timeline-status">${t.status}</div>`;
                    if (t.location) html += `<div class="timeline-location">Location: ${t.location}</div>`;
                    if (t.notes) html += `<div class="timeline-notes">${escHtml(t.notes)}</div>`;
                    html += `</div>`;
                });
                html += `</div></div>`;
            } else {
                html += `<div class="section"><h2>Tracking Timeline</h2><p style="font-size:10px;color:#888;font-style:italic">No movement logs recorded yet.</p></div>`;
            }

            if (o.tickets && o.tickets.length > 0) {
                html += `<div class="section"><h2>Associated Tickets</h2><table><thead><tr><th>Ticket ID</th><th>Subject</th><th>Status</th><th>Created</th></tr></thead><tbody>`;
                o.tickets.forEach(function(tk) {
                    var tkStatusClass = tk.status === "Resolved" || tk.status === "Closed" ? "badge-resolved" : "badge-open";
                    html += `<tr><td>#TK-${tk.id}</td><td>${escHtml(tk.subject || "N/A")}</td><td><span class="badge ${tkStatusClass}">${tk.status}</span></td><td>${tk.created_at_formatted || ""}</td></tr>`;
                    if (tk.replies && tk.replies.length > 0) {
                        html += `<tr><td colspan="4" style="padding:4px 8px 12px"><div style="font-size:9px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">Conversation</div>`;
                        tk.replies.forEach(function(r) {
                            var senderLabel = r.sender_type === \'admin\' ? \'Staff\' : \'Client\';
                            html += `<div style="font-size:9px;padding:4px 8px;margin:2px 0;background:#f9f9f9;border-radius:4px;border-left:2px solid #EAB308"><strong>${senderLabel}</strong> <span style="color:#999">${r.created_at_formatted}</span><br>${escHtml(r.message)}</div>`;
                        });
                        html += `</td></tr>`;
                    }
                });
                html += `</tbody></table></div>`;
            } else {
                html += `<div class="section"><h2>Associated Tickets</h2><p style="font-size:10px;color:#888;font-style:italic">No tickets linked to this order.</p></div>`;
            }

            html += `<div class="footer">WilsOveWel Supply Chain Manifest | Confidential</div></div>`;
        });
        html += `</body></html>`;
        win.document.write(html);
        win.document.close();
        win.focus();
        win.print();
    }

    function escHtml(str) {
        if (!str) return "";
        var d = document.createElement("div");
        d.textContent = str;
        return d.innerHTML;
    }
</script>
';

require_once __DIR__ . '/../components/client_layout.php';
