<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Rainwater Catch Basin</title>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    
    <style>
        body {
            background: linear-gradient(to right, #636FA4, #E8CBC0);
            animation: gradient 5s ease infinite;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .main-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            padding: 0 20px;
        }
        .left-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        .circle-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-image: url('https://scontent.fcgy1-3.fna.fbcdn.net/v/t39.30808-6/466458716_869630271997402_9083455242095747570_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=dNEe5EZGq5gQ7kNvgHUbXVU&_nc_oc=AdiK6xyn3f9rhAh5RUvNyPF_1lVwgpI7fvjAIku_rdZAAf_XeldRO7rpn7eGd9dBf75CPO4fsxIvpIIzPWkYGsNU&_nc_zt=23&_nc_ht=scontent.fcgy1-3.fna&_nc_gid=ACngSlc3OEVAckWpcnA64Qd&oh=00_AYAJ_1ftU1H9QIQAD22G7GW2wJbSDL3YrSKg33xbkkDVpA&oe=67ACD92D');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            text-align: center;
        }
        .btn-container {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .right-container {
            width: 500px;
        }
        .card {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        footer {
            margin-top: 20px;
            text-align: center;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Left Side: Logo & Buttons -->
        <div class="left-container">
            <div class="circle-logo"></div>
            <h3>Rainwater Catch Basin</h3>
            <div class="btn-container">
                <a href="{{ route('login') }}" class="btn btn-primary">Get Started</a>
            </div>
        </div>

        <!-- Right Side: Facebook Container -->
        <div class="right-container">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
                <div class="card-body">
                    <!-- Facebook Page Plugin -->
                    <div class="fb-page" 
                        data-href="https://www.facebook.com/NMACLRCfacebookpageofficial"
                        data-tabs="timeline"
                        data-width="500"
                        data-height="650"
                        data-small-header="false"
                        data-adapt-container-width="true"
                        data-hide-cover="false"
                        data-show-facepile="true">
                    </div>
                    
                    <!-- Facebook SDK -->
                    <script async defer crossorigin="anonymous" 
                        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v16.0">
                    </script>
                </div>
            </div>
        </div>
    </div>

    <footer>
        &copy; 2025 Rainwater Catch Basin. All rights reserved.
    </footer>
</body>
</html>
