<?php

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $keep_auth = isset($_POST['keep_auth']);

    if ($username === 'admin' && $password === 'password') {
        $success = 'Login successful! Redirecting to dashboard...';
    } else {
        $error = 'Invalid corporate ID or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixtora — Architectural Concierge Support</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy:       #1a2e4a;
            --navy-mid:   #1e3a5f;
            --blue-btn:   #1e3a6e;
            --blue-hover: #16305c;
            --blue-icon:  #3a5f8a;
            --label:      #1a2e4a;
            --muted:      #7a8fa6;
            --border:     #d6dde6;
            --input-bg:   #f4f6f9;
            --bg-page:    #eef0f4;
            --bg-card:    #ffffff;
            --text-link:  #3a6ea8;
            --check-border: #b0bec5;
            --shadow:     rgba(30, 58, 110, 0.10);
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            background: var(--bg-page);
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 15% 20%, rgba(180,200,230,0.35) 0%, transparent 70%),
                radial-gradient(ellipse 50% 60% at 80% 80%, rgba(200,215,235,0.25) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .page-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;   
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            background: #dfe6f0;
        }

        .form-side {
            flex: none;
            width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            min-width: 0;
            background: var(--bg-page);
        }

        .form-card {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: var(--bg-card);
            padding: 40px 32px;
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(30,58,110,0.08);
        }

        .logo-icon {
            width: 72px;
            height: 72px;
            background: var(--blue-btn);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(30,58,110,0.18);
        }

        .logo-icon svg {
            width: 36px;
            height: 36px;
        }

        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 32px;
            font-weight: 400;
            color: var(--navy);
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .brand-tagline {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 2.5px;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        .divider {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border) 20%, var(--border) 80%, transparent);
            margin-bottom: 36px;
        }

        .login-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .field-group {
            margin-bottom: 20px;
        }

        .field-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .field-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            color: var(--label);
            text-transform: uppercase;
        }

        .forgot-link {
            font-size: 13px;
            font-weight: 400;
            color: var(--text-link);
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .forgot-link:hover {
            opacity: 0.7;
            text-decoration: underline;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
            display: flex;
        }

        .input-wrap input {
            width: 100%;
            height: 52px;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 0 16px 0 46px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: var(--navy);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none;
        }

        .input-wrap input::placeholder {
            color: #b0bec5;
            font-weight: 300;
        }

        .input-wrap input:focus {
            border-color: var(--blue-btn);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30,58,110,0.08);
        }

        .input-wrap input[type="password"] {
            letter-spacing: 2px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-row input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid var(--check-border);
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: border-color 0.2s, background 0.2s;
        }

        .checkbox-row input[type="checkbox"]:checked {
            background: var(--blue-btn);
            border-color: var(--blue-btn);
        }

        .checkbox-row input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 3px;
            top: 0px;
            width: 6px;
            height: 10px;
            border: 2px solid #fff;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
        }

        .checkbox-label {
            font-size: 14px;
            color: #4a5e72;
            cursor: pointer;
        }

        .btn-signin {
            width: 100%;
            height: 56px;
            background: var(--blue-btn);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.3px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(30,58,110,0.22);
        }

        .btn-signin:hover {
            background: var(--blue-hover);
            box-shadow: 0 6px 20px rgba(30,58,110,0.30);
        }

        .btn-signin:active {
            transform: scale(0.99);
        }

        .alert {
            width: 100%;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert-error {
            background: #fdecea;
            border: 1px solid #f5c6c4;
            color: #c0392b;
        }

        .alert-success {
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            color: #2e7d32;
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 32px;
            color: var(--muted);
            font-size: 11px;
            letter-spacing: 1.8px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .security-badge svg {
            color: #90a4b4;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            font-size: 12.5px;
            color: var(--muted);
            white-space: nowrap; 
            text-align: center;
            background: var(--bg-card); 
            padding: 12px 20px;
            border-radius: 12px; 
        }

        .footer a {
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: var(--navy);
        }

        .footer-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #b0bec5;
            display: inline-block;
        }

        .image-side {
            width: 42%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            display: none;
        }

        .image-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, #d8e2ed 0%, #eaecf2 50%, #f0f2f5 100%);
            z-index: 0;
        }

        .image-side::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                repeating-linear-gradient(0deg,   transparent, transparent 59px, rgba(180,195,215,0.35) 59px, rgba(180,195,215,0.35) 60px),
                repeating-linear-gradient(90deg,  transparent, transparent 59px, rgba(180,195,215,0.35) 59px, rgba(180,195,215,0.35) 60px);
            z-index: 1;
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            background:
                radial-gradient(ellipse 80% 60% at 60% 40%, rgba(240,244,250,0.45) 0%, transparent 70%),
                linear-gradient(200deg, rgba(220,232,245,0.5) 0%, transparent 60%);
        }

        @media (max-width: 768px) {
            .image-side { display: none; }
            .form-side { padding: 40px 20px; }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="form-side">
        <div class="form-card">

            <div class="logo-icon">
                <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="10" stroke="white" stroke-width="1.8" fill="none"/>
                    <line x1="18" y1="8" x2="18" y2="28" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="8" y1="18" x2="28" y2="18" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="18" cy="18" r="2.5" fill="white"/>

                    <path d="M18 10 L20.5 17.5 L18 16 L15.5 17.5 Z" fill="white" opacity="0.85"/>
                    <path d="M18 26 L20.5 18.5 L18 20 L15.5 18.5 Z" fill="white" opacity="0.4"/>
                </svg>
            </div>

            <div class="brand-name">Fixtora</div>
            <div class="brand-tagline">Architectural Concierge Support</div>

            <div class="divider"></div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="">

                <div class="field-group">
                    <div class="field-header">
                        <label class="field-label" for="username">Username</label>
                    </div>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your corporate ID"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-header">
                        <label class="field-label" for="password">Password</label>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <label class="checkbox-row">
                    <input
                        type="checkbox"
                        name="keep_auth"
                        <?= isset($_POST['keep_auth']) ? 'checked' : '' ?>
                    >
                    <span class="checkbox-label">Keep me authenticated</span>
                </label>

                <button type="submit" class="btn-signin">Sign In to Dashboard</button>

            </form>

            <div class="security-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                AES-256 Encrypted Connection
            </div>

            <div class="footer">
                <span>© 2024 Fixtora Enterprise</span>
                <span class="footer-dot"></span>
                <a href="#">Security Standards</a>
                <span class="footer-dot"></span>
                <a href="#">Privacy Policy</a>
            </div>

        </div>
    </div>

    <div class="image-side" aria-hidden="true">
        <div class="image-overlay"></div>
    </div>

</div>

</body>
</html>