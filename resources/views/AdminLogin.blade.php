<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Poppins font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Apply Poppins font to all elements */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Main background */
        .gradient-form {
            background: linear-gradient(to right, rgba(255, 241, 235, 0.5), rgba(172, 224, 249, 0.5));
        }

        /* Right-side background */
        .gradient-custom-2 {
            background: rgb(12,125,148);
            background: linear-gradient(90deg, rgba(12,125,148,1) 0%, rgba(9,9,121,1) 52%, rgba(2,0,36,1) 100%);
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }

        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }

        .form-label {
            padding-right: 325px;
        }

        /* Make the image circular with shadow */
        .Logo img {
            width: 150px; /* Adjust the size as needed */
            height: 150px; /* Ensure width and height are equal */
            border-radius: 50%; /* Makes the image circular */
            object-fit: cover; /* Ensures the image covers the area without distortion */
            display: block; /* Ensures the image is centered */
            margin: 0 auto; /* Centers the image horizontally */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); /* Shadow added */
        }

        .Register {
            color: blue;
        }

        /* Add shadow to login card */
        .card {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); /* Soft shadow */
        }

        /* Add shadow to login button */
        .btn-primary {
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>

<section class="h-100 gradient-form">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <!-- Left Column (Wider) -->
            <div class="col-lg-8">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <div class="Logo">
                    <!-- Circular Image -->
                    <img src="https://scontent.fcgy1-3.fna.fbcdn.net/v/t39.30808-6/466458716_869630271997402_9083455242095747570_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=twa2sYRZSv8Q7kNvgEIVIQt&_nc_zt=23&_nc_ht=scontent.fcgy1-3.fna&_nc_gid=AnLICNg_XXqjYeqDaQ6WL4_&oh=00_AYACF0oAie-yHqNR4PTsfht2kZfNsOquvKu97cuquBtsow&oe=679FE26D"
                      alt="logo">
                  </div>
                  <h2 class="mt-1 mb-5 pb-1">Welcome!</h2>
                </div>

                <form>
                  <p>Please login to your account</p>

                  <div class="form-outline mb-4">
                    <label class="form-label" for="form2Example11">Username</label>  
                    <input type="email" id="form2Example11" class="form-control"
                      placeholder="Phone number or email address" />
                  </div>

                  <div class="form-outline mb-4">
                    <label class="form-label" for="form2Example22">Password</label> 
                    <a class="text-muted" href="#">Forgot password?</a> 
                    <input type="password" id="form2Example22" class="form-control" placeholder="Password" />
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                    <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="button">Log in</button>
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Don't have an account?</p>
                    <a class="Register" href="#">Register</a> 
                  </div>
                </form>
              </div>
            </div>

            <!-- Right Column (Smaller) -->
            <div class="col-lg-4 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <!-- Content for the right side -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Bootstrap JS (with Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>