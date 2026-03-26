<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Ticket Scheduling - Concierge Desk</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "secondary-container": "#bfd9fd",
                    "on-surface": "#191c1d",
                    "primary-container": "#1a4b84",
                    "error": "#ba1a1a",
                    "surface-container-low": "#f3f4f5",
                    "on-tertiary-container": "#f7a967",
                    "surface-container-lowest": "#ffffff",
                    "background": "#f8f9fa",
                    "primary-fixed": "#d5e3ff",
                    "surface-container-high": "#e7e8e9",
                    "on-background": "#191c1d",
                    "on-secondary": "#ffffff",
                    "on-secondary-container": "#455f7d",
                    "primary": "#003466",
                    "inverse-primary": "#a6c8ff",
                    "inverse-on-surface": "#f0f1f2",
                    "on-tertiary-fixed-variant": "#6e3900",
                    "primary-fixed-dim": "#a6c8ff",
                    "on-primary-fixed": "#001c3b",
                    "on-error": "#ffffff",
                    "on-tertiary-fixed": "#2f1500",
                    "surface-tint": "#335f99",
                    "tertiary-container": "#733c00",
                    "surface-dim": "#d9dadb",
                    "tertiary": "#522900",
                    "outline-variant": "#c3c6d1",
                    "on-surface-variant": "#424750",
                    "surface-variant": "#e1e3e4",
                    "on-tertiary": "#ffffff",
                    "on-primary-container": "#93bcfc",
                    "tertiary-fixed": "#ffdcc3",
                    "surface-container": "#edeeef",
                    "surface": "#f8f9fa",
                    "tertiary-fixed-dim": "#ffb77d",
                    "outline": "#737781",
                    "on-error-container": "#93000a",
                    "on-secondary-fixed-variant": "#2e4966",
                    "error-container": "#ffdad6",
                    "secondary-fixed": "#d1e4ff",
                    "on-primary": "#ffffff",
                    "secondary-fixed-dim": "#aec9ec",
                    "surface-container-highest": "#e1e3e4",
                    "secondary": "#46607f",
                    "surface-bright": "#f8f9fa",
                    "on-secondary-fixed": "#001d36",
                    "on-primary-fixed-variant": "#144780",
                    "inverse-surface": "#2e3132"
                },
                fontFamily: {
                    "headline": ["Montserrat", "sans-serif"],
                    "body": ["Montserrat", "sans-serif"],
                    "label": ["Montserrat", "sans-serif"],
                    "montserrat": ["Montserrat", "sans-serif"]
                },
                borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
        },
    }
</script>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        display: inline-block;
        line-height: 1;
    }
    .primary-gradient { background: linear-gradient(135deg, #003466 0%, #1a4b84 100%); }
    body { font-family: 'Montserrat', sans-serif; }
</style>
</head>
<body class="bg-surface text-on-surface">

<!-- Sidebar -->
<aside class="h-screen w-56 fixed left-0 top-0 bg-slate-100 flex flex-col py-8 px-4 gap-y-2 z-50">
    <div class="mb-8 px-4">
        <h1 class="text-base font-black text-blue-900 leading-none">Concierge Desk</h1>
        <p class="text-[9px] font-semibold uppercase tracking-widest text-slate-500 mt-1">Operational Lead</p>
    </div>
    <nav class="space-y-0.5">
        <?php
        $navItems = [
            ['icon' => 'dashboard',           'label' => 'Dashboard', 'active' => false],
            ['icon' => 'confirmation_number',  'label' => 'Tickets',   'active' => false],
            ['icon' => 'calendar_today',       'label' => 'Schedule',  'active' => true],
            ['icon' => 'assignment',           'label' => 'Tasks',     'active' => false],
            ['icon' => 'assessment',           'label' => 'Reports',   'active' => false],
            ['icon' => 'settings',             'label' => 'Settings',  'active' => false],
        ];
        foreach ($navItems as $item):
            $cls = $item['active']
                ? 'flex items-center gap-3 px-4 py-3 text-blue-900 border-l-4 border-blue-900 bg-white/50 text-xs font-semibold uppercase tracking-widest'
                : 'flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-blue-700 transition-all text-xs font-semibold uppercase tracking-widest';
        ?>
        <a class="<?= $cls ?>" href="#">
            <span class="material-symbols-outlined text-base"><?= $item['icon'] ?></span>
            <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="mt-auto px-4 pt-8">
        <button class="w-full primary-gradient text-white py-3 px-4 rounded-xl font-bold flex items-center justify-center gap-2 text-sm shadow-lg hover:scale-[0.98] transition-transform">
            <span class="material-symbols-outlined">add</span> New Ticket
        </button>
    </div>
</aside>

<!-- Main -->
<main class="ml-56 min-h-screen flex flex-col">

    <!-- Top Nav -->
    <header class="w-full h-16 bg-slate-50 flex justify-between items-center px-6 sticky top-0 z-40 border-b border-slate-200">
        <div class="flex items-center gap-4 flex-1">
            <div class="relative w-full max-w-md group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-base">search</span>
                <input class="w-full bg-slate-100 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary-fixed-dim transition-all" placeholder="Search maintenance logs..." type="text"/>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex gap-3">
                <button class="p-2 text-slate-500 hover:bg-slate-200/50 rounded-full transition-colors relative">
                    <span class="material-symbols-outlined text-base">notifications</span>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-error rounded-full"></span>
                </button>
                <button class="p-2 text-slate-500 hover:bg-slate-200/50 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-base">help_outline</span>
                </button>
            </div>
            <div class="flex items-center gap-3 pl-5 border-l border-slate-200">
                <div class="text-right">
                    <p class="text-sm font-bold text-blue-900 leading-none">Alex Rivera</p>
                    <p class="text-[10px] text-slate-500 font-semibold tracking-tighter">ADMINISTRATOR</p>
                </div>
                <div class="w-9 h-9 rounded-full primary-gradient flex items-center justify-center text-white text-xs font-bold">AR</div>
            </div>
        </div>
    </header>

    <div class="p-6 space-y-6">

        <!-- Metrics Row -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <!-- Total Scheduled -->
            <div class="bg-white p-5 rounded-xl flex flex-col justify-between hover:bg-slate-50 transition-colors shadow-sm">
                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Total Scheduled</span>
                <div class="flex items-end justify-between mt-2">
                    <h2 class="text-3xl font-black text-primary leading-none">142</h2>
                    <div class="bg-primary-fixed px-2 py-1 rounded-lg text-[9px] font-bold text-primary flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">trending_up</span> +12%
                    </div>
                </div>
            </div>
            <!-- Active in Testing -->
            <div class="bg-white p-5 rounded-xl flex flex-col justify-between hover:bg-slate-50 transition-colors shadow-sm">
                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Active in Testing</span>
                <div class="flex items-end justify-between mt-2">
                    <h2 class="text-3xl font-black text-tertiary leading-none">28</h2>
                    <div class="bg-tertiary-fixed px-2 py-1 rounded-lg text-[9px] font-bold text-tertiary flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">sync</span> STABLE
                    </div>
                </div>
            </div>
            <!-- Overdue -->
            <div class="bg-white p-5 rounded-xl flex flex-col justify-between hover:bg-slate-50 transition-colors shadow-sm">
                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Overdue for Completion</span>
                <div class="flex items-end justify-between mt-2">
                    <h2 class="text-3xl font-black text-error leading-none">04</h2>
                    <div class="bg-error-container px-2 py-1 rounded-lg text-[9px] font-bold text-on-error-container flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">priority_high</span> ACTION
                    </div>
                </div>
            </div>
            <!-- Team Availability -->
            <div class="primary-gradient p-5 rounded-xl flex flex-col justify-center text-white relative overflow-hidden shadow-sm">
                <div class="relative z-10">
                    <p class="text-[9px] font-bold uppercase tracking-widest opacity-70">Team Availability</p>
                    <h2 class="text-2xl font-black mt-1">94%<br>Capacity</h2>
                </div>
                <div class="absolute -right-3 -bottom-3 opacity-20">
                    <span class="material-symbols-outlined text-7xl" style="font-variation-settings: 'FILL' 1;">groups</span>
                </div>
            </div>
        </section>

        <!-- Title + View Toggle -->
        <section class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-blue-900 leading-tight">Operational Schedule</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Monitoring fleet maintenance and ticket lifecycle</p>
            </div>
            <div class="flex items-center gap-2 bg-surface-container p-1 rounded-xl">
                <button class="bg-white px-4 py-2 rounded-lg text-xs font-bold text-primary shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">calendar_view_month</span> Calendar View
                </button>
                <button class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:bg-slate-200/50 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">view_kanban</span> List View
                </button>
            </div>
        </section>

        <!-- Calendar + Live Monitoring -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            <!-- Calendar -->
            <div class="xl:col-span-8 bg-white rounded-2xl overflow-hidden shadow-sm">
                <!-- Calendar Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h3 class="text-base font-bold">October 2023</h3>
                        <div class="flex gap-1">
                            <button class="p-1 hover:bg-slate-100 rounded-md transition-colors">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </button>
                            <button class="p-1 hover:bg-slate-100 rounded-md transition-colors">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <span class="text-[9px] font-bold uppercase text-slate-400">In Progress</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span class="text-[9px] font-bold uppercase text-slate-400">Testing</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[9px] font-bold uppercase text-slate-400">Scheduled</span>
                        </div>
                    </div>
                </div>

                <!-- Day Headers -->
                <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50">
                    <?php foreach (['MON','TUE','WED','THU','FRI','SAT','SUN'] as $d): ?>
                    <div class="p-3 text-center text-[9px] font-black text-slate-400 uppercase"><?= $d ?></div>
                    <?php endforeach; ?>
                </div>

                <!-- Calendar Grid -->
                <?php
                // Define the calendar: 5 rows × 7 cols
                // Each cell: [day, events[]]
                // event: ['label', 'color' => 'green|blue|primary|purple']
                $cal = [
                    // Row 1
                    ['day'=>'28','prev'=>true,'events'=>[]],
                    ['day'=>'29','prev'=>true,'events'=>[]],
                    ['day'=>'30','prev'=>true,'events'=>[]],
                    ['day'=>'1', 'prev'=>false,'events'=>[['label'=>'Server Patch A-12','color'=>'green']]],
                    ['day'=>'2', 'prev'=>false,'events'=>[]],
                    ['day'=>'3', 'prev'=>false,'events'=>[['label'=>'Database Migration','color'=>'blue']]],
                    ['day'=>'4', 'prev'=>false,'events'=>[]],
                    // Row 2
                    ['day'=>'5', 'prev'=>false,'events'=>[]],
                    ['day'=>'6', 'prev'=>false,'today'=>true,'events'=>[['label'=>'Main UI Refresh','color'=>'primary'],['label'=>'Beta Testing v2','color'=>'purple']]],
                    ['day'=>'7', 'prev'=>false,'events'=>[]],
                    ['day'=>'8', 'prev'=>false,'events'=>[]],
                    ['day'=>'9', 'prev'=>false,'events'=>[['label'=>'Weekly Sync','color'=>'green']]],
                    ['day'=>'10','prev'=>false,'events'=>[]],
                    ['day'=>'11','prev'=>false,'events'=>[]],
                    // Row 3
                    ['day'=>'12','prev'=>false,'events'=>[]],
                    ['day'=>'13','prev'=>false,'events'=>[]],
                    ['day'=>'14','prev'=>false,'events'=>[['label'=>'Stress Testing','color'=>'purple']]],
                    ['day'=>'15','prev'=>false,'events'=>[]],
                    ['day'=>'16','prev'=>false,'events'=>[]],
                    ['day'=>'17','prev'=>false,'events'=>[]],
                    ['day'=>'18','prev'=>false,'events'=>[]],
                    // Row 4
                    ['day'=>'19','prev'=>false,'events'=>[]],
                    ['day'=>'20','prev'=>false,'events'=>[]],
                    ['day'=>'21','prev'=>false,'events'=>[]],
                    ['day'=>'22','prev'=>false,'events'=>[]],
                    ['day'=>'23','prev'=>false,'events'=>[]],
                    ['day'=>'24','prev'=>false,'events'=>[]],
                    ['day'=>'25','prev'=>false,'events'=>[]],
                    // Row 5
                    ['day'=>'26','prev'=>false,'events'=>[]],
                    ['day'=>'27','prev'=>false,'events'=>[]],
                    ['day'=>'28','prev'=>false,'events'=>[]],
                    ['day'=>'29','prev'=>false,'events'=>[]],
                    ['day'=>'30','prev'=>false,'events'=>[]],
                    ['day'=>'31','prev'=>false,'events'=>[]],
                    ['day'=>'1', 'prev'=>true,'events'=>[]],
                ];

                $colorMap = [
                    'green'   => 'bg-emerald-100 text-emerald-800',
                    'blue'    => 'bg-blue-100 text-blue-800',
                    'primary' => 'bg-primary text-white shadow-md',
                    'purple'  => 'bg-purple-100 text-purple-800',
                ];
                ?>
                <div class="grid grid-cols-7 grid-rows-5" style="height:520px">
                    <?php foreach ($cal as $i => $cell):
                        $isLast   = ($i % 7 === 6);
                        $isLastRow= ($i >= 28);
                        $border   = 'border-r border-b border-slate-100';
                        if ($isLast)    $border = str_replace('border-r ', '', $border);
                        if ($isLastRow) $border = str_replace(' border-b', '', $border);
                        $bg = isset($cell['prev']) && $cell['prev'] ? 'bg-slate-50/60' : (isset($cell['today']) ? 'bg-blue-50/30' : '');
                        $dayColor = isset($cell['prev']) && $cell['prev'] ? 'text-slate-300' : (isset($cell['today']) ? 'text-primary font-extrabold' : 'text-slate-700 font-bold');
                    ?>
                    <div class="p-2 <?= $border ?> <?= $bg ?> overflow-hidden">
                        <span class="text-[11px] <?= $dayColor ?>"><?= $cell['day'] ?></span>
                        <?php if (!empty($cell['events'])): ?>
                        <div class="mt-1.5 space-y-1">
                            <?php foreach ($cell['events'] as $ev):
                                $cls = $colorMap[$ev['color']] ?? 'bg-slate-100 text-slate-700';
                            ?>
                            <div class="<?= $cls ?> px-1.5 py-1 rounded-lg text-[9px] font-bold leading-tight cursor-pointer hover:opacity-80 transition-opacity">
                                <?= $ev['label'] ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Live Monitoring -->
            <div class="xl:col-span-4">
                <div class="bg-surface-container-low rounded-2xl p-5 h-full border border-slate-200/50">
                    <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> Live Monitoring
                    </h3>
                    <div class="space-y-4">
                        <?php
                        $tickets = [
                            [
                                'status'      => 'In Progress',
                                'status_cls'  => 'bg-blue-50 text-blue-700',
                                'border'      => 'border-primary',
                                'timer'       => '02:45',
                                'timer_cls'   => 'text-error',
                                'title'       => 'Client API Gateway timeout resolution',
                                'avatars'     => ['JD'],
                                'extra'       => '+1',
                                'id'          => 'TKT-8842',
                            ],
                            [
                                'status'      => 'Testing',
                                'status_cls'  => 'bg-purple-50 text-purple-700',
                                'border'      => 'border-purple-500',
                                'timer'       => '14:12',
                                'timer_cls'   => 'text-slate-400',
                                'title'       => 'SSL Certificate renewal automation script',
                                'avatars'     => ['SJ'],
                                'extra'       => '',
                                'id'          => 'TKT-8910',
                            ],
                            [
                                'status'      => 'In Progress',
                                'status_cls'  => 'bg-blue-50 text-blue-700',
                                'border'      => 'border-primary',
                                'timer'       => '48:30',
                                'timer_cls'   => 'text-emerald-500',
                                'title'       => 'Hardware firewall replacement - Floor 4',
                                'avatars'     => ['ML'],
                                'extra'       => '',
                                'id'          => 'TKT-8955',
                            ],
                        ];
                        foreach ($tickets as $t):
                        ?>
                        <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 <?= $t['border'] ?> group hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                            <div class="flex justify-between items-start mb-2">
                                <span class="<?= $t['status_cls'] ?> px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-tight"><?= $t['status'] ?></span>
                                <div class="flex items-center <?= $t['timer_cls'] ?> font-bold text-[10px] gap-0.5">
                                    <span class="material-symbols-outlined text-xs">timer</span> <?= $t['timer'] ?>
                                </div>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 leading-snug group-hover:text-primary transition-colors"><?= $t['title'] ?></h4>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex -space-x-1.5">
                                    <?php foreach ($t['avatars'] as $av): ?>
                                    <div class="w-6 h-6 rounded-full primary-gradient flex items-center justify-center text-white text-[8px] font-bold border-2 border-white"><?= $av ?></div>
                                    <?php endforeach; ?>
                                    <?php if ($t['extra']): ?>
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center border-2 border-white text-[8px] font-bold text-slate-500"><?= $t['extra'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400"><?= $t['id'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <button class="w-full py-3 rounded-xl border-2 border-dashed border-slate-300 text-slate-400 text-xs font-bold hover:bg-slate-50 transition-colors">
                            View All Active Tickets
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Shift Roster -->
        <section class="bg-white rounded-2xl p-7 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-7">
                <div>
                    <h3 class="text-base font-black text-blue-900">Current Shift Roster</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Monitoring engineer availability for urgent dispatch</p>
                </div>
                <button class="text-primary text-xs font-bold flex items-center gap-1 hover:underline">
                    Manage Schedules <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <?php
                $roster = [
                    ['name'=>'David Chen',    'initials'=>'DC', 'status'=>'Available',   'dot'=>'bg-emerald-500', 'opacity'=>''],
                    ['name'=>'Sarah Jenkins', 'initials'=>'SJ', 'status'=>'Available',   'dot'=>'bg-emerald-500', 'opacity'=>''],
                    ['name'=>'Marcio Lopez',  'initials'=>'ML', 'status'=>'In Meeting',  'dot'=>'bg-blue-500',    'opacity'=>''],
                    ['name'=>'Elena Belov',   'initials'=>'EB', 'status'=>'Off Duty',    'dot'=>'bg-slate-300',   'opacity'=>'opacity-50'],
                    ['name'=>'James Porter',  'initials'=>'JP', 'status'=>'Available',   'dot'=>'bg-emerald-500', 'opacity'=>''],
                ];
                $statusColor = ['Available'=>'text-emerald-600','In Meeting'=>'text-blue-600','Off Duty'=>'text-slate-400'];
                foreach ($roster as $r):
                    $sc = $statusColor[$r['status']] ?? 'text-slate-400';
                ?>
                <div class="flex items-center gap-3">
                    <div class="relative <?= $r['opacity'] ?>">
                        <div class="w-11 h-11 rounded-xl primary-gradient flex items-center justify-center text-white text-sm font-bold"><?= $r['initials'] ?></div>
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 <?= $r['dot'] ?> rounded-full border-2 border-white"></span>
                    </div>
                    <div class="<?= $r['opacity'] ?>">
                        <p class="text-xs font-bold text-slate-800"><?= $r['name'] ?></p>
                        <p class="text-[10px] font-bold <?= $sc ?> uppercase"><?= $r['status'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div><!-- end p-6 -->
</main>

<!-- FAB -->
<button class="fixed bottom-7 right-7 primary-gradient w-13 h-13 w-12 h-12 rounded-full flex items-center justify-center text-white shadow-2xl shadow-primary/40 hover:scale-110 transition-transform z-50">
    <span class="material-symbols-outlined">add</span>
</button>

</body>
</html>