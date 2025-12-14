<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceMate - Forgot Password</title>

    <!-- Reuse same styles as login -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #76b852, #8DC26F);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: #fff;
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2575fc;
            font-weight: 700;
        }

        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            border: 1.5px solid #ccc;
            transition: 0.3s;
        }

        input:focus {
            border-color: #2575fc;
            box-shadow: 0 0 10px rgba(37,117,252,0.25);
            outline: none;
        }

        button {
            width: 100%;
            padding: 0.8rem;
            background: #2575fc;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #6a11cb;
        }

        .back {
            text-align: center;
            margin-top: 1rem;
        }

        .back a {
            color: #2575fc;
            text-decoration: none;
        }
    </style>
</head>

<body>
<div class="login-container">

    <h1>Forgot Password</h1>

    <form action="{{ route('password.sendOtp') }}" method="POST">
        @csrf

        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <button type="submit">Send OTP</button>

        <div class="back">
            <a href="{{ route('page-login') }}">Back to Login</a>
        </div>
    </form>

</div>
</body>
</html>
