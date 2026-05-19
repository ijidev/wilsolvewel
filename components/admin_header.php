<header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-4 sm:px-6 shrink-0 z-20 relative">
    <div class="flex items-center gap-3 min-w-0">
        <button id="admin-sidebar-toggle" class="hidden p-1.5 -ml-1 text-slate-500 hover:text-blue-600 transition-colors rounded-lg hover:bg-slate-100">
            <span class="material-symbols-outlined text-xl">menu</span>
        </button>
        <div class="min-w-0">
            <h1 class="text-sm sm:text-lg font-bold font-headline text-slate-900 leading-tight truncate"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
            <?php if (!empty($page_subtitle)): ?>
                <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate"><?php echo htmlspecialchars($page_subtitle); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="flex items-center gap-1 sm:gap-2">
        <?php if (!empty($page_header_actions)): ?>
            <div class="hidden sm:flex items-center gap-2 mr-2"><?php echo $page_header_actions; ?></div>
        <?php endif; ?>
        <button id="admin-btn-notifications" class="relative p-2 text-slate-500 hover:text-blue-600 transition-colors rounded-lg hover:bg-slate-100">
            <span class="material-symbols-outlined text-xl">notifications</span>
            <span id="admin-notif-badge" class="hidden absolute top-1 right-1 min-w-[16px] h-4 bg-red-500 text-white text-[8px] font-bold flex items-center justify-center rounded-full border-2 border-white px-1 leading-none shadow-sm">0</span>
        </button>
        <a href="<?= app_url('admin/profile.php') ?>" class="p-2 text-slate-500 hover:text-blue-600 transition-colors rounded-lg hover:bg-slate-100 hidden sm:block">
            <span class="material-symbols-outlined text-xl">account_circle</span>
        </a>
    </div>
</header>

<!-- Admin Notifications Dropdown -->
<div id="admin-dropdown-notifications" class="hidden fixed top-16 right-4 sm:right-6 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
    <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
        <div>
            <span class="text-xs font-bold text-slate-900 uppercase tracking-widest">Notifications</span>
            <span id="admin-notif-count-label" class="text-[10px] text-slate-400 ml-2"></span>
        </div>
        <span id="admin-notif-mark-read" class="text-[10px] text-blue-600 cursor-pointer hover:underline hidden">Mark all read</span>
    </div>
    <div id="admin-notif-list" class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-slate-50">
        <div class="p-6 text-center text-slate-500">
            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">notifications_paused</span>
            <p class="text-xs">Loading...</p>
        </div>
    </div>
</div>

<?php
$ticket_count = 0;
$inquiry_count = 0;
if (isset($conn) && $conn) {
    try {
        $res = $conn->query("SELECT COUNT(*) FROM tickets WHERE status NOT IN ('Resolved','Closed')");
        if ($res) $ticket_count = (int)$res->fetch_row()[0];
    } catch (\Throwable $e) {}
    try {
        $res = $conn->query("SELECT COUNT(*) FROM inquiries WHERE status = 'New'");
        if ($res) $inquiry_count = (int)$res->fetch_row()[0];
    } catch (\Throwable $e) {}
}
?>
<!-- Mobile Bottom Nav -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] safe-area-bottom">
    <div class="grid grid-cols-5 items-center h-14 px-1">
        <a href="<?= app_url('admin/tickets.php') ?>" class="relative flex flex-col items-center justify-center gap-0.5 text-slate-400 hover:text-blue-600 transition-colors py-1">
            <span class="material-symbols-outlined text-xl">confirmation_number</span>
            <span class="text-[7px] font-bold uppercase tracking-widest">Ticket</span>
            <?php if ($ticket_count > 0): ?>
            <span class="absolute -top-0.5 right-0.5 min-w-[14px] h-3.5 bg-red-500 text-white text-[7px] font-bold flex items-center justify-center rounded-full px-1 leading-none"><?= $ticket_count > 99 ? '99+' : $ticket_count ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= app_url('admin/inquiries.php') ?>" class="relative flex flex-col items-center justify-center gap-0.5 text-slate-400 hover:text-blue-600 transition-colors py-1">
            <span class="material-symbols-outlined text-xl">inbox</span>
            <span class="text-[7px] font-bold uppercase tracking-widest">Inbox</span>
            <?php if ($inquiry_count > 0): ?>
            <span class="absolute -top-0.5 right-0.5 min-w-[14px] h-3.5 bg-red-500 text-white text-[7px] font-bold flex items-center justify-center rounded-full px-1 leading-none"><?= $inquiry_count > 99 ? '99+' : $inquiry_count ?></span>
            <?php endif; ?>
        </a>
        <button id="footer-sidebar-toggle" class="relative flex flex-col items-center justify-center gap-0.5 text-primary py-1 -mt-3">
            <span class="material-symbols-outlined text-2xl">grid_view</span>
            <span class="text-[7px] font-bold uppercase tracking-widest">Menu</span>
        </button>
        <button id="footer-notif-btn" class="relative flex flex-col items-center justify-center gap-0.5 text-slate-400 hover:text-blue-600 transition-colors py-1">
            <span class="material-symbols-outlined text-xl">notifications</span>
            <span class="text-[7px] font-bold uppercase tracking-widest">Alerts</span>
            <span id="footer-notif-badge" class="hidden absolute -top-0.5 right-0.5 min-w-[14px] h-3.5 bg-red-500 text-white text-[7px] font-bold flex items-center justify-center rounded-full px-1 leading-none">0</span>
        </button>
        <a href="<?= app_url('admin/profile.php') ?>" class="relative flex flex-col items-center justify-center gap-0.5 text-slate-400 hover:text-blue-600 transition-colors py-1">
            <span class="material-symbols-outlined text-xl">account_circle</span>
            <span class="text-[7px] font-bold uppercase tracking-widest">Profile</span>
            <?php if (!empty($_SESSION['admin_name'])): ?>
                <span class="absolute -top-0.5 right-0.5 w-2 h-2 bg-emerald-400 rounded-full border-2 border-white"></span>
            <?php endif; ?>
        </a>
    </div>
</nav>

<script src="<?= app_url('components/admin_topnav.js') ?>" data-root="<?= APP_ROOT ? APP_ROOT . '/' : '/' ?>"></script>