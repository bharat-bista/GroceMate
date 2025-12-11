<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceMate - Reset Password</title>

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
        }
    </style>
</head>

<body>
<div class="login-container">

    <h1>Reset Password</h1>

    <form action="{{ route('password.reset') }}" method="POST">
        @csrf

        <label>New Password</label>
        <input type="password" name="password" placeholder="New password" required>

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" placeholder="Confirm password" required>

        <button type="submit">Reset Password</button>

    </form>

</div>
</body>
</html>
