<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raincatch Water Basin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@1,300&display=swap');

        body {
            background: linear-gradient(to right, rgba(255, 241, 235, 0.5), rgba(172, 224, 249, 0.5));
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 15px;
            display: flex;
            justify-content: center; /* Keep it centered */
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            margin-left: 0%; /* Adjust this value to move navbar to the right */
        }

        .navbar .navbar-container {
            display: flex;
            justify-content: flex-end; /* Keep links aligned to the right */
            width: 100%;
            max-width: 1200px; /* Optional: Adjust the max width */
        }

        .navbar a {
            color: black;
            text-decoration: none;
            margin: 0 20px; /* Space between links */
            font-size: 18px;
            font-style: italic;
            transition: color 0.3s ease-in-out;
        }

        .navbar a:hover {
            color: #555;
        }

        /* Main Container */
        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100vh;
            padding: 20px;
            text-align: center;
            flex-wrap: wrap;
        }

        .left {
            flex: 1;
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            justify-content: center; /* Center vertically */
            align-items: center;     /* Center horizontally */
            text-align: center;      /* Ensure the text is centered */
            padding-right: 20px;
        }

        .left .logo {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background-color: #007BFF;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .left .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        /* Updated Button */
        .get-started {
            margin-top: 20px;
            padding: 12px 25px;
            font-size: 18px;
            color: white;
            background-color: #6a1b9a; /* Initial gradient color */
            border: none;
            cursor: pointer;
            border-radius: 30px;
            box-shadow: 0 0 15px rgba(106, 27, 154, 0.7);
            transition: background 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        /* Glowing Gradient Effect */
        .get-started:hover {
            background: linear-gradient(45deg, #6a1b9a, #9c27b0, #ba68c8);
            box-shadow: 0 0 25px rgba(106, 27, 154, 1), 0 0 40px rgba(156, 39, 176, 1);
        }

        .get-started:active {
            box-shadow: 0 0 20px rgba(106, 27, 154, 1), 0 0 35px rgba(156, 39, 176, 1);
        }

        /* Right Section (Facebook Embed Box) */
        .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .box {
            width: 100%;
            max-width: 500px;
            padding: 30px;  /* Increased padding for height */
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: 700px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-container">
            <a href="#">About Us</a>
            <a href="#">Contact Us</a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Left Section -->
        <div class="left">
            <div class="logo">
                <img src="https://scontent.fcgy1-3.fna.fbcdn.net/v/t39.30808-6/466458716_869630271997402_9083455242095747570_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=twa2sYRZSv8Q7kNvgEIVIQt&_nc_zt=23&_nc_ht=scontent.fcgy1-3.fna&_nc_gid=AnLICNg_XXqjYeqDaQ6WL4_&oh=00_AYACF0oAie-yHqNR4PTsfht2kZfNsOquvKu97cuquBtsow&oe=679FE26D" alt="Logo">
            </div>
            <h1>Rainwater Catch Basin</h1>
            <!-- Replaced Button -->
            <button type="button" class="btn btn-dark btn-rounded get-started" data-mdb-ripple-init>Get Started</button>
        </div>

        <!-- Right Section -->
        <div class="right">
            <div class="box">
                <div class="fb-page" 
                    data-href="https://www.facebook.com/NMACLRCfacebookpageofficial/"
                    data-tabs="timeline"
                    data-width="500"
                    data-height="700"
                    data-small-header="false"
                    data-adapt-container-width="true"
                    data-hide-cover="false"
                    data-show-facepile="true">
                </div>
            </div>
        </div>
    </div>

    <!-- Facebook SDK Script (Required for Facebook Plugin to Work) -->
    <script async defer crossorigin="anonymous" 
        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v17.0">
    </script>

</body>
</html>
