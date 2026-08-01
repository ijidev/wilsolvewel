<?php require_once 'config.php'; secure_session_start(); generate_csrf_token();
$contact_address  = get_global_setting('contact_address', 'Victoria Island, Lagos, Nigeria.');
$contact_email    = get_global_setting('contact_email', 'info@wilsolvewel.com');
$contact_procurement_email = get_global_setting('contact_procurement_email', 'procurement@wilsolvewel.com');
$contact_phone    = get_global_setting('contact_phone', '+234 (0) 800 945 768');
$contact_mobile_phone = get_global_setting('contact_mobile_phone', '+234 (0) 811 620 7920');
$contact_technical_email = get_global_setting('contact_technical_email', 'support@wilsolvewel.com');
$contact_tel      = preg_replace('/[^0-9+]/', '', $contact_phone);
$hours_weekdays   = get_global_setting('hours_weekdays', '8:00 AM - 5:00 PM');
$hours_saturday   = get_global_setting('hours_saturday', 'By Appointment');
$hours_sunday     = get_global_setting('hours_sunday', 'Closed');
$map_lat          = get_global_setting('map_latitude', '6.5244');
$map_lng          = get_global_setting('map_longitude', '3.3792');
$map_api_key      = get_global_setting('google_maps_api_key', '');
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
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Inter:wght@300;400;500;600;700;800&amp;display=swap"
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

        <!-- Hero Section -->
        <section class="relative py-16 px-5 sm:px-6 lg:px-12 z-10 overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="aspect-[21/9] rounded-3xl overflow-hidden shadow-2xl relative border border-outline-variant/20">
                    <img class="w-full h-full object-cover grayscale transition-all duration-1000 hover:scale-105 hover:grayscale-0"
                        alt="Engineering Team"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9OxK7bvEeBHiB4IiD08woFatMHovl7-Mrrn2nVScQbp2TSyCXI-o0CTKd_wCTcm4Z5eTu7p4EIzDhsJZ76ptcJu1U4nRYG4STYB1gA1sG9Sc7w3jDbhMgICS838aIhHwIh_eVvoDmx4Bns1MkrwcqCKiq7yeS1Mt9sAngeckaWjVMqc2OGhh4cwx56PQK-8mtYSC_CfaMB7m1O9b5lKk2mKF6zFungAuDwRy0UdMo_o-fMNcPiWpHVRWQDzZQRohfco4zwbYxyD1s" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-10 lg:p-16">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">Contact Us</span>
                        <h1 class="font-headline text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-[0.95] tracking-tighter max-w-3xl mb-4">
                            Let's Discuss Your Engineering, Procurement, Maintenance or Construction Requirements
                        </h1>
                        <p class="text-white/70 text-base md:text-lg max-w-2xl font-light leading-relaxed mb-6">Whether you require technical support, spare parts sourcing, procurement assistance, logistics coordination, equipment maintenance or industrial construction services, our team is ready to assist.</p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#contact-form" class="anodized-gradient text-on-primary px-8 py-4 rounded-lg font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:translate-y-[-2px] transition-transform">
                                Request Technical Support
                            </a>
                            <a href="tel:<?= htmlspecialchars($contact_tel) ?>" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-4 rounded-lg font-headline font-bold text-sm uppercase tracking-widest hover:bg-white/20 transition-all">
                                Contact Our Team
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Contact Bar -->
        <section class="py-8 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/10 flex items-center gap-4 hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">location_on</span>
                        </div>
                        <div>
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline font-bold">Location</p>
                            <p class="text-on-surface font-semibold text-sm"><?= htmlspecialchars($contact_address) ?></p>
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/10 flex items-center gap-4 hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">call</span>
                        </div>
                        <div>
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline font-bold">Phone</p>
                            <p class="text-on-surface font-semibold text-sm"><?= htmlspecialchars($contact_phone) ?></p>
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/10 flex items-center gap-4 hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">mail</span>
                        </div>
                        <div>
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline font-bold">Email</p>
                            <p class="text-on-surface font-semibold text-sm"><?= htmlspecialchars($contact_email) ?></p>
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/10 flex items-center gap-4 hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">schedule</span>
                        </div>
                        <div>
                            <p class="font-label text-[10px] uppercase tracking-widest text-outline font-bold">Working Hours</p>
                            <p class="text-on-surface font-semibold text-sm"><?= htmlspecialchars($hours_weekdays) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Get In Touch + Contact Information -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-start">
                <div class="space-y-8">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Get In Touch</span>
                    <h2 class="font-headline text-3xl md:text-4xl font-bold text-on-surface leading-tight tracking-tight">We Are Ready to <span class="text-primary">Support Your Operations.</span></h2>
                    <div class="space-y-6 text-on-surface-variant text-base font-light leading-relaxed">
                        <p>Our team is available to discuss your requirements, answer technical questions, review project needs and provide procurement support.</p>
                        <p>Whether your requirement is urgent equipment support, planned maintenance, spare parts procurement, logistics coordination or construction services, we welcome the opportunity to assist.</p>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Technical Consultation"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <h3 class="font-headline text-xl font-bold text-on-surface mb-6">Reach Us Through Any of the Following Channels</h3>
                    <!-- Corporate Office -->
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary">location_on</span>
                            </div>
                            <p class="font-headline font-bold text-sm uppercase tracking-widest">Corporate Office</p>
                        </div>
                        <p class="text-on-surface-variant font-light leading-relaxed">Wilsolvewel Nigeria Limited<br /><?= htmlspecialchars($contact_address) ?></p>
                    </div>
                    <!-- Phone Numbers -->
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary">call</span>
                            </div>
                            <p class="font-headline font-bold text-sm uppercase tracking-widest">Phone Numbers</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-on-surface-variant font-light"><span class="font-medium text-on-surface">Main Office:</span> <?= htmlspecialchars($contact_phone) ?></p>
                            <p class="text-on-surface-variant font-light"><span class="font-medium text-on-surface">Mobile Support:</span> <?= htmlspecialchars($contact_mobile_phone) ?></p>
                        </div>
                    </div>
                    <!-- Email Addresses -->
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary">mail</span>
                            </div>
                            <p class="font-headline font-bold text-sm uppercase tracking-widest">Email Addresses</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-on-surface-variant font-light"><span class="font-medium text-on-surface">General Enquiries:</span> <?= htmlspecialchars($contact_email) ?></p>
                            <p class="text-on-surface-variant font-light"><span class="font-medium text-on-surface">Technical Support:</span> <?= htmlspecialchars($contact_technical_email) ?></p>
                        </div>
                    </div>
                    <!-- Working Hours -->
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary">schedule</span>
                            </div>
                            <p class="font-headline font-bold text-sm uppercase tracking-widest">Working Hours</p>
                        </div>
                        <p class="text-on-surface-variant font-light"><span class="font-medium text-on-surface">Monday – Friday:</span> <?= htmlspecialchars($hours_weekdays) ?></p>
                        <p class="text-on-surface-variant font-light"><span class="font-medium text-on-surface">Saturday:</span> <?= htmlspecialchars($hours_saturday) ?></p>
                        <p class="text-on-surface-variant font-light"><span class="font-medium text-on-surface">Sunday:</span> <?= htmlspecialchars($hours_sunday) ?></p>
                        <p class="text-on-surface-variant text-sm font-light italic">Emergency support arrangements may be available for critical operational requirements</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How Can We Help You? -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">How Can We Help You?</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Select the Service <span class="text-primary">You Need.</span></h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Service 1 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">engineering</span>
                        </div>
                        <h3 class="font-headline text-lg font-bold text-on-surface mb-3 tracking-tight">Technical Support &amp; Maintenance</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow mb-6">Diagnosis, troubleshooting, emergency maintenance, engine overhaul, power plant support, and heavy equipment services.</p>
                        <a href="mailto:<?= htmlspecialchars($contact_technical_email) ?>" class="text-primary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                            Contact Technical Team <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Service 2 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-secondary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-secondary group-hover:text-on-secondary">inventory_2</span>
                        </div>
                        <h3 class="font-headline text-lg font-bold text-on-surface mb-3 tracking-tight">Procurement &amp; International Logistics</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow mb-6">OEM parts sourcing, global procurement, logistics coordination, customs clearance, and material supply.</p>
                        <a href="mailto:<?= htmlspecialchars($contact_procurement_email) ?>" class="text-secondary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                            Contact Procurement Team <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Service 3 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">settings_suggest</span>
                        </div>
                        <h3 class="font-headline text-lg font-bold text-on-surface mb-3 tracking-tight">Hydraulic Pump Solutions</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow mb-6">Hydraulic pump supply, internal components, rebuild kits, and refurbishment support.</p>
                        <a href="mailto:<?= htmlspecialchars($contact_technical_email) ?>" class="text-primary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                            Request Pump Support <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Service 4 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-secondary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-secondary group-hover:text-on-secondary">public</span>
                        </div>
                        <h3 class="font-headline text-lg font-bold text-on-surface mb-3 tracking-tight">Strategic Global Sourcing Solutions</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow mb-6">Industrial equipment sourcing, specialized materials procurement, and supplier verification support.</p>
                        <a href="mailto:<?= htmlspecialchars($contact_procurement_email) ?>" class="text-secondary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                            Submit Requirement <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Service 5 -->
                    <div class="group bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col sm:col-span-2 lg:col-span-1">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">foundation</span>
                        </div>
                        <h3 class="font-headline text-lg font-bold text-on-surface mb-3 tracking-tight">Industrial Civil &amp; Construction Services</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow mb-6">Industrial foundations, construction works, equipment installation support, and engineering documentation.</p>
                        <a href="#contact-form" class="text-primary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 group-hover:translate-x-2 transition-transform">
                            Discuss Your Project <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Send Us A Message -->
        <section id="contact-form" class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-16 items-start">
                <!-- Contact Form -->
                <div class="lg:col-span-7">
                    <div class="bg-surface-container-lowest p-10 rounded-3xl border border-outline-variant/10 shadow-sm">
                        <h2 class="font-headline text-3xl font-bold text-on-surface tracking-tight mb-2">Send Us A <span class="text-primary">Message.</span></h2>
                        <p class="text-on-surface-variant text-sm font-light mb-8">We encourage visitors to provide detailed information so that our team can respond effectively.</p>
                        <form action="submit_handler.php" method="POST" class="space-y-6" enctype="multipart/form-data">
                            <?= get_csrf_field() ?>
                            <input type="hidden" name="form_type" value="Contact">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Full Name *</label>
                                    <input name="name" required class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all text-sm" placeholder="Your full name" type="text" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Company Name</label>
                                    <input name="company" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all text-sm" placeholder="Company name" type="text" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Position</label>
                                    <input name="position" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all text-sm" placeholder="Your position" type="text" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Email Address *</label>
                                    <input name="email" required class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all text-sm" placeholder="you@company.com" type="email" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Phone Number</label>
                                    <input name="phone" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all text-sm" placeholder="+234..." type="tel" />
                                </div>
                                <div class="space-y-2">
                                    <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Service Required</label>
                                    <select name="subject" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all appearance-none text-sm">
                                        <option value="">Select a service...</option>
                                        <option>Technical Support &amp; Maintenance</option>
                                        <option>Procurement &amp; International Logistics</option>
                                        <option>Hydraulic Pump Solutions</option>
                                        <option>Strategic Global Sourcing</option>
                                        <option>Industrial Civil &amp; Construction Services</option>
                                        <option>General Enquiry</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Message / Requirement *</label>
                                <textarea name="message" required class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-all text-sm" placeholder="Describe your industrial requirements in detail..." rows="5"></textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="font-label text-xs uppercase tracking-widest text-outline font-bold">Attachment (Optional)</label>
                                <div class="bg-surface-container-low rounded-lg border-2 border-dashed border-outline-variant/30 p-6 text-center hover:border-primary/30 transition-all">
                                    <span class="material-symbols-outlined text-outline text-3xl mb-2">upload_file</span>
                                    <p class="text-on-surface-variant text-xs font-light mb-2">Upload parts lists, drawings, specifications, technical documents, or equipment photographs</p>
                                    <input type="file" name="attachment" class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all" />
                                </div>
                            </div>
                            <button class="w-full anodized-gradient text-on-primary py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest flex items-center justify-center gap-3 active:scale-[0.98] transition-transform shadow-xl shadow-primary/20" type="submit">
                                Send Message
                                <span class="material-symbols-outlined text-xl">send</span>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Request A Quotation -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-surface-container-low rounded-3xl p-10 border border-outline-variant/10">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-3xl text-primary">request_quote</span>
                        </div>
                        <h3 class="font-headline text-2xl font-bold text-on-surface mb-4 tracking-tight">Request A Quotation</h3>
                        <p class="text-on-surface-variant text-sm font-light mb-6">For faster response, provide:</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Equipment Make
                            </li>
                            <li class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Model Number
                            </li>
                            <li class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Serial Number
                            </li>
                            <li class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Project Location
                            </li>
                            <li class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Scope of Requirement
                            </li>
                            <li class="flex items-center gap-3 text-sm text-on-surface-variant">
                                <span class="w-1.5 h-1.5 bg-primary rounded-full flex-shrink-0"></span> Required Delivery Date
                            </li>
                        </ul>
                        <p class="text-on-surface-variant text-sm font-light mb-6">Our team will review your request and respond accordingly.</p>
                        <a href="mailto:<?= htmlspecialchars($contact_email) ?>?subject=Quotation%20Request" class="block w-full text-center anodized-gradient text-on-primary py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:translate-y-[-2px] transition-transform">
                            Request a Quotation
                        </a>
                    </div>
                    <!-- Emergency Support -->
                    <div class="p-6 bg-secondary/5 rounded-2xl border border-secondary/20 flex items-center gap-4">
                        <span class="material-symbols-outlined text-secondary text-3xl" style="font-variation-settings: 'FILL' 1;">warning</span>
                        <div>
                            <p class="text-sm font-bold text-secondary uppercase tracking-tighter">Emergency Support</p>
                            <p class="text-xs text-on-surface-variant">Available for critical operational requirements</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Location Map -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Our Location</span>
                    <h2 class="font-headline text-3xl md:text-4xl font-bold text-on-surface tracking-tight leading-none mb-4">Visit Our <span class="text-primary">Office.</span></h2>
                </div>
                <div class="w-full relative">
                    <div class="absolute top-6 left-6 z-10 bg-surface-container-lowest/90 backdrop-blur-md p-6 rounded-2xl shadow-xl max-w-xs border border-outline-variant/20">
                        <h4 class="font-headline font-bold text-lg mb-2">Corporate Office</h4>
                        <p class="text-sm text-on-surface-variant mb-4"><?= htmlspecialchars($contact_address) ?></p>
                        <a href="https://maps.google.com/maps?q=<?= htmlspecialchars($map_lat) ?>,<?= htmlspecialchars($map_lng) ?>&z=15" target="_blank" class="text-primary font-headline font-bold text-xs uppercase tracking-widest flex items-center gap-2 hover:underline">
                            Get Directions <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </a>
                    </div>
                    <div class="w-full h-[500px] rounded-2xl overflow-hidden relative shadow-2xl">
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
        </section>

        <!-- Why Clients Contact Wilsolvewel -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Why Clients Choose Us</span>
                    <h2 class="font-headline text-3xl md:text-4xl font-bold text-on-surface tracking-tight leading-none mb-6">Reliable Support. Professional Response. <span class="text-primary">Practical Solutions.</span></h2>
                    <p class="text-on-surface-variant text-base font-light leading-relaxed">Organizations choose Wilsolvewel because we provide:</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">chat</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Responsive Communication</h4>
                        <p class="text-xs text-on-surface-variant font-light">Prompt response to enquiries and support requests.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">engineering</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Technical Understanding</h4>
                        <p class="text-xs text-on-surface-variant font-light">Engineering-based review of client requirements.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Procurement Expertise</h4>
                        <p class="text-xs text-on-surface-variant font-light">Structured sourcing and logistics coordination.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">precision_manufacturing</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Practical Experience</h4>
                        <p class="text-xs text-on-surface-variant font-light">Support across Oil &amp; Gas, Power Generation, Construction, and Industrial sectors.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">handshake</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Commitment to Success</h4>
                        <p class="text-xs text-on-surface-variant font-light">Focused on delivering solutions that support operational continuity and project success.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Careers / Partnership -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-on-surface text-surface relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-8">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Collaborate With Us</span>
                        <h2 class="font-headline text-3xl md:text-4xl font-bold tracking-tight">Interested in Working <span class="text-primary">With Us?</span></h2>
                        <p class="text-surface-bright/60 font-light leading-relaxed">We welcome opportunities to collaborate with clients, suppliers, technical professionals and strategic partners.</p>
                        <div class="flex flex-wrap gap-4">
                            <a href="mailto:<?= htmlspecialchars($contact_email) ?>?subject=Supplier%20Enquiry" class="bg-primary text-on-primary px-8 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Become a Supplier
                            </a>
                            <a href="mailto:<?= htmlspecialchars($contact_email) ?>?subject=Partnership%20Enquiry" class="bg-surface/10 text-surface-bright px-8 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest border border-surface/20 hover:bg-surface/20 transition-all">
                                Explore Partnership Opportunities
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-surface/10">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                                alt="Business Partnership"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAoiC3Rxm03OmGzT8s4cxzzD6CqTr49EN7zUgr4MdewbZ32bTu6oje81NkZmBg7Bn7g2eG3JoX0DO-Sjt1jOe0wskEIBSVSKc2v-7oe8km3bCT1X7M1WI70k2zDsYIz9ote_CD2PdoH4NcJCBXNppqqeEM431VDKvb7QNYPkgy7UjOaaQ-bE3bjZow8e06SQ_e49JeuLyHQALD33DzNCGohdL87_kSsXg6g0ZQaF0jnWw0oeeQ_xhPYyU83RJ0NLKAj2EQkIwqCqjuF" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-6xl mx-auto anodized-gradient rounded-3xl p-12 lg:p-24 text-center text-on-primary shadow-3xl relative overflow-hidden">
                <div class="absolute top-0 right-0 max-w-full w-64 h-64 sm:w-96 sm:h-96 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-2xl translate-y-1/2 -translate-x-1/2"></div>
                <div class="relative z-10 space-y-8">
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold tracking-tighter leading-none">Let's Start the <span class="text-primary-fixed">Conversation.</span></h2>
                    <p class="text-lg text-primary-fixed/80 max-w-3xl mx-auto font-light leading-relaxed">Whether you need urgent technical support, procurement assistance, equipment maintenance, logistics coordination or industrial construction expertise, our team is ready to help.</p>
                    <div class="flex flex-wrap justify-center gap-6 pt-4">
                        <a href="tel:<?= htmlspecialchars($contact_tel) ?>" class="bg-white text-primary px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-[0.2em] shadow-2xl hover:scale-105 transition-all">
                            Contact Us Today
                        </a>
                        <a href="mailto:<?= htmlspecialchars($contact_technical_email) ?>" class="bg-transparent border-2 border-white/40 text-white px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-[0.2em] hover:bg-white/10 transition-all">
                            Request Support
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer (Updated using COMPONENTS_97) -->
    <!-- Footer Shell -->
    <script src="./components/footer.js" data-root="./"></script>
</body>

</html>
