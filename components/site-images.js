(function () {
    var currentScript = document.currentScript;
    var rootPath = './';
    if (currentScript && currentScript.getAttribute('data-root')) {
        rootPath = currentScript.getAttribute('data-root');
    }

    // Resolve the page's site-relative path (e.g. "services/engineering.php", "index.php")
    function pagePath() {
        try {
            var apiUrl = rootPath + 'api/site_images.php';
            var apiAbs = new URL(apiUrl, window.location.href).pathname;
            var siteRoot = apiAbs.slice(0, apiAbs.lastIndexOf('/api/'));
            var rel = window.location.pathname.replace(siteRoot, '').replace(/^\/+/, '');
            return rel === '' ? 'index.php' : rel;
        } catch (e) {
            var p = window.location.pathname.split('/').pop();
            return p || 'index.php';
        }
    }

    fetch(rootPath + 'api/site_images.php', { cache: 'no-store' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var map = (data && data.images) || {};
            var keys = Object.keys(map);
            if (keys.length === 0) return;
            var file = pagePath();
            var baseUrl = null;
            try { baseUrl = new URL(rootPath === './' ? '' : rootPath, window.location.origin); } catch (e) {}
            document.querySelectorAll('img').forEach(function (img) {
                var src = img.getAttribute('src');
                if (!src) return;
                var key = file + ':' + src;
                if (map[key]) {
                    img.setAttribute('src', baseUrl ? new URL(map[key], baseUrl).href : map[key]);
                }
            });
        })
        .catch(function () {});
})();
