<?php
require_once '../config.php';
secure_session_start();

// Already logged in
if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$conn = get_db_connection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Invalid form submission. Please reload and try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $error = 'Email and password are required.';
        } else {
            $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? AND status = 'Active' LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $result->num_rows > 0) {
                    $admin = $result->fetch_assoc();
                    $stmt->close();
                    if (password_verify($password, $admin['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['admin_id']     = $admin['id'];
                        $_SESSION['admin_name']   = $admin['name'];
                        $_SESSION['admin_email']  = $admin['email'];
                        $_SESSION['admin_role']   = $admin['role'];
                        $_SESSION['admin_avatar'] = $admin['avatar'];
                        $_SESSION['is_impersonating'] = false;
                        $_SESSION['last_regenerated'] = time();

                        log_audit($conn, 'Login', 'Auth', 'Admin', $admin['id'], 'Admin logged in: ' . $admin['email']);
                        header('Location: index.php');
                        exit;
                    } else {
                        $error = 'Invalid credentials. Please try again.';
                    }
                } else {
                    $stmt->close();
                    $error = 'Invalid credentials. Please try again.';
                }
            } else {
                $error = 'System error. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Login | Wilsolvewel Terminal</title>
    <meta name="description" content="Secure administrator login for Wilsolvewel Terminal operations portal."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { primary: '#EAB308', 'on-primary': '#000000' },
                fontFamily: { headline: ['Outfit'], body: ['Manrope'] }
            }}
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20; }
        .grid-bg { background-image: radial-gradient(circle, #e2e8f0 1px, transparent 1px); background-size: 28px 28px; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .float-anim { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="bg-slate-50 font-body min-h-screen flex items-center justify-center relative overflow-hidden">

<div class="absolute inset-0 grid-bg opacity-60 pointer-events-none"></div>
<div class="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
<div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-slate-200/40 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

<div class="relative z-10 w-full max-w-md px-6">

    <!-- Logo -->
    <div class="text-center mb-10 float-anim">
        <div class="w-16 h-16 bg-slate-900 rounded-[1.5rem] flex items-center justify-center mx-auto mb-5 shadow-2xl shadow-slate-900/30">
            <span class="material-symbols-outlined text-primary text-3xl">terminal</span>
        </div>
        <h1 class="text-3xl font-headline font-extrabold text-slate-900 tracking-tight">Wilsolvewel</h1>
        <p class="text-[10px] font-bold tracking-[0.3em] text-primary uppercase mt-1">Terminal Admin Portal</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200 border border-slate-100 p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-headline font-black text-slate-900">Sign In</h2>
            <p class="text-sm text-slate-400 font-medium mt-1">Enter your credentials to access the command center.</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl">
            <span class="material-symbols-outlined text-sm shrink-0">error</span>
            <p class="text-xs font-bold"><?= htmlspecialchars($error) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate class="space-y-5">
            <?= get_csrf_field() ?>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2" for="email">Email Address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[20px]">mail</span>
                    <input id="email" name="email" type="email" required autocomplete="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="admin@wilsolvewel.com"
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2" for="password">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[20px]">lock</span>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium text-slate-900 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    <button type="button" onclick="togglePwd()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-600 transition-colors">
                        <span id="pwd-eye" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-primary text-on-primary py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 mt-2">
                <span class="material-symbols-outlined text-[20px]">login</span> Sign In to Terminal
            </button>
        </form>
    </div>

    <p class="text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest mt-8">
        Wilsolvewel &copy; <?= date('Y') ?> &bull; Secure Operations
    </p>
</div>

<script>
function togglePwd() {
    var pwd = document.getElementById('password');
    var eye = document.getElementById('pwd-eye');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        eye.textContent = 'visibility_off';
    } else {
        pwd.type = 'password';
        eye.textContent = 'visibility';
    }
}
</script>
</body>
</html>
