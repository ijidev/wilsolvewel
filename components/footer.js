(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const footerHTML = `
<!-- Footer Shell -->
<footer class="w-full bg-surface py-16 px-6 lg:px-12 border-t border-outline-variant/20 relative z-20 transition-all">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-12">
        <!-- Brand Section -->
        <div class="md:col-span-4 space-y-6">
            <a href="${rootPath}index.html" class="flex items-center gap-4 group">
                <div class="h-16 w-20 overflow-hidden rounded-lg shadow-sm border border-outline-variant/20 bg-white">
                    <img src="${rootPath}assets/WSW logo.jpg.jpeg" class="w-full h-[150%] object-cover object-top" alt="WSW Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-bold font-headline text-on-surface group-hover:text-primary transition-colors">
                        Wilsovlewel
                    </span>
                    <span class="text-[10px] font-bold font-headline uppercase tracking-[0.2em] text-on-surface-variant/60">
                        Engineering
                    </span>
                </div>
            </a>
            <p class="text-on-surface-variant text-sm leading-relaxed max-w-sm">
                Nigeria's specialized technical partner for heavy machinery repair, technical procurement, and industrial asset optimization.
            </p>
            <div class="flex gap-3">
                <a href="#" class="w-9 h-9 rounded-lg border border-outline-variant flex items-center justify-center text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all group">
                    <span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">hub</span>
                </a>
                <a href="mailto:contact@wilsovewel.com" class="w-9 h-9 rounded-lg border border-outline-variant flex items-center justify-center text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all group">
                    <span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">mail</span>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="md:col-span-8 grid grid-cols-2 sm:grid-cols-3 gap-8">
            <div class="space-y-4">
                <h4 class="text-[10px] font-bold font-headline uppercase tracking-[0.2em] text-primary">Capabilities</h4>
                <div class="flex flex-col gap-3">
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}services.html">Services</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}industries.html">Industries</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}projects.html">Projects</a>
                </div>
            </div>
            <div class="space-y-4">
                <h4 class="text-[10px] font-bold font-headline uppercase tracking-[0.2em] text-primary">Information</h4>
                <div class="flex flex-col gap-3">
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}about.html">About Us</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}career.html">Careers</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}faq.html">FAQ</a>
                </div>
            </div>
            <div class="space-y-4 col-span-2 sm:col-span-1">
                <h4 class="text-[10px] font-bold font-headline uppercase tracking-[0.2em] text-primary">Support</h4>
                <div class="flex flex-col gap-3">
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}contact.html">Contact</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}hsse.html">HSSE Policy</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-colors text-[11px] font-bold uppercase tracking-widest" href="${rootPath}login.html">Terminal Access</a>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-16 pt-8 border-t border-outline-variant/10 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="font-body text-[10px] text-on-surface-variant/60 uppercase tracking-widest">© 2024 Wilsovlewel Engineering. Precision Industrial Support.</p>
        <div class="flex gap-6">
            <a href="#" class="text-[10px] text-on-surface-variant/60 hover:text-on-surface transition-colors uppercase tracking-widest">Privacy</a>
            <a href="#" class="text-[10px] text-on-surface-variant/60 hover:text-on-surface transition-colors uppercase tracking-widest">Terms</a>
        </div>
    </div>
</footer>
    `;

    document.write(footerHTML);
})();
