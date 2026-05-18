(function () {
    var rootPath = './';
    var script = document.currentScript;
    if (script && script.getAttribute('data-root')) rootPath = script.getAttribute('data-root');

    const topnavHTML = `
<!-- TopNavBar -->
<header class="flex justify-between items-center h-16 px-4 sm:px-8 lg:ml-64 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl sticky top-0 z-40 border-b border-slate-100 dark:border-slate-800">
<div class="flex items-center gap-4">
<button id="admin-sidebar-toggle" class="lg:hidden p-2 text-slate-500 hover:text-blue-600 transition-colors">
<span class="material-symbols-outlined">menu</span>
</button>
<div class="hidden md:flex items-center bg-surface-container-low px-4 py-2 rounded-sm gap-3 w-96">
<span class="material-symbols-outlined text-outline text-lg">search</span>
<input class="bg-transparent border-none focus:ring-0 text-sm font-body w-full" placeholder="Search system database..." type="text"/>
</div>
</div>
<div class="flex items-center gap-6">
<div class="flex items-center gap-4 text-slate-600 dark:text-slate-400 relative">
<button id="admin-btn-notifications" class="relative p-1 text-slate-500 hover:text-blue-600 transition-colors cursor-pointer">
<span class="material-symbols-outlined text-xl">notifications</span>
<span id="admin-notif-badge" class="hidden absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-red-500 text-white text-[8px] font-bold flex items-center justify-center rounded-full border-2 border-white dark:border-slate-900 px-1 leading-none shadow-sm">0</span>
</button>
<span class="material-symbols-outlined cursor-pointer hover:text-blue-600 transition-all">help_outline</span>
<span class="material-symbols-outlined cursor-pointer hover:text-blue-600 transition-all">account_circle</span>
</div>
</div>
</header>

<!-- Admin Notifications Dropdown -->
<div id="admin-dropdown-notifications" class="hidden fixed top-16 right-4 sm:right-8 lg:right-8 w-80 sm:w-96 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden z-50" style="margin-left: 16rem;">
    <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
        <div>
            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">Notifications</span>
            <span id="admin-notif-count-label" class="text-[10px] text-slate-400 ml-2"></span>
        </div>
        <span id="admin-notif-mark-read" class="text-[10px] text-blue-600 cursor-pointer hover:underline hidden">Mark all read</span>
    </div>
    <div id="admin-notif-list" class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-800">
        <div class="p-6 text-center text-slate-500 dark:text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">notifications_paused</span>
            <p class="text-xs">Loading...</p>
        </div>
    </div>
</div>
    `;
    document.write(topnavHTML);

    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById('admin-sidebar-toggle');
        const sidebar = document.querySelector('aside');
        if (toggle && sidebar) {
            toggle.onclick = (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
            };
            document.addEventListener('click', (e) => {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        }

        // ── Admin Notifications ────────────────────────────────────────────────
        const btnNotif = document.getElementById('admin-btn-notifications');
        const dropNotif = document.getElementById('admin-dropdown-notifications');
        const notifBadge = document.getElementById('admin-notif-badge');
        const notifList = document.getElementById('admin-notif-list');
        const notifMarkRead = document.getElementById('admin-notif-mark-read');
        const notifCountLabel = document.getElementById('admin-notif-count-label');

        if (!btnNotif) return;

        let lastNotifId = 0;
        var notifOpen = false;

        async function fetchNotifications() {
            try {
                const res = await fetch(rootPath + 'client/fetch_notifications.php?last_id=' + lastNotifId);
                const data = await res.json();
                if (data.status !== 'success') return;

                const unread = data.unread || 0;
                if (unread > 0) {
                    notifBadge.textContent = unread > 99 ? '99+' : unread;
                    notifBadge.classList.remove('hidden');
                } else {
                    notifBadge.classList.add('hidden');
                }

                if (data.notifications && data.notifications.length > 0) {
                    lastNotifId = data.notifications[0].id;
                    if (document.hidden && 'Notification' in window && Notification.permission === 'granted') {
                        var n = data.notifications[0];
                        new Notification(n.title, { body: n.message || '', icon: '/favicon.ico', tag: 'opencode-notif' });
                    }
                }

                if (notifOpen) renderNotifications(data);
            } catch (e) {}
        }

        function renderNotifications(data) {
            var notifs = data.notifications || [];
            var unread = data.unread || 0;
            notifCountLabel.textContent = unread > 0 ? '(' + unread + ' unread)' : '';
            notifMarkRead.classList.toggle('hidden', unread === 0);

            if (notifs.length === 0 && unread === 0) {
                notifList.innerHTML = '<div class="p-6 text-center text-slate-500"><span class="material-symbols-outlined text-4xl mb-2 opacity-50">notifications_paused</span><p class="text-xs">You\'re all caught up!</p></div>';
                return;
            }

            notifList.innerHTML = notifs.map(function(n) {
                var cls = n.is_read == 0 ? 'bg-blue-50/50 dark:bg-blue-900/10' : '';
                var icon = n.icon || 'notifications';
                return '<div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors ' + cls + '">' +
                    '<div class="flex items-start gap-3">' +
                        '<span class="material-symbols-outlined text-lg text-blue-600 shrink-0 mt-0.5">' + icon + '</span>' +
                        '<div class="min-w-0 flex-1">' +
                            '<p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">' + escHtml(n.title) + '</p>' +
                            (n.message ? '<p class="text-[10px] text-slate-500 mt-0.5 line-clamp-2">' + escHtml(n.message) + '</p>' : '') +
                            '<p class="text-[9px] text-slate-400 mt-1 uppercase tracking-widest font-bold">' + timeSince(n.created_at) + '</p>' +
                        '</div>' +
                        (n.link ? '<a href="' + n.link + '" class="shrink-0 text-blue-600 hover:text-blue-800"><span class="material-symbols-outlined text-sm">arrow_forward</span></a>' : '') +
                    '</div>' +
                '</div>';
            }).join('');
        }

        function escHtml(str) { var d = document.createElement('div'); d.textContent = str; return d.innerHTML; }

        function timeSince(ts) {
            var now = new Date();
            var d = new Date(ts.replace(' ', 'T') + 'Z');
            var diff = Math.floor((now - d) / 1000);
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return Math.floor(diff / 86400) + 'd ago';
        }

        async function markAllRead() {
            var fd = new FormData();
            fd.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');
            fd.append('type', 'all');
            await fetch(rootPath + 'client/mark_notification_read.php', { method: 'POST', body: fd });
            notifBadge.classList.add('hidden');
            fetchNotifications();
        }

        btnNotif.addEventListener('click', function(e) {
            e.stopPropagation();
            notifOpen = !dropNotif.classList.contains('hidden');
            dropNotif.classList.toggle('hidden');
            if (!notifOpen) {
                notifOpen = true;
                fetchNotifications();
            } else {
                notifOpen = false;
            }
        });

        if (notifMarkRead) notifMarkRead.addEventListener('click', markAllRead);

        if ('Notification' in window && Notification.permission === 'default') Notification.requestPermission();

        fetchNotifications();
        setInterval(fetchNotifications, 30000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) fetchNotifications();
        });

        document.addEventListener('click', function(e) {
            if (dropNotif && !dropNotif.contains(e.target) && e.target !== btnNotif && !btnNotif.contains(e.target)) {
                dropNotif.classList.add('hidden');
                notifOpen = false;
            }
        });
    });
})();
