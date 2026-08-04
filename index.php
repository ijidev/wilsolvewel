<?php require_once 'config.php'; secure_session_start(); generate_csrf_token();
$conn = get_db_connection();
$showcase_projects = [];
$res = $conn->query("SELECT * FROM showcase_projects WHERE status='Active' ORDER BY sort_order ASC, id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $showcase_projects[] = $row;
}
?><!DOCTYPE html>

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
        <section class="relative min-h-[80vh] flex items-center px-5 sm:px-6 lg:px-12 z-10 overflow-hidden">
            <img class="hero-bg absolute inset-0 w-full h-full object-cover object-center" loading="eager"
                alt="Industrial Engineering Services"
                src="https://www.se.com/ww/en/assets/1024951026/header-1024951026.jpg" />
            <div class="absolute inset-0 bg-gradient-to-r from-surface via-surface/85 to-surface/15"></div>
            <div class="absolute inset-0 opacity-20 technical-grid pointer-events-none"></div>
            <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-12 items-center relative z-10 w-full">
                <div class="lg:col-span-12 xl:col-span-8 space-y-8">
                    <div class="inline-flex items-center gap-3 px-3 py-1 bg-primary/10 rounded-full border border-primary/20">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.2em]">Supporting Industrial Operations Across Nigeria</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-6xl font-bold tracking-tighter text-on-surface leading-[0.95]">
                        Reducing Equipment <span class="text-primary italic">Downtime.</span><br />
                        Delivering <span class="text-secondary">Reliable Engineering Solutions.</span><br />
                    </h2>
                    <p class="text-on-surface-variant text-base md:text-lg max-w-2xl leading-relaxed font-light">
                        When equipment fails, supply chains are disrupted or infrastructure challenges threaten productivity, organizations need a dependable partner that can respond quickly and deliver results.
                    </p>
                    <p class="text-on-surface-variant text-base md:text-lg max-w-2xl leading-relaxed font-light">
                        Wilsolvewel Nigeria Limited provides Technical Support & Maintenance, Procurement & International Logistics and Industrial Civil, Structural & Construction Services that help organizations reduce downtime, protect critical assets and execute projects with confidence across Nigeria.
                    </p>
                    <div class="inline-flex items-center gap-3 px-3 py-1 bg-primary/10 rounded-full border border-primary/20">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.2em]">Technical Support & Maintenance | Procurement & Logistics | Industrial Construction</span>
                    </div>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="contact.php" class="anodized-gradient text-on-primary px-10 py-4 rounded-lg font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:translate-y-[-2px] transition-transform">
                            Request Technical Support
                        </a>
                        <a href="contact.php" class="bg-surface-container-high text-on-surface px-10 py-4 rounded-lg font-headline font-bold text-sm uppercase tracking-widest hover:bg-surface-container-highest transition-all">
                            Talk to an Engineer
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust Section -->
        <section class="py-32 px-5 sm:px-6 lg:px-12 z-10 relative bg-surface-container-low/30">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-4xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">Our Commitment</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Trusted Engineering Support for <span class="text-primary">Critical Operations.</span></h2>
                    <p class="text-on-surface-variant text-base leading-relaxed font-light max-w-3xl mx-auto">Wilsolvewel Nigeria Limited is an indigenous engineering and industrial support company delivering practical solutions to organizations that depend on reliable equipment, efficient procurement systems and robust infrastructure.</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-lg leading-relaxed font-light">We support clients across:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary">local_gas_station</span>
                                <span class="font-headline font-bold text-sm text-on-surface">Oil &amp; Gas</span>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary">bolt</span>
                                <span class="font-headline font-bold text-sm text-on-surface">Power Generation &amp; Utilities</span>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary">precision_manufacturing</span>
                                <span class="font-headline font-bold text-sm text-on-surface">Manufacturing &amp; Industrial Facilities</span>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary">foundation</span>
                                <span class="font-headline font-bold text-sm text-on-surface">Construction &amp; Infrastructure Projects</span>
                            </div>
                            <div class="flex items-center gap-3 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/10 sm:col-span-2">
                                <span class="material-symbols-outlined text-primary">account_balance</span>
                                <span class="font-headline font-bold text-sm text-on-surface">Government &amp; Corporate Facilities</span>
                            </div>
                        </div>
                        <p class="text-on-surface-variant text-base leading-relaxed font-light pt-4">From emergency equipment support to complex procurement and industrial construction projects, we help organizations maintain operational continuity and achieve project success.</p>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Trusted Engineering Support"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9OxK7bvEeBHiB4IiD08woFatMHovl7-Mrrn2nVScQbp2TSyCXI-o0CTKd_wCTcm4Z5eTu7p4EIzDhsJZ76ptcJu1U4nRYG4STYB1gA1sG9Sc7w3jDbhMgICS838aIhHwIh_eVvoDmx4Bns1MkrwcqCKiq7yeS1Mt9sAngeckaWjVMqc2OGhh4cwx56PQK-8mtYSC_CfaMB7m1O9b5lKk2mKF6zFungAuDwRy0UdMo_o-fMNcPiWpHVRWQDzZQRohfco4zwbYxyD1s" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Operational Challenges We Help Solve -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-4xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">What We Address</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Operational Challenges We <span class="text-primary">Help Solve.</span></h2>
                    <p class="text-on-surface-variant text-base leading-relaxed font-light max-w-3xl mx-auto">Industrial operations face challenges that can significantly impact productivity, safety, and profitability. Wilsolvewel helps clients overcome:</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Card 1 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">power_off</span>
                        </div>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-3 tracking-tight">Equipment Breakdowns</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">
                            Unexpected failures that result in costly downtime, lost production and project delays.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-secondary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-secondary group-hover:text-on-secondary">schedule</span>
                        </div>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-3 tracking-tight">Procurement Delays</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">
                            Long lead times, unreliable suppliers and logistics bottlenecks that disrupt maintenance schedules and project execution.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">gpp_bad</span>
                        </div>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-3 tracking-tight">Counterfeit &amp; Substandard Components</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">
                            Poor-quality parts that increase maintenance costs, reduce equipment reliability and expose operations to unnecessary risk.
                        </p>
                    </div>

                    <!-- Card 4 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-secondary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-secondary group-hover:text-on-secondary">warning</span>
                        </div>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-3 tracking-tight">Infrastructure &amp; Foundation Failures</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">
                            Poorly designed or executed civil works that compromise equipment performance, structural integrity and operational safety.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Services -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative bg-surface-container-low/30">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-4xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">What We Deliver</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Our <span class="text-primary">Services.</span></h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Service 1: Technical Support & Maintenance -->
                    <div class="group bg-surface-container-lowest rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden">
                        <div class="h-48 bg-surface-container-high relative overflow-hidden">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105"
                                alt="Technical Support & Maintenance"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                            <div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest via-surface-container-lowest/20 to-transparent"></div>
                        </div>
                        <div class="p-8 flex flex-col flex-grow">
                            <h3 class="font-headline text-2xl font-bold text-on-surface mb-2 tracking-tight">Technical Support &amp; Maintenance</h3>
                            <p class="text-primary font-headline font-bold text-xs uppercase tracking-widest mb-4">Keeping Critical Equipment Operating at Peak Performance</p>
                            <p class="text-on-surface-variant text-sm mb-6 leading-relaxed">
                                Equipment reliability is essential to operational success. Our technical support team delivers rapid diagnostics, maintenance solutions, and specialized engineering support designed to restore performance, extend equipment life, and minimize downtime.
                            </p>
                            <p class="text-on-surface font-headline font-bold text-xs uppercase tracking-widest mb-4">Key Services</p>
                            <ul class="space-y-2 mb-8 flex-grow">
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Diagnostic &amp; Troubleshooting Services
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Emergency &amp; Corrective Maintenance
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Engine Repair &amp; Overhaul
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Alternator &amp; Electric Motor Reconditioning
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Power Plant Technical Support
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Heavy Equipment &amp; Earthmoving Machinery Support
                                </li>
                            </ul>
                            <a href="services/technical-support.php" class="text-primary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                                Learn More <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <!-- Service 2: Procurement & International Logistics -->
                    <div class="group bg-surface-container-lowest rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden">
                        <div class="h-48 bg-surface-container-high relative overflow-hidden">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105"
                                alt="Procurement & International Logistics"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL2icG4PWakb4pmy3ahdG-5OsyndkSQ_XAp34Aja84XNeMxyihqgKc9J740YcrLkU3RAQVAgsEpIO_6s1imD5VcaAE8UyR0RJVzhUZ7yV51fXNAs6ddL-yf-rH-DHSdAiz1l_eIoylGhr1sh1-Pgxah-MIm0nj8Z-aQjynFCM7uNq64WdHjv-fb2wZrY65ZABrfFf_QUsNNFg0wmMyqRygLgC3EsUngMi4TgqUEk6_evRNwwIBhAmw0e76jYtl9CCFH0vk5LcpAUoG" />
                            <div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest via-surface-container-lowest/20 to-transparent"></div>
                        </div>
                        <div class="p-8 flex flex-col flex-grow">
                            <h3 class="font-headline text-2xl font-bold text-on-surface mb-2 tracking-tight">Procurement &amp; International Logistics</h3>
                            <p class="text-secondary font-headline font-bold text-xs uppercase tracking-widest mb-4">Source the Right Components. Deliver Them on Time.</p>
                            <p class="text-on-surface-variant text-sm mb-6 leading-relaxed">
                                Reliable procurement is critical to operational continuity. We help organizations source genuine OEM and approved OEM-equivalent materials through verified suppliers while managing international logistics from sourcing to final delivery.
                            </p>
                            <p class="text-on-surface font-headline font-bold text-xs uppercase tracking-widest mb-4">Key Services</p>
                            <ul class="space-y-2 mb-8 flex-grow">
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-secondary rounded-full flex-shrink-0"></span> OEM &amp; OEM-Equivalent Procurement
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-secondary rounded-full flex-shrink-0"></span> Strategic Global Sourcing
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-secondary rounded-full flex-shrink-0"></span> Supplier Verification &amp; Quality Assurance
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-secondary rounded-full flex-shrink-0"></span> International Freight Coordination
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-secondary rounded-full flex-shrink-0"></span> Customs Clearance &amp; Documentation
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-secondary rounded-full flex-shrink-0"></span> Delivery Management
                                </li>
                            </ul>
                            <a href="services/procurement.php" class="text-secondary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                                Learn More <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <!-- Service 3: Industrial Civil, Structural & Construction -->
                    <div class="group bg-surface-container-lowest rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden">
                        <div class="h-48 bg-surface-container-high relative overflow-hidden">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105"
                                alt="Industrial Construction Services"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjl9eVb8XrEFxh0iW_kBJ_0dcnn3laux3ZDkXr7fGycCo30F5khhyyNnrodhk0WaVuXCqQOuiUmvx8599xERC4FYQOAwViCSHlC-SxjdLZ_g0isD4hDnop8ClLgDFLTdWrBo8h19SFeURf_NAQovQrUy40JwF_foBE9myeGhjMeDuS5CpDyfWKz0SXRAgtjAHo7RO0GDfOKM6LQ-QjaeOsSSBWIyeZNldqQeWvCV295VFECBSV0yyjE0uAbXg-yKljf5SzDo6MkCnI" />
                            <div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest via-surface-container-lowest/20 to-transparent"></div>
                        </div>
                        <div class="p-8 flex flex-col flex-grow">
                            <h3 class="font-headline text-2xl font-bold text-on-surface mb-2 tracking-tight">Industrial Civil, Structural &amp; Construction Services</h3>
                            <p class="text-primary font-headline font-bold text-xs uppercase tracking-widest mb-4">Building the Foundations Behind Reliable Operations</p>
                            <p class="text-on-surface-variant text-sm mb-6 leading-relaxed">
                                Industrial facilities and heavy equipment require properly engineered infrastructure to operate safely and efficiently. We provide engineering-driven civil, structural, and construction solutions that support industrial growth and long-term asset reliability.
                            </p>
                            <p class="text-on-surface font-headline font-bold text-xs uppercase tracking-widest mb-4">Key Services</p>
                            <ul class="space-y-2 mb-8 flex-grow">
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Equipment Foundations
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Structural Steel Erection
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Reinforced Concrete Structures
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Industrial Civil Works
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Construction Projects
                                </li>
                                <li class="flex items-center gap-3 text-xs text-on-surface-variant">
                                    <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Engineering Design &amp; Documentation
                                </li>
                            </ul>
                            <a href="services/engineering.php" class="text-primary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                                Learn More <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Organizations Choose Wilsolvewel -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">Our Advantage</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Why Organizations Choose <span class="text-primary">Wilsolvewel.</span></h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">bolt</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Fast Response</h4>
                        <p class="text-xs text-on-surface-variant font-light">Rapid mobilization and technical support to minimize downtime and operational disruption.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">engineering</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Engineering-Led Solutions</h4>
                        <p class="text-xs text-on-surface-variant font-light">Practical solutions based on sound engineering principles and real-world industrial experience.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Verified Global Procurement Network</h4>
                        <p class="text-xs text-on-surface-variant font-light">Access to trusted manufacturers, authorized distributors and reliable international logistics partners.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">groups</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Skilled Technical Personnel</h4>
                        <p class="text-xs text-on-surface-variant font-light">Experienced engineers, technicians and project support professionals dedicated to delivering results.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">security</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Safety &amp; Compliance Focus</h4>
                        <p class="text-xs text-on-surface-variant font-light">Execution aligned with industry standards, HSE requirements and quality assurance principles.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">handshake</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">End-to-End Support</h4>
                        <p class="text-xs text-on-surface-variant font-light">Technical support, procurement, logistics and industrial construction services delivered through a single trusted partner.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Experience -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative bg-surface-container-low/30">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                    <div class="max-w-2xl">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">Proven Performance</span>
                        <h2 class="font-headline text-4xl font-bold text-on-surface tracking-tight">Featured <span class="text-primary">Experience.</span></h2>
                        <p class="text-on-surface-variant text-base font-light mt-4">Wilsolvewel has supported organizations across multiple industries through engineering, procurement and technical service delivery.</p>
                    </div>
                    <a href="projects.php" class="text-on-surface-variant font-headline font-bold uppercase tracking-widest text-[10px] border-b border-on-surface-variant pb-1 hover:text-primary hover:border-primary transition-colors">View Projects <span class="material-symbols-outlined text-sm align-middle ml-1">arrow_forward</span></a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php if (empty($showcase_projects)): ?>
                    <!-- Static fallback projects when no DB projects exist -->
                    <!-- Project 1 -->
                    <div class="group space-y-6">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-surface-container-high relative border border-outline-variant/10">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 grayscale hover:grayscale-0"
                                alt="Gas Turbine Commissioning"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                        </div>
                        <div class="space-y-2 px-2">
                            <span class="text-[10px] font-label text-primary font-bold uppercase tracking-widest">Commissioning</span>
                            <h3 class="font-headline text-xl font-bold text-on-surface">Gas Turbine Commissioning Support</h3>
                            <p class="text-on-surface-variant text-sm font-light">Supporting the successful commissioning of critical power generation equipment.</p>
                        </div>
                    </div>

                    <!-- Project 2 -->
                    <div class="group space-y-6">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-surface-container-high relative border border-outline-variant/10">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 grayscale hover:grayscale-0"
                                alt="Crude Oil Pump Installation"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL2icG4PWakb4pmy3ahdG-5OsyndkSQ_XAp34Aja84XNeMxyihqgKc9J740YcrLkU3RAQVAgsEpIO_6s1imD5VcaAE8UyR0RJVzhUZ7yV51fXNAs6ddL-yf-rH-DHSdAiz1l_eIoylGhr1sh1-Pgxah-MIm0nj8Z-aQjynFCM7uNq64WdHjv-fb2wZrY65ZABrfFf_QUsNNFg0wmMyqRygLgC3EsUngMi4TgqUEk6_evRNwwIBhAmw0e76jYtl9CCFH0vk5LcpAUoG" />
                        </div>
                        <div class="space-y-2 px-2">
                            <span class="text-[10px] font-label text-primary font-bold uppercase tracking-widest">Installation</span>
                            <h3 class="font-headline text-xl font-bold text-on-surface">Export Crude Oil Pump Installation Support</h3>
                            <p class="text-on-surface-variant text-sm font-light">Installation and testing support for crude oil export infrastructure.</p>
                        </div>
                    </div>

                    <!-- Project 3 -->
                    <div class="group space-y-6">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-surface-container-high relative border border-outline-variant/10">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 grayscale hover:grayscale-0"
                                alt="Tower Crane Rehabilitation"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjl9eVb8XrEFxh0iW_kBJ_0dcnn3laux3ZDkXr7fGycCo30F5khhyyNnrodhk0WaVuXCqQOuiUmvx8599xERC4FYQOAwViCSHlC-SxjdLZ_g0isD4hDnop8ClLgDFLTdWrBo8h19SFeURf_NAQovQrUy40JwF_foBE9myeGhjMeDuS5CpDyfWKz0SXRAgtjAHo7RO0GDfOKM6LQ-QjaeOsSSBWIyeZNldqQeWvCV295VFECBSV0yyjE0uAbXg-yKljf5SzDo6MkCnI" />
                        </div>
                        <div class="space-y-2 px-2">
                            <span class="text-[10px] font-label text-primary font-bold uppercase tracking-widest">Maintenance</span>
                            <h3 class="font-headline text-xl font-bold text-on-surface">Tower Crane Rehabilitation</h3>
                            <p class="text-on-surface-variant text-sm font-light">Maintenance and restoration of heavy lifting equipment for major construction projects.</p>
                        </div>
                    </div>

                    <!-- Project 4 -->
                    <div class="group flex bg-surface-container-lowest rounded-2xl border border-outline-variant/10 overflow-hidden shadow-sm hover:shadow-lg transition-all">
                        <div class="w-1/3 min-h-[160px] bg-surface-container-high relative overflow-hidden">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700"
                                alt="Equipment Maintenance"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9OxK7bvEeBHiB4IiD08woFatMHovl7-Mrrn2nVScQbp2TSyCXI-o0CTKd_wCTcm4Z5eTu7p4EIzDhsJZ76ptcJu1U4nRYG4STYB1gA1sG9Sc7w3jDbhMgICS838aIhHwIh_eVvoDmx4Bns1MkrwcqCKiq7yeS1Mt9sAngeckaWjVMqc2OGhh4cwx56PQK-8mtYSC_CfaMB7m1O9b5lKk2mKF6zFungAuDwRy0UdMo_o-fMNcPiWpHVRWQDzZQRohfco4zwbYxyD1s" />
                        </div>
                        <div class="p-6 flex flex-col justify-center space-y-2 w-2/3">
                            <span class="text-[10px] font-label text-primary font-bold uppercase tracking-widest">Technical Support</span>
                            <h3 class="font-headline text-lg font-bold text-on-surface">Equipment Maintenance &amp; Technical Consultancy</h3>
                            <p class="text-on-surface-variant text-sm font-light">Technical support services for industrial and corporate facilities.</p>
                        </div>
                    </div>

                    <!-- Project 5 -->
                    <div class="group flex bg-surface-container-lowest rounded-2xl border border-outline-variant/10 overflow-hidden shadow-sm hover:shadow-lg transition-all">
                        <div class="w-1/3 min-h-[160px] bg-surface-container-high relative overflow-hidden">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700"
                                alt="Solar Borehole Project"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjl9eVb8XrEFxh0iW_kBJ_0dcnn3laux3ZDkXr7fGycCo30F5khhyyNnrodhk0WaVuXCqQOuiUmvx8599xERC4FYQOAwViCSHlC-SxjdLZ_g0isD4hDnop8ClLgDFLTdWrBo8h19SFeURf_NAQovQrUy40JwF_foBE9myeGhjMeDuS5CpDyfWKz0SXRAgtjAHo7RO0GDfOKM6LQ-QjaeOsSSBWIyeZNldqQeWvCV295VFECBSV0yyjE0uAbXg-yKljf5SzDo6MkCnI" />
                        </div>
                        <div class="p-6 flex flex-col justify-center space-y-2 w-2/3">
                            <span class="text-[10px] font-label text-primary font-bold uppercase tracking-widest">Infrastructure</span>
                            <h3 class="font-headline text-lg font-bold text-on-surface">Solar Borehole Infrastructure Project</h3>
                            <p class="text-on-surface-variant text-sm font-light">Supporting sustainable water infrastructure development.</p>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($showcase_projects as $project): ?>
                    <div class="group space-y-6">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-surface-container-high relative border border-outline-variant/10">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 grayscale hover:grayscale-0"
                                alt="<?= htmlspecialchars($project['title']) ?>"
                                src="<?= htmlspecialchars($project['image_url'] ?: 'https://placehold.co/600x450/1e293b/64748b?text=Project') ?>" />
                        </div>
                        <div class="space-y-2 px-2">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-label text-primary font-bold uppercase tracking-widest"><?= htmlspecialchars($project['category'] ?: 'Project') ?></span>
                                <span class="text-[10px] font-label text-outline font-bold uppercase tracking-widest"><?= htmlspecialchars($project['year'] ?: '') ?></span>
                            </div>
                            <h3 class="font-headline text-xl font-bold text-on-surface"><?= htmlspecialchars($project['title']) ?></h3>
                            <p class="text-on-surface-variant text-sm font-light"><?= htmlspecialchars($project['description'] ?: $project['client_name']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-6xl mx-auto anodized-gradient rounded-3xl p-12 lg:p-24 text-center text-on-primary shadow-3xl relative overflow-hidden">
                <div class="absolute top-0 right-0 max-w-full w-64 h-64 sm:w-96 sm:h-96 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-2xl translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative z-10 space-y-8">
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold tracking-tighter leading-none">Ready to Improve Reliability,<br />Reduce Downtime and Deliver Your<br />Next Project Successfully?</h2>
                    <p class="text-lg text-primary-fixed/80 max-w-3xl mx-auto font-light leading-relaxed">Whether you require urgent technical support, reliable procurement solutions, international logistics coordination or industrial construction services, Wilsolvewel is ready to support your operations.</p>
                    <p class="text-base text-primary-fixed/70 max-w-2xl mx-auto font-light leading-relaxed">Partner with a team committed to delivering safe, practical and reliable engineering solutions that keep your business moving forward.</p>
                    
                    <div class="flex flex-wrap justify-center gap-6 pt-6">
                        <a href="contact.php" class="bg-white text-primary px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-[0.2em] shadow-2xl hover:scale-105 transition-all">
                            Contact Us Today
                        </a>
                        <a href="contact.php" class="bg-transparent border-2 border-white/40 text-white px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-[0.2em] hover:bg-white/10 transition-all">
                            Request a Consultation
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer Shell -->
    <script src="./components/footer.js" data-root="./"></script>
</body>

</html>