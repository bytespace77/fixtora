<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Helpdesk Pro - Ticket Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "surface-dim": "#d9dadb",
                    "tertiary": "#522900",
                    "tertiary-container": "#733c00",
                    "on-error-container": "#93000a",
                    "on-surface": "#191c1d",
                    "outline-variant": "#c3c6d1",
                    "surface-container": "#edeeef",
                    "inverse-primary": "#a6c8ff",
                    "on-secondary-fixed-variant": "#2e4966",
                    "primary-container": "#1a4b84",
                    "on-primary-fixed": "#001c3b",
                    "tertiary-fixed": "#ffdcc3",
                    "inverse-on-surface": "#f0f1f2",
                    "on-surface-variant": "#424750",
                    "on-primary-fixed-variant": "#144780",
                    "tertiary-fixed-dim": "#ffb77d",
                    "surface-variant": "#e1e3e4",
                    "primary-fixed-dim": "#a6c8ff",
                    "on-tertiary-fixed-variant": "#6e3900",
                    "surface-container-low": "#f3f4f5",
                    "surface-tint": "#335f99",
                    "inverse-surface": "#2e3132",
                    "on-secondary-fixed": "#001d36",
                    "on-secondary": "#ffffff",
                    "on-primary-container": "#93bcfc",
                    "surface-container-high": "#e7e8e9",
                    "on-secondary-container": "#455f7d",
                    "primary": "#003466",
                    "surface-container-highest": "#e1e3e4",
                    "surface-container-lowest": "#ffffff",
                    "error-container": "#ffdad6",
                    "background": "#f8f9fa",
                    "on-background": "#191c1d",
                    "surface": "#f8f9fa",
                    "primary-fixed": "#d5e3ff",
                    "secondary-fixed-dim": "#aec9ec",
                    "on-tertiary-fixed": "#2f1500",
                    "secondary-fixed": "#d1e4ff",
                    "on-tertiary-container": "#f7a967",
                    "on-primary": "#ffffff",
                    "on-error": "#ffffff",
                    "outline": "#737781",
                    "surface-bright": "#f8f9fa",
                    "secondary": "#46607f",
                    "secondary-container": "#bfd9fd",
                    "on-tertiary": "#ffffff",
                    "error": "#ba1a1a"
                },
                fontFamily: {
                    "headline": ["Montserrat", "sans-serif"],
                    "body": ["Montserrat", "sans-serif"],
                    "label": ["Montserrat", "sans-serif"]
                },
                borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
        },
    }
</script>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .premium-gradient { background: linear-gradient(135deg, #003466 0%, #1a4b84 100%); }
    .custom-shadow { box-shadow: 0 24px 24px -4px rgba(25,28,29,.06); }
    body { font-family: 'Montserrat', sans-serif; }
</style>
</head>
<body class="bg-surface text-on-surface antialiased">

<!-- ── Sidebar ── -->
<aside class="h-screen w-64 fixed left-0 top-0 overflow-y-auto bg-slate-100 z-50">
    <div class="flex flex-col h-full py-6">

        <!-- Brand -->
        <div class="px-6 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl premium-gradient flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">architecture</span>
                </div>
                <div>
                    <h1 class="text-base font-bold tracking-tight text-blue-900">Helpdesk Pro</h1>
                    <p class="text-[9px] uppercase tracking-widest font-bold text-slate-500 leading-tight">Architectural<br>Concierge</p>
                </div>
            </div>
            <button class="w-full premium-gradient text-white py-3 px-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-lg active:scale-95 transition-all">
                <span class="material-symbols-outlined text-sm">add</span> New Ticket
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 space-y-1">
            <?php
            $navItems = [
                ['icon'=>'dashboard',          'label'=>'Dashboard',     'active'=>false],
                ['icon'=>'confirmation_number', 'label'=>'Tickets',      'active'=>true],
                ['icon'=>'assignment',          'label'=>'Tasks',        'active'=>false],
                ['icon'=>'speed',               'label'=>'SLA Monitor',  'active'=>false],
                ['icon'=>'notifications',       'label'=>'Notifications','active'=>false],
            ];
            foreach ($navItems as $n):
                $cls = $n['active']
                    ? 'flex items-center gap-3 px-4 py-3 text-blue-900 font-bold border-l-4 border-blue-800 bg-white rounded-r-xl'
                    : 'flex items-center gap-3 px-4 py-3 text-slate-600 font-medium hover:bg-slate-200 transition-colors duration-200 rounded-xl';
            ?>
            <a class="<?= $cls ?>" href="#">
                <span class="material-symbols-outlined"><?= $n['icon'] ?></span>
                <?= $n['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Bottom -->
        <div class="px-3 mt-auto space-y-1 border-t border-slate-200 pt-5">
            <a class="flex items-center gap-3 px-4 py-3 text-slate-600 font-medium hover:bg-slate-200 transition-colors rounded-xl" href="#">
                <span class="material-symbols-outlined">settings</span> Settings
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-slate-600 font-medium hover:bg-slate-200 transition-colors rounded-xl" href="#">
                <span class="material-symbols-outlined">help</span> Help
            </a>
            <div class="mt-5 px-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full premium-gradient flex items-center justify-center text-white text-xs font-bold shrink-0">AR</div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-900 truncate">Alex Rivera</p>
                    <p class="text-[10px] text-slate-500 uppercase">Senior Architect</p>
                </div>
            </div>
        </div>

    </div>
</aside>

<!-- ── Main ── -->
<main class="ml-64 min-h-screen">

    <!-- Top Nav -->
    <header class="fixed top-0 right-0 left-64 h-16 z-40 bg-slate-50/80 backdrop-blur-xl flex items-center justify-between px-8 shadow-sm">
        <div class="flex items-center flex-1 max-w-xl">
            <div class="relative w-full group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                <input class="w-full bg-white border border-outline-variant/20 rounded-xl py-2 pl-10 pr-4 focus:ring-2 focus:ring-primary-fixed focus:border-primary outline-none text-sm transition-all font-medium" placeholder="Search tickets, agents, or assets..." type="text"/>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <button class="p-2 text-slate-500 hover:text-blue-900 transition-colors relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full"></span>
            </button>
            <button class="p-2 text-slate-500 hover:text-blue-900 transition-colors">
                <span class="material-symbols-outlined">settings</span>
            </button>
            <div class="h-8 w-px bg-slate-200 mx-1"></div>
            <div class="w-8 h-8 rounded-lg premium-gradient flex items-center justify-center text-white text-xs font-bold ring-2 ring-white shadow-sm">AR</div>
        </div>
    </header>

    <!-- Content -->
    <div class="pt-24 px-10 pb-12">

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">
                    <span>Management</span>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="text-primary">Ticket Registry</span>
                </div>
                <h2 class="text-4xl font-extrabold text-blue-900 tracking-tight">Active Tickets</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex bg-surface-container rounded-xl p-1">
                    <button class="px-4 py-2 rounded-lg text-xs font-bold bg-white text-primary shadow-sm">List View</button>
                    <button class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">Analytics</button>
                </div>
                <button class="flex items-center gap-2 px-5 py-2.5 bg-white border border-outline-variant/30 rounded-xl text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-sm">tune</span> Filter View
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-12 gap-6 mb-12">
            <!-- Response Velocity -->
            <div class="col-span-12 lg:col-span-4 bg-primary-container/10 rounded-2xl p-6 border-l-4 border-primary">
                <p class="text-xs font-bold text-primary uppercase tracking-wider mb-1">Response Velocity</p>
                <h3 class="text-3xl font-extrabold text-primary-container mb-4">1.2h Average</h3>
                <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-primary h-full w-3/4 rounded-full"></div>
                </div>
                <p class="text-[11px] text-slate-500 mt-3 font-medium">12% faster than last week's benchmark.</p>
            </div>
            <!-- Critical Blockers -->
            <div class="col-span-6 lg:col-span-4 bg-white custom-shadow rounded-2xl p-6 flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div class="p-2 bg-error-container/20 rounded-lg">
                        <span class="material-symbols-outlined text-error">priority_high</span>
                    </div>
                    <span class="text-[10px] font-bold text-error uppercase">Action Required</span>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold text-slate-900">08</h4>
                    <p class="text-xs font-medium text-slate-500 uppercase">Critical Blockers</p>
                </div>
            </div>
            <!-- Tickets Resolved -->
            <div class="col-span-6 lg:col-span-4 bg-white custom-shadow rounded-2xl p-6 flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div class="p-2 bg-secondary-container/20 rounded-lg">
                        <span class="material-symbols-outlined text-secondary">check_circle</span>
                    </div>
                    <span class="text-[10px] font-bold text-secondary uppercase">Last 24h</span>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold text-slate-900">142</h4>
                    <p class="text-xs font-medium text-slate-500 uppercase">Tickets Resolved</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-8 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Status:</span>
                <div class="flex gap-2">
                    <?php
                    $statuses = ['All Issues'=>true,'Open'=>false,'In Progress'=>false,'Resolved'=>false];
                    foreach ($statuses as $s => $active):
                        $cls = $active
                            ? 'px-3 py-1.5 rounded-full text-[11px] font-bold bg-primary text-white'
                            : 'px-3 py-1.5 rounded-full text-[11px] font-bold bg-white text-slate-600 border border-slate-100 hover:border-slate-300 transition-colors';
                    ?>
                    <button class="<?= $cls ?>"><?= $s ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="h-4 w-px bg-slate-300 mx-1"></div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Priority:</span>
                <select class="bg-transparent border-none text-[11px] font-bold text-slate-700 focus:ring-0 p-0 pr-6 cursor-pointer">
                    <option>All Priorities</option>
                    <option>Urgent</option>
                    <option>High</option>
                    <option>Normal</option>
                </select>
            </div>
        </div>

        <!-- Ticket List -->
        <?php
        $tickets = [
            [
                'id'         => '#7822',
                'title'      => 'Main API Gateway: 502 Bad Gateway across production-04',
                'reporter'   => 'James D.',
                'time'       => '8 mins ago',
                'system'     => 'Cloud-Infra-US',
                'system_icon'=> 'dns',
                'priority'   => 'Urgent',
                'priority_cls'=> 'bg-error-container text-on-error-container',
                'status_badge'=> null,
                'border'     => 'border-error',
                'resolved'   => false,
                'right_label'=> 'Unassigned',
                'right_icon' => 'hourglass_empty',
                'right_cls'  => 'text-primary',
                'right_sub'  => 'SLA Breach in 52m',
                'action_icon'=> 'chevron_right',
                'action_cls' => 'hover:bg-primary-fixed hover:text-primary',
                'row_cls'    => '',
            ],
            [
                'id'         => '#7819',
                'title'      => 'SSO Integration: Auth token leak via client-side caching',
                'reporter'   => 'Sarah W.',
                'time'       => '2 hours ago',
                'system'     => 'Auth-Core',
                'system_icon'=> 'security',
                'priority'   => 'High Priority',
                'priority_cls'=> 'bg-primary-container text-on-primary-container',
                'status_badge'=> ['label'=>'In Progress','cls'=>'bg-secondary-container/30 text-secondary'],
                'border'     => 'border-primary',
                'resolved'   => false,
                'right_label'=> 'Alex Rivera',
                'right_icon' => null,
                'right_cls'  => 'text-slate-900',
                'right_sub'  => 'Reviewing Logs...',
                'action_icon'=> 'chevron_right',
                'action_cls' => 'hover:bg-primary-fixed hover:text-primary',
                'row_cls'    => '',
            ],
            [
                'id'         => '#7815',
                'title'      => 'UI Bug: Dashboard navigation overlaps on small mobile viewports',
                'reporter'   => 'Marcus K.',
                'time'       => 'Closed 4h ago',
                'time_icon'  => 'task_alt',
                'system'     => 'Frontend-App',
                'system_icon'=> 'web',
                'priority'   => 'Normal',
                'priority_cls'=> 'bg-slate-100 text-slate-400',
                'status_badge'=> ['label'=>'Resolved','cls'=>'bg-emerald-100 text-emerald-700'],
                'border'     => 'border-slate-300',
                'resolved'   => true,
                'right_label'=> 'Archived',
                'right_icon' => null,
                'right_cls'  => 'text-slate-400',
                'right_sub'  => null,
                'action_icon'=> 'history',
                'action_cls' => 'hover:bg-slate-200 hover:text-slate-600',
                'row_cls'    => 'opacity-80 hover:opacity-100 grayscale-[0.4] hover:grayscale-0',
            ],
            [
                'id'         => '#7825',
                'title'      => 'Database Latency: PostgreSQL spikes during report generation',
                'reporter'   => 'Tom L.',
                'time'       => '1 hour ago',
                'system'     => 'DB-Cluster-Main',
                'system_icon'=> 'storage',
                'priority'   => 'High',
                'priority_cls'=> 'bg-primary-container/10 text-primary',
                'status_badge'=> ['label'=>'Triaging','cls'=>'bg-slate-100 text-slate-600'],
                'border'     => 'border-primary/40',
                'resolved'   => false,
                'right_label'=> 'Claim Ticket',
                'right_icon' => 'person_add',
                'right_cls'  => 'text-primary',
                'right_sub'  => 'First Response Target: 30m',
                'action_icon'=> 'chevron_right',
                'action_cls' => 'hover:bg-primary-fixed hover:text-primary',
                'row_cls'    => '',
            ],
        ];
        ?>

        <div class="space-y-4">
            <?php foreach ($tickets as $t): ?>
            <div class="group bg-white rounded-2xl transition-all duration-300 hover:shadow-xl border-l-4 <?= $t['border'] ?> relative overflow-hidden <?= $t['row_cls'] ?>">
                <div class="p-6 flex items-center gap-6">

                    <!-- ID -->
                    <div class="hidden md:flex flex-col items-center justify-center min-w-[60px] <?= $t['resolved'] ? 'text-slate-300' : 'text-slate-400' ?>">
                        <span class="text-[9px] font-black uppercase tracking-tighter">ID</span>
                        <span class="text-sm font-bold <?= $t['resolved'] ? '' : 'text-slate-900' ?>"><?= $t['id'] ?></span>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2.5 mb-1.5">
                            <h3 class="text-base font-bold <?= $t['resolved'] ? 'text-slate-500 line-through' : 'text-slate-900 group-hover:text-primary' ?> transition-colors cursor-pointer leading-snug">
                                <?= $t['title'] ?>
                            </h3>
                            <span class="px-2.5 py-1 rounded-md <?= $t['priority_cls'] ?> text-[10px] font-black uppercase tracking-wider whitespace-nowrap">
                                <?= $t['priority'] ?>
                            </span>
                            <?php if ($t['status_badge']): ?>
                            <span class="px-2.5 py-1 rounded-md <?= $t['status_badge']['cls'] ?> text-[10px] font-black uppercase tracking-wider whitespace-nowrap">
                                <?= $t['status_badge']['label'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-xs font-medium <?= $t['resolved'] ? 'text-slate-400' : 'text-slate-500' ?>">
                            <!-- Reporter -->
                            <div class="flex items-center gap-1">
                                <div class="w-4 h-4 rounded-full premium-gradient flex items-center justify-center text-white text-[7px] font-bold <?= $t['resolved'] ? 'opacity-50' : '' ?>">
                                    <?= strtoupper(substr($t['reporter'],0,1)) ?>
                                </div>
                                <span><?= $t['reporter'] ?></span>
                            </div>
                            <span>•</span>
                            <!-- Time -->
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm"><?= isset($t['time_icon']) ? $t['time_icon'] : 'schedule' ?></span>
                                <span><?= $t['time'] ?></span>
                            </div>
                            <span>•</span>
                            <!-- System -->
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm"><?= $t['system_icon'] ?></span>
                                <span><?= $t['system'] ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Action -->
                    <div class="flex items-center gap-5 shrink-0">
                        <div class="text-right">
                            <div class="flex items-center justify-end gap-1.5 <?= $t['right_cls'] ?> font-bold">
                                <?php if ($t['right_icon']): ?>
                                <span class="material-symbols-outlined text-sm"><?= $t['right_icon'] ?></span>
                                <?php endif; ?>
                                <span class="text-[11px] uppercase tracking-wider"><?= $t['right_label'] ?></span>
                            </div>
                            <?php if ($t['right_sub']): ?>
                            <div class="text-[10px] text-slate-400 mt-1"><?= $t['right_sub'] ?></div>
                            <?php endif; ?>
                        </div>
                        <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center transition-all <?= $t['action_cls'] ?>">
                            <span class="material-symbols-outlined"><?= $t['action_icon'] ?></span>
                        </button>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Load More -->
        <div class="mt-10 flex flex-col items-center gap-3">
            <button class="flex items-center gap-2 px-8 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 hover:shadow-md transition-all">
                <span class="material-symbols-outlined text-sm">expand_more</span> Load More Tickets
            </button>
            <p class="text-xs text-slate-400 font-medium">Showing 4 of 42 active tickets in current view.</p>
        </div>

    </div><!-- /content -->
</main>

</body>
</html>