<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

$client_id = $_SESSION['client_id'] ?? 0;
if ($client_id === 0) {
    header("Location: login.php");
    exit();
}
$conn = get_db_connection();

$message = '';
$error = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid or expired CSRF token. Please reload the page and try again.';
    } else {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $company = $_POST['company'];
        $address = $_POST['address'];

        $stmt = $conn->prepare("UPDATE clients SET name = ?, email = ?, company = ?, address = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $company, $address, $client_id);
        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
        } else {
            $error = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid or expired CSRF token. Please reload the page and try again.';
    } else {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        $stmt = $conn->prepare("SELECT password FROM clients WHERE id = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        if ($new_pass !== $confirm_pass) {
            $error = "New passwords do not match.";
        } elseif (!password_verify($current_pass, $user['password'])) {
            $error = "Current password is incorrect.";
        } else {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE clients SET password = ? WHERE id = ?");
            $stmt2->bind_param("si", $hashed_pass, $client_id);
            if ($stmt2->execute()) {
                $message = "Password updated successfully.";
            } else {
                $error = "Error updating password.";
            }
            $stmt2->close();
        }
    }
}

// Handle Profile Picture Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Invalid or expired CSRF token. Please reload the page and try again.';
    } else {
        $target_dir = __DIR__ . "/../uploads/clients/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . "client_" . $client_id . "_" . time() . "." . $file_ext;
        
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $db_path = "uploads/clients/" . basename($target_file);
            $stmt = $conn->prepare("UPDATE clients SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $db_path, $client_id);
            $stmt->execute();
            $stmt->close();
            $message = "Profile picture updated.";
        } else {
            $error = "Failed to upload image.";
        }
    }
}

// Fetch Client Info
$stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$client_res = $stmt->get_result();
$stmt->close();
$client = $client_res->fetch_assoc();

$page_title = 'Wilsolvewel | Client Settings';
$page_h1 = 'Account Settings';
$page_h1_sub = 'Update your company profile and security credentials.';

ob_start();
?>

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
    <div class="md:col-span-8 space-y-8">
        <section class="bg-white p-4 sm:p-6 lg:p-8 rounded-3xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-8 text-center sm:text-left">
                <div class="relative group">
                    <form id="avatarForm" method="POST" enctype="multipart/form-data">
                        <?= get_csrf_field() ?>
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-slate-50 shadow-inner">
                            <img src="<?= $client['profile_pic'] ? '../' . htmlspecialchars($client['profile_pic']) : '../assets/default-avatar.png' ?>" class="w-full h-full object-cover" alt="Avatar">
                        </div>
                        <label for="profile_pic_input" class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                            <span class="material-symbols-outlined">photo_camera</span>
                        </label>
                        <input id="profile_pic_input" type="file" name="profile_pic" accept="image/*" class="hidden" onchange="document.getElementById('avatarForm').submit()">
                    </form>
                </div>
                <div>
                    <h3 class="text-xl font-bold font-headline">Profile Avatar</h3>
                    <p class="text-sm text-slate-500">JPG, PNG or GIF. Max size 2MB.</p>
                </div>
            </div>
        </section>

        <section class="bg-white p-4 sm:p-6 lg:p-8 rounded-3xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="font-headline text-[10px] uppercase tracking-widest text-primary font-bold">Protocol 01</span>
                    <h2 class="text-xl font-bold font-headline mt-1">Company Profile</h2>
                </div>
                <span class="material-symbols-outlined text-slate-300">domain</span>
            </div>
            <form method="POST" class="space-y-6">
                <?= get_csrf_field() ?>
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
                <button type="submit" class="bg-primary text-on-primary px-4 sm:px-8 py-4 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Save Profile</button>
            </form>
        </section>
    </div>

    <div class="md:col-span-4 space-y-8">
        <section class="bg-white p-4 sm:p-6 lg:p-8 rounded-3xl shadow-sm border border-slate-100">
            <div class="mb-8">
                <span class="font-headline text-[10px] uppercase tracking-widest text-primary font-bold">Protocol 02</span>
                <h2 class="text-xl font-bold font-headline mt-1">Security</h2>
            </div>
            <form method="POST" class="space-y-4">
                <?= get_csrf_field() ?>
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

        <section class="bg-primary p-4 sm:p-6 lg:p-8 rounded-3xl shadow-lg text-on-primary">
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
<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../components/client_layout.php';
