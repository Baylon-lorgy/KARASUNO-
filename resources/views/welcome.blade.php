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
        }
        .main-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100vh;
            padding: 0 100px;
        }
        .left-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            margin-left: 400px;
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
        .btn-container {
            display: flex;
            gap: 10px;
        }
        .right-container {
            width: 500px;
        }
        .card {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Left Side: Logo & Buttons -->
        <div class="left-container">
            <div class="circle-logo"></div>
            <h3 class="text-white">Rainwater Catch Basin</h3>
            <div class="btn-container">
                <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
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
</body>
</html>
