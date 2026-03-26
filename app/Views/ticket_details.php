<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Ticket Details - Architectural Concierge</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "tertiary-fixed-dim": "#ffb77d",
                    "on-primary-container": "#93bcfc",
                    "inverse-surface": "#2e3132",
                    "surface-dim": "#d9dadb",
                    "on-secondary-fixed": "#001d36",
                    "inverse-primary": "#a6c8ff",
                    "on-surface-variant": "#424750",
                    "surface-container-high": "#e7e8e9",
                    "surface-container": "#edeeef",
                    "on-tertiary-fixed-variant": "#6e3900",
                    "primary-fixed": "#d5e3ff",
                    "surface-bright": "#f8f9fa",
                    "outline": "#737781",
                    "primary-container": "#1a4b84",
                    "on-background": "#191c1d",
                    "surface-container-highest": "#e1e3e4",
                    "on-error-container": "#93000a",
                    "on-tertiary-fixed": "#2f1500",
                    "on-tertiary-container": "#f7a967",
                    "surface": "#f8f9fa",
                    "background": "#f8f9fa",
                    "surface-container-lowest": "#ffffff",
                    "primary": "#003466",
                    "on-secondary-container": "#455f7d",
                    "surface-variant": "#e1e3e4",
                    "on-secondary": "#ffffff",
                    "error-container": "#ffdad6",
                    "tertiary": "#522900",
                    "secondary": "#46607f",
                    "surface-tint": "#335f99",
                    "inverse-on-surface": "#f0f1f2",
                    "secondary-fixed": "#d1e4ff",
                    "on-primary-fixed": "#001c3b",
                    "on-secondary-fixed-variant": "#2e4966",
                    "tertiary-fixed": "#ffdcc3",
                    "tertiary-container": "#733c00",
                    "on-tertiary": "#ffffff",
                    "on-primary-fixed-variant": "#144780",
                    "error": "#ba1a1a",
                    "surface-container-low": "#f3f4f5",
                    "secondary-fixed-dim": "#aec9ec",
                    "primary-fixed-dim": "#a6c8ff",
                    "outline-variant": "#c3c6d1",
                    "on-surface": "#191c1d",
                    "secondary-container": "#bfd9fd",
                    "on-primary": "#ffffff",
                    "on-error": "#ffffff"
                },
                fontFamily: {
                    "montserrat": ["Montserrat", "sans-serif"],
                    "headline": ["Manrope", "sans-serif"],
                    "body": ["Manrope", "sans-serif"],
                    "label": ["Manrope", "sans-serif"]
                },
                borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
            },
        },
    }
</script>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { font-family: 'Manrope', sans-serif; }
    .font-montserrat { font-family: 'Montserrat', sans-serif; }
</style>
</head>
<body class="bg-surface text-on-background min-h-screen">

<!-- Top Nav -->
<header class="bg-slate-50 dark:bg-slate-900 top-0 z-50 flex justify-between items-center w-full px-8 h-16 fixed border-b border-slate-200">
    <div class="flex items-center gap-8">
        <span class="text-xl font-bold tracking-tighter text-blue-900 font-montserrat">Architectural Concierge</span>
        <nav class="hidden md:flex items-center gap-6 font-montserrat text-sm font-medium tracking-tight">
            <a class="text-slate-500 hover:text-blue-700 transition-colors" href="#">Dashboard</a>
            <a class="text-blue-900 border-b-2 border-blue-900 pb-1" href="#">Tickets</a>
            <a class="text-slate-500 hover:text-blue-700 transition-colors" href="#">Customers</a>
            <a class="text-slate-500 hover:text-blue-700 transition-colors" href="#">Reports</a>
        </nav>
    </div>
    <div class="flex items-center gap-4">
        <button class="p-2 text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">notifications</span>
        </button>
        <button class="p-2 text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">settings</span>
        </button>
        <div class="w-8 h-8 rounded-full bg-blue-900 flex items-center justify-center text-white text-xs font-bold">JD</div>
    </div>
</header>

<!-- Sidebar -->
<aside class="bg-slate-100 h-screen w-56 fixed left-0 top-0 pt-16 flex flex-col border-r border-slate-200 hidden md:flex">
    <div class="p-5">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-on-primary-container">
                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">support_agent</span>
            </div>
            <div>
                <p class="font-montserrat text-[9px] font-semibold uppercase tracking-widest text-slate-500">Agent Portal</p>
                <p class="text-xs font-bold text-blue-900">High Priority Queue</p>
            </div>
        </div>
        <button class="w-full py-2.5 px-4 bg-primary text-on-primary rounded-xl font-bold flex items-center justify-center gap-2 text-xs shadow hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            New Ticket
        </button>
    </div>
    <nav class="flex-1 font-montserrat text-[10px] font-semibold uppercase tracking-widest">
        <a class="flex items-center gap-3 text-slate-500 px-5 py-3.5 hover:bg-slate-200 transition-all" href="#">
            <span class="material-symbols-outlined text-base">dashboard</span> Overview
        </a>
        <a class="flex items-center gap-3 text-blue-900 border-l-4 border-blue-900 bg-white px-5 py-3.5 transition-all" href="#">
            <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">confirmation_number</span> My Tickets
        </a>
        <a class="flex items-center gap-3 text-slate-500 px-5 py-3.5 hover:bg-slate-200 transition-all" href="#">
            <span class="material-symbols-outlined text-base">group_add</span> Unassigned
        </a>
        <a class="flex items-center gap-3 text-slate-500 px-5 py-3.5 hover:bg-slate-200 transition-all" href="#">
            <span class="material-symbols-outlined text-base">lan</span> Teams
        </a>
        <a class="flex items-center gap-3 text-slate-500 px-5 py-3.5 hover:bg-slate-200 transition-all" href="#">
            <span class="material-symbols-outlined text-base">inventory_2</span> Archive
        </a>
    </nav>
    <div class="mt-auto p-4 border-t border-slate-200 font-montserrat text-[10px] font-semibold uppercase tracking-widest">
        <a class="flex items-center gap-3 text-slate-500 px-3 py-2.5 hover:bg-slate-200 rounded-lg transition-all" href="#">
            <span class="material-symbols-outlined text-base">help</span> Help Center
        </a>
        <a class="flex items-center gap-3 text-error px-3 py-2.5 hover:bg-red-50 rounded-lg transition-all" href="#">
            <span class="material-symbols-outlined text-base">logout</span> Logout
        </a>
    </div>
</aside>

<!-- Main Content -->
<main class="md:pl-56 pt-16 min-h-screen bg-surface">
    <div class="max-w-6xl mx-auto p-6">

        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center justify-between bg-surface-container-low px-5 py-2.5 rounded-xl">
            <div class="flex items-center gap-3 text-sm font-medium">
                <span class="text-slate-400 cursor-pointer hover:text-primary transition-colors">Tickets</span>
                <span class="material-symbols-outlined text-slate-300 text-sm">chevron_right</span>
                <span class="text-primary font-bold">#TK-7822</span>
            </div>
            <div class="text-slate-400 text-xs bg-surface-container-high px-2 py-1 rounded">CMD + K to search</div>
        </div>

        <!-- Ticket Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="text-xs font-bold text-slate-400 tracking-widest uppercase">#TK-7822</span>
                        <span class="px-2.5 py-0.5 bg-secondary-container/50 text-on-secondary-container text-[10px] font-bold uppercase tracking-wider rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> In Progress
                        </span>
                        <span class="px-2.5 py-0.5 bg-error-container text-on-error-container text-[10px] font-bold uppercase tracking-wider rounded-full">
                            Critical
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold font-montserrat text-blue-900 tracking-tight leading-tight">
                        Main API Gateway: 502 Bad Gateway
                    </h1>
                </div>
                <div class="flex gap-3 shrink-0">
                    <button class="px-4 py-2 bg-white text-primary font-bold rounded-xl text-sm border border-slate-200 hover:bg-slate-50 transition-colors">
                        Share Ticket
                    </button>
                    <button class="px-4 py-2 bg-primary text-on-primary font-bold rounded-xl text-sm shadow hover:opacity-90 transition-opacity">
                        Update Status
                    </button>
                </div>
            </div>
        </div>

        <!-- Two-column layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Column -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Description Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-primary">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-4">Detailed Description</h3>
                    <div class="text-sm text-slate-700 leading-relaxed space-y-3">
                        <p>Intermittent 502 Bad Gateway errors reported across all endpoints for the production API gateway in the us-east-1 region. Traffic spikes coincide with increased latency in the downstream microservices cluster.</p>
                        <p>Root cause analysis currently focused on the load balancer configuration and potential connection timeouts between the ingress and the service mesh.</p>
                    </div>
                    <!-- System Info -->
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block mb-1">System</span>
                            <span class="text-sm font-bold text-primary flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">cloud</span> Cloud-Infra-US
                            </span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block mb-1">Category</span>
                            <span class="text-sm font-bold text-primary flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">terminal</span> Backend
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="bg-slate-50 p-6 rounded-2xl">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-6">Activity Timeline</h3>
                    <div class="space-y-6 relative">
                        <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-slate-200"></div>

                        <?php
                        $timeline = [
                            ['icon' => 'bolt', 'active' => true,  'label' => 'Investigation started', 'time' => 'Today, 10:45 AM', 'by' => 'James D.'],
                            ['icon' => 'analytics', 'active' => false, 'label' => 'Logs analyzed',         'time' => 'Today, 09:30 AM', 'by' => 'System Monitor'],
                            ['icon' => 'person_add', 'active' => false, 'label' => 'Assigned to James D.',  'time' => 'Today, 09:15 AM', 'by' => 'Admin'],
                            ['icon' => 'fiber_new',  'active' => false, 'label' => 'Ticket Created',        'time' => 'Today, 09:10 AM', 'by' => 'Auto-Injest'],
                        ];
                        foreach ($timeline as $item):
                            $bg  = $item['active'] ? 'bg-primary' : 'bg-slate-300';
                            $txt = $item['active'] ? 'text-blue-900 font-bold' : 'text-slate-600 font-semibold';
                        ?>
                        <div class="relative flex gap-5 items-start">
                            <div class="w-6 h-6 rounded-full <?= $bg ?> flex items-center justify-center z-10 ring-4 ring-slate-50 shrink-0">
                                <span class="material-symbols-outlined text-white text-[11px]" style="font-variation-settings: 'FILL' 1;"><?= $item['icon'] ?></span>
                            </div>
                            <div>
                                <p class="text-sm <?= $txt ?>"><?= $item['label'] ?></p>
                                <p class="text-xs text-slate-400 mt-0.5"><?= $item['time'] ?> • <?= $item['by'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Collaboration Hub -->
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-5">Collaboration Hub</h3>

                    <!-- Comments -->
                    <div class="space-y-5 mb-6">
                        <?php
                        $comments = [
                            [
                                'name'    => 'James D.',
                                'initials'=> 'JD',
                                'time'    => '10 mins ago',
                                'text'    => 'Checking the ELB metrics now. It seems the target group health checks are failing intermittently. Escalating to DevOps if this persists.',
                                'color'   => 'bg-blue-900',
                            ],
                        ];
                        foreach ($comments as $c):
                        ?>
                        <div class="flex gap-4">
                            <div class="w-9 h-9 rounded-xl <?= $c['color'] ?> flex items-center justify-center text-white text-xs font-bold shrink-0"><?= $c['initials'] ?></div>
                            <div class="flex-1 bg-slate-50 p-4 rounded-2xl rounded-tl-none">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-sm font-bold text-blue-900"><?= $c['name'] ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase"><?= $c['time'] ?></span>
                                </div>
                                <p class="text-sm text-slate-600"><?= $c['text'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Comment Input -->
                    <div class="relative">
                        <textarea class="w-full h-28 bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none" placeholder="Type your update here..."></textarea>
                        <div class="absolute bottom-3 right-3 flex items-center gap-2">
                            <button class="p-1.5 text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-xl">attach_file</span>
                            </button>
                            <button class="bg-primary text-on-primary px-4 py-2 rounded-xl text-xs font-bold hover:opacity-90 transition-opacity">
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Stakeholders -->
                <div class="bg-white p-5 rounded-2xl shadow-sm">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-5">Stakeholders</h3>
                    <div class="space-y-4">
                        <?php
                        $stakeholders = [
                            ['role' => 'Assignee', 'name' => 'James Davidson', 'initials' => 'JD', 'color' => 'bg-blue-900'],
                            ['role' => 'Reporter', 'name' => 'System Monitor',  'initials' => 'SM', 'color' => 'bg-secondary-container'],
                        ];
                        foreach ($stakeholders as $s):
                        ?>
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl <?= $s['color'] ?> flex items-center justify-center text-white text-xs font-bold"><?= $s['initials'] ?></div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide"><?= $s['role'] ?></p>
                                    <p class="text-sm font-bold text-primary"><?= $s['name'] ?></p>
                                </div>
                            </div>
                            <button class="opacity-0 group-hover:opacity-100 p-1.5 hover:bg-slate-100 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-slate-400 text-sm">edit</span>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SLA Status -->
                <div class="bg-white p-5 rounded-2xl shadow-sm">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-5">SLA Status</h3>
                    <div class="space-y-5">
                        <?php
                        $sla = [
                            ['label' => 'Time to Respond', 'value' => 'Completed', 'color' => 'text-primary', 'bar_color' => 'bg-primary', 'width' => 'w-full'],
                            ['label' => 'Time to Resolve', 'value' => '2h 45m left', 'color' => 'text-error', 'bar_color' => 'bg-error/70', 'width' => 'w-[65%]'],
                        ];
                        foreach ($sla as $s):
                        ?>
                        <div>
                            <div class="flex justify-between text-[10px] font-bold uppercase mb-2">
                                <span class="text-slate-500"><?= $s['label'] ?></span>
                                <span class="<?= $s['color'] ?>"><?= $s['value'] ?></span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full <?= $s['bar_color'] ?> <?= $s['width'] ?> rounded-full"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="bg-white p-5 rounded-2xl shadow-sm">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-4">Attachments</h3>
                    <div class="space-y-2">
                        <?php
                        $attachments = [
                            ['icon' => 'description', 'name' => 'server_logs.txt'],
                            ['icon' => 'image',       'name' => 'error_screenshot.png'],
                        ];
                        foreach ($attachments as $a):
                        ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl group cursor-pointer hover:bg-slate-100 transition-all">
                            <div class="flex items-center gap-2.5 overflow-hidden">
                                <span class="material-symbols-outlined text-primary text-lg"><?= $a['icon'] ?></span>
                                <span class="text-sm font-bold text-primary truncate"><?= $a['name'] ?></span>
                            </div>
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors text-lg">download</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="w-full mt-3 py-3 border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-xs font-bold hover:border-primary/40 hover:text-primary transition-all">
                        Drop files here to upload
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 gap-3">
                    <button class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-sm border border-transparent hover:border-primary/20 transition-all gap-2">
                        <span class="material-symbols-outlined text-slate-500">person_add</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Assign</span>
                    </button>
                    <button class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-sm border border-transparent hover:border-error/20 transition-all gap-2 group">
                        <span class="material-symbols-outlined text-slate-500 group-hover:text-error transition-colors">task_alt</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 group-hover:text-error">Close</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</main>

</body>
</html>