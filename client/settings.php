<?php
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: ../client-login.php");
    exit();
}
$conn = get_db_connection();

$message = '';
$error = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $company = $conn->real_escape_string($_POST['company']);
    $address = $conn->real_escape_string($_POST['address']);

    $sql = "UPDATE clients SET name = '$name', email = '$email', company = '$company', address = '$address' WHERE id = $client_id";
    if ($conn->query($sql)) {
        $message = "Profile updated successfully.";
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}

// Handle Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    $res = $conn->query("SELECT password FROM clients WHERE id = $client_id");
    $user = $res->fetch_assoc();

    if ($new_pass !== $confirm_pass) {
        $error = "New passwords do not match.";
    } elseif (!password_verify($current_pass, $user['password'])) {
        $error = "Current password is incorrect.";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        if ($conn->query("UPDATE clients SET password = '$hashed_pass' WHERE id = $client_id")) {
            $message = "Password updated successfully.";
        } else {
            $error = "Error updating password.";
        }
    }
}

// Handle Profile Picture Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $target_dir = "../uploads/clients/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_ext = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . "client_" . $client_id . "_" . time() . "." . $file_ext;
    
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $db_path = "uploads/clients/" . basename($target_file);
        $conn->query("UPDATE clients SET profile_pic = '$db_path' WHERE id = $client_id");
        $message = "Profile picture updated.";
    } else {
        $error = "Failed to upload image.";
    }
}

// Fetch Client Info
$client_res = $conn->query("SELECT * FROM clients WHERE id = $client_id");
$client = $client_res->fetch_assoc();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Wilsovlewel | Client Settings</title>
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
                        "surface": "#FDFDFD",
                        "on-surface": "#1A1A1A",
                    },
                    fontFamily: { "headline": ["Space Grotesk"], "body": ["Manrope"], "label": ["Space Grotesk"] }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .technical-grid { background-image: radial-gradient(circle, #EAB308 1px, transparent 1px); background-size: 24px 24px; opacity: 0.05; }
        .site-gradient-bg { background: radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.05) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(0, 0, 0, 0.05) 0%, transparent 50%); background-attachment: fixed; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface site-gradient-bg">
    <!-- TopNavBar -->
    <script src="../components/client_topnav.js" data-root="../"></script>
    <!-- SideNavBar -->
    <script src="../components/client_sidenav.js" data-root="../"></script>
    <script src="../components/effects.js"></script>
    <div class="fixed inset-0 technical-grid pointer-events-none z-0"></div>

    <main class="lg:ml-64 pt-20 px-6 pb-8 max-w-[1400px] relative z-10">
        <div class="mb-8 space-y-1">
            <h1 class="text-2xl font-bold font-headline tracking-tight text-on-surface">Account Settings</h1>
            <p class="text-slate-500 font-body text-xs">Update your company profile and security credentials.</p>
        </div>

        <?php if ($message): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm">
            <?= $message ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-error-container border border-error text-on-error-container rounded-xl font-bold text-sm">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            <!-- Left Column -->
            <div class="md:col-span-8 space-y-8">
                <!-- Profile Picture Section -->
                <section class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-8">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-slate-50 shadow-inner">
                                <img src="../<?= $client['profile_pic'] ?: 'assets/default-avatar.png' ?>" class="w-full h-full object-cover">
                            </div>
                            <label class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                <span class="material-symbols-outlined">photo_camera</span>
                                <form method="POST" enctype="multipart/form-data" class="hidden">
                                    <input type="file" name="profile_pic" onchange="this.form.submit()">
                                </form>
                            </label>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold font-headline">Profile Avatar</h3>
                            <p class="text-sm text-slate-500">JPG, PNG or GIF. Max size 2MB.</p>
                        </div>
                    </div>
                </section>

                <!-- Company Details Form -->
                <section class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <span class="font-headline text-[10px] uppercase tracking-widest text-primary font-bold">Protocol 01</span>
                            <h2 class="text-xl font-bold font-headline mt-1">Company Profile</h2>
                        </div>
                        <span class="material-symbols-outlined text-slate-300">domain</span>
                    </div>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Contact Name</label>
                                <input name="name" class="bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all" type="text" value="<?= htmlspecialchars($client['name']) ?>" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Email Address</label>
                                <input name="email" class="bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all" type="email" value="<?= htmlspecialchars($client['email']) ?>" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Company Name</label>
                                <input name="company" class="bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all" type="text" value="<?= htmlspecialchars($client['company']) ?>" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Account ID</label>
                                <input class="bg-slate-100 border-none rounded-xl p-4 text-sm font-mono text-slate-400" disabled type="text" value="CLI-<?= str_pad($client['id'], 4, '0', STR_PAD_LEFT) ?>" />
                            </div>
                            <div class="md:col-span-2 flex flex-col gap-2">
                                <label class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Physical Address</label>
                                <textarea name="address" class="bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all" rows="2"><?= htmlspecialchars($client['address']) ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="bg-primary text-on-primary px-8 py-4 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Save Profile</button>
                    </form>
                </section>
            </div>

            <!-- Right Column -->
            <div class="md:col-span-4 space-y-8">
                <!-- Security Card -->
                <section class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="mb-8">
                        <span class="font-headline text-[10px] uppercase tracking-widest text-primary font-bold">Protocol 02</span>
                        <h2 class="text-xl font-bold font-headline mt-1">Security</h2>
                    </div>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="update_password" value="1">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Current Password</label>
                            <input type="password" name="current_password" required class="w-full bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">New Password</label>
                            <input type="password" name="new_password" required class="w-full bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Confirm Password</label>
                            <input type="password" name="confirm_password" required class="w-full bg-slate-50 border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20">
                        </div>
                        <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-slate-800 transition-all">Update Password</button>
                    </form>
                </section>

                <!-- Status Card -->
                <section class="bg-primary p-8 rounded-3xl shadow-lg text-on-primary">
                    <span class="material-symbols-outlined text-4xl mb-4">verified_user</span>
                    <h3 class="text-lg font-bold font-headline mb-2">Identity Verified</h3>
                    <p class="text-sm opacity-80 mb-6">Your account is fully verified and connected to the central maintenance terminal.</p>
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Terminal Link: Operational
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
