(function () {
    let currentScript = document.currentScript;
    let rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    // Inject Central CSS
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = `${rootPath}components/global.css`;
    document.head.appendChild(link);

    // Centralized Tailwind Configuration
    if (window.tailwind) {
        window.tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#EAB308",
                        "on-primary": "#000000",
                        "primary-container": "#FEF9C3",
                        "on-primary-container": "#422006",
                        "secondary": "#1A1A1A",
                        "on-secondary": "#FFFFFF",
                        "secondary-container": "#E0E0E0",
                        "on-secondary-container": "#1A1A1A",
                        "tertiary": "#D32F2F",
                        "on-tertiary": "#FFFFFF",
                        "background": "#FDFDFD",
                        "on-background": "#1A1A1A",
                        "surface": "#FDFDFD",
                        "on-surface": "#1A1A1A",
                        "surface-variant": "#F5F5F5",
                        "on-surface-variant": "#4A4A4A",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container-low": "#F7F7F7",
                        "surface-container": "#F3F3F3",
                        "surface-container-high": "#EFEFEF",
                        "surface-container-highest": "#EBEBEB",
                        "outline": "#79747E",
                        "outline-variant": "#CAC4D0",
                        "error": "#B00020"
                    },
                    borderRadius: {
                        "DEFAULT": "0.75rem",
                        "lg": "1.5rem",
                        "xl": "2.25rem",
                        "full": "9999px"
                    },
                    fontFamily: {
                        "headline": ["Space Grotesk"],
                        "body": ["Manrope"],
                        "label": ["Space Grotesk"]
                    },
                    fontSize: {
                        "xs": ["0.65rem", { "lineHeight": "1rem" }],
                        "sm": ["0.75rem", { "lineHeight": "1.125rem" }],
                        "base": ["0.875rem", { "lineHeight": "1.25rem" }],
                        "lg": ["1rem", { "lineHeight": "1.5rem" }],
                        "xl": ["1.125rem", { "lineHeight": "1.75rem" }],
                        "2xl": ["1.25rem", { "lineHeight": "1.75rem" }],
                        "3xl": ["1.5rem", { "lineHeight": "2rem" }],
                        "4xl": ["1.875rem", { "lineHeight": "2.25rem" }],
                        "5xl": ["2.25rem", { "lineHeight": "2.5rem" }],
                        "6xl": ["3rem", { "lineHeight": "1" }],
                        "7xl": ["3.75rem", { "lineHeight": "1" }]
                    }
                }
            }
        };
    }

    const pages = [
        { 
            name: 'Services', 
            path: 'services.html',
            hasMega: true,
            items: [
                { name: 'Civil Engineering', path: 'services/engineering.html', desc: 'Structural design & foundations', icon: 'architecture' },
                { name: 'OEM Procurement', path: 'services/procurement.html', desc: 'Global sourcing & logistics', icon: 'inventory_2' },
                { name: 'Technical Support', path: 'services/technical-support.html', desc: 'Maintenance & diagnostics', icon: 'engineering' },
                { name: 'Specialized Solutions', path: 'services.html#specialized', desc: 'Custom hydraulic refurbishment', icon: 'water_drop' }
            ]
        },
        { 
            name: 'Industries', 
            path: 'industries.html',
            hasMega: true,
            items: [
                { name: 'Oil & Gas', path: 'industries.html#oil-gas', desc: 'E&P flow improvement', icon: 'oil_barrel' },
                { name: 'Power Gen', path: 'industries.html#power', desc: 'Turbine & grid support', icon: 'bolt' },
                { name: 'Manufacturing', path: 'industries.html#manufacturing', desc: 'Plant flow optimization', icon: 'factory' },
                { name: 'Construction', path: 'industries.html#construction', desc: 'Heavy machinery foundations', icon: 'foundation' }
            ]
        },
        { name: 'About', path: 'about.html' },
        { name: 'HSSE', path: 'hsse.html' },
        { name: 'Projects', path: 'projects.html' },
        { name: 'Technical Specs', path: 'spec-forms.html' },
        { name: 'Contact', path: 'contact.html' }
    ];

    const navLinksHTML = pages.map(page => {
        if (page.hasMega) {
            return `
            <div class="group/mega relative h-full flex items-center">
                <a class="nav-link text-on-surface-variant font-headline font-bold text-[11px] uppercase tracking-widest hover:text-primary transition-all pb-1 flex items-center gap-1 cursor-pointer" 
                   href="${rootPath}${page.path}">
                   ${page.name}
                   <span class="material-symbols-outlined text-xs transition-transform group-hover/mega:rotate-180">expand_more</span>
                </a>
                
                <!-- Mega Menu Dropdown -->
                <div class="absolute top-[calc(100%-8px)] left-1/2 -translate-x-1/2 w-[480px] bg-white/95 backdrop-blur-2xl rounded-2xl shadow-2xl border border-outline-variant/10 p-6 opacity-0 translate-y-4 pointer-events-none group-hover/mega:opacity-100 group-hover/mega:translate-y-0 group-hover/mega:pointer-events-auto transition-all duration-300 ease-out z-[110]">
                    <div class="grid grid-cols-2 gap-4">
                        ${page.items.map(item => `
                            <a href="${rootPath}${item.path}" class="flex items-start gap-4 p-4 rounded-xl hover:bg-primary/5 group/item transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center shrink-0 group-hover/item:bg-primary/10">
                                    <span class="material-symbols-outlined text-on-surface-variant group-hover/item:text-primary transition-colors text-xl">${item.icon}</span>
                                </div>
                                <div class="space-y-1">
                                    <span class="block text-[11px] font-bold uppercase tracking-widest text-on-surface group-hover/item:text-primary transition-colors">${item.name}</span>
                                    <span class="block text-[10px] text-on-surface-variant font-light leading-tight opacity-70">${item.desc}</span>
                                </div>
                            </a>
                        `).join('')}
                    </div>
                </div>
            </div>
            `;
        }
        return `
            <a class="nav-link text-on-surface-variant font-headline font-bold text-[11px] uppercase tracking-widest hover:text-primary transition-all pb-1" 
               href="${rootPath}${page.path}">${page.name}</a>
        `;
    }).join('');

    const mobileLinksHTML = pages.map(page => {
        if (page.hasMega) {
            return `
            <div class="w-full">
                <button onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.arrow').classList.toggle('rotate-180')" class="flex justify-between items-center w-full text-on-surface text-lg font-headline font-bold py-4 border-b border-outline-variant/30">
                    ${page.name}
                    <span class="material-symbols-outlined arrow transition-transform">expand_more</span>
                </button>
                <div class="hidden bg-surface-container-low/50 px-4 py-2 border-b border-outline-variant/10">
                    ${page.items.map(item => `
                        <a class="mobile-nav-link block py-3 text-sm font-headline font-bold text-on-surface-variant hover:text-primary" 
                           href="${rootPath}${item.path}">${item.name}</a>
                    `).join('')}
                </div>
            </div>
            `;
        }
        return `
            <a class="mobile-nav-link text-on-surface text-lg font-headline font-bold py-4 border-b border-outline-variant/30 w-full" 
               href="${rootPath}${page.path}">${page.name}</a>
        `;
    }).join('');

    const headerHTML = `
    <!-- Top Navigation Shell -->
    <nav id="main-header" class="fixed top-0 w-full z-[100] glass-nav h-16 flex items-center shadow-sm transition-all duration-500 ease-in-out">
        <div class="max-w-7xl mx-auto w-full px-6 flex justify-between items-center h-full">
            <a href="${rootPath}index.html" class="flex items-center gap-3 group">
                <div class="h-10 w-16 overflow-hidden rounded shadow-sm border border-outline-variant/20 bg-white">
                    <img src="${rootPath}assets/WSW logo.jpg" class="w-full h-full object-contain" alt="WSW Logo">
                </div>
            </a>
            
            <!-- Desktop Nav -->
            <div class="hidden lg:flex items-center gap-8 h-full" id="nav-links">
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
                <div class="h-12 w-20 overflow-hidden rounded bg-white">
                     <img src="${rootPath}assets/WSW logo.jpg" class="w-full h-full object-contain" alt="WSW Logo">
                </div>
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
        const header = document.getElementById('main-header');
        const toggle = document.getElementById('mobile-menu-toggle');
        const close = document.getElementById('mobile-menu-close');
        const menu = document.getElementById('mobile-menu');

        // Scroll Behavior: Hide on Scroll Down, Show on Scroll Up
        let lastScrollY = window.scrollY;
        window.addEventListener('scroll', () => {
            if (window.scrollY > lastScrollY && window.scrollY > 80) {
                header.classList.add('nav-hidden');
            } else {
                header.classList.remove('nav-hidden');
            }
            
            // Add background shadow on scroll
            if (window.scrollY > 20) {
                header.classList.add('shadow-md');
                header.classList.remove('shadow-sm');
            } else {
                header.classList.add('shadow-sm');
                header.classList.remove('shadow-md');
            }
            
            lastScrollY = window.scrollY;
        });

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
