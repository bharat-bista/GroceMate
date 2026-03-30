<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceMate | Register</title>
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

        * { box-sizing: border-box; }
        html, body { min-height: 100%; margin: 0; }
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

        .auth-shell {
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

        .brand-panel::before { width: 280px; height: 280px; top: -110px; right: -100px; }
        .brand-panel::after { width: 320px; height: 320px; bottom: -180px; left: -160px; }
        .brand-top, .brand-bottom { position: relative; z-index: 1; }

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

        .logo-showcase {
            margin-top: 52px;
            padding: 26px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
        }

        .logo-card {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 250px;
            padding: 26px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .logo-image {
            width: min(100%, 310px);
            height: auto;
            display: block;
            filter: drop-shadow(0 18px 28px rgba(7, 84, 34, 0.16));
        }

        .logo-caption {
            margin-top: 18px;
            color: rgba(255, 255, 255, 0.84);
            font-size: 0.95rem;
            line-height: 1.75;
        }

        .highlight-list { display: grid; gap: 18px; margin-top: 24px; }
        .highlight-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .highlight-icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .highlight-icon svg { width: 22px; height: 22px; }
        .highlight-text strong { display: block; font-size: 1rem; font-weight: 700; margin-bottom: 4px; }
        .highlight-text span { display: block; color: rgba(255, 255, 255, 0.82); font-size: 0.93rem; line-height: 1.65; }
        .brand-footer { color: rgba(255, 255, 255, 0.82); font-size: 0.96rem; }

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

        .form-card { width: min(484px, 100%); }
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

        .alerts { display: grid; gap: 12px; margin-bottom: 18px; }
        .alert { padding: 14px 16px; border-radius: 16px; font-size: 0.93rem; line-height: 1.6; }
        .alert.error { background: var(--danger-bg); color: var(--danger-text); border: 1px solid rgba(179, 58, 47, 0.12); }
        .alert.success { background: var(--success-bg); color: var(--success-text); border: 1px solid rgba(22, 115, 68, 0.12); }
        .alert ul { margin: 0; padding-left: 18px; }

        .auth-form { display: grid; gap: 20px; }
        .field { display: grid; gap: 8px; }
        .field-grid { display: grid; gap: 20px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .field label { font-size: 0.92rem; font-weight: 700; color: #334155; }

        .field input,
        .field select {
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

        .field input::placeholder { color: #99a2b0; }
        .field input:focus,
        .field select:focus {
            outline: none;
            border-color: rgba(32, 179, 87, 0.45);
            box-shadow: 0 0 0 4px rgba(32, 179, 87, 0.12);
            transform: translateY(-1px);
        }

        .field input.is-invalid,
        .field select.is-invalid {
            border-color: rgba(179, 58, 47, 0.3);
            box-shadow: 0 0 0 4px rgba(179, 58, 47, 0.08);
        }

        .error-text { margin: 0; color: var(--danger-text); font-size: 0.85rem; }
        .submit-btn,
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

        .submit-btn:hover,
        .google-btn:hover { transform: translateY(-2px); filter: brightness(1.01); }
        .submit-btn {
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
        .divider::after { content: ''; flex: 1; border-top: 1px solid #d8e0e8; }

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

        .footer-text a { color: var(--green-3); font-weight: 700; text-decoration: none; }
        .footer-text a:hover { text-decoration: underline; }

        @media (max-width: 980px) {
            .auth-shell { grid-template-columns: 1fr; }
            .brand-panel, .form-panel { padding: 36px 28px; }
            .logo-showcase { margin-top: 34px; }
        }

        @media (max-width: 640px) {
            .page { padding: 10px; }
            .auth-shell { min-height: auto; border-radius: 24px; }
            .brand-name { font-size: 2.5rem; }
            .welcome-title { font-size: 2.2rem; }
            .field-grid { grid-template-columns: 1fr; }
            .field input, .field select, .submit-btn, .google-btn { height: 58px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="auth-shell">
            <aside class="brand-panel">
                <div class="brand-top">
                    <h1 class="brand-name">GroceMate</h1>
                    <p class="brand-subtitle">Create your account to manage the store or shop online through one connected GroceMate experience.</p>

                    <div class="logo-showcase">
                        <div class="logo-card">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="GroceMate logo" class="logo-image">
                        </div>
                        <p class="logo-caption">Get started with inventory, POS, orders, and customer activity from a single account.</p>

                        <div class="highlight-list">
                            <div class="highlight-item">
                                <div class="highlight-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 5v14M5 12h14" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                                        <rect x="4" y="4" width="16" height="16" rx="4" stroke="white" stroke-width="1.8"/>
                                    </svg>
                                </div>
                                <div class="highlight-text">
                                    <strong>Quick onboarding</strong>
                                    <span>Create your profile and move directly into store management or customer access.</span>
                                </div>
                            </div>

                            <div class="highlight-item">
                                <div class="highlight-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 7h11l-1.1 6.1a1.5 1.5 0 0 1-1.48 1.23H10.1a1.5 1.5 0 0 1-1.47-1.22L7 7Z" stroke="white" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M9 7 10 4.8A1.5 1.5 0 0 1 11.37 4h1.26A1.5 1.5 0 0 1 14 4.8L15 7" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="highlight-text">
                                    <strong>Shared platform access</strong>
                                    <span>Use one account system for business workflows and ecommerce customer journeys.</span>
                                </div>
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
                    <h2 class="welcome-title">Create Account</h2>
                    <p class="welcome-subtitle">Register to open your GroceMate account and continue with verification.</p>

                    <div class="alerts">
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

                    <form method="POST" action="{{ route('register.sendOtp') }}" class="auth-form">
                        @csrf

                        <div class="field-grid">
                            <div class="field">
                                <label for="full_name">Full name</label>
                                <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" value="{{ old('full_name') }}" class="@error('full_name') is-invalid @enderror" required>
                                @error('full_name')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="@error('gender') is-invalid @enderror" required>
                                    <option value="" disabled @selected(!old('gender'))>Select gender</option>
                                    <option value="male" @selected(old('gender') === 'male')>Male</option>
                                    <option value="female" @selected(old('gender') === 'female')>Female</option>
                                    <option value="other" @selected(old('gender') === 'other')>Other</option>
                                </select>
                                @error('gender')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="field">
                            <label for="email">Email address</label>
                            <input type="email" name="email" id="email" placeholder="Email address" value="{{ old('email') }}" class="@error('email') is-invalid @enderror" required>
                            @error('email')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field-grid">
                            <div class="field">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" placeholder="Create password" class="@error('password') is-invalid @enderror" required>
                                @error('password')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="password_confirmation">Confirm password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm password" required>
                            </div>
                        </div>

                        <button type="submit" class="submit-btn">Create Account</button>

                        <div class="divider">OR</div>

                        <a href="{{ route('google.redirect') }}" class="google-btn">
                            <svg width="22" height="22" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M44.5 20H24V28.5H35.8C34.7 33.9 30.1 37 24 37C16.8 37 11 31.2 11 24C11 16.8 16.8 11 24 11C27.1 11 29.9 12.1 32.2 14L38.3 7.9C34.5 4.4 29.5 2.25 24 2.25C12 2.25 2.25 12 2.25 24C2.25 36 12 45.75 24 45.75C35 45.75 44.75 37.75 44.75 24.45C44.75 22.95 44.65 21.45 44.5 20Z" fill="#FFC107"/>
                                <path d="M6.95 14.69L13.93 19.81C15.82 15.13 19.55 11.75 24 11.75C27.1 11.75 29.9 12.86 32.2 14.75L38.3 8.65C34.5 5.15 29.5 3 24 3C16.31 3 9.66 7.34 6.95 14.69Z" fill="#FF3D00"/>
                                <path d="M24 45.75C29.39 45.75 34.31 43.75 38.08 40.38L31.41 34.74C29.22 36.4 26.48 37.37 24 37.37C18.13 37.37 13.15 33.64 11.86 28.52L4.93 33.87C7.59 41.31 15 45.75 24 45.75Z" fill="#4CAF50"/>
                                <path d="M44.5 20H24V28.5H35.8C35.28 31.05 33.82 33.2 31.41 34.74L38.08 40.38C42.03 36.74 44.75 31.38 44.75 24.45C44.75 22.95 44.65 21.45 44.5 20Z" fill="#1976D2"/>
                            </svg>
                            <span>Sign up with Google</span>
                        </a>
                    </form>

                    <p class="footer-text">Already have an account? <a href="{{ route('page-login') }}">Sign in</a></p>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
