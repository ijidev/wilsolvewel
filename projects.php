<?php require_once 'config.php'; secure_session_start(); generate_csrf_token();
$conn = get_db_connection();
$showcase_projects = [];
$res = $conn->query("SELECT * FROM showcase_projects WHERE status='Active' ORDER BY sort_order ASC, id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $showcase_projects[] = $row;
}
?><!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Case Studies | Industrial Precision Engineering</title>
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
  <script src="./components/header.js" data-root="./"></script>
  <script src="./components/effects.js"></script>
  <main class="relative pt-20">
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>
    <!-- Hero -->
    <section class="relative py-16 px-5 sm:px-6 lg:px-12 z-10 max-w-7xl mx-auto">
      <div class="flex flex-col items-start max-w-4xl space-y-4">
        <span class="font-label uppercase tracking-[0.3em] text-primary text-[10px] font-bold block">Proven Performance</span>
        <h1 class="font-headline text-3xl md:text-5xl font-bold tracking-tighter text-on-surface leading-[0.95]">
          INDUSTRIAL <br /><span class="text-primary italic">MILESTONES.</span>
        </h1>
        <p class="text-on-surface-variant text-base font-light leading-relaxed max-w-2xl">
          A portfolio of precision engineering, strategic procurement, and technical support across Nigeria's vital industrial sectors.
        </p>
      </div>
    </section>

    <!-- Project Grid -->
    <section class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-12 pb-24">
      <?php if (empty($showcase_projects)): ?>
      <div class="text-center py-20">
        <span class="material-symbols-outlined text-5xl text-outline mb-3">folder_off</span>
        <p class="text-on-surface-variant text-sm">No case studies available yet.</p>
      </div>
      <?php else: ?>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($showcase_projects as $project): ?>
        <a href="project-detail.php?id=<?= $project['id'] ?>" class="group block bg-surface-container-lowest rounded-2xl border border-outline-variant/10 overflow-hidden hover:shadow-xl hover:border-primary/30 transition-all duration-500">
          <div class="aspect-[16/10] overflow-hidden bg-surface-container-high">
            <img class="w-full h-full object-cover group-hover:scale-105 transition-all duration-700"
              src="<?= htmlspecialchars($project['image_url'] ?: 'https://placehold.co/600x375/1e293b/64748b?text=Project') ?>"
              alt="<?= htmlspecialchars($project['title']) ?>" />
          </div>
          <div class="p-5 space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-[9px] font-label text-primary font-bold uppercase tracking-widest"><?= htmlspecialchars($project['category'] ?: 'Project') ?></span>
              <?php if ($project['year']): ?>
              <span class="text-[9px] font-label text-outline font-bold uppercase tracking-widest"><?= htmlspecialchars($project['year']) ?></span>
              <?php endif; ?>
            </div>
            <h3 class="font-headline text-lg font-bold text-on-surface group-hover:text-primary transition-colors"><?= htmlspecialchars($project['title']) ?></h3>
            <p class="text-on-surface-variant text-sm font-light line-clamp-2"><?= htmlspecialchars($project['description'] ?: $project['client_name']) ?></p>
            <div class="pt-2 flex items-center gap-1 text-primary text-[10px] font-bold uppercase tracking-widest">
              View Case Study <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>

    <!-- CTA -->
    <section class="py-20 px-5 sm:px-6 lg:px-12 bg-on-surface text-surface text-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
      <div class="max-w-3xl mx-auto space-y-8 relative z-10">
        <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tighter">Ready to Engineer Your <br /><span class="text-primary italic">Next Milestone?</span></h2>
        <p class="text-surface-bright/60 text-base font-light max-w-xl mx-auto leading-relaxed">Partner with Wilsolvewel Engineering for solutions that prioritize asset longevity and operational integrity.</p>
        <div class="flex flex-wrap justify-center gap-4">
          <a href="contact.php" class="bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
            Consult an Engineer
          </a>
          <a href="services.php" class="bg-surface/10 text-surface-bright px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest border border-surface/20 hover:bg-surface/20 transition-all">
            Technical Scope
          </a>
        </div>
      </div>
    </section>
  </main>
  <script src="./components/footer.js" data-root="./"></script>
</body>

</html>