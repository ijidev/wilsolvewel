<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }

    $title = $_POST['title'] ?? '';
    $type = $_POST['type'] ?? 'Routine';
    $severity = $_POST['severity'] ?? 'Low';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';

    $stmt = $conn->prepare("INSERT INTO hsse_observations (title, type, severity, location, description, inspector_id, status) VALUES (?, ?, ?, ?, ?, ?, 'Open')");
    if ($stmt) {
        $stmt->bind_param("sssssi", $title, $type, $severity, $location, $description, $admin_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: monitor.php?success=1");
            exit;
        }
        $stmt->close();
    }
}

// Fetch Projects for dropdown
$projects_res = $conn->query("SELECT id, name FROM projects ORDER BY name ASC");
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>New HSSE Observation | Industrial Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    "fontSize": {
    "xs": ["0.65rem", { "lineHeight": "1rem" }],
    "sm": ["0.75rem", { "lineHeight": "1.125rem" }],
    "base": ["0.875rem", { "lineHeight": "1.25rem" }],
    "lg": ["1rem", { "lineHeight": "1.5rem" }],
    "xl": ["1.125rem", { "lineHeight": "1.75rem" }],
    "2xl": ["1.25rem", { "lineHeight": "1.75rem" }],
    "3xl": ["1.5rem", { "lineHeight": "2rem" }],
    "4xl": ["1.875rem", { "lineHeight": "2.25rem" }],
    "5xl": ["2.25rem", { "lineHeight": "2.5rem" }],
    "6xl": ["3rem", { "lineHeight": "1" }],
    "7xl": ["3.75rem", { "lineHeight": "1" }]
    }
    },
    },
    },
    }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .technical-grid {
            background-image: radial-gradient(circle, #c2c6d4 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.05;
        }

        .anodized-gradient {
            background: linear-gradient(135deg, #00488d 0%, #005fb8 100%);
        }

        .site-gradient-bg {
            background: radial-gradient(circle at 0% 0%, rgba(0, 72, 141, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(173, 51, 0, 0.3) 0%, transparent 50%);
            background-attachment: fixed;
        }
    </style>
</head>

<body
    class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
    <!-- SideNavBar -->
    <script src="../../components/admin_sidenav.js" data-root="../../"></script>
    <script src="../../components/effects.js"></script>
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>
    <!-- Main Content Canvas -->
    <main class="lg:ml-64 pt-20 relative z-10 min-h-screen">
        <!-- TopNavBar -->
        <script src="../../components/admin_topnav.js" data-root="../../"></script>
        <div class="max-w-7xl mx-auto p-8 flex flex-col md:flex-row gap-8">
            <!-- Left Column: Primary Reporting Form -->
            <div class="flex-1 space-y-8">
                <!-- Header Section -->
                <section class="asymmetric-header translate-x-6">
                    <span class="text-primary font-label text-xs uppercase tracking-widest mb-2 block">Safety Protocol
                        4.02</span>
                    <h1 class="text-5xl font-headline font-bold text-on-surface tracking-tighter">New HSSE Observation
                    </h1>
                    <p class="text-on-surface-variant max-w-xl mt-4 body-md">Detailed incident and hazard documentation.
                        All reports are timestamped and logged directly into the Terminal node ledger.</p>
                </section>
                <!-- Form Section -->
                <form method="POST" action="" class="bg-surface-container-lowest rounded-md p-8 shadow-[0_40px_60px_-15px_rgba(0,0,0,0.04)] space-y-12">
                    <?= get_csrf_field() ?>
                    <!-- Observation Type Selection -->
                    <div class="space-y-6">
                        <label class="font-label text-[10px] uppercase tracking-[0.2em] text-slate-500">01. Observation Type & Severity</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <span class="text-sm font-medium text-on-surface">Type</span>
                                <select name="type" class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 font-body text-sm focus:ring-2 focus:ring-primary/20">
                                    <option value="Routine">Routine Check</option>
                                    <option value="Hazard">Hazard Detection</option>
                                    <option value="Incident">Near Miss / Incident</option>
                                    <option value="Audit">Safety Audit</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <span class="text-sm font-medium text-on-surface">Severity</span>
                                <select name="severity" class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 font-body text-sm focus:ring-2 focus:ring-primary/20">
                                    <option value="Low">Low (Routine)</option>
                                    <option value="Medium">Medium (Attention Required)</option>
                                    <option value="High">High (Immediate Action)</option>
                                    <option value="Critical">Critical (Emergency)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Details Section -->
                        <div class="space-y-2">
                            <span class="text-sm font-medium text-on-surface">Observation Title</span>
                            <input name="title" required
                                class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 font-body text-sm focus:ring-2 focus:ring-primary/20"
                                placeholder="Brief summary of the observation..." type="text" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <span class="text-sm font-medium text-on-surface">Project Context</span>
                                <div class="relative">
                                    <select name="project_id"
                                        class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 font-body text-sm appearance-none focus:ring-2 focus:ring-primary/20">
                                        <option value="">General Site / No Project</option>
                                        <?php while($p = $projects_res->fetch_assoc()): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <span
                                        class="material-symbols-outlined absolute right-4 top-3 text-slate-400 pointer-events-none">expand_more</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <span class="text-sm font-medium text-on-surface">Precision Location</span>
                                <input name="location"
                                    class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 font-body text-sm focus:ring-2 focus:ring-primary/20"
                                    placeholder="e.g., Sector 7G, North Wall" type="text" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <span class="text-sm font-medium text-on-surface">Event Description</span>
                            <textarea name="description" required
                                class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 font-body text-sm focus:ring-2 focus:ring-primary/20"
                                placeholder="Detail the specific sequence of events..." rows="4"></textarea>
                        </div>
                    </div>
                    <!-- Evidence Upload -->
                    <div class="space-y-6">
                        <label class="font-label text-[10px] uppercase tracking-[0.2em] text-slate-500">03. Digital
                            Evidence</label>
                        <div
                            class="border-2 border-dashed border-outline-variant/30 rounded-md p-12 flex flex-col items-center justify-center bg-slate-50/50 hover:bg-blue-50/30 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-4">cloud_upload</span>
                            <p class="font-headline font-bold text-on-surface">Drop high-resolution assets here</p>
                            <p class="text-xs text-on-surface-variant mt-2">RAW, JPG, MP4 (Max 500MB per file)</p>
                            <button
                                class="mt-6 px-6 py-2 bg-white text-primary font-headline font-bold text-sm rounded-sm border border-outline-variant/30 shadow-sm hover:bg-surface-container-low transition-colors">Select
                                Files</button>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div
                                class="aspect-square bg-surface-container-low rounded-sm overflow-hidden relative group">
                                <img class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"
                                    data-alt="close-up of industrial machinery gears with metallic texture and industrial grease"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCXMhM6A8XbecpFbwfueQ4WUeRViS1u_6ThDMUE5Aixw-tCR3_47Ua_f30xxtIO24cpFHGjNYygPXo7l7Yb-waA57gEWOqmm0Gwg7qEhUcU5M-IH1t5SIETT1xKR4QEO34LJC9VFwfJW4rz5UeEjQPMqGVX6xQHfOqSJ90DN0c1MP4rEWE-_HSPbuOpyFnARIoaj_7eWnaReEtTQKDbamFQ4sGHWyEPRXUXlbJQN_2CvX-8PcpXpt7qj-a0lIri8csvmpMSk26XX5Ic" />
                                <button
                                    class="absolute top-2 right-2 bg-black/40 text-white p-1 rounded-full backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-xs">close</span>
                                </button>
                            </div>
                            <div
                                class="aspect-square bg-surface-container-low rounded-sm overflow-hidden relative group border-2 border-primary/20">
                                <div class="w-full h-full flex items-center justify-center bg-slate-100">
                                    <span class="material-symbols-outlined text-primary text-3xl">videocam</span>
                                </div>
                                <span
                                    class="absolute bottom-2 left-2 text-[10px] font-label bg-black/50 text-white px-2 py-1 rounded-full">0:14</span>
                            </div>
                        </div>
                    </div>
                    <!-- Footer Actions -->
                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-outline-variant/10">
                        <button type="button" onclick="window.location.href='monitor.php'"
                            class="px-8 py-3 text-slate-500 font-headline font-bold hover:bg-slate-50 transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-10 py-3 bg-gradient-to-br from-primary to-primary-container text-white font-headline font-bold rounded-md shadow-lg shadow-primary/20 active:scale-95 transition-all">Submit Observation</button>
                    </div>
                </form>
            </div>
            <!-- Right Column: Contextual Sidebar -->
            <div class="w-full md:w-80 space-y-6">
                <!-- Safety Score Card -->
                <div class="bg-surface-container-high rounded-md p-6 space-y-4">
                    <h3 class="font-label text-[10px] uppercase tracking-widest text-slate-500">Node Safety Status</h3>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-headline font-bold text-blue-900 tracking-tighter">98.4</span>
                        <span class="text-sm font-medium text-slate-400 mb-1">Index</span>
                    </div>
                    <!-- Segmented Progress Bar -->
                    <div class="flex gap-1 h-2">
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-primary rounded-full"></div>
                        <div class="flex-1 bg-slate-300/30 rounded-full"></div>
                        <div class="flex-1 bg-slate-300/30 rounded-full"></div>
                    </div>
                    <p class="text-xs text-on-surface-variant leading-relaxed">Safety performance is 2.4% higher than
                        last quarter. Keep up the rigorous reporting.</p>
                </div>
                <!-- Recent Observations -->
                <div class="bg-surface-container-lowest rounded-md p-6 shadow-sm border border-outline-variant/10">
                    <h3 class="font-label text-[10px] uppercase tracking-widest text-slate-500 mb-6">Recent Reports</h3>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div
                                class="h-10 w-10 rounded-sm bg-tertiary-container/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-tertiary text-xl">priority_high</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-on-surface">Brake Fluid Leak</h4>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Crane A-12 • 2h ago
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="h-10 w-10 rounded-sm bg-primary-container/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl">check_circle</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-on-surface">PPE Integrity Check</h4>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Sector 4 • 5h ago
                                </p>
                            </div>
                        </div>
                    </div>
                    <button
                        class="w-full mt-8 py-2 text-xs font-label uppercase tracking-widest text-primary border-t border-outline-variant/10 hover:bg-slate-50 transition-colors">View
                        All Logs</button>
                </div>
                <!-- Technical Assistance -->
                <div class="p-6 bg-slate-900 rounded-md text-white relative overflow-hidden group">
                    <div class="relative z-10">
                        <span
                            class="material-symbols-outlined text-primary-fixed-dim text-3xl mb-4">support_agent</span>
                        <h4 class="font-headline font-bold text-lg leading-tight">Need technical safety guidance?</h4>
                        <p class="text-slate-400 text-xs mt-2 leading-relaxed">Direct line to Terminal Safety Officers
                            available 24/7 for incident escalation.</p>
                        <button
                            class="mt-6 w-full py-3 bg-white/10 hover:bg-white/20 rounded-sm font-headline font-bold text-sm transition-all border border-white/10">Open
                            Secure Channel</button>
                    </div>
                    <div
                        class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-700">
                        <span class="material-symbols-outlined text-[120px]"
                            style="font-variation-settings: 'wght' 700;">shield</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Bottom Nav for Mobile -->
    <nav
        class="fixed bottom-0 w-full bg-white/90 backdrop-blur-md md:hidden flex justify-around py-4 z-50 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
        <button class="flex flex-col items-center text-slate-400">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-[10px] font-label mt-1">HOME</span>
        </button>
        <button class="flex flex-col items-center text-blue-700">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">health_and_safety</span>
            <span class="text-[10px] font-label mt-1">HSSE</span>
        </button>
        <button class="flex flex-col items-center text-slate-400">
            <span class="material-symbols-outlined">settings</span>
            <span class="text-[10px] font-label mt-1">SET</span>
        </button>
    </nav>
</body>

</html>