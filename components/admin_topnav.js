(function () {
    var rootPath = './';
    var script = document.currentScript;
    if (script && script.getAttribute('data-root')) rootPath = script.getAttribute('data-root');

    document.addEventListener("DOMContentLoaded", function () {
        // ── Sidebar Toggle (header + footer) ─────────────────────────────────
        var sidebar = document.querySelector('aside');
        var headerToggle = document.getElementById('admin-sidebar-toggle');
        var footerToggle = document.getElementById('footer-sidebar-toggle');

        function toggleSidebar(e) {
            e.stopPropagation();
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
        }

        if (headerToggle && sidebar) headerToggle.onclick = toggleSidebar;
        if (footerToggle && sidebar) footerToggle.onclick = toggleSidebar;

        document.addEventListener('click', function (e) {
            if (sidebar && !sidebar.contains(e.target) &&
                !headerToggle?.contains(e.target) &&
                !footerToggle?.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // ── Admin Notifications ────────────────────────────────────────────────
        var btnNotif = document.getElementById('admin-btn-notifications');
        var footerNotifBtn = document.getElementById('footer-notif-btn');
        var dropNotif = document.getElementById('admin-dropdown-notifications');
        var notifBadge = document.getElementById('admin-notif-badge');
        var footerNotifBadge = document.getElementById('footer-notif-badge');
        var notifList = document.getElementById('admin-notif-list');
        var notifMarkRead = document.getElementById('admin-notif-mark-read');
        var notifCountLabel = document.getElementById('admin-notif-count-label');

        if (!btnNotif || !dropNotif) return;

        let lastNotifId = 0;
        var notifOpen = false;

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
                const res = await fetch(rootPath + 'client/fetch_notifications.php?type=admin&last_id=' + fetchId);
                const data = await res.json();
                if (data.status !== 'success') return;

                const unread = data.unread || 0;
                if (unread > 0) {
                    notifBadge.textContent = unread > 99 ? '99+' : unread;
                    notifBadge.classList.remove('hidden');
                    if (footerNotifBadge) { footerNotifBadge.textContent = unread > 99 ? '99+' : unread; footerNotifBadge.classList.remove('hidden'); }
                } else {
                    notifBadge.classList.add('hidden');
                    if (footerNotifBadge) footerNotifBadge.classList.add('hidden');
                }

                if (data.notifications && data.notifications.length > 0) {
                    lastNotifId = data.notifications[0].id;
                    if ('Notification' in window && Notification.permission === 'granted') {
                        data.notifications.forEach(function(n) {
                            try { new Notification(n.title, { body: n.message || '', icon: '/favicon.ico' }); } catch(e) {}
                        });
                    }
                }

                if (notifOpen) renderNotifications(data);
            } catch (e) { console.error('[Notif] Fetch error:', e); }
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
                var cls = n.is_read == 0 ? 'bg-blue-50/50' : '';
                var icon = n.icon || 'notifications';
                var href = n.link || '#';
                var onclick = n.link ? 'event.preventDefault(); window.clickNotif(' + n.id + ',\'' + href.replace(/'/g, "\\'") + '\')' : '';
                return '<a href="' + href + '" onclick="' + onclick + '" class="block p-4 hover:bg-slate-50 transition-colors ' + cls + '">' +
                    '<div class="flex items-start gap-3">' +
                        '<span class="material-symbols-outlined text-lg text-blue-600 shrink-0 mt-0.5">' + icon + '</span>' +
                        '<div class="min-w-0 flex-1">' +
                            '<p class="text-xs font-bold text-slate-900 truncate">' + escHtml(n.title) + '</p>' +
                            (n.message ? '<p class="text-[10px] text-slate-500 mt-0.5 line-clamp-2">' + escHtml(n.message) + '</p>' : '') +
                            '<p class="text-[9px] text-slate-400 mt-1 uppercase tracking-widest font-bold">' + timeSince(n.created_at) + '</p>' +
                        '</div>' +
                    '</div>' +
                '</a>';
            }).join('');
        }

        window.clickNotif = function(id, link) {
            var fd = new FormData();
            fd.append('id', id);
            fetch(rootPath + 'client/mark_notification_read.php', { method: 'POST', body: fd }).then(function() {
                window.location.href = link;
            }).catch(function() {
                window.location.href = link;
            });
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
            fd.append('csrf_token', window.CSRF_TOKEN || document.querySelector('input[name="csrf_token"]')?.value || '');
            fd.append('type', 'all');
            await fetch(rootPath + 'client/mark_notification_read.php', { method: 'POST', body: fd });
            notifBadge.classList.add('hidden');
            fetchNotifications();
        }

        function toggleNotifications(e) {
            e.stopPropagation();
            notifOpen = dropNotif.classList.contains('hidden');
            dropNotif.classList.toggle('hidden');
            if (notifOpen) fetchNotifications(true);
        }

        if (btnNotif) btnNotif.addEventListener('click', toggleNotifications);
        if (footerNotifBtn) footerNotifBtn.addEventListener('click', toggleNotifications);

        if (notifMarkRead) notifMarkRead.addEventListener('click', markAllRead);

        if ('Notification' in window && Notification.permission === 'default') requestNotifPermission();

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