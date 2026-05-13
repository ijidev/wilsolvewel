(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const clientSidenavHTML = `
<!-- Sidenav Overlay -->
<div id="sidenav-overlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-[55] opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Floating SideNavBar -->
<aside id="client-sidenav" class="fixed left-0 top-0 bottom-0 z-[60] w-72 bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-slate-200/50 dark:border-slate-800/50 flex flex-col p-6 gap-2 font-['Manrope'] text-sm font-medium transition-all duration-500 -translate-x-[120%] shadow-2xl rounded-[2rem] m-6 lg:m-8 overflow-hidden group">
    <!-- Close Button -->
    <button id="close-sidenav" class="absolute top-6 right-6 p-2 text-slate-400 hover:text-on-surface transition-colors lg:hidden">
        <span class="material-symbols-outlined">close</span>
    </button>

    <div class="px-2 py-4 mb-4">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-on-primary">
                <span class="material-symbols-outlined text-sm">precision_manufacturing</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-on-surface font-headline leading-none">Wilsovlewel</h2>
                <p class="text-[9px] text-slate-400 uppercase tracking-[0.2em] mt-1">Terminal v1.0</p>
            </div>
        </div>
    </div>

    <nav class="flex flex-col gap-1.5 overflow-y-auto pr-2 custom-scrollbar" id="client-sidenav-links">
        <a class="nav-link flex items-center gap-4 px-5 py-3.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-2xl transition-all duration-300 group/link" href="${rootPath}client/index.php" data-href="client/index.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform">dashboard</span>
            <span class="font-headline tracking-tight">Dashboard</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-2xl transition-all duration-300 group/link" href="${rootPath}client/projects.php" data-href="client/projects.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform">engineering</span>
            <span class="font-headline tracking-tight">Projects</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-2xl transition-all duration-300 group/link" href="${rootPath}client/propose_project.php" data-href="client/propose_project.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform">add_task</span>
            <span class="font-headline tracking-tight">Propose Project</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-2xl transition-all duration-300 group/link" href="${rootPath}client/procurement.php" data-href="client/procurement.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform">local_shipping</span>
            <span class="font-headline tracking-tight">Procurement</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-2xl transition-all duration-300 group/link" href="${rootPath}client/tickets.php" data-href="client/tickets.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform">confirmation_number</span>
            <span class="font-headline tracking-tight">Tickets</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-2xl transition-all duration-300 group/link" href="${rootPath}client/hsse.php" data-href="client/hsse.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform">health_and_safety</span>
            <span class="font-headline tracking-tight">HSSE</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-2xl transition-all duration-300 group/link" href="${rootPath}client/settings.php" data-href="client/settings.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform">settings</span>
            <span class="font-headline tracking-tight">Settings</span>
        </a>
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-200/50 flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <a class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-primary transition-colors group/mini" href="${rootPath}documentation.php">
                <span class="material-symbols-outlined text-lg">description</span>
                <span class="text-xs font-bold uppercase tracking-widest">Documentation</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-primary transition-colors group/mini" href="${rootPath}client/tickets.php">
                <span class="material-symbols-outlined text-lg">help_center</span>
                <span class="text-xs font-bold uppercase tracking-widest">Help Center</span>
            </a>
        </div>
        <a href="${rootPath}client/logout.php" class="flex items-center justify-center gap-3 w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-[10px] uppercase tracking-[0.15em] hover:bg-slate-800 transition-all active:scale-[0.98] shadow-lg shadow-slate-200/20">
            <span class="material-symbols-outlined text-sm">logout</span>
            Logout
        </a>
    </div>
</aside>

<style>
    #client-sidenav.active {
        transform: translateX(0);
    }
    #sidenav-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
</style>
    `;

    document.write(clientSidenavHTML);

    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById('client-sidenav');
        const overlay = document.getElementById('sidenav-overlay');
        const closeBtn = document.getElementById('close-sidenav');
        const links = document.querySelectorAll('#client-sidenav-links .nav-link');
        const currentUrl = window.location.href;

        function toggleSidenav(show) {
            if (show) {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Expose toggle function globally for topnav
        window.toggleClientSidenav = toggleSidenav;

        overlay.onclick = () => toggleSidenav(false);
        if (closeBtn) closeBtn.onclick = () => toggleSidenav(false);

        links.forEach(link => {
            const dataHref = link.getAttribute('data-href');
            if (dataHref && currentUrl.includes(dataHref)) {
                link.classList.remove('text-slate-500', 'dark:text-slate-400');
                link.classList.add('bg-white', 'dark:bg-slate-950', 'text-primary', 'shadow-xl', 'shadow-primary/10', 'ring-1', 'ring-primary/20');
            }
        });
    });
})();

