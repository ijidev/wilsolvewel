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
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700&amp;display=swap"
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
        <section class="relative py-24 px-5 sm:px-6 lg:px-12 z-10">
            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-16 items-start">
                <div class="lg:w-7/12 space-y-8">
                    <div class="inline-flex items-center gap-3 px-3 py-1 bg-primary/10 rounded-full border border-primary/20">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.2em]">Founded 2021 | Indigenous Strength</span>
                    </div>
                    <h1 class="font-headline text-4xl md:text-6xl font-bold text-on-surface leading-[0.95] tracking-tighter">
                        WILSOLVEWEL <br /><span class="text-primary">NIGERIA LIMITED.</span>
                    </h1>
                    <p class="text-xl font-light text-on-surface-variant leading-relaxed max-w-2xl border-l-4 border-secondary pl-8">
                        We are a fully indigenous engineering, procurement, and technical support company dedicated to industrial efficiency across Nigeria.
                    </p>
                </div>
                <div class="lg:w-5/12 relative">
                    <div class="aspect-square rounded-3xl overflow-hidden shadow-2xl border border-outline-variant/10 relative group">
                        <img class="w-full h-full object-cover grayscale transition-all duration-1000 group-hover:scale-105 group-hover:grayscale-0" 
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAoiC3Rxm03OmGzT8s4cxzzD6CqTr49EN7zUgr4MdewbZ32bTu6oje81NkZmBg7Bn7g2eG3JoX0DO-Sjt1jOe0wskEIBSVSKc2v-7oe8km3bCT1X7M1WI70k2zDsYIz9ote_CD2PdoH4NcJCBXNppqqeEM431VDKvb7QNYPkgy7UjOaaQ-bE3bjZow8e06SQ_e49JeuLyHQALD33DzNCGohdL87_kSsXg6g0ZQaF0jnWw0oeeQ_xhPYyU83RJ0NLKAj2EQkIwqCqjuF" alt="Engineering Excellence" />
                        <div class="absolute inset-0 bg-gradient-to-tr from-black/60 via-transparent to-transparent"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Corporate Overview -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-16 items-center">
                <div class="lg:col-span-12 xl:col-span-5">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Our Story</span>
                    <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tight mb-8">Engineering <br /><span class="text-primary italic">Surgeons.</span></h2>
                </div>
                <div class="lg:col-span-12 xl:col-span-7 space-y-6 text-on-surface-variant font-light text-lg leading-relaxed">
                    <p>Wilsolvewel Nigeria Limited was founded to keep industries running. We specialize in providing fast-response engineering support, reliable procurement, and technical expertise that prevents costly downtime.</p>
                    <p>We partner with clients to solve critical operational challenges, protect equipment investments, and ensure projects and facilities perform safely, efficiently, and without disruption. At Wilsolvewel, we are not just service providers; we are technical partners in your industrial success.</p>
                </div>
            </div>
        </section>

        <!-- Vision & Mission -->
        <section class="py-24 px-5 sm:px-6 lg:px-12">
            <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-8">
                <div class="bg-surface-container-lowest p-12 rounded-3xl border border-outline-variant/10 shadow-sm space-y-6">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-4xl">visibility</span>
                    </div>
                    <h3 class="font-headline text-3xl font-bold">Our Vision</h3>
                    <p class="text-on-surface-variant text-lg font-light leading-relaxed">
                        To set the benchmark in engineering support across Africa by empowering industries through driving innovation, skilled manpower and sustainability across industries through genuine solutions and sustainable practices.
                    </p>
                </div>
                <div class="anodized-gradient p-12 rounded-3xl shadow-xl shadow-primary/20 space-y-6 text-on-primary">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-4xl">rocket_launch</span>
                    </div>
                    <h3 class="font-headline text-3xl font-bold">Our Mission</h3>
                    <p class="text-primary-fixed/80 text-lg font-light leading-relaxed">
                        To be the trusted partner for engineering, maintenance and procurement solutions, delivering exceptional value, safety and performance in every project.
                    </p>
                </div>
            </div>
        </section>

        <!-- Who We Serve -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-on-surface text-surface relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-20 space-y-4">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Our Clients</span>
                    <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tight">Who We Serve.</h2>
                    <p class="text-surface-bright/60 font-light">We solve the specific technical headaches faced by the leaders of Nigeria's primary industries.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12">
                    <div class="space-y-6 p-8 bg-surface/5 rounded-3xl border border-surface/10">
                        <h4 class="font-headline font-bold text-xl text-primary uppercase tracking-widest">Project Managers</h4>
                        <p class="text-sm font-light text-surface-bright/70">Reliable sub-contracting support to ensure construction and infrastructure projects are delivered on specification and without machinery failure.</p>
                    </div>
                    <div class="space-y-6 p-8 bg-surface/5 rounded-3xl border border-surface/10 border-t-4 border-t-primary">
                        <h4 class="font-headline font-bold text-xl text-primary uppercase tracking-widest">Plant &amp; Operations Managers</h4>
                        <p class="text-sm font-light text-surface-bright/70">Strategic sourcing and civil engineering solutions that maintain the structural integrity and flow efficiency of industrial facilities.</p>
                    </div>
                    <div class="space-y-6 p-8 bg-surface/5 rounded-3xl border border-surface/10">
                        <h4 class="font-headline font-bold text-xl text-primary uppercase tracking-widest">Maintenance Managers</h4>
                        <p class="text-sm font-light text-surface-bright/70">Fast-response technical support and OEM parts sourcing to restore failed assets and optimize equipment lifecycle performance.</p>
                    </div>
                    <div class="space-y-6 p-8 bg-surface/5 rounded-3xl border border-surface/10 border-t-4 border-t-primary">
                        <h4 class="font-headline font-bold text-xl text-primary uppercase tracking-widest">Engineering Managers</h4>
                        <p class="text-sm font-light text-surface-bright/70">Specialized engineering consultation for structural foundation design, pump installations, turbine civil works, and OEM parts integration across all industrial sectors.</p>
                    </div>
                    <div class="space-y-6 p-8 bg-surface/5 rounded-3xl border border-surface/10">
                        <h4 class="font-headline font-bold text-xl text-primary uppercase tracking-widest">Procurement &amp; Supply Chain Managers</h4>
                        <p class="text-sm font-light text-surface-bright/70">End-to-end procurement logistics including strategic sourcing, supplier qualification, post-delivery support, and inventory management to keep your supply chain running.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values -->
        <section class="py-32 px-5 sm:px-6 lg:px-12">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center gap-8 mb-20">
                    <h2 class="font-headline text-4xl font-bold whitespace-nowrap">Core Values</h2>
                    <div class="h-px bg-outline-variant/30 w-full"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                    <div class="space-y-6 group">
                        <span class="font-headline text-6xl font-black text-outline-variant/20 group-hover:text-primary transition-colors">01</span>
                        <h4 class="font-headline font-bold text-xl uppercase tracking-widest">Integrity</h4>
                        <p class="text-on-surface-variant text-sm font-light leading-relaxed">We build trust through transparent operations and technical honesty. We deliver exactly what we specify.</p>
                    </div>
                    <div class="space-y-6 group">
                        <span class="font-headline text-6xl font-black text-outline-variant/20 group-hover:text-primary transition-colors">02</span>
                        <h4 class="font-headline font-bold text-xl uppercase tracking-widest">Innovation</h4>
                        <p class="text-on-surface-variant text-sm font-light leading-relaxed">We leverage modern diagnostic tools and creative engineering to solve complex industrial bottlenecks.</p>
                    </div>
                    <div class="space-y-6 group">
                        <span class="font-headline text-6xl font-black text-outline-variant/20 group-hover:text-primary transition-colors">03</span>
                        <h4 class="font-headline font-bold text-xl uppercase tracking-widest">Excellence</h4>
                        <p class="text-on-surface-variant text-sm font-light leading-relaxed">We aim for 100% precision in every blueprint, every part sourced, and every engine overhaul we perform.</p>
                    </div>
                    <div class="space-y-6 group">
                        <span class="font-headline text-6xl font-black text-outline-variant/20 group-hover:text-primary transition-colors">04</span>
                        <h4 class="font-headline font-bold text-xl uppercase tracking-widest">Customer Success</h4>
                        <p class="text-on-surface-variant text-sm font-light leading-relaxed">Placing client needs and growth at the center of our operations. Your success defines our performance.</p>
                    </div>
                    <div class="space-y-6 group">
                        <span class="font-headline text-6xl font-black text-outline-variant/20 group-hover:text-primary transition-colors">05</span>
                        <h4 class="font-headline font-bold text-xl uppercase tracking-widest">Sustainability</h4>
                        <p class="text-on-surface-variant text-sm font-light leading-relaxed">Committing to environmentally responsible practices in all we do, ensuring long-term industrial and ecological balance.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Experience -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30">
            <div class="max-w-7xl mx-auto">
                <div class="bg-on-surface text-surface rounded-3xl p-12 lg:p-20 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
                    <div class="relative z-10 grid lg:grid-cols-2 gap-16 items-center">
                        <div class="space-y-6">
                            <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Track Record</span>
                            <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tight">Our <span class="text-primary">Experience.</span></h2>
                            <p class="text-surface-bright/60 font-light leading-relaxed">
                                From gas turbine commissioning to structural rehabilitation and procurement of critical OEM components, our team has delivered engineering solutions across multiple sectors. We combine local expertise with global sourcing capability — positioning us as a reliable partner in industrial performance management.
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="p-6 bg-surface/5 rounded-2xl border border-surface/10 text-center">
                                <span class="block font-headline text-3xl font-bold text-primary">2021</span>
                                <span class="text-[10px] font-label uppercase tracking-widest text-surface-bright/40">Founded</span>
                            </div>
                            <div class="p-6 bg-surface/5 rounded-2xl border border-surface/10 text-center">
                                <span class="block font-headline text-3xl font-bold text-primary">5+</span>
                                <span class="text-[10px] font-label uppercase tracking-widest text-surface-bright/40">Sectors Served</span>
                            </div>
                            <div class="p-6 bg-surface/5 rounded-2xl border border-surface/10 text-center">
                                <span class="block font-headline text-3xl font-bold text-primary">150+</span>
                                <span class="text-[10px] font-label uppercase tracking-widest text-surface-bright/40">Projects Delivered</span>
                            </div>
                            <div class="p-6 bg-surface/5 rounded-2xl border border-surface/10 text-center">
                                <span class="block font-headline text-3xl font-bold text-primary">OEM</span>
                                <span class="text-[10px] font-label uppercase tracking-widest text-surface-bright/40">Global Network</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Operational Approach -->
        <section class="py-24 px-5 sm:px-6 lg:px-12">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-3xl mx-auto mb-20">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Our Methodology</span>
                    <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tight mb-6">Operational <span class="text-primary">Approach.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed">A structured and transparent execution process to ensure consistent service quality and client satisfaction.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="text-center space-y-4 p-8 bg-surface-container-low rounded-2xl border border-outline-variant/10 group hover:border-primary/30 transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="text-2xl font-bold text-primary group-hover:text-on-primary">1</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Needs Assessment</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Understanding client requirements, site conditions and operational challenges.</p>
                    </div>
                    <div class="text-center space-y-4 p-8 bg-surface-container-low rounded-2xl border border-outline-variant/10 group hover:border-primary/30 transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="text-2xl font-bold text-primary group-hover:text-on-primary">2</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Planning &amp; Review</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Developing a clear technical and execution plan with engineering review.</p>
                    </div>
                    <div class="text-center space-y-4 p-8 bg-surface-container-low rounded-2xl border border-outline-variant/10 group hover:border-primary/30 transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="text-2xl font-bold text-primary group-hover:text-on-primary">3</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Structured Execution</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Deploying skilled personnel, tools and resources to deliver results.</p>
                    </div>
                    <div class="text-center space-y-4 p-8 bg-surface-container-low rounded-2xl border border-outline-variant/10 group hover:border-primary/30 transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="text-2xl font-bold text-primary group-hover:text-on-primary">4</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Quality &amp; Safety</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Implementing inspections, testing and compliance checks throughout.</p>
                    </div>
                    <div class="text-center space-y-4 p-8 bg-surface-container-low rounded-2xl border border-outline-variant/10 group hover:border-primary/30 transition-all">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mx-auto group-hover:bg-primary transition-colors">
                            <span class="text-2xl font-bold text-primary group-hover:text-on-primary">5</span>
                        </div>
                        <h4 class="font-headline font-bold text-sm uppercase tracking-widest">Handover &amp; Follow-Up</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed">Providing documentation, support and post-service assistance.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Management Team -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center gap-8 mb-16">
                    <h2 class="font-headline text-4xl font-bold whitespace-nowrap">Management Team</h2>
                    <div class="h-px bg-outline-variant/30 w-full"></div>
                </div>
                <p class="text-on-surface-variant font-light leading-relaxed max-w-3xl mb-12">
                    Led by experienced professionals in engineering, procurement, technical support, finance and business development, our team drives strategic growth while delivering excellence.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php if ($team_count > 0): ?>
                    <?php $idx = 0; foreach ($team_members as $m): ?>
                    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 text-center space-y-4 group hover:shadow-lg transition-all">
                        <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto group-hover:bg-primary transition-colors overflow-hidden">
                            <?php if ($m['photo_url']): ?>
                            <img src="<?php echo htmlspecialchars($m['photo_url']); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($m['name']); ?>">
                            <?php else: ?>
                            <span class="material-symbols-outlined text-4xl text-primary group-hover:text-on-primary"><?php echo $icons[$idx % count($icons)]; ?></span>
                            <?php endif; ?>
                        </div>
                        <h4 class="font-headline font-bold"><?php echo htmlspecialchars($m['name']); ?></h4>
                        <p class="text-xs text-on-surface-variant uppercase tracking-widest font-bold"><?php echo htmlspecialchars($m['position']); ?></p>
                        <?php if ($m['bio']): ?>
                        <p class="text-[10px] text-on-surface-variant font-light"><?php echo htmlspecialchars($m['bio']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php $idx++; endforeach; ?>
                    <?php else: ?>
                    <div class="col-span-full text-center py-12">
                        <p class="text-on-surface-variant italic">Team member information coming soon.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Technical Stats -->
        <section class="py-24 bg-surface-container-low/50">
            <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-12 grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                <div class="space-y-2">
                    <span class="block font-headline text-5xl font-bold text-primary tracking-tighter">150+</span>
                    <span class="block text-[10px] font-label uppercase tracking-widest text-outline font-bold">Projects Delivered</span>
                </div>
                <div class="space-y-2">
                    <span class="block font-headline text-5xl font-bold text-primary tracking-tighter">99.2%</span>
                    <span class="block text-[10px] font-label uppercase tracking-widest text-outline font-bold">Uptime Rate</span>
                </div>
                <div class="space-y-2">
                    <span class="block font-headline text-5xl font-bold text-primary tracking-tighter">100%</span>
                    <span class="block text-[10px] font-label uppercase tracking-widest text-outline font-bold">Indigenous</span>
                </div>
                <div class="space-y-2">
                    <span class="block font-headline text-5xl font-bold text-primary tracking-tighter">Verified</span>
                    <span class="block text-[10px] font-label uppercase tracking-widest text-outline font-bold">Global Network</span>
                </div>
            </div>
        </section>

        <!-- CSR -->
        <section class="py-24 px-5 sm:px-6 lg:px-12">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-6">
                        <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Corporate Responsibility</span>
                        <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tight">Social <span class="text-primary">Impact.</span></h2>
                        <p class="text-on-surface-variant font-light leading-relaxed">Our CSR efforts focus on skill development, community engagement and local employment generation. We invest in the communities where we operate, creating shared value through technical training programs and local workforce development.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                            <span class="text-3xl font-headline font-bold text-primary">Skill</span>
                            <p class="text-[10px] font-label uppercase tracking-widest text-outline font-bold mt-2">Development</p>
                        </div>
                        <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                            <span class="text-3xl font-headline font-bold text-primary">Local</span>
                            <p class="text-[10px] font-label uppercase tracking-widest text-outline font-bold mt-2">Employment</p>
                        </div>
                        <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                            <span class="text-3xl font-headline font-bold text-primary">Community</span>
                            <p class="text-[10px] font-label uppercase tracking-widest text-outline font-bold mt-2">Engagement</p>
                        </div>
                        <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 text-center">
                            <span class="text-3xl font-headline font-bold text-primary">Shared</span>
                            <p class="text-[10px] font-label uppercase tracking-widest text-outline font-bold mt-2">Value</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Clients & Partners -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-surface-container-low/30">
            <div class="max-w-7xl mx-auto text-center">
                <div class="max-w-3xl mx-auto space-y-8">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] block">Our Network</span>
                    <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tight">Clients &amp; <span class="text-primary">Partners.</span></h2>
                    <p class="text-on-surface-variant font-light leading-relaxed">
                        We have worked with multinational corporations, local businesses and government agencies across Nigeria's critical industries. Our partnerships with OEMs ensure quality and reliability in every component we source.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 pt-4">
                        <span class="px-6 py-3 bg-surface-container-lowest rounded-full text-xs font-bold border border-outline-variant/10">Multinational Corporations</span>
                        <span class="px-6 py-3 bg-surface-container-lowest rounded-full text-xs font-bold border border-outline-variant/10">Local Enterprises</span>
                        <span class="px-6 py-3 bg-surface-container-lowest rounded-full text-xs font-bold border border-outline-variant/10">Government Agencies</span>
                        <span class="px-6 py-3 bg-surface-container-lowest rounded-full text-xs font-bold border border-outline-variant/10">OEM Manufacturers</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Client Benefits -->
        <section class="py-24 px-5 sm:px-6 lg:px-12">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-primary font-label text-[10px] font-bold uppercase tracking-[0.4em] mb-4 block">Value Proposition</span>
                    <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tight mb-6">Client <span class="text-primary">Benefits.</span></h2>
                    <p class="text-on-surface-variant font-light">What you can expect when you partner with Wilsolvewel Nigeria Limited.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">engineering</span>
                        </div>
                        <h4 class="font-headline font-bold">Expert Engineering</h4>
                        <p class="text-sm text-on-surface-variant font-light">High-quality engineering expertise delivering technically sound solutions for complex industrial challenges.</p>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                        </div>
                        <h4 class="font-headline font-bold">Reliable Procurement</h4>
                        <p class="text-sm text-on-surface-variant font-light">Cost-effective procurement with verified suppliers ensuring genuine OEM and OEM-equivalent parts.</p>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">support_agent</span>
                        </div>
                        <h4 class="font-headline font-bold">Strong Support</h4>
                        <p class="text-sm text-on-surface-variant font-light">Professional technical support and field service ensuring minimal downtime and restored performance.</p>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <h4 class="font-headline font-bold">Compliance &amp; Safety</h4>
                        <p class="text-sm text-on-surface-variant font-light">Full adherence to industry standards and safety practices, ensuring regulatory compliance and asset protection.</p>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">forum</span>
                        </div>
                        <h4 class="font-headline font-bold">Clear Communication</h4>
                        <p class="text-sm text-on-surface-variant font-light">Transparent communication and project documentation ensuring full visibility throughout every engagement.</p>
                    </div>
                    <div class="p-8 bg-surface-container-lowest rounded-2xl border border-outline-variant/10 space-y-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">trending_up</span>
                        </div>
                        <h4 class="font-headline font-bold">Operational Value</h4>
                        <p class="text-sm text-on-surface-variant font-light">Greater operational value and efficiency through engineering-led solutions that protect assets and extend lifecycle.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Let's Work Together -->
        <section class="py-24 px-5 sm:px-6 lg:px-12 bg-on-surface text-surface text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
            <div class="max-w-4xl mx-auto space-y-10 relative z-10">
                <h2 class="font-headline text-4xl md:text-6xl font-bold tracking-tighter">Let's <span class="text-primary">Work Together.</span></h2>
                <p class="text-surface-bright/60 text-lg font-light max-w-2xl mx-auto leading-relaxed">
                    Wilsolvewel Nigeria Limited is ready to support your engineering, procurement and technical needs. Contact us today to discuss your project requirements, equipment challenges or supply needs. Our team will assess your situation and deliver solutions that keep your operations running efficiently and safely.
                </p>
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="contact.php" class="bg-primary text-on-primary px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                        Contact Us Today
                    </a>
                    <a href="services.html" class="bg-surface/10 text-surface-bright px-12 py-5 rounded-full font-headline font-bold text-sm uppercase tracking-widest border border-surface/20 hover:bg-surface/20 transition-all">
                        View Our Services
                    </a>
                </div>
            </div>
        </section>

    </main>
    <!-- Footer Shell -->
    <script src="./components/footer.js" data-root="./"></script>
</body>

</html>
