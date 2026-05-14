(function () {
    const clientTopnavHTML = `
<!-- TopNavBar -->
<nav class="fixed top-0 w-full z-50 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl bg-gradient-to-b from-slate-200/20 to-transparent">
<div class="flex justify-between items-center px-8 h-16 w-full max-w-[1920px] mx-auto">
<div class="flex items-center gap-2">
<button id="client-sidebar-toggle" class="p-2 text-slate-500 hover:text-primary transition-all active:scale-90">
<span class="material-symbols-outlined text-2xl">menu</span>
</button>
<span class="text-xl font-bold tracking-tighter text-slate-900 dark:text-slate-100 font-headline ml-2">WILSOVLEWEL</span>
<div class="hidden md:flex items-center gap-6 font-headline tracking-tight ml-8">
<a class="nav-top-link text-slate-500 hover:text-primary transition-all font-medium text-sm" href="index.php" data-href="index.php">Terminal</a>
<a class="nav-top-link text-slate-500 hover:text-primary transition-all font-medium text-sm" href="projects.php" data-href="projects.php">Insights</a>
<a class="nav-top-link text-slate-500 hover:text-primary transition-all font-medium text-sm" href="tickets.php" data-href="tickets.php">Support</a>
</div>
</div>
<div class="flex items-center gap-4 relative">
<div class="flex items-center gap-2">
<button id="btn-notifications" class="p-2 text-slate-500 hover:text-primary transition-colors relative">
<span class="material-symbols-outlined text-xl">notifications</span>
<span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-900"></span>
</button>
<a href="settings.php" class="p-2 text-slate-500 hover:text-primary transition-colors">
<span class="material-symbols-outlined text-xl">settings</span>
</a>
</div>
<div class="w-px h-6 bg-slate-200 dark:bg-slate-800 mx-2"></div>
<img id="btn-profile" alt="Client Profile" class="w-9 h-9 rounded-full border-2 border-primary/20 object-cover shadow-sm hover:ring-4 hover:ring-primary/10 transition-all cursor-pointer" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvA8jiYE3XLfxn4zoHv6yGSqPmhfH6SJUNq-eww-gmysXbVVvS-kVHyB9j_fmBK7TEQqVZbftrasDbkl09jygOBEW56PWx_Pu6Z9oVxvFZP90ISPrRCxJhPiZMqkEYbUo72qibthSnqTDxCVixma9uRAy8mPcPDpzkjSig8-rw54vkqOkBY_twlToUUw4w8hc-o0fKg3xLyL3QKGp5Fd04ua9doAraSzEvB7vs82CJ9cyIoJbzsJQcKYn2Pw-cFHv-AxTQfiCn8xnq"/>

<!-- Notifications Dropdown -->
<div id="dropdown-notifications" class="hidden absolute top-14 right-14 w-80 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden z-50">
    <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
        <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">Notifications</span>
        <span class="text-[10px] text-primary cursor-pointer hover:underline">Mark all read</span>
    </div>
    <div class="p-6 text-center text-slate-500 dark:text-slate-400">
        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">notifications_paused</span>
        <p class="text-xs">You're all caught up!</p>
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

        function closeDropdowns() {
            if(dropNotif) dropNotif.classList.add('hidden');
            if(dropProfile) dropProfile.classList.add('hidden');
        }

        if(btnNotif && dropNotif) {
            btnNotif.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropNotif.classList.contains('hidden');
                closeDropdowns();
                if (isHidden) dropNotif.classList.remove('hidden');
            });
        }

        if(btnProfile && dropProfile) {
            btnProfile.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropProfile.classList.contains('hidden');
                closeDropdowns();
                if (isHidden) dropProfile.classList.remove('hidden');
            });
        }

        document.addEventListener('click', (e) => {
            if(dropNotif && !dropNotif.contains(e.target)) dropNotif.classList.add('hidden');
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
