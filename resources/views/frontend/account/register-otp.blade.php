<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceMate | Registration OTP</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        :root { --page-bg:#f3f8f2; --page-bg-alt:#eef8ff; --ink:#1f2937; --muted:#6b7280; --line:#d6dee7; --green-1:#20b357; --green-2:#4ad97c; --green-3:#169b48; --danger-bg:#fff1ef; --danger-text:#b33a2f; --success-bg:#ecfff2; --success-text:#167344; --shadow:0 28px 70px rgba(26,73,41,0.14); }
        *{box-sizing:border-box;} html,body{min-height:100%;margin:0;} body{font-family:'Plus Jakarta Sans',sans-serif;color:var(--ink);background:radial-gradient(circle at top left,rgba(74,217,124,0.18),transparent 24%),radial-gradient(circle at bottom right,rgba(104,202,255,0.16),transparent 22%),linear-gradient(135deg,var(--page-bg) 0%,var(--page-bg-alt) 100%);}
        .page{min-height:100vh;display:grid;place-items:center;padding:16px;} .auth-shell{width:min(1220px,100%);min-height:min(820px,calc(100vh - 32px));display:grid;grid-template-columns:1fr 1fr;border-radius:34px;overflow:hidden;background:rgba(255,255,255,0.82);border:1px solid rgba(255,255,255,0.7);box-shadow:var(--shadow);backdrop-filter:blur(16px);}
        .brand-panel{position:relative;display:flex;flex-direction:column;justify-content:space-between;padding:56px 54px;color:#fff;background:linear-gradient(150deg,var(--green-1) 0%,var(--green-2) 100%);overflow:hidden;}
        .brand-panel::before,.brand-panel::after{content:'';position:absolute;border-radius:50%;background:rgba(255,255,255,0.08);pointer-events:none;} .brand-panel::before{width:280px;height:280px;top:-110px;right:-100px;} .brand-panel::after{width:320px;height:320px;bottom:-180px;left:-160px;}
        .brand-top,.brand-bottom{position:relative;z-index:1;} .brand-name{margin:0 0 14px;font-family:'Outfit',sans-serif;font-size:clamp(2.8rem,5vw,4rem);line-height:1;letter-spacing:-0.04em;} .brand-subtitle{margin:0;max-width:28rem;font-size:1.05rem;line-height:1.8;color:rgba(255,255,255,0.88);}
        .logo-showcase{margin-top:52px;padding:26px;border-radius:28px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.18);} .logo-card{display:flex;align-items:center;justify-content:center;min-height:250px;padding:26px;border-radius:24px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.16);} .logo-image{width:min(100%,310px);height:auto;display:block;} .logo-caption{margin-top:18px;color:rgba(255,255,255,0.84);font-size:0.95rem;line-height:1.75;} .brand-footer{color:rgba(255,255,255,0.82);font-size:0.96rem;}
        .form-panel{display:flex;align-items:center;justify-content:center;padding:56px 64px;background:radial-gradient(circle at bottom left,rgba(74,217,124,0.08),transparent 26%),radial-gradient(circle at top right,rgba(98,185,255,0.08),transparent 22%),rgba(255,255,255,0.92);} .form-card{width:min(484px,100%);}
        .welcome-title{margin:0;font-family:'Outfit',sans-serif;font-size:clamp(2.3rem,4vw,3.3rem);line-height:1.05;letter-spacing:-0.04em;color:#263247;} .welcome-subtitle{margin:14px 0 34px;color:var(--muted);font-size:1rem;line-height:1.8;}
        .alerts{display:grid;gap:12px;margin-bottom:18px;} .alert{padding:14px 16px;border-radius:16px;font-size:0.93rem;line-height:1.6;} .alert.error{background:var(--danger-bg);color:var(--danger-text);border:1px solid rgba(179,58,47,0.12);} .alert.success{background:var(--success-bg);color:var(--success-text);border:1px solid rgba(22,115,68,0.12);} .alert ul{margin:0;padding-left:18px;}
        .auth-form{display:grid;gap:20px;} .field{display:grid;gap:8px;} .field label{font-size:0.92rem;font-weight:700;color:#334155;} .field input{width:100%;height:68px;padding:0 20px;border-radius:20px;border:1px solid var(--line);background:rgba(255,255,255,0.96);color:var(--ink);font:inherit;font-size:1rem;}
        .field input:focus{outline:none;border-color:rgba(32,179,87,0.45);box-shadow:0 0 0 4px rgba(32,179,87,0.12);} .field input.is-invalid{border-color:rgba(179,58,47,0.3);box-shadow:0 0 0 4px rgba(179,58,47,0.08);} .error-text{margin:0;color:var(--danger-text);font-size:0.85rem;}
        .submit-btn{width:100%;height:64px;border:none;border-radius:18px;color:#fff;background:linear-gradient(135deg,#23c15d 0%,#169b48 100%);box-shadow:0 18px 30px rgba(22,155,72,0.2);font:inherit;font-size:1.02rem;font-weight:700;cursor:pointer;}
        .footer-text{margin:10px 0 0;text-align:center;color:var(--muted);font-size:0.98rem;} .footer-text a{color:var(--green-3);font-weight:700;text-decoration:none;} .footer-text a:hover{text-decoration:underline;}
        @media (max-width:980px){.auth-shell{grid-template-columns:1fr;}.brand-panel,.form-panel{padding:36px 28px;}.logo-showcase{margin-top:34px;}} @media (max-width:640px){.page{padding:10px;}.auth-shell{min-height:auto;border-radius:24px;}.brand-name{font-size:2.5rem;}.welcome-title{font-size:2.2rem;}.field input,.submit-btn{height:58px;}}
    </style>
</head>
<body>
    <main class="page">
        <section class="auth-shell">
            <aside class="brand-panel">
                <div class="brand-top">
                    <h1 class="brand-name">GroceMate</h1>
                    <p class="brand-subtitle">Finish registration by verifying the OTP sent to {{ $user->email }}.</p>
                    <div class="logo-showcase">
                        <div class="logo-card">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="GroceMate logo" class="logo-image">
                        </div>
                        <p class="logo-caption">This last step activates your account so you can start using GroceMate right away.</p>
                    </div>
                </div>
                <div class="brand-bottom"><div class="brand-footer">&copy; {{ date('Y') }} GroceMate</div></div>
            </aside>
            <section class="form-panel">
                <div class="form-card">
                    <h2 class="welcome-title">Verify Registration</h2>
                    <p class="welcome-subtitle">Enter the 6-digit OTP from your email to activate your account.</p>
                    <div class="alerts">
                        @if(session('success'))
                            <div class="alert success">{{ session('success') }}</div>
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
                    <form action="{{ route('register.verifyOtp', $user->id) }}" method="POST" class="auth-form">
                        @csrf
                        <div class="field">
                            <label for="otp">Registration OTP</label>
                            <input type="text" name="otp" id="otp" placeholder="Enter 6-digit OTP" value="{{ old('otp') }}" class="@error('otp') is-invalid @enderror" inputmode="numeric" maxlength="6" required autofocus>
                            @error('otp')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="submit-btn">Verify & Activate</button>
                    </form>
                    <form action="{{ route('register.resendOtp', $user->id) }}" method="POST" style="margin-top:14px;text-align:center;">
                        @csrf
                        <button type="submit" style="background:none;border:none;color:var(--green-3);font-size:0.97rem;font-weight:600;cursor:pointer;text-decoration:underline;padding:0;">
                            Didn't receive it? Resend OTP
                        </button>
                    </form>
                    <p class="footer-text" style="margin-top:10px;"><a href="{{ route('register') }}">Back to register</a></p>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
