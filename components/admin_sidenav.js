(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const sidenavHTML = `
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 border-r-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-6 px-4 z-[60] transition-transform duration-300 -translate-x-full lg:translate-x-0">
<div class="mb-10 px-2">
<a href="${rootPath}admin/index.html" class="block hover:opacity-80 transition-opacity">
<h1 class="text-xl font-bold font-['Space_Grotesk'] tracking-tight text-slate-900 dark:text-slate-100">Wilsolvewel</h1>
<p class="text-[10px] font-label uppercase tracking-[0.1em] text-slate-500">Terminal v1.0.4</p>
</a>
</div>
<nav class="flex-1 space-y-1" id="admin-sidenav-links">
<a class="nav-link flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}admin/index.html" data-href="admin/index.html">
<span class="material-symbols-outlined">dashboard</span> Dashboard
</a>
<a class="nav-link flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}admin/project/index.html" data-href="admin/project">
<span class="material-symbols-outlined">folder_special</span> Projects
</a>
<a class="nav-link flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}admin/asset/index.html" data-href="admin/asset">
<span class="material-symbols-outlined">inventory_2</span> Assets
</a>
<a class="nav-link flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}admin/procurement/order.html" data-href="admin/procurement">
<span class="material-symbols-outlined">shopping_cart</span> Procurement
</a>
<a class="nav-link flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}admin/inquiries.html" data-href="admin/inquiries">
<span class="material-symbols-outlined">forum</span> Inquiries
</a>
<a class="nav-link flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}admin/hsse/monitor.html" data-href="admin/hsse">
<span class="material-symbols-outlined">health_and_safety</span> HSSE
</a>
<a class="nav-link flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}admin/settings.html" data-href="admin/settings.html">
<span class="material-symbols-outlined">settings</span> Settings
</a>
<a class="nav-link mt-4 flex items-center gap-3 px-3 py-2 text-slate-500 dark:text-slate-400 hover:text-red-500 transition-colors rounded-lg font-['Space_Grotesk'] tracking-tight" href="${rootPath}index.html">
<span class="material-symbols-outlined">logout</span> Exit Terminal
</a>
</nav>
<div class="mt-auto px-2 flex items-center gap-3">
<img alt="User profile" class="w-10 h-10 rounded-full object-cover" data-alt="professional portrait of a chief safety officer in industrial environment with soft office lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBlbzr9Im1MftxEP8s0zGaTLmPwd94SgOCIb85vP9kseD6ilwirY_O9quGTp_x8iVFHNPShp_BVjwmJkQENcmUKp54IYNcQy1JdDMqmxyxqRkmLwdofi3YNK0RwNTcROa1wpy66hGNPm_8WxYrpeQD3cnUB120jekXBqKuP_LXz4nhNPVrRwY3ygyV8-DeWAn2YikQCw-gBoRyTzfMc0T_yS4TwomKFykAzglmTjng2tJdV2rHMoIEzKfmh1Vs2S-2e95bAP0sETXE2"/>
<div>
<p class="text-sm font-bold text-slate-900">M. Sterling</p>
<p class="text-xs text-slate-500">Director of Safety</p>
</div>
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
                // Remove default classes
                link.classList.remove('text-slate-500', 'hover:bg-slate-200/50', 'dark:text-slate-400', 'dark:hover:bg-slate-800');
                // Add active classes
                link.classList.add('text-blue-700', 'dark:text-blue-400', 'font-bold', 'border-r-2', 'border-blue-700', 'dark:border-blue-400', 'bg-slate-100', 'dark:bg-slate-800/50', 'rounded-l-lg');
                link.classList.remove('rounded-lg');
            }
        });
    });
})();
