<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }

    $name = $_POST['name'];
    $email = $_POST['email'];

    $update_sql = "UPDATE admins SET name=?, email=?";
    $types = "ss";
    $params = [$name, $email];

    if (!empty($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_sql .= ", password=?";
        $types .= "s";
        $params[] = $pass;
    }

    // Handle avatar upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/avatars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $tmp_name = $_FILES['avatar']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed)) {
            $new_filename = 'admin_' . $admin_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                $avatar_path = 'uploads/avatars/' . $new_filename;
                $update_sql .= ", avatar=?";
                $types .= "s";
                $params[] = $avatar_path;
            }
        }
    }

    $update_sql .= " WHERE id=?";
    $types .= "i";
    $params[] = $admin_id;

    $stmt = $conn->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            log_audit($conn, 'Update', 'Profile', 'Admin', $admin_id, "Admin updated their own profile");
            $success_msg = "Profile updated successfully.";
        } else {
            $error_msg = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Error preparing update: " . $conn->error;
    }
}

// Fetch current admin data
$stmt = $conn->prepare("SELECT a.*, d.name as dept_name FROM admins a LEFT JOIN departments d ON a.department_id = d.id WHERE a.id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$res = $stmt->get_result();
$admin = $res->fetch_assoc();
$stmt->close();
$initials = strtoupper(substr($admin['name'], 0, 1) . (strpos($admin['name'], ' ') ? substr($admin['name'], strpos($admin['name'], ' ')+1, 1) : ''));
$avatar_url = $admin['avatar'] ?? '';

$permissions = get_admin_permissions($admin_id);

$page_title = 'My Profile';
$page_subtitle = 'Personal Account Settings & Staff ID';
$page_header_actions = '<button onclick="window.print()" class="bg-slate-900 text-white px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-slate-800 transition-colors">
    <span class="material-symbols-outlined text-sm">print</span> PRINT ID CARD
</button>';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>My Profile | Terminal</title>
    <script>
        window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;
        window.WILSOLVEWEL_AVATAR = <?php echo json_encode($avatar_url); ?>;
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet" />
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                "primary": "#EAB308",
                "on-primary": "#000000",
                "surface": "#F8FAFC",
                "on-surface": "#0F172A"
            },
            "fontFamily": {
                "headline": ["Space Grotesk"],
                "body": ["Manrope"]
            }
          },
        },
      }
    </script>
    <style>
        /* Print Styles for ID Card */
        @media print {
            body * { visibility: hidden; }
            #printable-id-card, #printable-id-card * { visibility: visible; }
            #printable-id-card { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); margin: 0; width: 3.375in; height: 2.125in; border-radius: 0.25in; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        /* Web View ID Card Sizing (Standard CR80 size approx) */
        .id-card-wrapper {
            width: 100%; max-width: 380px; aspect-ratio: 1.586;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">
    
    <script src="../components/admin_sidenav.js" data-root="../"></script>

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6 lg:p-12">
            
            <div class="max-w-6xl mx-auto grid grid-cols-1 xl:grid-cols-12 gap-8">
                
                <!-- Profile Edit -->
                <div class="xl:col-span-7">
                    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                            <h3 class="font-bold text-slate-900">Update Information</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">General & Security Settings</p>
                        </div>
                        
                        <?php if (isset($success_msg)): ?>
                            <div class="mx-8 mt-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl text-xs font-bold flex items-center gap-3">
                                <span class="material-symbols-outlined">check_circle</span> <?php echo $success_msg; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                            <?= get_csrf_field() ?>
                            
                            <!-- Avatar Upload Section -->
                            <div class="flex items-center gap-6 pb-6 border-b border-slate-50">
                                <div class="w-20 h-20 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold text-2xl uppercase overflow-hidden shadow-md">
                                    <?php if ($avatar_url): ?>
                                        <img src="../<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?php echo $initials; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="space-y-2 flex-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Profile Avatar</label>
                                    <input type="file" name="avatar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-slate-900 hover:file:bg-primary/20 transition-all cursor-pointer">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                </div>
                            </div>
                            
                            <div class="pt-6 border-t border-slate-50">
                                <div class="space-y-2 max-w-sm">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                                    <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full bg-slate-50 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-primary">
                                    <p class="text-[9px] text-slate-400 mt-1 italic ml-1">Must be at least 8 characters for high security.</p>
                                </div>
                            </div>

                            <div class="pt-8 flex justify-end">
                                <button type="submit" name="update_profile" class="bg-primary text-on-primary px-10 py-4 rounded-2xl font-bold text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Virtual ID Card -->
                <div class="xl:col-span-5 flex flex-col items-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 w-full text-center">Staff Virtual ID Credential</p>
                    
                    <!-- The ID Card (Styled for both Screen and Print) -->
                    <div id="printable-id-card" class="id-card-wrapper bg-slate-900 rounded-2xl overflow-hidden relative shadow-2xl flex flex-col text-white print:bg-slate-900">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 16px 16px;"></div>
                        <div class="absolute top-0 right-0 w-32 h-32 bg-primary rounded-bl-full opacity-20"></div>
                        
                        <!-- Card Header -->
                        <div class="relative z-10 px-6 py-4 flex justify-between items-start border-b border-slate-700/50">
                            <div>
                                <h3 class="text-sm font-bold font-headline tracking-widest uppercase">Wilsolvewel</h3>
                                <p class="text-[8px] font-bold text-primary uppercase tracking-[0.2em] mt-0.5">Engineering Division</p>
                            </div>
                            <span class="material-symbols-outlined text-slate-500">qr_code_2</span>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="relative z-10 flex-1 px-6 py-4 flex items-center gap-5">
                            <div class="w-16 h-16 rounded-xl bg-slate-800 border-2 border-slate-700 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                                <?php if ($avatar_url): ?>
                                    <img src="../<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-2xl font-bold uppercase"><?php echo $initials; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Authorized Personnel</p>
                                <h2 class="text-lg font-bold font-headline leading-tight truncate"><?php echo htmlspecialchars($admin['name']); ?></h2>
                                <p class="text-[10px] font-bold text-primary mt-1 truncate uppercase tracking-widest"><?php echo htmlspecialchars($admin['role']); ?> &bull; <?php echo htmlspecialchars($admin['dept_name'] ?: 'Executive'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="relative z-10 px-6 py-3 bg-slate-950 flex justify-between items-center mt-auto">
                            <div>
                                <p class="text-[7px] text-slate-500 uppercase tracking-widest">Staff ID</p>
                                <p class="text-[10px] font-mono font-bold tracking-widest">WSE-S<?php echo str_pad($admin['id'], 5, '0', STR_PAD_LEFT); ?></p>
                            </div>
                            <div>
                                <p class="text-[7px] text-slate-500 uppercase tracking-widest text-right">Status</p>
                                <p class="text-[10px] font-mono font-bold tracking-widest text-emerald-500 text-right"><?php echo htmlspecialchars($admin['status']); ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- End ID Card -->

                    <div class="bg-primary/5 border border-primary/20 rounded-2xl p-6 flex gap-4 mt-8 w-full max-w-[380px]">
                        <span class="material-symbols-outlined text-primary text-2xl">info</span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 font-headline mb-1">About Staff ID Cards</h4>
                            <p class="text-xs text-slate-600 leading-relaxed">This virtual ID card serves as an official credential for site access. Click "Print ID Card" in the header to generate a PDF formatted specifically for CR80 standard card printers.</p>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>
</body>
</html>
