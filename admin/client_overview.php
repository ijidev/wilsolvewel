<?php
include '../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

if (!isset($_GET['id'])) {
    header("Location: clients.php");
    exit;
}

$id = (int)$_GET['id'];
$res = $conn->query("SELECT * FROM clients WHERE id = $id");
$client = $res->fetch_assoc();

if (!$client) {
    echo "Client not found.";
    exit;
}

$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Client Overview | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
        <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">

<script src="../components/admin_sidenav.js" data-root="../"></script>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 z-20">
        <div class="flex items-center gap-4">
            <a href="clients.php" class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
            </a>
            <div>
                <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Client Overview</h1>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Client Profile & Information</p>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-10">
        <div class="max-w-4xl mx-auto">
            
            <!-- Details -->
            <div class="space-y-6">
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-20 h-20 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-2xl font-bold font-headline">
                            <?php echo strtoupper(substr($client['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900 font-headline"><?php echo htmlspecialchars($client['name']); ?></h2>
                            <p class="text-xs font-bold text-primary uppercase tracking-widest mt-1"><?php echo htmlspecialchars($client['company'] ?: 'Independent Client'); ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Address</p>
                            <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($client['email']); ?></p>
                        </div>
                        <div class="space-y-1.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Phone Number</p>
                            <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($client['phone'] ?: 'N/A'); ?></p>
                        </div>
                        <div class="space-y-1.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Account Status</p>
                            <div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest inline-block mt-1 <?php echo $client['status']=='Active'?'bg-emerald-50 text-emerald-600 border border-emerald-100':'bg-red-50 text-red-500 border border-red-100'; ?>"><?php echo $client['status']; ?></span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registered Date</p>
                            <p class="text-sm font-bold text-slate-900"><?php echo date('M j, Y', strtotime($client['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
