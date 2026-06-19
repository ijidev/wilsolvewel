<?php require_once 'config.php'; secure_session_start(); generate_csrf_token();
$contact_address  = get_setting('contact_address', 'Victoria Island, Lagos, Nigeria.');
$contact_email    = get_setting('contact_email', 'info@wilsolvewel.com');
$contact_phone    = get_setting('contact_phone', '+234 (0) 800 945 768');
$hours_weekdays   = get_setting('hours_weekdays', '8:00 AM - 5:00 PM');
$hours_saturday   = get_setting('hours_saturday', 'By Appointment');
$hours_sunday     = get_setting('hours_sunday', 'Closed');
$map_lat          = get_setting('map_latitude', '6.5244');
$map_lng          = get_setting('map_longitude', '3.3792');
$map_api_key      = get_setting('google_maps_api_key', '');
$map_src = $map_api_key
    ? "https://www.google.com/maps/embed/v1/place?key=" . urlencode($map_api_key) . "&q=" . urlencode($map_lat) . "," . urlencode($map_lng) . "&zoom=15"
    : "https://maps.google.com/maps?q=" . urlencode($map_lat) . "," . urlencode($map_lng) . "&z=15&output=embed";
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Contact | Wilsolvewel Nigeria Limited</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

</head>

<body
    class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
    <!-- Top Navigation Shell -->
    <script src="./components/header.js" data-root="./"></script>
    <script src="./components/effects.js"></script>
    <main class="relative pt-20">
        <!-- Technical Grid Overlay Background -->
        <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>
        <!-- Hero Header -->
        <div class="relative py-16 px-5 sm:px-6 lg:px-12 z-10">
            <div class="max-w-7xl mx-auto">
                <span class="font-label text-secondary uppercase tracking-[0.2em] text-[10px] font-bold mb-4 block">Get
                    In
                    Touch</span>
                <h1
                    class="font-headline text-3xl md:text-5xl font-bold text-on-surface tracking-tighter leading-none mb-6">
                    Technical Support & <br /> <span class="text-primary">Repair Inquiry Portal</span>
                </h1>
                <p class="max-w-2xl text-on-surface-variant text-base leading-relaxed">
                    Connect with our heavy machinery division for maintenance scheduling, drivetrain restoration, and
                    specialized engineering support across your operation sites.
                </p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6">
            <!-- Main Content Section: Two Column Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-24">
                <!-- Left Column: Inquiry Form -->
                <div
                    class="lg:col-span-7 bg-surface-container-lowest rounded-lg p-10 shadow-[0_40px_60px_rgba(25,28,30,0.04)]">
                    <form action="submit_handler.php" method="POST" class="space-y-6">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="form_type" value="Contact">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Full Name</label>
                                <input name="name" required class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all" placeholder="John Doe" type="text" />
                            </div>
                            <div class="space-y-2">
                                <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Email Address</label>
                                <input name="email" required class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all" placeholder="j.doe@enterprise.com" type="email" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Subject / Department</label>
                            <select name="subject" class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all appearance-none">
                                <option>Infrastructure Logistics</option>
                                <option>Compliance &amp; Certification</option>
                                <option>Safety Standards Consultation</option>
                                <option>General Industrial Inquiry</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Project Brief / Message</label>
                            <textarea name="message" required class="w-full bg-surface-container-low border-none rounded-sm px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all" placeholder="Describe your industrial requirements in detail..." rows="5"></textarea>
                        </div>
                        <button class="w-full bg-gradient-to-br from-primary to-primary-container text-on-primary py-4 rounded-full font-headline font-semibold text-lg flex items-center justify-center gap-3 active:scale-[0.98] transition-transform" type="submit">
                            Transmit Inquiry
                            <span class="material-symbols-outlined text-xl">send</span>
                        </button>
                    </form>
                </div>
                <!-- Right Column: Contact Details -->
                <div class="lg:col-span-5 space-y-8">
                    <!-- Contact Cards -->
                    <div class="bg-surface-container-low rounded-lg p-8 border-l-4 border-primary">
                        <h3 class="font-headline font-bold text-xl mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">hub</span>
                            Headquarters
                        </h3>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">location_on</span>
                                </div>
                                <div>
                                    <p class="font-label text-[10px] uppercase tracking-widest text-outline">Office Address
                                    </p>
                                    <p class="text-on-surface font-semibold"><?= htmlspecialchars($contact_address) ?></p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">mail</span>
                                </div>
                                <div>
                                    <p class="font-label text-[10px] uppercase tracking-widest text-outline">Digital
                                        Correspondence</p>
                                    <p class="text-on-surface font-semibold"><?= htmlspecialchars($contact_email) ?></p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">call</span>
                                </div>
                                <div>
                                    <p class="font-label text-[10px] uppercase tracking-widest text-outline">Direct Line</p>
                                    <p class="text-on-surface font-semibold"><?= htmlspecialchars($contact_phone) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Business Hours Card -->
                    <div class="bg-surface-container-high rounded-lg p-8">
                        <h3 class="font-headline font-bold text-xl mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">schedule</span>
                            Operations Window
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex justify-between items-center py-2 border-b border-outline-variant/30">
                                <span class="font-label text-sm uppercase text-on-surface-variant">Mon - Fri</span>
                                <span class="font-bold text-on-surface"><?= htmlspecialchars($hours_weekdays) ?></span>
                            </li>
                            <li class="flex justify-between items-center py-2 border-b border-outline-variant/30">
                                <span class="font-label text-sm uppercase text-on-surface-variant">Saturday</span>
                                <span class="font-bold text-on-surface"><?= htmlspecialchars($hours_saturday) ?></span>
                            </li>
                            <li class="flex justify-between items-center py-2">
                                <span class="font-label text-sm uppercase text-on-surface-variant">Sunday</span>
                                <span class="font-bold text-secondary"><?= htmlspecialchars($hours_sunday) ?></span>
                            </li>
                        </ul>
                    </div>
                    <!-- Emergency Support -->
                    <div class="p-6 bg-secondary/5 rounded-lg border border-secondary/20 flex items-center gap-4">
                        <span class="material-symbols-outlined text-secondary text-3xl"
                            style="font-variation-settings: 'FILL' 1;">warning</span>
                        <div>
                            <p class="text-sm font-bold text-secondary uppercase tracking-tighter">Emergency Field Response
                            </p>
                            <p class="text-xs text-on-surface-variant">Available 24/7 for active blueprint site partners.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Large Section: Interactive Map -->
            <div class="w-full relative group mb-24">
                <div
                    class="absolute top-8 left-8 z-10 bg-surface-container-lowest/90 backdrop-blur-md p-6 rounded-lg shadow-xl max-w-xs border border-outline-variant/20">
                    <h4 class="font-headline font-bold text-lg mb-2">Technical Hub Lagos</h4>
                    <p class="text-sm text-on-surface-variant mb-4"><?= htmlspecialchars($contact_address) ?></p>
                    <a href="<?= htmlspecialchars($map_src) ?>" target="_blank"
                        class="text-primary font-label text-xs uppercase font-bold flex items-center gap-2 hover:underline">
                        Get Navigational Data
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                    </a>
                </div>
                <div class="w-full h-[500px] rounded-lg overflow-hidden relative shadow-2xl">
                    <iframe class="w-full h-full"
                        src="<?= htmlspecialchars($map_src) ?>"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        style="border:0;filter:grayscale(1) contrast(1.1);"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer (Updated using COMPONENTS_97) -->
    <!-- Footer Shell -->
    <script src="./components/footer.js" data-root="./"></script>
</body>

</html>
