<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Fixtora - Login')</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #2563eb;
            --bg-light: #f7f8fc;
            --border-color: #dde2f0;
            --text-primary: #111827;
            --text-secondary: #6b7280;
        }

        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1a3a5c 0%, #1e3a8a 100%);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1a3a5c 0%, #1e3a8a 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 24px;
            color: white;
            margin: 0 auto 16px;
        }

        .auth-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .auth-header p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-primary);
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .form-checkbox input {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }

        .form-checkbox label {
            margin: 0;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .btn-submit {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, #1a3a5c 0%, #1e3a8a 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 16px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.3);
        }

        .auth-footer {
            text-align: center;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--primary-light);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 4px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
            border: 1px solid rgba(220, 38, 38, 0.2);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            @yield('content')
        </div>
    </div>
</body>
</html>