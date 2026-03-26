<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Fixtora - Architectural Concierge')</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #2563eb;
            --secondary: #1e3a6e;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
            --bg-light: #f7f8fc;
            --bg-surface: #ffffff;
            --border-color: #dde2f0;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --radius: 8px;
            --shadow: 0 1px 3px rgba(37, 99, 235, 0.06);
        }

        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-light);
            color: var(--text-primary);
        }

        /* Sidebar Navigation */
        .app-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            background: linear-gradient(135deg, #1a3a5c 0%, #1e3a8a 100%);
            color: white;
            padding: 24px 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            color: white;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .brand-tier {
            font-size: 11px;
            opacity: 0.7;
            margin: 2px 0 0 0;
            font-weight: 500;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 24px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 12px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 6px;
            border-left: 3px solid transparent;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active {
            color: white;
            background: rgba(37, 99, 235, 0.15);
            border-left-color: #2563eb;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        .btn-new-ticket {
            display: block;
            width: 100%;
            padding: 12px 16px;
            background: var(--primary-light);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 24px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }

        .btn-new-ticket:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .sidebar-bottom {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-item-bottom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 12px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-item-bottom:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            margin-top: 16px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            margin: 2px 0 0 0;
            font-size: 11px;
            opacity: 0.7;
        }

        /* Main Content Area */
        .main-wrapper {
            margin-left: 220px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .top-nav {
            height: 64px;
            background: white;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-search {
            flex: 1;
            max-width: 400px;
        }

        .nav-search input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            background: var(--bg-light);
            font-family: 'Montserrat', sans-serif;
        }

        .nav-search input::placeholder {
            color: var(--text-secondary);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .system-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--success);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
            padding: 0;
        }

        .icon-btn svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        .icon-btn:hover {
            color: var(--primary);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            overflow-y: auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 16px 8px;
            }

            .brand-name,
            .brand-tier,
            .nav-item span,
            .nav-item-bottom span {
                display: none;
            }

            .main-wrapper {
                margin-left: 60px;
            }

            .nav-search {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">F</div>
                <div>
                    <h2 class="brand-name">Fixtora</h2>
                    <p class="brand-tier">Architectural Concierge</p>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>Tickets</span>
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"></path>
                    </svg>
                    <span>Tasks</span>
                </a>
                <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    <span>Reports</span>
                </a>
                <a href="{{ route('sla-monitor.index') }}" class="nav-item {{ request()->routeIs('sla-monitor.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="1"></circle>
                        <path d="M4.22 4.22a10 10 0 0115.56 0M1.46 7.46a14 14 0 0121.08 0M8.63 8.63a4 4 0 016.74 0"></path>
                    </svg>
                    <span>SLA Monitor</span>
                </a>
            </nav>

            <!-- New Ticket Button -->
            <a href="{{ route('tickets.create') }}" class="btn-new-ticket">+ New Ticket</a>

            <!-- Bottom Menu -->
            <div class="sidebar-bottom">
                <a href="{{ route('settings.index') }}" class="nav-item-bottom {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="1"></circle>
                        <path d="M12 1v6m0 6v6"></path>
                        <circle cx="12" cy="12" r="9"></circle>
                    </svg>
                    <span>Settings</span>
                </a>
                <a href="{{ route('help.index') }}" class="nav-item-bottom {{ request()->routeIs('help.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                    <span>Help</span>
                </a>

                <!-- User Profile -->
                <a href="{{ route('profile.show') }}" class="user-profile" style="text-decoration: none; color: inherit;">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'AC', 0, 2)) }}</div>
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name ?? 'Alex Chen' }}</p>
                        <p class="user-role">Senior Architect</p>
                    </div>
                </a>

                <a href="{{ route('logout') }}" class="nav-item-bottom" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-wrapper">
            <!-- Top Navigation -->
            <header class="top-nav">
                <div class="nav-search">
                    <input type="text" placeholder="Search tickets, agents, or knowledge...">
                </div>
                <div class="nav-actions">
                    <span class="system-status">
                        <span class="status-dot"></span>
                        SYSTEM ONLINE
                    </span>
                    <a href="{{ route('notifications.index') }}" class="icon-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </a>
                    <button class="icon-btn" onclick="alert('Menu coming soon!')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="1"></circle>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
