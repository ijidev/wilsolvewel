<?php
include '../config.php';

$conn = get_db_connection();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_smtp'])) {
    set_setting('smtp_host', $_POST['smtp_host']);
    set_setting('smtp_port', $_POST['smtp_port']);
    set_setting('smtp_user', $_POST['smtp_user']);
    if (!empty($_POST['smtp_pass'])) {
        set_setting('smtp_pass', $_POST['smtp_pass']);
    }
    set_setting('smtp_encryption', $_POST['smtp_encryption']);
    set_setting('smtp_from_email', $_POST['smtp_from_email']);
    set_setting('smtp_from_name', $_POST['smtp_from_name']);
    $success_msg = "SMTP settings updated successfully.";
}

// Fetch current settings
$smtp_host = get_setting('smtp_host');
$smtp_port = get_setting('smtp_port');
$smtp_user = get_setting('smtp_user');
$smtp_encryption = get_setting('smtp_encryption');
$smtp_from_email = get_setting('smtp_from_email');
$smtp_from_name = get_setting('smtp_from_name');

?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Wilsovlewel Terminal - System Settings</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#EAB308",
                        "on-primary": "#000000",
                        "primary-container": "#FEF9C3",
                        "on-primary-container": "#422006",
                        "secondary": "#1A1A1A",
                        "on-secondary": "#FFFFFF",
                        "surface": "#FDFDFD",
                        "on-surface": "#1A1A1A",
                        "surface-variant": "#F5F5F5",
                        "on-surface-variant": "#4A4A4A",
                        "outline": "#79747E",
                        "outline-variant": "#CAC4D0",
                        "error": "#B00020",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container-low": "#F7F7F7",
                        "surface-container": "#F3F3F3",
                        "surface-container-high": "#EFEFEF",
                        "surface-container-highest": "#EBEBEB",
                    },
                    fontFamily: {
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
        .technical-grid {
            background-image: radial-gradient(circle, #EAB308 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.05;
        }
        .anodized-gradient {
            background: linear-gradient(135deg, #1A1A1A 0%, #333333 100%);
        }
        .site-gradient-bg {
            background: radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(0, 0, 0, 0.05) 0%, transparent 50%);
            background-attachment: fixed;
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface selection:bg-primary selection:text-on-primary site-gradient-bg">
    <!-- SideNavBar -->
    <script src="../components/admin_sidenav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    
    <div class="ml-64 min-h-screen flex flex-col relative">
        <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>
        
        <!-- TopNavBar -->
        <script src="../components/admin_topnav.js" data-root="../"></script>
        
        <main class="flex-1 p-8 relative z-10">
            <div class="max-w-6xl mx-auto space-y-8 relative">
                <?php if (isset($success_msg)): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="text-sm font-bold"><?php echo $success_msg; ?></span>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-12 gap-8">
                    <!-- Left Sidebar Info (Profile Summary) -->
                    <div class="col-span-12 lg:col-span-4 space-y-6">
                        <section class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/10">
                            <div class="flex flex-col items-center text-center mb-8">
                                <div class="relative mb-4">
                                    <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-surface-container-low shadow-sm bg-primary/10 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-5xl text-primary">person</span>
                                    </div>
                                </div>
                                <h3 class="font-headline text-xl font-bold">Terminal Admin</h3>
                                <p class="font-label text-[10px] uppercase tracking-widest text-primary font-bold">Master Control Node</p>
                            </div>
                            <nav class="space-y-1">
                                <a href="settings.php" class="w-full flex items-center justify-between p-3 bg-primary/10 rounded-lg text-primary font-bold transition-all">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-lg">settings</span>
                                        <span class="text-sm">System Settings</span>
                                    </div>
                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                </a>
                                <a href="inquiries.php" class="w-full flex items-center justify-between p-3 text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-all">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-lg">mail</span>
                                        <span class="text-sm">Inquiries Log</span>
                                    </div>
                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                </a>
                            </nav>
                        </section>
                    </div>

                    <!-- Right Main Content -->
                    <div class="col-span-12 lg:col-span-8 space-y-8">
                        <!-- SMTP Configuration Section -->
                        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-outline-variant/10">
                            <div class="flex justify-between items-end mb-8">
                                <div>
                                    <h4 class="font-headline text-2xl font-bold tracking-tight">SMTP Gateway</h4>
                                    <p class="text-on-surface-variant text-sm mt-1 opacity-70">Configure your outgoing mail server for inquiries and alerts.</p>
                                </div>
                            </div>
                            
                            <form method="POST" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold ml-1">SMTP Host</label>
                                        <input name="smtp_host" type="text" value="<?php echo htmlspecialchars($smtp_host); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="smtp.gmail.com" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold ml-1">SMTP Port</label>
                                        <input name="smtp_port" type="text" value="<?php echo htmlspecialchars($smtp_port); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="587" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold ml-1">SMTP Username</label>
                                        <input name="smtp_user" type="text" value="<?php echo htmlspecialchars($smtp_user); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="your-email@gmail.com" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold ml-1">SMTP Password</label>
                                        <input name="smtp_pass" type="password" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="••••••••••••" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold ml-1">Encryption</label>
                                        <select name="smtp_encryption" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                                            <option value="tls" <?php echo $smtp_encryption == 'tls' ? 'selected' : ''; ?>>TLS / STARTTLS</option>
                                            <option value="ssl" <?php echo $smtp_encryption == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                            <option value="none" <?php echo $smtp_encryption == 'none' ? 'selected' : ''; ?>>None</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold ml-1">From Email</label>
                                        <input name="smtp_from_email" type="email" value="<?php echo htmlspecialchars($smtp_from_email); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="noreply@wilsolvewel.com" />
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold ml-1">Sender Name</label>
                                        <input name="smtp_from_name" type="text" value="<?php echo htmlspecialchars($smtp_from_name); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Wilsolvewel Engineering Notifications" />
                                    </div>
                                </div>
                                <button type="submit" name="save_smtp" class="w-full md:w-fit bg-primary text-on-primary px-8 py-3 rounded-lg font-label font-bold text-[10px] uppercase tracking-widest shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
                                    Save Gateway Config
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="mt-auto px-8 py-6 border-t border-surface-container-low text-[10px] font-label uppercase tracking-widest text-on-surface-variant opacity-50 text-center">
            © 2026 Wilsovlewel Engineering Systems | Internal Terminal
        </footer>
    </div>
</body>
</html>
