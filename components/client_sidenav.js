(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const clientSidenavHTML = `
<!-- Sidenav Overlay -->
<div id="sidenav-overlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-[1px] z-[55] opacity-0 pointer-events-none transition-all duration-500"></div>

<!-- Floating SideNavBar -->
<aside id="client-sidenav" class="fixed left-[50px] top-[50px] bottom-[50px] z-[60] w-96 bg-white/95 dark:bg-slate-900/98 backdrop-blur-md border border-white/20 dark:border-slate-800/50 flex flex-col p-6 gap-2 font-['Manrope'] text-sm font-medium transition-all cubic-bezier(0.4, 0, 0.2, 1) duration-700 -translate-x-[calc(100%+100px)] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] rounded-[3rem] overflow-hidden group ring-1 ring-black/5 dark:ring-white/10">
    <!-- Close Button -->
    <button id="close-sidenav" class="absolute top-8 right-8 p-2 text-slate-400 hover:text-primary transition-all active:scale-90 z-30">
        <span class="material-symbols-outlined text-2xl">close</span>
    </button>

    <div class="px-2 py-4 mb-2 shrink-0 relative z-20">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary to-amber-600 flex items-center justify-center text-on-primary shadow-xl shadow-primary/30 ring-2 ring-white/50">
                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">precision_manufacturing</span>
            </div>
            <div>
                <h2 class="text-lg font-black text-on-surface font-headline leading-none tracking-tighter italic uppercase">Wilsovlewel</h2>
                <p class="text-[9px] text-primary font-bold uppercase tracking-[0.3em] mt-1 opacity-80">Terminal v1.0</p>
            </div>
        </div>
    </div>

    <nav class="flex flex-col gap-1 overflow-y-auto pr-2 custom-scrollbar flex-1 relative z-20" id="client-sidenav-links">
        <a class="nav-link flex items-center gap-4 px-5 py-3 text-slate-900 dark:text-slate-100 hover:bg-primary/10 rounded-[1.2rem] transition-all duration-300 group/link" href="${rootPath}client/index.php" data-href="client/index.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform text-slate-400">dashboard</span>
            <span class="font-headline font-bold text-sm tracking-tight">Dashboard</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3 text-slate-900 dark:text-slate-100 hover:bg-primary/10 rounded-[1.2rem] transition-all duration-300 group/link" href="${rootPath}client/projects.php" data-href="client/projects.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform text-slate-400">engineering</span>
            <span class="font-headline font-bold text-sm tracking-tight">Projects</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3 text-slate-900 dark:text-slate-100 hover:bg-primary/10 rounded-[1.2rem] transition-all duration-300 group/link" href="${rootPath}client/propose_project.php" data-href="client/propose_project.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform text-slate-400">add_task</span>
            <span class="font-headline font-bold text-sm tracking-tight">Propose</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3 text-slate-900 dark:text-slate-100 hover:bg-primary/10 rounded-[1.2rem] transition-all duration-300 group/link" href="${rootPath}client/procurement.php" data-href="client/procurement.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform text-slate-400">local_shipping</span>
            <span class="font-headline font-bold text-sm tracking-tight">Logistics</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3 text-slate-900 dark:text-slate-100 hover:bg-primary/10 rounded-[1.2rem] transition-all duration-300 group/link" href="${rootPath}client/tickets.php" data-href="client/tickets.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform text-slate-400">confirmation_number</span>
            <span class="font-headline font-bold text-sm tracking-tight">Support</span>
        </a>
        <a class="nav-link flex items-center gap-4 px-5 py-3 text-slate-900 dark:text-slate-100 hover:bg-primary/10 rounded-[1.2rem] transition-all duration-300 group/link" href="${rootPath}client/hsse.php" data-href="client/hsse.php">
            <span class="material-symbols-outlined text-xl group-hover/link:scale-110 transition-transform text-slate-400">health_and_safety</span>
            <span class="font-headline font-bold text-sm tracking-tight">HSSE</span>
        </a>
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-200/50 dark:border-slate-800/50 flex items-center justify-between gap-3 shrink-0 relative z-20">
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-primary hover:bg-primary/5 rounded-2xl transition-all group/mini" href="${rootPath}client/settings.php">
            <span class="material-symbols-outlined text-lg">settings</span>
            <span class="text-[9px] font-black uppercase tracking-[0.15em]">Settings</span>
        </a>
        
        <a href="${rootPath}client/logout.php" class="flex items-center gap-3 px-6 py-3 bg-slate-950 text-white rounded-2xl font-bold text-[9px] uppercase tracking-[0.15em] hover:bg-red-500 transition-all active:scale-[0.95] shadow-lg ring-1 ring-white/10">
            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">logout</span>
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
    body.sidenav-active main {
        filter: blur(1px) brightness(0.98);
        transform: scale(0.995) translateX(2px);
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        border-radius: 2rem;
        overflow: hidden;
    }
    main {
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
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
                document.body.classList.add('sidenav-active');
            } else {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.classList.remove('sidenav-active');
            }
        }

        window.toggleClientSidenav = toggleSidenav;
        overlay.onclick = () => toggleSidenav(false);
        if (closeBtn) closeBtn.onclick = () => toggleSidenav(false);

        links.forEach(link => {
            const dataHref = link.getAttribute('data-href');
            if (dataHref && currentUrl.includes(dataHref)) {
                link.classList.remove('text-slate-900', 'dark:text-slate-100');
                link.classList.add('bg-white', 'dark:bg-slate-950', 'text-primary', 'shadow-xl', 'shadow-primary/10', 'ring-1', 'ring-primary/10');
            }
        });
    });
})();

