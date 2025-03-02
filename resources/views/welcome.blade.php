<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Botanical Garden & Herbarium - Rainwater Catch Basin</title>
    
    <!-- Facebook SDK -->
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0" nonce="random_nonce"></script>
    
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Background and Container Styles */
        :root {
            --color-sage: #E8F3E9;
            --color-mint: #DCE8E0;
            --color-sky: #E3EDF2;
            --color-mist: rgba(255, 255, 255, 0.95);
            --primary-color: #1B5E20;
            --secondary-color: #388E3C;
            --accent-color: #81C784;
            --success-color: #66BB6A;
            --warning-color: #FFA726;
            --danger-color: #EF5350;
            --background-light: #F8FAF8;
            --text-primary: #2F3B4C;
            --text-secondary: #546E7A;
            --border-radius: 1rem;
            --transition: all 0.3s ease;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.03);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.05);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.07);
            --gradient-primary: linear-gradient(135deg, #1B5E20, #388E3C);
            --gradient-secondary: linear-gradient(135deg, #388E3C, #81C784);
            --gradient-accent: linear-gradient(135deg, #81C784, #A5D6A7);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, 
                var(--color-sage) 0%,
                var(--color-mint) 50%,
                var(--color-sky) 100%
            );
            color: var(--text-primary);
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/assets/img/pattern.svg') repeat;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }

        .content-container {
            position: relative;
            z-index: 2;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(129, 199, 132, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            color: var(--text-primary);
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn-login {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0.625rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.2);
            color: white;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-secondary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login span {
            position: relative;
            z-index: 1;
        }

        /* Hero Section */
        .hero {
            position: relative;
            padding: 8rem 0 6rem;
            background: linear-gradient(135deg, 
                rgba(27, 94, 32, 0.05) 0%,
                rgba(56, 142, 60, 0.05) 50%,
                rgba(129, 199, 132, 0.05) 100%
            );
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('/assets/img/pattern.svg') repeat;
            opacity: 0.05;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 600px;
            line-height: 1.8;
        }

        /* Add styles for Facebook updates container */
        .social-updates {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin: 0 auto;
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(129, 199, 132, 0.2);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .social-updates::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .social-updates:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent-color);
        }

        .fb-page-container {
            margin-top: 1rem;
            width: 100%;
            min-height: 450px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow: hidden;
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.5);
            padding: 0.5rem;
        }

        .fb-page {
            background: transparent;
            border-radius: 8px;
            width: 100%;
        }

        .social-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(129, 199, 132, 0.1);
        }

        .social-header i {
            font-size: 1.75rem;
            color: var(--primary-color);
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .social-title {
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #1877F2;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .social-link:hover {
            transform: translateX(5px);
            color: var(--primary-color);
        }

        /* Features Section */
        .features {
            padding: 6rem 0;
            background: linear-gradient(180deg, 
                rgba(255,255,255,1) 0%,
                rgba(248,250,248,1) 100%
            );
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: var(--border-radius);
            padding: 2.5rem 2rem;
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(129, 199, 132, 0.1);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(12px);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            border-color: rgba(129, 199, 132, 0.2);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            box-shadow: 0 4px 15px rgba(27, 94, 32, 0.15);
        }

        .feature-icon::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            bottom: -2px;
            left: -2px;
            background: var(--gradient-secondary);
            border-radius: 50%;
            z-index: -1;
            opacity: 0;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon::after {
            opacity: 1;
        }

        .feature-icon i {
            font-size: 2rem;
            color: white;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .feature-description {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, 
                var(--text-primary) 0%,
                #1B5E20 100%
            );
            color: white;
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(45deg, 
                rgba(129, 199, 132, 0.05) 0%,
                rgba(56, 142, 60, 0.05) 100%
            );
            opacity: 0.1;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-link {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            padding-bottom: 0.25rem;
        }

        .footer-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-accent);
            transition: var(--transition);
        }

        .footer-link:hover {
            color: white;
            transform: translateY(-2px);
        }

        .footer-link:hover::after {
            width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 3rem;
            }
            
            .hero-subtitle {
                font-size: 1.125rem;
            }
            
            .feature-card {
                margin-bottom: 2rem;
            }
            
            .social-updates {
                margin-top: 2rem;
                width: 100%;
                max-width: 100%;
            }
            
            .fb-page-container {
                min-height: 400px;
            }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 4rem 0;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .social-updates {
                margin-top: 1.5rem;
            }
            
            .fb-page-container {
                min-height: 350px;
            }
            
            .social-title {
                font-size: 1.25rem;
            }
        }

        /* Add smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Enhance animations */
        .animate-fadeInUp {
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Card Styles */
        .card {
            background: var(--color-mist);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.15),
                0 3px 8px rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-flower1"></i>
                <span>Botanical Garden & Herbarium</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="navbar-nav">
                    <a href="{{ route('admin.login') }}" class="btn btn-login">
                        <span>Admin Login</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content animate-fadeInUp">
                    <h1 class="hero-title">Rainwater <br>Catch Basin</h1>
                    <p class="hero-subtitle">Experience the beauty of nature with our smart garden management system. Monitor, maintain, and nurture your garden with precision and care.</p>
                    <a href="{{ route('admin.login') }}" class="btn btn-login">
                        <span>Get Started</span>
                    </a>
                </div>
                <div class="col-lg-6 animate-fadeInUp" style="animation-delay: 0.3s">
                    <div class="social-updates">
                        <div class="social-header">
                            <i class="bi bi-flower1"></i>
                            <h3 class="social-title">BukSU BGH Community</h3>
                        </div>
                        <div class="fb-page-container">
                            <div class="fb-page" 
                                data-href="https://www.facebook.com/profile.php?id=100068682045391"
                                data-tabs="timeline"
                                data-width="500"
                                data-height="450"
                                data-small-header="true"
                                data-adapt-container-width="true"
                                data-hide-cover="false"
                                data-show-facepile="true">
                                <blockquote cite="https://www.facebook.com/profile.php?id=100068682045391" class="fb-xfbml-parse-ignore">
                                    <a href="https://www.facebook.com/profile.php?id=100068682045391">BukSU Botanical Garden and Herbarium</a>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-title animate-fadeInUp">
                <h2>Smart Garden Features</h2>
                <p class="text-muted">Discover the power of intelligent garden management</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 animate-fadeInUp" style="animation-delay: 0.2s">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <h3 class="feature-title">Smart Irrigation</h3>
                        <p class="feature-description">Automated watering system that ensures optimal moisture levels for your plants.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate-fadeInUp" style="animation-delay: 0.4s">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3 class="feature-title">Real-time Monitoring</h3>
                        <p class="feature-description">Track environmental conditions and plant health with advanced sensors.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate-fadeInUp" style="animation-delay: 0.6s">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h3 class="feature-title">Scheduling System</h3>
                        <p class="feature-description">Create and manage watering schedules for different zones of your garden.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <a href="#" class="footer-brand">
                    <i class="bi bi-flower1"></i>
                    Botanical Garden
                </a>
                <div class="footer-links">
                    <a href="#" class="footer-link">About Us</a>
                    <a href="#" class="footer-link">Contact</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                </div>
                <p class="mb-0">&copy; {{ date('Y') }} Botanical Garden & Herbarium. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
