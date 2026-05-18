(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const clientSidenavHTML = `
<!-- Sidenav Overlay -->
<div id="sidenav-overlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-[55] opacity-0 pointer-events-none transition-all duration-500"></div>

<!-- Floating SideNavBar -->
<aside id="client-sidenav" class="fixed inset-x-4 top-4 bottom-4 md:left-[32px] md:top-[60px] md:bottom-[60px] md:inset-x-auto z-[60] md:w-80 bg-white dark:bg-slate-900 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/50 flex flex-col overflow-hidden rounded-[1.75rem] shadow-[0_40px_80px_-16px_rgba(0,0,0,0.3)] transition-all duration-500 cubic-bezier(0.32, 0.72, 0, 1) -translate-x-[calc(100%+80px)] md:-translate-x-[calc(100%+80px)] ring-1 ring-black/5 dark:ring-white/10">

    <!-- Header -->
    <div class="px-5 pt-5 pb-3 shrink-0 border-b border-slate-100 dark:border-slate-800 relative">
        <button id="close-sidenav" class="absolute top-5 right-5 w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-all active:scale-90">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary to-amber-600 flex items-center justify-center text-on-primary shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">precision_manufacturing</span>
            </div>
            <div class="min-w-0">
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white font-headline tracking-tight">Wilsovlewel</h2>
                <p class="text-[9px] text-primary font-bold uppercase tracking-[0.2em]">Terminal v1.0</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto custom-scrollnav px-3 py-3 space-y-0.5" id="client-sidenav-links">
        <p class="px-3 pb-1 pt-2 text-[8px] font-bold text-slate-400 uppercase tracking-[0.2em]">Main</p>
        <a class="nav-link flex items-center gap-3.5 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-primary/5 rounded-xl transition-all duration-200 group/link" href="${rootPath}client/index.php" data-href="client/index.php">
            <span class="material-symbols-outlined text-xl text-slate-400 group-hover/link:text-primary transition-colors" style="font-variation-settings:'FILL' 1;">dashboard</span>
            <span class="font-headline font-semibold text-sm">Dashboard</span>
        </a>
        <a class="nav-link flex items-center gap-3.5 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-primary/5 rounded-xl transition-all duration-200 group/link" href="${rootPath}client/projects.php" data-href="client/projects.php">
            <span class="material-symbols-outlined text-xl text-slate-400 group-hover/link:text-primary transition-colors" style="font-variation-settings:'FILL' 1;">engineering</span>
            <span class="font-headline font-semibold text-sm">Projects</span>
        </a>


        <p class="px-3 pb-1 pt-4 text-[8px] font-bold text-slate-400 uppercase tracking-[0.2em]">Operations</p>
        <a class="nav-link flex items-center gap-3.5 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-primary/5 rounded-xl transition-all duration-200 group/link" href="${rootPath}client/procurement.php" data-href="client/procurement.php">
            <span class="material-symbols-outlined text-xl text-slate-400 group-hover/link:text-primary transition-colors" style="font-variation-settings:'FILL' 1;">local_shipping</span>
            <span class="font-headline font-semibold text-sm">Logistics</span>
        </a>
        <a class="nav-link flex items-center gap-3.5 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-primary/5 rounded-xl transition-all duration-200 group/link" href="${rootPath}client/tickets.php" data-href="client/tickets.php">
            <span class="material-symbols-outlined text-xl text-slate-400 group-hover/link:text-primary transition-colors" style="font-variation-settings:'FILL' 1;">confirmation_number</span>
            <span class="font-headline font-semibold text-sm">Support</span>
        </a>
        <a class="nav-link flex items-center gap-3.5 px-4 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-primary/5 rounded-xl transition-all duration-200 group/link" href="${rootPath}client/hsse.php" data-href="client/hsse.php">
            <span class="material-symbols-outlined text-xl text-slate-400 group-hover/link:text-primary transition-colors" style="font-variation-settings:'FILL' 1;">health_and_safety</span>
            <span class="font-headline font-semibold text-sm">HSSE</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="px-3 py-3 border-t border-slate-100 dark:border-slate-800 shrink-0 mt-auto">
        <a class="flex items-center gap-3.5 px-4 py-2.5 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all duration-200 group/set" href="${rootPath}client/settings.php">
            <span class="material-symbols-outlined text-xl">settings</span>
            <span class="font-headline font-semibold text-xs tracking-wider">Settings</span>
        </a>
        <a href="${rootPath}client/logout.php" class="flex items-center gap-3.5 px-4 py-2.5 mt-0.5 bg-slate-900 dark:bg-slate-800 text-white hover:bg-red-600 dark:hover:bg-red-600 rounded-xl transition-all duration-200 active:scale-[0.98]">
            <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">logout</span>
            <span class="font-headline font-semibold text-xs tracking-wider">Logout</span>
        </a>
    </div>
</aside>

<style>
    #client-sidenav.active { transform: translateX(0); }
    #sidenav-overlay.active { opacity: 1; pointer-events: auto; }
    body.sidenav-active main {
        filter: blur(2px) brightness(0.96);
        transform: scale(0.985) translateX(3px);
        transition: all 0.5s cubic-bezier(0.32, 0.72, 0, 1);
        pointer-events: none;
        border-radius: 2rem;
        overflow: hidden;
    }
    main { transition: all 0.5s cubic-bezier(0.32, 0.72, 0, 1); }
    #client-sidenav-links .nav-link.active {
        background: rgba(234, 179, 8, 0.08);
        color: #1a1a1a;
        font-weight: 700;
    }
    #client-sidenav-links .nav-link.active .material-symbols-outlined {
        color: #EAB308;
    }
    .custom-scrollnav::-webkit-scrollbar { width: 3px; }
    .custom-scrollnav::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollnav::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.08); border-radius: 10px; }
    .dark .custom-scrollnav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); }
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
            if (dataHref && (currentUrl.endsWith(dataHref) || currentUrl.includes('/' + dataHref))) {
                link.classList.add('active');
            }
        });
    });
})();

