(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const footerHTML = `
<!-- Footer Shell -->
<footer class="w-full bg-slate-100 py-12 px-8 border-t border-slate-200 relative z-20 transition-all">
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 items-center">
<div class="space-y-2">
<a href="${rootPath}index.html" class="text-lg font-bold font-headline hover:opacity-80 transition-opacity block">Wilsovlewel Engineering</a>
<p class="font-body text-xs text-slate-500">© 2024 Industrial Precision Engineering. All rights reserved.</p>
</div>
<div class="flex flex-wrap justify-center gap-6 my-8 md:my-0">
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}index.html">Home</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}about.html">About</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}services.html">Services</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}industries.html">Industries</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}hsse.html">HSSE</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}projects.html">Projects</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}career.html">Careers</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}faq.html">FAQ</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}contact.html">Contact</a>
<a class="text-slate-500 hover:text-slate-900 transition-colors font-body text-xs font-medium underline-offset-4 hover:underline" href="${rootPath}login.html">Terminal</a>
</div>
<div class="flex justify-end gap-4">
<a href="#" class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-blue-700 hover:text-white transition-colors cursor-pointer group">
<span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">hub</span>
</a>
<a href="mailto:contact@wilsovewel.com" class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-blue-700 hover:text-white transition-colors cursor-pointer group">
<span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">mail</span>
</a>
</div>
</div>
</footer>
    `;

    document.write(footerHTML);
})();
