(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const clientSidenavHTML = `
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 z-[60] bg-slate-50 dark:bg-slate-950 border-r border-slate-200/50 dark:border-slate-800/50 flex flex-col p-4 gap-2 pt-20 font-['Manrope'] text-sm font-medium transition-transform duration-300 -translate-x-full lg:translate-x-0">
<div class="px-4 py-2">
<span class="font-['Space_Grotesk'] uppercase tracking-[0.05em] text-[10px] text-slate-500">Terminal v1.0</span>
<h2 class="text-lg font-bold text-on-surface">Wilsovlewel</h2>
<p class="text-[10px] text-slate-400 uppercase tracking-widest">Precision Maintenance</p>
</div>
<nav class="flex flex-col gap-1 mt-4" id="client-sidenav-links">
<a class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl hover:translate-x-1 transition-transform duration-200" href="${rootPath}client/index.html" data-href="client/index.html">
<span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </a>
<a class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl hover:translate-x-1 transition-transform duration-200" href="${rootPath}client/project.html" data-href="client/project.html">
<span class="material-symbols-outlined">engineering</span>
                Projects
            </a>
<a class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl hover:translate-x-1 transition-transform duration-200" href="${rootPath}client/propose_project.html" data-href="client/propose_project.html">
<span class="material-symbols-outlined">add_task</span>
                Propose Project
            </a>
<a class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl hover:translate-x-1 transition-transform duration-200" href="${rootPath}client/procurement.html" data-href="client/procurement.html">
<span class="material-symbols-outlined">local_shipping</span>
                Procurement
            </a>
<a class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl hover:translate-x-1 transition-transform duration-200" href="${rootPath}client/ticket.html" data-href="client/ticket.html">
<span class="material-symbols-outlined">confirmation_number</span>
                Tickets
            </a>
<a class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl hover:translate-x-1 transition-transform duration-200" href="${rootPath}client/hsse.html" data-href="client/hsse.html">
<span class="material-symbols-outlined">health_and_safety</span>
                HSSE
            </a>
<a class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl hover:translate-x-1 transition-transform duration-200" href="${rootPath}client/settings.html" data-href="client/settings.html">
<span class="material-symbols-outlined">settings</span>
                Settings
            </a>
</nav>
<div class="mt-auto flex flex-col gap-1">
<a href="${rootPath}index.html" class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-error text-white rounded-xl font-bold text-xs uppercase tracking-tighter hover:bg-red-700 transition-colors active:scale-[0.98]">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">logout</span>
                Logout
            </a>
<div class="mt-4 pt-4 border-t border-slate-200/50">
<a class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-slate-600 transition-colors" href="#">
<span class="material-symbols-outlined text-base">description</span>
                    Documentation
                </a>
<a class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-slate-600 transition-colors" href="#">
<span class="material-symbols-outlined text-base">help_center</span>
                    Support
                </a>
</div>
</div>
</aside>
    `;

    document.write(clientSidenavHTML);

    document.addEventListener("DOMContentLoaded", function () {
        const links = document.querySelectorAll('#client-sidenav-links .nav-link');
        const currentUrl = window.location.href;

        links.forEach(link => {
            const dataHref = link.getAttribute('data-href');
            if (dataHref && currentUrl.includes(dataHref)) {
                // Remove default non-active classes
                link.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-100', 'dark:hover:bg-slate-800');
                // Add active classes
                link.classList.add('bg-white', 'dark:bg-slate-900', 'text-blue-700', 'dark:text-blue-400', 'shadow-sm');
            }
        });
    });
})();
