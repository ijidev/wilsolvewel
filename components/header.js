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

    // Load Primary Body Font (Inter)
    const fontLink = document.createElement('link');
    fontLink.rel = 'stylesheet';
    fontLink.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap';
    document.head.appendChild(fontLink);

    // Load Client-Configurable Site Image Overrides
    const siteImagesScript = document.createElement('script');
    siteImagesScript.src = `${rootPath}components/site-images.js?v=2`;
    siteImagesScript.setAttribute('data-root', rootPath);
    document.body.appendChild(siteImagesScript);

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
                        "body": ["Inter", "Arial", "sans-serif"],
                        "label": ["Space Grotesk"]
                    },
                    fontSize: {
                        "xs": ["0.75rem", { "lineHeight": "1.25rem" }],
                        "sm": ["0.875rem", { "lineHeight": "1.5rem" }],
                        "base": ["1.125rem", { "lineHeight": "2rem" }],
                        "lg": ["1.25rem", { "lineHeight": "2.25rem" }],
                        "xl": ["1.375rem", { "lineHeight": "2.25rem" }],
                        "2xl": ["1.5rem", { "lineHeight": "2rem" }],
                        "3xl": ["2rem", { "lineHeight": "2.5rem" }],
                        "4xl": ["2.375rem", { "lineHeight": "2.5rem" }],
                        "5xl": ["3rem", { "lineHeight": "1.15" }],
                        "6xl": ["4rem", { "lineHeight": "1.05" }],
                        "7xl": ["4.5rem", { "lineHeight": "1" }]
                    }
                }
            }
        };
    }

    const pages = [
        { 
            name: 'Services', 
            path: 'services.php',
            hasMega: true,
            items: [
                { name: 'Civil Engineering', path: 'services/engineering.php', desc: 'Structural design & foundations', icon: 'architecture' },
                { name: 'Technical Support', path: 'services/technical-support.php', desc: 'Maintenance & diagnostics', icon: 'engineering' }
            ]
        },
        { 
            name: 'Industries', 
            path: 'industries.php',
            hasMega: true,
            items: [
                { name: 'Oil & Gas', path: 'industries.php#oil-gas', desc: 'E&P flow improvement', icon: 'oil_barrel' },
                { name: 'Power Gen', path: 'industries.php#power', desc: 'Turbine & grid support', icon: 'bolt' },
                { name: 'Manufacturing', path: 'industries.php#manufacturing', desc: 'Plant flow optimization', icon: 'factory' },
                { name: 'Construction', path: 'industries.php#construction', desc: 'Heavy machinery foundations', icon: 'foundation' }
            ]
        },
        { name: 'About Us', path: 'about.php' },
        { name: 'Hydraulic Pump Solution', path: 'services/hydraulic-pump.php' },
        { name: 'Strategic Global Sourcing Solution', path: 'services/procurement.php' },
        { name: 'HSSE', path: 'hsse.php' },
        { name: 'Project Experience', path: 'projects.php' },
        { name: 'Contact Us', path: 'contact.php' }
    ];

    const navLinksHTML = pages.map(page => {
        if (page.hasMega) {
            return `
            <div class="group/dropdown relative h-full flex items-center">
                <a class="nav-link text-on-surface-variant font-headline font-bold text-[11px] uppercase tracking-widest hover:text-primary transition-all pb-1 flex items-center gap-1 cursor-pointer" 
                   href="${rootPath}${page.path}">
                   ${page.name}
                   <span class="material-symbols-outlined text-xs transition-transform group-hover/dropdown:rotate-180">expand_more</span>
                </a>
                
                <!-- Dropdown Menu -->
                <div class="absolute top-[calc(100%-4px)] left-0 w-72 bg-white/95 backdrop-blur-2xl rounded-xl shadow-2xl border border-outline-variant/10 py-2 opacity-0 translate-y-2 pointer-events-none group-hover/dropdown:opacity-100 group-hover/dropdown:translate-y-0 group-hover/dropdown:pointer-events-auto transition-all duration-200 ease-out z-[110]">
                    ${page.items.map(item => `
                        <a href="${rootPath}${item.path}" class="flex items-center gap-3 px-4 py-3 hover:bg-primary/5 group/item transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-surface-container-high flex items-center justify-center shrink-0 group-hover/item:bg-primary/10">
                                <span class="material-symbols-outlined text-on-surface-variant group-hover/item:text-primary transition-colors text-lg">${item.icon}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-bold uppercase tracking-widest text-on-surface group-hover/item:text-primary transition-colors leading-tight">${item.name}</span>
                                <span class="text-[10px] text-on-surface-variant font-light leading-tight opacity-60">${item.desc}</span>
                            </div>
                        </a>
                    `).join('')}
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
            <a href="${rootPath}index.php" class="flex items-center gap-3 group">
                <div class="h-10 w-16 overflow-hidden rounded shadow-sm border border-outline-variant/20 bg-white">
                    <img src="${rootPath}assets/WSW logo.jpg" class="w-full h-full object-contain" alt="WSW Logo">
                </div>
            </a>
            
            <!-- Desktop Nav -->
            <div class="hidden lg:flex items-center gap-8 h-full" id="nav-links">
                ${navLinksHTML}
            </div>

            <div class="flex items-center gap-4">
                <a href="${rootPath}client/login.php" class="hidden md:inline-flex items-center justify-center anodized-gradient text-on-primary px-5 py-2.5 rounded-xl font-headline font-bold text-[10px] uppercase tracking-[0.15em] hover:shadow-lg transition-all active:scale-95 shadow-md shadow-primary/20">
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
                <a href="${rootPath}client/login.php" class="flex w-full items-center justify-center anodized-gradient text-on-primary py-4 rounded-xl font-headline font-bold text-sm uppercase tracking-widest">
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
        const currentPath = window.location.pathname.split('/').pop() || 'index.php';
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
