(function () {
    const clientTopnavHTML = `
<!-- TopNavBar -->
<nav class="fixed top-0 w-full z-50 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl bg-gradient-to-b from-slate-200/20 to-transparent">
<div class="flex justify-between items-center px-4 sm:px-8 h-16 w-full max-w-[1920px] mx-auto">
<div class="flex items-center gap-1 sm:gap-2 min-w-0">
<button id="client-sidebar-toggle" class="p-1.5 sm:p-2 text-slate-500 hover:text-primary transition-all active:scale-90 shrink-0">
<span class="material-symbols-outlined text-xl sm:text-2xl">menu</span>
</button>
<span class="text-lg sm:text-xl font-bold tracking-tighter text-slate-900 dark:text-slate-100 font-headline ml-1 sm:ml-2 truncate">WILSOVLEWEL</span>
<div class="hidden md:flex items-center gap-6 font-headline tracking-tight ml-8">
<a class="nav-top-link text-slate-500 hover:text-primary transition-all font-medium text-sm" href="index.php" data-href="index.php">Terminal</a>
<a class="nav-top-link text-slate-500 hover:text-primary transition-all font-medium text-sm" href="projects.php" data-href="projects.php">Insights</a>
<a class="nav-top-link text-slate-500 hover:text-primary transition-all font-medium text-sm" href="tickets.php" data-href="tickets.php">Support</a>
</div>
</div>
<div class="flex items-center gap-2 sm:gap-4 relative shrink-0">
<div class="flex items-center gap-0 sm:gap-2">
<button id="btn-notifications" class="p-1.5 sm:p-2 text-slate-500 hover:text-primary transition-colors relative">
<span class="material-symbols-outlined text-lg sm:text-xl">notifications</span>
<span id="notif-badge" class="hidden absolute top-0.5 right-0.5 min-w-[16px] h-4 bg-red-500 text-white text-[8px] font-bold flex items-center justify-center rounded-full border border-white dark:border-slate-900 px-1 leading-none shadow-sm">0</span>
</button>
<a href="settings.php" class="p-1.5 sm:p-2 text-slate-500 hover:text-primary transition-colors hidden sm:block">
<span class="material-symbols-outlined text-lg sm:text-xl">settings</span>
</a>
</div>
<div class="w-px h-5 sm:h-6 bg-slate-200 dark:bg-slate-800 mx-1 sm:mx-2"></div>
<img id="btn-profile" alt="Client Profile" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border-2 border-primary/20 object-cover shadow-sm hover:ring-4 hover:ring-primary/10 transition-all cursor-pointer shrink-0" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvA8jiYE3XLfxn4zoHv6yGSqPmhfH6SJUNq-eww-gmysXbVVvS-kVHyB9j_fmBK7TEQqVZbftrasDbkl09jygOBEW56PWx_Pu6Z9oVxvFZP90ISPrRCxJhPiZMqkEYbUo72qibthSnqTDxCVixma9uRAy8mPcPDpzkjSig8-rw54vkqOkBY_twlToUUw4w8hc-o0fKg3xLyL3QKGp5Fd04ua9doAraSzEvB7vs82CJ9cyIoJbzsJQcKYn2Pw-cFHv-AxTQfiCn8xnq"/>

<!-- Notifications Dropdown -->
<div id="dropdown-notifications" class="hidden absolute top-14 right-14 w-80 sm:w-96 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden z-50">
    <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
        <div>
            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">Notifications</span>
            <span id="notif-count-label" class="text-[10px] text-slate-400 ml-2"></span>
        </div>
        <span id="notif-mark-read" class="text-[10px] text-primary cursor-pointer hover:underline hidden">Mark all read</span>
    </div>
    <div id="notif-list" class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-800">
        <div class="p-6 text-center text-slate-500 dark:text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">notifications_paused</span>
            <p class="text-xs">Loading...</p>
        </div>
    </div>
</div>

<!-- Profile Dropdown -->
<div id="dropdown-profile" class="hidden absolute top-14 right-0 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden z-50">
    <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
        <p class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">Client Account</p>
        <p class="text-xs text-slate-500 truncate">Manage your terminal</p>
    </div>
    <div class="p-2 flex flex-col">
        <a href="settings.php" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg flex items-center gap-3 transition-colors">
            <span class="material-symbols-outlined text-lg">person</span> Profile
        </a>
        <a href="tickets.php" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg flex items-center gap-3 transition-colors">
            <span class="material-symbols-outlined text-lg">support_agent</span> Support
        </a>
        <div class="h-px bg-slate-100 dark:bg-slate-700 my-1 mx-2"></div>
        <a href="logout.php" class="px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg flex items-center gap-3 transition-colors">
            <span class="material-symbols-outlined text-lg">logout</span> Sign Out
        </a>
    </div>
</div>

</div>
</div>
</nav>
    `;
    document.write(clientTopnavHTML);

    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById('client-sidebar-toggle');
        if (toggle) {
            toggle.onclick = (e) => {
                e.stopPropagation();
                if (window.toggleClientSidenav) window.toggleClientSidenav(true);
            };
        }

        // Dropdowns Logic
        const btnNotif = document.getElementById('btn-notifications');
        const dropNotif = document.getElementById('dropdown-notifications');
        const btnProfile = document.getElementById('btn-profile');
        const dropProfile = document.getElementById('dropdown-profile');
        const notifBadge = document.getElementById('notif-badge');
        const notifList = document.getElementById('notif-list');
        const notifMarkRead = document.getElementById('notif-mark-read');
        const notifCountLabel = document.getElementById('notif-count-label');

        function closeDropdowns() {
            if(dropNotif) dropNotif.classList.add('hidden');
            if(dropProfile) dropProfile.classList.add('hidden');
        }

        // ── Notifications ──────────────────────────────────────────────────────

        let lastNotifId = 0;
        let notifInterval = null;
        let notifOpen = false;

        function requestNotifPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                if (localStorage.getItem('notif_prompt_shown')) return;
                localStorage.setItem('notif_prompt_shown', '1');
                var overlay = document.createElement('div');
                overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;padding:16px';
                var modal = document.createElement('div');
                modal.style.cssText = 'background:#1E293B;border-radius:20px;padding:32px;max-width:400px;width:100%;text-align:center;box-shadow:0 25px 50px rgba(0,0,0,0.4);border:1px solid rgba(234,179,8,0.2)';
                modal.innerHTML = '<div style="width:56px;height:56px;background:rgba(234,179,8,0.15);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><span class="material-symbols-outlined" style="font-size:28px;color:#EAB308">notifications_active</span></div><p style="color:#fff;font-size:15px;font-weight:700;margin:0 0 8px;font-family:Manrope,sans-serif">Stay in the loop</p><p style="color:#94A3B8;font-size:13px;line-height:1.6;margin:0 0 24px;font-family:Manrope,sans-serif">Enable notifications so you never miss updates, messages, and project logs from <strong style="color:#EAB308">WilsOveWel</strong>.</p><div style="display:flex;gap:12px"><button id="notif-prompt-accept" style="flex:1;background:#EAB308;color:#0F172A;border:none;border-radius:12px;padding:12px 20px;font-size:13px;font-weight:700;cursor:pointer;font-family:Manrope,sans-serif">Enable Notifications</button><button id="notif-prompt-dismiss" style="flex:1;background:rgba(255,255,255,0.08);color:#94A3B8;border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:12px 20px;font-size:13px;font-weight:700;cursor:pointer;font-family:Manrope,sans-serif">Maybe Later</button></div>';
                overlay.appendChild(modal);
                document.body.appendChild(overlay);
                document.getElementById('notif-prompt-accept').addEventListener('click', function () { Notification.requestPermission(); overlay.remove(); });
                document.getElementById('notif-prompt-dismiss').addEventListener('click', function () { overlay.remove(); });
            }
        }

        async function fetchNotifications(fullReload) {
            try {
                var fetchId = fullReload ? 0 : lastNotifId;
                const res = await fetch('fetch_notifications.php?type=client&last_id=' + fetchId);
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
                    console.log('[Notif] New notifications:', data.notifications.length, 'Permission:', Notification.permission);
                    if ('Notification' in window && Notification.permission === 'granted') {
                        data.notifications.forEach(function(n) {
                            console.log('[Notif] Firing push for:', n.title);
                            try { new Notification(n.title, { body: n.message || '', icon: '/favicon.ico' }); } catch(e) { console.error('[Notif] Push error:', e); }
                        });
                    }
                }

                if (notifOpen) renderNotifications(data);
            } catch (e) { console.error('[Notif] Fetch error:', e); }
        }

        function renderNotifications(data) {
            const notifs = data.notifications || [];
            const unread = data.unread || 0;

            notifCountLabel.textContent = unread > 0 ? '(' + unread + ' unread)' : '';
            notifMarkRead.classList.toggle('hidden', unread === 0);

            if (notifs.length === 0 && unread === 0) {
                notifList.innerHTML = '<div class="p-6 text-center text-slate-500 dark:text-slate-400"><span class="material-symbols-outlined text-4xl mb-2 opacity-50">notifications_paused</span><p class="text-xs">You\'re all caught up!</p></div>';
                return;
            }

            notifList.innerHTML = notifs.map(function(n) {
                var timeAgo = timeSince(n.created_at);
                var cls = n.is_read == 0 ? 'bg-primary/5 dark:bg-primary/5' : '';
                var icon = n.icon || 'notifications';
                var href = n.link || '#';
                var onclick = n.link ? 'event.preventDefault(); window.clickNotif(' + n.id + ',\'' + href.replace(/'/g, "\\'") + '\')' : '';
                return '<a href="' + href + '" onclick="' + onclick + '" class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors ' + cls + '">' +
                    '<div class="flex items-start gap-3">' +
                        '<span class="material-symbols-outlined text-lg text-primary shrink-0 mt-0.5">' + icon + '</span>' +
                        '<div class="min-w-0 flex-1">' +
                            '<p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">' + escHtml(n.title) + '</p>' +
                            (n.message ? '<p class="text-[10px] text-slate-500 mt-0.5 line-clamp-2">' + escHtml(n.message) + '</p>' : '') +
                            '<p class="text-[9px] text-slate-400 mt-1 uppercase tracking-widest font-bold">' + timeAgo + '</p>' +
                        '</div>' +
                    '</div>' +
                '</a>';
            }).join('');
        }

        window.clickNotif = function(id, link) {
            var fd = new FormData();
            fd.append('id', id);
            fetch('mark_notification_read.php', { method: 'POST', body: fd }).then(function() {
                window.location.href = link;
            }).catch(function() {
                window.location.href = link;
            });
        }

        function escHtml(str) {
            var d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

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
            fd.append('csrf_token', window.CSRF_TOKEN || document.querySelector('input[name="csrf_token"]')?.value || '');
            fd.append('type', 'all');
            await fetch('mark_notification_read.php', { method: 'POST', body: fd });
            notifBadge.classList.add('hidden');
            fetchNotifications();
        }

        if (btnNotif && dropNotif) {
            btnNotif.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropNotif.classList.contains('hidden');
                closeDropdowns();
                if (isHidden) {
                    dropNotif.classList.remove('hidden');
                    notifOpen = true;
                    fetchNotifications(true);
                } else {
                    notifOpen = false;
                }
            });
        }

        if (notifMarkRead) {
            notifMarkRead.addEventListener('click', markAllRead);
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            requestNotifPermission();
        }

        // Start polling
        fetchNotifications();
        notifInterval = setInterval(fetchNotifications, 30000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) fetchNotifications();
        });

        // ── Profile Dropdown ───────────────────────────────────────────────────

        if(btnProfile && dropProfile) {
            btnProfile.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropProfile.classList.contains('hidden');
                closeDropdowns();
                if (isHidden) dropProfile.classList.remove('hidden');
            });
        }

        document.addEventListener('click', (e) => {
            if(dropNotif && !dropNotif.contains(e.target)) {
                dropNotif.classList.add('hidden');
                notifOpen = false;
            }
            if(dropProfile && !dropProfile.contains(e.target)) dropProfile.classList.add('hidden');
        });

        // Active link highlighting
        const topLinks = document.querySelectorAll('.nav-top-link');
        const currentUrl = window.location.href;
        topLinks.forEach(link => {
            if (currentUrl.includes(link.getAttribute('data-href'))) {
                link.classList.remove('text-slate-500');
                link.classList.add('text-primary', 'font-bold', 'border-b-2', 'border-primary');
            }
        });
    });
})();
