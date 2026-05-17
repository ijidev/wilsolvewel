<?php
require_once 'config.php';
secure_session_start();
$conn = get_db_connection();

$token = trim($_GET['token'] ?? '');
$step = 'verify'; // verify | setup | done | expired

$error = '';
$client = null;

if (!empty($token)) {
    $stmt = $conn->prepare("SELECT ct.*, c.name, c.email FROM client_password_tokens ct 
                          JOIN clients c ON ct.client_id = c.id 
                          WHERE ct.token = ? AND ct.used = 0 AND ct.expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    if ($res && $res->num_rows > 0) {
        $client = $res->fetch_assoc();
    } else {
        $step = 'expired';
    }
}

if ($step !== 'expired' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'setup' && $client) {
        $pass1 = $_POST['password'] ?? '';
        $pass2 = $_POST['confirm'] ?? '';
        if (strlen($pass1) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($pass1 !== $pass2) {
            $error = 'Passwords do not match.';
        } else {
            $hashed = password_hash($pass1, PASSWORD_DEFAULT);
            $cid = (int)$client['client_id'];

            $stmt = $conn->prepare("UPDATE clients SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $cid);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE client_password_tokens SET used = 1 WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();

            $step = 'done';
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Account Setup | Wilsolvewel Engineering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        body { background: #F8FAFC; font-family: 'Manrope', sans-serif; }
        /* Fallback Styles if Tailwind is blocked */
        .w-full { width: 100%; } .max-w-md { max-width: 28rem; margin: 0 auto; }
        .text-center { text-align: center; } .mb-10 { margin-bottom: 2.5rem; }
        .font-bold { font-weight: bold; } .text-2xl { font-size: 1.5rem; }
        .bg-white { background-color: #fff; } .rounded-\[3rem\] { border-radius: 3rem; }
        .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .p-8 { padding: 2rem; } .mb-4 { margin-bottom: 1rem; }
        .bg-slate-900 { background-color: #0f172a; color: white; }
        .text-slate-900 { color: #0f172a; } .text-slate-400 { color: #94a3b8; }
        .bg-primary { background-color: #EAB308; } .text-on-primary { color: #000000; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; } .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .rounded-2xl { border-radius: 1rem; } .uppercase { text-transform: uppercase; }
        input { box-sizing: border-box; width: 100%; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 1rem; margin-top: 0.5rem; }
        button[type="submit"] { width: 100%; padding: 1rem; background: #EAB308; color: black; border: none; border-radius: 1rem; font-weight: bold; cursor: pointer; margin-top: 1.5rem; }
    </style>
</head>
<body class="font-body min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold font-headline text-slate-900 tracking-tight">Wilsolvewel</h1>
            <p class="text-[10px] font-bold text-primary uppercase tracking-[0.25em] mt-1">Engineering Terminal</p>
        </div>

        <?php if ($step === 'expired'): ?>
        <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 p-10 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-3xl text-red-500">link_off</span>
            </div>
            <h2 class="text-xl font-bold font-headline text-slate-900 mb-2">Link Expired</h2>
            <p class="text-xs text-slate-500 leading-relaxed">This setup link has expired or already been used. Please contact your administrator to send a new link.</p>
        </div>

        <?php elseif ($step === 'done'): ?>
        <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 p-10 text-center">
            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-3xl text-emerald-500">check_circle</span>
            </div>
            <h2 class="text-xl font-bold font-headline text-slate-900 mb-2">Account Ready</h2>
            <p class="text-xs text-slate-500 leading-relaxed mb-8">Your password has been set successfully. You can now log in to your account.</p>
            <a href="/client/login.php" class="block w-full bg-primary text-on-primary py-4 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] hover:shadow-lg transition-all text-center">
                GO TO LOGIN
            </a>
        </div>

        <?php elseif ($client): ?>
        <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="bg-slate-900 p-8 text-center">
                <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold text-on-primary">
                    <?php echo strtoupper(substr($client['name'], 0, 1)); ?>
                </div>
                <h2 class="text-lg font-bold font-headline text-white">Hello, <?php echo htmlspecialchars(explode(' ', $client['name'])[0]); ?>!</h2>
                <p class="text-[10px] text-slate-400 mt-1"><?php echo htmlspecialchars($client['email']); ?></p>
            </div>

            <div class="p-8">
                <div class="mb-8 text-center">
                    <p class="text-sm font-bold text-slate-900 font-headline">Set Your Account Password</p>
                    <p class="text-xs text-slate-400 mt-1">Choose a strong password of at least 6 characters.</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-6 bg-red-50 border border-red-100 text-red-600 px-5 py-3 rounded-2xl text-xs font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">error</span> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-5">
                    <?= get_csrf_field() ?>
                    <input type="hidden" name="action" value="setup">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="pwd" required minlength="6" placeholder="Min 6 characters"
                                   class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-4 pr-12 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <button type="button" onclick="togglePwd('pwd','eyePwd')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <span id="eyePwd" class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="confirm" id="conf" required minlength="6" placeholder="Repeat password"
                                   class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-4 pr-12 text-xs font-bold focus:ring-1 focus:ring-primary">
                            <button type="button" onclick="togglePwd('conf','eyeConf')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <span id="eyeConf" class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Password strength indicator -->
                    <div>
                        <div class="flex gap-1 mt-1">
                            <div id="str1" class="h-1 flex-1 rounded-full bg-slate-100 transition-colors"></div>
                            <div id="str2" class="h-1 flex-1 rounded-full bg-slate-100 transition-colors"></div>
                            <div id="str3" class="h-1 flex-1 rounded-full bg-slate-100 transition-colors"></div>
                            <div id="str4" class="h-1 flex-1 rounded-full bg-slate-100 transition-colors"></div>
                        </div>
                        <p id="strLabel" class="text-[9px] text-slate-400 mt-1 font-bold uppercase tracking-widest">Strength: —</p>
                    </div>

                    <button type="submit" class="w-full bg-primary text-on-primary py-4 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] hover:shadow-lg transition-all mt-2">
                        SET PASSWORD & ACTIVATE
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function togglePwd(inputId, iconId) {
            const inp = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (inp.type === 'password') { inp.type = 'text'; icon.innerText = 'visibility_off'; }
            else { inp.type = 'password'; icon.innerText = 'visibility'; }
        }

        const pwd = document.getElementById('pwd');
        if (pwd) {
            pwd.addEventListener('input', function() {
                const v = this.value;
                let score = 0;
                if (v.length >= 6) score++;
                if (v.length >= 10) score++;
                if (/[A-Z]/.test(v) && /[0-9]/.test(v)) score++;
                if (/[^A-Za-z0-9]/.test(v)) score++;
                const colors = ['bg-red-400','bg-orange-400','bg-amber-400','bg-emerald-500'];
                const labels = ['Weak','Fair','Good','Strong'];
                for (let i = 1; i <= 4; i++) {
                    const el = document.getElementById('str' + i);
                    el.className = 'h-1 flex-1 rounded-full transition-colors ' + (i <= score ? colors[score-1] : 'bg-slate-100');
                }
                document.getElementById('strLabel').innerText = score > 0 ? 'Strength: ' + labels[score-1] : 'Strength: —';
            });
        }
    </script>
</body>
</html>
