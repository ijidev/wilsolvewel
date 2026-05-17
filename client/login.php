<?php
require_once __DIR__ . '/../config.php';
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

                        header("Location: index.php");
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
    <script>
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
    </style>
</head>

<body class="bg-surface font-body text-on-surface min-h-screen flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="grid md:grid-cols-2 min-h-[32rem]">
            <!-- Col 1: Branding & Info -->
            <div class="bg-slate-900 p-10 lg:p-14 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px;"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 bg-primary/10 rounded-full mb-6 border border-primary/20">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-[10px] font-headline font-bold uppercase tracking-widest text-primary">Client Gateway</span>
                    </div>
                    <h1 class="font-headline font-bold text-white tracking-tighter mb-8">
                        <span class="text-4xl lg:text-5xl leading-[1.1] block">Client <span class="text-primary">Terminal</span></span>
                        <span class="text-xl lg:text-2xl text-white/30 font-medium mt-2 block tracking-[0.15em] uppercase">Access Portal</span>
                    </h1>
                    <div class="space-y-5">
                        <div class="flex items-center gap-4 text-white/60">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl">security</span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-widest font-bold font-label text-white/80">Encrypted Access</span>
                                <span class="block text-[10px] text-white/40">Secure viewing of your project logs and tickets.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-white/60">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl">description</span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-widest font-bold font-label text-white/80">Project Dashboard</span>
                                <span class="block text-[10px] text-white/40">Track milestones, timelines, and deliverables.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-white/60">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl">support</span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-widest font-bold font-label text-white/80">Support Tickets</span>
                                <span class="block text-[10px] text-white/40">Submit and track support requests in real time.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Col 2: Login Form -->
            <div class="p-10 lg:p-14 flex flex-col justify-center">
                <div class="w-full max-w-sm mx-auto">
                    <h2 class="font-headline text-2xl font-bold text-on-surface tracking-tight mb-1">Welcome back</h2>
                    <p class="text-on-surface-variant text-xs font-medium mb-8 opacity-70">Log in to your client portal.</p>

                    <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-error/10 text-error rounded-lg border border-error/20 flex gap-3">
                        <span class="material-symbols-outlined text-xl">warning</span>
                        <div class="text-xs font-bold"><?= htmlspecialchars($error) ?></div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-5">
                        <?= get_csrf_field() ?>
                        <div class="space-y-1.5">
                            <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Email Address</label>
                            <input name="email" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg py-3 px-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="client@company.com" type="email" required />
                        </div>
                        <div class="space-y-1.5">
                            <label class="font-label text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Access Key (Password)</label>
                            <div class="relative">
                                <input id="loginPassword" name="password" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg py-3 pl-4 pr-11 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="••••••••••••" type="password" required />
                                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant/50 hover:text-on-surface transition-colors">
                                    <span id="passwordToggleIcon" class="material-symbols-outlined text-lg">visibility</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-label uppercase tracking-widest font-bold">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary/20" />
                                <span class="group-hover:text-primary transition-colors">Remember Me</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full bg-primary text-on-primary py-4 rounded-xl font-label font-bold uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all shadow-lg shadow-primary/20">
                            Client Login
                        </button>
                    </form>

                    <div class="mt-6 p-4 bg-surface-container-low rounded-xl flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="font-label text-[9px] uppercase tracking-widest text-on-surface font-bold">System: Online</span>
                            </div>
                            <span class="font-headline text-sm font-bold text-on-surface block">Active Terminal</span>
                        </div>
                        <span class="material-symbols-outlined text-primary opacity-30 text-3xl">hub</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
function togglePassword() {
    var p = document.getElementById('loginPassword');
    var i = document.getElementById('passwordToggleIcon');
    if (p.type === 'password') {
        p.type = 'text';
        i.textContent = 'visibility_off';
    } else {
        p.type = 'password';
        i.textContent = 'visibility';
    }
}
</script>
</body>
</html>
