<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 1;
$conn = get_db_connection();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Wilsovlewel HSSE Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#EAB308",
                        "on-primary": "#000000",
                        "surface": "#FDFDFD",
                        "on-surface": "#1A1A1A",
                    },
                    fontFamily: { "headline": ["Space Grotesk"], "body": ["Manrope"], "label": ["Space Grotesk"] }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .technical-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.05; }
        .site-gradient-bg { background: radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.05) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(0, 0, 0, 0.05) 0%, transparent 50%); background-attachment: fixed; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface site-gradient-bg">
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

    <main class="lg:ml-64 pt-24 pb-12 px-8 relative z-10">
        <!-- TopNavBar -->
        <script src="../components/client_topnav.js" data-root="../"></script>
        
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8">
                    <div class="mb-10">
                        <span class="font-headline text-xs uppercase tracking-widest text-primary font-bold mb-2 block">System Status: Active</span>
                        <h1 class="font-headline text-5xl font-extrabold tracking-tighter text-on-surface mb-4">HSSE Operations</h1>
                        <p class="font-body text-slate-500 max-w-xl">Real-time health, safety, security, and environmental monitoring for your active projects.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                            <div>
                                <span class="font-headline text-[10px] uppercase tracking-wider text-slate-400 font-bold">Incidence-Free Period</span>
                                <h3 class="font-headline text-6xl font-black text-primary mt-2">142</h3>
                                <p class="font-body text-sm font-bold text-on-surface mt-1">SAFE DAYS RECORDED</p>
                            </div>
                            <div class="mt-8 flex items-center gap-4">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden flex gap-1">
                                    <div class="h-full w-full bg-primary" style="width: 60%"></div>
                                </div>
                                <span class="font-headline text-xs font-bold text-primary">60% TO GOAL</span>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                            <div>
                                <span class="font-headline text-[10px] uppercase tracking-wider text-slate-400 font-bold">Compliance Index</span>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <h3 class="font-headline text-6xl font-black text-slate-900">98.4</h3>
                                    <span class="text-xl font-bold text-white bg-slate-900 px-2 rounded">%</span>
                                </div>
                                <p class="font-body text-sm text-slate-500 mt-2">Tier 1 Safety Certification Maintained</p>
                            </div>
                            <div class="mt-6 flex gap-2">
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-bold">AUDIT PASS</span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold">ISO 45001</span>
                            </div>
                        </div>

                        <div class="md:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-2 h-2 rounded-full bg-error animate-pulse"></div>
                                <h3 class="font-headline text-lg font-bold">Live Hazard Feed</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-start gap-4 p-4 bg-error/5 border-l-4 border-error rounded-r-xl">
                                    <span class="material-symbols-outlined text-error">priority_high</span>
                                    <div>
                                        <p class="font-body font-bold text-sm text-on-surface">Weather Alert: High Wind Velocity</p>
                                        <p class="text-xs text-slate-500">Zone 4 Cranes ordered to stow position. Current speed: 42 knots.</p>
                                    </div>
                                    <span class="ml-auto text-[10px] font-headline text-slate-400 uppercase">02m AGO</span>
                                </div>
                                <div class="flex items-start gap-4 p-4 bg-slate-50 border-l-4 border-slate-300 rounded-r-xl">
                                    <span class="material-symbols-outlined text-slate-400">info</span>
                                    <div>
                                        <p class="font-body font-bold text-sm text-on-surface">Perimeter Inspection Complete</p>
                                        <p class="text-xs text-slate-500">Gate 2 sensor recalibrated by HSSE Team Delta.</p>
                                    </div>
                                    <span class="ml-auto text-[10px] font-headline text-slate-400 uppercase">45m AGO</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="bg-slate-900 text-white p-8 rounded-3xl relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="font-headline text-2xl font-bold mb-4">Emergency Initiation</h3>
                            <p class="font-body text-sm text-slate-400 mb-8">Execute protocol only in the event of immediate danger.</p>
                            <div class="space-y-3">
                                <button class="w-full py-4 border-2 border-error text-error font-headline font-black rounded-xl hover:bg-error hover:text-white transition-all active:scale-95 flex items-center justify-center gap-3">
                                    <span class="material-symbols-outlined">emergency</span>
                                    INITIATE SIGNAL
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100">
                        <h4 class="font-headline text-xs font-black tracking-widest text-primary mb-6">UPCOMING TOOLBOX TALKS</h4>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="flex-none w-12 h-12 bg-white rounded-xl flex flex-col items-center justify-center shadow-sm">
                                    <span class="text-[10px] font-bold text-slate-400">OCT</span>
                                    <span class="text-lg font-black text-slate-900 leading-tight">24</span>
                                </div>
                                <div>
                                    <p class="font-body font-bold text-sm">Working at Heights v3</p>
                                    <p class="text-xs text-slate-500">08:30 AM • Site B Hub</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
