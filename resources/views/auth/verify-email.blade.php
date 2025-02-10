<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Email Verification - Rainwater Catch Basin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="../assets/css/material-kit.css?v=3.1.0" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(to right, #636FA4, #E8CBC0);
        }
        .verification-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .verification-card {
            width: 400px;
            padding: 30px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .circle-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-image: url('https://your-logo-url-here.com/logo.png');
            background-size: cover;
            background-position: center;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="circle-logo"></div>
            <h3>Rainwater Catch Basin</h3>
            <p class="text-sm text-gray-600">Thanks for signing up! Before getting started, please verify your email by clicking the link we sent. If you didn't receive the email, we can send another.</p>
            
            @if (session('status') == 'verification-link-sent')
                <p class="text-sm text-green-600">A new verification link has been sent to your email.</p>
            @endif
            
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
            </form>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-secondary w-100">Log Out</button>
            </form>
        </div>
    </div>
</body>
</html>
