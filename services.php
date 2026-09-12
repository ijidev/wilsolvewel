<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
    <script src="./components/header.js" data-root="./"></script>
    <script src="./components/effects.js"></script>

    <main class="relative pt-20">
        <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

        <!-- HERO SECTION -->
        <section class="relative py-24 px-5 sm:px-6 lg:px-12 z-10 overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="aspect-[21/9] rounded-3xl overflow-hidden shadow-2xl relative border border-outline-variant/20">
                    <img class="w-full h-full object-cover transition-all duration-1000 hover:scale-105"
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

        <!-- INTRODUCTION SECTION -->
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
                        <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
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

                <!-- Text Left, Image Right -->
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
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Lack of specialized technical expertise
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
                            <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Technical Maintenance"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCD17Sy51HJsUf63BByvgnnpMfg5yeqdoC3ixi4nP7Z6LyxVCxK5YlP8CszalT0CLIcJHV_IuwrmDpO0gG6eRKZzZozHXdoHu5dri-S8XpxPoib378h975L8XxlKTqd-NjMEb_E1m3_JEyo7tdAOst35_RHhz1ysEbLYmq1etwvvpFe4yhurPx71Twg-BY6ju0DE9XztQpcjDU5xFeQ89DOYoFxW7fvm-vaoR-0uUNLsZLZhmIHbSuUjuEq_Wu48P0S-yuznS-Flu6s" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- Capabilities: Image Left, Text Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="relative order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
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
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Professional Reporting &amp; Documentation</div>
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
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Reliable OEM &amp; <span class="text-secondary italic">OEM-Equivalent Parts.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Without Supply Delays. Successful operations depend on timely access to quality equipment, spare parts, tools and industrial materials.</p>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap justify-center gap-4 mb-20">
                    <a href="contact.php" class="inline-block bg-secondary text-on-secondary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-secondary/20 hover:scale-105 transition-all">
                        Request Procurement Support
                    </a>
                    <a href="spec-forms.php" class="inline-flex items-center gap-2 border-2 border-secondary/30 text-secondary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:bg-secondary/10 transition-all">
                        <span class="material-symbols-outlined text-lg">upload</span> Submit Material Requirement
                    </a>
                </div>

                <!-- Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel Nigeria Limited delivers structured procurement and international logistics solutions that ensure the efficient supply of genuine OEM and approved OEM-equivalent products while minimizing procurement risks, import delays and operational disruptions.</p>
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">From technical sourcing and supplier verification to international shipping, customs clearance and final delivery, we manage the complete procurement lifecycle with transparency, traceability and accountability.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Client Challenges We Address</h4>
                            <p class="text-sm text-on-surface-variant font-light">Organizations frequently encounter:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Counterfeit or substandard spare parts
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Unverified suppliers and procurement risks
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Import delays affecting project schedules
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Long equipment downtime due to sourcing issues
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Poor procurement visibility and traceability
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> High procurement costs caused by inefficient sourcing
                                </div>
                            </div>
                            <p class="text-sm text-on-surface-variant font-light">Wilsolvewel addresses these challenges through controlled procurement processes, supplier verification, technical review procedures and structured logistics planning.</p>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Procurement Logistics"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- Procurement Capability -->
                <div class="mb-20">
                    <h3 class="font-headline text-3xl font-bold text-on-surface tracking-tight text-center mb-12">Structured Procurement Solutions for <span class="text-secondary">Critical Operations.</span></h3>
                    <p class="text-on-surface-variant font-light text-center max-w-3xl mx-auto mb-12">We provide professional sourcing support for industrial equipment, mechanical &amp; rotating systems, electrical &amp; instrumentation materials, hydraulic systems and specialized hard-to-source materials.</p>
                    <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-3">
                            <span class="material-symbols-outlined text-secondary text-3xl block text-center">precision_manufacturing</span>
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Industrial Equipment &amp; Components</h4>
                            <p class="text-xs text-on-surface-variant font-light">Equipment, assemblies and operational components supporting industrial facilities and critical operations.</p>
                        </div>
                        <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-3">
                            <span class="material-symbols-outlined text-secondary text-3xl block text-center">settings_input_component</span>
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Mechanical &amp; Rotating Systems</h4>
                            <p class="text-xs text-on-surface-variant font-light">Engines, pumps, compressors, gearboxes, rotating equipment and associated components.</p>
                        </div>
                        <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-3">
                            <span class="material-symbols-outlined text-secondary text-3xl block text-center">bolt</span>
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Electrical &amp; Instrumentation</h4>
                            <p class="text-xs text-on-surface-variant font-light">Control systems, instrumentation devices, electrical accessories and industrial automation components.</p>
                        </div>
                        <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-3">
                            <span class="material-symbols-outlined text-secondary text-3xl block text-center">water_drop</span>
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Hydraulic Systems &amp; Accessories</h4>
                            <p class="text-xs text-on-surface-variant font-light">Hydraulic pumps, valves, cylinders, motors, hoses, fittings and associated accessories.</p>
                        </div>
                        <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-3">
                            <span class="material-symbols-outlined text-secondary text-3xl block text-center">search</span>
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Hard-To-Source Materials</h4>
                            <p class="text-xs text-on-surface-variant font-light">Difficult-to-obtain industrial components requiring technical sourcing and supplier verification.</p>
                        </div>
                    </div>
                </div>

                <!-- Brands We Support -->
                <div class="bg-surface-container-lowest p-10 rounded-3xl border border-outline-variant/10 mb-20">
                    <div class="text-center mb-10">
                        <h3 class="font-headline text-2xl font-bold">Our Procurement Capability Covers Spare Parts and Support Equipment for Major Industrial and Heavy-Duty Brands Including:</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <div class="p-6 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Caterpillar</span>
                        </div>
                        <div class="p-6 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Cummins</span>
                        </div>
                        <div class="p-6 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Perkins</span>
                        </div>
                        <div class="p-6 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Terex</span>
                        </div>
                        <div class="p-6 bg-surface-container rounded-xl text-center">
                            <span class="block text-xs font-bold uppercase tracking-widest">Schwing</span>
                        </div>
                    </div>
                </div>

                <!-- Our Procurement Solutions -->
                <div class="grid lg:grid-cols-3 gap-12 mb-20">
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <span class="material-symbols-outlined text-secondary text-3xl">verified</span>
                        <h4 class="font-headline font-bold text-lg">OEM &amp; OEM-Equivalent Sourcing</h4>
                        <p class="text-sm text-on-surface-variant font-light">Supply of genuine OEM and approved OEM-equivalent products from verified manufacturers and authorized distributors.</p>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <span class="material-symbols-outlined text-secondary text-3xl">local_shipping</span>
                        <h4 class="font-headline font-bold text-lg">International Logistics Coordination</h4>
                        <p class="text-sm text-on-surface-variant font-light">Global pickup, freight management, customs clearance and delivery coordination.</p>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <span class="material-symbols-outlined text-secondary text-3xl">fact_check</span>
                        <h4 class="font-headline font-bold text-lg">Supplier Verification &amp; Quality Assurance</h4>
                        <p class="text-sm text-on-surface-variant font-light">Technical evaluation and supplier screening designed to reduce sourcing risks and improve procurement confidence.</p>
                    </div>
                </div>

                <!-- Procurement Process Flow -->
                <div class="p-10 bg-on-surface rounded-3xl text-surface relative overflow-hidden mb-20">
                    <div class="absolute inset-0 opacity-10 technical-grid pointer-events-none"></div>
                    <div class="relative z-10">
                        <h3 class="font-headline text-2xl font-bold text-center mb-12">Our Procurement <span class="text-secondary">Process.</span></h3>
                        <div class="flex flex-wrap justify-center gap-x-8 gap-y-6 text-center">
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">1</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Client Submits Specification</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">2</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Technical Review &amp; Clarification</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">3</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">OEM Identification / Equivalent Sourcing</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">4</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Supplier Verification &amp; Screening</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">5</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Quotation &amp; Lead Time Submission</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">6</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Client Approval</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">7</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Procurement Execution</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">8</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">International Logistics</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-surface/30 flex items-center justify-center mx-auto text-xs font-bold">9</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest">Customs Clearance</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-secondary bg-secondary flex items-center justify-center mx-auto text-xs font-bold text-on-secondary">10</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-secondary">Final Delivery</span>
                            </div>
                            <span class="material-symbols-outlined text-surface/30 self-center">arrow_downward</span>
                            <div class="space-y-2 min-w-[140px]">
                                <span class="w-10 h-10 rounded-full border border-secondary bg-secondary flex items-center justify-center mx-auto text-xs font-bold text-on-secondary">11</span>
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-secondary">Post-Delivery Support</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Solution 1: Hydraulic Pump -->
                <div class="grid lg:grid-cols-12 gap-12 items-center mb-20">
                    <div class="lg:col-span-7 space-y-8">
                        <div class="inline-flex items-center gap-3 text-secondary">
                            <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">water_drop</span>
                            <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Featured Solution</span>
                        </div>
                        <h3 class="font-headline text-3xl md:text-4xl font-bold tracking-tight">Hydraulic Pump Solutions: Supply, <span class="text-secondary">Components &amp; Refurbishment.</span></h3>
                        <p class="text-on-surface-variant font-light leading-relaxed">Reduce Replacement Costs. Restore Performance. Extend Equipment Life. Hydraulic pumps play a critical role in industrial, construction, marine, oilfield and heavy equipment operations.</p>
                        <p class="text-on-surface-variant font-light leading-relaxed">When internal components wear, many organizations immediately consider replacing the entire pump assembly. However, in many cases, professional refurbishment supported by genuine replacement components can restore performance at a significantly lower cost.</p>
                        <p class="text-on-surface-variant font-light leading-relaxed">Wilsolvewel provides structured sourcing and supply of hydraulic pumps, internal components, rebuild kits, and accessories that support cost-effective restoration and long-term reliability.</p>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Genuine Hydraulic Pump Supply
                            </div>
                            <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> OEM-Grade Internal Components
                            </div>
                            <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Rebuild Kits &amp; Accessories
                            </div>
                            <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Refurbishment Support
                            </div>
                            <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Technical Component Identification
                            </div>
                            <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-secondary rounded-full"></span> Global Sourcing &amp; Delivery
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4 pt-4">
                            <a href="contact.php" class="inline-flex items-center gap-2 bg-secondary text-on-secondary px-8 py-3 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:scale-105 transition-all">
                                Learn More <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
                            <a href="spec-forms.php#pump-form" class="inline-flex items-center gap-2 border-2 border-secondary/30 text-secondary px-8 py-3 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:bg-secondary/10 transition-all">
                                Download Pump Specification Form
                            </a>
                        </div>
                    </div>
                    <div class="lg:col-span-5">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Hydraulic Pump"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-outline-variant/20 mb-20"></div>

                <!-- Featured Solution 2: Strategic Global Sourcing -->
                <div class="grid lg:grid-cols-12 gap-12 items-center mb-20">
                    <div class="lg:col-span-5 order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Global Sourcing"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL2icG4PWakb4pmy3ahdG-5OsyndkSQ_XAp34Aja84XNeMxyihqgKc9J740YcrLkU3RAQVAgsEpIO_6s1imD5VcaAE8UyR0RJVzhUZ7yV51fXNAs6ddL-yf-rH-DHSdAiz1l_eIoylGhr1sh1-Pgxah-MIm0nj8Z-aQjynFCM7uNq64WdHjv-fb2wZrY65ZABrfFf_QUsNNFg0wmMyqRygLgC3EsUngMi4TgqUEk6_evRNwwIBhAmw0e76jYtl9CCFH0vk5LcpAUoG" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    <div class="lg:col-span-7 space-y-8 order-1 lg:order-2">
                        <div class="inline-flex items-center gap-3 text-primary">
                            <span class="material-symbols-outlined text-4xl">public</span>
                            <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Global Network</span>
                        </div>
                        <h3 class="font-headline text-3xl md:text-4xl font-bold tracking-tight">Strategic <span class="text-primary">Global Sourcing</span> Solutions.</h3>
                        <p class="text-on-surface-variant font-light leading-relaxed">Access Verified Suppliers Worldwide Through a Structured Sourcing Process. Finding the right supplier is often more difficult than finding the required part.</p>
                        <p class="text-on-surface-variant font-light leading-relaxed">Wilsolvewel helps organizations source specialized equipment, industrial materials, spare parts, and hard-to-find components through a controlled global sourcing network that emphasizes supplier verification, quality assurance, and procurement transparency.</p>
                        <p class="text-on-surface-variant font-light leading-relaxed">Whether the requirement involves a single component or a complete equipment package, our sourcing process is designed to reduce procurement risk while improving supply reliability.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Our General Sourcing Capability Covers:</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/10">
                                    <h5 class="font-headline font-bold text-xs uppercase tracking-widest mb-2 text-primary">Mechanical Equipment &amp; Components</h5>
                                    <p class="text-xs text-on-surface-variant font-light">Engines, Pumps, Compressors, Gearboxes, Rotating Equipment</p>
                                </div>
                                <div class="p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/10">
                                    <h5 class="font-headline font-bold text-xs uppercase tracking-widest mb-2 text-primary">Electrical &amp; Automation Systems</h5>
                                    <p class="text-xs text-on-surface-variant font-light">Control Panels, PLC Systems, Sensors, Instrumentation Devices, Electrical Components</p>
                                </div>
                                <div class="p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/10">
                                    <h5 class="font-headline font-bold text-xs uppercase tracking-widest mb-2 text-primary">Hydraulic Equipment &amp; Accessories</h5>
                                    <p class="text-xs text-on-surface-variant font-light">Hydraulic Pumps, Motors, Cylinders, Valves, Hydraulic Assemblies</p>
                                </div>
                                <div class="p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/10">
                                    <h5 class="font-headline font-bold text-xs uppercase tracking-widest mb-2 text-primary">Industrial Consumables &amp; Maintenance</h5>
                                    <p class="text-xs text-on-surface-variant font-light">Filters, Bearings, Seals, Fasteners, Maintenance Consumables</p>
                                </div>
                                <div class="p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/10 sm:col-span-2">
                                    <h5 class="font-headline font-bold text-xs uppercase tracking-widest mb-2 text-primary">Specialized &amp; Hard-To-Source Equipment</h5>
                                    <p class="text-xs text-on-surface-variant font-light">OEM Spare Parts, Legacy Components, Custom Industrial Materials, Project-Specific Equipment</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4 pt-4">
                            <a href="contact.php" class="inline-flex items-center gap-2 bg-primary text-on-primary px-8 py-3 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:scale-105 transition-all">
                                Learn More <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
                            <a href="spec-forms.php" class="inline-flex items-center gap-2 border-2 border-primary/30 text-primary px-8 py-3 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:bg-primary/10 transition-all">
                                Download Material Specification Submission Form
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Why Clients Choose + CTA -->
                <div class="mt-20 bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">Why Choose Wilsolvewel for Procurement</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Verified Global Suppliers</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> End-to-End Traceability</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Technical Procurement Expertise</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Cost-Controlled Procurement Strategy</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Reliable Logistics Planning</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-secondary">✔</span> Professional Documentation</div>
                            </div>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Need Parts, Equipment or Specialized Materials?</p>
                            <p class="text-surface-bright/50 text-sm font-light">Submit your requirements and let our procurement team handle sourcing, shipping, customs clearance and delivery.</p>
                            <div class="flex flex-col items-center lg:items-end gap-3 pt-2">
                                <a href="contact.php" class="inline-block bg-secondary text-on-secondary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-secondary/20 hover:scale-105 transition-all">
                                    Request Procurement Support
                                </a>
                                <a href="spec-forms.php" class="inline-block border-2 border-surface/30 text-surface-bright px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:bg-surface/10 transition-all">
                                    Submit Specifications
                                </a>
                            </div>
                            <div class="pt-2">
                                <p class="text-surface-bright/40 text-xs font-light">Need a Material or Component Sourced?</p>
                                <a href="spec-forms.php" class="inline-flex items-center gap-2 text-secondary text-sm font-bold uppercase tracking-widest pt-1">
                                    Submit Your Specification <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </a>
                            </div>
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
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Building Reliable Infrastructure for Industrial Operations. Industrial facilities and equipment require properly engineered foundations and infrastructure to achieve safe, reliable and long-term performance.</p>
                </div>

                <!-- Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel delivers industrial civil, structural and construction solutions designed to support heavy equipment installations, industrial facilities and infrastructure projects from concept through execution.</p>
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Our approach combines engineering design, construction expertise and practical project execution to ensure structures perform safely and effectively throughout their operational life.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Challenges We Help Solve</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Weak equipment foundations
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Structural instability
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment vibration issues
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Construction quality concerns
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Project execution delays
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Infrastructure reliability challenges
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Industrial Construction"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgAXGKG4O_fRgqzt1Ov-O4zdKcd1miG_RYprvywVztEDtFxKm6sK36ssUHA0zyOGBE--ETWfnDKawgK9Ep6hf9PEoHK0M07G0GURrfsCDVPbglFwzrBjcJwnAFs3KIK4lcAnjjOt5cQtlFj0qS3lC2kscx1VyGFs9FLPCc7AWjmTqU5xWEwefnm6C2Z3Fe-IPIfZIQuL1R3mDM2Gq0clSfrsiZY0MztT1ydZ5pQwdgpS7uR3msPiDQoxxKQmY6uXp0rxffPZSkrXGz" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- Capabilities: Image Left, Text Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="relative order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover transition-all duration-1000 scale-105 group-hover:scale-100"
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
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Equipment Foundations</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Design and construction of foundations capable of supporting static and dynamic industrial loads.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">grid_on</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Structural Steel Erection</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Installation of structural steel systems supporting industrial facilities and equipment.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">precision_manufacturing</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Reinforced Concrete Structures</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Construction of durable concrete structures designed for industrial applications.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">construction</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Industrial Civil Works</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Civil engineering solutions supporting industrial operations and infrastructure development.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">task_alt</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Construction Project Execution</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Management and execution of construction activities aligned with project requirements and engineering standards.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">draw</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Engineering Design &amp; Documentation</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Preparation of engineering drawings, technical documentation and project support information.</p>
                                </div>
                            </div>
                            <div class="p-6 bg-surface-container-lowest rounded-2xl flex gap-4 items-start border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-2xl mt-0.5">landscape</span>
                                <div>
                                    <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-1">Site Preparation &amp; Infrastructure Development</h4>
                                    <p class="text-xs text-on-surface-variant font-light">Development of facilities and supporting infrastructure required for industrial operations.</p>
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
                            <h3 class="font-headline text-2xl font-bold">Why Clients Choose Our Construction Team</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Engineering-Led Execution</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Safety &amp; Compliance Focus</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Structural Integrity Assurance</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Practical Construction Experience</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Quality Workmanship</div>
                                <div class="flex items-center gap-2 text-sm text-surface-bright/80"><span class="text-primary">✔</span> Professional Project Coordination</div>
                            </div>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Planning an Industrial Construction Project?</p>
                            <a href="contact.php" class="inline-block bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Discuss Your Requirements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FINAL CTA SECTION -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary to-primary/80"></div>
            <div class="absolute inset-0 opacity-10 technical-grid pointer-events-none"></div>
            <div class="max-w-7xl mx-auto text-center relative z-10 space-y-10">
                <span class="text-on-primary/60 font-label text-[10px] font-bold uppercase tracking-[0.4em] block">One Partner. Multiple Solutions. Reliable Results.</span>
                <h2 class="font-headline text-4xl md:text-5xl lg:text-6xl font-bold text-on-primary leading-[0.95] tracking-tighter max-w-4xl mx-auto">
                    Reliable Results Across Every <span class="italic">Operation.</span>
                </h2>
                <p class="text-on-primary/80 font-light text-lg max-w-2xl mx-auto">Whether you require urgent technical support, strategic procurement assistance, international logistics coordination or industrial construction expertise, Wilsolvewel Nigeria Limited is ready to support your objectives.</p>
                <p class="text-on-primary/70 font-light max-w-2xl mx-auto">We combine engineering expertise, procurement capability and practical project execution to help organizations operate safely, efficiently and successfully.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="contact.php" class="bg-on-primary text-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl hover:scale-105 transition-all">
                        Contact Our Team
                    </a>
                    <a href="spec-forms.php" class="border-2 border-on-primary/30 text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:bg-on-primary/10 transition-all">
                        Request a Consultation
                    </a>
                </div>
            </div>
        </section>
    </main>

    <script src="./components/footer.js" data-root="./"></script>
</body>
</html>