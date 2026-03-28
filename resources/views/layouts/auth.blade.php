<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fixtora')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --bg:       #0d1b2e;
        --bg-r:     #0f2040;
        --card:     #132035;
        --blue:     #2563eb;
        --blue-2:   #1d4ed8;
        --border:   rgba(255,255,255,0.08);
        --input-bg: #0a1829;
        --text:     #ffffff;
        --muted:    rgba(255,255,255,0.45);
        --subtle:   rgba(255,255,255,0.15);
    }

    html, body {
        height: 100%;
        font-family: 'Montserrat', sans-serif;
        background: var(--bg);
    }

    body {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    /* ══════════════════════════
       LEFT
    ══════════════════════════ */
    .l-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: var(--bg);
        position: relative;
        overflow: hidden;
        padding: 60px 64px;
    }

    /* grid lines */
    .l-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 52px 52px;
    }

    /* glow */
    .l-panel::after {
        content: '';
        position: absolute;
        bottom: -80px; right: -80px;
        width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(37,99,235,0.15) 0%, transparent 65%);
        pointer-events: none;
    }

    .l-inner {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 460px;
    }

    .l-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 64px;
    }

    .l-logo {
        width: 44px; height: 44px;
        background: var(--blue);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 0 1px rgba(255,255,255,0.1), 0 4px 16px rgba(37,99,235,0.4);
        flex-shrink: 0;
    }

    .l-brand-name {
        font-size: 18px; font-weight: 800;
        color: #fff; letter-spacing: -0.3px;
    }
    .l-brand-sub {
        font-size: 9px; font-weight: 500;
        letter-spacing: 2px; text-transform: uppercase;
        color: var(--subtle); margin-top: 2px;
    }

    .l-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(37,99,235,0.12);
        border: 1px solid rgba(37,99,235,0.25);
        padding: 6px 14px; border-radius: 100px;
        font-size: 10px; font-weight: 700;
        letter-spacing: 1.5px; text-transform: uppercase;
        color: #93c5fd; margin-bottom: 28px;
    }

    .l-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #60a5fa; position: relative; flex-shrink: 0;
    }
    .l-dot::after {
        content: '';
        position: absolute; inset: -3px;
        border-radius: 50%;
        border: 1.5px solid rgba(96,165,250,0.35);
        animation: ripple 1.8s infinite;
    }
    @keyframes ripple {
        0%   { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(1.8); opacity: 0; }
    }

    .l-headline {
        font-size: 52px; font-weight: 900;
        line-height: 1.0; color: #fff;
        letter-spacing: -2.5px; margin-bottom: 20px;
    }
    .l-headline em {
        font-style: normal;
        -webkit-text-stroke: 1.5px rgba(255,255,255,0.35);
    }

    .l-desc {
        font-size: 14px; font-weight: 400;
        line-height: 1.85; color: var(--muted);
        margin-bottom: 48px;
    }

    .l-stats {
        display: flex; gap: 12px;
    }

    .l-stat {
        flex: 1;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px 22px;
    }

    .l-stat-n {
        font-size: 28px; font-weight: 800;
        color: #fff; letter-spacing: -1px; line-height: 1;
    }
    .l-stat-n sup { font-size: 14px; color: #60a5fa; font-weight: 700; }

    .l-stat-l {
        font-size: 10px; font-weight: 600;
        letter-spacing: 1.2px; text-transform: uppercase;
        color: var(--subtle); margin-top: 7px;
    }

    .l-copy {
        position: absolute;
        bottom: 28px; left: 0; right: 0;
        text-align: center;
        font-size: 11px; font-weight: 400;
        color: rgba(255,255,255,0.12);
    }

    /* ══════════════════════════
       RIGHT
    ══════════════════════════ */
    .r-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: var(--bg-r);
        position: relative;
        padding: 60px 64px;
        border-left: 1px solid var(--border);
    }

    .r-inner {
        width: 100%;
        max-width: 420px;
    }

    /* ── FORM ── */
    .f-eyebrow {
        font-size: 10px; font-weight: 700;
        letter-spacing: 2px; text-transform: uppercase;
        color: #60a5fa; margin-bottom: 10px;
        display: flex; align-items: center; gap: 8px;
    }
    .f-eyebrow::before {
        content: '';
        width: 18px; height: 2px;
        background: #60a5fa; border-radius: 2px; flex-shrink: 0;
    }

    .f-title {
        font-size: 32px; font-weight: 800;
        color: #fff; letter-spacing: -1px;
        line-height: 1.15; margin-bottom: 8px;
    }

    .f-sub {
        font-size: 14px; font-weight: 400;
        color: var(--muted); margin-bottom: 36px;
    }

    /* alerts */
    .alert {
        display: flex; align-items: flex-start; gap: 9px;
        padding: 12px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 500;
        margin-bottom: 20px; line-height: 1.5;
        border: 1px solid transparent;
    }
    .alert svg { flex-shrink: 0; margin-top: 1px; }
    .alert-danger  { background: rgba(239,68,68,0.1);  border-color: rgba(239,68,68,0.2);  color: #fca5a5; }
    .alert-success { background: rgba(34,197,94,0.1);  border-color: rgba(34,197,94,0.2);  color: #86efac; }

    /* fields */
    .f-group { margin-bottom: 20px; }

    .f-row {
        display: flex; align-items: center;
        justify-content: space-between; margin-bottom: 8px;
    }

    .f-label {
        display: block;
        font-size: 12px; font-weight: 700;
        letter-spacing: 0.5px; text-transform: uppercase;
        color: rgba(255,255,255,0.6); margin-bottom: 8px;
    }
    .f-label-inline {
        font-size: 12px; font-weight: 700;
        letter-spacing: 0.5px; text-transform: uppercase;
        color: rgba(255,255,255,0.6);
    }

    .f-link {
        font-size: 12px; font-weight: 600;
        color: #60a5fa; text-decoration: none; transition: color 0.15s;
    }
    .f-link:hover { color: #93c5fd; }

    .f-wrap { position: relative; }

    .f-icon {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,0.2); pointer-events: none; transition: color 0.15s;
    }
    .f-wrap:focus-within .f-icon { color: #60a5fa; }

    .f-input {
        width: 100%; height: 52px;
        background: var(--input-bg);
        border: 1.5px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        padding: 0 14px 0 42px;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px; font-weight: 500;
        color: #fff; outline: none; transition: all 0.15s;
    }
    .f-input::placeholder { color: rgba(255,255,255,0.2); font-weight: 400; }
    .f-input:focus {
        background: #091523;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
    }
    .f-input.is-invalid { border-color: #ef4444; }
    .f-input.has-toggle { padding-right: 46px; }

    .f-toggle {
        position: absolute; right: 14px; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: rgba(255,255,255,0.2); padding: 4px;
        display: flex; align-items: center; line-height: 0;
        transition: color 0.15s;
    }
    .f-toggle:hover { color: rgba(255,255,255,0.6); }

    .f-error { font-size: 11px; font-weight: 500; color: #fca5a5; margin-top: 5px; }

    /* checkbox */
    .f-check {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 28px; cursor: pointer; user-select: none;
    }
    .f-check input[type="checkbox"] {
        appearance: none; -webkit-appearance: none;
        width: 18px; height: 18px;
        border: 1.5px solid rgba(255,255,255,0.15); border-radius: 5px;
        background: var(--input-bg); cursor: pointer;
        position: relative; flex-shrink: 0; transition: all 0.15s;
    }
    .f-check input:checked { background: var(--blue); border-color: var(--blue); }
    .f-check input:checked::after {
        content: '';
        position: absolute; left: 3px; top: 0;
        width: 6px; height: 10px;
        border: 2px solid #fff; border-top: none; border-left: none;
        transform: rotate(45deg);
    }
    .f-check label {
        font-size: 13px; font-weight: 500;
        color: var(--muted); cursor: pointer;
    }

    /* button */
    .f-btn {
        width: 100%; height: 52px;
        background: var(--blue); color: #fff;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px; font-weight: 700;
        letter-spacing: 0.5px;
        border: none; border-radius: 10px; cursor: pointer;
        transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
        box-shadow: 0 4px 20px rgba(37,99,235,0.4);
        position: relative; overflow: hidden;
    }
    .f-btn::after {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, transparent 60%);
        pointer-events: none;
    }
    .f-btn:hover { background: var(--blue-2); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(37,99,235,0.5); }
    .f-btn:active { transform: translateY(0); }

    .f-footer {
        text-align: center; margin-top: 20px;
        font-size: 13px; font-weight: 500; color: var(--muted);
    }
    .f-footer a { color: #60a5fa; font-weight: 700; text-decoration: none; transition: color 0.15s; }
    .f-footer a:hover { color: #93c5fd; }

    /* animations */
    .f-eyebrow, .f-title, .f-sub, .r-inner form {
        animation: fadeUp 0.4s ease both; opacity: 0;
    }
    .f-eyebrow { animation-delay: 0.05s; }
    .f-title   { animation-delay: 0.1s; }
    .f-sub     { animation-delay: 0.13s; }
    .r-inner form { animation-delay: 0.17s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 860px) {
        body { grid-template-columns: 1fr; }
        .l-panel { display: none; }
        .r-panel { padding: 48px 32px; border-left: none; }
    }
    </style>
</head>
<body>

<!-- LEFT -->
<div class="l-panel">
    <div class="l-inner">

        <div class="l-brand">
            <div class="l-logo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <div class="l-brand-name">Fixtora</div>
                <div class="l-brand-sub">Architectural Concierge</div>
            </div>
        </div>

        <h2 class="l-headline">
            Built for Architects<br>
            <br>
        </h2>

        <p class="l-desc">
            End-to-end ticket management for architectural concierge services. Log issues, assign priorities, and track resolution all in one place.
        </p>
    </div>
    <div class="l-copy">© {{ date('Y') }} Fixtora Enterprise. All rights reserved.</div>
</div>

<!-- RIGHT -->
<div class="r-panel">
    <div class="r-inner">
        @yield('content')
    </div>
</div>

<script>
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
    } else {
        input.type = 'password';
        icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    }
}
</script>
</body>
</html>
