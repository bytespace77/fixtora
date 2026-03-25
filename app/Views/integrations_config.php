<?php

$active_tab = $_GET['tab'] ?? 'all';
$filter     = $_GET['filter'] ?? '';

$tabs = ['all' => 'All Tools', 'communication' => 'Communication', 'developer' => 'Developer Tools', 'analytics' => 'Analytics'];

$active_connections = [
    [
        'name'    => 'Slack',
        'desc'    => 'Real-time ticket notifications and status updates directly in channels.',
        'color'   => '#611f69',
        'icon'    => 'slack',
    ],
    [
        'name'    => 'Jira Software',
        'desc'    => 'Bi-directional synchronization of issues, comments, and resolutions.',
        'color'   => '#0052cc',
        'icon'    => 'jira',
    ],
];

$catalog = [
    ['name' => 'GitHub',         'desc' => 'Automate ticket closure when pull requests are merged and link commits to support tasks.',        'color' => '#24292e', 'icon' => 'github',   'category' => 'developer'],
    ['name' => 'Azure DevOps',   'desc' => 'Scale your enterprise workflow with native ADO work item tracking and CI/CD triggers.',           'color' => '#0078d4', 'icon' => 'azure',    'category' => 'developer'],
    ['name' => 'Microsoft Teams','desc' => 'Bring your helpdesk into the Teams ecosystem with custom tabs and adaptive cards.',               'color' => '#464eb8', 'icon' => 'teams',    'category' => 'communication'],
    ['name' => 'Zendesk',        'desc' => 'Migrate or sync your legacy tickets into the Orchestrator for advanced routing.',                 'color' => '#03363d', 'icon' => 'zendesk',  'category' => 'communication'],
];

$filtered = array_filter($catalog, function($t) use ($active_tab, $filter) {
    $tab_ok  = $active_tab === 'all' || $t['category'] === $active_tab;
    $name_ok = $filter === '' || stripos($t['name'], $filter) !== false;
    return $tab_ok && $name_ok;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Integrations — Helpdesk Orchestrator</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --sidebar-w:192px;
    --nav-h:56px;
    --bg:#f5f6fa;
    --white:#ffffff;
    --navy:#0d1b2e;
    --navy-mid:#1a2e4a;
    --blue:#2563eb;
    --blue-light:#eff4ff;
    --border:#e2e6ed;
    --muted:#6b7a8d;
    --text:#1a2335;
    --connected:#16a34a;
    --connected-bg:#f0fdf4;
    --connected-border:#bbf7d0;
    --shadow:0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.05);
    --shadow-md:0 4px 12px rgba(0,0,0,.10);
}
html,body{height:100%;font-family:'Inter',sans-serif;font-size:14px;color:var(--text);background:var(--bg)}

.topnav{
    position:fixed;top:0;left:0;right:0;height:var(--nav-h);
    background:var(--white);border-bottom:1px solid var(--border);
    display:flex;align-items:center;gap:0;z-index:100;
}
.topnav-brand{
    width:var(--sidebar-w);flex-shrink:0;
    padding:0 20px;
    display:flex;flex-direction:column;justify-content:center;
    border-right:1px solid var(--border);height:100%;
}
.topnav-brand .org{font-size:13px;font-weight:700;color:var(--navy);letter-spacing:.3px}
.topnav-brand .tier{font-size:10px;font-weight:500;letter-spacing:1.8px;color:var(--muted);text-transform:uppercase;margin-top:1px}
.topnav-center{
    flex:1;display:flex;align-items:center;gap:4px;
    padding:0 24px;
}
.topnav-title{font-size:15px;font-weight:600;color:var(--navy);margin-right:16px}
.topnav-link{
    font-size:13.5px;color:var(--muted);text-decoration:none;
    padding:6px 12px;border-radius:6px;transition:background .15s,color .15s;
}
.topnav-link:hover{background:#f0f2f5;color:var(--navy)}
.topnav-search{
    margin-left:auto;
    display:flex;align-items:center;
    background:#f4f5f8;border:1px solid var(--border);border-radius:8px;
    padding:0 12px;height:34px;gap:8px;min-width:200px;
}
.topnav-search input{
    border:none;background:transparent;outline:none;font-size:13px;color:var(--text);width:100%;
}
.topnav-search input::placeholder{color:#a0aab4}
.topnav-icons{display:flex;align-items:center;gap:4px;padding:0 16px}
.icon-btn{
    width:34px;height:34px;border-radius:8px;border:none;background:transparent;
    cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);
    transition:background .15s,color .15s;
}
.icon-btn:hover{background:#f0f2f5;color:var(--navy)}
.avatar{
    width:34px;height:34px;border-radius:50%;
    background:linear-gradient(135deg,#f97316,#ef4444);
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:13px;font-weight:600;cursor:pointer;
}

.sidebar{
    position:fixed;top:var(--nav-h);left:0;bottom:0;
    width:var(--sidebar-w);
    background:var(--white);border-right:1px solid var(--border);
    display:flex;flex-direction:column;z-index:90;
    padding:16px 0;
}
.nav-item{
    display:flex;align-items:center;gap:10px;
    padding:9px 20px;
    text-decoration:none;
    font-size:12px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;
    color:var(--muted);
    transition:background .15s,color .15s,border-color .15s;
    border-left:3px solid transparent;
    position:relative;
}
.nav-item:hover{background:#f5f6fa;color:var(--navy)}
.nav-item.active{
    background:var(--blue-light);
    color:var(--blue);
    border-left-color:var(--blue);
}
.nav-item svg{flex-shrink:0;opacity:.7}
.nav-item.active svg{opacity:1}
.sidebar-bottom{margin-top:auto;border-top:1px solid var(--border);padding-top:12px}

.main{
    margin-left:var(--sidebar-w);
    margin-top:var(--nav-h);
    padding:0 32px 60px;
    min-height:calc(100vh - var(--nav-h));
}

.breadcrumb-bar{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 0;
    border-bottom:1px solid var(--border);
    margin-bottom:32px;
}
.breadcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)}
.breadcrumb a{color:var(--muted);text-decoration:none}
.breadcrumb a:hover{color:var(--navy)}
.breadcrumb .sep{color:#c8ced7}
.breadcrumb .current{color:var(--navy);font-weight:600}
.shortcut{
    display:flex;align-items:center;gap:6px;
    font-size:11px;font-weight:500;letter-spacing:.5px;color:var(--muted);
}
.kbd{
    display:inline-flex;align-items:center;justify-content:center;
    background:#f0f2f5;border:1px solid var(--border);border-radius:4px;
    padding:2px 6px;font-size:10.5px;font-family:monospace;color:var(--navy);
    min-width:22px;
}

.page-header{margin-bottom:36px}
.page-header h1{font-size:26px;font-weight:700;color:var(--navy);margin-bottom:8px}
.page-header p{font-size:14px;color:var(--muted);line-height:1.6;max-width:560px}

.section-label{
    display:flex;align-items:center;gap:12px;
    font-size:11px;font-weight:700;letter-spacing:2px;color:var(--muted);
    text-transform:uppercase;margin-bottom:20px;
}
.section-label::before{
    content:'';display:block;width:28px;height:2px;background:var(--navy-mid);border-radius:2px;
}

.active-grid{
    display:grid;grid-template-columns:repeat(2,1fr);gap:16px;
    margin-bottom:40px;
}
@media(max-width:700px){.active-grid{grid-template-columns:1fr}}

.conn-card{
    background:var(--white);border:1px solid var(--border);border-radius:12px;
    padding:20px 22px;
    display:flex;flex-direction:column;gap:10px;
    box-shadow:var(--shadow);
    transition:box-shadow .2s;
}
.conn-card:hover{box-shadow:var(--shadow-md)}
.conn-card-top{display:flex;align-items:flex-start;gap:14px}
.conn-logo{
    width:48px;height:48px;border-radius:10px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    font-size:20px;font-weight:700;color:#fff;
}
.conn-info{flex:1}
.conn-name-row{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:5px}
.conn-name{font-size:15px;font-weight:600;color:var(--navy)}
.badge-connected{
    font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;
    color:var(--connected);background:var(--connected-bg);
    border:1px solid var(--connected-border);
    border-radius:20px;padding:2px 9px;
}
.conn-desc{font-size:13px;color:var(--muted);line-height:1.5}
.configure-link{
    display:inline-flex;align-items:center;gap:5px;
    font-size:13px;font-weight:500;color:var(--blue);text-decoration:none;
    margin-top:4px;transition:gap .15s;
}
.configure-link:hover{gap:8px}

.catalog-header{
    display:flex;align-items:center;justify-content:space-between;
    flex-wrap:wrap;gap:14px;
    margin-bottom:18px;
}
.tab-group{display:flex;gap:4px;flex-wrap:wrap}
.tab-btn{
    padding:7px 14px;border-radius:20px;border:1px solid var(--border);
    background:var(--white);font-size:13px;font-weight:500;color:var(--muted);
    cursor:pointer;text-decoration:none;
    transition:background .15s,color .15s,border-color .15s;
}
.tab-btn:hover{background:#f0f2f5;color:var(--navy)}
.tab-btn.active{background:var(--navy);color:#fff;border-color:var(--navy)}
.filter-wrap{
    display:flex;align-items:center;gap:8px;
    background:var(--white);border:1px solid var(--border);border-radius:8px;
    padding:0 12px;height:38px;min-width:220px;
}
.filter-wrap input{
    border:none;background:transparent;outline:none;
    font-size:13px;color:var(--text);width:100%;
}
.filter-wrap input::placeholder{color:#a0aab4}

.catalog-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:16px;
    border:none;
    background:transparent);
    margin-bottom:32px;
}
@media(max-width:900px){.catalog-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:580px){.catalog-grid{grid-template-columns:1fr}}

.tool-card:hover{
    background:var(--white);
    padding:32px 28px 28px;
    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;
    border:1px solid var(--border);   
    border-radius:12px;               
    box-shadow:var(--shadow);         
    transition:all .2s ease;
    transform:translateY(-2px);
}
.tool-logo{
    width:56px;height:56px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    font-size:22px;font-weight:700;color:#fff;
    margin-bottom:18px;flex-shrink:0;
}
.tool-name{font-size:16px;font-weight:700;color:var(--navy);margin-bottom:10px}
.tool-desc{font-size:13px;color:var(--muted);line-height:1.55;margin-bottom:20px;flex:1}
.btn-connect{
    width:100%;padding:10px;
    background:var(--navy);color:#fff;
    border:none;border-radius:8px;
    font-family:'Inter',sans-serif;font-size:13px;font-weight:600;
    cursor:pointer;transition:background .15s,transform .1s;
}
.btn-connect:hover{background:#162234}
.btn-connect:active{transform:scale(.98)}

.custom-cta{
    grid-column:1/3;
    background:var(--white);padding:36px 32px;
    display:flex;align-items:center;gap:28px;
}
.custom-cta-icon{
    width:56px;height:56px;border-radius:50%;border:2px solid var(--border);
    display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0;
}
.custom-cta-body h3{font-size:18px;font-weight:700;color:var(--navy);margin-bottom:8px}
.custom-cta-body p{font-size:13.5px;color:var(--muted);line-height:1.55;max-width:440px;margin-bottom:14px}
.cta-link{
    display:inline-flex;align-items:center;gap:5px;
    font-size:13.5px;font-weight:600;color:var(--navy);text-decoration:none;
    border-bottom:2px solid var(--navy);padding-bottom:1px;
    transition:gap .15s;
}
.cta-link:hover{gap:8px}

.page-footer{
    display:flex;align-items:center;justify-content:space-between;
    padding:22px 0 0;border-top:1px solid var(--border);
    flex-wrap:wrap;gap:12px;
}
.security-badges{display:flex;align-items:center;gap:6px}
.sec-label{font-size:10.5px;font-weight:700;letter-spacing:1.8px;color:var(--muted);text-transform:uppercase;margin-right:8px}
.sec-badge{
    display:flex;align-items:center;gap:5px;
    background:#f0fdf4;border:1px solid #bbf7d0;border-radius:20px;
    padding:4px 10px;font-size:11.5px;font-weight:600;color:#15803d;
}
.footer-copy{font-size:12px;color:var(--muted);text-align:right;line-height:1.5}
.footer-copy .status{color:#16a34a;font-weight:600}
</style>
</head>
<body>

<header class="topnav">
    <div class="topnav-brand">
        <div class="org">GLOBAL OPS</div>
        <div class="tier">Enterprise Tier</div>
    </div>
    <div class="topnav-center">
        <span class="topnav-title">Helpdesk Orchestrator</span>
        <a href="#" class="topnav-link">Docs</a>
        <a href="#" class="topnav-link">API</a>
        <a href="#" class="topnav-link">Support</a>
        <div class="topnav-search" style="margin-left:auto">
            <svg width="14" height="14" fill="none" stroke="#a0aab4" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search components...">
        </div>
    </div>
    <div class="topnav-icons">
        <button class="icon-btn" title="Notifications">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </button>
        <button class="icon-btn" title="Help">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </button>
        <div class="avatar">G</div>
    </div>
</header>

<aside class="sidebar">
    <nav>
        <a href="#" class="nav-item">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Overview
        </a>
        <a href="#" class="nav-item">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
            Tickets
        </a>
        <a href="#" class="nav-item">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Tasks
        </a>
        <a href="#" class="nav-item active">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            Settings
        </a>
    </nav>
    <div class="sidebar-bottom">
        <a href="#" class="nav-item">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Help Center
        </a>
        <a href="#" class="nav-item">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </a>
    </div>
</aside>

<main class="main">
    <div class="breadcrumb-bar">
        <div class="breadcrumb">
            <a href="#">Settings</a>
            <span class="sep">›</span>
            <span class="current">Integrations</span>
        </div>
        <div class="shortcut">
            <span>PRESS</span>
            <span class="kbd">⌘</span>
            <span>+</span>
            <span class="kbd">K</span>
            <span>TO QUICK SEARCH</span>
        </div>
    </div>

    <div class="page-header">
        <h1>Connected Ecosystem</h1>
        <p>Centralize your operations by bridging the gap between your favorite tools and our orchestration engine.</p>
    </div>

    <div class="section-label">Active Connections</div>
    <div class="active-grid">
        <?php foreach ($active_connections as $c): ?>
        <div class="conn-card">
            <div class="conn-card-top">
                <div class="conn-logo" style="background:<?= $c['color'] ?>">
                    <?php if ($c['icon']==='slack'): ?>
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="white"><path d="M6 15a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                    <?php else: ?>
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="white"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><path d="M17 13h4v4h-4zM13 17h4v4h-4z" fill="rgba(255,255,255,0.6)"/></svg>
                    <?php endif; ?>
                </div>
                <div class="conn-info">
                    <div class="conn-name-row">
                        <div class="conn-name"><?= htmlspecialchars($c['name']) ?></div>
                        <span class="badge-connected">Connected</span>
                    </div>
                    <div class="conn-desc"><?= htmlspecialchars($c['desc']) ?></div>
                </div>
            </div>
            <div><a href="#" class="configure-link">Configure →</a></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="catalog-header">
        <div style="display:flex;flex-direction:column;gap:14px;width:100%">
            <div class="section-label" style="margin-bottom:0">Integration Catalog</div>
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
                <div class="tab-group">
                    <?php foreach ($tabs as $key => $label): ?>
                    <a href="?tab=<?= $key ?>&filter=<?= urlencode($filter) ?>"
                       class="tab-btn <?= $active_tab === $key ? 'active' : '' ?>">
                        <?= $label ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <form method="GET" style="display:flex" action="">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                    <div class="filter-wrap">
                        <svg width="14" height="14" fill="none" stroke="#a0aab4" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
                        <input type="text" name="filter" value="<?= htmlspecialchars($filter) ?>" placeholder="Filter by name..." oninput="this.form.submit()">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="catalog-grid">
        <div class="custom-cta" style="grid-column:1/<?= count($filtered) < 3 ? count($filtered)+1 : '3' ?>; order:999">
            <div class="custom-cta-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="custom-cta-body">
                <h3>Request Custom Integration</h3>
                <p>Don't see your internal tools? Our engineering concierge team can build a custom connector for your enterprise stack.</p>
                <a href="#" class="cta-link">Speak with an architect →</a>
            </div>
        </div>

        <?php foreach ($filtered as $tool): ?>
        <div class="tool-card">
            <div class="tool-logo" style="background:<?= $tool['color'] ?>">
                <?php if ($tool['icon']==='github'): ?>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.5.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.22-.25-4.55-1.11-4.55-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.64 0 0 .84-.27 2.75 1.02A9.56 9.56 0 0 1 12 6.8c.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.37.2 2.39.1 2.64.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.69-4.57 4.94.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10.01 10.01 0 0 0 22 12c0-5.52-4.48-10-10-10z"/></svg>
                <?php elseif ($tool['icon']==='azure'): ?>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M5.4 21L9.8 5.8l5.8 9.4H9.4L5.4 21zM13.4 3l5.2 14.4-8.6 1.6L13.4 3z" opacity=".9"/></svg>
                <?php elseif ($tool['icon']==='teams'): ?>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M19.5 8.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" opacity=".6"/><path d="M14 11.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/><path d="M17 13h5v6a2 2 0 0 1-2 2h-3v-8z" opacity=".6"/><path d="M3 14a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6z"/></svg>
                <?php else: ?>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                <?php endif; ?>
            </div>
            <div class="tool-name"><?= htmlspecialchars($tool['name']) ?></div>
            <div class="tool-desc"><?= htmlspecialchars($tool['desc']) ?></div>
            <button class="btn-connect">Connect Tool</button>
        </div>
        <?php endforeach; ?>
    </div>

    <footer class="page-footer">
        <div class="security-badges">
            <span class="sec-label">Security Standards</span>
            <span class="sec-badge">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                SOC2 Type II
            </span>
            <span class="sec-badge">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                AES-256 Encrypted
            </span>
        </div>
        <div class="footer-copy">
            © 2024 Helpdesk Orchestrator Enterprise<br>
            System Status: <span class="status">OPERATIONAL</span>
        </div>
    </footer>

</main>

</body>
</html>