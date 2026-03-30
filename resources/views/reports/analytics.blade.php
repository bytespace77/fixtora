<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reports & Analytics — Helpdesk Analytics</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --sw:210px;
    --nh:56px;
    --bg:#f2f4f8;
    --white:#fff;
    --navy:#0d1b2e;
    --navy2:#1a2e4a;
    --blue:#1e3a6e;
    --blue-act:#1d4ed8;
    --border:#e2e6ed;
    --muted:#6b7a8d;
    --text:#1a2335;
    --green:#16a34a;
    --orange:#f59e0b;
    --red:#ef4444;
    --shadow:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
    --shadow-md:0 4px 14px rgba(0,0,0,.09);
    --mont:'Montserrat',sans-serif;
}
html,body{height:100%;font-family:'Inter',sans-serif;font-size:14px;color:var(--text);background:var(--bg);overflow-x:hidden}

.tnav{
    position:fixed;top:0;left:0;right:0;height:var(--nh);
    background:var(--white);border-bottom:1px solid var(--border);
    display:flex;align-items:center;z-index:200;
    padding:0 20px 0 0;
}
.tnav-brand{
    width:var(--sw);flex-shrink:0;padding:0 20px;
    font-size:15px;font-weight:700;color:var(--navy);
    letter-spacing:-.2px;
}
.tnav-links{display:flex;align-items:center;gap:2px;flex:1;padding-left:8px}
.tlink{
    padding:7px 14px;font-size:13.5px;font-weight:500;color:var(--muted);
    text-decoration:none;border-radius:6px;transition:color .15s;
    position:relative;
}
.tlink:hover{color:var(--navy)}
.tlink.active{color:var(--blue-act);font-weight:600}
.tlink.active::after{
    content:'';position:absolute;bottom:-17px;left:14px;right:14px;
    height:2px;background:var(--blue-act);border-radius:2px;
}
.tnav-right{display:flex;align-items:center;gap:6px;margin-left:auto}
.tnav-search{
    display:flex;align-items:center;gap:8px;
    background:#f4f5f8;border:1px solid var(--border);border-radius:8px;
    padding:0 12px;height:34px;width:220px;
}
.tnav-search input{border:none;background:transparent;outline:none;font-size:13px;color:var(--text);width:100%}
.tnav-search input::placeholder{color:#a0aab4}
.icon-btn{
    width:34px;height:34px;border-radius:8px;border:none;background:transparent;
    cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);
    transition:background .15s;
}
.icon-btn:hover{background:#f0f2f5}
.avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#94a3b8,#64748b);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:600;cursor:pointer}

.sidebar{
    position:fixed;top:var(--nh);left:0;bottom:0;width:var(--sw);
    background:var(--white);border-right:1px solid var(--border);
    display:flex;flex-direction:column;z-index:100;padding:16px 0;
}
.sb-brand{
    display:flex;align-items:center;gap:12px;
    padding:10px 16px 18px;margin-bottom:4px;
    border-bottom:1px solid var(--border);
}
.sb-icon{
    width:38px;height:38px;border-radius:10px;background:var(--navy2);
    display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;
}
.sb-label{font-size:12.5px;font-weight:700;color:var(--navy)}
.sb-sub{font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-top:1px}
.nav-item{
    display:flex;align-items:center;gap:10px;
    padding:10px 18px;
    font-size:12px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;
    color:var(--muted);text-decoration:none;
    border-left:3px solid transparent;
    transition:background .15s,color .15s;
}
.nav-item:hover{background:#f5f6fa;color:var(--navy)}
.nav-item.active{background:#eff4ff;color:var(--blue-act);border-left-color:var(--blue-act)}
.sb-footer{margin-top:auto;border-top:1px solid var(--border);padding:16px}
.btn-export{
    width:100%;padding:11px;background:var(--navy2);color:#fff;
    border:none;border-radius:9px;font-family:'Inter',sans-serif;
    font-size:12px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;
    cursor:pointer;transition:background .15s;
}
.btn-export:hover{background:#162234}
.sb-links{margin-top:14px;display:flex;flex-direction:column;gap:2px}
.sb-link{
    display:flex;align-items:center;gap:8px;padding:7px 0;
    font-size:12.5px;color:var(--muted);text-decoration:none;
    transition:color .15s;
}
.sb-link:hover{color:var(--navy)}

.main{margin-left:var(--sw);margin-top:var(--nh);padding:28px 28px 80px;min-height:calc(100vh - var(--nh))}

.page-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:14px}
.page-head h1{font-size:28px;font-weight:800;color:var(--navy);letter-spacing:-.5px}
.page-head p{font-size:13.5px;color:var(--muted);margin-top:4px}
.head-controls{display:flex;align-items:center;gap:8px}
.ctrl-btn{
    display:flex;align-items:center;gap:7px;
    padding:8px 14px;background:var(--white);border:1px solid var(--border);border-radius:8px;
    font-size:13px;font-weight:500;color:var(--text);cursor:pointer;
    transition:background .15s;white-space:nowrap;
}
.ctrl-btn:hover{background:#f5f6fa}
.btn-apply{
    padding:9px 20px;background:var(--navy2);color:#fff;border:none;
    border-radius:8px;font-family:'Inter',sans-serif;font-size:13px;font-weight:600;
    cursor:pointer;transition:background .15s;
}
.btn-apply:hover{background:#162234}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
.kpi-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:20px 20px 18px;box-shadow:var(--shadow);
}
.kpi-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px}
.kpi-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.kpi-badge{font-size:11px;font-weight:600;padding:3px 8px;border-radius:20px}
.badge-up{color:#16a34a;background:#f0fdf4}
.badge-down{color:#ef4444;background:#fef2f2}
.badge-target{color:#f59e0b;background:#fffbeb}
.badge-neutral{color:#6b7a8d;background:#f4f5f8}
.kpi-label{font-size:11.5px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.kpi-value{font-size:28px;font-weight:800;color:var(--navy);letter-spacing:-1px}

.charts-row{display:grid;grid-template-columns:1fr 320px;gap:16px;margin-bottom:24px}
@media(max-width:900px){.charts-row{grid-template-columns:1fr}}
.chart-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:22px 24px;box-shadow:var(--shadow);
}
.card-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px}
.card-title{font-size:15px;font-weight:700;color:var(--navy)}
.card-sub{font-size:12.5px;color:var(--muted);margin-bottom:16px}
.legend{display:flex;align-items:center;gap:14px}
.leg-item{display:flex;align-items:center;gap:5px;font-size:12px;font-weight:500;color:var(--muted)}
.leg-dot{width:8px;height:8px;border-radius:50%}

.line-chart-svg{width:100%;height:240px;display:block;overflow:visible}

.donut-wrap{display:flex;justify-content:center;margin:8px 0 20px;position:relative}
canvas#donutChart{max-width:180px;max-height:180px}
.donut-center{
    position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    text-align:center;pointer-events:none;
}
.donut-val{font-size:22px;font-weight:800;color:var(--navy)}
.donut-lbl{font-size:10px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted)}
.dist-list{display:flex;flex-direction:column;gap:10px;margin-top:4px}
.dist-row{display:flex;align-items:center;justify-content:space-between}
.dist-left{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text)}
.dist-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.dist-pct{font-size:13px;font-weight:600;color:var(--navy)}

.team-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:22px 24px;box-shadow:var(--shadow);
}
.team-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px}
.view-all{font-size:13px;font-weight:500;color:var(--blue-act);text-decoration:none;display:flex;align-items:center;gap:6px}
.view-all:hover{text-decoration:underline}
table{width:100%;border-collapse:collapse;margin-top:16px}
thead th{
    font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
    color:var(--muted);padding:0 12px 12px;text-align:left;border-bottom:1px solid var(--border);
}
tbody tr{border-bottom:1px solid #f0f2f5;transition:background .15s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:#fafbfd}
td{padding:14px 12px;vertical-align:middle}
.agent-cell{display:flex;align-items:center;gap:12px}
.agent-av{
    width:38px;height:38px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:12px;font-weight:700;flex-shrink:0;
}
.agent-name{font-size:13.5px;font-weight:600;color:var(--navy)}
.agent-role{font-size:11.5px;color:var(--muted);margin-top:1px}
.load-bar-wrap{width:100px;height:6px;background:#eef0f4;border-radius:10px;overflow:hidden}
.load-bar{height:100%;border-radius:10px;background:var(--navy2)}
.csat-val{font-size:13.5px;font-weight:600;color:var(--green)}
.status-badge{
    display:inline-flex;align-items:center;gap:5px;
    font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
}
.dot-online{width:7px;height:7px;border-radius:50%;background:var(--green)}
.dot-away  {width:7px;height:7px;border-radius:50%;background:var(--orange)}
.online-txt{color:var(--green)}
.away-txt  {color:var(--orange)}

/* ── SECTION DIVIDER ── */
.section-divider{display:flex;align-items:center;gap:14px;margin:36px 0 20px}
.section-divider-line{flex:1;height:1px;background:var(--border)}
.section-divider-label{font-family:var(--mont);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--muted);white-space:nowrap;padding:0 4px}

/* ── REPORT SUMMARY PANEL ── */
.report-summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px}
@media(max-width:900px){.report-summary-grid{grid-template-columns:1fr}}
.report-summary-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:22px;box-shadow:var(--shadow);
    font-family:var(--mont);
}
.rsc-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.rsc-title{font-size:13px;font-weight:700;letter-spacing:.4px;color:var(--navy)}
.rsc-menu-btn{width:28px;height:28px;border:none;background:transparent;cursor:pointer;border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:background .15s}
.rsc-menu-btn:hover{background:#f0f2f5}
.rsc-metric-row{display:flex;flex-direction:column;gap:14px}
.rsc-metric{display:flex;align-items:center;justify-content:space-between}
.rsc-metric-label{font-size:11.5px;font-weight:500;color:var(--muted);letter-spacing:.3px}
.rsc-metric-value{font-size:13px;font-weight:700;color:var(--navy)}
.rsc-bar-track{width:100%;height:5px;background:#eef0f4;border-radius:10px;margin-top:6px;overflow:hidden}
.rsc-bar-fill{height:100%;border-radius:10px;transition:width .6s ease}
.rsc-divider{height:1px;background:var(--border);margin:14px 0}
.rsc-footer{display:flex;align-items:center;gap:6px}
.rsc-footer-text{font-size:11px;font-weight:500;color:var(--muted)}
.rsc-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px 0 14px;gap:8px}
.rsc-empty-icon{width:40px;height:40px;border-radius:50%;background:#f4f5f8;display:flex;align-items:center;justify-content:center;color:#b0b9c6}
.rsc-empty-text{font-size:12px;font-weight:500;color:#b0b9c6;letter-spacing:.3px}

/* ── TRENDS DETAIL SECTION ── */
.trends-grid{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:24px}
@media(max-width:900px){.trends-grid{grid-template-columns:1fr}}
.trends-card{background:var(--white);border:1px solid var(--border);border-radius:14px;padding:22px 24px;box-shadow:var(--shadow);font-family:var(--mont)}
.trends-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:4px}
.trends-title{font-size:14px;font-weight:700;letter-spacing:.3px;color:var(--navy)}
.trends-sub{font-size:11.5px;color:var(--muted);margin-bottom:18px;font-weight:500}
.trends-tab-row{display:flex;align-items:center;gap:4px;margin-bottom:18px;background:#f4f5f8;border-radius:8px;padding:3px}
.trends-tab{font-family:var(--mont);font-size:11px;font-weight:600;letter-spacing:.5px;padding:6px 12px;border-radius:6px;border:none;background:transparent;cursor:pointer;color:var(--muted);transition:all .15s}
.trends-tab.active{background:var(--white);color:var(--navy2);box-shadow:0 1px 3px rgba(0,0,0,.08)}
.trend-chart-area{height:200px;position:relative;display:flex;align-items:flex-end}
.trend-empty-bars{display:flex;align-items:flex-end;gap:6px;width:100%;height:100%}
.trend-bar-group{display:flex;flex-direction:column;align-items:center;gap:4px;flex:1}
.trend-bar-label{font-family:var(--mont);font-size:9.5px;font-weight:600;color:#b0b9c6;letter-spacing:.5px;text-transform:uppercase}
.trend-bar-outer{flex:1;width:100%;background:#eef0f4;border-radius:6px 6px 0 0;display:flex;flex-direction:column;justify-content:flex-end;overflow:hidden;min-height:8px}
.trend-bar-inner{width:100%;border-radius:6px 6px 0 0;transition:height .5s ease}
.trend-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:16px;padding-top:14px;border-top:1px solid var(--border)}
.trend-stat{display:flex;flex-direction:column;gap:3px}
.trend-stat-label{font-family:var(--mont);font-size:10px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted)}
.trend-stat-value{font-family:var(--mont);font-size:18px;font-weight:800;color:var(--navy);letter-spacing:-.5px}
.trend-stat-delta{font-family:var(--mont);font-size:10.5px;font-weight:600}
.delta-up{color:var(--green)}
.delta-down{color:var(--red)}
.delta-neutral{color:var(--muted)}

/* ── TREND MINI CARDS ── */
.trend-mini-stack{display:flex;flex-direction:column;gap:16px}
.trend-mini-card{background:var(--white);border:1px solid var(--border);border-radius:14px;padding:18px 20px;box-shadow:var(--shadow);font-family:var(--mont)}
.tmc-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.tmc-title{font-size:12px;font-weight:700;letter-spacing:.5px;color:var(--navy);text-transform:uppercase}
.tmc-badge{font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;font-family:var(--mont)}
.tmc-body{display:flex;flex-direction:column;gap:10px}
.tmc-row{display:flex;align-items:center;justify-content:space-between;gap:8px}
.tmc-row-label{font-size:11.5px;font-weight:500;color:var(--muted);flex:1}
.tmc-row-bar{flex:1.5;height:4px;background:#eef0f4;border-radius:10px;overflow:hidden}
.tmc-row-bar-fill{height:100%;border-radius:10px}
.tmc-row-val{font-size:12px;font-weight:700;color:var(--navy);min-width:30px;text-align:right}

/* ── ISSUE DISTRIBUTION DETAIL ── */
.issue-detail-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px}
@media(max-width:1000px){.issue-detail-grid{grid-template-columns:1fr 1fr}}
@media(max-width:700px){.issue-detail-grid{grid-template-columns:1fr}}
.issue-detail-card{background:var(--white);border:1px solid var(--border);border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);font-family:var(--mont)}
.idc-header{display:flex;align-items:flex-start;gap:12px;margin-bottom:16px}
.idc-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.idc-titles{flex:1}
.idc-title{font-size:13px;font-weight:700;letter-spacing:.3px;color:var(--navy)}
.idc-sub{font-size:11px;font-weight:500;color:var(--muted);margin-top:2px}
.idc-count{font-family:var(--mont);font-size:26px;font-weight:800;color:var(--navy);letter-spacing:-1px;margin-bottom:12px}
.idc-count-sub{font-size:11px;font-weight:500;color:var(--muted);margin-left:2px;letter-spacing:0}
.idc-progress-stack{display:flex;flex-direction:column;gap:10px}
.idc-progress-item{display:flex;flex-direction:column;gap:4px}
.idc-progress-meta{display:flex;align-items:center;justify-content:space-between}
.idc-progress-label{font-size:11px;font-weight:600;color:var(--muted)}
.idc-progress-val{font-size:11px;font-weight:700;color:var(--navy)}
.idc-progress-bar{width:100%;height:5px;background:#eef0f4;border-radius:10px;overflow:hidden}
.idc-progress-fill{height:100%;border-radius:10px}
.idc-footer{margin-top:14px;padding-top:12px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.idc-footer-label{font-size:11px;font-weight:500;color:var(--muted)}
.idc-footer-action{font-size:11.5px;font-weight:600;color:var(--blue-act);cursor:pointer;text-decoration:none}
.idc-footer-action:hover{text-decoration:underline}

/* ── TEAM PERFORMANCE EXTENDED ── */
.team-ext-grid{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:24px}
@media(max-width:900px){.team-ext-grid{grid-template-columns:1fr}}
.team-ext-card{background:var(--white);border:1px solid var(--border);border-radius:14px;padding:22px 24px;box-shadow:var(--shadow);font-family:var(--mont)}
.tec-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:4px}
.tec-title{font-size:14px;font-weight:700;letter-spacing:.3px;color:var(--navy)}
.tec-sub{font-size:11.5px;color:var(--muted);margin-bottom:18px;font-weight:500}
.tec-actions{display:flex;align-items:center;gap:8px}
.tec-filter-btn{font-family:var(--mont);display:flex;align-items:center;gap:6px;padding:7px 12px;background:var(--white);border:1px solid var(--border);border-radius:7px;font-size:11.5px;font-weight:600;color:var(--text);cursor:pointer;transition:background .15s;letter-spacing:.3px}
.tec-filter-btn:hover{background:#f5f6fa}
.tec-filter-btn.active{background:#eff4ff;border-color:#bfdbfe;color:var(--blue-act)}

.agent-perf-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
.agent-perf-card{border:1px solid var(--border);border-radius:12px;padding:16px;transition:box-shadow .15s;cursor:pointer;position:relative;overflow:hidden}
.agent-perf-card:hover{box-shadow:var(--shadow-md)}
.agent-perf-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0}
.agent-perf-card.status-online::before{background:var(--green)}
.agent-perf-card.status-away::before{background:var(--orange)}
.agent-perf-card.status-offline::before{background:#cbd5e1}
.apc-top{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.apc-av{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;font-family:var(--mont)}
.apc-info{flex:1;min-width:0}
.apc-name{font-size:12.5px;font-weight:700;color:var(--navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.apc-role{font-size:10.5px;font-weight:500;color:var(--muted);margin-top:1px}
.apc-status{font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:2px 7px;border-radius:20px}
.apc-status.online{color:var(--green);background:#f0fdf4}
.apc-status.away{color:var(--orange);background:#fffbeb}
.apc-status.offline{color:var(--muted);background:#f4f5f8}
.apc-metrics{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.apc-metric{display:flex;flex-direction:column;gap:2px}
.apc-metric-label{font-size:9.5px;font-weight:600;letter-spacing:.7px;text-transform:uppercase;color:var(--muted)}
.apc-metric-value{font-size:14px;font-weight:800;color:var(--navy);letter-spacing:-.3px}
.apc-csat{color:var(--green)}
.apc-load-row{margin-top:10px}
.apc-load-label{display:flex;justify-content:space-between;margin-bottom:5px}
.apc-load-text{font-size:10px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--muted)}
.apc-load-pct{font-size:10px;font-weight:700;color:var(--navy2)}
.apc-load-bar{width:100%;height:5px;background:#eef0f4;border-radius:10px;overflow:hidden}
.apc-load-fill{height:100%;border-radius:10px;background:var(--navy2);transition:width .5s}
.apc-empty-card{border:1px dashed var(--border);border-radius:12px;padding:16px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;min-height:140px;cursor:pointer;transition:background .15s}
.apc-empty-card:hover{background:#fafbfd}
.apc-empty-plus{width:32px;height:32px;border-radius:50%;background:#f4f5f8;display:flex;align-items:center;justify-content:center;color:#b0b9c6;font-size:18px}
.apc-empty-text{font-family:var(--mont);font-size:11px;font-weight:600;color:#b0b9c6;letter-spacing:.4px}

/* ── LEADERBOARD ── */
.leaderboard-card{background:var(--white);border:1px solid var(--border);border-radius:14px;padding:22px 24px;box-shadow:var(--shadow);font-family:var(--mont)}
.lb-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.lb-title{font-size:14px;font-weight:700;letter-spacing:.3px;color:var(--navy)}
.lb-period{font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px}
.lb-list{display:flex;flex-direction:column;gap:12px}
.lb-item{display:flex;align-items:center;gap:10px}
.lb-rank{font-size:14px;font-weight:800;color:var(--muted);width:18px;text-align:center;flex-shrink:0}
.lb-rank.rank-1{color:#f59e0b}
.lb-rank.rank-2{color:#94a3b8}
.lb-rank.rank-3{color:#b45309}
.lb-av{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0}
.lb-info{flex:1;min-width:0}
.lb-name{font-size:12px;font-weight:700;color:var(--navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lb-score-row{display:flex;align-items:center;gap:6px;margin-top:4px}
.lb-score-bar{flex:1;height:4px;background:#eef0f4;border-radius:10px;overflow:hidden}
.lb-score-fill{height:100%;border-radius:10px;background:linear-gradient(90deg,var(--navy2),#3b6ea8)}
.lb-score-val{font-size:11px;font-weight:700;color:var(--navy2);min-width:28px;text-align:right}

/* ── ACTIVITY FEED ── */
.activity-card{background:var(--white);border:1px solid var(--border);border-radius:14px;padding:22px 24px;box-shadow:var(--shadow);font-family:var(--mont)}
.ac-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.ac-title{font-size:14px;font-weight:700;letter-spacing:.3px;color:var(--navy)}
.ac-live-dot{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:var(--green)}
.ac-live-dot::before{content:'';width:7px;height:7px;border-radius:50%;background:var(--green);box-shadow:0 0 0 2px rgba(22,163,74,.2)}
.ac-feed{display:flex;flex-direction:column;gap:0}
.ac-item{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid #f0f2f5;position:relative}
.ac-item:last-child{border-bottom:none}
.ac-item-line{position:absolute;left:15px;top:36px;bottom:-1px;width:1px;background:#eef0f4}
.ac-item:last-child .ac-item-line{display:none}
.ac-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid var(--white);box-shadow:0 0 0 1px var(--border)}
.ac-body{flex:1;min-width:0;padding-top:4px}
.ac-body-title{font-size:12px;font-weight:600;color:var(--navy);line-height:1.4}
.ac-body-meta{display:flex;align-items:center;gap:8px;margin-top:4px}
.ac-body-time{font-size:10.5px;font-weight:500;color:var(--muted)}
.ac-body-tag{font-size:10px;font-weight:700;letter-spacing:.5px;padding:2px 6px;border-radius:4px}
.ac-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding:32px 0;color:#b0b9c6}
.ac-empty-icon{font-size:32px;opacity:.3}
.ac-empty-text{font-size:12px;font-weight:600;letter-spacing:.5px;text-align:center;line-height:1.6}

/* ── DATA TABLE SECTION ── */
.data-table-card{background:var(--white);border:1px solid var(--border);border-radius:14px;padding:22px 24px;box-shadow:var(--shadow);margin-bottom:24px;font-family:var(--mont)}
.dtc-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px}
.dtc-left{display:flex;flex-direction:column;gap:3px}
.dtc-title{font-size:14px;font-weight:700;letter-spacing:.3px;color:var(--navy)}
.dtc-sub{font-size:11.5px;font-weight:500;color:var(--muted)}
.dtc-controls{display:flex;align-items:center;gap:8px}
.dtc-search{display:flex;align-items:center;gap:8px;background:#f4f5f8;border:1px solid var(--border);border-radius:8px;padding:0 10px;height:32px;width:180px}
.dtc-search input{border:none;background:transparent;outline:none;font-size:12px;color:var(--text);width:100%;font-family:var(--mont)}
.dtc-search input::placeholder{color:#a0aab4}
.dtc-btn{display:flex;align-items:center;gap:6px;padding:7px 12px;background:var(--white);border:1px solid var(--border);border-radius:7px;font-family:var(--mont);font-size:11.5px;font-weight:600;color:var(--text);cursor:pointer;transition:background .15s;letter-spacing:.3px}
.dtc-btn:hover{background:#f5f6fa}
.dtc-btn-primary{background:var(--navy2);color:#fff;border-color:var(--navy2)}
.dtc-btn-primary:hover{background:#162234}
.dt-table{width:100%;border-collapse:collapse}
.dt-table thead th{font-family:var(--mont);font-size:10px;font-weight:700;letter-spacing:.9px;text-transform:uppercase;color:var(--muted);padding:0 14px 12px;text-align:left;border-bottom:1px solid var(--border)}
.dt-table tbody tr{border-bottom:1px solid #f0f2f5;transition:background .15s;cursor:pointer}
.dt-table tbody tr:last-child{border-bottom:none}
.dt-table tbody tr:hover{background:#fafbfd}
.dt-table td{padding:13px 14px;vertical-align:middle;font-family:var(--mont);font-size:12px;color:var(--text)}
.dt-ticket-id{font-size:11.5px;font-weight:700;color:var(--blue-act);letter-spacing:.4px}
.dt-priority-badge{font-size:10px;font-weight:700;letter-spacing:.6px;padding:3px 8px;border-radius:5px;font-family:var(--mont)}
.dt-priority-high{color:#dc2626;background:#fef2f2}
.dt-priority-medium{color:#d97706;background:#fffbeb}
.dt-priority-low{color:#16a34a;background:#f0fdf4}
.dt-status-badge{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;letter-spacing:.6px;font-family:var(--mont)}
.dt-status-open{color:#2563eb}
.dt-status-progress{color:#d97706}
.dt-status-resolved{color:#16a34a}
.dt-status-closed{color:var(--muted)}
.dt-empty-row td{padding:40px 14px;text-align:center}
.dt-empty-inner{display:flex;flex-direction:column;align-items:center;gap:10px;color:#b0b9c6}
.dt-empty-inner svg{opacity:.3}
.dt-empty-inner span{font-family:var(--mont);font-size:12px;font-weight:600;letter-spacing:.5px}
.dt-pagination{display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:14px;border-top:1px solid var(--border)}
.dt-page-info{font-family:var(--mont);font-size:11.5px;font-weight:500;color:var(--muted)}
.dt-page-btns{display:flex;align-items:center;gap:4px}
.dt-page-btn{width:30px;height:30px;border-radius:6px;border:1px solid var(--border);background:var(--white);display:flex;align-items:center;justify-content:center;cursor:pointer;font-family:var(--mont);font-size:12px;font-weight:600;color:var(--muted);transition:all .15s}
.dt-page-btn:hover{background:#f0f2f5;color:var(--navy)}
.dt-page-btn.active{background:var(--navy2);color:#fff;border-color:var(--navy2)}

.fab{
    position:fixed;bottom:32px;right:32px;
    width:52px;height:52px;border-radius:50%;
    background:var(--navy2);color:#fff;border:none;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;box-shadow:0 4px 16px rgba(30,58,110,.30);
    font-size:24px;transition:background .15s,transform .1s;z-index:300;
}
.fab:hover{background:#162234}
.fab:active{transform:scale(.95)}

[data-tooltip]{position:relative}
[data-tooltip]:hover::after{content:attr(data-tooltip);position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:var(--navy);color:#fff;font-family:var(--mont);font-size:10.5px;font-weight:600;letter-spacing:.3px;padding:5px 10px;border-radius:6px;white-space:nowrap;z-index:999;pointer-events:none;box-shadow:0 2px 8px rgba(0,0,0,.18)}
</style>
</head>
<body>

<header class="tnav">
    <div class="tnav-brand">Helpdesk Analytics</div>
    <nav class="tnav-links">
        <a href="#" class="tlink">Dashboards</a>
        <a href="#" class="tlink active">Reports</a>
        <a href="#" class="tlink">SLA Status</a>
        <a href="#" class="tlink">Team Performance</a>
    </nav>
    <div class="tnav-right">
        <div class="tnav-search">
            <svg width="14" height="14" fill="none" stroke="#a0aab4" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search reports...">
        </div>
        <button class="icon-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </button>
        <button class="icon-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        </button>
        <div class="avatar">
            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
    </div>
</header>

<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-icon">
            <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </div>
        <div>
            <div class="sb-label">Global Metrics</div>
            <div class="sb-sub">Q3 Performance</div>
        </div>
    </div>
    <nav style="margin-top:8px">
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Overview
        </a>
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Ticket Volume
        </a>
        <a href="#" class="nav-item active">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Resolution Rate
        </a>
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Customer CSAT
        </a>
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Agent Heatmap
        </a>
    </nav>
    <div class="sb-footer">
        <button class="btn-export">Export Report</button>
        <div class="sb-links">
            <a href="#" class="sb-link">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Help Center
            </a>
            <a href="#" class="sb-link">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><polyline points="14 2 14 8 20 8"/></svg>
                Documentation
            </a>
        </div>
    </div>
</aside>

<main class="main">

    <!-- PAGE HEADER -->
    <div class="page-head">
        <div>
            <h1>Reports &amp; Analytics</h1>
            <p>Real-time performance overview for Q3 period.</p>
        </div>
        <div class="head-controls">
            <button class="ctrl-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Last 30 Days
            </button>
            <button class="ctrl-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
                All Systems
            </button>
            <button class="btn-apply">Apply</button>
        </div>
    </div>

    <!-- KPI GRID -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#eff4ff">
                    <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                </div>
                <span class="kpi-badge badge-up">+12.5%</span>
            </div>
            <div class="kpi-label">Total Tickets</div>
            <div class="kpi-value">1,284</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#f0f9ff">
                    <svg width="20" height="20" fill="none" stroke="#0284c7" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="kpi-badge badge-down">-4.2%</span>
            </div>
            <div class="kpi-label">Avg. Resolution</div>
            <div class="kpi-value">4h 12m</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#fffbeb">
                    <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <span class="kpi-badge badge-target">↑ Target</span>
            </div>
            <div class="kpi-label">SLA Compliance</div>
            <div class="kpi-value">94.8%</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#f0fdf4">
                    <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <span class="kpi-badge badge-neutral">+0.4</span>
            </div>
            <div class="kpi-label">Customer CSAT</div>
            <div class="kpi-value">4.8/5</div>
        </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="charts-row">
        <div class="chart-card">
            <div class="card-head">
                <div>
                    <div class="card-title">Ticket Volume Trends</div>
                    <div class="card-sub">Active resolution spikes over the last 30 days</div>
                </div>
                <div class="legend">
                    <div class="leg-item"><div class="leg-dot" style="background:#1e3a6e"></div>NEW</div>
                    <div class="leg-item"><div class="leg-dot" style="background:#93c5fd"></div>CLOSED</div>
                </div>
            </div>
            <svg class="line-chart-svg" viewBox="0 0 520 220" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="fillGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%"   stop-color="#1e3a6e" stop-opacity="0.12"/>
                        <stop offset="100%" stop-color="#1e3a6e" stop-opacity="0.01"/>
                    </linearGradient>
                    <clipPath id="chartClip">
                        <rect x="0" y="0" width="520" height="215"/>
                    </clipPath>
                </defs>
                <line x1="0" y1="10"  x2="520" y2="10"  stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="60"  x2="520" y2="60"  stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="110" x2="520" y2="110" stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="160" x2="520" y2="160" stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="210" x2="520" y2="210" stroke="#f0f2f5" stroke-width="1"/>
                <text x="2" y="14"  font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">80</text>
                <text x="2" y="64"  font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">60</text>
                <text x="2" y="114" font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">40</text>
                <text x="2" y="164" font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">20</text>
                <g clip-path="url(#chartClip)">
                    <path fill="url(#fillGrad)" d="M0,210 C6,204 12,200 17.9,197 C23,194 30,210 35.9,207 C41,204 48,165 53.8,155 C59,146 66,186 71.7,177 C77,168 84,142 89.7,132 C95,122 102,153 107.6,145 C113,137 120,196 125.5,187 C131,178 137,181 143.4,171 C149,161 155,133 161.4,123 C167,113 173,109 179.3,100 C185,91 191,147 197.2,139 C203,131 209,153 215.2,145 C221,137 227,97 233.1,87 C239,77 245,119 251.0,110 C257,101 263,75 269.0,65 C275,55 281,87 286.9,78 C292,69 298,141 304.8,132 C310,123 317,116 322.8,106 C328,96 334,68 340.7,58 C346,48 352,45 358.6,35 C364,25 370,57 376.6,48 C382,39 388,96 394.5,87 C400,78 406,81 412.4,71 C418,61 424,33 430.3,23 C436,13 442,20 448.3,10 C454,0 460,51 466.2,42 C472,33 478,67 484.1,58 C490,49 496,87 502.1,78 C508,69 514,109 520,100 L520,215 L0,215 Z"/>
                    <path fill="none" stroke="#1e3a6e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M0,210 C6,204 12,200 17.9,197 C23,194 30,210 35.9,207 C41,204 48,165 53.8,155 C59,146 66,186 71.7,177 C77,168 84,142 89.7,132 C95,122 102,153 107.6,145 C113,137 120,196 125.5,187 C131,178 137,181 143.4,171 C149,161 155,133 161.4,123 C167,113 173,109 179.3,100 C185,91 191,147 197.2,139 C203,131 209,153 215.2,145 C221,137 227,97 233.1,87 C239,77 245,119 251.0,110 C257,101 263,75 269.0,65 C275,55 281,87 286.9,78 C292,69 298,141 304.8,132 C310,123 317,116 322.8,106 C328,96 334,68 340.7,58 C346,48 352,45 358.6,35 C364,25 370,57 376.6,48 C382,39 388,96 394.5,87 C400,78 406,81 412.4,71 C418,61 424,33 430.3,23 C436,13 442,20 448.3,10 C454,0 460,51 466.2,42 C472,33 478,67 484.1,58 C490,49 496,87 502.1,78 C508,69 514,109 520,100"/>
                    <path fill="none" stroke="#93c5fd" stroke-width="1.8" stroke-dasharray="4 4" stroke-linecap="round" stroke-linejoin="round" d="M0,210 C6,207 12,204 17.9,200 C23,196 30,193 35.9,190 C41,187 48,181 53.8,177 C59,173 66,170 71.7,167 C77,163 84,156 89.7,150 C95,144 102,160 107.6,157 C113,153 120,147 125.5,143 C131,139 137,136 143.4,133 C149,129 155,122 161.4,117 C167,112 173,126 179.3,123 C185,119 191,113 197.2,110 C203,107 209,103 215.2,100 C221,97 227,126 233.1,123 C239,119 245,106 251.0,103 C257,99 263,83 269.0,77 C275,70 281,97 286.9,90 C292,83 298,70 304.8,63 C310,56 317,89 322.8,83 C328,77 334,63 340.7,57 C346,51 352,46 358.6,43 C364,40 370,54 376.6,50 C382,46 388,73 394.5,67 C400,60 406,36 412.4,33 C418,27 424,26 430.3,23 C436,19 442,14 448.3,10 C454,6 460,39 466.2,33 C472,27 478,21 484.1,17 C490,13 496,50 502.1,43 C508,37 514,50 520,57"/>
                    <circle cx="358.6" cy="35" r="4.5" fill="white" stroke="#1e3a6e" stroke-width="2"/>
                    <text x="358.6" y="23" font-family="Inter,sans-serif" font-size="10.5" font-weight="600" fill="#1e3a6e" text-anchor="middle">Peak:42</text>
                </g>
            </svg>
            <div style="display:flex;justify-content:space-between;padding-top:10px">
                <span style="font-size:11.5px;color:var(--muted)">SEP 01</span>
                <span style="font-size:11.5px;color:var(--muted)">SEP 10</span>
                <span style="font-size:11.5px;color:var(--muted)">SEP 20</span>
                <span style="font-size:11.5px;color:var(--muted)">SEP 30</span>
            </div>
        </div>
        <div class="chart-card">
            <div class="card-title">Issue Distribution</div>
            <div class="card-sub">Resolution by infrastructure type</div>
            <div class="donut-wrap">
                <canvas id="donutChart" width="180" height="180"></canvas>
                <div class="donut-center">
                    <div class="donut-val">1.2k</div>
                    <div class="donut-lbl">Tickets</div>
                </div>
            </div>
            <div class="dist-list">
                <div class="dist-row">
                    <div class="dist-left"><div class="dist-dot" style="background:#1e3a6e"></div>Backend Infrastructure</div>
                    <div class="dist-pct">45%</div>
                </div>
                <div class="dist-row">
                    <div class="dist-left"><div class="dist-dot" style="background:#3b6ea8"></div>Frontend / UI Issues</div>
                    <div class="dist-pct">30%</div>
                </div>
                <div class="dist-row">
                    <div class="dist-left"><div class="dist-dot" style="background:#bfdbfe"></div>API Integrations</div>
                    <div class="dist-pct">25%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TEAM PERFORMANCE (original table) -->
    <div class="team-card">
        <div class="team-head">
            <div>
                <div class="card-title">Team Performance</div>
                <div class="card-sub" style="margin-bottom:0">Live metrics for active support agents</div>
            </div>
            <div style="display:flex;align-items:center;gap:12px">
                <a href="#" class="view-all">View All Agents</a>
                <button class="icon-btn" title="Export">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </button>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Agent Name</th>
                    <th>Resolved</th>
                    <th>Avg Response</th>
                    <th>Load</th>
                    <th>CSAT</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($agents as $a)
                <tr>
                    <td>
                        <div class="agent-cell">
                            <div class="agent-av" style="background:{{ $a['color'] }}">{{ $a['initials'] }}</div>
                            <div>
                                <div class="agent-name">{{ $a['name'] }}</div>
                                <div class="agent-role">{{ $a['role'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:14px;font-weight:600;color:var(--navy)">{{ $a['resolved'] }}</td>
                    <td style="font-size:13.5px;color:var(--text)">{{ $a['avg_response'] }}</td>
                    <td>
                        <div class="load-bar-wrap">
                            <div class="load-bar" style="width:{{ $a['load'] }}%"></div>
                        </div>
                    </td>
                    <td><span class="csat-val">{{ $a['csat'] }}</span></td>
                    <td>
                        @if (($a['status'] ?? '') === 'online')
                            <span class="status-badge"><span class="dot-online"></span><span class="online-txt">ONLINE</span></span>
                        @else
                            <span class="status-badge"><span class="dot-away"></span><span class="away-txt">AWAY</span></span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ═══════════ EXTENDED SECTIONS ═══════════ -->

    <!-- SECTION: REPORT & ANALYTICS DETAIL -->
    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">Report &amp; Analytics</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="report-summary-grid">
        <!-- Resolution Summary -->
        <div class="report-summary-card">
            <div class="rsc-header">
                <div class="rsc-title">Resolution Summary</div>
                <button class="rsc-menu-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                </button>
            </div>
            <div class="rsc-empty-state">
                <div class="rsc-empty-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="rsc-empty-text">Awaiting resolution data</div>
            </div>
            <div class="rsc-metric-row">
                <div class="rsc-metric">
                    <div class="rsc-metric-label">1st Contact Resolution</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#1e3a6e"></div></div>
                <div class="rsc-divider"></div>
                <div class="rsc-metric">
                    <div class="rsc-metric-label">Escalation Rate</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#ef4444"></div></div>
                <div class="rsc-divider"></div>
                <div class="rsc-metric">
                    <div class="rsc-metric-label">Reopened Tickets</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#f59e0b"></div></div>
            </div>
            <div class="rsc-footer" style="margin-top:16px">
                <div class="rsc-empty-icon" style="width:20px;height:20px"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                <span class="rsc-footer-text">Data syncs every 15 minutes from database</span>
            </div>
        </div>

        <!-- SLA Breakdown -->
        <div class="report-summary-card">
            <div class="rsc-header">
                <div class="rsc-title">SLA Breakdown</div>
                <button class="rsc-menu-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                </button>
            </div>
            <div class="rsc-empty-state">
                <div class="rsc-empty-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="rsc-empty-text">No SLA data connected</div>
            </div>
            <div class="rsc-metric-row">
                <div class="rsc-metric">
                    <div class="rsc-metric-label">Within SLA</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#16a34a"></div></div>
                <div class="rsc-divider"></div>
                <div class="rsc-metric">
                    <div class="rsc-metric-label">Breached SLA</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#ef4444"></div></div>
                <div class="rsc-divider"></div>
                <div class="rsc-metric">
                    <div class="rsc-metric-label">At Risk</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#f59e0b"></div></div>
            </div>
            <div class="rsc-footer" style="margin-top:16px">
                <div class="rsc-empty-icon" style="width:20px;height:20px"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                <span class="rsc-footer-text">Pulled from SLA policy engine</span>
            </div>
        </div>

        <!-- Channel Breakdown -->
        <div class="report-summary-card">
            <div class="rsc-header">
                <div class="rsc-title">Channel Breakdown</div>
                <button class="rsc-menu-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                </button>
            </div>
            <div class="rsc-empty-state">
                <div class="rsc-empty-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="rsc-empty-text">No channel data yet</div>
            </div>
            <div class="rsc-metric-row">
                <div class="rsc-metric">
                    <div class="rsc-metric-label">Email</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#1e3a6e"></div></div>
                <div class="rsc-divider"></div>
                <div class="rsc-metric">
                    <div class="rsc-metric-label">Live Chat</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#3b6ea8"></div></div>
                <div class="rsc-divider"></div>
                <div class="rsc-metric">
                    <div class="rsc-metric-label">Phone / Other</div>
                    <div class="rsc-metric-value">—</div>
                </div>
                <div class="rsc-bar-track"><div class="rsc-bar-fill" style="width:0%;background:#bfdbfe"></div></div>
            </div>
            <div class="rsc-footer" style="margin-top:16px">
                <div class="rsc-empty-icon" style="width:20px;height:20px"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                <span class="rsc-footer-text">Aggregated from all support channels</span>
            </div>
        </div>
    </div>

    <!-- SECTION: TRENDS -->
    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">Trends</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="trends-grid">
        <div class="trends-card">
            <div class="trends-header">
                <div>
                    <div class="trends-title">Volume &amp; Resolution Trends</div>
                </div>
                <div style="display:flex;gap:8px">
                    <button class="tec-filter-btn">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Custom Range
                    </button>
                    <button class="icon-btn">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </button>
                </div>
            </div>
            <div class="trends-sub">Ticket volume by week · Awaiting database connection</div>
            <div class="trends-tab-row">
                <button class="trends-tab active">Weekly</button>
                <button class="trends-tab">Monthly</button>
                <button class="trends-tab">Quarterly</button>
                <button class="trends-tab">YTD</button>
            </div>
            <div class="trend-chart-area">
                <div class="trend-empty-bars" id="trendBars"></div>
            </div>
            <div class="trend-stat-row">
                <div class="trend-stat">
                    <div class="trend-stat-label">Total Volume</div>
                    <div class="trend-stat-value">0</div>
                    <div class="trend-stat-delta delta-neutral">No data</div>
                </div>
                <div class="trend-stat">
                    <div class="trend-stat-label">Resolved</div>
                    <div class="trend-stat-value">0</div>
                    <div class="trend-stat-delta delta-neutral">No data</div>
                </div>
                <div class="trend-stat">
                    <div class="trend-stat-label">Pending</div>
                    <div class="trend-stat-value">0</div>
                    <div class="trend-stat-delta delta-neutral">No data</div>
                </div>
            </div>
        </div>

        <div class="trend-mini-stack">
            <div class="trend-mini-card">
                <div class="tmc-header">
                    <div class="tmc-title">Priority Split</div>
                    <span class="tmc-badge badge-neutral">No Data</span>
                </div>
                <div class="tmc-body">
                    <div class="tmc-row">
                        <div class="tmc-row-label">Critical</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#ef4444;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                    <div class="tmc-row">
                        <div class="tmc-row-label">High</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#f59e0b;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                    <div class="tmc-row">
                        <div class="tmc-row-label">Medium</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#3b6ea8;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                    <div class="tmc-row">
                        <div class="tmc-row-label">Low</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#bfdbfe;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                </div>
            </div>
            <div class="trend-mini-card">
                <div class="tmc-header">
                    <div class="tmc-title">Response Time</div>
                    <span class="tmc-badge badge-neutral">No Data</span>
                </div>
                <div class="tmc-body">
                    <div class="tmc-row">
                        <div class="tmc-row-label">&lt; 1 hour</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#16a34a;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                    <div class="tmc-row">
                        <div class="tmc-row-label">1 – 4 hours</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#1e3a6e;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                    <div class="tmc-row">
                        <div class="tmc-row-label">4 – 24 hours</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#f59e0b;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                    <div class="tmc-row">
                        <div class="tmc-row-label">&gt; 24 hours</div>
                        <div class="tmc-row-bar"><div class="tmc-row-bar-fill" style="width:0%;height:100%;background:#ef4444;border-radius:10px"></div></div>
                        <div class="tmc-row-val">—</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION: ISSUE DISTRIBUTION DETAIL -->
    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">Issue Distribution</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="issue-detail-grid">
        <!-- Backend Infrastructure -->
        <div class="issue-detail-card">
            <div class="idc-header">
                <div class="idc-icon" style="background:#eff4ff">
                    <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                </div>
                <div class="idc-titles">
                    <div class="idc-title">Backend Infrastructure</div>
                    <div class="idc-sub">Server, DB &amp; network issues</div>
                </div>
            </div>
            <div class="idc-count">0 <span class="idc-count-sub">tickets</span></div>
            <div class="idc-progress-stack">
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Database Errors</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#1e3a6e"></div></div>
                </div>
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Server Downtime</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#3b6ea8"></div></div>
                </div>
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Network Latency</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#93c5fd"></div></div>
                </div>
            </div>
            <div class="idc-footer">
                <span class="idc-footer-label">Last synced: —</span>
                <a href="#" class="idc-footer-action">View Details</a>
            </div>
        </div>

        <!-- Frontend / UI Issues -->
        <div class="issue-detail-card">
            <div class="idc-header">
                <div class="idc-icon" style="background:#fffbeb">
                    <svg width="18" height="18" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                </div>
                <div class="idc-titles">
                    <div class="idc-title">Frontend / UI Issues</div>
                    <div class="idc-sub">Interface &amp; rendering bugs</div>
                </div>
            </div>
            <div class="idc-count">0 <span class="idc-count-sub">tickets</span></div>
            <div class="idc-progress-stack">
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Rendering Bugs</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#d97706"></div></div>
                </div>
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Layout / CSS</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#f59e0b"></div></div>
                </div>
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">JS Runtime Errors</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#fcd34d"></div></div>
                </div>
            </div>
            <div class="idc-footer">
                <span class="idc-footer-label">Last synced: —</span>
                <a href="#" class="idc-footer-action">View Details</a>
            </div>
        </div>

        <!-- API Integrations -->
        <div class="issue-detail-card">
            <div class="idc-header">
                <div class="idc-icon" style="background:#f0fdf4">
                    <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                </div>
                <div class="idc-titles">
                    <div class="idc-title">API Integrations</div>
                    <div class="idc-sub">Third-party &amp; webhook errors</div>
                </div>
            </div>
            <div class="idc-count">0 <span class="idc-count-sub">tickets</span></div>
            <div class="idc-progress-stack">
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Auth / OAuth</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#16a34a"></div></div>
                </div>
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Rate Limiting</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#4ade80"></div></div>
                </div>
                <div class="idc-progress-item">
                    <div class="idc-progress-meta"><span class="idc-progress-label">Webhook Failures</span><span class="idc-progress-val">—</span></div>
                    <div class="idc-progress-bar"><div class="idc-progress-fill" style="width:0%;background:#86efac"></div></div>
                </div>
            </div>
            <div class="idc-footer">
                <span class="idc-footer-label">Last synced: —</span>
                <a href="#" class="idc-footer-action">View Details</a>
            </div>
        </div>
    </div>

    <!-- SECTION: TEAM PERFORMANCE EXTENDED -->
    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">Team Performance</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="team-ext-grid">
        <div class="team-ext-card">
            <div class="tec-header">
                <div>
                    <div class="tec-title">Agent Performance Cards</div>
                </div>
                <div class="tec-actions">
                    <button class="tec-filter-btn active">All</button>
                    <button class="tec-filter-btn">Online</button>
                    <button class="tec-filter-btn">Away</button>
                    <button class="icon-btn">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </button>
                </div>
            </div>
            <div class="tec-sub">Individual workload &amp; CSAT breakdown · pulled from agent database</div>
            <div class="agent-perf-grid" id="agentPerfGrid">
                <div class="apc-empty-card"><div class="apc-empty-plus">+</div><div class="apc-empty-text">Add Agent</div></div>
                <div class="apc-empty-card"><div class="apc-empty-plus">+</div><div class="apc-empty-text">Add Agent</div></div>
                <div class="apc-empty-card"><div class="apc-empty-plus">+</div><div class="apc-empty-text">Add Agent</div></div>
                <div class="apc-empty-card"><div class="apc-empty-plus">+</div><div class="apc-empty-text">Add Agent</div></div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
            <!-- Leaderboard -->
            <div class="leaderboard-card">
                <div class="lb-header">
                    <div class="lb-title">Top Performers</div>
                    <div class="lb-period">This period</div>
                </div>
                <div class="lb-list">
                    <div class="ac-empty" style="padding:20px 0">
                        <svg width="28" height="28" fill="none" stroke="#b0b9c6" stroke-width="1.5" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <div class="ac-empty-text">Rankings load from<br>database automatically</div>
                    </div>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="activity-card">
                <div class="ac-header">
                    <div class="ac-title">Live Activity</div>
                    <div class="ac-live-dot">Live</div>
                </div>
                <div class="ac-feed">
                    <div class="ac-empty">
                        <svg width="28" height="28" fill="none" stroke="#b0b9c6" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        <div class="ac-empty-text">No recent activity<br>Connect database to stream events</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION: TICKET LOG -->
    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">Ticket Log</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="data-table-card">
        <div class="dtc-header">
            <div class="dtc-left">
                <div class="dtc-title">All Tickets</div>
                <div class="dtc-sub">Full ticket log · sorted by created date · from database</div>
            </div>
            <div class="dtc-controls">
                <div class="dtc-search">
                    <svg width="12" height="12" fill="none" stroke="#a0aab4" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" placeholder="Search tickets...">
                </div>
                <button class="dtc-btn">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
                    Filter
                </button>
                <button class="dtc-btn">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    Sort
                </button>
                <button class="dtc-btn dtc-btn-primary">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </button>
            </div>
        </div>
        <table class="dt-table">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Subject</th>
                    <th>Assigned To</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Resolved</th>
                    <th>CSAT</th>
                </tr>
            </thead>
            <tbody id="ticketTableBody">
                <tr class="dt-empty-row"><td colspan="8">
                    <div class="dt-empty-inner">
                        <svg width="32" height="32" fill="none" stroke="#b0b9c6" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                        <span>No tickets found · awaiting database connection</span>
                    </div>
                </td></tr>
            </tbody>
        </table>
        <div class="dt-pagination">
            <div class="dt-page-info">Showing 0 of 0 tickets</div>
            <div class="dt-page-btns">
                <button class="dt-page-btn">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button class="dt-page-btn active">1</button>
                <button class="dt-page-btn">2</button>
                <button class="dt-page-btn">3</button>
                <button class="dt-page-btn">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
    </div>

</main>

<button class="fab" title="New Ticket">
    <svg width="22" height="22" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
</button>

<script>
// ── Donut chart ──
(function(){
    const ctx = document.getElementById('donutChart').getContext('2d');
    new Chart(ctx,{
        type:'doughnut',
        data:{
            datasets:[{
                data:[45,30,25],
                backgroundColor:['#1e3a6e','#3b6ea8','#bfdbfe'],
                borderWidth:0,
                hoverOffset:6,
            }]
        },
        options:{
            cutout:'72%',
            responsive:false,
            plugins:{
                legend:{display:false},
                tooltip:{
                    backgroundColor:'#0d1b2e',
                    titleColor:'#fff',
                    bodyColor:'#93c5fd',
                    padding:10,
                    cornerRadius:8,
                    callbacks:{label:function(c){return ' '+c.label+': '+c.parsed+'%'}}
                }
            }
        }
    });
})();

// ── Trend empty bars ──
(function(){
    const container = document.getElementById('trendBars');
    const weeks = ['W1','W2','W3','W4','W5','W6','W7','W8'];
    weeks.forEach(w => {
        const g = document.createElement('div');
        g.className = 'trend-bar-group';
        g.innerHTML = `
            <div class="trend-bar-outer" style="height:100%">
                <div class="trend-bar-inner" style="height:0%;background:#eef0f4"></div>
            </div>
            <div class="trend-bar-label">${w}</div>
        `;
        container.appendChild(g);
    });
})();

// ── Trends tab switching ──
document.querySelectorAll('.trends-tab').forEach(btn => {
    btn.addEventListener('click', function(){
        document.querySelectorAll('.trends-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});

// ── Filter buttons (team ext) ──
document.querySelectorAll('.tec-filter-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const siblings = this.parentElement.querySelectorAll('.tec-filter-btn');
        siblings.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
</body>
</html>