<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

</head>

<body
    class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
    <!-- Top Navigation Shell -->
    <script src="./components/header.js" data-root="./"></script>
    <script src="./components/effects.js"></script>
    <main class="relative pt-20">
        <!-- Technical Grid Overlay Background -->
        <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

        <!-- Hero Section -->
        <section class="relative py-24 px-5 sm:px-6 lg:px-12 z-10 overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="aspect-[21/9] rounded-3xl overflow-hidden shadow-2xl relative border border-outline-variant/20">
                    <img class="w-full h-full object-cover grayscale transition-all duration-1000 hover:scale-105 hover:grayscale-0"
                        alt="Our Services"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCD17Sy51HJsUf63BByvgnnpMfg5yeqdoC3ixi4nP7Z6LyxVCxK5YlP8CszalT0CLIcJHV_IuwrmDpO0gG6eRKZzZozHXdoHu5dri-S8XpxPoib378h975L8XxlKTqd-NjMEb_E1m3_JEyo7tdAOst35_RHhz1ysEbLYmq1etwvvpFe4yhurPx71Twg-BY6ju0DE9XztQpcjDU5xFeQ89DOYoFxW7fvm-vaoR-0uUNLsZLZhmIHbSuUjuEq_Wu48P0S-yuznS-Flu6s" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-10 lg:p-16">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">Our Services</span>
                        <h1 class="font-headline text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-[0.95] tracking-tighter max-w-4xl mb-4">
                            Integrated Engineering, Procurement &amp; Industrial Support Solutions
                        </h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Introduction -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Solutions Designed Around Operational Performance</span>
                    <div class="space-y-6 text-on-surface-variant text-base font-light leading-relaxed">
                        <p>Organizations operating in demanding environments require more than service providers—they require dependable partners capable of solving technical challenges, supporting critical operations and delivering measurable results.</p>
                        <p>Every organization depends on reliable equipment, efficient procurement systems and well-executed infrastructure. When any of these areas fail, productivity suffers, costs increase and projects become vulnerable to delays.</p>
                        <p>Our service portfolio is designed to help organizations maintain operational continuity, improve asset reliability and execute projects with confidence.</p>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                        <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                            alt="Industrial Operations"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9OxK7bvEeBHiB4IiD08woFatMHovl7-Mrrn2nVScQbp2TSyCXI-o0CTKd_wCTcm4Z5eTu7p4EIzDhsJZ76ptcJu1U4nRYG4STYB1gA1sG9Sc7w3jDbhMgICS838aIhHwIh_eVvoDmx4Bns1MkrwcqCKiq7yeS1Mt9sAngeckaWjVMqc2OGhh4cwx56PQK-8mtYSC_CfaMB7m1O9b5lKk2mKF6zFungAuDwRy0UdMo_o-fMNcPiWpHVRWQDzZQRohfco4zwbYxyD1s" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SERVICE 1: Technical Support & Maintenance -->
        <section class="py-32 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-primary mb-4">
                        <span class="material-symbols-outlined text-4xl">engineering</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Service 01</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Technical Support &amp; <span class="text-primary italic">Maintenance.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Keeping Critical Equipment Operating at Peak Performance. Equipment failure can disrupt production, delay projects, increase operating costs and compromise business objectives.</p>
                </div>

                <!-- Alternating Layout: Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel provides professional technical support and maintenance services that help organizations restore equipment performance, extend asset life and minimize operational downtime.</p>
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Our team combines practical field experience with structured diagnostic processes to identify root causes, implement effective corrective actions and support long-term equipment reliability.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Challenges We Help Solve</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Unexpected equipment breakdowns
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Recurring equipment failures
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Poor equipment performance
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Lack of specialized expertise
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> High maintenance costs
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Extended operational downtime
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Technical Maintenance"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCD17Sy51HJsUf63BByvgnnpMfg5yeqdoC3ixi4nP7Z6LyxVCxK5YlP8CszalT0CLIcJHV_IuwrmDpO0gG6eRKZzZozHXdoHu5dri-S8XpxPoib378h975L8XxlKTqd-NjMEb_E1m3_JEyo7tdAOst35_RHhz1ysEbLYmq1etwvvpFe4yhurPx71Twg-BY6ju0DE9XztQpcjDU5xFeQ89DOYoFxW7fvm-vaoR-0uUNLsZLZhmIHbSuUjuEq_Wu48P0S-yuznS-Flu6s" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- Alternating Layout: Image Left, Text Right - Capabilities -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="relative order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Engine Overhaul"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBVgwZ6-ASDZ4F6zjwKc3HQtRiQuYcbJ7n-Y_wylgzjfQJAhwLhb4J_OEhuZ5XvY2x2zJKDQIF6ehkN73MpnB8l9YHNlHGW1bfSZTjm4uRjQqYeTdGTnAEf1CVF6W0uphCOSj9v2Ef5_Z--C1z6DPobwDVHhVwgR9gUj43_8SelTxvQcU1QXkOg49IxcbOiTYm9xvkiLbPbzm4ZXq0JbxeVnPtsyo5ByFqr7Irw4t9hMhRwleprFvTcaE_OOFScUFF-mrr39D5wagVp" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    <div class="space-y-8 order-1 lg:order-2">
                        <h3 class="font-headline text-3xl font-bold text-on-surface tracking-tight">Our Technical Support <span class="text-primary">Capabilities.</span></h3>
                        <div class="space-y-4">
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">troubleshoot</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Diagnostic &amp; Troubleshooting Services</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Systematic fault identification and root-cause analysis for industrial equipment, engines, power systems and heavy machinery.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">build</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Emergency &amp; Corrective Maintenance</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Rapid-response maintenance support designed to restore operations and reduce downtime during critical equipment failures.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">settings</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Engine Repair &amp; Overhaul</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Professional engine inspection, repair, rebuilding and overhaul services aimed at restoring performance and reliability.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">bolt</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Alternator &amp; Electric Motor Reconditioning</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Restoration and rehabilitation services that improve electrical performance and extend equipment service life.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">power</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Power Plant Technical Support</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Technical assistance for generators, gas engines, power systems and related infrastructure supporting energy production.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">construction</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Heavy Equipment &amp; Earthmoving Machinery Support</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Maintenance and technical support for excavators, wheel loaders, bulldozers, cranes, concrete pumps and other heavy-duty equipment.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">play_circle</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Installation &amp; Commissioning Support</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Professional support for equipment installation, startup, testing and operational readiness.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">school</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Technical Training &amp; Consultancy</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Specialized training programs and technical advisory services that improve operational competence and maintenance effectiveness.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Why Clients Choose + CTA -->
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">Why Clients Choose Our Technical Support Team</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Fast Response</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Accurate Diagnostics</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Practical Industrial Experience</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Reduced Downtime</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Improved Asset Reliability</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Professional Reporting</div>
                            </div>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Need Immediate Technical Assistance?</p>
                            <a href="contact.php" class="inline-block bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Contact Our Technical Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SERVICE 2: Procurement & International Logistics -->
        <section class="py-32 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-secondary mb-4">
                        <span class="material-symbols-outlined text-4xl">inventory_2</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Service 02</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Procurement &amp; <span class="text-secondary italic">International Logistics.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Eliminating Supply Delays and Procurement Complexity. Inconsistent supply chains, unreliable vendors and delayed deliveries can halt operations and inflate project costs.</p>
                </div>

                <!-- Alternating Layout: Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel manages the complete procurement lifecycle—from technical specification review and OEM verification to global sourcing, international freight, customs clearance and final delivery to site.</p>
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">We work with verified international suppliers and authorized distributors to ensure every component meets manufacturer standards and client specifications.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Challenges We Help Solve</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Difficulty sourcing OEM parts
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Extended delivery timelines
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Customs and import delays
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Vendor reliability concerns
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Unclear technical specifications
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> High procurement overhead
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Procurement Logistics"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- Alternating Layout: Image Left, Text Right - Capabilities -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="relative order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Global Sourcing"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL2icG4PWakb4pmy3ahdG-5OsyndkSQ_XAp34Aja84XNeMxyihqgKc9J740YcrLkU3RAQVAgsEpIO_6s1imD5VcaAE8UyR0RJVzhUZ7yV51fXNAs6ddL-yf-rH-DHSdAiz1l_eIoylGhr1sh1-Pgxah-MIm0nj8Z-aQjynFCM7uNq64WdHjv-fb2wZrY65ZABrfFf_QUsNNFg0wmMyqRygLgC3EsUngMi4TgqUEk6_evRNwwIBhAmw0e76jYtl9CCFH0vk5LcpAUoG" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    <div class="space-y-8 order-1 lg:order-2">
                        <h3 class="font-headline text-3xl font-bold text-on-surface tracking-tight">Our Procurement <span class="text-secondary">Capabilities.</span></h3>
                        <div class="space-y-4">
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-secondary text-2xl mt-0.5">settings_input_component</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Mechanical &amp; Rotational Equipment</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Pumps, compressors, gearboxes, bearings and associated components from authorized OEM sources.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-secondary text-2xl mt-0.5">bolt</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Electrical &amp; Instrumentation</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Switchgear, transmitters, sensors, control panels and electrical components to specification.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-secondary text-2xl mt-0.5">water_drop</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Hydraulic Systems &amp; Components</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Pumps, valves, cylinders, seals, hose assemblies and complete hydraulic power units.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-secondary text-2xl mt-0.5">search</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Specialized &amp; Hard-to-Source Materials</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Precision procurement for niche components, legacy parts and specification-driven materials.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brands We Support -->
                <div class="bg-surface-container-lowest p-10 rounded-3xl border border-outline-variant/10 mb-20">
                    <div class="text-center mb-10">
                        <h3 class="font-headline text-2xl font-bold">Brands We <span class="text-secondary">Support.</span></h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div class="p-4 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Caterpillar</span>
                        </div>
                        <div class="p-4 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Cummins</span>
                        </div>
                        <div class="p-4 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Perkins</span>
                        </div>
                        <div class="p-4 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Terex</span>
                        </div>
                        <div class="p-4 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Schwing</span>
                        </div>
                        <div class="p-4 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">OEM-Equiv.</span>
                        </div>
                    </div>
                </div>

                <!-- Procurement Process Flow -->
                <div class="p-10 bg-on-surface rounded-3xl text-surface relative overflow-hidden mb-20">
                    <div class="absolute inset-0 opacity-10 technical-grid pointer-events-none"></div>
                    <div class="relative z-10">
                        <h3 class="font-headline text-2xl font-bold text-center mb-12">Our Procurement <span class="text-secondary">Process.</span></h3>
                        <div class="flex flex-wrap justify-between gap-8 text-center items-start">
                            <div class="flex-1 min-w-[120px] space-y-4">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">1</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Spec Review</span>
                            </div>
                            <div class="flex-1 min-w-[120px] space-y-4">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">2</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">OEM Verify</span>
                            </div>
                            <div class="flex-1 min-w-[120px] space-y-4">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">3</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Sourcing</span>
                            </div>
                            <div class="flex-1 min-w-[120px] space-y-4">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">4</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Global Freight</span>
                            </div>
                            <div class="flex-1 min-w-[120px] space-y-4">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">5</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Clearance</span>
                            </div>
                            <div class="flex-1 min-w-[120px] space-y-4">
                                <span class="w-10 h-10 rounded-full border border-secondary bg-secondary flex items-center justify-center mx-auto text-xs font-bold text-on-secondary">6</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-secondary">Final Delivery</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Solutions: Hydraulic Pump + Global Sourcing -->
                <div class="space-y-20">
                    <!-- Hydraulic Pump Refurbishment -->
                    <div class="grid lg:grid-cols-12 gap-12 items-center">
                        <div class="lg:col-span-7 space-y-8">
                            <div class="inline-flex items-center gap-3 text-secondary">
                                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">water_drop</span>
                                <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Featured Solution</span>
                            </div>
                            <h3 class="font-headline text-3xl md:text-4xl font-bold tracking-tight">Hydraulic Pump <span class="text-secondary">Refurbishment.</span></h3>
                            <p class="text-on-surface-variant font-light leading-relaxed">Rather than purchasing an entirely new pump assembly, many systems can be restored to optimal performance through professional refurbishment supported by authentic OEM internal components.</p>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Cylinder Blocks
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Piston Shoes
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Valve Plates
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Swash Plates
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Seal &amp; Gasket Kits
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Control Assemblies
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-5">
                            <div class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/10 space-y-6">
                                <h4 class="font-headline font-bold text-lg">Submit Pump Specifications</h4>
                                <p class="text-xs text-on-surface-variant leading-relaxed">Submit your pump manufacturer, model and serial number for a structured sourcing evaluation.</p>
                                <a href="spec-forms.php#pump-form" class="block bg-secondary text-on-secondary text-center py-4 rounded-xl font-headline font-bold text-sm uppercase tracking-widest hover:scale-[1.02] transition-transform">
                                    Submit Specs Online
                                </a>
                                <p class="text-[10px] text-center text-on-surface-variant/60 italic">Send completed form to: procurement@wilsolvewel.com</p>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-outline-variant/20"></div>

                    <!-- Strategic Global Sourcing -->
                    <div class="grid lg:grid-cols-12 gap-12 items-center">
                        <div class="lg:col-span-5 order-2 lg:order-1">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                                    <span class="material-symbols-outlined text-secondary text-3xl mb-3">memory</span>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest">IT &amp; Technical</span>
                                </div>
                                <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                                    <span class="material-symbols-outlined text-secondary text-3xl mb-3">settings_input_component</span>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest">Valves &amp; Flow</span>
                                </div>
                                <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                                    <span class="material-symbols-outlined text-secondary text-3xl mb-3">bolt</span>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest">Electrical</span>
                                </div>
                                <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                                    <span class="material-symbols-outlined text-secondary text-3xl mb-3">settings_suggest</span>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest">Instrumentation</span>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-7 space-y-8 order-1 lg:order-2">
                            <div class="inline-flex items-center gap-3 text-primary">
                                <span class="material-symbols-outlined text-4xl">public</span>
                                <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Global Network</span>
                            </div>
                            <h3 class="font-headline text-3xl md:text-4xl font-bold tracking-tight">Strategic <span class="text-primary">Global Sourcing</span> Solutions.</h3>
                            <p class="text-on-surface-variant font-light leading-relaxed">We provide precision procurement for specialized and hard-to-source materials based strictly on client specifications. We do not engage in speculative sourcing—we source precisely what you specify using our verified international supplier network.</p>
                            <a href="spec-forms.php#sourcing-form" class="inline-flex items-center gap-4 text-secondary font-headline font-bold text-sm uppercase tracking-widest border-b border-secondary pb-1 hover:gap-6 transition-all">
                                Material Specification Form <span class="material-symbols-outlined">east</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Why Clients Choose + CTA -->
                <div class="mt-20 bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">Why Clients Choose Our Procurement Team</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Verified OEM Sources</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Global Supplier Network</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> End-to-End Logistics</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Customs Clearance Support</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Specification Accuracy</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Delivery Tracking</div>
                            </div>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Ready to Streamline Your Procurement?</p>
                            <a href="contact.php" class="inline-block bg-secondary text-on-secondary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-secondary/20 hover:scale-105 transition-all">
                                Contact Our Procurement Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SERVICE 3: Industrial Civil, Structural & Construction -->
        <section class="py-32 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-primary mb-4">
                        <span class="material-symbols-outlined text-4xl">architecture</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Service 03</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Industrial Civil, Structural &amp; <span class="text-primary italic">Construction.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Engineering and Constructing Infrastructure That Supports Heavy-Duty Industrial Operations. Structural failures, inadequate foundations and poorly executed civil works can compromise equipment performance and project timelines.</p>
                </div>

                <!-- Alternating Layout: Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel provides specialized civil and structural engineering designed to support heavy-duty machinery, industrial plants and large-scale infrastructure projects.</p>
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Our solutions focus on structural integrity, load safety and durability—ensuring that equipment installations perform optimally throughout their service life.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Challenges We Help Solve</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Inadequate equipment foundations
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Structural integrity concerns
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Load capacity limitations
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Construction quality issues
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Project scope uncertainty
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Regulatory compliance gaps
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Industrial Construction"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgAXGKG4O_fRgqzt1Ov-O4zdKcd1miG_RYprvywVztEDtFxKm6sK36ssUHA0zyOGBE--ETWfnDKawgK9Ep6hf9PEoHK0M07G0GURrfsCDVPbglFwzrBjcJwnAFs3KIK4lcAnjjOt5cQtlFj0qS3lC2kscx1VyGFs9FLPCc7AWjmTqU5xWEwefnm6C2Z3Fe-IPIfZIQuL1R3mDM2Gq0clSfrsiZY0MztT1ydZ5pQwdgpS7uR3msPiDQoxxKQmY6uXp0rxffPZSkrXGz" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- Alternating Layout: Image Left, Text Right - Capabilities -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="relative order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Structural Engineering"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBVgwZ6-ASDZ4F6zjwKc3HQtRiQuYcbJ7n-Y_wylgzjfQJAhwLhb4J_OEhuZ5XvY2x2zJKDQIF6ehkN73MpnB8l9YHNlHGW1bfSZTjm4uRjQqYeTdGTnAEf1CVF6W0uphCOSj9v2Ef5_Z--C1z6DPobwDVHhVwgR9gUj43_8SelTxvQcU1QXkOg49IxcbOiTYm9xvkiLbPbzm4ZXq0JbxeVnPtsyo5ByFqr7Irw4t9hMhRwleprFvTcaE_OOFScUFF-mrr39D5wagVp" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    <div class="space-y-8 order-1 lg:order-2">
                        <h3 class="font-headline text-3xl font-bold text-on-surface tracking-tight">Our Construction <span class="text-primary">Capabilities.</span></h3>
                        <div class="space-y-4">
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">foundation</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Equipment Base &amp; Foundation Design</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Structural foundations engineered to support heavy machinery, turbines, compressors and industrial equipment.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">grid_on</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Concrete Platforms &amp; Reinforced Structures</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Reinforced concrete platforms, equipment pads and structural frames built to industrial load specifications.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">precision_manufacturing</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Structural Civil Works for Plants</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Complete civil works packages for industrial plants including structural steel, pipe supports and equipment installations.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">draw</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Technical Drawings &amp; Engineering</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Professional civil and structural drawings, load calculations and engineering documentation for approval and execution.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">construction</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Industrial Facility Construction</h4>
                                    <p class="text-xs text-on-surface-variant font-light">End-to-end construction management for workshops, warehouses, processing facilities and industrial buildings.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Industries Served -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center space-y-4">
                        <span class="material-symbols-outlined text-primary text-3xl">local_fire_department</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Oil &amp; Gas Facilities</h4>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center space-y-4">
                        <span class="material-symbols-outlined text-primary text-3xl">bolt</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Power Plants</h4>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center space-y-4">
                        <span class="material-symbols-outlined text-primary text-3xl">factory</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Manufacturing</h4>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center space-y-4">
                        <span class="material-symbols-outlined text-primary text-3xl">engineering</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Construction Sites</h4>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center space-y-4">
                        <span class="material-symbols-outlined text-primary text-3xl">water_pump</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Water Treatment</h4>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center space-y-4">
                        <span class="material-symbols-outlined text-primary text-3xl">solar_power</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Energy Infrastructure</h4>
                    </div>
                </div>

                <!-- Why Clients Choose + CTA -->
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">Why Clients Choose Our Construction Team</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Structural Engineering Expertise</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Industrial Load Knowledge</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Quality Construction Standards</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Project Delivery Reliability</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Safety Compliance</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Cost-Effective Solutions</div>
                            </div>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Planning an Infrastructure Project?</p>
                            <a href="contact.php" class="inline-block bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Discuss Your Project
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary to-primary/80"></div>
            <div class="absolute inset-0 opacity-10 technical-grid pointer-events-none"></div>
            <div class="max-w-7xl mx-auto text-center relative z-10 space-y-10">
                <span class="text-on-primary/60 font-label text-[10px] font-bold uppercase tracking-[0.4em] block">One Partner. Multiple Solutions. Reliable Results.</span>
                <h2 class="font-headline text-4xl md:text-5xl lg:text-6xl font-bold text-on-primary leading-[0.95] tracking-tighter max-w-4xl mx-auto">
                    Ready to Discuss Your Next Project?
                </h2>
                <p class="text-on-primary/80 font-light text-lg max-w-2xl mx-auto">Whether you need technical support, procurement assistance or construction expertise—our team is ready to deliver.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="contact.php" class="bg-on-primary text-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl hover:scale-105 transition-all">
                        Get In Touch
                    </a>
                    <a href="spec-forms.php" class="border-2 border-on-primary/30 text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:bg-on-primary/10 transition-all">
                        View Specification Forms
                    </a>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer -->
    <!-- Footer Shell -->
    <script src="./components/footer.js" data-root="./"></script>
</body>

</html>
