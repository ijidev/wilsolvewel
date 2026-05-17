<?php
require_once 'config.php';
secure_session_start();

$conn = get_db_connection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Invalid form submission. Please reload and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            $stmt = $conn->prepare("SELECT id, name, password, status FROM clients WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && $res->num_rows > 0) {
                    $client = $res->fetch_assoc();
                    $stmt->close();
                    if ($client['status'] !== 'Active') {
                        $error = "Account is suspended or pending setup. Please contact support.";
                    } else if (password_verify($password, $client['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['client_id'] = $client['id'];
                        $_SESSION['client_name'] = $client['name'];
                        $_SESSION['last_regenerated'] = time();
                        
                        log_audit($conn, 'Login', 'Auth', 'Client', $client['id'], "Client logged in successfully.");
                        
                        header("Location: client/index.php");
                        exit;
                    } else {
                        $error = "Invalid credentials.";
                    }
                } else {
                    $stmt->close();
                    $error = "Invalid credentials.";
                }
            } else {
                $error = "System error. Please try again.";
            }
        } else {
            $error = "Please enter email and password.";
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Wilsovlewel Engineering | Client Login</title>
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
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .technical-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.05; }
        .site-gradient-bg { background: radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.05) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(0, 0, 0, 0.05) 0%, transparent 50%); background-attachment: fixed; }
        .glass-morphic { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); }
    </style>
</head>

<body class="bg-surface font-body text-on-surface site-gradient-bg min-h-screen flex flex-col">
    <!-- Top Navigation Shell -->
    <script src="components/header.js" data-root="./"></script>

    <main class="relative flex-1 flex flex-col items-center justify-center pt-20 pb-12">
        <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

        <div class="relative z-10 w-full max-w-6xl px-6 grid md:grid-cols-12 gap-12 items-center">
            <!-- Left Side -->
            <div class="hidden md:block md:col-span-7">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-primary/10 rounded-full mb-6 border border-primary/20">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-[10px] font-headline font-bold uppercase tracking-widest text-primary">Client Gateway</span>
                </div>
                <h1 class="font-headline font-bold text-on-surface tracking-tighter mb-8">
                    <span class="text-5xl lg:text-7xl leading-[1.1] block">Client <span class="text-primary">Terminal</span></span>
                    <span class="text-2xl lg:text-3xl text-on-surface-variant/50 font-medium mt-3 block tracking-[0.15em] uppercase">Access Portal</span>
                </h1>
                <div class="space-y-6 max-w-md">
                    <div class="flex items-center gap-4 text-on-surface-variant">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">security</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-widest font-bold font-label">Encrypted Access</span>
                            <span class="block text-[10px] opacity-60">Secure viewing of your project logs and tickets.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="md:col-span-5 space-y-6">
                <div class="glass-morphic border border-white shadow-2xl rounded-2xl p-8 lg:p-10">
                    <div class="mb-8">
                        <h2 class="font-headline text-2xl font-bold text-on-surface tracking-tight mb-2">Authenticate</h2>
                        <p class="text-on-surface-variant text-[11px] font-medium leading-relaxed opacity-70">
                            Log in to view project progress, submit tickets, and manage settings.
                        </p>
                    </div>

                    <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-error/10 text-error rounded-lg border border-error/20 flex gap-3">
                        <span class="material-symbols-outlined">warning</span>
                        <div class="text-xs font-bold"><?= htmlspecialchars($error) ?></div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-6">
                        <?= get_csrf_field() ?>
                        <div class="space-y-2">
                            <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold px-1">Email Address</label>
                            <input name="email" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg py-3 px-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="client@company.com" type="email" required />
                        </div>
                        <div class="space-y-2">
                            <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold px-1">Access Key (Password)</label>
                            <input name="password" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg py-3 px-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="••••••••••••" type="password" required />
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-label uppercase tracking-widest font-bold">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary/20" />
                                <span class="group-hover:text-primary transition-colors">Remember Me</span>
                            </label>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-primary text-on-primary py-4 rounded-xl font-label font-bold uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all shadow-lg shadow-primary/20">
                                Client Login
                            </button>
                        </div>
                    </form>
                </div>

                <div class="glass-morphic p-6 border border-white/40 rounded-2xl shadow-sm flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="font-label text-[9px] uppercase tracking-widest text-on-surface font-bold">System Status: Nominal</span>
                        </div>
                        <span class="font-headline text-sm font-bold text-on-surface block">Active Terminal</span>
                    </div>
                    <span class="material-symbols-outlined text-primary opacity-40 text-3xl">hub</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Shell -->
    <script src="components/footer.js" data-root="./"></script>
</body>
</html>
