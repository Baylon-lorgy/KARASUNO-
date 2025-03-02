<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Botanical Gardens & Herbarium</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-green: #4a7862;
            --secondary-green: #75a78b;
            --light-green: #c8e6d5;
            --color-sage: #E8F3E9;
            --color-mint: #DCE8E0;
            --color-sky: #E3EDF2;
            --color-lavender: #E9EDF5;
            --color-mist: rgba(255, 255, 255, 0.95);
            --leaf-shadow: rgba(74, 120, 98, 0.15);
            --error-red: #ef4444;
            --success-green: #10b981;
            --warning-yellow: #f59e0b;
        }

        body {
            background: 
                linear-gradient(
                    135deg,
                    var(--color-sage) 0%,
                    var(--color-mint) 25%,
                    var(--color-sky) 50%,
                    var(--color-lavender) 100%
                );
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
            position: relative;
            overflow-x: hidden;
            color: #374151;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(
                    rgba(255, 255, 255, 0.02) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255, 255, 255, 0.02) 1px,
                    transparent 1px
                );
            background-size: 20px 20px;
            opacity: 0.5;
            pointer-events: none;
            z-index: 0;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: var(--color-mist);
            backdrop-filter: blur(10px);
            border-radius: 1.25rem;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.08),
                0 2px 4px rgba(255, 255, 255, 0.1);
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.12),
                0 3px 8px rgba(255, 255, 255, 0.1);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-green), var(--light-green));
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--secondary-green);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: var(--primary-green);
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--secondary-green);
            box-shadow: 0 0 0 4px rgba(117, 167, 139, 0.15);
            background: white;
        }

        .form-input.error {
            border-color: var(--error-red);
            background: rgba(239, 68, 68, 0.05);
        }

        .form-input.error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        .error-message {
            color: var(--error-red);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            border-radius: 0.25rem;
            border: 2px solid var(--secondary-green);
            cursor: pointer;
        }

        .remember-me label {
            color: var(--secondary-green);
            font-size: 0.95rem;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px var(--leaf-shadow);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px var(--leaf-shadow);
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-password a {
            color: var(--secondary-green);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary-green);
            text-decoration: underline;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(145deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success-green);
        }

        .alert-error {
            background: linear-gradient(145deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--error-red);
        }

        .gif-container {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .gif-container::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: radial-gradient(circle, 
                rgba(200, 230, 213, 0.2) 0%,
                rgba(117, 167, 139, 0.1) 50%,
                transparent 70%);
            z-index: -1;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0.5;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 0.8;
            }
            100% {
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0.5;
            }
        }

        .circular-gif {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--light-green);
            box-shadow: 0 6px 16px var(--leaf-shadow);
            transition: all 0.3s ease;
            position: relative;
            animation: glow 2s ease-in-out infinite;
        }

        .circular-gif:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px var(--leaf-shadow),
                       0 0 30px rgba(117, 167, 139, 0.6);
        }

        @keyframes glow {
            0% {
                box-shadow: 0 6px 16px var(--leaf-shadow),
                           0 0 15px rgba(117, 167, 139, 0.3);
            }
            50% {
                box-shadow: 0 6px 16px var(--leaf-shadow),
                           0 0 30px rgba(117, 167, 139, 0.6),
                           0 0 45px rgba(117, 167, 139, 0.3);
            }
            100% {
                box-shadow: 0 6px 16px var(--leaf-shadow),
                           0 0 15px rgba(117, 167, 139, 0.3);
            }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .spin {
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @media (max-width: 640px) {
            .login-card {
                padding: 2rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem;
            }

            .btn-login {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Botanical Gardens & Herbarium</h1>
                <p>Welcome back! Please login to your account.</p>
            </div>

            <div class="gif-container">
                <img src="https://i.pinimg.com/originals/e8/88/d4/e888d4feff8fd5ff63a965471a94b874.gif" alt="Botanical Animation" class="circular-gif">
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-error">
                    <div class="font-medium">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        {{ __('Whoops! Something went wrong.') }}
                    </div>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope me-2"></i>Email Address
                    </label>
                    <input id="email" 
                           class="form-input" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="bi bi-lock me-2"></i>Password
                    </label>
                    <input id="password" 
                           class="form-input" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Enter your password">
                </div>

                <div class="remember-me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                        Log in
                    </button>

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">
                        <i class="bi bi-key me-1"></i>
                        Forgot your password?
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            // Add error class and message
            function showError(input, message) {
                input.classList.add('error');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i>${message}`;
                
                // Remove existing error message if any
                const existingError = input.parentElement.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                input.parentElement.appendChild(errorDiv);
            }
            
            // Remove error class and message
            function removeError(input) {
                input.classList.remove('error');
                const errorDiv = input.parentElement.querySelector('.error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
            
            // Validate email format
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            // Real-time email validation
            emailInput.addEventListener('input', function() {
                if (this.value.length > 0 && !validateEmail(this.value)) {
                    showError(this, 'Please enter a valid email address');
                } else {
                    removeError(this);
                }
            });
            
            // Real-time password validation
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 6) {
                    showError(this, 'Password must be at least 6 characters');
                } else {
                    removeError(this);
                }
            });
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                let hasError = false;
                
                // Validate email
                if (!emailInput.value) {
                    showError(emailInput, 'Email is required');
                    hasError = true;
                } else if (!validateEmail(emailInput.value)) {
                    showError(emailInput, 'Please enter a valid email address');
                    hasError = true;
                }
                
                // Validate password
                if (!passwordInput.value) {
                    showError(passwordInput, 'Password is required');
                    hasError = true;
                } else if (passwordInput.value.length < 6) {
                    showError(passwordInput, 'Password must be at least 6 characters');
                    hasError = true;
                }
                
                if (hasError) {
                    e.preventDefault();
                }
            });

            // Add loading state to login button
            form.addEventListener('submit', function() {
                const button = this.querySelector('.btn-login');
                button.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Logging in...';
                button.disabled = true;
            });

            // Handle alert dismissal
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>
</body>
</html> 