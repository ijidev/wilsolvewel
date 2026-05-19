function buildSidebarHTML(root) {
    root = root || '';
    return [
        '<!-- Mobile overlay -->',
        '<div id="mobile-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>',
        '<!-- Sidebar -->',
        '<aside id="sidebar" class="fixed top-0 left-0 h-screen w-64 bg-white border-r border-slate-100 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">',
        '  <div class="h-20 flex items-center px-8 border-b border-slate-50 shrink-0">',
        '    <div>',
        '      <h1 class="text-xl font-bold font-headline tracking-tight text-slate-900">Wilsolvewel</h1>',
        '      <p class="text-[8px] font-bold tracking-[0.2em] text-primary uppercase mt-0.5">Terminal</p>',
        '    </div>',
        '  </div>',
        '  <nav id="sidebar-nav" class="flex-1 overflow-y-auto py-6 px-4 space-y-8 custom-scrollbar">',

        '    <div data-section="overview">',
        '      <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Overview</p>',
        '      <div class="space-y-1">',
        '        <a href="' + root + 'admin/index.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all"><span class="material-symbols-outlined text-[20px]">dashboard</span> Dashboard</a>',
        '        <a href="' + root + 'admin/audit_monitor.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff"><span class="material-symbols-outlined text-[20px]">history_toggle_off</span> Audit Monitor</a>',
        '      </div>',
        '    </div>',

        '    <div data-section="communications">',
        '      <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Communications</p>',
        '      <div class="space-y-1">',
        '        <a href="' + root + 'admin/inquiries.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="view_inquiries"><span class="material-symbols-outlined text-[20px]">inbox</span> Inquiries</a>',
        '        <a href="' + root + 'admin/tickets.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="view_inquiries"><span class="material-symbols-outlined text-[20px]">forum</span> Support Tickets</a>',
        '        <a href="' + root + 'admin/faqs.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all"><span class="material-symbols-outlined text-[20px]">quiz</span> FAQ Manager</a>',
        '      </div>',
        '    </div>',

        '    <div data-section="operations">',
        '      <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Operations</p>',
        '      <div class="space-y-1">',
        '        <a href="' + root + 'admin/clients.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_clients"><span class="material-symbols-outlined text-[20px]">person</span> CRM &amp; Clients</a>',
        '        <a href="' + root + 'admin/projects.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_projects"><span class="material-symbols-outlined text-[20px]">folder_special</span> Projects</a>',
        '        <a href="' + root + 'admin/assets.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_projects"><span class="material-symbols-outlined text-[20px]">inventory_2</span> Asset Register</a>',
        '        <a href="' + root + 'admin/hsse.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="HSSE"><span class="material-symbols-outlined text-[20px]">health_and_safety</span> HSSE Monitor</a>',
        '        <a href="' + root + 'admin/procurement/index.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_procurement"><span class="material-symbols-outlined text-[20px]">local_shipping</span> Procurement</a>',
        '        <a href="' + root + 'admin/showcase_projects.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all"><span class="material-symbols-outlined text-[20px]">business_center</span> Showcase Projects</a>',
        '      </div>',
        '    </div>',

        '    <div data-section="administration">',
        '      <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Administration</p>',
        '      <div class="space-y-1">',
        '        <a href="' + root + 'admin/staff.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff"><span class="material-symbols-outlined text-[20px]">badge</span> Staff</a>',
        '        <a href="' + root + 'admin/departments.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff"><span class="material-symbols-outlined text-[20px]">domain</span> Departments</a>',
        '        <a href="' + root + 'admin/privileges.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff"><span class="material-symbols-outlined text-[20px]">admin_panel_settings</span> Access Control</a>',
        '      </div>',
        '    </div>',

        '    <div data-section="system">',
        '      <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">System</p>',
        '      <div class="space-y-1">',
        '        <a href="' + root + 'admin/settings.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_settings"><span class="material-symbols-outlined text-[20px]">settings</span> Settings</a>',
        '        <a href="' + root + 'admin/documentation.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all"><span class="material-symbols-outlined text-[20px]">help</span> Help Guide</a>',
        '      </div>',
        '    </div>',

        '  </nav>',
        '  <div class="p-4 border-t border-slate-50 shrink-0">',
        '    <a href="' + root + 'admin/profile.php" class="nav-link flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 transition-colors cursor-pointer group">',
        '      <div id="sidebar-avatar-container" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:bg-primary group-hover:text-on-primary transition-colors overflow-hidden">',
        '        <span class="material-symbols-outlined text-lg">person</span>',
        '      </div>',
        '      <div class="flex-1 min-w-0">',
        '        <p class="text-sm font-bold text-slate-900 truncate">My Profile</p>',
        '        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate">Manage Account</p>',
        '      </div>',
        '    </a>',
        '    <a href="' + root + 'admin/logout.php" class="mt-2 flex items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 text-xs font-bold text-slate-500 hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition-all uppercase tracking-widest"><span class="material-symbols-outlined text-[16px]">logout</span> Log Out</a>',
        '  </div>',
        '</aside>',
    ].join('\n');
}

// Determine root path from script data-root attribute
var scriptTag = document.currentScript;
var ADMIN_ROOT = (scriptTag && scriptTag.getAttribute('data-root')) ? scriptTag.getAttribute('data-root') : '';
window.ADMIN_ROOT = ADMIN_ROOT;

// Inject sidebar HTML
var sidebarHTML = buildSidebarHTML(ADMIN_ROOT);
if (document.readyState === 'loading') {
    document.write(sidebarHTML);
} else {
    document.body.insertAdjacentHTML('afterbegin', sidebarHTML);
}

document.addEventListener('DOMContentLoaded', function () {

    // 1. Highlight active link
    var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
    var links = document.querySelectorAll('.nav-link');
    links.forEach(function (link) {
        try {
            var linkPath = new URL(link.href).pathname.replace(/\/+$/, '') || '/';
            if (currentPath === linkPath || (currentPath === '/dashboard' && linkPath === '/dashboard')) {
                link.classList.add('bg-primary/10', 'text-slate-900');
                link.classList.remove('text-slate-600', 'hover:bg-slate-50');
                var indicator = document.createElement('div');
                indicator.className = 'absolute left-0 w-1 h-8 bg-primary rounded-r-full';
                link.classList.add('relative');
                link.appendChild(indicator);
            }
        } catch (e) {}
    });

    // 2. Set avatar
    if (window.WILSOLVEWEL_AVATAR) {
        var avatarContainer = document.getElementById('sidebar-avatar-container');
        if (avatarContainer) {
            avatarContainer.innerHTML = '<img src="' + (window.ADMIN_ROOT || '') + window.WILSOLVEWEL_AVATAR + '" class="w-full h-full object-cover" alt="Profile" />';
        }
    }

    // 3. Apply RBAC Permissions
    var perms = window.WILSOLVEWEL_PERMISSIONS;

    // No permissions object = root/unassigned admin, show everything
    if (!perms) return;

    // Director role = show everything
    if (perms.role === 'Director') return;

    // No specific permission keys beyond role = show everything
    var permKeys = Object.keys(perms).filter(function(k){ return k !== 'role'; });
    if (permKeys.length === 0) return;

    // Apply per-link RBAC
    document.querySelectorAll('[data-perm]').forEach(function (el) {
        var requiredPerm = el.getAttribute('data-perm');
        var perm = perms[requiredPerm];
        var hasAccess = perm && (perm.read === true || perm.write === true);
        el.style.display = hasAccess ? 'flex' : 'none';
    });

    // Hide empty section group titles
    document.querySelectorAll('#sidebar-nav > div[data-section]').forEach(function (section) {
        var permLinks = section.querySelectorAll('[data-perm]');
        if (permLinks.length === 0) return;
        var allHidden = Array.from(permLinks).every(function (el) { return el.style.display === 'none'; });
        if (allHidden) section.style.display = 'none';
    });
});

function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('mobile-overlay');
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}
