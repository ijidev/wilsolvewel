<?php
require_once '../includes/admin_auth.php';
generate_csrf_token();
$conn = get_db_connection();
$admin_id = (int)$_SESSION['admin_id'];

$site_root = realpath(__DIR__ . '/..');

// Public front-end pages to scan (pages that load components/header.js)
$scan_pages = [
    'index.php', 'about.php', 'career.php', 'contact.php', 'faq.php',
    'projects.php', 'spec-forms.php', 'project-detail.php',
    'services.php', 'industries.php', 'hsse.php', 'gateway.php',
    'services/engineering.php', 'services/procurement.php',
    'services/technical-support.php', 'services/hydraulic-pump.php'
];

function truncate_text($s, $n) {
    $s = trim($s);
    if ($s === '') return '';
    return (function_exists('mb_substr') ? mb_substr($s, 0, $n) : substr($s, 0, $n)) . (strlen($s) > $n ? '…' : '');
}

function admin_preview_url($url) {
    if ($url !== '' && strpos($url, 'uploads/') === 0) return '../' . $url;
    return $url;
}

function scan_page_images($site_root, $file) {
    $path = $site_root . '/' . $file;
    if (!is_file($path)) return [];

    $html = @file_get_contents($path);
    if ($html === false) return [];

    preg_match_all('/<\s*img\b[^>]*>/i', $html, $img_matches, PREG_OFFSET_CAPTURE);

    $headings = [];
    preg_match_all('/<h([1-4])\b[^>]*>(.*?)<\/h\1>/is', $html, $h_matches, PREG_OFFSET_CAPTURE);
    foreach ($h_matches[0] as $i => $hm) {
        $headings[] = [
            'pos'  => $hm[1],
            'text' => trim(strip_tags(html_entity_decode($h_matches[2][$i][0], ENT_QUOTES, 'UTF-8')))
        ];
    }

    $entries = [];
    foreach ($img_matches[0] as $img_match) {
        $tag = $img_match[0];
        $pos = $img_match[1];

        $src = '';
        if (preg_match('/\bsrc\s*=\s*["\']([^"\']*)["\']/i', $tag, $sm)) $src = trim($sm[1]);
        if ($src === '' || $src[0] === '#' || strpos($src, 'data:') === 0 || strpos($src, '<?') !== false) continue;

        $alt = '';
        if (preg_match('/\balt\s*=\s*["\']([^"\']*)["\']/i', $tag, $am)) $alt = trim($am[1]);

        $section = '';
        foreach ($headings as $h) {
            if ($h['pos'] < $pos) $section = $h['text'];
            else break;
        }
        $section = $section !== '' ? truncate_text($section, 70) : 'General section';

        $entries[] = [
            'page'    => $file,
            'src'     => $src,
            'alt'     => truncate_text($alt, 120),
            'section' => $section
        ];
    }
    return $entries;
}

// Scan every public page and aggregate by (page, src)
$by_key = [];
foreach ($scan_pages as $file) {
    foreach (scan_page_images($site_root, $file) as $e) {
        $key = $e['page'] . ':' . $e['src'];
        if (!isset($by_key[$key])) {
            $e['count'] = 1;
            $by_key[$key] = $e;
        } else {
            $by_key[$key]['count']++;
        }
    }
}

// Load stored overrides
$overrides = [];
$res = $conn->query("SELECT image_key, override_url FROM site_images");
if ($res) {
    while ($row = $res->fetch_assoc()) $overrides[$row['image_key']] = $row['override_url'];
}

// Group by page for rendering
$pages = [];
foreach ($by_key as $key => $e) {
    $e['key'] = $key;
    $e['override'] = $overrides[$key] ?? '';
    $pages[$e['page']][] = $e;
}

$total_images = count($by_key);
$custom_count = 0;
foreach ($by_key as $e) {
    if (!empty($overrides[$e['page'] . ':' . $e['src']])) $custom_count++;
}

$page_title = 'Site Images';
$page_subtitle = 'Replace front-end images without touching code';
$page_header_actions = '<a href="' . app_url('index.php') . '" target="_blank" class="inline-flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs hover:shadow-lg transition-all active:scale-95">
    <span class="material-symbols-outlined text-sm">open_in_new</span> PREVIEW SITE
</a>';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Site Images | Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Inter"]}}}}</script>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .img-card{border:1px solid #E2E8F0;border-radius:1rem;background:#fff;transition:box-shadow .2s, transform .2s}
        .img-card:hover{box-shadow:0 10px 30px rgba(15,23,42,.08)}
        .img-card.custom{border-color:#86EFAC}
        .thumb-fallback{background:linear-gradient(135deg,#F1F5F9,#E2E8F0);display:flex;align-items:center;justify-content:center}
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">
<script src="../components/admin_sidenav.js?v=2" data-root="../"></script>

<div id="toast" class="fixed top-6 right-6 z-[400] transform translate-x-[150%] transition-transform duration-300 pointer-events-none">
    <div id="toastContent" class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
        <span id="toastIcon" class="material-symbols-outlined text-primary">check_circle</span>
        <span id="toastMsg" class="text-sm font-semibold"></span>
    </div>
</div>

<main class="flex-1 min-w-0 flex flex-col">
    <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

    <div class="p-4 sm:p-6 lg:p-8 flex-1">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Info + Stats -->
            <div class="bg-primary/10 border border-primary/20 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <p class="text-sm text-slate-700 leading-relaxed max-w-2xl">
                    Every image below was <strong>automatically scanned</strong> from the public pages. Pick any one and
                    upload a photo or paste a URL — no code changes needed. Changes go live immediately.
                </p>
                <div class="flex gap-3 shrink-0">
                    <div class="bg-white rounded-xl px-4 py-2 text-center shadow-sm">
                        <p class="text-xl font-black font-headline text-slate-900"><?= $total_images ?></p>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Images Found</p>
                    </div>
                    <div class="bg-white rounded-xl px-4 py-2 text-center shadow-sm">
                        <p class="text-xl font-black font-headline text-emerald-600"><?= $custom_count ?></p>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Customized</p>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input id="imgSearch" type="text" placeholder="Search pages, sections, or image URLs…"
                       class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"/>
            </div>

            <!-- Page groups -->
            <?php foreach ($pages as $page_file => $imgs): $page_custom = 0; foreach ($imgs as $i) { if (!empty($i['override'])) $page_custom++; } ?>
            <section class="img-group space-y-3" data-page="<?= htmlspecialchars($page_file) ?>">
                <div class="flex items-center justify-between pt-2">
                    <h2 class="font-headline font-bold text-slate-900 text-base flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">description</span>
                        <span class="group-name"><?= htmlspecialchars($page_file) ?></span>
                    </h2>
                    <div class="flex gap-2 text-[10px] font-bold uppercase tracking-widest">
                        <span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded-lg"><?= count($imgs) ?> images</span>
                        <?php if ($page_custom > 0): ?>
                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg"><?= $page_custom ?> customized</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <?php foreach ($imgs as $img):
                        $is_custom = !empty($img['override']);
                        $active_url = $is_custom ? admin_preview_url($img['override']) : $img['src'];
                    ?>
                    <div class="img-card <?= $is_custom ? 'custom' : '' ?> p-4 flex flex-col gap-3" data-key="<?= htmlspecialchars($img['key']) ?>" data-src="<?= htmlspecialchars($img['src']) ?>">
                        <div class="flex gap-3">
                            <div class="thumb-fallback w-28 h-20 rounded-xl overflow-hidden shrink-0 border border-slate-100">
                                <img class="thumb w-full h-full object-cover" src="<?= htmlspecialchars($active_url) ?>" loading="lazy" alt="" onerror="this.parentElement.classList.add('thumb-fallback'); this.style.display='none'">
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="badge <?= $is_custom ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' ?> text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-md">
                                        <?= $is_custom ? 'Custom' : 'Original' ?>
                                    </span>
                                    <?php if ($img['count'] > 1): ?>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-amber-600">×<?= $img['count'] ?> on page</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-sm font-bold text-slate-900 leading-snug group-label" title="<?= htmlspecialchars($img['section']) ?>"><?= htmlspecialchars($img['section']) ?></p>
                                <?php if (!empty($img['alt'])): ?><p class="text-xs text-slate-400 truncate" title="<?= htmlspecialchars($img['alt']) ?>"><?= htmlspecialchars($img['alt']) ?></p><?php endif; ?>
                            </div>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Source</p>
                            <p class="text-[11px] text-slate-500 truncate src-text" title="<?= htmlspecialchars($img['src']) ?>"><?= htmlspecialchars($img['src']) ?></p>
                        </div>

                        <div class="space-y-2 mt-auto">
                            <label class="flex items-center justify-center gap-2 w-full bg-slate-900 text-white px-3 py-2 rounded-xl text-xs font-bold cursor-pointer hover:bg-slate-700 transition-colors active:scale-[0.98]">
                                <span class="material-symbols-outlined text-sm">upload_file</span>
                                UPLOAD PHOTO
                                <input type="file" accept="image/*" class="hidden file-input">
                            </label>
                            <div class="flex gap-2">
                                <input type="url" class="url-input flex-1 min-w-0 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-primary/40" placeholder="Or paste image URL…" value="<?= htmlspecialchars($is_custom ? $img['override'] : '') ?>">
                                <button class="save-btn bg-primary text-on-primary px-3 py-2 rounded-xl text-xs font-bold hover:shadow-md transition-all active:scale-95">Save</button>
                                <?php if ($is_custom): ?>
                                <button class="reset-btn bg-red-50 text-red-500 px-3 py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors" title="Revert to original">Reset</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>

            <?php if (empty($pages)): ?>
            <div class="text-center py-20 bg-white rounded-2xl border border-slate-100">
                <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">image_search</span>
                <p class="text-slate-500 text-sm">No images found in the scanned pages.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
(function () {
    var CSRF = <?= json_encode(generate_csrf_token()) ?>;

    function toast(msg, ok) {
        var t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        document.getElementById('toastIcon').textContent = ok ? 'check_circle' : 'error';
        document.getElementById('toastIcon').classList.toggle('text-red-400', !ok);
        document.getElementById('toastIcon').classList.toggle('text-primary', ok);
        t.classList.remove('translate-x-[150%]');
        setTimeout(function () { t.classList.add('translate-x-[150%]'); }, 2600);
    }

    function api(formData, cb) {
        fetch('../api/site_images.php', { method: 'POST', body: formData })
            .then(function (r) { return r.json().catch(function () { return { status: 'error', message: 'Bad response' }; }); })
            .then(cb)
            .catch(function () { toast('Network error', false); });
    }

    function applyOverride(card, url) {
        var thumb = card.querySelector('.thumb');
        if (thumb) {
            thumb.style.display = '';
            thumb.src = (url.indexOf('uploads/') === 0) ? '../' + url : url;
        }
        card.classList.add('custom');
        var badge = card.querySelector('.badge');
        if (badge) { badge.textContent = 'Custom'; badge.className = 'badge bg-emerald-100 text-emerald-700 text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-md'; }
        var input = card.querySelector('.url-input');
        if (input) input.value = url;
        var reset = card.querySelector('.reset-btn');
        if (!reset) {
            var wrapper = card.querySelector('.flex.gap-2');
            var btn = document.createElement('button');
            btn.className = 'reset-btn bg-red-50 text-red-500 px-3 py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors';
            btn.title = 'Revert to original';
            btn.textContent = 'Reset';
            btn.addEventListener('click', function () { resetImage(card); });
            wrapper.appendChild(btn);
        }
    }

    function saveImage(card) {
        var key = card.getAttribute('data-key');
        var url = card.querySelector('.url-input').value.trim();
        if (!url) { toast('Enter a URL or upload a file.', false); return; }
        var fd = new FormData();
        fd.append('action', 'save');
        fd.append('image_key', key);
        fd.append('override_url', url);
        fd.append('csrf_token', CSRF);
        api(fd, function (res) {
            if (res.status === 'success') { applyOverride(card, res.url); toast(res.message, true); }
            else toast(res.message, false);
        });
    }

    function resetImage(card) {
        var key = card.getAttribute('data-key');
        var fd = new FormData();
        fd.append('action', 'delete');
        fd.append('image_key', key);
        fd.append('csrf_token', CSRF);
        api(fd, function (res) {
            if (res.status === 'success') {
                var thumb = card.querySelector('.thumb');
                var orig = card.getAttribute('data-src') || '';
                if (thumb) {
                    thumb.style.display = '';
                    if (orig) thumb.src = orig;
                }
                card.classList.remove('custom');
                var badge = card.querySelector('.badge');
                if (badge) { badge.textContent = 'Original'; badge.className = 'badge bg-slate-100 text-slate-400 text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-md'; }
                var input = card.querySelector('.url-input');
                if (input) input.value = '';
                var reset = card.querySelector('.reset-btn');
                if (reset) reset.remove();
                toast(res.message, true);
            } else toast(res.message, false);
        });
    }

    document.addEventListener('click', function (e) {
        var save = e.target.closest('.save-btn');
        if (save) { saveImage(save.closest('.img-card')); return; }
        var reset = e.target.closest('.reset-btn');
        if (reset) { resetImage(reset.closest('.img-card')); return; }
    });

    document.addEventListener('change', function (e) {
        var input = e.target.closest('.file-input');
        if (!input || !input.files || !input.files[0]) return;
        var card = input.closest('.img-card');
        var key = card.getAttribute('data-key');
        var fd = new FormData();
        fd.append('action', 'upload');
        fd.append('image_key', key);
        fd.append('file', input.files[0]);
        fd.append('csrf_token', CSRF);
        toast('Uploading…', true);
        api(fd, function (res) {
            if (res.status === 'success') { applyOverride(card, res.url); toast(res.message, true); }
            else toast(res.message, false);
            input.value = '';
        });
    });

    // Search filter
    var search = document.getElementById('imgSearch');
    if (search) {
        search.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('.img-group').forEach(function (group) {
                var page = group.getAttribute('data-page').toLowerCase();
                var cardMatch = false;
                group.querySelectorAll('.img-card').forEach(function (card) {
                    var label = card.querySelector('.group-label') ? card.querySelector('.group-label').textContent.toLowerCase() : '';
                    var srcEl = card.querySelector('.src-text');
                    var src = srcEl ? (srcEl.getAttribute('title') || '').toLowerCase() : '';
                    var url = (card.querySelector('.url-input').value || '').toLowerCase();
                    var hit = label.indexOf(q) !== -1 || src.indexOf(q) !== -1 || url.indexOf(q) !== -1 || page.indexOf(q) !== -1;
                    card.style.display = hit ? '' : 'none';
                    if (hit) cardMatch = true;
                });
                group.style.display = cardMatch ? '' : 'none';
            });
        });
    }
})();
</script>
</body>
</html>
