<?php
require_once __DIR__ . '/config.php';
$conn = get_db_connection();

$team_members = [];
$res = $conn->query("SELECT * FROM team_members WHERE status='Active' ORDER BY sort_order ASC, id ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) $team_members[] = $row;
}
$icons = ['engineering', 'inventory_2', 'support_agent', 'finance', 'business', 'groups'];
$team_count = count($team_members);
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>About Us | Wilsolvewel Engineering</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Inter:wght@300;400;500;600;700&amp;display=swap"
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
        <section class="relative py-24 px-5 sm:px-6 lg:px-12 z-10 overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="aspect-[21/9] rounded-3xl overflow-hidden shadow-2xl relative border border-outline-variant/20">
                    <img class="w-full h-full object-cover grayscale transition-all duration-1000 hover:scale-105 hover:grayscale-0"
                        alt="Industrial Engineering Site"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAoiC3Rxm03OmGzT8s4cxzzD6CqTr49EN7zUgr4MdewbZ32bTu6oje81NkZmBg7Bn7g2eG3JoX0DO-Sjt1jOe0wskEIBSVSKc2v-7oe8km3bCT1X7M1WI70k2zDsYIz9ote_CD2PdoH4NcJCBXNppqqeEM431VDKvb7QNYPkgy7UjOaaQ-bE3bjZow8e06SQ_e49JeuLyHQALD33DzNCGohdL87_kSsXg6g0ZQaF0jnWw0oeeQ_xhPYyU83RJ0NLKAj2EQkIwqCqjuF" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-10 lg:p-16">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block">About Us</span>
                        <h1 class="font-headline text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-[0.95] tracking-tighter max-w-3xl">
                            About Wilsolvewel <span class="text-primary">Nigeria Limited.</span>
                        </h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Company Introduction -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Who We Are</span>
                    <h2 class="font-headline text-3xl md:text-4xl font-bold text-on-surface leading-tight tracking-tight">Engineering Expertise. Reliable Procurement. <span class="text-primary">Practical Solutions.</span></h2>
                    <div class="space-y-6 text-on-surface-variant text-base font-light leading-relaxed">
                        <p>Wilsolvewel Nigeria Limited is an indigenous engineering and industrial support company helping organizations reduce downtime, improve operational reliability and execute projects with confidence.</p>
                        <p>We deliver Technical Support & Maintenance, Procurement & International Logistics and Industrial Civil, Structural & Construction Services that support critical industries across Nigeria.</p>
                        <p class="font-medium text-on-surface">Our focus is simple: helping clients keep equipment running, projects progressing and operations performing at their best.</p>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                        <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                            alt="Equipment Inspection"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3ogqFv4fevzwquJt3OipOqsrBIinjhoo_7J7VWYZiZS49OIk8P00Rw8ykn1uaCLhL9WioL3xHgLsLbUUaloBJYRTH4a5t87FOhUzGYgVE_mJCT5CIQ8n_EDp2-1Ui1bRvltpVtt_gnbyzUT0ycYak7GEeIH-rN2WiOhaS-03bGtYdbxJ6eXX6YDIz1G-H2HiIwnCLiZzBXrVZPc5vaN0cbmM0CWx8qi2mz-igSLHN3t1QsCf5brfOi-fopQBLouk0RZcp_wq-LZig" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Who We Are (Detailed) -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative order-2 lg:order-1">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                        <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                            alt="Industrial Maintenance Activity"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjl9eVb8XrEFxh0iW_kBJ_0dcnn3laux3ZDkXr7fGycCo30F5khhyyNnrodhk0WaVuXCqQOuiUmvx8599xERC4FYQOAwViCSHlC-SxjdLZ_g0isD4hDnop8ClLgDFLTdWrBo8h19SFeURf_NAQovQrUy40JwF_foBE9myeGhjMeDuS5CpDyfWKz0SXRAgtjAHo7RO0GDfOKM6LQ-QjaeOsSSBWIyeZNldqQeWvCV295VFECBSV0yyjE0uAbXg-yKljf5SzDo6MkCnI" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    </div>
                </div>
                <div class="space-y-8 order-1 lg:order-2">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Who We Are</span>
                    <h2 class="font-headline text-3xl md:text-4xl font-bold text-on-surface leading-tight tracking-tight">Your Trusted Partner for <span class="text-primary">Industrial Operations.</span></h2>
                    <div class="space-y-6 text-on-surface-variant text-base font-light leading-relaxed">
                        <p>Founded in 2021, Wilsolvewel Nigeria Limited was established to address the growing demand for reliable engineering support, dependable procurement solutions and practical industrial services.</p>
                        <p>Today, we support organizations operating in demanding environments where equipment reliability, technical competence and timely project execution are critical to success.</p>
                        <p>Our multidisciplinary approach enables us to support clients throughout the entire operational lifecycle—from equipment maintenance and technical troubleshooting to procurement, logistics coordination and industrial construction services.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Purpose -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Our Purpose</span>
                    <h2 class="font-headline text-3xl md:text-4xl font-bold text-on-surface leading-tight tracking-tight">Why We <span class="text-primary">Exist.</span></h2>
                    <div class="space-y-6 text-on-surface-variant text-base font-light leading-relaxed">
                        <p>Industrial operations face increasing pressure to maintain productivity, control costs, meet project deadlines and ensure safety.</p>
                        <p>Equipment failures, procurement delays, infrastructure deficiencies and technical resource gaps can significantly impact business performance.</p>
                        <p>Wilsolvewel exists to help organizations overcome these challenges through practical engineering solutions, reliable procurement systems and responsive technical support that improve operational outcomes.</p>
                        <p class="font-medium text-on-surface">Our goal is not simply to provide services. Our goal is to become a trusted partner that contributes to our clients' long-term success.</p>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                        <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                            alt="Industrial Operations"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL2icG4PWakb4pmy3ahdG-5OsyndkSQ_XAp34Aja84XNeMxyihqgKc9J740YcrLkU3RAQVAgsEpIO_6s1imD5VcaAE8UyR0RJVzhUZ7yV51fXNAs6ddL-yf-rH-DHSdAiz1l_eIoylGhr1sh1-Pgxah-MIm0nj8Z-aQjynFCM7uNq64WdHjv-fb2wZrY65ZABrfFf_QUsNNFg0wmMyqRygLgC3EsUngMi4TgqUEk6_evRNwwIBhAmw0e76jYtl9CCFH0vk5LcpAUoG" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision & Mission -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-on-surface text-surface relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="p-12 rounded-3xl border border-surface/10 bg-surface/5 space-y-6">
                        <div class="w-16 h-16 bg-primary/20 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-4xl">visibility</span>
                        </div>
                        <h3 class="font-headline text-3xl font-bold">Our Vision</h3>
                        <p class="text-surface-bright/70 text-lg font-light leading-relaxed">
                            To become a leading engineering and industrial support company recognized for delivering reliable solutions that improve operational performance, enhance asset reliability and support sustainable industrial growth across Africa.
                        </p>
                    </div>
                    <div class="anodized-gradient p-12 rounded-3xl shadow-xl shadow-primary/20 space-y-6 text-on-primary">
                        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-4xl">rocket_launch</span>
                        </div>
                        <h3 class="font-headline text-3xl font-bold">Our Mission</h3>
                        <p class="text-primary-fixed/80 text-lg font-light leading-relaxed">
                            To provide engineering, procurement, logistics and technical support solutions that help organizations operate safely, efficiently and profitably while maintaining the highest standards of professionalism, integrity and service excellence.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Values -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Our Core Values</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none">What <span class="text-primary">Defines Us.</span></h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">
                    <div class="space-y-4 p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">emoji_events</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Excellence</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Delivering quality solutions that consistently meet client expectations.</p>
                    </div>
                    <div class="space-y-4 p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">verified</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Integrity</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Operating transparently, ethically and professionally in every engagement.</p>
                    </div>
                    <div class="space-y-4 p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">lightbulb</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Innovation</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Applying practical thinking and continuous improvement to solve complex challenges.</p>
                    </div>
                    <div class="space-y-4 p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">handshake</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Customer Success</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Building long-term relationships through measurable value and dependable service delivery.</p>
                    </div>
                    <div class="space-y-4 p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center group hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">eco</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Sustainability</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Supporting responsible operations that create lasting benefits for clients and communities.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- What We Do -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">What We Do</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Integrated Solutions for <span class="text-primary">Industrial Operations.</span></h2>
                    <p class="text-on-surface-variant text-base font-light leading-relaxed">Wilsolvewel provides three core service areas designed to support the full operational needs of industrial organizations.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group bg-surface-container-lowest p-10 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">engineering</span>
                        </div>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-3 tracking-tight">Technical Support &amp; Maintenance</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow">
                            Helping organizations reduce downtime, improve equipment reliability and maintain operational continuity.
                        </p>
                    </div>
                    <div class="group bg-surface-container-lowest p-10 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-secondary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-secondary group-hover:text-on-secondary">inventory_2</span>
                        </div>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-3 tracking-tight">Procurement &amp; International Logistics</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow">
                            Providing structured sourcing, supplier verification, international shipping coordination, customs clearance and delivery management.
                        </p>
                    </div>
                    <div class="group bg-surface-container-lowest p-10 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-primary">foundation</span>
                        </div>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-3 tracking-tight">Industrial Civil, Structural &amp; Construction Services</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed flex-grow">
                            Delivering industrial construction solutions including equipment foundations, structural works, reinforced concrete structures and engineering support services.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Industries We Serve -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Industries We Serve</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Supporting Critical Sectors <span class="text-primary">Across Nigeria.</span></h2>
                    <p class="text-on-surface-variant text-base font-light leading-relaxed">We provide engineering and industrial support services to organizations operating in:</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/10 text-center hover:bg-primary/5 hover:border-primary/30 transition-all group">
                        <span class="material-symbols-outlined text-3xl text-primary mb-3">local_gas_station</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Oil &amp; Gas</h4>
                    </div>
                    <div class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/10 text-center hover:bg-primary/5 hover:border-primary/30 transition-all group">
                        <span class="material-symbols-outlined text-3xl text-primary mb-3">bolt</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Power Generation &amp; Utilities</h4>
                    </div>
                    <div class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/10 text-center hover:bg-primary/5 hover:border-primary/30 transition-all group">
                        <span class="material-symbols-outlined text-3xl text-primary mb-3">precision_manufacturing</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Manufacturing &amp; Industrial Facilities</h4>
                    </div>
                    <div class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/10 text-center hover:bg-primary/5 hover:border-primary/30 transition-all group">
                        <span class="material-symbols-outlined text-3xl text-primary mb-3">foundation</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Construction &amp; Infrastructure</h4>
                    </div>
                    <div class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/10 text-center hover:bg-primary/5 hover:border-primary/30 transition-all group">
                        <span class="material-symbols-outlined text-3xl text-primary mb-3">account_balance</span>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Government &amp; Corporate Facilities</h4>
                    </div>
                </div>
                <p class="text-on-surface-variant text-base font-light leading-relaxed text-center max-w-3xl mx-auto mt-10">Our understanding of these sectors enables us to deliver practical solutions aligned with industry-specific operational requirements.</p>
            </div>
        </section>

        <!-- Why Clients Choose Wilsolvewel -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 z-10 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Our Advantage</span>
                    <h2 class="font-headline text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-none mb-6">Why Clients Choose <span class="text-primary">Wilsolvewel.</span></h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">bolt</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Rapid Response</h4>
                        <p class="text-xs text-on-surface-variant font-light">Supporting clients when operational challenges require immediate attention.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">engineering</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Engineering-Led Approach</h4>
                        <p class="text-xs text-on-surface-variant font-light">Combining technical expertise with practical field experience.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Verified Procurement Network</h4>
                        <p class="text-xs text-on-surface-variant font-light">Access to trusted suppliers and reliable logistics channels.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">groups</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Skilled Technical Personnel</h4>
                        <p class="text-xs text-on-surface-variant font-light">Experienced professionals committed to delivering results.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">security</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">Safety &amp; Compliance Focus</h4>
                        <p class="text-xs text-on-surface-variant font-light">Work executed according to industry standards and HSSE requirements.</p>
                    </div>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all group">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary" style="font-variation-settings: 'FILL' 1;">handshake</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest mb-2">End-to-End Support</h4>
                        <p class="text-xs text-on-surface-variant font-light">A single partner for engineering support, procurement, logistics, and industrial construction services.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Commitment -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 z-10 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative group border border-outline-variant/20">
                        <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 scale-105 group-hover:scale-100"
                            alt="Engineering Team on Site"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9OxK7bvEeBHiB4IiD08woFatMHovl7-Mrrn2nVScQbp2TSyCXI-o0CTKd_wCTcm4Z5eTu7p4EIzDhsJZ76ptcJu1U4nRYG4STYB1gA1sG9Sc7w3jDbhMgICS838aIhHwIh_eVvoDmx4Bns1MkrwcqCKiq7yeS1Mt9sAngeckaWjVMqc2OGhh4cwx56PQK-8mtYSC_CfaMB7m1O9b5lKk2mKF6zFungAuDwRy0UdMo_o-fMNcPiWpHVRWQDzZQRohfco4zwbYxyD1s" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    </div>
                </div>
                <div class="space-y-8">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Our Commitment</span>
                    <h2 class="font-headline text-3xl md:text-4xl font-bold text-on-surface leading-tight tracking-tight">Delivering Value Beyond <span class="text-primary">Service Delivery.</span></h2>
                    <div class="space-y-6 text-on-surface-variant text-base font-light leading-relaxed">
                        <p>At Wilsolvewel, we understand that our clients are not simply purchasing services—they are investing in operational reliability, project success and business continuity.</p>
                        <p>We are committed to delivering solutions that help organizations reduce risk, improve efficiency, protect critical assets and achieve their operational objectives.</p>
                        <p>Every project, maintenance activity, procurement engagement and construction assignment is approached with professionalism, accountability and a focus on measurable results.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-on-surface text-surface text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
            <div class="max-w-4xl mx-auto space-y-10 relative z-10">
                <h2 class="font-headline text-4xl md:text-5xl font-bold tracking-tighter">Let's Build <span class="text-primary">Reliable Operations</span> Together.</h2>
                <p class="text-surface-bright/60 text-lg font-light max-w-2xl mx-auto leading-relaxed">
                    Whether you require technical support, reliable procurement solutions, international logistics coordination or industrial construction services, Wilsolvewel Nigeria Limited is ready to support your organization.
                </p>
                <p class="text-surface-bright/50 text-base font-light max-w-2xl mx-auto leading-relaxed">
                    Partner with a team committed to delivering practical solutions, dependable service, and long-term value.
                </p>
                <div class="flex flex-wrap justify-center gap-6 pt-4">
                    <a href="contact.php" class="bg-primary text-on-primary px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                        Contact Our Team
                    </a>
                    <a href="contact.php" class="bg-surface/10 text-surface-bright px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-widest border border-surface/20 hover:bg-surface/20 transition-all">
                        Discuss Your Requirements
                    </a>
                </div>
            </div>
        </section>

    </main>
    <!-- Footer Shell -->
    <script src="./components/footer.js" data-root="./"></script>
</body>

</html>
