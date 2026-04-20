(function () {
    const clientTopnavHTML = `
<!-- TopNavBar -->
<nav class="fixed top-0 w-full z-50 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl bg-gradient-to-b from-slate-200/20 to-transparent shadow-[0_40px_60px_-15px_rgba(0,0,0,0.05)]">
<div class="flex justify-between items-center px-8 h-16 w-full max-w-[1920px] mx-auto">
<div class="flex items-center gap-2">
<button id="client-sidebar-toggle" class="lg:hidden p-2 text-slate-500 hover:text-blue-600 transition-colors">
<span class="material-symbols-outlined">menu</span>
</button>
<span class="text-xl font-bold tracking-tighter text-slate-900 dark:text-slate-100 font-['Space_Grotesk']">WILSOVLEWEL</span>
<div class="hidden md:flex items-center gap-6 font-['Space_Grotesk'] tracking-tight">
<a class="text-blue-700 dark:text-blue-400 font-bold border-b-2 border-blue-700 transition-colors" href="#">Terminal</a>
<a class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors" href="#">Insights</a>
<a class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors" href="#">Reports</a>
</div>
</div>
<div class="flex items-center gap-4">
<div class="flex items-center gap-2">
<button class="p-2 text-slate-500 hover:text-blue-600 transition-colors">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="p-2 text-slate-500 hover:text-blue-600 transition-colors">
<span class="material-symbols-outlined">settings</span>
</button>
</div>
<img alt="Client Profile" class="w-10 h-10 rounded-full border-2 border-surface-container-high object-cover" data-alt="professional portrait of a mechanical engineer in a modern office with blue industrial blueprints in background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvA8jiYE3XLfxn4zoHv6yGSqPmhfH6SJUNq-eww-gmysXbVVvS-kVHyB9j_fmBK7TEQqVZbftrasDbkl09jygOBEW56PWx_Pu6Z9oVxvFZP90ISPrRCxJhPiZMqkEYbUo72qibthSnqTDxCVixma9uRAy8mPcPDpzkjSig8-rw54vkqOkBY_twlToUUw4w8hc-o0fKg3xLyL3QKGp5Fd04ua9doAraSzEvB7vs82CJ9cyIoJbzsJQcKYn2Pw-cFHv-AxTQfiCn8xnq"/>
</div>
</div>
</nav>
    `;
    document.write(clientTopnavHTML);

    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById('client-sidebar-toggle');
        const sidebar = document.querySelector('aside');
        if (toggle && sidebar) {
            toggle.onclick = (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
            };
            document.addEventListener('click', (e) => {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        }
    });
})();
