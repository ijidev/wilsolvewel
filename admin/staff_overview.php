<?php
include '../config.php';
$conn = get_db_connection();

if (session_status() === PHP_SESSION_NONE) session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: staff.php'); exit; }

$res = $conn->query("SELECT a.*, d.name as dept_name FROM admins a LEFT JOIN departments d ON a.department_id = d.id WHERE a.id = $id");
$staff = $res->fetch_assoc();
if (!$staff) { header('Location: staff.php'); exit; }

$site_name = get_setting('site_name') ?: 'Wilsolvewel';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Staff Overview | <?php echo htmlspecialchars($staff['name']); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .id-card-gradient { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); }
        .id-card-chip { background: linear-gradient(135deg, #FFD700 0%, #EAB308 100%); width: 40px; height: 30px; border-radius: 4px; position: relative; }
        .id-card-chip::after { content: ''; position: absolute; inset: 4px; border: 1px solid rgba(0,0,0,0.1); border-radius: 2px; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface lg:pl-64 flex min-h-screen">

<script src="../components/admin_sidenav.js" data-root="../"></script>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 z-20">
        <div class="flex items-center gap-4">
            <a href="staff.php" class="text-slate-400 hover:text-slate-900 transition-colors"><span class="material-symbols-outlined">arrow_back</span></a>
            <div>
                <h1 class="text-lg font-bold font-headline text-slate-900 leading-tight">Staff Overview</h1>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?php echo htmlspecialchars($staff['name']); ?></p>
            </div>
        </div>
        <button onclick="downloadIDCard()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">picture_as_pdf</span> DOWNLOAD ID CARD
        </button>
    </header>

    <main class="flex-1 overflow-y-auto p-6 md:p-12">
        <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Details Section -->
            <div class="space-y-8">
                <section>
                    <h2 class="text-3xl font-bold font-headline mb-2 text-slate-900"><?php echo htmlspecialchars($staff['name']); ?></h2>
                    <p class="text-primary font-bold uppercase tracking-[0.2em] text-xs"><?php echo htmlspecialchars($staff['role'] ?: 'Internal Staff'); ?></p>
                </section>

                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-4">Account Information</span>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Email Address</span>
                                <span class="text-xs font-bold"><?php echo htmlspecialchars($staff['email']); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Department</span>
                                <span class="text-xs font-bold"><?php echo htmlspecialchars($staff['dept_name'] ?: 'Unassigned'); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Status</span>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[10px] font-bold uppercase"><?php echo htmlspecialchars($staff['status'] ?: 'Active'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-900 p-6 rounded-3xl text-white shadow-xl relative overflow-hidden">
                        <div class="relative z-10">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-4">Security Clearance</span>
                            <p class="text-sm font-medium mb-4">Authorized access to high-precision engineering terminal nodes.</p>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                                <span class="text-[10px] font-bold uppercase tracking-widest">ACTIVE CLEARANCE</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-white opacity-5 text-9xl">verified_user</span>
                    </div>
                </div>
            </div>

            <!-- Virtual ID Card Section -->
            <div class="flex flex-col items-center col-span-1 lg:col-span-2 xl:col-span-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6">Staff Virtual ID Card</span>
                
                <div class="flex flex-col sm:flex-row gap-8 items-center" id="id-card-capture-container">
                    
                    <!-- FRONT VIEW -->
                    <div id="id-card-capture-front" class="w-[350px] h-[520px] id-card-gradient rounded-3xl shadow-2xl relative overflow-hidden p-8 flex flex-col items-center text-white border-4 border-slate-800 shrink-0">
                        <!-- Background Design -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
                        <div class="absolute -left-20 bottom-20 w-48 h-48 bg-primary/5 rounded-full blur-2xl"></div>
                        
                        <!-- Header -->
                        <div class="w-full flex justify-between items-start mb-8 z-10">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black tracking-widest uppercase text-primary"><?php echo $site_name; ?></span>
                                <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Engineering Personnel</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-slate-500 text-lg opacity-60">contactless</span>
                                <div class="id-card-chip"></div>
                            </div>
                        </div>

                        <!-- Photo -->
                        <div class="w-40 h-40 rounded-3xl bg-slate-800 border-4 border-slate-700 overflow-hidden mb-6 z-10 shadow-lg flex items-center justify-center">
                            <?php if(!empty($staff['profile_pic'])): ?>
                                <img src="../<?php echo htmlspecialchars($staff['profile_pic']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-5xl font-black text-slate-600 font-headline uppercase"><?php echo substr($staff['name'], 0, 2); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Name & Info -->
                        <div class="text-center z-10 w-full">
                            <h3 class="text-2xl font-black font-headline uppercase tracking-tight mb-1 truncate"><?php echo htmlspecialchars($staff['name']); ?></h3>
                            <div class="px-4 py-1 bg-primary text-on-primary rounded-full text-[10px] font-black uppercase tracking-widest mb-8 inline-block"><?php echo htmlspecialchars($staff['role'] ?: 'Staff'); ?></div>
                        </div>

                        <!-- Data Grid -->
                        <div class="w-full grid grid-cols-2 gap-4 mt-auto z-10 border-t border-slate-800 pt-6">
                            <div>
                                <span class="text-[8px] font-bold text-slate-500 uppercase block tracking-widest">ID NUMBER</span>
                                <span class="text-xs font-black font-headline text-slate-200">WLV-<?php echo str_pad($staff['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[8px] font-bold text-slate-500 uppercase block tracking-widest">DEPT</span>
                                <span class="text-xs font-black font-headline text-slate-200"><?php echo strtoupper(htmlspecialchars($staff['dept_name'] ?: 'GEN')); ?></span>
                            </div>
                            <div>
                                <span class="text-[8px] font-bold text-slate-500 uppercase block tracking-widest">JOIN DATE</span>
                                <span class="text-xs font-black font-headline text-slate-200"><?php echo date('M Y', strtotime($staff['created_at'])); ?></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[8px] font-bold text-slate-500 uppercase block tracking-widest">CLEARANCE</span>
                                <span class="text-xs font-black font-headline text-primary">L-4 AUTH</span>
                            </div>
                        </div>

                        <!-- Footer / QR Placeholder -->
                        <div class="mt-6 flex justify-between items-center w-full z-10 opacity-30">
                            <div class="flex flex-col gap-0.5">
                                <div class="w-24 h-1 bg-slate-600 rounded"></div>
                                <div class="w-16 h-1 bg-slate-600 rounded"></div>
                            </div>
                            <div class="w-8 h-8 border-2 border-slate-600 rounded p-1">
                                <div class="w-full h-full bg-slate-600 rounded-sm"></div>
                            </div>
                        </div>
                    </div>

                    <!-- BACK VIEW -->
                    <div id="id-card-capture-back" class="w-[350px] h-[520px] id-card-gradient rounded-3xl shadow-2xl relative overflow-hidden text-white border-4 border-slate-800 shrink-0">
                        <div class="w-full h-16 bg-slate-900 mt-8 shadow-inner"></div>
                        <div class="p-8 flex flex-col h-full">
                            <div class="w-full h-12 bg-white/10 rounded-md mb-6"></div>
                            <p class="text-[9px] text-slate-400 mb-4 leading-relaxed font-bold tracking-wide">
                                This card is the property of <span class="text-slate-200"><?php echo $site_name; ?></span>. It must be surrendered upon request or termination of employment.
                            </p>
                            <p class="text-[9px] text-slate-400 mb-8 leading-relaxed tracking-wide">
                                If found, please return to:<br>
                                <span class="text-slate-200 font-bold">Security Department</span><br>
                                Wilsolvewel Engineering HQ
                            </p>
                            
                            <div class="mt-auto border-t border-slate-800 pt-6">
                                <div class="flex items-center gap-4 mb-2">
                                    <span class="material-symbols-outlined text-primary text-3xl">policy</span>
                                    <div>
                                        <span class="text-[10px] font-black uppercase text-primary tracking-widest block">Level 4 Clearance</span>
                                        <span class="text-[8px] text-slate-500 uppercase tracking-widest">Authorized Access Only</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full mt-6 bg-white/5 h-12 flex items-center justify-center rounded border border-white/10">
                                <span class="font-barcode text-3xl tracking-[0.5em] opacity-50">||||||||||||||||||||</span>
                            </div>
                        </div>
                    </div>

                </div>

                <p class="mt-8 text-xs text-slate-400 max-w-sm text-center italic">Digital ID cards are valid for on-site terminal identification and HSSE clearance verification. Use the button above to generate a high-resolution printable PDF of both sides.</p>
            </div>
        </div>
    </main>
</div>

<script>
async function downloadIDCard() {
    const frontEl = document.getElementById('id-card-capture-front');
    const backEl = document.getElementById('id-card-capture-back');
    const name = "<?php echo addslashes($staff['name']); ?>".replace(/ /g, '_');
    
    // Capture Front
    const canvasFront = await html2canvas(frontEl, {
        scale: 3,
        useCORS: true,
        backgroundColor: null
    });
    const imgDataFront = canvasFront.toDataURL('image/png');

    // Capture Back
    const canvasBack = await html2canvas(backEl, {
        scale: 3,
        useCORS: true,
        backgroundColor: null
    });
    const imgDataBack = canvasBack.toDataURL('image/png');

    const { jsPDF } = window.jspdf;
    
    // Create PDF with dimensions that fit both cards side-by-side or stacked
    // Here we'll make a 2-page PDF
    const pdf = new jsPDF({
        orientation: 'p',
        unit: 'px',
        format: [canvasFront.width / 3, canvasFront.height / 3]
    });
    
    // Page 1: Front
    pdf.addImage(imgDataFront, 'PNG', 0, 0, canvasFront.width / 3, canvasFront.height / 3);
    
    // Page 2: Back
    pdf.addPage();
    pdf.addImage(imgDataBack, 'PNG', 0, 0, canvasBack.width / 3, canvasBack.height / 3);

    pdf.save(`ID_CARD_${name}.pdf`);
}
</script>
</body>
</html>
