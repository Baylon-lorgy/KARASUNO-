<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Password Confirmation - Rainwater Catch Basin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(to right, #636FA4, #E8CBC0);
        }
        .card-header img {
            display: block;
            margin: 0 auto 10px;
        }
        .card-header h4 {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
                    <div class="container-fluid ps-2 pe-0">
                        <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="#">Rainwater Catch Basin</a>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center" style="background-image: url('../assets/img/illustrations/illustration-signup.jpg'); background-size: cover;">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
                            <div class="card card-plain">
                                <div class="card-body">
                                    <img src="https://your-logo-url-here.com/logo.png" alt="Logo" width="100">
                                    <h4 class="font-weight-bolder text-black">Rainwater Catch Basin</h4>
                                    <p class="text-sm text-gray-600">This is a secure area of the application. Please confirm your password before continuing.</p>
                                    <form method="POST" action="{{ route('password.confirm') }}">
                                        @csrf
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" id="password" name="password" class="form-control" required>
                                        </div>
                                        @error('password')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg bg-gradient-dark w-100 mt-4 mb-0">Confirm</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>
