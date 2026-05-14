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
                <div class="flex justify-between items-center w-full max-w-[520px] mb-6">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Virtual ID Card</span>
                    <div class="flex bg-slate-100 p-1 rounded-xl shadow-sm border border-slate-200">
                        <button onclick="toggleIDCard('front')" id="btn-front" class="px-4 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-widest bg-white shadow-sm text-slate-900 transition-all">Front</button>
                        <button onclick="toggleIDCard('back')" id="btn-back" class="px-4 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">Back</button>
                    </div>
                </div>
                
                <div class="flex flex-col gap-8 items-center relative w-[520px]" id="id-card-capture-container">
                    
                    <!-- FRONT VIEW -->
                    <div id="id-card-capture-front" class="w-[520px] h-[328px] id-card-gradient rounded-[1.5rem] shadow-2xl relative overflow-hidden flex text-white border-4 border-slate-800 shrink-0">
                        <!-- Left Section: Photo & Name -->
                        <div class="w-2/5 border-r border-slate-800 p-6 flex flex-col items-center justify-center bg-slate-900/50 z-10 relative">
                            <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
                            <div class="w-28 h-28 rounded-3xl bg-slate-800 border-4 border-slate-700 overflow-hidden mb-4 shadow-lg flex items-center justify-center shrink-0">
                                <?php if(!empty($staff['profile_pic'])): ?>
                                    <img src="../<?php echo htmlspecialchars($staff['profile_pic']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-4xl font-black text-slate-600 font-headline uppercase"><?php echo substr($staff['name'], 0, 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-center w-full">
                                <h3 class="text-lg font-black font-headline uppercase tracking-tight mb-1 truncate"><?php echo htmlspecialchars($staff['name']); ?></h3>
                                <div class="px-3 py-1 bg-primary text-on-primary rounded-full text-[8px] font-black uppercase tracking-widest inline-block truncate max-w-full"><?php echo htmlspecialchars($staff['role'] ?: 'Staff'); ?></div>
                            </div>
                        </div>

                        <!-- Right Section: Details -->
                        <div class="w-3/5 p-6 flex flex-col z-10 relative bg-[#0F172A]">
                            <div class="absolute -right-10 -top-10 w-48 h-48 bg-primary/10 rounded-full blur-3xl z-0 pointer-events-none"></div>
                            <div class="absolute -left-10 bottom-10 w-32 h-32 bg-primary/5 rounded-full blur-2xl z-0 pointer-events-none"></div>
                            
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-6 z-10">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black tracking-widest uppercase text-primary"><?php echo $site_name; ?></span>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase tracking-widest">Engineering Personnel</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-500 text-base opacity-60">contactless</span>
                                    <div class="id-card-chip scale-75 origin-right"></div>
                                </div>
                            </div>

                            <!-- Data Grid -->
                            <div class="grid grid-cols-2 gap-y-4 gap-x-2 mt-auto z-10">
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-widest">ID NUMBER</span>
                                    <span class="text-[11px] font-black font-headline text-slate-200">WLV-<?php echo str_pad($staff['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-widest">DEPT</span>
                                    <span class="text-[11px] font-black font-headline text-slate-200"><?php echo strtoupper(htmlspecialchars($staff['dept_name'] ?: 'GEN')); ?></span>
                                </div>
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-widest">JOIN DATE</span>
                                    <span class="text-[11px] font-black font-headline text-slate-200"><?php echo date('M Y', strtotime($staff['created_at'])); ?></span>
                                </div>
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-widest">CLEARANCE</span>
                                    <span class="text-[11px] font-black font-headline text-primary">L-4 AUTH</span>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-6 flex justify-between items-center w-full z-10 opacity-30 border-t border-slate-800 pt-4">
                                <div class="flex flex-col gap-0.5">
                                    <div class="w-20 h-1 bg-slate-600 rounded"></div>
                                    <div class="w-12 h-1 bg-slate-600 rounded"></div>
                                </div>
                                <div class="w-6 h-6 border-2 border-slate-600 rounded p-0.5">
                                    <div class="w-full h-full bg-slate-600 rounded-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BACK VIEW -->
                    <div id="id-card-capture-back" class="hidden w-[520px] h-[328px] id-card-gradient rounded-[1.5rem] shadow-2xl relative overflow-hidden text-white border-4 border-slate-800 shrink-0 flex-col">
                        <div class="w-full h-12 bg-slate-900 mt-6 shadow-inner shrink-0"></div>
                        <div class="p-6 flex flex-col h-full z-10">
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <div class="w-full h-8 bg-white/10 rounded-sm mb-4"></div>
                                    <p class="text-[8px] text-slate-400 mb-2 leading-relaxed font-bold tracking-wide">
                                        This card is the property of <span class="text-slate-200"><?php echo $site_name; ?></span>. It must be surrendered upon request or termination of employment.
                                    </p>
                                    <p class="text-[8px] text-slate-400 leading-relaxed tracking-wide">
                                        If found, please return to:<br>
                                        <span class="text-slate-200 font-bold">Security Department</span><br>
                                        Wilsolvewel Engineering HQ
                                    </p>
                                </div>
                                <div class="w-20 shrink-0 flex flex-col items-center justify-center border-l border-slate-800 pl-4">
                                    <div class="w-full bg-white/5 h-[100px] flex items-center justify-center rounded border border-white/10 overflow-hidden text-center relative">
                                        <!-- Vertical Barcode placeholder -->
                                        <div class="absolute inset-2 flex flex-col justify-between opacity-30">
                                            <div class="w-full h-[2px] bg-white"></div>
                                            <div class="w-full h-[4px] bg-white"></div>
                                            <div class="w-full h-[1px] bg-white"></div>
                                            <div class="w-full h-[3px] bg-white"></div>
                                            <div class="w-full h-[1px] bg-white"></div>
                                            <div class="w-full h-[4px] bg-white"></div>
                                            <div class="w-full h-[2px] bg-white"></div>
                                            <div class="w-full h-[1px] bg-white"></div>
                                            <div class="w-full h-[5px] bg-white"></div>
                                            <div class="w-full h-[2px] bg-white"></div>
                                            <div class="w-full h-[1px] bg-white"></div>
                                            <div class="w-full h-[3px] bg-white"></div>
                                            <div class="w-full h-[2px] bg-white"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-auto border-t border-slate-800 pt-4 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-primary text-2xl">policy</span>
                                    <div>
                                        <span class="text-[9px] font-black uppercase text-primary tracking-widest block">Level 4 Clearance</span>
                                        <span class="text-[7px] text-slate-500 uppercase tracking-widest">Authorized Access Only</span>
                                    </div>
                                </div>
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
function toggleIDCard(view) {
    const front = document.getElementById('id-card-capture-front');
    const back = document.getElementById('id-card-capture-back');
    const btnFront = document.getElementById('btn-front');
    const btnBack = document.getElementById('btn-back');

    if (view === 'front') {
        front.classList.remove('hidden');
        back.classList.add('hidden');
        btnFront.className = "px-4 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-widest bg-white shadow-sm text-slate-900 transition-all";
        btnBack.className = "px-4 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all";
    } else {
        front.classList.add('hidden');
        back.classList.remove('hidden');
        btnBack.className = "px-4 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-widest bg-white shadow-sm text-slate-900 transition-all";
        btnFront.className = "px-4 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all";
    }
}

async function downloadIDCard() {
    const frontEl = document.getElementById('id-card-capture-front');
    const backEl = document.getElementById('id-card-capture-back');
    const name = "<?php echo addslashes($staff['name']); ?>".replace(/ /g, '_');
    
    // Temporarily show both to ensure html2canvas can capture them properly
    const wasBackHidden = backEl.classList.contains('hidden');
    const wasFrontHidden = frontEl.classList.contains('hidden');
    
    frontEl.classList.remove('hidden');
    backEl.classList.remove('hidden');
    
    // Small delay to allow browser to render the unhidden elements
    await new Promise(r => setTimeout(r, 50));

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
    
    // Restore original visibility state
    if(wasBackHidden) backEl.classList.add('hidden');
    if(wasFrontHidden) frontEl.classList.add('hidden');

    const { jsPDF } = window.jspdf;
    
    // Create PDF with dimensions that fit both cards side-by-side or stacked
    // Here we'll make a 2-page PDF
    const pdf = new jsPDF({
        orientation: 'l', // Landscape page orientation for landscape cards
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
