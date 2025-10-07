import { useEffect, useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';

export default function Login({ status, canResetPassword, csrf_token }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
        _token: csrf_token,
    });

    const [showPassword, setShowPassword] = useState(false);

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const validateEmail = (email) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    };

    const submit = (e) => {
        e.preventDefault();

        // Client-side validation
        if (!data.email) {
            setData('errors', { email: 'Email is required' });
            return;
        }

        if (!validateEmail(data.email)) {
            setData('errors', { email: 'Please enter a valid email address' });
            return;
        }

        if (!data.password) {
            setData('errors', { password: 'Password is required' });
            return;
        }

        // Use Inertia's post method with CSRF token
        post(route('login'), {
            preserveScroll: true,
            onSuccess: () => {
                reset('password');
            },
        });
    };

    return (
        <>
            <Head>
                <title>Admin Login - Botanical Gardens & Herbarium</title>
                <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
            </Head>

            <style jsx="true" global="true">{`
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
                }

                body {
                    background: linear-gradient(to top,rgb(197, 210, 226) 0%,rgb(176, 212, 240) 100%);
                    min-height: 100vh;
                    font-family: 'Figtree', sans-serif;
                }

                @keyframes float {
                    0%, 100% { transform: translateY(0) rotate(0deg); }
                    50% { transform: translateY(-10px) rotate(5deg); }
                }

                @keyframes pulse {
                    0% { transform: scale(0.95); opacity: 0.5; }
                    50% { transform: scale(1.05); opacity: 0.8; }
                    100% { transform: scale(0.95); opacity: 0.5; }
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

                .floating-leaves {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    pointer-events: none;
                    z-index: 0;
                }

                .floating-leaf {
                    position: absolute;
                    width: 40px;
                    height: 40px;
                    background: linear-gradient(45deg, var(--primary-green), var(--secondary-green));
                    opacity: 0.1;
                    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                    animation: float 6s infinite;
                }

                .floating-leaf:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
                .floating-leaf:nth-child(2) { top: 20%; right: 10%; animation-delay: 1s; }
                .floating-leaf:nth-child(3) { bottom: 10%; left: 15%; animation-delay: 2s; }
                .floating-leaf:nth-child(4) { bottom: 20%; right: 15%; animation-delay: 3s; }

                .login-card {
                    background: rgba(255, 255, 255, 0.9);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.5);
                    box-shadow: 
                        0 20px 40px rgba(0, 0, 0, 0.1),
                        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
                    transform: translateY(0);
                    transition: all 0.3s ease;
                }

                .login-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 
                        0 25px 50px rgba(0, 0, 0, 0.15),
                        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
                }

                .input-group {
                    position: relative;
                    margin-bottom: 1.5rem;
                }

                .input-icon {
                    position: absolute;
                    left: 1rem;
                    top: 50%;
                    transform: translateY(-50%);
                    color: var(--primary-green);
                    transition: all 0.3s ease;
                }

                .form-input {
                    padding-left: 2.75rem !important;
                    border-radius: 12px !important;
                    transition: all 0.3s ease;
                }

                .form-input:focus {
                    border-color: var(--primary-green) !important;
                    box-shadow: 0 0 0 3px var(--leaf-shadow) !important;
                }

                .form-input:focus + .input-icon {
                    transform: translateY(-50%) scale(1.1);
                }

                .submit-button {
                    background: linear-gradient(135deg, #09203f, #537895);
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }

                .submit-button::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                    transform: translateX(-100%);
                    transition: transform 0.6s ease;
                }

                .submit-button:hover::after {
                    transform: translateX(100%);
                }

                .gif-container {
                    position: relative;
                    width: 32px;
                    height: 32px;
                    margin: 0 auto;
                    margin-bottom: 2rem;
                }

                .gif-glow {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 150%;
                    height: 150%;
                    transform: translate(-50%, -50%);
                    background: radial-gradient(circle, rgba(200, 230, 213, 0.2) 0%, rgba(117, 167, 139, 0.1) 50%, transparent 70%);
                    animation: pulse 3s ease-in-out infinite;
                }

                .error-popup {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #fee2e2;
                    border: 1px solid #ef4444;
                    padding: 1rem 1.5rem;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                    z-index: 50;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    animation: slideIn 0.3s ease-out;
                }

                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }

                .error-popup i {
                    color: #dc2626;
                    font-size: 1.25rem;
                }

                .error-popup span {
                    color: #7f1d1d;
                    font-size: 0.875rem;
                    font-weight: 500;
                }
            `}</style>

            <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
                {errors.error && (
                    <div className="error-popup">
                        <i className="bi bi-exclamation-circle"></i>
                        <span>{errors.error}</span>
                    </div>
                )}

                <div className="floating-leaves">
                    <div className="floating-leaf"></div>
                    <div className="floating-leaf"></div>
                    <div className="floating-leaf"></div>
                    <div className="floating-leaf"></div>
                </div>

                <div className="w-full sm:max-w-md mt-6 px-8 py-6 login-card sm:rounded-2xl overflow-hidden">
                    <div className="text-center mb-8">
                        <div className="flex items-center justify-center mb-4">
                            <i className="bi bi-shield-lock-fill text-4xl text-primary-green"></i>
                        </div>
                        <h1 className="text-2xl font-bold bg-gradient-to-r from-primary-green to-secondary-green bg-clip-text text-transparent">
                            Welcome Back
                        </h1>
                        <p className="mt-2 text-gray-600">Please sign in to continue</p>
                    </div>

                    {/* Circular GIF */}
                    <div className="relative w-32 h-32 mx-auto mb-8">
                        <div className="absolute top-1/2 left-1/2 w-36 h-36 -translate-x-1/2 -translate-y-1/2"
                             style={{
                                 background: 'radial-gradient(circle, rgba(200, 230, 213, 0.2) 0%, rgba(117, 167, 139, 0.1) 50%, transparent 70%)',
                                 animation: 'pulse 2s ease-in-out infinite'
                             }} />
                        <img
                            src="https://i.pinimg.com/originals/e8/88/d4/e888d4feff8fd5ff63a965471a94b874.gif"
                            alt="Botanical Animation"
                            className="w-32 h-32 rounded-full object-cover border-3 border-[#c8e6d5]"
                            style={{
                                animation: 'glow 2s ease-in-out infinite',
                                boxShadow: '0 6px 16px rgba(74, 120, 98, 0.15)'
                            }}
                        />
                    </div>

                    <form onSubmit={submit} className="space-y-6">
                        <div className="input-group">
                            <input
                                type="email"
                                name="email"
                                value={data.email}
                                className="form-input w-full px-4 py-3 bg-white/80 border-2 border-gray-200"
                                placeholder="Email Address"
                                onChange={e => setData('email', e.target.value)}
                            />
                            <i className="bi bi-envelope-fill input-icon"></i>
                        </div>

                        <div className="input-group">
                            <input
                                type={showPassword ? 'text' : 'password'}
                                name="password"
                                value={data.password}
                                className="form-input w-full px-4 pr-12 py-3 bg-white/80 border-2 border-gray-200"
                                placeholder="Password"
                                onChange={e => setData('password', e.target.value)}
                            />
                            <button
                                type="button"
                                aria-label={showPassword ? 'Hide password' : 'Show password'}
                                onClick={() => setShowPassword(!showPassword)}
                                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <i className={showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'}></i>
                            </button>
                            <i className="bi bi-lock-fill input-icon"></i>
                        </div>

                        <div className="flex items-center justify-between">
                            <label className="flex items-center gap-2 cursor-pointer group">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                    className="w-4 h-4 rounded border-2 border-gray-300 text-primary-green focus:ring-primary-green"
                                />
                                <span className="text-sm text-gray-600 group-hover:text-gray-800">Remember me</span>
                            </label>

                            {canResetPassword && (
                                <Link
                                    href={route('password.request')}
                                    className="text-sm text-primary-green hover:text-secondary-green transition-colors"
                                >
                                    Forgot password?
                                </Link>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="submit-button w-full py-3 rounded-xl text-white font-semibold 
                                     shadow-lg flex items-center justify-center gap-2
                                     disabled:opacity-50 disabled:cursor-not-allowed
                                     hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                        >
                            {processing ? (
                                <>
                                    <i className="bi bi-arrow-repeat animate-spin"></i>
                                    <span>Signing in...</span>
                                </>
                            ) : (
                                <>
                                    <i className="bi bi-box-arrow-in-right"></i>
                                    <span>Sign In</span>
                                </>
                            )}
                        </button>
                    </form>
                </div>
            </div>
        </>
    );
}
