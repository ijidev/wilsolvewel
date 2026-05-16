const SIDEBAR_HTML = `
<!-- Mobile overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 h-screen w-64 bg-white border-r border-slate-100 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
    <!-- Header/Logo -->
    <div class="h-20 flex items-center px-8 border-b border-slate-50 shrink-0">
        <div>
            <h1 class="text-xl font-bold font-headline tracking-tight text-slate-900">Wilsolvewel</h1>
            <p class="text-[8px] font-bold tracking-[0.2em] text-primary uppercase mt-0.5">Terminal</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8 custom-scrollbar">
        <!-- Overview -->
        <div>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Overview</p>
            <div class="space-y-1">
                <a href="${window.ADMIN_ROOT || ''}index.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span> Dashboard
                </a>
                <a href="${window.ADMIN_ROOT || ''}audit_monitor.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff">
                    <span class="material-symbols-outlined text-[20px]">history_toggle_off</span> Audit Monitor
                </a>
            </div>
        </div>

        <!-- Communications -->
        <div>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Communications</p>
            <div class="space-y-1">
                <a href="${window.ADMIN_ROOT || ''}inquiries.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="view_inquiries">
                    <span class="material-symbols-outlined text-[20px]">inbox</span> Inquiries
                </a>
                <a href="${window.ADMIN_ROOT || ''}tickets.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="view_inquiries">
                    <span class="material-symbols-outlined text-[20px]">forum</span> Support Tickets
                </a>
            </div>
        </div>

        <!-- Operations -->
        <div>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Operations</p>
            <div class="space-y-1">
                <a href="${window.ADMIN_ROOT || ''}clients.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_clients">
                    <span class="material-symbols-outlined text-[20px]">person</span> CRM &amp; Clients
                </a>
                <a href="${window.ADMIN_ROOT || ''}projects.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_projects">
                    <span class="material-symbols-outlined text-[20px]">folder_special</span> Projects
                </a>
                <a href="${window.ADMIN_ROOT || ''}assets.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_projects">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span> Asset Register
                </a>
                <a href="${window.ADMIN_ROOT || ''}hsse.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="HSSE">
                    <span class="material-symbols-outlined text-[20px]">health_and_safety</span> HSSE Monitor
                </a>
                <a href="${window.ADMIN_ROOT || ''}procurement.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_procurement">
                    <span class="material-symbols-outlined text-[20px]">local_shipping</span> Procurement
                </a>
            </div>
        </div>

        <!-- Administration -->
        <div>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Administration</p>
            <div class="space-y-1">
                <a href="${window.ADMIN_ROOT || ''}staff.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff">
                    <span class="material-symbols-outlined text-[20px]">badge</span> Staff
                </a>
                <a href="${window.ADMIN_ROOT || ''}departments.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff">
                    <span class="material-symbols-outlined text-[20px]">domain</span> Departments
                </a>
                <a href="${window.ADMIN_ROOT || ''}privileges.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_staff">
                    <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span> Access Control
                </a>
            </div>
        </div>

        <!-- System -->
        <div>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">System</p>
            <div class="space-y-1">
                <a href="${window.ADMIN_ROOT || ''}settings.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all" data-perm="manage_settings">
                    <span class="material-symbols-outlined text-[20px]">settings</span> Settings
                </a>
                <a href="${window.ADMIN_ROOT || ''}documentation.php" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">
                    <span class="material-symbols-outlined text-[20px]">help</span> Help Guide
                </a>
            </div>
        </div>
    </nav>

    <!-- Bottom User Area -->
    <div class="p-4 border-t border-slate-50 shrink-0">
        <a href="${window.ADMIN_ROOT || ''}profile.php" class="nav-link flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 transition-colors cursor-pointer group">
            <div id="sidebar-avatar-container" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:bg-primary group-hover:text-on-primary transition-colors overflow-hidden">
                <span class="material-symbols-outlined text-lg">person</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-900 truncate">My Profile</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate">Manage Account</p>
            </div>
        </a>
        <a href="${window.ADMIN_ROOT || ''}logout.php" class="mt-2 flex items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 text-xs font-bold text-slate-500 hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition-all uppercase tracking-widest">
            <span class="material-symbols-outlined text-[16px]">logout</span> Log Out
        </a>
    </div>
</aside>

<!-- Mobile Menu Toggle Button -->
<button onclick="toggleSidebar()" class="fixed bottom-6 left-6 w-12 h-12 bg-slate-900 text-white rounded-2xl shadow-xl flex items-center justify-center lg:hidden z-30 hover:bg-primary hover:text-on-primary transition-colors">
    <span class="material-symbols-outlined">grid_view</span>
</button>
`;

// Determine root path based on script data attribute
const scriptTag = document.currentScript;
if (scriptTag && scriptTag.hasAttribute('data-root')) {
    window.ADMIN_ROOT = scriptTag.getAttribute('data-root');
} else {
    window.ADMIN_ROOT = '';
}

// Inject HTML
if (document.readyState === 'loading') {
    document.write(SIDEBAR_HTML);
} else {
    document.body.insertAdjacentHTML('afterbegin', SIDEBAR_HTML);
}

// Highlight Active Link & Apply Permissions
document.addEventListener("DOMContentLoaded", () => {
    // 1. Highlight Active link
    const currentPath = window.location.pathname;
    const links = document.querySelectorAll('.nav-link');

    links.forEach(link => {
        try {
            const linkPath = new URL(link.href).pathname;
            if (currentPath === linkPath || (currentPath.endsWith('/') && linkPath.endsWith('index.php'))) {
                link.classList.add('bg-primary/10', 'text-slate-900');
                link.classList.remove('text-slate-600', 'hover:bg-slate-50');
                const indicator = document.createElement('div');
                indicator.className = 'absolute left-0 w-1 h-8 bg-primary rounded-r-full';
                link.classList.add('relative');
                link.appendChild(indicator);
            }
        } catch(e) {}
    });

    // 2. Set Avatar if exists
    if (window.WILSOLVEWEL_AVATAR) {
        const avatarContainer = document.getElementById('sidebar-avatar-container');
        if (avatarContainer) {
            avatarContainer.innerHTML = `<img src="${window.ADMIN_ROOT || ''}${window.WILSOLVEWEL_AVATAR}" class="w-full h-full object-cover" alt="Profile" />`;
        }
    }

    // 3. Apply RBAC Permissions
    const perms = window.WILSOLVEWEL_PERMISSIONS;

    // If no permissions object at all, show everything (superadmin / unassigned user fallback)
    if (!perms) return;

    // Check if the admin has no permission keys set (null return from get_admin_permissions)
    // In that case also show everything — they are likely a root/director admin
    const permKeys = Object.keys(perms).filter(k => k !== 'role');
    if (permKeys.length === 0) return;

    document.querySelectorAll('[data-perm]').forEach(el => {
        const requiredPerm = el.getAttribute('data-perm');

        // Director role always sees everything
        if (perms.role === 'Director') {
            el.style.display = 'flex';
            return;
        }

        // RBAC check
        const perm = perms[requiredPerm];
        const hasAccess = perm && (perm.read === true || perm.write === true);

        el.style.display = hasAccess ? 'flex' : 'none';
    });
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');

    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}
