<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Rainwater Catch Basin</title>
   <!-- Fonts & Icons -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

   




    <style>
        body {
            background: linear-gradient(to right, #636FA4, #E8CBC0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            position: relative;
            overflow: hidden;
            width: 900px; /* Increased width */
            max-width: 100%;
            min-height: 700px; /* Increased height */
        }
        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }
        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }
        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }
        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }
        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }
        @keyframes show {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }
        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }
        .overlay {
            background-color: #21D4FD;
            background-image: linear-gradient(19deg, #21D4FD 0%, #B721FF 100%);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }
        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .overlay-left {
            transform: translateX(-20%);
        }
        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }
        .overlay-right {
            right: 0;
            transform: translateX(0);
        }
        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }
        .social-container {
            margin: 20px 0;
        }
        .social-container a {
            border: 1px solid #DDDDDD;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
        }
        .form-container form {
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .form-container form h1 {
            font-weight: bold;
            margin: 0;
        }
        .form-container form input {
            background: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }
        .form-container form .btn {
            border-radius: 5px;
            border: none;
            background: #007bff;
            color: #FFFFFF;
            font-size: 1rem;
            font-weight: bold;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .form-container form .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .form-container form a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }
        footer {
            background-color: #222;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        footer a {
            color: #FF4B2B;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
        #signUp {
            background-color: #FFFFFF;
            color: #007bff;
            border: 1px solid #007bff;
        }

        #signUp:hover {
            background-color: #f0f0f0;
            color: #0056b3;
        }

        #signIn {
            background-color: #FFFFFF;
            color: #007bff;
            border: 1px solid #007bff;
        }

        #signIn:hover {
            background-color: #f0f0f0;
            color: #0056b3;
        }
        .circle-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-image: url('https://scontent.fcgy1-3.fna.fbcdn.net/v/t39.30808-6/466458716_869630271997402_9083455242095747570_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeESqb9kOvO31JySvEXLdZNMj9_UUMw76LSP39RQzDvotLTYI6P6-l4uJv4JGcamYs6NmpBqWpGfgMmGo3bUH6xY&_nc_ohc=rlyV0WYTny8Q7kNvgFOwbbO&_nc_oc=AdjI8HW7RcI6wIm4XhkvY_fj61mLMzEBRsbO_nXINj6VHUFJD7g0U6OmJXXT5BlDqujyOpvJYodsyg6sF6LUyt6H&_nc_zt=23&_nc_ht=scontent.fcgy1-3.fna&_nc_gid=ACMC7skbT0YfcUUfp_faqqI&oh=00_AYC1aFoe2OUNBOXnjtqe7kTyI4FDpUF39uIOOrUisC0Sng&oe=67BF866D');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .btn {
            border-radius: 5px;
            border: none;
            background: #007bff;
            color: #FFFFFF;
            font-size: 1rem;
            font-weight: bold;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .plant {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
        <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <h1>Sign in</h1>
                                    
                                    @if(session('error'))
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
                <div id="errorToast" class="toast show fade bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-danger text-white">
                        <strong class="me-auto">Error</strong>
                        <small>Just now</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
            @endif


                                    <div class="social-container">
                                       
                                    </div>
                                    <span>Login admin account</span>
                                    <input type="email" placeholder="Email" name="email" required />
                                    <input type="password" placeholder="Password" name="password" required />
                                    <a href="{{ route('password.request') }}">Forgot your password?</a>
                                    <button type="submit" class="btn">Sign In</button>
                                </form>

                        </div>
                        
                        
                        
                       

                        <div class="form-container sign-in-container">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <h1>View only</h1>

                                    <img src="https://i.pinimg.com/originals/e8/88/d4/e888d4feff8fd5ff63a965471a94b874.gif" alt="Google Sign-in" class="plant">


                                    <br>
                                    <span>Sign in only google account</span>
                                    
                                   

                                    <div class="social-container">
                                        <a href="{{ route('google.redirect') }}" class="social">
                                            <i class="fab fa-google-plus-g"></i>
                                        </a>
                                    </div>
                                    
                                </form>
                            </div>
                        <div class="overlay-container">
                            <div class="overlay">
                                <div class="overlay-panel overlay-left">
                                    <div class="circle-logo"></div>
                                    <h1>Rainwater Catch Basin</h1>
                                    <p>Go back to previous? </p>
                                    <button class="ghost btn" id="signIn">Go back</button>
                                </div>
                                <div class="overlay-panel overlay-right">
                                    <div class="circle-logo"></div>
                                    <h1>Rainwater Catch Basin</h1>
                                    <p>Login as admin?</p>
                                    <button class="ghost btn" id="signUp">Admin login</button>
                                </div>
                            </div>
                        </div>
                    </div>

   

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });

    </script>
     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        

</body>

</html>

