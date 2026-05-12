(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    // Permission check helper
    const hasAccess = (moduleName) => {
        if (!window.WILSOLVEWEL_PERMISSIONS) return true; // Full access for super admins
        const perms = window.WILSOLVEWEL_PERMISSIONS[moduleName];
        if (!perms) return false;
        return perms.read || perms.write;
    };

    const sidenavHTML = `
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-white border-r border-slate-100 flex flex-col py-6 px-4 z-[100] transition-transform duration-300 -translate-x-full lg:translate-x-0">
    <style>
        .sidenav-scrollbar::-webkit-scrollbar { width: 3px; }
        .sidenav-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidenav-scrollbar::-webkit-scrollbar-thumb { background: #F1F5F9; border-radius: 10px; }
        .sidenav-scrollbar:hover::-webkit-scrollbar-thumb { background: #E2E8F0; }
    </style>
    
    <div class="mb-8 px-2 shrink-0">
        <a href="${rootPath}admin/index.php" class="block group">
            <h1 class="text-xl font-bold font-headline tracking-tighter text-slate-900 group-hover:text-primary transition-colors">Wilsolvewel</h1>
            <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-primary mt-1">Terminal v1.0.4</p>
        </a>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto sidenav-scrollbar pr-1" id="admin-sidenav-links">
        ${hasAccess('Dashboard') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/index.php" data-href="admin/index.php">
            <span class="material-symbols-outlined text-xl">dashboard</span> 
            <span class="text-xs">Dashboard</span>
        </a>` : ''}
        
        ${hasAccess('Projects') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/project/index.html" data-href="admin/project">
            <span class="material-symbols-outlined text-xl">folder_special</span> 
            <span class="text-xs">Projects</span>
        </a>` : ''}

        ${hasAccess('Assets') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/asset/index.html" data-href="admin/asset">
            <span class="material-symbols-outlined text-xl">inventory_2</span> 
            <span class="text-xs">Assets</span>
        </a>` : ''}

        ${hasAccess('Procurement') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/procurement/order.html" data-href="admin/procurement">
            <span class="material-symbols-outlined text-xl">shopping_cart</span> 
            <span class="text-xs">Procurement</span>
        </a>` : ''}

        ${hasAccess('Inquiries') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/inquiries.php" data-href="admin/inquiries.php">
            <span class="material-symbols-outlined text-xl">forum</span> 
            <span class="text-xs">Inquiries</span>
        </a>` : ''}

        ${hasAccess('HSSE') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/hsse/monitor.html" data-href="admin/hsse">
            <span class="material-symbols-outlined text-xl">health_and_safety</span> 
            <span class="text-xs">HSSE</span>
        </a>` : ''}

        ${hasAccess('Departments') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/departments.php" data-href="admin/departments.php">
            <span class="material-symbols-outlined text-xl">corporate_fare</span> 
            <span class="text-xs">Departments</span>
        </a>` : ''}

        ${hasAccess('Privileges') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/privileges.php" data-href="admin/privileges.php">
            <span class="material-symbols-outlined text-xl">admin_panel_settings</span> 
            <span class="text-xs">Privileges</span>
        </a>` : ''}

        ${hasAccess('Settings') ? `
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/settings.php" data-href="admin/settings.php">
            <span class="material-symbols-outlined text-xl">settings</span> 
            <span class="text-xs">Settings</span>
        </a>` : ''}
        
        <a class="nav-link flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all rounded-xl font-headline font-medium" href="${rootPath}admin/documentation.php" data-href="admin/documentation.php">
            <span class="material-symbols-outlined text-xl">menu_book</span> 
            <span class="text-xs">Documentation</span>
        </a>
    </nav>

    <div class="mt-4 px-2 flex items-center gap-3 pt-4 border-t border-slate-50 shrink-0">
        <img alt="User profile" class="w-10 h-10 rounded-xl object-cover shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBlbzr9Im1MftxEP8s0zGaTLmPwd94SgOCIb85vP9kseD6ilwirY_O9quGTp_x8iVFHNPShp_BVjwmJkQENcmUKp54IYNcQy1JdDMqmxyxqRkmLwdofi3YNK0RwNTcROa1wpy66hGNPm_8WxYrpeQD3cnUB120jekXBqKuP_LXz4nhNPVrRwY3ygyV8-DeWAn2YikQCw-gBoRyTzfMc0T_yS4TwomKFykAzglmTjng2tJdV2rHMoIEzKfmh1Vs2S-2e95bAP0sETXE2"/>
        <div class="min-w-0">
            <p class="text-xs font-bold text-slate-900 truncate">M. Sterling</p>
            <p class="text-[9px] font-bold text-slate-400 uppercase truncate tracking-wider">Director</p>
        </div>
        <a href="${rootPath}index.html" class="ml-auto w-8 h-8 flex items-center justify-center text-slate-300 hover:text-red-500 transition-colors" title="Exit Terminal">
            <span class="material-symbols-outlined text-xl">logout</span>
        </a>
    </div>
</aside>
    `;
    document.write(sidenavHTML);

    document.addEventListener("DOMContentLoaded", function () {
        const links = document.querySelectorAll('#admin-sidenav-links .nav-link');
        const currentUrl = window.location.href;

        links.forEach(link => {
            const dataHref = link.getAttribute('data-href');
            if (dataHref && currentUrl.includes(dataHref)) {
                link.classList.remove('text-slate-500', 'hover:bg-slate-50');
                link.classList.add('bg-primary', 'text-on-primary', 'font-bold');
            }
        });
    });
})();
