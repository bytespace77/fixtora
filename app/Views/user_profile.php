<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>User Profile & Settings | Concierge Desk</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "outline": "#737781",
                    "secondary-fixed-dim": "#aec9ec",
                    "on-primary": "#ffffff",
                    "error-container": "#ffdad6",
                    "surface-dim": "#d9dadb",
                    "inverse-on-surface": "#f0f1f2",
                    "on-tertiary-container": "#f7a967",
                    "on-primary-fixed": "#001c3b",
                    "on-tertiary-fixed": "#2f1500",
                    "on-secondary-fixed": "#001d36",
                    "surface-tint": "#335f99",
                    "surface-container-highest": "#e1e3e4",
                    "surface-bright": "#f8f9fa",
                    "on-secondary": "#ffffff",
                    "background": "#f8f9fa",
                    "tertiary-fixed": "#ffdcc3",
                    "on-background": "#191c1d",
                    "error": "#ba1a1a",
                    "surface-container-high": "#e7e8e9",
                    "inverse-surface": "#2e3132",
                    "primary-fixed-dim": "#a6c8ff",
                    "on-tertiary": "#ffffff",
                    "secondary-container": "#bfd9fd",
                    "on-surface-variant": "#424750",
                    "outline-variant": "#c3c6d1",
                    "on-primary-fixed-variant": "#144780",
                    "on-tertiary-fixed-variant": "#6e3900",
                    "on-error": "#ffffff",
                    "on-surface": "#191c1d",
                    "on-primary-container": "#93bcfc",
                    "tertiary-container": "#733c00",
                    "surface-container-low": "#f3f4f5",
                    "primary-container": "#1a4b84",
                    "secondary": "#46607f",
                    "primary": "#003466",
                    "surface-container-lowest": "#ffffff",
                    "inverse-primary": "#a6c8ff",
                    "surface-container": "#edeeef",
                    "tertiary-fixed-dim": "#ffb77d",
                    "on-secondary-container": "#455f7d",
                    "on-secondary-fixed-variant": "#2e4966",
                    "surface": "#f8f9fa",
                    "primary-fixed": "#d5e3ff",
                    "on-error-container": "#93000a",
                    "tertiary": "#522900",
                    "secondary-fixed": "#d1e4ff",
                    "surface-variant": "#e1e3e4"
                },
                fontFamily: {
                    "montserrat": ["Montserrat", "sans-serif"],
                    "headline":   ["Montserrat", "sans-serif"],
                    "body":       ["Montserrat", "sans-serif"],
                    "label":      ["Montserrat", "sans-serif"]
                },
                borderRadius: {"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},
            },
        },
    }
</script>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { font-family: 'Montserrat', sans-serif; }
    .prim-grad { background: linear-gradient(135deg, #003466 0%, #1a4b84 100%); }

    /* Toggle switch */
    .toggle { position:relative; display:inline-block; width:44px; height:24px; }
    .toggle input { opacity:0; width:0; height:0; }
    .slider {
        position:absolute; cursor:pointer; inset:0;
        background:#cbd5e1; border-radius:9999px; transition:.3s;
    }
    .slider::before {
        content:""; position:absolute;
        width:18px; height:18px; left:3px; bottom:3px;
        background:#fff; border-radius:50%; transition:.3s;
    }
    input:checked + .slider { background:#003466; }
    input:checked + .slider::before { transform:translateX(20px); }

    /* Checkbox custom */
    .custom-cb { width:18px; height:18px; accent-color:#003466; cursor:pointer; }
</style>
</head>
<body class="bg-background text-on-surface antialiased">

<!-- ── Sidebar ── -->
<aside class="h-screen w-48 fixed left-0 top-0 bg-slate-100 flex flex-col py-6 z-50">
    <div class="px-6 mb-10">
        <h1 class="text-base font-black text-blue-900 uppercase tracking-tighter">Concierge</h1>
        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.2em] mt-1">Enterprise Support</p>
    </div>
    <nav class="flex-grow space-y-0.5">
        <?php
        $nav = [
            ['icon'=>'dashboard',          'label'=>'Dashboard', 'active'=>false],
            ['icon'=>'confirmation_number', 'label'=>'Tickets',  'active'=>false],
            ['icon'=>'assignment',          'label'=>'Tasks',    'active'=>false],
            ['icon'=>'settings',            'label'=>'Settings', 'active'=>true],
        ];
        foreach ($nav as $n):
            $cls = $n['active']
                ? 'flex items-center gap-3 px-6 py-3 text-blue-900 font-bold border-l-4 border-blue-900 bg-white'
                : 'flex items-center gap-3 px-6 py-3 text-slate-500 font-semibold hover:text-blue-800 hover:bg-white/60 transition-all';
        ?>
        <a class="<?= $cls ?>" href="#">
            <span class="material-symbols-outlined text-base"><?= $n['icon'] ?></span>
            <span class="text-[10px] uppercase tracking-wider"><?= $n['label'] ?></span>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="mt-auto pt-5 px-5 border-t border-slate-200 space-y-0.5">
        <a class="flex items-center gap-3 px-3 py-3 text-slate-500 hover:text-blue-800 transition-all" href="#">
            <span class="material-symbols-outlined text-base">contact_support</span>
            <span class="font-semibold text-[10px] uppercase tracking-wider">Support</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-3 text-slate-500 hover:text-red-600 transition-all" href="#">
            <span class="material-symbols-outlined text-base">logout</span>
            <span class="font-semibold text-[10px] uppercase tracking-wider">Logout</span>
        </a>
    </div>
</aside>

<!-- ── Main ── -->
<div class="pl-48 flex flex-col min-h-screen">

    <!-- Top Bar -->
    <header class="flex justify-between items-center px-10 h-16 w-full bg-slate-50 sticky top-0 z-40 border-b border-slate-100">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-bold tracking-tighter text-blue-900">Concierge Desk</h2>
            <div class="h-4 w-px bg-slate-300 mx-1"></div>
            <span class="text-slate-400 text-xs font-semibold uppercase tracking-widest">User Profile</span>
        </div>
        <div class="flex items-center gap-5">
            <div class="flex items-center gap-1">
                <button class="p-2 text-slate-500 hover:bg-slate-200/50 rounded-full transition-colors relative">
                    <span class="material-symbols-outlined text-base">notifications</span>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-error rounded-full"></span>
                </button>
                <button class="p-2 text-slate-500 hover:bg-slate-200/50 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-base">help_outline</span>
                </button>
            </div>
            <div class="w-8 h-8 rounded-full prim-grad flex items-center justify-center text-white text-xs font-bold">AS</div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-grow p-10 bg-surface">

        <!-- Tab Bar + Save -->
        <div class="mb-8 flex items-center justify-between bg-surface-container-low px-6 py-3 rounded-xl">
            <div class="flex items-center gap-8">
                <?php
                $tabs = ['General'=>true, 'Security'=>false, 'Notifications'=>false, 'Integrations'=>false];
                foreach ($tabs as $tab => $active):
                    $cls = $active
                        ? 'relative py-2 text-primary font-bold text-sm tracking-tight border-b-2 border-primary'
                        : 'py-2 text-slate-500 font-semibold text-sm tracking-tight hover:text-primary transition-colors';
                ?>
                <button class="<?= $cls ?>"><?= $tab ?></button>
                <?php endforeach; ?>
            </div>
            <button class="prim-grad text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg active:scale-95 transition-all">
                Save Changes
            </button>
        </div>

        <!-- Two-column layout -->
        <div class="grid grid-cols-12 gap-8 items-start">

            <!-- ── Left Column ── -->
            <div class="col-span-12 lg:col-span-4 space-y-6">

                <!-- Profile Card -->
                <div class="bg-white p-8 rounded-xl">
                    <!-- Avatar -->
                    <div class="relative w-36 h-36 mx-auto mb-6">
                        <div class="w-full h-full rounded-xl prim-grad flex items-center justify-center text-white text-4xl font-black shadow-xl overflow-hidden">
                            AS
                        </div>
                        <button class="absolute -bottom-3 -right-3 bg-white p-2.5 rounded-full shadow-lg border border-slate-100 text-primary hover:bg-slate-50 transition-colors">
                            <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">photo_camera</span>
                        </button>
                    </div>
                    <!-- Name -->
                    <div class="text-center mb-8">
                        <h3 class="text-xl font-bold tracking-tight text-on-surface">Alex Sterling</h3>
                        <p class="text-slate-500 font-semibold uppercase text-[9px] tracking-widest mt-1">Senior Architect</p>
                    </div>
                    <!-- Meta -->
                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-wide">Account Status</span>
                            <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full font-bold text-[11px]">Verified<br>Professional</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-wide">Member Since</span>
                            <span class="text-on-surface font-semibold">Jan 2022</span>
                        </div>
                    </div>
                </div>

                <!-- Storage -->
                <div class="bg-primary/5 p-6 rounded-xl border border-primary/10">
                    <h4 class="text-primary font-bold text-[10px] uppercase tracking-widest mb-4">Storage Usage</h4>
                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden mb-3">
                        <div class="prim-grad h-full rounded-full" style="width:72%"></div>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium">7.2 GB of 10 GB used (72%)</p>
                </div>

            </div>

            <!-- ── Right Column ── -->
            <div class="col-span-12 lg:col-span-8 space-y-8">

                <!-- General Information -->
                <div class="bg-white p-8 rounded-xl">
                    <h3 class="text-lg font-bold text-on-surface mb-1">General Information</h3>
                    <p class="text-sm text-slate-500 mb-7">Update your professional identity and contact details.</p>

                    <!-- Name + Role row -->
                    <div class="grid grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Full Name</label>
                            <input type="text" value="Alex Sterling"
                                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-semibold text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Role</label>
                            <input type="text" value="Senior Architect"
                                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-semibold text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"/>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-5">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Email Address</label>
                        <div class="flex items-center bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 gap-3">
                            <span class="material-symbols-outlined text-slate-400 text-base">mail</span>
                            <input type="email" value="alex.sterling@concierge.ai"
                                class="flex-1 bg-transparent text-sm font-semibold text-on-surface focus:outline-none"/>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Phone Number</label>
                        <div class="flex items-center bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 gap-3">
                            <span class="material-symbols-outlined text-slate-400 text-base">phone</span>
                            <input type="tel" value="+1 (555) 892-4401"
                                class="flex-1 bg-transparent text-sm font-semibold text-on-surface focus:outline-none"/>
                        </div>
                    </div>
                </div>

                <!-- Security & Privacy -->
                <div class="bg-white p-8 rounded-xl">
                    <div class="flex items-start justify-between mb-1">
                        <div>
                            <h3 class="text-lg font-bold text-on-surface">Security & Privacy</h3>
                            <p class="text-sm text-slate-500 mt-1">Manage your access and active session logs.</p>
                        </div>
                        <button class="text-[10px] font-bold uppercase tracking-widest text-primary hover:underline mt-1">
                            Manage Security
                        </button>
                    </div>

                    <!-- 2FA Toggle -->
                    <div class="mt-6 flex items-center justify-between bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 prim-grad rounded-xl flex items-center justify-center text-white shrink-0">
                                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">verified_user</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface">Two-Factor Authentication</p>
                                <p class="text-xs text-slate-500 mt-0.5">Extra layer of security via mobile authenticator.</p>
                            </div>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" checked/>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <!-- Active Sessions -->
                    <div class="mt-7">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-4">Active Sessions</p>
                        <div class="space-y-3">
                            <?php
                            $sessions = [
                                ['icon'=>'laptop_mac', 'label'=>'MacOS • San Francisco, US', 'sub'=>'Chrome Browser · Active Now', 'badge'=>'Current', 'badge_cls'=>'text-slate-500 font-black text-[10px] uppercase tracking-wider', 'current'=>true],
                                ['icon'=>'phone_iphone', 'label'=>'iPhone 14 Pro • San Francisco, US', 'sub'=>'Concierge App · 2 hours ago', 'badge'=>'Logout', 'badge_cls'=>'text-error font-black text-[10px] uppercase tracking-wider hover:underline cursor-pointer', 'current'=>false],
                            ];
                            foreach ($sessions as $s):
                            ?>
                            <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                                        <span class="material-symbols-outlined text-base"><?= $s['icon'] ?></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-on-surface"><?= $s['label'] ?></p>
                                        <p class="text-[11px] text-slate-400 mt-0.5"><?= $s['sub'] ?></p>
                                    </div>
                                </div>
                                <span class="<?= $s['badge_cls'] ?>"><?= $s['badge'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="bg-white p-8 rounded-xl">
                    <h3 class="text-lg font-bold text-on-surface mb-1">Notifications Preferences</h3>
                    <p class="text-sm text-slate-500 mb-7">Control how you stay informed about helpdesk activities.</p>

                    <!-- Table -->
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left text-[10px] font-bold uppercase tracking-widest text-slate-400 pb-3">Event Type</th>
                                <th class="text-center text-[10px] font-bold uppercase tracking-widest text-slate-400 pb-3 w-16">Email</th>
                                <th class="text-center text-[10px] font-bold uppercase tracking-widest text-slate-400 pb-3 w-16">Push</th>
                                <th class="text-center text-[10px] font-bold uppercase tracking-widest text-slate-400 pb-3 w-16">Desktop</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $notifs = [
                                ['label'=>'New Ticket Assignment',  'desc'=>'When a new ticket is assigned to you.',    'email'=>true,  'push'=>true,  'desktop'=>false],
                                ['label'=>'SLA Breach Alert',       'desc'=>'Critical alerts for tickets nearing SLA deadlines.', 'email'=>true,  'push'=>true,  'desktop'=>true],
                                ['label'=>'System Update',          'desc'=>'Platform maintenance and feature releases.', 'email'=>false, 'push'=>false, 'desktop'=>true],
                            ];
                            foreach ($notifs as $n):
                            ?>
                            <tr class="border-b border-slate-50 last:border-0">
                                <td class="py-5">
                                    <p class="font-bold text-on-surface text-sm"><?= $n['label'] ?></p>
                                    <p class="text-[11px] text-slate-400 mt-0.5"><?= $n['desc'] ?></p>
                                </td>
                                <td class="text-center py-5">
                                    <input type="checkbox" class="custom-cb" <?= $n['email'] ? 'checked' : '' ?>/>
                                </td>
                                <td class="text-center py-5">
                                    <input type="checkbox" class="custom-cb" <?= $n['push'] ? 'checked' : '' ?>/>
                                </td>
                                <td class="text-center py-5">
                                    <input type="checkbox" class="custom-cb" <?= $n['desktop'] ? 'checked' : '' ?>/>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div><!-- /right col -->
        </div>
    </main>
</div>

</body>
</html>