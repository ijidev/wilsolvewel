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
<div class="flex items-center gap-4">
<div class="flex items-center gap-2">
<button class="p-2 text-slate-500 hover:text-primary transition-colors">
<span class="material-symbols-outlined text-xl">notifications</span>
</button>
<a href="settings.php" class="p-2 text-slate-500 hover:text-primary transition-colors">
<span class="material-symbols-outlined text-xl">settings</span>
</a>
</div>
<div class="w-px h-6 bg-slate-200 dark:bg-slate-800 mx-2"></div>
<img alt="Client Profile" class="w-9 h-9 rounded-full border-2 border-primary/20 object-cover shadow-sm hover:ring-4 hover:ring-primary/10 transition-all cursor-pointer" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvA8jiYE3XLfxn4zoHv6yGSqPmhfH6SJUNq-eww-gmysXbVVvS-kVHyB9j_fmBK7TEQqVZbftrasDbkl09jygOBEW56PWx_Pu6Z9oVxvFZP90ISPrRCxJhPiZMqkEYbUo72qibthSnqTDxCVixma9uRAy8mPcPDpzkjSig8-rw54vkqOkBY_twlToUUw4w8hc-o0fKg3xLyL3QKGp5Fd04ua9doAraSzEvB7vs82CJ9cyIoJbzsJQcKYn2Pw-cFHv-AxTQfiCn8xnq"/>
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
