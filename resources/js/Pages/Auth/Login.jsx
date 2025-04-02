import { useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), {
            onSuccess: () => {
                window.location.href = '/admin/dashboard';
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

            <style jsx global>{`
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
                    background: linear-gradient(
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
                    background: linear-gradient(
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

                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
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
            `}</style>

            <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
                <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white/95 backdrop-blur-xl shadow-xl sm:rounded-2xl overflow-hidden relative animate-[fadeInUp_0.5s_ease-out]"
                     style={{
                         boxShadow: '0 8px 32px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(255, 255, 255, 0.1)',
                         transform: 'translateY(0)',
                         transition: 'all 0.3s ease'
                     }}>
                    
                    {/* Top Gradient Border */}
                    <div className="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#4a7862] via-[#75a78b] to-[#c8e6d5]" />

                    <div className="text-center mb-8">
                        <h1 className="text-2xl font-semibold bg-gradient-to-r from-[#4a7862] to-[#75a78b] bg-clip-text text-transparent">
                            Botanical Gardens & Herbarium
                        </h1>
                        <p className="mt-2 text-[#75a78b]">Welcome back! Please login to your account.</p>
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

                    {status && (
                        <div className="mb-4 p-4 bg-gradient-to-r from-[rgba(16,185,129,0.1)] to-[rgba(16,185,129,0.05)] border border-[rgba(16,185,129,0.2)] rounded-xl text-[#10b981] flex items-center gap-2 animate-[fadeInUp_0.3s_ease-out]">
                            <i className="bi bi-check-circle" />
                            <span>{status}</span>
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        <div>
                            <label className="block text-[#4a7862] font-medium mb-2" htmlFor="email">
                                <i className="bi bi-envelope me-2" />Email Address
                            </label>
                            <input
                                id="email"
                                type="email"
                                value={data.email}
                                className="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-[#75a78b] focus:ring focus:ring-[rgba(117,167,139,0.15)] bg-white/80 focus:bg-white transition-all duration-300"
                                autoComplete="username"
                                onChange={(e) => setData('email', e.target.value)}
                            />
                            <InputError message={errors.email} className="mt-2 text-[#ef4444] text-sm flex items-center gap-1">
                                <i className="bi bi-exclamation-circle" />
                            </InputError>
                        </div>

                        <div>
                            <label className="block text-[#4a7862] font-medium mb-2" htmlFor="password">
                                <i className="bi bi-lock me-2" />Password
                            </label>
                            <input
                                id="password"
                                type="password"
                                value={data.password}
                                className="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-[#75a78b] focus:ring focus:ring-[rgba(117,167,139,0.15)] bg-white/80 focus:bg-white transition-all duration-300"
                                autoComplete="current-password"
                                onChange={(e) => setData('password', e.target.value)}
                            />
                            <InputError message={errors.password} className="mt-2 text-[#ef4444] text-sm flex items-center gap-1">
                                <i className="bi bi-exclamation-circle" />
                            </InputError>
                        </div>

                        <div className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                className="w-4 h-4 rounded border-2 border-[#75a78b] text-[#4a7862] focus:ring-[#75a78b]"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                            />
                            <label htmlFor="remember" className="text-[#75a78b]">
                                Remember me
                            </label>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-3.5 bg-gradient-to-r from-[#4a7862] to-[#75a78b] text-white rounded-xl font-medium shadow-lg shadow-[rgba(74,120,98,0.15)] hover:translate-y-[-2px] hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            {processing ? (
                                <>
                                    <i className="bi bi-arrow-repeat animate-spin" />
                                    Logging in...
                                </>
                            ) : (
                                <>
                                    <i className="bi bi-box-arrow-in-right" />
                                    Log in
                                </>
                            )}
                        </button>

                        {canResetPassword && (
                            <div className="text-center">
                                <Link
                                    href={route('password.request')}
                                    className="text-[#75a78b] hover:text-[#4a7862] transition-colors duration-300 flex items-center justify-center gap-1"
                                >
                                    <i className="bi bi-key" />
                                    Forgot your password?
                                </Link>
                            </div>
                        )}
                    </form>
                </div>
            </div>
        </>
    );
}
