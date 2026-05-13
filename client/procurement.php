<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: ../client-login.php");
    exit();
}

$conn = get_db_connection();

// Fetch orders linked to client's projects
$orders_res = $conn->query("
    SELECT po.*, p.name as project_name 
    FROM procurement_orders po 
    LEFT JOIN projects p ON po.project_id = p.id 
    WHERE (p.client_id = $client_id OR po.requested_by = $client_id)
    ORDER BY po.created_at DESC
");

// Stats
$active_orders = $conn->query("SELECT COUNT(*) FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = $client_id OR po.requested_by = $client_id) AND po.status != 'Delivered'")->fetch_row()[0];
$in_transit = $conn->query("SELECT COUNT(*) FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = $client_id OR po.requested_by = $client_id) AND po.status = 'In Transit'")->fetch_row()[0];
$held_customs = $conn->query("SELECT COUNT(*) FROM procurement_orders po LEFT JOIN projects p ON po.project_id = p.id WHERE (p.client_id = $client_id OR po.requested_by = $client_id) AND po.status = 'Held by Customs'")->fetch_row()[0];
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Procurement & Logistics | Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#EAB308",
                        "on-primary": "#000000",
                        "surface": "#FDFDFD",
                        "on-surface": "#1A1A1A",
                    },
                    fontFamily: { "headline": ["Space Grotesk"], "body": ["Manrope"], "label": ["Space Grotesk"] }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .technical-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.03; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        /* Slide-in panel animation */
        #detail-panel { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        #detail-panel.hidden-panel { transform: translateX(100%); }
    </style>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen overflow-x-hidden">
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <!-- TopNavBar -->
    <script src="../components/client_topnav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

    <main class="lg:ml-64 pt-20 pb-8 relative z-10">
        <div class="max-w-6xl mx-auto px-6">
            <header class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="bg-primary/10 text-primary text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded font-headline">Supply Chain</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-500 text-xs">Procurement & Logistics Terminal</span>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-on-surface font-headline">Order Tracking</h1>
                </div>
                <div class="flex gap-2">
                    <button class="bg-white border border-slate-200 text-on-surface px-4 py-2 rounded-full font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 hover:bg-slate-50 transition-all shadow-sm">
                        <span class="material-symbols-outlined text-sm">download</span> Export Manifest
                    </button>
                </div>
            </header>

            <!-- Stats Bar (Compact) -->
            <div class="grid grid-cols-3 gap-4 mb-8">
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
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Order Ref</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Item Description</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Project Context</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right"></th>
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
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-bold text-slate-400">#<?= htmlspecialchars($po['order_number']) ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm text-on-surface"><?= htmlspecialchars($po['item_name']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= (int)$po['quantity'] ?> Units • QTY Tracking</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-slate-600"><?= htmlspecialchars($po['project_name'] ?: 'Maintenance Independent') ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-<?= $status_color ?>"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-<?= $status_color ?>"><?= $po['status'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openDetail(<?= htmlspecialchars(json_encode($po)) ?>)" class="px-4 py-1.5 rounded-lg bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-all">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <span class="material-symbols-outlined text-4xl text-slate-200 block mb-2">local_shipping</span>
                                <p class="text-xs text-slate-400 italic">No procurement records found.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Detail Sliding Panel -->
    <div id="detail-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[60] hidden transition-opacity" onclick="closeDetail()"></div>
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

        <!-- Tabs -->
        <div class="flex border-b border-slate-100 bg-white">
            <button onclick="switchTab('overview')" id="tab-overview" class="flex-1 py-4 text-[10px] font-bold uppercase tracking-widest text-primary border-b-2 border-primary">Overview</button>
            <button onclick="switchTab('track')" id="tab-track" class="flex-1 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-on-surface transition-all">Track</button>
            <button onclick="switchTab('support')" id="tab-support" class="flex-1 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-on-surface transition-all">Support</button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-2 font-headline">Status</span>
                        <div id="detail-status-badge" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-bold uppercase">
                            PENDING
                        </div>
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

            <!-- Track Tab -->
            <div id="content-track" class="tab-content hidden space-y-6">
                <div id="tracking-timeline" class="relative pl-8 space-y-8 before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                    <!-- Timeline items will be injected here -->
                </div>
            </div>

            <!-- Support Tab -->
            <div id="content-support" class="tab-content hidden space-y-6">
                <div id="order-tickets-list" class="space-y-3 mb-6">
                    <!-- Tickets will be loaded here -->
                </div>

                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-4">support_agent</span>
                    <h4 class="font-bold text-on-surface font-headline">Need assistance with this order?</h4>
                    <p class="text-xs text-slate-500 mt-2">Open a priority ticket and our logistics team will respond within 24 engineering hours.</p>
                </div>
                <div id="ticket-success" class="hidden p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-bold text-center">
                    Ticket submitted successfully! Our team will review it.
                </div>
                <form id="support-form" class="space-y-4">
                    <input type="hidden" name="order_id" id="support-order-id">
                    <input type="hidden" name="subject" id="support-subject">
                    <input type="hidden" name="ajax_ticket" value="1">
                    <div class="space-y-1">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest font-headline">Message to Logistics</label>
                        <textarea name="description" id="support-description" required class="w-full bg-white border border-slate-200 rounded-xl p-4 text-xs focus:ring-2 focus:ring-primary/20 transition-all outline-none" rows="5" placeholder="Detail your inquiry..."></textarea>
                    </div>
                    <button type="submit" id="support-submit-btn" class="w-full py-4 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-colors shadow-lg">
                        Submit Priority Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentOrder = null;

        function openDetail(order) {
            currentOrder = order;
            document.getElementById('detail-ref').innerText = '#' + order.order_number;
            document.getElementById('detail-title').innerText = order.item_name;
            document.getElementById('detail-status-badge').innerText = order.status;
            document.getElementById('detail-tracking-id').innerText = order.tracking_id || 'N/A';
            document.getElementById('detail-qty').innerText = parseInt(order.quantity) + ' Units';
            document.getElementById('detail-unit-price').innerText = '$' + parseFloat(order.unit_price).toLocaleString();
            document.getElementById('detail-total-price').innerText = '$' + parseFloat(order.total_price).toLocaleString();
            document.getElementById('detail-supplier').innerText = order.supplier || 'OEM Partner';
            document.getElementById('detail-location').innerText = order.current_location || 'Tracking initiated';
            document.getElementById('detail-updated').innerText = order.updated_at || order.created_at;
            
            document.getElementById('ticket-success').classList.add('hidden');
            document.getElementById('support-form').classList.remove('hidden');
            document.getElementById('support-description').value = '';

            // Fetch History & Tickets
            fetchTrackingHistory(order.id);
            fetchOrderTickets(order.id);

            const panel = document.getElementById('detail-panel');
            const overlay = document.getElementById('detail-overlay');
            panel.classList.remove('hidden-panel');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            switchTab('overview');
        }

        function closeDetail() {
            const panel = document.getElementById('detail-panel');
            const overlay = document.getElementById('detail-overlay');
            panel.classList.add('hidden-panel');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById('content-' + tab).classList.remove('hidden');
            
            document.querySelectorAll('[id^="tab-"]').forEach(t => {
                t.classList.remove('text-primary', 'border-primary');
                t.classList.add('text-slate-400', 'border-transparent');
            });
            document.getElementById('tab-' + tab).classList.add('text-primary', 'border-primary');
            document.getElementById('tab-' + tab).classList.remove('text-slate-400', 'border-transparent');
        }

        function fetchTrackingHistory(orderId) {
            const timeline = document.getElementById('tracking-timeline');
            timeline.innerHTML = '<p class="text-xs text-slate-400 italic">Syncing logistical history...</p>';
            
            fetch(`fetch_tracking.php?order_id=${orderId}`)
                .then(r => r.json())
                .then(data => {
                    timeline.innerHTML = '';
                    if (data.length === 0) {
                        timeline.innerHTML = '<p class="text-xs text-slate-400 italic">No movement logs found yet.</p>';
                    } else {
                        data.forEach(log => {
                            const item = document.createElement('div');
                            item.className = 'relative';
                            item.innerHTML = `
                                <div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-white border-4 border-primary z-10 shadow-sm"></div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest font-headline">${log.created_at}</span>
                                    <h5 class="text-xs font-bold text-on-surface uppercase mt-1">${log.status}</h5>
                                    <p class="text-[10px] text-slate-500 mt-1">${log.location ? 'Location: ' + log.location : ''}</p>
                                    ${log.notes ? `<p class="text-[10px] p-2 bg-slate-50 rounded-lg mt-2 border border-slate-100 text-slate-600">${log.notes}</p>` : ''}
                                </div>
                            `;
                            timeline.appendChild(item);
                        });
                    }
                })
                .catch(err => {
                    timeline.innerHTML = '<p class="text-xs text-red-500 italic">Error fetching logistical data. Terminal offline.</p>';
                    console.error(err);
                });
        }

        function fetchOrderTickets(orderId) {
            const list = document.getElementById('order-tickets-list');
            list.innerHTML = '<p class="text-[10px] text-slate-400 italic">Syncing active inquiries...</p>';
            
            fetch(`fetch_order_tickets.php?order_id=${orderId}`)
                .then(r => r.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.length > 0) {
                        const title = document.createElement('h4');
                        title.className = 'text-[10px] font-bold text-slate-400 uppercase tracking-widest font-headline mb-3';
                        title.innerText = 'Active Tickets for this Order';
                        list.appendChild(title);
                        
                        data.forEach(t => {
                            const item = document.createElement('div');
                            item.className = 'p-3 bg-white border border-slate-100 rounded-xl shadow-sm flex justify-between items-center';
                            item.innerHTML = `
                                <div>
                                    <span class="block text-[10px] font-bold text-primary font-headline">#TK-${t.id}</span>
                                    <span class="block text-xs font-bold text-on-surface line-clamp-1">${t.subject}</span>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 uppercase tracking-wider">${t.status}</span>
                            `;
                            list.appendChild(item);
                        });
                    }
                })
                .catch(err => console.error(err));
        }

        // Handle Support Ticket AJAX
        document.getElementById('support-form').onsubmit = function(e) {
            e.preventDefault();
            const btn = document.getElementById('support-submit-btn');
            const form = document.getElementById('support-form');
            const success = document.getElementById('ticket-success');
            
            btn.disabled = true;
            btn.innerText = "Processing...";
            
            const formData = new FormData(form);
            fetch('tickets.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                form.classList.add('hidden');
                success.classList.remove('hidden');
                fetchOrderTickets(currentOrder.id);
            })
            .catch(err => {
                // Check if it's actually success (sometimes PHP redirects cause fetch errors)
                form.classList.add('hidden');
                success.classList.remove('hidden');
                fetchOrderTickets(currentOrder.id);
            });
        };
    </script>
</body>
</html>
