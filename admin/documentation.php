<?php
include '../config.php';
// For sidebar rendering consistency
if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;
$permissions = get_admin_permissions($admin_id);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Step-by-Step Walkthrough | Terminal</title>
    <script>window.WILSOLVEWEL_PERMISSIONS = <?php echo json_encode($permissions); ?>;</script>
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
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20; }
        html { scroll-behavior: smooth; }
        .doc-section { scroll-margin-top: 5rem; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface overflow-hidden h-screen lg:pl-64 flex">
    
    <script src="../components/admin_sidenav.js" data-root="../"></script>

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- Top Nav -->
        <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 relative z-20">
            <div class="flex flex-col">
                <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Admin Walkthrough</h1>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">A simple guide for managing the platform</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-on-primary font-bold text-xs">JD</div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 flex overflow-hidden">
            
            <!-- Side Navigation -->
            <aside class="w-64 bg-white border-r border-slate-50 overflow-y-auto custom-scrollbar p-6 shrink-0 hidden md:block">
                <nav class="space-y-8">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">The Walkthrough</p>
                        <div class="space-y-4">
                            <a href="#welcome" class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-base">handshake</span> Welcome
                            </a>
                            <a href="#inquiries-step" class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-base">forum</span> 1. Managing Messages
                            </a>
                            <a href="#team-step" class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-base">groups</span> 2. Building your Team
                            </a>
                            <a href="#projects-step" class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-base">folder_special</span> 3. Projects & Assets
                            </a>
                            <a href="#permissions-step" class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-base">lock_person</span> 4. Security & Access
                            </a>
                            <a href="#settings-step" class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-base">settings</span> 5. System Settings
                            </a>
                        </div>
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar p-8 lg:p-12 bg-white">
                <div class="max-w-3xl mx-auto space-y-24 pb-20">
                    
                    <!-- Welcome Section -->
                    <section id="welcome" class="doc-section space-y-6">
                        <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl text-primary">auto_awesome</span>
                        </div>
                        <h2 class="text-4xl font-bold font-headline text-slate-900 tracking-tight">Welcome to your Admin Terminal</h2>
                        <p class="text-base text-slate-600 leading-relaxed">
                            This guide is designed for non-technical users. We will walk you through exactly how to handle daily tasks, from replying to customers to managing your staff's access. No coding required.
                        </p>
                    </section>

                    <!-- Step 1: Inquiries -->
                    <section id="inquiries-step" class="doc-section space-y-8">
                        <div class="flex items-center gap-4">
                            <span class="text-5xl font-black text-slate-100 font-headline">01</span>
                            <h3 class="text-2xl font-bold font-headline text-slate-900">How to Manage Inquiries</h3>
                        </div>
                        <div class="space-y-6">
                            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                                <p class="text-sm font-bold text-slate-800 mb-4 italic underline decoration-primary decoration-4 underline-offset-4">The Process:</p>
                                <ol class="space-y-6">
                                    <li class="flex gap-4">
                                        <div class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold shrink-0">1</div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 mb-1">Select a Message</p>
                                            <p class="text-xs text-slate-500 leading-relaxed">On the **Inquiries** page, click any message card in the left column. The full project details will open on the right.</p>
                                        </div>
                                    </li>
                                    <li class="flex gap-4">
                                        <div class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold shrink-0">2</div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 mb-1">Assign to a Department</p>
                                            <p class="text-xs text-slate-500 leading-relaxed">Look for the **"Assign to Dept"** dropdown. Click it and choose which team should handle this project (e.g., "Operations"). This helps keep everyone organized.</p>
                                        </div>
                                    </li>
                                    <li class="flex gap-4">
                                        <div class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold shrink-0">3</div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 mb-1">Archive when Finished</p>
                                            <p class="text-xs text-slate-500 leading-relaxed">Once a request is handled, click the **Archive** icon (the box with an arrow) in the top right. This clears the message from your main view but keeps it in the database for your records.</p>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </section>

                    <!-- Step 2: Team -->
                    <section id="team-step" class="doc-section space-y-8">
                        <div class="flex items-center gap-4">
                            <span class="text-5xl font-black text-slate-100 font-headline">02</span>
                            <h3 class="text-2xl font-bold font-headline text-slate-900">Building Your Team</h3>
                        </div>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-t-4 border-t-primary">
                                    <p class="text-sm font-bold text-slate-900 mb-2">Creating a Department</p>
                                    <p class="text-xs text-slate-500 leading-relaxed mb-4">Go to the **Departments** page. Click **"REGISTER DEPT"**, type a name like "Safety Division", and save. You can now assign staff to this new group.</p>
                                </div>
                                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-t-4 border-t-slate-900">
                                    <p class="text-sm font-bold text-slate-900 mb-2">Adding Staff Members</p>
                                    <p class="text-xs text-slate-500 leading-relaxed mb-4">On any department card, click the **Person+ icon**. Select a staff member from the list to add them to that department. This automatically sets their permissions.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Step 3: Projects & Assets -->
                    <section id="projects-step" class="doc-section space-y-8">
                        <div class="flex items-center gap-4">
                            <span class="text-5xl font-black text-slate-100 font-headline">03</span>
                            <h3 class="text-2xl font-bold font-headline text-slate-900">Projects & Asset Tracking</h3>
                        </div>
                        <div class="space-y-6">
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-primary">
                                <p class="text-sm font-bold text-slate-900 mb-2">Ongoing Projects</p>
                                <p class="text-xs text-slate-500 leading-relaxed">The **Projects** page is where you track active site work. You can view blueprints, project status, and estimated completion dates here.</p>
                            </div>
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-slate-900">
                                <p class="text-sm font-bold text-slate-900 mb-2">Inventory (Assets)</p>
                                <p class="text-xs text-slate-500 leading-relaxed">The **Assets** page lists all company hardware and equipment. Use this to ensure your field teams have the tools they need for every job.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Step 4: Permissions -->
                    <section id="permissions-step" class="doc-section space-y-8">
                        <div class="flex items-center gap-4">
                            <span class="text-5xl font-black text-slate-100 font-headline">04</span>
                            <h3 class="text-2xl font-bold font-headline text-slate-900">Security & Access Control</h3>
                        </div>
                        <div class="bg-slate-900 text-white p-10 rounded-[3rem] space-y-6">
                            <h4 class="text-lg font-bold">How to use "Templates"</h4>
                            <p class="text-xs text-slate-400 leading-relaxed">Templates are like "roles". Instead of setting permissions for every single person, you create a template (e.g. "Manager") and apply it to a department.</p>
                            <div class="space-y-4 pt-4 border-t border-white/10">
                                <div class="flex gap-4">
                                    <span class="material-symbols-outlined text-primary">check_circle</span>
                                    <p class="text-xs text-slate-300">**Read Access**: They can see the page but can't click "Delete" or "Save".</p>
                                </div>
                                <div class="flex gap-4">
                                    <span class="material-symbols-outlined text-primary">check_circle</span>
                                    <p class="text-xs text-slate-300">**Write Access**: They have full power to edit and delete.</p>
                                </div>
                                <div class="flex gap-4">
                                    <span class="material-symbols-outlined text-primary">visibility_off</span>
                                    <p class="text-xs text-slate-300">**No Access**: If both are unchecked, the menu item disappears for them.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Step 5: Settings -->
                    <section id="settings-step" class="doc-section space-y-8">
                        <div class="flex items-center gap-4">
                            <span class="text-5xl font-black text-slate-100 font-headline">05</span>
                            <h3 class="text-2xl font-bold font-headline text-slate-900">System Setup (SMTP)</h3>
                        </div>
                        <div class="bg-white p-8 rounded-3xl border border-slate-100">
                            <p class="text-xs text-slate-500 leading-relaxed mb-6">If your internal emails (Forwarding) aren't sending, you need to check your SMTP settings.</p>
                            <div class="space-y-4">
                                <p class="text-xs font-bold text-slate-800">What to enter:</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 bg-slate-50 rounded-xl">
                                        <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Host</p>
                                        <p class="text-xs font-mono text-slate-600">e.g. smtp.gmail.com</p>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl">
                                        <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Port</p>
                                        <p class="text-xs font-mono text-slate-600">587 or 465</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </main>
        </div>
    </div>
</body>
</html>
