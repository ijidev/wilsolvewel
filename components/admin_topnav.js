(function () {
    const topnavHTML = `
<!-- TopNavBar -->
<header class="flex justify-between items-center h-16 px-8 ml-64 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl sticky top-0 z-40 border-b border-slate-100 dark:border-slate-800">
<div class="flex items-center bg-surface-container-low px-4 py-2 rounded-sm gap-3 w-96">
<span class="material-symbols-outlined text-outline text-lg">search</span>
<input class="bg-transparent border-none focus:ring-0 text-sm font-body w-full" placeholder="Search system database..." type="text"/>
</div>
<div class="flex items-center gap-6">
<div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
<span class="material-symbols-outlined cursor-pointer hover:text-blue-600 transition-all">notifications</span>
<span class="material-symbols-outlined cursor-pointer hover:text-blue-600 transition-all">help_outline</span>
<span class="material-symbols-outlined cursor-pointer hover:text-blue-600 transition-all">account_circle</span>
</div>
</div>
</header>
    `;
    document.write(topnavHTML);
})();
