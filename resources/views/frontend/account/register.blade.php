<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceMate | Register</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #76b852, #8DC26F);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            background-color: #fff;
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-container h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2575fc;
            font-weight: 700;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        input {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1.4rem;
            border: 1.5px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            transition: 0.3s;
        }

        input:focus {
            border-color: #2575fc;
            outline: none;
            box-shadow: 0 0 10px rgba(37,117,252,0.25);
        }

        button {
            width: 100%;
            padding: 0.8rem 1rem;
            background-color: #2575fc;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #6a11cb;
        }

        .login-link {
            text-align: center;
            font-size: 0.95rem;
            margin-top: 1.2rem;
        }

        .login-link a {
            color: #6a11cb;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Google Button */
        .google-btn {
            margin-top: 1rem;
            width: 100%;
            height: 50px;
            border: 1.5px solid #ddd;
            border-radius: 999px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            color: #444;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .google-btn:hover {
            background-color: #f7f7f7;
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }

        .google-icon {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h1>GroceMate</h1>
    @if ($errors->any())
  <div style="background:#ffe3e3;color:#b00020;padding:10px;border-radius:10px;margin-bottom:15px;">
    @foreach ($errors->all() as $error)
      <div>{{ $error }}</div>
    @endforeach
  </div>
@endif


    <form method="POST" action="{{ route('register.sendOtp') }}">
        @csrf

        <label for="name">Full Name</label>
        <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" required>

        <label for="gender">Gender</label>
<select name="gender" id="gender" required
    style="width:100%;padding:0.8rem 1rem;margin-bottom:1.4rem;border:1.5px solid #ccc;border-radius:10px;font-size:1rem;">
    <option value="" disabled selected>Select gender</option>
    <option value="male">Male</option>
    <option value="female">Female</option>
    <option value="other">Other</option>
</select>


        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Create a password" required>

        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm password" required>

        <button type="submit">Create Account</button>

        <!-- Google Register Button -->
        <a href="{{ route('google.redirect') }}" class="google-btn">
            <img src="https://developers.google.com/identity/images/g-logo.png" class="google-icon">
            <span>Sign up with Google</span>
        </a>

        <div class="login-link">
            Already have an account? <a href="{{ route('page-login') }}">Sign In</a>
        </div>
    </form>
</div>

</body>
</html>
