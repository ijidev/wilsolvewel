<?php
$page_title = $page_title ?? 'WILSOVLEWEL | Client Portal';
$page_class = $page_class ?? '';
$page_styles = $page_styles ?? '';
$page_cdn_heads = $page_cdn_heads ?? '';
$page_scripts = $page_scripts ?? '';
$page_footer = $page_footer ?? '';
$page_after_main = $page_after_main ?? '';
if (!isset($page_main_class)) {
    $page_main_class = $page_h1 ? 'max-w-7xl mx-auto pt-6 pb-8 px-6' : 'max-w-7xl mx-auto pt-20 pb-8 px-6';
}
$page_grid_class = $page_grid_class ?? 'technical-grid';

$page_h1 = $page_h1 ?? '';
$page_h1_sub = $page_h1_sub ?? '';
$page_h1_badge = $page_h1_badge ?? '';
$page_h1_action = $page_h1_action ?? '';
$page_back_link = $page_back_link ?? 'javascript:history.back()';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <?= $page_cdn_heads ?>
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
                        "surface-variant": "#F5F5F5",
                        "on-surface-variant": "#4A4A4A",
                        "error": "#B00020",
                        "error-container": "#FFDAD6",
                        "on-error-container": "#410002",
                        "primary-container": "#FEF9C3",
                        "on-primary-container": "#422006",
                        "secondary": "#1A1A1A",
                        "on-secondary": "#FFFFFF",
                    },
                    fontFamily: { "headline": ["Space Grotesk"], "body": ["Manrope"], "label": ["Space Grotesk"] }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .technical-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.03; }
        .site-gradient-bg { background: radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.05) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(0, 0, 0, 0.05) 0%, transparent 50%); background-attachment: fixed; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        <?= $page_styles ?>
    </style>
</head>
<body class="bg-surface font-body text-on-surface <?= $page_class ?>">
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <script src="../components/client_topnav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    <div class="fixed inset-0 pointer-events-none <?= $page_grid_class ?> z-0"></div>

    <?php if ($page_h1 || $page_h1_action): ?>
    <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-4 sm:px-6 shrink-0 z-20 relative mt-16">
        <div class="flex items-center gap-3 min-w-0">
            <?php if ($page_back_link): ?>
            <a href="<?= htmlspecialchars($page_back_link) ?>" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 transition-colors shrink-0 -ml-1">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <?php endif; ?>
            <div class="min-w-0">
                <?php if ($page_h1_badge): ?>
                <span class="text-[9px] font-bold text-primary uppercase tracking-widest font-headline block"><?= $page_h1_badge ?></span>
                <?php endif; ?>
                <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight truncate"><?= $page_h1 ?></h1>
                <?php if ($page_h1_sub): ?>
                <p class="hidden sm:block text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?= $page_h1_sub ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($page_h1_action): ?>
        <div class="shrink-0 ml-2">
            <?= $page_h1_action ?>
        </div>
        <?php endif; ?>
    </header>
    <?php endif; ?>

    <main class="relative <?= $page_main_class ?>">
        <?= $page_content ?>
    </main>

    <?= $page_after_main ?>

    <?php if ($page_footer): ?>
    <footer class="w-full relative z-10">
        <?= $page_footer ?>
    </footer>
    <?php endif; ?>

    <?= $page_scripts ?>
</body>
</html>
