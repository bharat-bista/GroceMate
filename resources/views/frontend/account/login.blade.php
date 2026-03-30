<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceMate | Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        :root {
            --page-bg: #f3f8f2;
            --page-bg-alt: #eef8ff;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #d6dee7;
            --green-1: #20b357;
            --green-2: #4ad97c;
            --green-3: #169b48;
            --green-soft: rgba(255, 255, 255, 0.14);
            --green-soft-border: rgba(255, 255, 255, 0.12);
            --white: #ffffff;
            --danger-bg: #fff1ef;
            --danger-text: #b33a2f;
            --success-bg: #ecfff2;
            --success-text: #167344;
            --shadow: 0 28px 70px rgba(26, 73, 41, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(74, 217, 124, 0.18), transparent 24%),
                radial-gradient(circle at bottom right, rgba(104, 202, 255, 0.16), transparent 22%),
                linear-gradient(135deg, var(--page-bg) 0%, var(--page-bg-alt) 100%);
        }

        .page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 16px;
        }

        .login-shell {
            width: min(1220px, 100%);
            min-height: min(820px, calc(100vh - 32px));
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-radius: 34px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: var(--shadow);
            backdrop-filter: blur(16px);
        }

        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 56px 54px;
            color: #fff;
            background: linear-gradient(150deg, var(--green-1) 0%, var(--green-2) 100%);
            overflow: hidden;
        }

        .brand-panel::before,
        .brand-panel::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .brand-panel::before {
            width: 280px;
            height: 280px;
            top: -110px;
            right: -100px;
        }

        .brand-panel::after {
            width: 320px;
            height: 320px;
            bottom: -180px;
            left: -160px;
        }

        .brand-top,
        .brand-bottom {
            position: relative;
            z-index: 1;
        }

        .brand-name {
            margin: 0 0 14px;
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.8rem, 5vw, 4rem);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .brand-subtitle {
            margin: 0;
            max-width: 28rem;
            font-size: 1.05rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.88);
        }

        .feature-list {
            display: grid;
            gap: 22px;
            margin-top: 54px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .feature-icon {
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: var(--green-soft);
            border: 1px solid var(--green-soft-border);
            font-size: 1.4rem;
            font-weight: 700;
        }

        .feature-text strong {
            display: block;
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .feature-text span {
            display: block;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .brand-footer {
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.96rem;
        }

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px 64px;
            background:
                radial-gradient(circle at bottom left, rgba(74, 217, 124, 0.08), transparent 26%),
                radial-gradient(circle at top right, rgba(98, 185, 255, 0.08), transparent 22%),
                rgba(255, 255, 255, 0.92);
        }

        .form-card {
            width: min(484px, 100%);
        }

        .welcome-title {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.3rem, 4vw, 3.3rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
            color: #263247;
        }

        .welcome-subtitle {
            margin: 14px 0 34px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.8;
        }

        .alerts {
            display: grid;
            gap: 12px;
            margin-bottom: 18px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 0.93rem;
            line-height: 1.6;
        }

        .alert.error {
            background: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid rgba(179, 58, 47, 0.12);
        }

        .alert.success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid rgba(22, 115, 68, 0.12);
        }

        .alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .login-form {
            display: grid;
            gap: 20px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field label {
            font-size: 0.92rem;
            font-weight: 700;
            color: #334155;
        }

        .field input {
            width: 100%;
            height: 68px;
            padding: 0 20px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.96);
            color: var(--ink);
            font: inherit;
            font-size: 1rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .field input::placeholder {
            color: #99a2b0;
        }

        .field input:focus {
            outline: none;
            border-color: rgba(32, 179, 87, 0.45);
            box-shadow: 0 0 0 4px rgba(32, 179, 87, 0.12);
            transform: translateY(-1px);
        }

        .field input.is-invalid {
            border-color: rgba(179, 58, 47, 0.3);
            box-shadow: 0 0 0 4px rgba(179, 58, 47, 0.08);
        }

        .error-text {
            margin: 0;
            color: var(--danger-text);
            font-size: 0.85rem;
        }

        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 0.96rem;
        }

        .remember input {
            width: 18px;
            height: 18px;
            accent-color: var(--green-3);
        }

        .link {
            color: var(--green-3);
            font-size: 0.96rem;
            font-weight: 600;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .login-btn,
        .google-btn {
            width: 100%;
            height: 64px;
            border-radius: 18px;
            font: inherit;
            font-size: 1.02rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .login-btn:hover,
        .google-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.01);
        }

        .login-btn {
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #23c15d 0%, #169b48 100%);
            box-shadow: 0 18px 30px rgba(22, 155, 72, 0.2);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #9aa3af;
            font-size: 0.92rem;
            margin: 2px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid #d8e0e8;
        }

        .google-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            border: 1px solid #d8e0e8;
            background: rgba(255, 255, 255, 0.95);
            color: #111827;
            text-decoration: none;
        }

        .footer-text {
            margin: 10px 0 0;
            text-align: center;
            color: var(--muted);
            font-size: 0.98rem;
        }

        .footer-text a {
            color: var(--green-3);
            font-weight: 700;
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 980px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .brand-panel,
            .form-panel {
                padding: 36px 28px;
            }

            .feature-list {
                margin-top: 36px;
            }
        }

        @media (max-width: 640px) {
            .page {
                padding: 10px;
            }

            .login-shell {
                min-height: auto;
                border-radius: 24px;
            }

            .brand-name {
                font-size: 2.5rem;
            }

            .welcome-title {
                font-size: 2.2rem;
            }

            .field input,
            .login-btn,
            .google-btn {
                height: 58px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="login-shell">
            <aside class="brand-panel">
                <div class="brand-top">
                    <h1 class="brand-name">GroceMate</h1>
                    <p class="brand-subtitle">
                        Smart grocery management and shopping access for stores, staff, and ecommerce customers.
                    </p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon">1</div>
                            <div class="feature-text">
                                <strong>Manage store operations</strong>
                                <span>Access inventory, purchases, billing, and supplier activity from one workspace.</span>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">2</div>
                            <div class="feature-text">
                                <strong>Continue customer shopping</strong>
                                <span>Let customers return to their account, orders, cart, and checkout flow with ease.</span>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">3</div>
                            <div class="feature-text">
                                <strong>One login for the platform</strong>
                                <span>A simple shared sign-in experience that works for both business users and online buyers.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="brand-bottom">
                    <div class="brand-footer">&copy; {{ date('Y') }} GroceMate</div>
                </div>
            </aside>

            <section class="form-panel">
                <div class="form-card">
                    <h2 class="welcome-title">Welcome Back</h2>
                    <p class="welcome-subtitle">
                        Login to manage your store or continue shopping with your GroceMate account.
                    </p>

                    <div class="alerts">
                        @if(session('popup_error'))
                            <div class="alert error">{{ session('popup_error') }}</div>
                        @endif

                        @if(session('status'))
                            <div class="alert success">{{ session('status') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert error">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('login.post') }}" class="login-form">
                        @csrf

                        <div class="field">
                            <label for="email">Email address</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                placeholder="Email address"
                                value="{{ old('email') }}"
                                class="@error('email') is-invalid @enderror"
                                required
                                autofocus
                            >
                            @error('email')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Password"
                                class="@error('password') is-invalid @enderror"
                                required
                            >
                            @error('password')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="meta-row">
                            <label class="remember" for="remember">
                                <input type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
                                <span>Remember me</span>
                            </label>

                            <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
                        </div>

                        <button type="submit" class="login-btn">Login</button>

                        <div class="divider">OR</div>

                        <a href="{{ route('google.redirect') }}" class="google-btn">
                            <svg width="22" height="22" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M44.5 20H24V28.5H35.8C34.7 33.9 30.1 37 24 37C16.8 37 11 31.2 11 24C11 16.8 16.8 11 24 11C27.1 11 29.9 12.1 32.2 14L38.3 7.9C34.5 4.4 29.5 2.25 24 2.25C12 2.25 2.25 12 2.25 24C2.25 36 12 45.75 24 45.75C35 45.75 44.75 37.75 44.75 24.45C44.75 22.95 44.65 21.45 44.5 20Z" fill="#FFC107"/>
                                <path d="M6.95 14.69L13.93 19.81C15.82 15.13 19.55 11.75 24 11.75C27.1 11.75 29.9 12.86 32.2 14.75L38.3 8.65C34.5 5.15 29.5 3 24 3C16.31 3 9.66 7.34 6.95 14.69Z" fill="#FF3D00"/>
                                <path d="M24 45.75C29.39 45.75 34.31 43.75 38.08 40.38L31.41 34.74C29.22 36.4 26.48 37.37 24 37.37C18.13 37.37 13.15 33.64 11.86 28.52L4.93 33.87C7.59 41.31 15 45.75 24 45.75Z" fill="#4CAF50"/>
                                <path d="M44.5 20H24V28.5H35.8C35.28 31.05 33.82 33.2 31.41 34.74L38.08 40.38C42.03 36.74 44.75 31.38 44.75 24.45C44.75 22.95 44.65 21.45 44.5 20Z" fill="#1976D2"/>
                            </svg>
                            <span>Continue with Google</span>
                        </a>
                    </form>

                    <p class="footer-text">
                        Don't have an account? <a href="{{ route('register') }}">Sign up</a>
                    </p>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
