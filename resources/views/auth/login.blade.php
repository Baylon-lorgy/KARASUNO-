<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Rainwater Catch Basin</title>
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
                                    
                                    <img src="https://scontent.fcgy1-3.fna.fbcdn.net/v/t39.30808-6/466458716_869630271997402_9083455242095747570_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=dNEe5EZGq5gQ7kNvgHUbXVU&_nc_oc=AdiK6xyn3f9rhAh5RUvNyPF_1lVwgpI7fvjAIku_rdZAAf_XeldRO7rpn7eGd9dBf75CPO4fsxIvpIIzPWkYGsNU&_nc_zt=23&_nc_ht=scontent.fcgy1-3.fna&_nc_gid=ACngSlc3OEVAckWpcnA64Qd&oh=00_AYAJ_1ftU1H9QIQAD22G7GW2wJbSDL3YrSKg33xbkkDVpA&oe=67ACD92D" alt="Logo" width="100">
                                    <h4 class="font-weight-bolder text-black">Rainwater Catch Basin</h4>

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" id="email" name="email" class="form-control" required autofocus>
                                        </div>
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" id="password" name="password" class="form-control" required>
                                        </div>
                                        <div class="form-check form-check-info text-start ps-0">
                                            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                            <label class="form-check-label text-white" for="remember_me">Remember Me</label>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg bg-gradient-dark w-100 mt-4 mb-0">Log in</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    @if (Route::has('password.request'))
                                    <p class="mb-2 text-sm">
                                        <a href="{{ route('password.request') }}" class="text-primary text-gradient font-weight-bold">Forgot your password?</a>
                                    </p>
                                    @endif
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
