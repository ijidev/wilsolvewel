<?php
require_once '../includes/admin_auth.php';
$conn = get_db_connection();
$admin_id = $_SESSION['admin_id'];

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: staff.php'); exit; }

$stmt = $conn->prepare("SELECT a.*, d.name as dept_name FROM admins a LEFT JOIN departments d ON a.department_id = d.id WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$stmt->close();
$staff = $res->fetch_assoc();
if (!$staff) { header('Location: staff.php'); exit; }

$site_name = get_setting('site_name') ?: 'Wilsolvewel';

$page_title = 'Staff Overview';
$page_subtitle = '';
$page_header_actions = '';
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

<script src="../components/admin_sidenav.js?v=2" data-root="../"></script>

<div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

    <main class="flex-1 overflow-y-auto p-6 md:p-12">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">
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
                
                <div class="flex flex-col gap-8 items-center relative w-full max-w-[520px]" id="id-card-capture-container">
                    
                    <!-- FRONT VIEW -->
                    <div id="id-card-scale-front" class="w-full overflow-hidden" style="height: 328px;">
                    <div id="id-card-capture-front" class="w-[520px] h-[328px] origin-top-left bg-slate-900 rounded-[1.5rem] shadow-2xl relative overflow-hidden flex text-white border border-white/10 shrink-0 id-card-gradient">
                        <!-- Left Section: Photo & Name -->
                        <div class="w-2/5 border-r border-white/5 p-6 flex flex-col items-center justify-center bg-black/20 z-10 relative backdrop-blur-sm">
                            <div class="w-28 h-28 rounded-full bg-slate-800 border border-white/10 overflow-hidden mb-5 shadow-2xl flex items-center justify-center shrink-0 p-1">
                                <div class="w-full h-full rounded-full overflow-hidden bg-slate-700 flex items-center justify-center">
                                    <?php if(!empty($staff['avatar'])): ?>
                                        <img src="../<?php echo htmlspecialchars($staff['avatar']); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span class="text-4xl font-black text-slate-500 font-headline uppercase"><?php echo substr($staff['name'], 0, 2); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="text-center w-full">
                                <h3 class="text-lg font-black font-headline uppercase tracking-tight mb-2 text-slate-100 truncate"><?php echo htmlspecialchars($staff['name']); ?></h3>
                                <div class="px-3 py-1 bg-white/5 border border-white/10 text-slate-300 rounded-full text-[8px] font-bold uppercase tracking-[0.2em] inline-block truncate max-w-full"><?php echo htmlspecialchars($staff['role'] ?: 'Staff'); ?></div>
                            </div>
                        </div>

                        <!-- Right Section: Details -->
                        <div class="w-3/5 p-8 flex flex-col z-10 relative bg-gradient-to-br from-slate-900 to-slate-950">
                            <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl z-0 pointer-events-none mix-blend-screen"></div>
                            
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-8 z-10">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[11px] font-black tracking-widest uppercase text-primary"><?php echo $site_name; ?></span>
                                    <span class="text-[7px] font-medium text-slate-400 uppercase tracking-widest opacity-80">Engineering Personnel</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-slate-400 text-base opacity-50">contactless</span>
                                    <div class="id-card-chip scale-75 origin-right opacity-80"></div>
                                </div>
                            </div>

                            <!-- Data Grid -->
                            <div class="grid grid-cols-2 gap-y-5 gap-x-4 mt-auto z-10">
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-[0.2em] mb-1">ID NUMBER</span>
                                    <span class="text-[12px] font-black font-headline text-slate-200 tracking-wider">WLV-<?php echo str_pad($staff['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-[0.2em] mb-1">DEPT</span>
                                    <span class="text-[12px] font-black font-headline text-slate-200 tracking-wider"><?php echo strtoupper(htmlspecialchars($staff['dept_name'] ?: 'GEN')); ?></span>
                                </div>
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-[0.2em] mb-1">JOIN DATE</span>
                                    <span class="text-[12px] font-black font-headline text-slate-200 tracking-wider"><?php echo date('M Y', strtotime($staff['created_at'])); ?></span>
                                </div>
                                <div>
                                    <span class="text-[7px] font-bold text-slate-500 uppercase block tracking-[0.2em] mb-1">CLEARANCE</span>
                                    <span class="text-[12px] font-black font-headline text-primary tracking-wider flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></div> L-4 AUTH</span>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-8 flex justify-between items-center w-full z-10 opacity-40 border-t border-white/5 pt-4">
                                <div class="flex flex-col gap-1">
                                    <div class="w-16 h-[2px] bg-slate-500 rounded-full"></div>
                                    <div class="w-10 h-[2px] bg-slate-500 rounded-full"></div>
                                </div>
                                <div class="w-6 h-6 border border-slate-500 rounded flex items-center justify-center p-0.5 opacity-50">
                                    <div class="w-full h-full bg-slate-500 rounded-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- BACK VIEW -->
                    <div id="id-card-scale-back" class="w-full overflow-hidden hidden" style="height: 328px;">
                    <div id="id-card-capture-back" class="w-[520px] h-[328px] origin-top-left bg-slate-950 rounded-[1.5rem] shadow-2xl relative overflow-hidden text-white border border-white/10 shrink-0 flex-col">
                        <div class="w-full h-14 bg-black mt-6 shadow-inner shrink-0 border-y border-white/5 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent skew-x-12 translate-x-full"></div>
                        </div>
                        <div class="p-8 flex flex-col h-full z-10 relative">
                            <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl z-0 pointer-events-none mix-blend-screen"></div>
                            
                            <div class="flex gap-6 relative z-10">
                                <div class="flex-1">
                                    <div class="w-3/4 h-8 bg-white/5 rounded border border-white/5 mb-5 flex items-center px-3">
                                        <span class="text-[7px] text-slate-500 uppercase tracking-widest font-bold opacity-50">Authorized Signature</span>
                                    </div>
                                    <p class="text-[8px] text-slate-400 mb-3 leading-loose font-medium tracking-wide">
                                        This card is the property of <strong class="text-slate-200"><?php echo $site_name; ?></strong>. It must be surrendered upon request or termination of employment. Non-transferable.
                                    </p>
                                    <p class="text-[8px] text-slate-400 leading-loose tracking-wide">
                                        If found, please return to:<br>
                                        <strong class="text-slate-200">Security Department</strong><br>
                                        Wilsolvewel Engineering HQ
                                    </p>
                                </div>
                                <div class="w-20 shrink-0 flex flex-col items-center justify-center border-l border-white/5 pl-6">
                                    <div class="w-full bg-black/40 h-[110px] flex items-center justify-center rounded border border-white/5 overflow-hidden text-center relative shadow-inner">
                                        <!-- Vertical Barcode placeholder -->
                                        <div class="absolute inset-3 flex flex-col justify-between opacity-40">
                                            <div class="w-full h-[2px] bg-slate-300"></div>
                                            <div class="w-full h-[4px] bg-slate-300"></div>
                                            <div class="w-full h-[1px] bg-slate-300"></div>
                                            <div class="w-full h-[3px] bg-slate-300"></div>
                                            <div class="w-full h-[1px] bg-slate-300"></div>
                                            <div class="w-full h-[5px] bg-slate-300"></div>
                                            <div class="w-full h-[2px] bg-slate-300"></div>
                                            <div class="w-full h-[1px] bg-slate-300"></div>
                                            <div class="w-full h-[4px] bg-slate-300"></div>
                                            <div class="w-full h-[2px] bg-slate-300"></div>
                                            <div class="w-full h-[1px] bg-slate-300"></div>
                                            <div class="w-full h-[3px] bg-slate-300"></div>
                                            <div class="w-full h-[2px] bg-slate-300"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-auto border-t border-white/5 pt-5 flex justify-between items-center relative z-10">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary text-sm">policy</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black uppercase text-slate-200 tracking-widest block mb-0.5">Level 4 Clearance</span>
                                        <span class="text-[7px] text-primary uppercase tracking-[0.2em] font-bold">Authorized Access Only</span>
                                    </div>
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
    const front = document.getElementById('id-card-scale-front');
    const back = document.getElementById('id-card-scale-back');
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
    requestAnimationFrame(scaleIDCard);
}

function scaleIDCard() {
    const cards = [
        { wrapper: document.getElementById('id-card-scale-front'), inner: document.getElementById('id-card-capture-front') },
        { wrapper: document.getElementById('id-card-scale-back'), inner: document.getElementById('id-card-capture-back') }
    ];
    cards.forEach(function(c) {
        if (!c.wrapper || !c.inner) return;
        var w = c.wrapper.clientWidth;
        if (w < 520) {
            var s = w / 520;
            c.inner.style.transform = 'scale(' + s + ')';
            c.wrapper.style.height = (328 * s) + 'px';
        } else {
            c.inner.style.transform = '';
            c.wrapper.style.height = '328px';
        }
    });
}
window.addEventListener('load', scaleIDCard);
window.addEventListener('resize', scaleIDCard);

async function downloadIDCard() {
    const frontEl = document.getElementById('id-card-capture-front');
    const backEl = document.getElementById('id-card-capture-back');
    const frontWrapper = document.getElementById('id-card-scale-front');
    const backWrapper = document.getElementById('id-card-scale-back');
    const name = "<?php echo addslashes($staff['name']); ?>".replace(/ /g, '_');
    
    // Temporarily show both and reset transforms for full-res capture
    const wasBackHidden = backWrapper.classList.contains('hidden');
    const wasFrontHidden = frontWrapper.classList.contains('hidden');
    const hadFrontTransform = frontEl.style.transform;
    const hadBackTransform = backEl.style.transform;
    
    frontWrapper.classList.remove('hidden');
    backWrapper.classList.remove('hidden');
    frontEl.style.transform = '';
    backEl.style.transform = '';
    frontWrapper.style.height = '328px';
    backWrapper.style.height = '328px';
    
    await new Promise(r => setTimeout(r, 50));

    const canvasFront = await html2canvas(frontEl, {
        scale: 3,
        useCORS: true,
        backgroundColor: null
    });
    const imgDataFront = canvasFront.toDataURL('image/png');

    const canvasBack = await html2canvas(backEl, {
        scale: 3,
        useCORS: true,
        backgroundColor: null
    });
    const imgDataBack = canvasBack.toDataURL('image/png');
    
    // Restore visibility and transforms
    if(wasBackHidden) backWrapper.classList.add('hidden');
    if(wasFrontHidden) frontWrapper.classList.add('hidden');
    frontEl.style.transform = hadFrontTransform;
    backEl.style.transform = hadBackTransform;
    scaleIDCard();

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
