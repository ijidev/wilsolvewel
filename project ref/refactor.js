const fs = require('fs');
const path = require('path');

const baseDir = path.resolve(__dirname, '..');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        if (file === 'node_modules' || file === '.git' || file === 'components') return;
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(filePath));
        } else if (filePath.endsWith('.html')) {
            results.push(filePath);
        }
    });
    return results;
}

const htmlFiles = walk(baseDir);

htmlFiles.forEach(filePath => {
    let content = fs.readFileSync(filePath, 'utf8');

    const relPath = path.relative(baseDir, filePath).replace(/\\/g, '/');
    const parts = relPath.split('/');
    const depth = parts.length - 1;
    const dataRoot = depth === 0 ? './' : '../'.repeat(depth);

    let modified = false;

    if (relPath.startsWith('admin/')) {
        // Admin Sidebar
        if (content.match(/<aside[^>]*>[\s\S]*?<\/aside>/i)) {
            const match = content.match(/<aside[^>]*>[\s\S]*?<\/aside>/i);
            if (match && !match[0].includes('components/admin_sidenav.js')) {
                const placeholder = `<!-- SideNavBar --><script src="${dataRoot}components/admin_sidenav.js" data-root="${dataRoot}"></script>`;
                content = content.replace(match[0], placeholder);
                modified = true;
            }
        }
        // Admin Topnav
        if (content.match(/<header[^>]*>[\s\S]*?<\/header>/i)) {
            const match = content.match(/<header[^>]*>[\s\S]*?<\/header>/i);
            if (match && !match[0].includes('components/admin_topnav.js')) {
                const placeholder = `<!-- TopNavBar --><script src="${dataRoot}components/admin_topnav.js"></script>`;
                content = content.replace(match[0], placeholder);
                modified = true;
            }
        }
    } else if (relPath.startsWith('client/')) {
        // Client Sidebar
        if (content.match(/<aside[^>]*>[\s\S]*?<\/aside>/i)) {
            const match = content.match(/<aside[^>]*>[\s\S]*?<\/aside>/i);
            if (match && !match[0].includes('components/client_sidenav.js')) {
                const placeholder = `<!-- SideNavBar --><script src="${dataRoot}components/client_sidenav.js" data-root="${dataRoot}"></script>`;
                content = content.replace(match[0], placeholder);
                modified = true;
            }
        }
        // Client Topnav
        if (content.match(/<nav[^>]*>[\s\S]*?<\/nav>/i)) {
            const match = content.match(/<nav[^>]*>[\s\S]*?<\/nav>/i);
            if (match && !match[0].includes('components/client_topnav.js')) {
                const placeholder = `<!-- TopNavBar --><script src="${dataRoot}components/client_topnav.js"></script>`;
                content = content.replace(match[0], placeholder);
                modified = true;
            }
        }
    } else {
        // Public pages
        if (content.match(/<nav[^>]*>[\s\S]*?<\/nav>/i)) {
            const match = content.match(/<nav[^>]*>[\s\S]*?<\/nav>/i);
            if (match && !match[0].includes('components/header.js')) {
                const placeholder = `<!-- Top Navigation Shell --><script src="${dataRoot}components/header.js" data-root="${dataRoot}"></script>`;
                content = content.replace(match[0], placeholder);
                modified = true;
            }
        }

        if (content.match(/<footer[^>]*>[\s\S]*?<\/footer>/i)) {
            const match = content.match(/<footer[^>]*>[\s\S]*?<\/footer>/i);
            if (match && !match[0].includes('components/footer.js')) {
                const placeholder = `<!-- Footer Shell --><script src="${dataRoot}components/footer.js" data-root="${dataRoot}"></script>`;
                content = content.replace(match[0], placeholder);
                modified = true;
            }
        }
    }

    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Updated ${relPath}`);
    }
});

console.log("Refactoring complete.");
