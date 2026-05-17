<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Platform Documentation | Terminal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet"/>
    <script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#EAB308","on-primary":"#000000",surface:"#F8FAFC","on-surface":"#0F172A"},fontFamily:{headline:["Space Grotesk"],body:["Manrope"]}}}}</script>
    <style>
        .custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20}
        html{scroll-behavior:smooth}
        .doc-section{scroll-margin-top:7rem}
        .step-num{width:28px;height:28px;border-radius:50%;background:#0F172A;color:#fff;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0}
        
        .nav-link { transition: all 0.2s ease; }
        .nav-link.active { background-color: #f8fafc; color: #EAB308; border-left: 4px solid #EAB308; padding-left: 1rem; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-body text-on-surface overflow-hidden h-screen flex flex-col">

    <!-- Top Header -->
    <header class="h-16 bg-white border-b border-slate-100 flex justify-between items-center px-6 shrink-0 z-20">
        <div>
            <h1 class="text-lg font-bold font-headline text-slate-900">Platform Documentation</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Step-by-step guide for non-technical users</p>
        </div>
        <a href="/dashboard" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-800 transition-colors">Return to Dashboard</a>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- TOC Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-50 overflow-y-auto custom-scrollbar p-5 shrink-0 hidden md:flex md:flex-col">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4 pl-4">Chapters</p>
            <nav id="tocNav" class="flex-1 space-y-1">
                <a href="#welcome" class="nav-link active block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Welcome</a>
                <a href="#dashboard" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Dashboard Overview</a>
                <a href="#clients" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Client Management</a>
                <a href="#projects" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Project Operations</a>
                <a href="#assets" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Asset Register</a>
                <a href="#procurement" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Logistics & Procurement</a>
                <a href="#tickets" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Support Tickets</a>
                <a href="#inquiries" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Inquiries</a>
                <a href="#hsse" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">HSSE Operations</a>
                <a href="#staff" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Staff Management</a>
                <a href="#departments" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Departments</a>
                <a href="#security" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Security & Access</a>
                <a href="#smtp" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Email Setup (SMTP)</a>
                <a href="#profile" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">My Profile</a>
                <a href="#client-portal" class="nav-link block px-4 py-2.5 rounded-r-xl text-sm font-bold text-slate-600 hover:text-primary transition-all">Client Terminal</a>
            </nav>
        </aside>

        <!-- Main -->
        <main id="mainContent" class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-12 bg-white relative">
            <div class="max-w-3xl mx-auto space-y-24 pb-32">

                <!-- WELCOME -->
                <section id="welcome" class="doc-section space-y-4">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl text-primary">auto_awesome</span>
                    </div>
                    <h2 class="text-3xl font-bold font-headline text-slate-900">Welcome to your Terminal</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">This guide explains every feature in plain language. No technical knowledge required — just follow the steps.</p>
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 text-xs text-amber-700 font-medium">
                        <strong>Tip:</strong> On mobile, tap the <strong>grid icon</strong> (bottom-left) to access the main menu.
                    </div>
                </section>

                <!-- Dashboard Overview -->
                <section id="dashboard" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Dashboard Overview</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">The Dashboard is your main control center. It gives you an immediate bird's-eye view of everything happening in the company.</p>

                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                            <h3 class="text-xl font-bold font-headline text-slate-900 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">insights</span> Key Metrics</h3>
                            <ul class="list-disc list-inside space-y-3 text-slate-700 text-sm">
                                <li><strong>Active Projects:</strong> Shows how many projects are currently ongoing vs the total recorded.</li>
                                <li><strong>Open Tickets:</strong> Tracks unresolved client support requests and highlights urgent issues.</li>
                                <li><strong>Company Assets:</strong> Displays the total count and estimated financial value of all registered company equipment.</li>
                                <li><strong>Pending Inquiries:</strong> Unread messages submitted through the public website contact form.</li>
                            </ul>
                            
                            <p class="text-sm text-slate-600 mt-6">Below the metrics, you'll find a quick-access list of the most recent Support Tickets and a Live Activity Feed showing the latest actions taken by staff across the system.</p>
                        </div>
                    </div>
                </section>

                <!-- Client Management -->
                <section id="clients" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Client Management (CRM)</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">The Client section is where you manage the companies and individuals you do business with. Clients must be added here before you can assign them to Projects or process their Tickets.</p>

                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                            <h3 class="text-xl font-bold font-headline text-slate-900 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">person_add</span> Adding a New Client</h3>
                            <ul class="list-decimal list-inside space-y-3 text-slate-700 text-sm">
                                <li>Click the <strong>Add Client</strong> button in the top right.</li>
                                <li>Fill in their company name, primary contact name, email, and phone number.</li>
                                <li>In the "Account Setup" section, you have three choices:
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1 text-slate-500">
                                        <li><strong>Skip for now:</strong> Creates the account, but the client cannot log in yet.</li>
                                        <li><strong>Manually set password:</strong> You choose their password and tell them what it is.</li>
                                        <li><strong>Send setup link:</strong> The system automatically emails them a secure link so they can choose their own password.</li>
                                    </ul>
                                </li>
                                <li>Click <strong>Save Client</strong>.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Project Operations -->
                <section id="projects" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Project Operations</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">This module allows you to track ongoing work for specific clients. It acts as a central hub for reporting progress directly to the client.</p>

                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                            <h3 class="text-xl font-bold font-headline text-slate-900 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">folder_special</span> Managing Projects</h3>
                            <ul class="list-decimal list-inside space-y-3 text-slate-700 text-sm">
                                <li>Click <strong>New Project</strong> to start. Select the Client this project belongs to.</li>
                                <li>Set the budget, estimated timeline, and a brief description.</li>
                                <li>Click on a project in the left-hand list to view its details.</li>
                                <li><strong>Project Reports:</strong> Use the report text box to write daily, weekly, or momentary updates. When you click "Post Report", this update is logged and will be visible to the client on their own portal.</li>
                                <li>Clients can leave comments on your reports, which will appear highlighted below your report entry.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Asset Register -->
                <section id="assets" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Asset Register</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">The Asset Register tracks all physical or digital equipment owned by the company. You can assign these assets to specific projects to keep track of where resources are deployed.</p>

                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                            <h3 class="text-xl font-bold font-headline text-slate-900 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">inventory_2</span> Logging Assets</h3>
                            <ul class="list-decimal list-inside space-y-3 text-slate-700 text-sm">
                                <li>Click <strong>Log Asset</strong>.</li>
                                <li>Enter the asset name and category type (e.g., "Heavy Machinery", "Vehicles").</li>
                                <li>Update its status (Active, In Maintenance, or Retired).</li>
                                <li><strong>Assignment:</strong> You can leave an asset as "Unassigned", or attach it to a specific active Project from the dropdown menu.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Logistics & Procurement -->
                <section id="procurement" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Logistics & Procurement</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">Track every order from creation to delivery — a full logistics command center.</p>

                    <div class="space-y-6">
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-primary">
                            <p class="text-sm font-bold text-slate-900 mb-2">Creating an Order</p>
                            <ol class="text-xs text-slate-500 space-y-2 list-decimal ml-4">
                                <li>Click <strong>"Procurement"</strong> in the sidebar</li>
                                <li>Click <strong>"INITIATE ORDER"</strong> (top-right)</li>
                                <li>Fill in the item name, quantity, price, and supplier</li>
                                <li>The budget summary auto-calculates on the right</li>
                                <li>Click <strong>"INITIATE ORDER"</strong> to save</li>
                            </ol>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm border-l-4 border-l-slate-900">
                            <p class="text-sm font-bold text-slate-900 mb-2">Tracking & Updating an Order</p>
                            <ol class="text-xs text-slate-500 space-y-2 list-decimal ml-4">
                                <li>Click any order in the left column to view its details</li>
                                <li>In the <strong>"Log Logistics Update"</strong> panel, set the new status</li>
                                <li>Enter the current location (e.g. "Lagos Port") and tracking ID</li>
                                <li>Add an internal note if needed</li>
                                <li>Click <strong>"LOG UPDATE"</strong></li>
                            </ol>
                        </div>

                        <div class="bg-slate-900 text-white p-6 rounded-2xl">
                            <p class="text-sm font-bold mb-3">Available Order Statuses</p>
                            <div class="grid grid-cols-2 gap-2 text-[10px] text-slate-300">
                                <span>• Pending</span><span>• Order Confirmed</span>
                                <span>• Processing</span><span>• Dispatched</span>
                                <span>• In Transit</span><span>• Held by Customs</span>
                                <span>• Awaiting Clearance</span><span>• Out for Delivery</span>
                                <span>• Delivered</span><span>• Completed</span>
                                <span>• Cancelled</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-3 border-t border-white/10 pt-2">Every update is saved in the <strong>Shipment History</strong> timeline with the date, who made the change, and any notes.</p>
                        </div>
                    </div>
                </section>

                <!-- Support Tickets -->
                <section id="tickets" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Support Tickets</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">The ticketing system is how clients request help, report issues, or assign tasks to the company. It functions like a live chat thread for specific problems.</p>

                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                            <h3 class="text-xl font-bold font-headline text-slate-900 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">forum</span> Handling Tickets</h3>
                            <ul class="list-decimal list-inside space-y-3 text-slate-700 text-sm">
                                <li>Tickets appear in the left sidebar. Click one to read the full conversation.</li>
                                <li><strong>Assigning Responsibility:</strong> At the top of the ticket, you can assign it to a specific Department (e.g., "Engineering") and a specific Staff Member.</li>
                                <li><strong>Updating Status:</strong> Change the status from "Open" to "In Progress", "Resolved", or "Closed". Closed tickets cannot receive new replies.</li>
                                <li>Type your message in the chat box at the bottom and click the send icon to reply directly to the client.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- INQUIRIES -->
                <section id="inquiries" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Managing Inquiries</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">When a customer fills out the contact form on the website, their message appears here.</p>
                    
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-5">
                        <div class="flex gap-3"><div class="step-num">1</div><div><p class="text-sm font-bold text-slate-900">Open Inquiries</p><p class="text-xs text-slate-500">Click <strong>"Inquiries"</strong> in the sidebar. You'll see a list of messages on the left.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">2</div><div><p class="text-sm font-bold text-slate-900">Read the message</p><p class="text-xs text-slate-500">Click any message card. The full details appear on the right: name, email, project type, and their message.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">3</div><div><p class="text-sm font-bold text-slate-900">Reply or Forward</p><p class="text-xs text-slate-500">Use <strong>"Compose Response"</strong> to email them back. Or click the <strong>Forward arrow</strong> to send it to a colleague's email.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">4</div><div><p class="text-sm font-bold text-slate-900">Archive when done</p><p class="text-xs text-slate-500">Click the <strong>Archive icon</strong> (box with arrow) to remove it from your active list. It stays in the database for records.</p></div></div>
                    </div>
                </section>

                <!-- HSSE OPERATIONS -->
                <section id="hsse" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">HSSE Operations</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">Monitor Health, Safety, and Environment metrics through an automated, real-time command center.</p>
                    
                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-emerald-500">
                            <h3 class="text-xl font-bold font-headline text-slate-900 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-emerald-500">shield_with_heart</span> Automated Safety Metrics</h3>
                            <ul class="space-y-4 text-slate-700 text-sm">
                                <li>
                                    <strong>Safe Days Counter:</strong> This resets automatically. Every time a "High Severity" observation is logged, the system recognizes a safety incident and resets the counter to zero.
                                </li>
                                <li>
                                    <strong>Milestone Archive:</strong> When the counter resets, the system doesn't just erase the previous record—it archives it. You can see the "Last Record" in the Milestone History at the bottom of the page.
                                </li>
                                <li>
                                    <strong>Compliance Index:</strong> Automatically calculated as (Resolved Safety Issues / Total Issues). This shows how active the team is in fixing safety hazards.
                                </li>
                            </ul>
                        </div>

                        <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl">
                            <h3 class="text-xl font-bold font-headline mb-4">Logging Observations</h3>
                            <ol class="list-decimal list-inside space-y-3 text-slate-300 text-sm">
                                <li>Click <strong>"LOG OBSERVATION"</strong> on the HSSE dashboard.</li>
                                <li>Select the type (Routine, Hazard, Incident) and <strong>Severity</strong>.</li>
                                <li><strong class="text-red-400">Caution:</strong> Setting severity to "High" will trigger an automatic reset of the Safe Days counter and archive a new milestone.</li>
                                <li>Linked the observation to a specific Project if applicable.</li>
                                <li>Describe the findings and click <strong>"Submit Report"</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </section>

                <!-- STAFF -->
                <section id="staff" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Staff Management</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">Add, edit, or remove team members who can log in to the admin terminal.</p>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm border-t-4 border-t-primary">
                            <p class="text-sm font-bold text-slate-900 mb-2">Adding a Staff Member</p>
                            <ol class="text-xs text-slate-500 space-y-2 list-decimal ml-4">
                                <li>Click <strong>"Staff"</strong> in the sidebar</li>
                                <li>Click <strong>"ADD NEW STAFF"</strong> (top-right)</li>
                                <li>Fill in: name, email, role, department</li>
                                <li>Set a temporary password for them</li>
                                <li>Click <strong>"Save Profile"</strong></li>
                            </ol>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm border-t-4 border-t-slate-900">
                            <p class="text-sm font-bold text-slate-900 mb-2">Editing / Suspending</p>
                            <ol class="text-xs text-slate-500 space-y-2 list-decimal ml-4">
                                <li>Find the staff card and click <strong>"Edit Member"</strong></li>
                                <li>Update any fields you need</li>
                                <li>To block access: change Status to <strong>"Suspended"</strong></li>
                                <li>Leave password blank to keep their current one</li>
                                <li>Click <strong>"Save Profile"</strong></li>
                            </ol>
                        </div>
                    </div>
                </section>

                <!-- DEPARTMENTS -->
                <section id="departments" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Departments</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">Organize staff into logical units for easier ticket routing and access control.</p>

                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-5">
                        <div class="flex gap-3"><div class="step-num">1</div><div><p class="text-sm font-bold text-slate-900">Create a department</p><p class="text-xs text-slate-500">Go to <strong>"Departments"</strong>, click <strong>"REGISTER DEPT"</strong>, give it a name (e.g. "Operations"), and save.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">2</div><div><p class="text-sm font-bold text-slate-900">Assign a permission template</p><p class="text-xs text-slate-500">Link a template to control what pages this department's staff can access. See the Permissions section below.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">3</div><div><p class="text-sm font-bold text-slate-900">Add staff to it</p><p class="text-xs text-slate-500">When editing a staff member, pick their department from the dropdown. Their permissions update automatically.</p></div></div>
                    </div>
                </section>

                <!-- PERMISSIONS -->
                <section id="security" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Security & Access Control</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">Control what each department is allowed to see or edit within the terminal.</p>

                    <div class="bg-slate-900 text-white p-6 rounded-2xl space-y-4">
                        <p class="text-sm font-bold">How "Templates" work</p>
                        <p class="text-xs text-slate-400">Templates are like roles. Instead of setting permissions for every person individually, create a template and apply it to a department. Everyone in that department inherits the same access.</p>
                        <div class="space-y-3 pt-3 border-t border-white/10">
                            <div class="flex gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span><p class="text-xs text-slate-300"><strong>Read Access</strong> — Can view the page but cannot edit or delete records.</p></div>
                            <div class="flex gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span><p class="text-xs text-slate-300"><strong>Write Access</strong> — Full power to create, edit, and delete.</p></div>
                            <div class="flex gap-3"><span class="material-symbols-outlined text-slate-500 text-sm">visibility_off</span><p class="text-xs text-slate-300"><strong>No Access</strong> — The menu item disappears entirely for them.</p></div>
                        </div>
                    </div>
                </section>

                <!-- SMTP -->
                <section id="smtp" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Email Setup (SMTP)</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">If emails aren't sending (forwarding inquiries, client setup links), check your SMTP settings.</p>

                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-xs text-slate-500 mb-4">Go to <strong>"Settings"</strong> -> <strong>"SMTP Setup"</strong> in the sidebar. Fill in these fields:</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Host</p><p class="text-xs font-mono text-slate-600">e.g. smtp.gmail.com</p></div>
                            <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Port</p><p class="text-xs font-mono text-slate-600">587 or 465</p></div>
                            <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Username</p><p class="text-xs font-mono text-slate-600">your-email@gmail.com</p></div>
                            <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Password</p><p class="text-xs font-mono text-slate-600">App-specific password</p></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-4">Click <strong>"Test Connection"</strong> to verify it works before saving.</p>
                    </div>
                </section>

                <!-- PROFILE -->
                <section id="profile" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">My Profile</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">Update your personal account details and virtual ID card.</p>

                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-5">
                        <div class="flex gap-3"><div class="step-num">1</div><div><p class="text-sm font-bold text-slate-900">Open your profile</p><p class="text-xs text-slate-500">Click <strong>"My Profile"</strong> at the bottom of the sidebar.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">2</div><div><p class="text-sm font-bold text-slate-900">Edit your details</p><p class="text-xs text-slate-500">Update your name or email. To change your password, type a new one in the password field. Leave it blank to keep your current password.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">3</div><div><p class="text-sm font-bold text-slate-900">Upload Avatar</p><p class="text-xs text-slate-500">Upload a profile picture to personalize your ID card and sidebar widget.</p></div></div>
                        <div class="flex gap-3"><div class="step-num">4</div><div><p class="text-sm font-bold text-slate-900">Save</p><p class="text-xs text-slate-500">Click <strong>"Save Profile"</strong> to finalize your changes.</p></div></div>
                    </div>
                </section>

                <!-- CLIENT PORTAL -->
                <section id="client-portal" class="doc-section">
                    <h2 class="text-3xl font-bold font-headline text-slate-900 mb-2">Client Terminal</h2>
                    <p class="text-slate-600 leading-relaxed mb-8">How your clients interact with the company through their own portal.</p>

                    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">login</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Login & Access</h4>
                                <p class="text-xs text-slate-500">Clients log in via <code>/client/login.php</code> using the credentials you set up or they created.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">analytics</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Project Visibility</h4>
                                <p class="text-xs text-slate-500">They can see active projects, read maintenance reports you post, and track logistics updates in real-time.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">confirmation_number</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Direct Support</h4>
                                <p class="text-xs text-slate-500">Clients can open new support tickets directly from their terminal, which appear instantly in your Admin Support module.</p>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- Active Link Highlighting Script -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sections = document.querySelectorAll('.doc-section');
            const navLinks = document.querySelectorAll('.nav-link');
            const mainContent = document.getElementById('mainContent');

            const observerOptions = {
                root: mainContent,
                rootMargin: '-20px 0px -60% 0px',
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        
                        // Remove active class from all links
                        navLinks.forEach(link => {
                            link.classList.remove('active', 'border-l-4', 'border-primary', 'pl-4', 'bg-slate-50');
                        });
                        
                        // Add active class to corresponding link
                        const activeLink = document.querySelector(`.nav-link[href="#${id}"]`);
                        if (activeLink) {
                            activeLink.classList.add('active', 'border-l-4', 'border-primary', 'pl-4', 'bg-slate-50');
                        }
                    }
                });
            }, observerOptions);

            sections.forEach(section => {
                observer.observe(section);
            });
            
            // Smooth scroll adjustment for container
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    
                    if (targetSection) {
                        // Calculate position relative to container
                        const containerTop = mainContent.getBoundingClientRect().top;
                        const elementTop = targetSection.getBoundingClientRect().top;
                        const scrollPos = elementTop - containerTop + mainContent.scrollTop - 80;
                        
                        mainContent.scrollTo({
                            top: scrollPos,
                            behavior: 'smooth'
                        });
                        
                        // Update URL hash without jumping
                        history.pushState(null, null, `#${targetId}`);
                    }
                });
            });
            
            // Set initial active state based on hash if present
            if(window.location.hash) {
                setTimeout(() => {
                    const targetSection = document.querySelector(window.location.hash);
                    if(targetSection) {
                        const containerTop = mainContent.getBoundingClientRect().top;
                        const elementTop = targetSection.getBoundingClientRect().top;
                        mainContent.scrollTo({
                            top: elementTop - containerTop + mainContent.scrollTop - 80,
                            behavior: 'smooth'
                        });
                    }
                }, 100);
            }
        });
    </script>
</body>
</html>
