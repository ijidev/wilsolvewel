<?php require_once 'config.php'; secure_session_start(); generate_csrf_token();
$conn = get_db_connection();

// If AJAX request, return JSON
$ajax_cat = $_GET['ajax_category'] ?? '';
if ($ajax_cat !== '') {
    header('Content-Type: application/json');
    if ($ajax_cat === 'all') {
        $res = $conn->query("SELECT f.id, f.question, f.answer, c.name as category_name, c.slug as category_slug, c.icon as category_icon FROM faqs f JOIN faq_categories c ON f.category_id=c.id WHERE f.status='Active' ORDER BY c.sort_order ASC, f.sort_order ASC, f.id DESC");
    } else {
        $stmt = $conn->prepare("SELECT f.id, f.question, f.answer, c.name as category_name, c.slug as category_slug, c.icon as category_icon FROM faqs f JOIN faq_categories c ON f.category_id=c.id WHERE c.slug=? AND f.status='Active' ORDER BY f.sort_order ASC, f.id DESC");
        $stmt->bind_param("s", $ajax_cat);
        $stmt->execute();
        $res = $stmt->get_result();
    }
    $faqs = [];
    if ($res) { while ($row = $res->fetch_assoc()) $faqs[] = $row; }
    if (isset($stmt)) $stmt->close();

    // Group by category for 'all' view
    $grouped = [];
    foreach ($faqs as $f) {
        $key = $f['category_slug'];
        if (!isset($grouped[$key])) {
            $grouped[$key] = ['name' => $f['category_name'], 'icon' => $f['category_icon'], 'slug' => $key, 'faqs' => []];
        }
        $grouped[$key]['faqs'][] = $f;
    }
    echo json_encode(['faqs' => $faqs, 'grouped' => array_values($grouped)]);
    exit;
}

// Initial page load: fetch categories for sidebar
$categories = [];
$res = $conn->query("SELECT * FROM faq_categories ORDER BY sort_order ASC, id ASC");
if ($res) { while ($row = $res->fetch_assoc()) $categories[] = $row; }
?><!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Precision Forge | FAQ Portal - Wilsovlewel Nigeria Limited</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        .faq-enter { animation: faqFadeIn .35s ease-out; }
        @keyframes faqFadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
    <script src="./components/header.js" data-root="./"></script>
    <script src="./components/effects.js"></script>
    <main class="relative pt-20">
        <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>
        <div class="relative z-10 py-16 px-5 sm:px-6 lg:px-12 max-w-7xl mx-auto">
            <header class="mb-16 text-center md:text-left space-y-6">
                <div class="inline-flex items-center gap-3 px-3 py-1 bg-primary/10 rounded-full border border-primary/20">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.2em]">Technical Knowledge Base</span>
                </div>
                <h1 class="font-headline text-4xl md:text-6xl font-bold tracking-tighter text-on-surface leading-[0.95]">
                    SOLVING INDUSTRIAL <br /><span class="text-primary italic">HEADACHES.</span>
                </h1>
                <p class="text-on-surface-variant text-lg font-light max-w-2xl leading-relaxed">
                    Clear answers to the technical, logistical, and operational questions that drive industrial efficiency in Nigeria.
                </p>
            </header>

            <div class="grid lg:grid-cols-12 gap-16">
                <!-- Sidebar: Category Tabs -->
                <div class="lg:col-span-3">
                    <div class="sticky top-32 space-y-8">
                        <div class="space-y-2">
                            <h3 class="text-[10px] font-label font-bold uppercase tracking-[0.4em] text-outline mb-6">Navigation</h3>
                            <nav class="flex flex-col gap-1" id="faqNav">
                                <button onclick="loadFaqs('all')" class="faq-tab text-left px-4 py-3 rounded-xl font-headline font-bold text-sm flex justify-between items-center group transition-all bg-primary text-on-primary" data-cat="">
                                    All Topics
                                    <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform text-on-primary">chevron_right</span>
                                </button>
                                <?php foreach ($categories as $c): ?>
                                <button onclick="loadFaqs('<?php echo $c['slug']; ?>')" class="faq-tab text-left px-4 py-3 rounded-xl font-headline font-bold text-sm flex justify-between items-center group transition-all hover:bg-surface-container-high" data-cat="<?php echo $c['slug']; ?>">
                                    <?php echo htmlspecialchars($c['name']); ?>
                                    <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">chevron_right</span>
                                </button>
                                <?php endforeach; ?>
                            </nav>
                        </div>

                        <div class="p-6 bg-surface-container-low rounded-3xl border border-outline-variant/10 space-y-4">
                            <span class="material-symbols-outlined text-primary text-3xl">support_agent</span>
                            <h4 class="font-headline font-bold text-sm uppercase tracking-widest">24/7 Response</h4>
                            <p class="text-xs text-on-surface-variant leading-relaxed">Facing a critical asset failure? Our diagnostic teams are ready for immediate deployment.</p>
                            <a href="contact.php" class="inline-flex items-center gap-2 text-primary font-label text-[10px] font-bold uppercase tracking-widest hover:underline">
                                Request Inspection <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Content: FAQs -->
                <div class="lg:col-span-9" id="faqContent">
                    <div class="text-center py-20">
                        <span class="material-symbols-outlined text-5xl text-outline mb-3 animate-spin">progress_activity</span>
                        <p class="text-on-surface-variant text-sm">Loading FAQs...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="./components/footer.js" data-root="./"></script>

    <script>
    function loadFaqs(slug) {
        // Update active tab
        document.querySelectorAll('.faq-tab').forEach(function(t) {
            t.classList.remove('bg-primary', 'text-on-primary');
            t.classList.add('hover:bg-surface-container-high');
            t.querySelector('.material-symbols-outlined')?.classList.remove('text-on-primary');
        });
        var activeTab = document.querySelector('.faq-tab[data-cat="' + slug + '"]') || document.querySelector('.faq-tab[data-cat=""]');
        if (activeTab) {
            activeTab.classList.remove('hover:bg-surface-container-high');
            activeTab.classList.add('bg-primary', 'text-on-primary');
            var icon = activeTab.querySelector('.material-symbols-outlined');
            if (icon) icon.classList.add('text-on-primary');
        }

        var container = document.getElementById('faqContent');
        container.innerHTML = '<div class="text-center py-20"><span class="material-symbols-outlined text-5xl text-outline mb-3 animate-spin">progress_activity</span><p class="text-on-surface-variant text-sm">Loading...</p></div>';

        fetch('faq.php?ajax_category=' + encodeURIComponent(slug || 'all'))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.grouped || data.grouped.length === 0) {
                    container.innerHTML = '<div class="text-center py-20"><span class="material-symbols-outlined text-5xl text-outline mb-3">quiz</span><p class="text-on-surface-variant text-sm">No FAQs available for this topic.</p></div>';
                    return;
                }
                var html = '';
                data.grouped.forEach(function(group) {
                    html += '<section class="space-y-8 mb-16 faq-enter">';
                    html += '<div class="flex items-center gap-4">';
                    html += '<div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary"><span class="material-symbols-outlined text-3xl">' + (group.icon || 'help') + '</span></div>';
                    html += '<h2 class="font-headline text-3xl font-bold tracking-tight">' + escapeHtml(group.name) + '</h2>';
                    html += '</div><div class="space-y-4">';
                    group.faqs.forEach(function(faq) {
                        html += '<details class="group bg-surface-container-low rounded-2xl border border-outline-variant/5 overflow-hidden transition-all hover:border-primary/20">';
                        html += '<summary class="flex justify-between items-center p-6 sm:p-8 cursor-pointer list-none font-headline font-bold text-base sm:text-lg">';
                        html += escapeHtml(faq.question);
                        html += '<span class="material-symbols-outlined transition-transform group-open:rotate-180 text-primary shrink-0 ml-4">expand_more</span>';
                        html += '</summary>';
                        html += '<div class="px-6 sm:px-8 pb-6 sm:pb-8 text-on-surface-variant font-light leading-relaxed border-t border-outline-variant/5 pt-6 text-sm sm:text-base">' + faq.answer + '</div>';
                        html += '</details>';
                    });
                    html += '</div></section>';
                });
                container.innerHTML = html;
            })
            .catch(function() {
                container.innerHTML = '<div class="text-center py-20"><span class="material-symbols-outlined text-5xl text-red-300 mb-3">error</span><p class="text-on-surface-variant text-sm">Failed to load FAQs.</p></div>';
            });
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Load all on page load
    document.addEventListener('DOMContentLoaded', function() { loadFaqs('all'); });
    </script>
</body>
</html>