<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Emergency Protocol | Wilsovlewel Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    "fontSize": {
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
    },
    },
    },
    }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .technical-grid {
            background-image: radial-gradient(circle, #c2c6d4 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.05;
        }

        .anodized-gradient {
            background: linear-gradient(135deg, #00488d 0%, #005fb8 100%);
        }

        .site-gradient-bg {
            background: radial-gradient(circle at 0% 0%, rgba(0, 72, 141, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(173, 51, 0, 0.3) 0%, transparent 50%);
            background-attachment: fixed;
        }
    </style>
</head>

<body
    class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
    <!-- SideNavBar -->
    <script src="../../components/admin_sidenav.js" data-root="../../"></script>
    <script src="../../components/effects.js"></script>
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>
    <!-- Main Content Canvas -->
    <main class="lg:ml-64 pt-28 pb-12 px-6 md:px-12 max-w-[1600px] relative z-10">
        <!-- TopNavBar -->
        <script src="../../components/admin_topnav.js" data-root="../../"></script>
        <!-- Sub-Header: Focused Context -->
        <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl">
                <div
                    class="inline-flex items-center gap-2 bg-error-container text-on-error-container px-3 py-1 rounded-full mb-4">
                    <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                    <span class="text-[10px] font-headline font-bold uppercase tracking-[0.1em]">Protocol Alpha
                        Active</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-headline font-bold tracking-tight text-blue-950 mb-4">EMERGENCY
                    PROTOCOL</h1>
                <p class="text-lg text-secondary leading-relaxed max-w-xl">
                    Authorized terminal overrides for Node 01. Immediate activation will broadcast across all sectors.
                    Use with extreme caution.
                </p>
            </div>
            <div class="bg-surface-container-low p-6 rounded-lg border border-outline-variant/20">
                <p class="text-[10px] font-headline uppercase tracking-widest text-secondary mb-2">Current System Time
                </p>
                <p class="text-2xl font-headline font-bold tabular-nums">14:28:03 UTC</p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Primary Action Column -->
            <div class="lg:col-span-7 space-y-8">
                <!-- Activation Panel -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Lockdown Button -->
                    <button
                        class="group relative flex flex-col items-start p-8 bg-surface-container-lowest rounded-lg shadow-[0_40px_60px_-15px_rgba(0,0,0,0.05)] border-2 border-transparent hover:border-error/20 transition-all active:scale-[0.98]">
                        <div
                            class="w-12 h-12 rounded-lg bg-error-container text-error flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-3xl"
                                style="font-variation-settings: 'FILL' 1;">lock</span>
                        </div>
                        <span class="text-2xl font-headline font-bold text-blue-950 mb-2">Site Lockdown</span>
                        <p class="text-sm text-secondary text-left mb-8">Seals all pneumatic gates and restricts
                            unauthorized exit from high-risk zones.</p>
                        <div
                            class="mt-auto w-full flex justify-between items-center text-error font-headline font-bold text-xs uppercase tracking-widest">
                            <span>Activate Override</span>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </div>
                    </button>
                    <!-- Evacuation Button -->
                    <button
                        class="group relative flex flex-col items-start p-8 bg-surface-container-lowest rounded-lg shadow-[0_40px_60px_-15px_rgba(0,0,0,0.05)] border-2 border-transparent hover:border-tertiary/20 transition-all active:scale-[0.98]">
                        <div
                            class="w-12 h-12 rounded-lg bg-tertiary-fixed text-on-tertiary-fixed-variant flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-3xl"
                                style="font-variation-settings: 'FILL' 1;">exit_to_app</span>
                        </div>
                        <span class="text-2xl font-headline font-bold text-blue-950 mb-2">Evacuation</span>
                        <p class="text-sm text-secondary text-left mb-8">Initiate multi-sector departure procedure.
                            High-visibility lights enabled.</p>
                        <div
                            class="mt-auto w-full flex justify-between items-center text-on-tertiary-fixed-variant font-headline font-bold text-xs uppercase tracking-widest">
                            <span>Execute Broadcast</span>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </div>
                    </button>
                    <!-- Emergency Services Dispatch -->
                    <button
                        class="md:col-span-2 group relative flex items-center p-8 bg-blue-900 text-white rounded-lg shadow-xl hover:bg-blue-950 transition-all active:scale-[0.99] overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1/3 bg-blue-800/30 -skew-x-12 translate-x-1/2">
                        </div>
                        <div class="relative z-10 flex items-center gap-8 w-full">
                            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl">emergency</span>
                            </div>
                            <div class="flex-1 text-left">
                                <span class="text-3xl font-headline font-bold mb-1 block">Emergency Services
                                    Dispatch</span>
                                <p class="text-blue-200 text-sm">Direct uplink to Civil Defense and Medical Response
                                    teams.</p>
                            </div>
                            <span class="material-symbols-outlined text-4xl">chevron_right</span>
                        </div>
                    </button>
                </div>
                <!-- Communication Logs (Glassmorphism Section) -->
                <div class="bg-surface-container-low rounded-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-headline font-bold text-blue-950">Active Communication Logs</h2>
                        <button
                            class="text-xs font-label uppercase tracking-widest text-primary font-bold hover:underline">Export
                            Logs</button>
                    </div>
                    <div class="space-y-4">
                        <!-- Log Entry 1 -->
                        <div class="flex gap-4 p-4 bg-white/50 rounded-lg border border-outline-variant/10">
                            <div class="text-[10px] font-headline font-bold text-slate-400 pt-1">14:25</div>
                            <div>
                                <p class="text-sm font-bold text-blue-900 mb-1 uppercase tracking-tight">System
                                    Notification</p>
                                <p class="text-sm text-secondary">Pressure anomaly detected in Sector G-14. Automated
                                    sensors triggered warning levels.</p>
                            </div>
                        </div>
                        <!-- Log Entry 2 -->
                        <div class="flex gap-4 p-4 bg-white/50 rounded-lg border border-outline-variant/10">
                            <div class="text-[10px] font-headline font-bold text-slate-400 pt-1">14:22</div>
                            <div>
                                <p class="text-sm font-bold text-blue-900 mb-1 uppercase tracking-tight">H. Miller
                                    (Safety Lead)</p>
                                <p class="text-sm text-secondary">Initiating manual diagnostic of Valve 09. Dispatching
                                    tech crew to location.</p>
                            </div>
                        </div>
                        <!-- Log Entry 3 -->
                        <div class="flex gap-4 p-4 bg-white/50 rounded-lg border border-outline-variant/10">
                            <div class="text-[10px] font-headline font-bold text-slate-400 pt-1">14:15</div>
                            <div>
                                <p class="text-sm font-bold text-blue-900 mb-1 uppercase tracking-tight">Shift Change
                                </p>
                                <p class="text-sm text-secondary">Terminal Node 01 transition complete. 42 personnel
                                    registered on site.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sidebar Widgets -->
            <div class="lg:col-span-5 space-y-8">
                <!-- Personnel Roll Call Widget -->
                <div class="bg-surface-container-lowest rounded-lg p-8 shadow-[0_40px_60px_-15px_rgba(0,0,0,0.05)]">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-headline font-bold text-blue-950">Personnel Roll Call</h2>
                            <p class="text-xs text-secondary font-label uppercase tracking-widest mt-1">Real-Time Sector
                                Tracking</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-headline font-bold text-blue-700">38<span
                                    class="text-sm text-slate-400 font-normal">/42</span></div>
                            <p class="text-[10px] text-primary font-bold uppercase tracking-tight">Accounted For</p>
                        </div>
                    </div>
                    <!-- Progress Segments (Industrial Style) -->
                    <div class="grid grid-cols-10 gap-1 mb-8">
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-primary rounded-sm"></div>
                        <div class="h-3 bg-slate-200 rounded-sm"></div>
                        <div class="h-3 bg-slate-200 rounded-sm"></div>
                    </div>
                    <div class="space-y-4 max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Personnel Card 1 -->
                        <div
                            class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low transition-colors hover:bg-surface-container">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        data-alt="professional engineer headshot with safety helmet in high-tech industrial environment"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfcIgIBYlbSn4mOvzlkV602TSjYv5DNLGrqNzaG_7QT_KBZ6DaR0egOzdfmUGufnivw05YKA3dfkowOd64eYj7PvCf1UP3lbyP_ma94e5jX5EY9eiqbbV3F7ozJIS-lgiDF6Z01O4prD3diREhhPMwrkOTTazMZnaXebXrcfJnEFBYKJ3tnPwocf-CHiVPjJMyKRnXHBTi3xA5RElR9-finf674yUe0mrwEeKJo007Nn4q2nFLttk-puW5BkL0QKNtMRibobHPF3QL" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-950">Marcus Chen</p>
                                    <p class="text-[10px] text-secondary font-label uppercase">Control Room · Sector A
                                    </p>
                                </div>
                            </div>
                            <div class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">
                                SAFE</div>
                        </div>
                        <!-- Personnel Card 2 -->
                        <div
                            class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low transition-colors hover:bg-surface-container">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        data-alt="senior technician female in industrial workwear with calm expression in bright lab"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuD8aUoB3TWPSy7cLl2tYunwQGtIiEClX8EMwMlKO69hJ0pomY-pTAYO5G9yujuYYln4m3Y_GkaN6YNrb3taUf5P4UvoPugcS6KmqNdGKhxG6c4N55Hx7hEzOYTNUoc7IqY--vgq-RCMl3K61zm3RunYGCENYYZgcJxnTCChxcUZGu55B6dfElgg5fmRDQBFC7_bsS6Yl-uaelxr1FXuAPjRc-Ze4SMe6k65X2xn_VKRqZXznjzsbt28aChQiMweNtgCGuy2Gw9EuVyP" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-950">Sarah Jenkins</p>
                                    <p class="text-[10px] text-secondary font-label uppercase">Turbine Hall · Sector G
                                    </p>
                                </div>
                            </div>
                            <div class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">
                                SAFE</div>
                        </div>
                        <!-- Personnel Card 3 (Unaccounted) -->
                        <div
                            class="flex items-center justify-between p-3 rounded-lg bg-error-container/30 border border-error/10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        data-alt="male maintenance worker in warehouse setting with orange high visibility vest"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBVp15V4tkpiBTyfZM7mpyqyYoRG8UMInYsLEufb785clcuRvQxHK4nOKml9N-lbDNzvUJQsMIABakkZQpUbL3kt-Azsr6SmYM5F5JPMybtI6eJ5EMS7P0obup_TI3Po_4mm8jNd7k7fvt58ZlZop2Ohms2x1XaM7C5fpprZJ94sX50Q48jHYc28TzouOCLRGhJeYS_pSH0BP_PwF-OEdf5TQ0jk5BW3R2ioreAjrNskwOGnnqRC71Klmtg8sZS7TGT5vMfAwF6gPIK" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-950">David Okoro</p>
                                    <p class="text-[10px] text-error font-label uppercase">External Pipes · Sector Z</p>
                                </div>
                            </div>
                            <div class="px-2 py-1 bg-error text-white text-[10px] font-bold rounded uppercase">MISSING
                            </div>
                        </div>
                        <!-- Personnel Card 4 -->
                        <div
                            class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low transition-colors hover:bg-surface-container">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white overflow-hidden">
                                    <img class="w-full h-full object-cover"
                                        data-alt="female safety officer with glasses and headset working in professional control station"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuATY39sNUQi1hUl5o1sHhfetAcSJWjOAfzXaja47RcuAjzik72u9ljaAbWIrQL6rsbUZfr_NLCsb1DME03OZElHtzxRJ2TUEIpEZGzNM6xMrw4gvuZTuHCqraweTcqS8ajI3F1cnziofmtreXdXY2UK2z_NqcKzdGcO0Z1eZWradZBy5bEouoOdoYmmqCnjMEwYanmN3ha8NINAqEz2i8HH_7Qej3PitMkZj8dluBPogZO-HwtCG4MSVpmYnhzKMLMpse68H2gr-z7s" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-950">Elena Rodriguez</p>
                                    <p class="text-[10px] text-secondary font-label uppercase">Logistics · Sector B</p>
                                </div>
                            </div>
                            <div class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">
                                SAFE</div>
                        </div>
                    </div>
                </div>
                <!-- Site Blueprint Status -->
                <div
                    class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-[0_40px_60px_-15px_rgba(0,0,0,0.05)] border border-outline-variant/10">
                    <div class="p-6 border-b border-surface-container">
                        <h2 class="text-lg font-headline font-bold text-blue-950 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">map</span>
                            Sector Status Overview
                        </h2>
                    </div>
                    <div class="aspect-video relative bg-slate-100 group">
                        <img class="w-full h-full object-cover grayscale opacity-50"
                            data-alt="technical 3D schematic map of an industrial plant with highlighted glowing sectors in blue and red"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4YMF8hPNvKkLR3QhXStPTn1CdbesSeLhvFd95Ssr1qUmlpg7PX9O0zm1ZMsaxcZn-2-jZy6grVaNP0JYdOOsq9jJR7fJ920wPRDzrDkRh08cKl4SARRpg_7tEahvOEDQ9mwLuz0ZW9i3KxTt-529UbeHYlmjsVtLoTGCShI6WpzFGL_TMWGUzLtK5SJbgwirKZu9q3wPF3jmV30xDWCEdE62JuTqZ5AhKSyP8on9MOKdwW6VS51NuRyvZF8fJFLrfmBTZU26hxHOV" />
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="relative w-full h-full">
                                <!-- Sector Indicators -->
                                <div class="absolute top-1/4 left-1/3 w-4 h-4 bg-error rounded-full animate-ping"></div>
                                <div class="absolute top-1/4 left-1/3 w-4 h-4 bg-error rounded-full"></div>
                                <div
                                    class="absolute top-1/4 left-1/3 mt-6 bg-error text-white text-[10px] font-bold px-2 py-0.5 rounded">
                                    ZONE G-14: BREACH</div>
                                <div class="absolute bottom-1/3 right-1/4 w-4 h-4 bg-primary rounded-full"></div>
                                <div
                                    class="absolute bottom-1/3 right-1/4 mt-6 bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded">
                                    ZONE B: CLEAR</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- SideNavBar (Hidden on Mobile) -->
    <!-- SideNavBar -->
    <script src="../../components/admin_sidenav.js" data-root="../../"></script>
    <!-- Footer Mobile Navigation -->
    <nav
        class="lg:hidden fixed bottom-0 w-full h-16 bg-white/90 backdrop-blur-md border-t border-surface-container flex items-center justify-around z-50">
        <span class="material-symbols-outlined text-slate-500">dashboard</span>
        <span class="material-symbols-outlined text-slate-500">precision_manufacturing</span>
        <div
            class="w-12 h-12 -mt-10 bg-error rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">warning</span>
        </div>
        <span class="material-symbols-outlined text-slate-500">forum</span>
        <span class="material-symbols-outlined text-slate-500">settings</span>
    </nav>
</body>

</html>