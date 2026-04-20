(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    const pages = [
        { name: 'Home', path: 'index.html' },
        { name: 'About', path: 'about.html' },
        { name: 'Services', path: 'services.html' },
        { name: 'Industries', path: 'industries.html' },
        { name: 'HSSE', path: 'hsse.html' },
        { name: 'Projects', path: 'projects.html' },
        { name: 'Careers', path: 'career.html' },
        { name: 'FAQ', path: 'faq.html' },
        { name: 'Contact', path: 'contact.html' }
    ];

    const navLinksHTML = pages.map(page => `
        <a class="nav-link text-on-surface-variant font-headline font-bold text-[11px] uppercase tracking-widest hover:text-primary transition-all pb-1" 
           href="${rootPath}${page.path}">${page.name}</a>
    `).join('');

    const mobileLinksHTML = pages.map(page => `
        <a class="mobile-nav-link text-on-surface text-lg font-headline font-bold py-4 border-b border-outline-variant/30 w-full" 
           href="${rootPath}${page.path}">${page.name}</a>
    `).join('');

    const headerHTML = `
    <!-- Top Navigation Shell -->
    <nav class="fixed top-0 w-full z-[100] glass-nav h-16 flex items-center shadow-sm">
        <div class="max-w-7xl mx-auto w-full px-6 flex justify-between items-center">
            <a href="${rootPath}index.html" class="text-lg font-bold tracking-tighter text-on-surface font-headline hover:opacity-80 transition-opacity">
                Wilsovlewel Engineering
            </a>
            
            <!-- Desktop Nav -->
            <div class="hidden lg:flex items-center gap-6" id="nav-links">
                ${navLinksHTML}
            </div>

            <div class="flex items-center gap-4">
                <a href="${rootPath}login.html" class="hidden md:flex anodized-gradient text-on-primary px-5 py-2 rounded-lg font-headline font-bold text-xs uppercase tracking-widest hover:shadow-lg transition-all active:scale-95">
                    Terminal
                </a>
                
                <!-- Mobile Toggle -->
                <button id="mobile-menu-toggle" class="lg:hidden p-2 text-on-surface hover:bg-surface-container-high rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 z-[110] bg-surface-container transition-all duration-300 translate-x-full lg:hidden">
        <div class="flex flex-col h-full bg-surface-container-low p-8">
            <div class="flex justify-between items-center mb-12">
                <span class="text-sm font-headline font-bold text-primary uppercase tracking-widest leading-none">Navigation</span>
                <button id="mobile-menu-close" class="p-2 text-on-surface">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>
            
            <div class="flex flex-col items-start overflow-y-auto">
                ${mobileLinksHTML}
            </div>
            
            <div class="mt-auto pt-8">
                <a href="${rootPath}login.html" class="flex w-full items-center justify-center anodized-gradient text-on-primary py-4 rounded-lg font-headline font-bold text-sm uppercase tracking-widest">
                    Access Terminal
                </a>
            </div>
        </div>
    </div>
    `;

    document.write(headerHTML);

    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById('mobile-menu-toggle');
        const close = document.getElementById('mobile-menu-close');
        const menu = document.getElementById('mobile-menu');

        if (toggle && menu && close) {
            toggle.onclick = () => menu.classList.remove('translate-x-full');
            close.onclick = () => menu.classList.add('translate-x-full');

            // Close menu on link click
            document.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.onclick = () => menu.classList.add('translate-x-full');
            });
        }

        // Active link highlighter
        const currentPath = window.location.pathname.split('/').pop() || 'index.html';
        const desktopLinks = document.querySelectorAll('#nav-links .nav-link');
        const mobileLinks = document.querySelectorAll('.mobile-nav-link');

        const highlight = (links) => {
            links.forEach(link => {
                const linkPath = link.getAttribute('href').split('/').pop();
                if (currentPath === linkPath) {
                    link.classList.remove('text-on-surface-variant', 'text-on-surface');
                    link.classList.add('text-primary');
                    if (link.classList.contains('nav-link')) {
                        link.classList.add('border-b-[2px]', 'border-primary');
                    }
                }
            });
        };

        highlight(desktopLinks);
        highlight(mobileLinks);
    });

})();
