<?php
include '../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;
$permissions = get_admin_permissions($admin_id);

// --- Fetch Dashboard Statistics ---

// Inquiries
$inq_stats = $conn->query("SELECT 
    COUNT(*) as total, 
    SUM(IF(status = 'New', 1, 0)) as pending 
    FROM inquiries")->fetch_assoc();

// Tickets
$ticket_stats = $conn->query("SELECT 
    COUNT(*) as total, 
    SUM(IF(status = 'Open' OR status = 'In Progress', 1, 0)) as active,
    SUM(IF(priority = 'Urgent' AND status != 'Closed' AND status != 'Resolved', 1, 0)) as urgent
    FROM tickets")->fetch_assoc();

// Projects
$proj_stats = $conn->query("SELECT 
    COUNT(*) as total, 
    SUM(IF(status = 'Active' OR status = 'Planning', 1, 0)) as ongoing,
    SUM(budget) as total_budget
    FROM projects")->fetch_assoc();

// Assets
$asset_stats = $conn->query("SELECT 
    COUNT(*) as total, 
    SUM(value) as total_value 
    FROM assets")->fetch_assoc();

// HSSE Safe Days
$last_lti = $conn->query("SELECT created_at FROM hsse_observations WHERE severity = 'High' ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
$safe_days = 0;
if ($last_lti) {
    $safe_days = (new DateTime())->diff(new DateTime($last_lti['created_at']))->days;
} else {
    $safe_days = get_setting('hsse_base_safe_days', 412);
}

// Recent Activity (Audit Logs)
$recent_logs = [];
$res = $conn->query("
    SELECT al.action_type, al.module, al.description, al.created_at, 
           IFNULL(a.name, 'System') as actor_name
    FROM audit_logs al
    LEFT JOIN admins a ON al.actor_id = a.id AND al.actor_type = 'Admin'
    ORDER BY al.created_at DESC LIMIT 5
");
while ($row = $res->fetch_assoc()) $recent_logs[] = $row;

// Recent Tickets
$recent_tickets = [];
$res = $conn->query("
    SELECT t.subject, t.status, t.priority, t.created_at, c.name as client_name
    FROM tickets t
    JOIN clients c ON t.client_id = c.id
    ORDER BY t.created_at DESC LIMIT 4
");
while ($row = $res->fetch_assoc()) $recent_tickets[] = $row;

?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Dashboard Overview | Terminal</title>
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
    <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 shrink-0 z-20">
        <div>
            <h1 class="text-2xl font-bold font-headline text-slate-900 leading-tight">Dashboard Overview</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></p>
        </div>
        <div class="hidden md:flex gap-4">
            <a href="inquiries.php" class="bg-slate-50 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-primary/10 hover:text-slate-900 transition-colors">
                <span class="material-symbols-outlined text-sm">inbox</span> Inquiries
                <?php if($inq_stats['pending'] > 0): ?><span class="bg-primary text-on-primary w-5 h-5 rounded-full flex items-center justify-center text-[9px]"><?php echo $inq_stats['pending']; ?></span><?php endif; ?>
            </a>
            <a href="tickets.php" class="bg-slate-900 text-white px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all">
                <span class="material-symbols-outlined text-sm">confirmation_number</span> Support Tickets
                <?php if($ticket_stats['active'] > 0): ?><span class="bg-primary text-on-primary px-1.5 rounded-md text-[9px]"><?php echo $ticket_stats['active']; ?></span><?php endif; ?>
            </a>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-10">
        
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Projects -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150"></div>
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">folder_special</span></div>
                    <span class="text-3xl font-black font-headline text-slate-900"><?php echo $proj_stats['ongoing']; ?></span>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-slate-900">Active Projects</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Out of <?php echo $proj_stats['total']; ?> Total</p>
                </div>
            </div>

            <!-- Tickets -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150"></div>
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">forum</span></div>
                    <span class="text-3xl font-black font-headline text-slate-900"><?php echo $ticket_stats['active']; ?></span>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-slate-900">Open Tickets</p>
                    <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest mt-1"><?php echo $ticket_stats['urgent']; ?> Urgent</p>
                </div>
            </div>

            <!-- HSSE Monitor -->
            <div onclick="location.href='hsse.php'" class="bg-slate-900 p-6 rounded-3xl shadow-xl shadow-slate-200 relative overflow-hidden group cursor-pointer hover:scale-[1.02] transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-primary/10 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150"></div>
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center"><span class="material-symbols-outlined text-xl">shield_with_heart</span></div>
                    <span class="text-3xl font-black font-headline text-white"><?php echo $safe_days; ?></span>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-white">Safe Days (HSSE)</p>
                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest mt-1">Zero-Harm Target</p>
                </div>
            </div>

            <!-- Inquiries -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150"></div>
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center"><span class="material-symbols-outlined text-xl">mark_email_unread</span></div>
                    <span class="text-3xl font-black font-headline text-slate-900"><?php echo $inq_stats['pending']; ?></span>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-slate-900">Pending Inquiries</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?php echo $inq_stats['total']; ?> All Time</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Recent Support Tickets -->
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-bold font-headline text-slate-900">Recent Support Tickets</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Client Requests & Issues</p>
                    </div>
                    <a href="tickets.php" class="text-[10px] font-bold text-primary hover:text-slate-900 uppercase tracking-widest transition-colors flex items-center gap-1">View All <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($recent_tickets)): ?>
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200"><p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No active tickets</p></div>
                    <?php endif; ?>
                    <?php foreach($recent_tickets as $t): ?>
                        <div class="p-4 rounded-2xl border border-slate-100 hover:border-primary/30 transition-colors flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 shrink-0"><span class="material-symbols-outlined">forum</span></div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate mb-1"><?php echo htmlspecialchars($t['subject']); ?></h3>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest truncate"><?php echo htmlspecialchars($t['client_name']); ?></p>
                            </div>
                            <div class="flex flex-col items-end shrink-0 gap-2">
                                <span class="text-[9px] font-bold text-slate-400"><?php echo date('M j', strtotime($t['created_at'])); ?></span>
                                <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest <?php echo $t['status']=='Open'?'bg-amber-50 text-amber-600':($t['status']=='In Progress'?'bg-blue-50 text-blue-600':($t['status']=='Resolved'?'bg-emerald-50 text-emerald-600':'bg-slate-100 text-slate-500')); ?>"><?php echo $t['status']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Global Activity Feed -->
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-bold font-headline text-slate-900">Live Activity Feed</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Latest Global Events</p>
                    </div>
                    <a href="audit_monitor.php" class="text-[10px] font-bold text-primary hover:text-slate-900 uppercase tracking-widest transition-colors flex items-center gap-1">Full Log <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                </div>
                
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-[15px] before:-translate-x-px before:h-full before:w-0.5 before:bg-slate-100">
                    <?php if (empty($recent_logs)): ?>
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200 relative z-10"><p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No activity logged</p></div>
                    <?php endif; ?>
                    <?php foreach($recent_logs as $log): 
                        $color = 'bg-slate-200';
                        if ($log['action_type'] === 'Create') $color = 'bg-emerald-400';
                        elseif ($log['action_type'] === 'Update') $color = 'bg-amber-400';
                        elseif ($log['action_type'] === 'Delete') $color = 'bg-red-400';
                    ?>
                        <div class="relative flex items-start gap-4 group">
                            <div class="w-[30px] h-[30px] rounded-full border-4 border-white <?php echo $color; ?> shadow-sm shrink-0 z-10"></div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <p class="text-sm text-slate-700 leading-snug"><span class="font-bold text-slate-900"><?php echo htmlspecialchars($log['actor_name']); ?></span> <?php echo htmlspecialchars($log['description']); ?></p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-[9px] font-bold text-primary uppercase tracking-widest"><?php echo htmlspecialchars($log['module']); ?></span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">&bull; <?php echo date('M j, h:i A', strtotime($log['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>