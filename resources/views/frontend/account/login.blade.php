<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceMate</title>
    <style>
        /* Import a modern font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #76b852, #8DC26F); /* fresh green gradient */
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

        .login-container h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2575fc;
            font-weight: 700;
        }

        .login-container label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1.5rem;
            border: 1.5px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            transition: 0.3s;
        }

        .login-container input:focus {
            border-color: #2575fc;
            outline: none;
            box-shadow: 0 0 10px rgba(37,117,252,0.25);
        }

        .login-container button {
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

        .login-container button:hover {
            background-color: #6a11cb;
        }

        .login-container .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .login-container .options a {
            color: #2575fc;
            text-decoration: none;
            transition: 0.3s;
        }

        .login-container .options a:hover {
            text-decoration: underline;
        }

        .login-container .register {
            text-align: center;
            font-size: 0.95rem;
            margin-top: 1rem;
        }

        .login-container .register a {
            color: #6a11cb;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .login-container .register a:hover {
            text-decoration: underline;
        }

        /* Checkbox styling */
        .checkbox-container {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>GroceMate</h1>

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required>
        <div id="errorPopup" class="popup">
    
</div>

<style>
    .popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.popup-content {
    background: #fff;
    padding: 25px;
    text-align: center;
    border-radius: 10px;
    width: 300px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: scaleIn 0.3s ease;
}

@keyframes scaleIn {
    from { transform: scale(0.7); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.popup-content button {
    margin-top: 10px;
    padding: 8px 20px;
    background: #2575fc;
    border: none;
    color: white;
    border-radius: 8px;
    cursor: pointer;
}

</style>

        <div class="options">
            <label class="checkbox-container">
                <input type="checkbox"> Remember Me
            </label>
            <a href="{{route('showEmailForm')}}">Forgot Password?</a>
        </div>
    
        <button type="submit">Sign In</button>

        <div class="register">
            Don't have an account? <a href="#">Register Now</a>
        </div>
    </form>
</div>

</body>
</html>
