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

        <!-- Hero Banner: Industrial Collage Mosaic -->
        <section class="relative z-10 overflow-hidden">
            <!-- Mosaic Grid Background -->
            <div class="absolute inset-0 grid grid-cols-2 md:grid-cols-4 grid-rows-2 gap-1 opacity-40">
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Oil & Gas Facility"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAKRUKt2u4eQPwM5w4z9gGURbnJPrFuz8nMjrxbys4FUonFuUEbjXW83ceyKEvI1GPigw0NEwCom-2-ZaErJY-Pt8xNGFUHkwWx5_yzW3eXMDBYxTeJEYeKLyDvtvtvbjZFsdA3TZJhHwJ_8e1JLliQ4V0ztxpvAwc9ou-JY3a9eAOYPF2-yHpv2CGyxkPivdzbeHONGgkh_I4ff9OweyMi8s84412nyqppvRRiI2Q-50ZkJZGpI3zfcH38yAdfGWDps4Abi_Cmk-76" />
                </div>
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Power Plant"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjl9eVb8XrEFxh0iW_kBJ_0dcnn3laux3ZDkXr7fGycCo30F5khhyyNnrodhk0WaVuXCqQOuiUmvx8599xERC4FYQOAwViCSHlC-SxjdLZ_g0isD4hDnop8ClLgDFLTdWrBo8h19SFeURf_NAQovQrUy40JwF_foBE9myeGhjMeDuS5CpDyfWKz0SXRAgtjAHo7RO0GDfOKM6LQ-QjaeOsSSBWIyeZNldqQeWvCV295VFECBSV0yyjE0uAbXg-yKljf5SzDo6MkCnI" />
                </div>
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Manufacturing Facility"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfFr582YiArBIQjEPMH_T-xgEh4j0HJAhiW6tqr1Fr8NiEH-CZ9sDVC0AFApM9RADHB9Fz7CgSJyWw0AOLc8HxBziJA79bKlJT11hYANSLCsTZ6qzQFYfq5Z7lSYPNQEyMrsWyBwIovdqtEX6kQJJ-Jc9NjDCqzZw0iWdmSwfnwNcxAeRx3PSwbuRyhHu7F32F8liwvrgRAknDLyMTU0EyPHxkD9N4C3fQnFmSRCHE6aDXEEwdFZK34QaFSHStCMPW31bYqzJl0gLo" />
                </div>
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Construction Site"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgAXGKG4O_fRgqzt1Ov-O4zdKcd1miG_RYprvywVztEDtFxKm6sK36ssUHA0zyOGBE--ETWfnDKawgK9Ep6hf9PEoHK0M07G0GURrfsCDVPbglFwzrBjcJwnAFs3KIK4lcAnjjOt5cQtlFj0qS3lC2kscx1VyGFs9FLPCc7AWjmTqU5xWEwefnm6C2Z3Fe-IPIfZIQuL1R3mDM2Gq0clSfrsiZY0MztT1ydZ5pQwdgpS7uR3msPiDQoxxKQmY6uXp0rxffPZSkrXGz" />
                </div>
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Gas Turbine"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                </div>
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Industrial Equipment"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCD17Sy51HJsUf63BByvgnnpMfg5yeqdoC3ixi4nP7Z6LyxVCxK5YlP8CszalT0CLIcJHV_IuwrmDpO0gG6eRKZzZozHXdoHu5dri-S8XpxPoib378h975L8XxlKTqd-NjMEb_E1m3_JEyo7tdAOst35_RHhz1ysEbLYmq1etwvvpFe4yhurPx71Twg-BY6ju0DE9XztQpcjDU5xFeQ89DOYoFxW7fvm-vaoR-0uUNLsZLZhmIHbSuUjuEq_Wu48P0S-yuznS-Flu6s" />
                </div>
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Engine Overhaul"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBVgwZ6-ASDZ4F6zjwKc3HQtRiQuYcbJ7n-Y_wylgzjfQJAhwLhb4J_OEhuZ5XvY2x2zJKDQIF6ehkN73MpnB8l9YHNlHGW1bfSZTjm4uRjQqYeTdGTnAEf1CVF6W0uphCOSj9v2Ef5_Z--C1z6DPobwDVHhVwgR9gUj43_8SelTxvQcU1QXkOg49IxcbOiTYm9xvkiLbPbzm4ZXq0JbxeVnPtsyo5ByFqr7Irw4t9hMhRwleprFvTcaE_OOFScUFF-mrr39D5wagVp" />
                </div>
                <div class="relative overflow-hidden">
                    <img class="w-full h-full object-cover grayscale scale-110" alt="Corporate Facility"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCskSpUXDG8TBndO5FBg2r8tQZX_zvEqYyw-7RmWeQJNhsmfrTtt1MYXVqOls9Yz33Z0_GnU4z0S-XODI1H6OEddlW9rRN0J7urSAVgvGbhwkJ03Oo5NqOLG9Mb88APXWwFStGuQ6ggHtywPZV8Ue_upteOvfshCQjqMHduoNHkGKqWnnkJj26frE5qmmW1GKk1xGsKisOtHoOHzqXYxJYedetTyCKV9pgusUMRyAqSwHaeKUj6seCheJFle-kCXsvVL1YkPulae1Dw" />
                </div>
            </div>
            <!-- Dark Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-black/80 via-black/60 to-black/40"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
            <!-- Content -->
            <div class="relative z-10 py-32 px-5 sm:px-6 lg:px-12">
                <div class="max-w-7xl mx-auto">
                    <div class="max-w-3xl space-y-8">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Sector Expertise</span>
                        <h1 class="font-headline text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-[0.95] tracking-tighter">
                            Industries <span class="text-primary italic">We Serve.</span>
                        </h1>
                        <p class="text-white/70 font-light text-lg leading-relaxed max-w-2xl">
                            Delivering engineering support, procurement solutions, technical expertise and industrial construction services across critical sectors that power economic growth and industrial development.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Intro Section -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Supporting Industries That Depend on Reliability</span>
                    <div class="space-y-6 text-on-surface-variant text-base font-light leading-relaxed">
                        <p>Every industry faces unique operational challenges. From equipment reliability and procurement risks to infrastructure development and project execution, organizations require dependable partners capable of delivering practical solutions.</p>
                        <p>Wilsolvewel Nigeria Limited supports clients across multiple sectors by combining technical expertise, procurement capability, international logistics coordination and industrial construction services that improve operational performance and reduce business risk.</p>
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

        <!-- Industry Summary Cards -->
        <section class="pb-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8">

                <!-- Card 1: Oil & Gas (Large) -->
                <div id="card-oil-gas" class="md:col-span-8 group relative overflow-hidden bg-surface-container-low rounded-3xl min-h-[500px] border border-outline-variant/10 flex flex-col justify-end p-12">
                    <div class="absolute inset-0 z-0">
                        <img class="w-full h-full object-cover grayscale transition-all duration-1000 group-hover:scale-105 group-hover:grayscale-0 opacity-60"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAKRUKt2u4eQPwM5w4z9gGURbnJPrFuz8nMjrxbys4FUonFuUEbjXW83ceyKEvI1GPigw0NEwCom-2-ZaErJY-Pt8xNGFUHkwWx5_yzW3eXMDBYxTeJEYeKLyDvtvtvbjZFsdA3TZJhHwJ_8e1JLliQ4V0ztxpvAwc9ou-JY3a9eAOYPF2-yHpv2CGyxkPivdzbeHONGgkh_I4ff9OweyMi8s84412nyqppvRRiI2Q-50ZkJZGpI3zfcH38yAdfGWDps4Abi_Cmk-76" alt="Oil & Gas" />
                        <div class="absolute inset-0 bg-gradient-to-t from-surface-container-low via-surface-container-low/20 to-transparent"></div>
                    </div>
                    <div class="relative z-10 space-y-4">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em]">Sector 01</span>
                        <h2 class="font-headline text-4xl font-bold tracking-tight">Oil &amp; Gas</h2>
                        <p class="text-on-surface-variant max-w-xl font-light">Supporting upstream, midstream and downstream operations with technical support, procurement solutions, equipment maintenance and industrial construction services that enhance reliability and operational continuity.</p>
                        <div class="flex flex-wrap gap-3 pt-4">
                            <span class="px-4 py-2 bg-white/5 rounded-full text-[9px] font-bold uppercase tracking-widest border border-white/10">Foundation Works</span>
                            <span class="px-4 py-2 bg-white/5 rounded-full text-[9px] font-bold uppercase tracking-widest border border-white/10">OEM Sourcing</span>
                            <span class="px-4 py-2 bg-white/5 rounded-full text-[9px] font-bold uppercase tracking-widest border border-white/10">Emergency Maint</span>
                        </div>
                        <a href="#oil-gas" class="inline-flex items-center gap-2 text-primary font-headline font-bold text-sm uppercase tracking-widest pt-4 group/link">
                            Learn More <span class="material-symbols-outlined group-hover/link:translate-x-1 transition-transform text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Card 2: Power Generation -->
                <div id="card-power" class="md:col-span-4 bg-on-surface text-surface rounded-3xl p-12 flex flex-col justify-between border-t-4 border-primary shadow-2xl">
                    <div class="space-y-6">
                        <span class="material-symbols-outlined text-primary text-5xl">bolt</span>
                        <h2 class="font-headline text-3xl font-bold tracking-tight">Power <br />Generation</h2>
                        <p class="text-surface-bright/60 font-light text-sm leading-relaxed">Helping power facilities maximize equipment availability, improve operational efficiency and maintain dependable energy production through engineering support and technical services.</p>
                    </div>
                    <a href="#power" class="flex items-center gap-2 text-primary font-label text-[10px] font-bold uppercase tracking-[0.2em] group/link">
                        Learn More <span class="material-symbols-outlined group-hover/link:translate-x-1 transition-transform text-sm">arrow_forward</span>
                    </a>
                </div>

                <!-- Card 3: Manufacturing -->
                <div id="card-manufacturing" class="md:col-span-4 group bg-surface-container-lowest rounded-3xl p-10 border border-outline-variant/10 space-y-8">
                    <div class="aspect-video rounded-2xl overflow-hidden grayscale group-hover:grayscale-0 transition-all duration-700">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfFr582YiArBIQjEPMH_T-xgEh4j0HJAhiW6tqr1Fr8NiEH-CZ9sDVC0AFApM9RADHB9Fz7CgSJyWw0AOLc8HxBziJA79bKlJT11hYANSLCsTZ6qzQFYfq5Z7lSYPNQEyMrsWyBwIovdqtEX6kQJJ-Jc9NjDCqzZw0iWdmSwfnwNcxAeRx3PSwbuRyhHu7F32F8liwvrgRAknDLyMTU0EyPHxkD9N4C3fQnFmSRCHE6aDXEEwdFZK34QaFSHStCMPW31bYqzJl0gLo" class="w-full h-full object-cover" alt="Manufacturing" />
                    </div>
                    <div class="space-y-4">
                        <h2 class="font-headline text-2xl font-bold">Manufacturing</h2>
                        <p class="text-on-surface-variant font-light text-sm">Supporting industrial plants with maintenance services, procurement solutions, equipment support and infrastructure development that improve productivity and operational reliability.</p>
                    </div>
                    <a href="#manufacturing" class="inline-flex items-center gap-2 text-primary font-headline font-bold text-sm uppercase tracking-widest group/link">
                        Learn More <span class="material-symbols-outlined group-hover/link:translate-x-1 transition-transform text-sm">arrow_forward</span>
                    </a>
                </div>

                <!-- Card 4: Construction -->
                <div id="card-construction" class="md:col-span-4 group bg-surface-container-low rounded-3xl p-10 border border-outline-variant/10 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 opacity-5 scale-150 rotate-12 group-hover:rotate-0 transition-transform duration-1000">
                        <span class="material-symbols-outlined text-[200px]">architecture</span>
                    </div>
                    <div class="relative z-10 space-y-6">
                        <span class="material-symbols-outlined text-primary text-4xl">foundation</span>
                        <h2 class="font-headline text-2xl font-bold">Construction &amp; Infrastructure</h2>
                        <p class="text-on-surface-variant font-light text-sm">Providing engineering support, equipment solutions, procurement services and industrial construction expertise that help projects achieve successful outcomes.</p>
                    </div>
                    <a href="#construction" class="relative z-10 inline-flex items-center gap-2 text-primary font-headline font-bold text-sm uppercase tracking-widest pt-8 border-t border-outline-variant/20 group/link">
                        Learn More <span class="material-symbols-outlined group-hover/link:translate-x-1 transition-transform text-sm">arrow_forward</span>
                    </a>
                </div>

                <!-- Card 5: Government & Corporate -->
                <div id="card-government" class="md:col-span-6 anodized-gradient text-on-primary rounded-3xl p-10 shadow-xl shadow-primary/20 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="flex justify-between items-start">
                            <span class="material-symbols-outlined text-4xl text-white">account_balance</span>
                            <span class="px-3 py-1 bg-white/10 rounded-full text-[8px] font-bold uppercase tracking-widest">Compliant</span>
                        </div>
                        <h2 class="font-headline text-3xl font-bold tracking-tight">Government &amp; Corporate</h2>
                        <p class="text-primary-fixed/70 font-light text-sm">Providing engineering support, maintenance services, procurement solutions and infrastructure improvements that enhance operational efficiency and facility performance.</p>
                    </div>
                    <a href="#government" class="inline-flex items-center gap-2 text-on-primary font-label text-[10px] font-bold uppercase tracking-[0.2em] pt-8 mt-8 border-t border-white/10 group/link">
                        Learn More <span class="material-symbols-outlined group-hover/link:translate-x-1 transition-transform text-sm">arrow_forward</span>
                    </a>
                </div>

                <!-- Card 6: Engineering Excellence (Side Image) -->
                <div class="md:col-span-6 group relative overflow-hidden rounded-3xl min-h-[300px] border border-outline-variant/10">
                    <div class="absolute inset-0 z-0">
                        <img class="w-full h-full object-cover grayscale transition-all duration-1000 group-hover:scale-105 group-hover:grayscale-0 opacity-70"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAoiC3Rxm03OmGzT8s4cxzzD6CqTr49EN7zUgr4MdewbZ32bTu6oje81NkZmBg7Bn7g2eG3JoX0DO-Sjt1jOe0wskEIBSVSKc2v-7oe8km3bCT1X7M1WI70k2zDsYIz9ote_CD2PdoH4NcJCBXNppqqeEM431VDKvb7QNYPkgy7UjOaaQ-bE3bjZow8e06SQ_e49JeuLyHQALD33DzNCGohdL87_kSsXg6g0ZQaF0jnWw0oeeQ_xhPYyU83RJ0NLKAj2EQkIwqCqjuF" alt="Engineering Excellence" />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
                    </div>
                    <div class="relative z-10 p-10 flex flex-col justify-between h-full min-h-[300px]">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em]">Engineering Excellence</span>
                        <div class="space-y-2">
                            <h3 class="font-headline text-2xl font-bold text-white">One Partner. <br />Multiple Solutions.</h3>
                            <p class="text-white/60 text-sm font-light">Technical expertise across every critical sector.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- EXPANDED INDUSTRY SECTIONS -->

        <!-- INDUSTRY 1: Oil & Gas -->
        <section id="oil-gas" class="py-32 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-primary mb-4">
                        <span class="material-symbols-outlined text-4xl">local_fire_department</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Sector 01</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Oil &amp; Gas <span class="text-primary italic">Operations.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Engineering Support for Critical Oil &amp; Gas Operations. The Oil &amp; Gas industry operates in demanding environments where equipment reliability, safety and operational continuity are essential to productivity and profitability.</p>
                </div>

                <!-- Alternating Layout: Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel Nigeria Limited provides specialized support services designed to help operators, contractors and service companies maintain critical assets, reduce downtime, and execute projects safely and efficiently.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Our Solutions for Oil &amp; Gas</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment Diagnostics &amp; Troubleshooting
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Rotating Equipment Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Power Generation Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Industrial Procurement
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> International Logistics Coordination
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment Installation Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Civil &amp; Construction Works
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment Foundations
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Technical Manpower Supply
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Oil & Gas Facility"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAKRUKt2u4eQPwM5w4z9gGURbnJPrFuz8nMjrxbys4FUonFuUEbjXW83ceyKEvI1GPigw0NEwCom-2-ZaErJY-Pt8xNGFUHkwWx5_yzW3eXMDBYxTeJEYeKLyDvtvtvbjZFsdA3TZJhHwJ_8e1JLliQ4V0ztxpvAwc9ou-JY3a9eAOYPF2-yHpv2CGyxkPivdzbeHONGgkh_I4ff9OweyMi8s84412nyqppvRRiI2Q-50ZkJZGpI3zfcH38yAdfGWDps4Abi_Cmk-76" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- How We Deliver Value -->
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">How We Deliver Value</h3>
                            <p class="text-surface-bright/70 font-light leading-relaxed">Our team helps clients minimize operational disruptions, improve equipment reliability, maintain project schedules and support safe field operations through practical engineering and technical solutions.</p>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Need Oil &amp; Gas Support?</p>
                            <a href="contact.php" class="inline-block bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Contact Our Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- INDUSTRY 2: Power Generation & Utilities -->
        <section id="power" class="py-32 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-secondary mb-4">
                        <span class="material-symbols-outlined text-4xl">bolt</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Sector 02</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Power Generation &amp; <span class="text-secondary italic">Utilities.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Supporting Reliable Power Production. Power generation facilities depend on reliable equipment performance to meet energy demand and maintain operational stability.</p>
                </div>

                <!-- Alternating Layout: Image Left, Text Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="relative order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Power Generation"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjl9eVb8XrEFxh0iW_kBJ_0dcnn3laux3ZDkXr7fGycCo30F5khhyyNnrodhk0WaVuXCqQOuiUmvx8599xERC4FYQOAwViCSHlC-SxjdLZ_g0isD4hDnop8ClLgDFLTdWrBo8h19SFeURf_NAQovQrUy40JwF_foBE9myeGhjMeDuS5CpDyfWKz0SXRAgtjAHo7RO0GDfOKM6LQ-QjaeOsSSBWIyeZNldqQeWvCV295VFECBSV0yyjE0uAbXg-yKljf5SzDo6MkCnI" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    <div class="space-y-8 order-1 lg:order-2">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel supports power plants and utility operators through technical maintenance, diagnostics, procurement, equipment rehabilitation and infrastructure support services.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Our Solutions for Power Facilities</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Generator Support Services
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Engine Overhaul
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Alternator Reconditioning
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Power Plant Diagnostics
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Preventive Maintenance Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Spare Parts Procurement
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> International Logistics
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Equipment Installation Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Civil &amp; Construction Works
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How We Deliver Value -->
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">How We Deliver Value</h3>
                            <p class="text-surface-bright/70 font-light leading-relaxed">Our solutions help reduce downtime, extend equipment life, improve operational efficiency, and maintain consistent power generation performance.</p>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Need Power Facility Support?</p>
                            <a href="contact.php" class="inline-block bg-secondary text-on-secondary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-secondary/20 hover:scale-105 transition-all">
                                Contact Our Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- INDUSTRY 3: Manufacturing & Industrial Facilities -->
        <section id="manufacturing" class="py-32 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-primary mb-4">
                        <span class="material-symbols-outlined text-4xl">factory</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Sector 03</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Manufacturing &amp; <span class="text-primary italic">Industrial Facilities.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Helping Industrial Facilities Operate Efficiently. Manufacturing facilities rely on equipment availability, process efficiency and reliable infrastructure to meet production targets and maintain competitiveness.</p>
                </div>

                <!-- Alternating Layout: Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel provides engineering support and procurement services that help industrial operators reduce equipment failures, improve maintenance outcomes and strengthen operational performance.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Our Solutions for Manufacturers</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment Troubleshooting
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Maintenance Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Motor Reconditioning
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Industrial Procurement
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Strategic Global Sourcing
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment Foundations
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Industrial Civil Works
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Construction Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Technical Consultancy
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Manufacturing Facility"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfFr582YiArBIQjEPMH_T-xgEh4j0HJAhiW6tqr1Fr8NiEH-CZ9sDVC0AFApM9RADHB9Fz7CgSJyWw0AOLc8HxBziJA79bKlJT11hYANSLCsTZ6qzQFYfq5Z7lSYPNQEyMrsWyBwIovdqtEX6kQJJ-Jc9NjDCqzZw0iWdmSwfnwNcxAeRx3PSwbuRyhHu7F32F8liwvrgRAknDLyMTU0EyPHxkD9N4C3fQnFmSRCHE6aDXEEwdFZK34QaFSHStCMPW31bYqzJl0gLo" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- How We Deliver Value -->
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">How We Deliver Value</h3>
                            <p class="text-surface-bright/70 font-light leading-relaxed">We help manufacturers reduce production interruptions, improve equipment reliability and support long-term operational growth.</p>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Need Manufacturing Support?</p>
                            <a href="contact.php" class="inline-block bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Contact Our Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- INDUSTRY 4: Construction & Infrastructure -->
        <section id="construction" class="py-32 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-primary mb-4">
                        <span class="material-symbols-outlined text-4xl">architecture</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Sector 04</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Construction &amp; <span class="text-primary italic">Infrastructure.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Supporting Construction Projects from Foundation to Completion. Construction projects depend on reliable equipment, efficient procurement systems and quality execution to meet safety, budget and schedule objectives.</p>
                </div>

                <!-- Alternating Layout: Image Left, Text Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="relative order-2 lg:order-1">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Construction Site"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgAXGKG4O_fRgqzt1Ov-O4zdKcd1miG_RYprvywVztEDtFxKm6sK36ssUHA0zyOGBE--ETWfnDKawgK9Ep6hf9PEoHK0M07G0GURrfsCDVPbglFwzrBjcJwnAFs3KIK4lcAnjjOt5cQtlFj0qS3lC2kscx1VyGFs9FLPCc7AWjmTqU5xWEwefnm6C2Z3Fe-IPIfZIQuL1R3mDM2Gq0clSfrsiZY0MztT1ydZ5pQwdgpS7uR3msPiDQoxxKQmY6uXp0rxffPZSkrXGz" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    <div class="space-y-8 order-1 lg:order-2">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel supports contractors, developers and project owners with technical support, procurement, logistics coordination and industrial construction services.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Our Solutions for Construction</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Heavy Equipment Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Earthmoving Machinery Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment Diagnostics
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Procurement &amp; Logistics
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Structural Steel Erection
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Equipment Foundations
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Reinforced Concrete Works
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Construction Project Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-lg">check_circle</span> Technical Supervision
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How We Deliver Value -->
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">How We Deliver Value</h3>
                            <p class="text-surface-bright/70 font-light leading-relaxed">Our multidisciplinary approach helps clients improve project efficiency, reduce delays and maintain high execution standards.</p>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Planning a Construction Project?</p>
                            <a href="contact.php" class="inline-block bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Contact Our Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- INDUSTRY 5: Government & Corporate Facilities -->
        <section id="government" class="py-32 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20 max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-3 text-secondary mb-4">
                        <span class="material-symbols-outlined text-4xl">account_balance</span>
                        <span class="font-label text-xs font-bold uppercase tracking-[0.2em]">Sector 05</span>
                    </div>
                    <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tight mb-6">Government &amp; <span class="text-secondary italic">Corporate Facilities.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mx-auto">Supporting Essential Facilities and Public Infrastructure. Government agencies and corporate organizations require reliable facilities, dependable equipment and efficient infrastructure to support their operations and service delivery objectives.</p>
                </div>

                <!-- Alternating Layout: Text Left, Image Right -->
                <div class="grid lg:grid-cols-2 gap-20 items-center mb-20">
                    <div class="space-y-8">
                        <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel provides practical engineering and technical solutions that improve facility reliability and operational performance.</p>
                        <div class="space-y-4">
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Our Solutions for Government &amp; Corporate</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Facility Maintenance Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Power System Support
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Procurement Services
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Equipment Installation
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Infrastructure Construction
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Civil Works
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Technical Consultancy
                                </div>
                                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-secondary text-lg">check_circle</span> Project Support Services
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Government Facility"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCskSpUXDG8TBndO5FBg2r8tQZX_zvEqYyw-7RmWeQJNhsmfrTtt1MYXVqOls9Yz33Z0_GnU4z0S-XODI1H6OEddlW9rRN0J7urSAVgvGbhwkJ03Oo5NqOLG9Mb88APXWwFStGuQ6ggHtywPZV8Ue_upteOvfshCQjqMHduoNHkGKqWnnkJj26frE5qmmW1GKk1xGsKisOtHoOHzqXYxJYedetTyCKV9pgusUMRyAqSwHaeKUj6seCheJFle-kCXsvVL1YkPulae1Dw" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>

                <!-- How We Deliver Value -->
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h3 class="font-headline text-2xl font-bold">How We Deliver Value</h3>
                            <p class="text-surface-bright/70 font-light leading-relaxed">We help organizations maintain operational readiness, improve infrastructure performance, and support long-term asset reliability.</p>
                        </div>
                        <div class="text-center lg:text-right space-y-4">
                            <p class="text-surface-bright/60 font-light">Need Facility Support?</p>
                            <a href="contact.php" class="inline-block bg-secondary text-on-secondary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-secondary/20 hover:scale-105 transition-all">
                                Contact Our Team
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
                <span class="text-on-primary/60 font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Supporting the Industries That Drive Growth</span>
                <h2 class="font-headline text-4xl md:text-5xl lg:text-6xl font-bold text-on-primary leading-[0.95] tracking-tighter max-w-4xl mx-auto">
                    Every Industry Faces Unique Challenges.
                </h2>
                <p class="text-on-primary/80 font-light text-lg max-w-3xl mx-auto">Wilsolvewel Nigeria Limited delivers practical engineering, procurement, maintenance and construction solutions tailored to the needs of critical sectors across Nigeria.</p>
                <p class="text-on-primary/70 font-light max-w-3xl mx-auto">Whether you operate an industrial plant, power facility, construction project, government institution or Oil &amp; Gas operation, our team is ready to support your success.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="contact.php" class="bg-on-primary text-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl hover:scale-105 transition-all">
                        Talk to an Engineer
                    </a>
                    <a href="contact.php" class="border-2 border-on-primary/30 text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest hover:bg-on-primary/10 transition-all">
                        Contact Us Today
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
