<?php
include '../../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$conn = get_db_connection();

// --- Fetch Statistics ---
// 1. Safe Days Since LTI (Mocked for now, but linked to a hypothetical LTI table or setting)
$safe_days = get_setting('hsse_safe_days', 412);

// 2. Compliance Index (Average of recent audit scores or static setting)
$compliance_index = get_setting('hsse_compliance_index', 94.2);

// 3. Recent Observations Count
$critical_count = 0;
$critical_res = $conn->query("SELECT COUNT(*) as count FROM hsse_observations WHERE severity = 'High' AND status != 'Resolved'");
if ($critical_res) $critical_count = $critical_res->fetch_assoc()['count'];

// 4. Recent Observations List
$observations_res = $conn->query("
    SELECT o.*, a.name as inspector_name, a.avatar as inspector_avatar 
    FROM hsse_observations o 
    LEFT JOIN admins a ON o.inspector_id = a.id 
    ORDER BY o.created_at DESC 
    LIMIT 10
");
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                "primary": "#EAB308",
                "on-primary": "#000000",
                "primary-container": "#FEF9C3",
                "on-primary-container": "#422006",
                "secondary": "#1A1A1A",
                "on-secondary": "#FFFFFF",
                "surface": "#FDFDFD",
                "on-surface": "#1A1A1A",
                "surface-container-lowest": "#FFFFFF",
                "surface-container-low": "#F7F7F7",
                "surface-container": "#F3F3F3",
                "surface-container-high": "#EFEFEF",
                "surface-container-highest": "#EBEBEB",
                "outline-variant": "#CAC4D0",
                "error": "#B00020",
                "background": "#FDFDFD"
            },
            "borderRadius": {
                    "DEFAULT": "1rem",
                    "lg": "2rem",
                    "xl": "3rem",
                    "full": "9999px"
            },
            "fontFamily": {
                    "headline": ["Space Grotesk"],
                    "body": ["Manrope"],
                    "label": ["Space Grotesk"]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .blueprint-grid {
            background-image: radial-gradient(circle, #c2c6d3 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.05;
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(24px);
        }
    </style>
</head>
<body class="bg-background font-body text-on-surface overflow-x-hidden">
<!-- Grid Overlay -->
<div class="fixed inset-0 blueprint-grid pointer-events-none z-0"></div>
<!-- SideNavBar (Execution from JSON) -->
<!-- SideNavBar --><script src="../../components/admin_sidenav.js" data-root="../../"></script>
<!-- TopNavBar (Execution from JSON) -->
<!-- TopNavBar --><script src="../../components/admin_topnav.js"></script>
<!-- Main Canvas -->
<main class="ml-64 p-8 relative z-10">
<div class="max-w-[1400px] mx-auto">
<!-- Header Section -->
<div class="mb-10 flex justify-between items-end">
<div>
<h2 class="text-4xl font-headline font-bold tracking-tight text-on-surface">HSSE Monitoring</h2>
<p class="font-label uppercase tracking-[0.2em] text-sm text-outline mt-1">Terminal Operational Environment • Sector 04</p>
</div>
<div class="flex gap-3">
<button class="bg-surface-container-high px-6 py-2.5 rounded-full font-label text-xs font-bold tracking-wider hover:bg-surface-container-highest transition-all flex items-center gap-2">
<span class="material-symbols-outlined text-lg">download</span> EXPORT REPORT
                    </button>
<button class="bg-gradient-to-br from-primary to-primary-container text-white px-6 py-2.5 rounded-full font-label text-xs font-bold tracking-wider shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-2">
<span class="material-symbols-outlined text-lg">add</span> NEW OBSERVATION
                    </button>
</div>
</div>
<!-- Bento Grid Layout -->
<div class="grid grid-cols-12 gap-6 items-start">
<!-- Zero-Harm Target Counter -->
<div class="col-span-12 md:col-span-4 bg-surface-container-lowest p-8 rounded-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between h-[320px]">
<div>
<span class="font-label text-[10px] uppercase tracking-widest text-primary font-bold">Performance Metric</span>
<h3 class="font-headline text-xl font-bold mt-2">Zero-Harm Target</h3>
</div>
<div class="text-center py-4">
<span class="text-7xl font-headline font-extrabold tracking-tighter text-on-surface"><?= $safe_days ?></span>
<p class="font-label uppercase text-[12px] tracking-widest text-outline mt-2">Safe Days Since LTI</p>
</div>
<div class="flex gap-1">
<div class="h-1 flex-1 bg-primary rounded-full"></div>
<div class="h-1 flex-1 bg-primary rounded-full"></div>
<div class="h-1 flex-1 bg-primary rounded-full"></div>
<div class="h-1 flex-1 bg-primary/20 rounded-full"></div>
<div class="h-1 flex-1 bg-primary/20 rounded-full"></div>
</div>
</div>
<!-- Compliance Index Gauge -->
<div class="col-span-12 md:col-span-4 bg-surface-container-lowest p-8 rounded-lg shadow-sm border border-outline-variant/10 h-[320px] flex flex-col">
<div>
<span class="font-label text-[10px] uppercase tracking-widest text-primary font-bold">Regulatory Health</span>
<h3 class="font-headline text-xl font-bold mt-2">Compliance Index</h3>
</div>
<div class="flex-1 flex flex-col items-center justify-center relative">
<svg class="w-48 h-48 transform -rotate-90">
<circle class="text-surface-container" cx="96" cy="96" fill="transparent" r="80" stroke="currentColor" stroke-width="12"></circle>
<circle class="text-primary" cx="96" cy="96" fill="transparent" r="80" stroke="currentColor" stroke-dasharray="502.6" stroke-dashoffset="29.1" stroke-width="12"></circle>
</svg>
<div class="absolute flex flex-col items-center">
<span class="text-4xl font-headline font-bold"><?= $compliance_index ?>%</span>
<span class="font-label text-[10px] text-outline tracking-wider">Operational Health</span>
</div>
</div>
</div>
<!-- Upcoming Audits -->
<div class="col-span-12 md:col-span-4 bg-surface-container-low p-8 rounded-lg h-[320px] flex flex-col">
<h3 class="font-headline text-xl font-bold mb-6">Upcoming Audits</h3>
<div class="space-y-4">
<div class="flex items-center gap-4 bg-surface-container-lowest p-4 rounded-md">
<div class="w-10 h-10 bg-secondary-container rounded flex items-center justify-center text-on-secondary-container">
<span class="material-symbols-outlined">verified_user</span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">ISO 14001 Review</p>
<p class="text-xs text-outline">Environmental Systems</p>
</div>
<div class="text-right">
<p class="text-xs font-label font-bold text-primary">OCT 24</p>
</div>
</div>
<div class="flex items-center gap-4 bg-surface-container-lowest p-4 rounded-md">
<div class="w-10 h-10 bg-tertiary-fixed rounded flex items-center justify-center text-on-tertiary-fixed">
<span class="material-symbols-outlined">fire_truck</span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">Fire Suppression Test</p>
<p class="text-xs text-outline">Pier 08 / Fuel Farm</p>
</div>
<div class="text-right">
<p class="text-xs font-label font-bold text-primary">OCT 28</p>
</div>
</div>
</div>
<button class="mt-auto text-xs font-label font-bold text-primary flex items-center gap-1 hover:gap-2 transition-all">
                        VIEW FULL SCHEDULE <span class="material-symbols-outlined text-sm">arrow_forward</span>
</button>
</div>
<!-- Recent Observations (Asymmetric Layout) -->
<div class="col-span-12 md:col-span-8 bg-surface-container-lowest p-8 rounded-lg shadow-sm border border-outline-variant/10">
<div class="flex justify-between items-center mb-8">
<h3 class="font-headline text-2xl font-bold">Recent Observations</h3>
<div class="flex gap-2">
<span class="px-3 py-1 bg-surface-container text-[10px] font-label font-bold rounded-full">ALL</span>
<?php if ($critical_count > 0): ?>
<span class="px-3 py-1 bg-error-container text-on-error-container text-[10px] font-label font-bold rounded-full">CRITICAL (<?= $critical_count ?>)</span>
<?php endif; ?>
</div>
</div>
<div class="space-y-1">
<?php 
if ($observations_res && $observations_res->num_rows > 0):
    while ($obs = $observations_res->fetch_assoc()):
        $severity_color = 'bg-primary';
        if ($obs['severity'] == 'High') $severity_color = 'bg-error';
        elseif ($obs['severity'] == 'Low') $severity_color = 'bg-secondary-container';
?>
<div class="group flex items-center gap-6 p-4 rounded-lg hover:bg-surface-container-low transition-colors cursor-pointer border-b border-outline-variant/10">
<div class="w-2 h-10 <?= $severity_color ?> rounded-full"></div>
<div class="flex-1">
<div class="flex items-center gap-2 mb-1">
<span class="text-sm font-bold text-on-surface"><?= htmlspecialchars($obs['title']) ?></span>
<span class="text-[10px] font-label font-bold uppercase px-2 py-0.5 bg-slate-100 text-slate-600 rounded"><?= htmlspecialchars($obs['type']) ?></span>
</div>
<p class="text-xs text-outline"><?= htmlspecialchars($obs['description']) ?></p>
</div>
<div class="text-right flex flex-col gap-1 items-end">
<span class="text-xs font-medium"><?= date('H:i A', strtotime($obs['created_at'])) ?></span>
<div class="flex -space-x-2">
<?php if ($obs['inspector_avatar']): ?>
<img alt="Inspector" class="w-6 h-6 rounded-full border-2 border-white" src="../../<?= htmlspecialchars($obs['inspector_avatar']) ?>"/>
<?php else: ?>
<div class="w-6 h-6 rounded-full bg-surface-container text-[8px] flex items-center justify-center font-bold border-2 border-white"><?= substr($obs['inspector_name'] ?: 'A', 0, 1) ?></div>
<?php endif; ?>
</div>
</div>
</div>
<?php 
    endwhile;
else:
?>
<p class="text-sm text-outline italic p-8 text-center">No recent observations logged.</p>
<?php endif; ?>
</div>
</div>
<!-- Hazard Heatmap -->
<div class="col-span-12 md:col-span-4 bg-surface-container-lowest p-8 rounded-lg shadow-sm border border-outline-variant/10 flex flex-col">
<h3 class="font-headline text-xl font-bold mb-4">Hazard Heatmap</h3>
<div class="relative flex-1 rounded-md overflow-hidden bg-surface-container-low min-h-[240px]">
<img alt="Terminal Map" class="w-full h-full object-cover opacity-60 mix-blend-multiply" data-alt="aerial schematic of an industrial port terminal with containers and cranes seen from above" data-location="Port Terminal" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDKudO_qQIZI5Qzm3gYwsEO_FpIwxUsKJjS7E8B7ZmzXNKOK66LiIP61Q8stAsl8VOgkhLxxSn8-LV5f1LI5UV-U5QwfJvCPc0OGZqShRcWxmTTVu6D0ZWrQ8BACfSs-i_2zqjPD08bUs7QBPjPWGLg_iGBRcofFVwuoA96e-EBNmvp45uaGMVHsLJarI8fUdyu8cb2IkZw_KMxJQqycopJX4Dy4rkVQIjrdhl2aXgB1bc6P4RYC4nn3xu6GXaajsXbSxkm47awOYc_"/>
<!-- Heatmap Pings -->
<div class="absolute top-1/4 left-1/3 w-12 h-12 bg-error/20 rounded-full animate-pulse flex items-center justify-center">
<div class="w-4 h-4 bg-error rounded-full"></div>
</div>
<div class="absolute bottom-1/3 right-1/4 w-8 h-8 bg-tertiary/20 rounded-full flex items-center justify-center">
<div class="w-2 h-2 bg-tertiary rounded-full"></div>
</div>
<div class="absolute top-1/2 right-1/2 w-16 h-16 bg-error/10 rounded-full animate-pulse flex items-center justify-center">
<div class="w-6 h-6 bg-error/40 rounded-full"></div>
</div>
<!-- Legend -->
<div class="absolute bottom-2 left-2 bg-white/80 backdrop-blur-md p-2 rounded text-[8px] font-label font-bold space-y-1">
<div class="flex items-center gap-1"><div class="w-2 h-2 bg-error rounded-full"></div> HIGH RISK</div>
<div class="flex items-center gap-1"><div class="w-2 h-2 bg-tertiary rounded-full"></div> MODERATE</div>
</div>
</div>
<div class="mt-4">
<p class="text-xs text-outline italic">Live telemetry from Zone 04 &amp; 09 active.</p>
</div>
</div>
</div>
<!-- Footer Metrics -->
<div class="mt-12 grid grid-cols-1 md:grid-cols-4 gap-6">
<div class="bg-surface-container-low px-6 py-4 rounded-md flex justify-between items-center">
<div>
<p class="text-[10px] font-label font-bold text-outline">TOTAL MAN HOURS</p>
<p class="text-lg font-headline font-bold">1.2M</p>
</div>
<span class="material-symbols-outlined text-outline">timelapse</span>
</div>
<div class="bg-surface-container-low px-6 py-4 rounded-md flex justify-between items-center">
<div>
<p class="text-[10px] font-label font-bold text-outline">NEAR MISSES (MTD)</p>
<p class="text-lg font-headline font-bold text-error">12</p>
</div>
<span class="material-symbols-outlined text-error/60">warning</span>
</div>
<div class="bg-surface-container-low px-6 py-4 rounded-md flex justify-between items-center">
<div>
<p class="text-[10px] font-label font-bold text-outline">TRAINING COMPLIANCE</p>
<p class="text-lg font-headline font-bold">98.8%</p>
</div>
<span class="material-symbols-outlined text-primary">school</span>
</div>
<div class="bg-surface-container-low px-6 py-4 rounded-md flex justify-between items-center">
<div>
<p class="text-[10px] font-label font-bold text-outline">SEC. BREACHES</p>
<p class="text-lg font-headline font-bold">0</p>
</div>
<span class="material-symbols-outlined text-outline">security</span>
</div>
</div>
</div>
</main>
<!-- Floating Technical Chip (Fab Alternative) -->
<div class="fixed bottom-8 right-8 z-50">
<button class="bg-on-surface text-surface flex items-center gap-3 px-6 py-4 rounded-full shadow-2xl hover:scale-105 transition-all">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">emergency</span>
<span class="font-label text-xs font-bold tracking-widest">EMERGENCY PROTOCOLS</span>
</button>
</div>
</body></html>