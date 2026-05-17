<?php require_once 'config.php'; secure_session_start(); generate_csrf_token();
$conn = get_db_connection();
$id = (int)($_GET['id'] ?? 0);
$project = null;
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM showcase_projects WHERE id=? AND status='Active'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    $stmt->close();
}
if (!$project) {
    header('Location: projects.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title><?= htmlspecialchars($project['title']) ?> | Wilsolvewel</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
  <style>
    .prose h2 { font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 0.75rem; color: #0F172A; }
    .prose h3 { font-family: 'Space Grotesk', sans-serif; font-size: 1.125rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.5rem; color: #0F172A; }
    .prose p { margin-bottom: 1rem; line-height: 1.75; color: #475569; }
    .prose ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
    .prose ul li { margin-bottom: 0.25rem; color: #475569; }
    .prose ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
    .prose ol li { margin-bottom: 0.25rem; color: #475569; }
    .prose img { border-radius: 0.75rem; margin: 1.5rem 0; max-width: 100%; }
    .prose blockquote { border-left: 3px solid #EAB308; padding-left: 1rem; margin: 1rem 0; color: #64748B; font-style: italic; }
    .prose strong { color: #0F172A; font-weight: 600; }
    .prose a { color: #EAB308; text-decoration: underline; }
  </style>
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed selection:text-on-primary-fixed site-gradient-bg">
  <script src="./components/header.js" data-root="./"></script>
  <script src="./components/effects.js"></script>
  <main class="relative pt-20">
    <div class="fixed inset-0 pointer-events-none technical-grid z-0"></div>

    <!-- Hero -->
    <section class="relative z-10">
      <div class="h-[50vh] min-h-[320px] relative overflow-hidden">
        <img class="w-full h-full object-cover grayscale"
          src="<?= htmlspecialchars($project['image_url'] ?: 'https://placehold.co/1200x600/1e293b/64748b?text=Project') ?>"
          alt="<?= htmlspecialchars($project['title']) ?>" />
        <div class="absolute inset-0 bg-gradient-to-t from-surface via-surface/60 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10 lg:p-16 max-w-5xl mx-auto">
          <div class="flex items-center gap-3 mb-3">
            <span class="text-[9px] font-label text-primary font-bold uppercase tracking-widest"><?= htmlspecialchars($project['category'] ?: 'Project') ?></span>
            <?php if ($project['year']): ?>
            <span class="w-1 h-1 rounded-full bg-outline"></span>
            <span class="text-[9px] font-label text-outline font-bold uppercase tracking-widest"><?= htmlspecialchars($project['year']) ?></span>
            <?php endif; ?>
            <?php if ($project['client_name']): ?>
            <span class="w-1 h-1 rounded-full bg-outline"></span>
            <span class="text-[9px] font-label text-outline font-bold uppercase tracking-widest"><?= htmlspecialchars($project['client_name']) ?></span>
            <?php endif; ?>
          </div>
          <h1 class="font-headline text-3xl sm:text-4xl lg:text-5xl font-bold text-on-surface tracking-tight leading-tight"><?= htmlspecialchars($project['title']) ?></h1>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="relative z-10 max-w-4xl mx-auto px-5 sm:px-6 lg:px-12 -mt-8">
      <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-10 lg:p-14 border border-outline-variant/10 shadow-xl">
        <?php if ($project['description']): ?>
        <div class="text-lg text-on-surface-variant font-light leading-relaxed mb-10 pb-10 border-b border-outline-variant/10">
          <?= nl2br(htmlspecialchars($project['description'])) ?>
        </div>
        <?php endif; ?>
        <?php if ($project['content']): ?>
        <div class="prose max-w-none"><?= $project['content'] ?></div>
        <?php else: ?>
        <div class="text-center py-12">
          <span class="material-symbols-outlined text-4xl text-outline mb-3">article</span>
          <p class="text-on-surface-variant text-sm">Full case study content coming soon.</p>
        </div>
        <?php endif; ?>
      </div>
      <div class="flex justify-between items-center mt-8 pb-16">
        <a href="projects.php" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-headline font-bold text-xs uppercase tracking-widest">
          <span class="material-symbols-outlined text-sm">arrow_back</span> All Projects
        </a>
        <a href="contact.php" class="anodized-gradient text-on-primary px-6 py-3 rounded-full font-headline font-bold text-xs uppercase tracking-widest shadow-lg hover:scale-105 transition-transform">
          Discuss Similar Project
        </a>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-20 px-5 sm:px-6 lg:px-12 bg-on-surface text-surface text-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-5 technical-grid pointer-events-none"></div>
      <div class="max-w-3xl mx-auto space-y-8 relative z-10">
        <h2 class="font-headline text-3xl md:text-5xl font-bold tracking-tighter">Need Similar <span class="text-primary italic">Engineering?</span></h2>
        <p class="text-surface-bright/60 text-base font-light max-w-xl mx-auto leading-relaxed">Let's discuss how we can apply our expertise to your next project.</p>
        <div class="flex flex-wrap justify-center gap-4">
          <a href="contact.php" class="bg-primary text-on-primary px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">Start a Conversation</a>
          <a href="services.html" class="bg-surface/10 text-surface-bright px-10 py-4 rounded-full font-headline font-bold text-sm uppercase tracking-widest border border-surface/20 hover:bg-surface/20 transition-all">View Services</a>
        </div>
      </div>
    </section>
  </main>
  <script src="./components/footer.js" data-root="./"></script>
</body>
</html>