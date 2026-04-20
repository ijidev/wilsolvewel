(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const headerHTML = `
<!-- Top Navigation Shell -->
<nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl shadow-[0_40px_60px_-15px_rgba(0,0,0,0.05)] h-20 flex items-center transition-all">
<div class="max-w-[1440px] mx-auto w-full px-8 flex justify-between items-center">
<a href="${rootPath}index.html" class="text-xl font-bold tracking-tighter text-slate-900 font-headline hover:opacity-80 transition-opacity">
                Wilsovlewel Engineering
            </a>
<div class="hidden lg:flex items-center gap-6" id="nav-links">
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}index.html">Home</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}about.html">About</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}faq.html">FAQ</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}services.html">Services</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}industries.html">Industries</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}hsse.html">HSSE</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}projects.html">Projects</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}career.html">Careers</a>
<a class="nav-link text-slate-600 pb-1 font-headline tracking-tight text-sm uppercase font-bold hover:text-blue-600 transition-colors duration-200" href="${rootPath}contact.html">Contact</a>
</div>
<div class="flex items-center gap-4">
<a href="${rootPath}login.html" class="text-slate-600 hover:text-blue-600 transition-colors group flex items-center justify-center">
<span class="material-symbols-outlined group-hover:scale-110 transition-transform">login</span>
</a>
<a href="${rootPath}login.html" class="anodized-gradient text-on-primary px-8 py-3 rounded-lg font-headline font-bold text-sm uppercase tracking-tight active:scale-95 hover:shadow-lg transition-all inline-block">
                    Terminal
                </a>
</div>
</div>
</nav>
    `;

    document.write(headerHTML);

    // Active link highlighter
    document.addEventListener("DOMContentLoaded", function () {
        const links = document.querySelectorAll('#nav-links .nav-link');
        const currentPath = window.location.pathname.split('/').pop() || 'index.html';

        links.forEach(link => {
            const linkPath = link.getAttribute('href').split('/').pop();
            if (currentPath === linkPath) {
                link.classList.remove('text-slate-600');
                link.classList.add('text-blue-700', 'border-b-2', 'border-blue-700');
            }
        });
    });

})();
