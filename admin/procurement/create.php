<?php
include '../../config.php';

$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['initiate_order'])) {
    $item_name = $conn->real_escape_string($_POST['item_name']);
    $quantity = (int)$_POST['quantity'];
    $unit_price = (float)$_POST['unit_price'];
    $total_price = $quantity * $unit_price;
    $supplier = $conn->real_escape_string($_POST['supplier']);
    $order_num = 'ORD-' . strtoupper(substr(uniqid(), -6));
    
    $sql = "INSERT INTO procurement_orders (order_number, item_name, quantity, unit_price, total_price, supplier, requested_by) 
            VALUES ('$order_num', '$item_name', $quantity, $unit_price, $total_price, '$supplier', $admin_id)";
    
    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    }
}

$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Initiate Procurement | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
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
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">
    
    <script src="../../components/admin_sidenav.js" data-root="../../"></script>

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 relative z-20">
            <div class="flex items-center gap-4">
                <a href="index.php" class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 rounded-lg text-slate-400">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div class="flex flex-col">
                    <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Initiate Order</h1>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Procurement Specification</p>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 lg:p-12">
            
            <form method="POST" class="max-w-4xl mx-auto grid grid-cols-12 gap-8 items-start pb-20">
                <!-- Left Column: Specs -->
                <div class="col-span-12 lg:col-span-8 space-y-8">
                    <section class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm space-y-8">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">inventory_2</span>
                            <h2 class="font-headline font-bold text-xl tracking-tight">Order Specifications</h2>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                            <div class="col-span-2 space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Item Nomenclature</label>
                                <input type="text" name="item_name" placeholder="e.g. Excavator Track Assembly (CAT-349-X02)" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-6 py-4 text-base font-bold text-slate-900 focus:ring-1 focus:ring-primary">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Quantity Required</label>
                                <div class="relative">
                                    <input type="number" name="quantity" id="qty" value="1" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-6 py-4 text-base font-bold text-slate-900 focus:ring-1 focus:ring-primary">
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 text-[10px] font-bold">UNITS</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Unit Price (Est. USD)</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="unit_price" id="uPrice" placeholder="0.00" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-6 py-4 text-base font-bold text-slate-900 focus:ring-1 focus:ring-primary">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs font-bold">$</span>
                                </div>
                            </div>

                            <div class="col-span-2 space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Preferred Supplier</label>
                                <input type="text" name="supplier" placeholder="e.g. Caterpillar Strategic Logistics" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-6 py-4 text-base font-bold text-slate-900 focus:ring-1 focus:ring-primary">
                            </div>
                        </div>
                    </section>

                    <section class="bg-white p-8 rounded-[3rem] border border-dashed border-slate-200">
                         <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-slate-400">architecture</span>
                                <h2 class="font-headline font-bold text-lg tracking-tight uppercase">Technical Schema</h2>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center py-12 bg-slate-50 rounded-[2.5rem] border border-dashed border-slate-200 group cursor-pointer hover:bg-slate-100 transition-all">
                            <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-primary transition-colors">cloud_upload</span>
                            <p class="text-[10px] font-bold text-slate-400 mt-4 uppercase tracking-[0.2em]">Upload ISO Diagram</p>
                        </div>
                    </section>
                </div>

                <!-- Right Column: Summary -->
                <div class="col-span-12 lg:col-span-4 space-y-8">
                    <section class="bg-slate-900 rounded-[3rem] shadow-2xl overflow-hidden border border-white/10">
                        <div class="p-8 pb-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/50 mb-1">Financial Analysis</p>
                            <h3 class="font-headline font-bold text-2xl tracking-tight text-white">Budget Summary</h3>
                        </div>
                        <div class="p-8 space-y-6 pt-0">
                            <div class="space-y-4 pt-6 border-t border-white/5">
                                <div class="flex justify-between text-xs text-white/60">
                                    <span>Est. Total Cost</span>
                                    <span id="totalDisplay" class="font-bold text-primary text-2xl tracking-tighter font-headline">$0.00</span>
                                </div>
                            </div>
                            <button type="submit" name="initiate_order" class="w-full bg-primary text-on-primary py-5 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                                INITIATE ORDER
                            </button>
                        </div>
                    </section>

                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="material-symbols-outlined text-slate-400">schedule</span>
                            <h3 class="font-headline font-bold text-sm tracking-tight uppercase">Operational Velocity</h3>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-relaxed font-medium">Standard procurement lead time is 14-21 days from initiation to Port Harcourt terminal delivery.</p>
                    </div>
                </div>
            </form>

        </main>
    </div>

    <script>
        const qty = document.getElementById('qty');
        const price = document.getElementById('uPrice');
        const display = document.getElementById('totalDisplay');

        function update() {
            const total = (qty.value * price.value) || 0;
            display.innerText = '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
        }

        qty.addEventListener('input', update);
        price.addEventListener('input', update);
    </script>
</body>
</html>
