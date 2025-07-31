import { Head, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';

export default function ForgotPassword({ status }) {
    const form = useForm({
        email: '2201105765@student.buksu.edu.ph'
    });

    const submit = (e) => {
        e.preventDefault();

        form.post(route('password.email'), {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Notification sent successfully');
            },
            onError: (errors) => {
                console.error('Error sending notification:', errors);
            }
        });
    };

    return (
        <>
            <Head>
                <title>Admin Password Reset - Botanical Gardens & Herbarium</title>
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

                .forgot-password-card {
                    background: rgba(255, 255, 255, 0.9);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.5);
                    box-shadow: 
                        0 20px 40px rgba(0, 0, 0, 0.1),
                        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
                    transform: translateY(0);
                    transition: all 0.3s ease;
                }

                .forgot-password-card:hover {
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
            `}</style>

            <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
                <div className="floating-leaves">
                    <div className="floating-leaf"></div>
                    <div className="floating-leaf"></div>
                    <div className="floating-leaf"></div>
                    <div className="floating-leaf"></div>
                </div>

                <div className="w-full sm:max-w-md mt-6 px-8 py-6 forgot-password-card sm:rounded-2xl overflow-hidden">
                    <div className="text-center mb-8">
                        <div className="flex items-center justify-center mb-4">
                            <i className="bi bi-shield-lock-fill text-4xl text-primary-green"></i>
                        </div>
                        <h1 className="text-2xl font-bold bg-gradient-to-r from-primary-green to-secondary-green bg-clip-text text-transparent">
                            Admin Password Reset
                        </h1>
                        <p className="mt-4 text-gray-600 leading-relaxed">
                            Need to reset the admin password? Click below to notify the system administrator.
                        </p>
                        <p className="mt-2 text-primary-green font-medium">
                            A notification will be sent to the administrator
                        </p>
            </div>

            {status && (
                        <div className="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-600 flex items-center gap-2 animate-fadeIn">
                            <i className="bi bi-check-circle"></i>
                            <span>{status}</span>
                        </div>
                    )}

                    {form.errors.email && (
                        <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 flex items-center gap-2">
                            <i className="bi bi-exclamation-circle"></i>
                            <span>{form.errors.email}</span>
                </div>
            )}

                    <form onSubmit={submit} className="space-y-6">
                        <div className="flex items-center justify-between gap-4">
                            <a
                                href={route('login')}
                                className="text-sm text-primary-green hover:text-secondary-green transition-colors duration-200 flex items-center gap-1"
                            >
                                <i className="bi bi-arrow-left"></i>
                                Back to Login
                            </a>

                            <button
                                type="submit"
                                disabled={form.processing}
                                className="submit-button px-6 py-3 rounded-xl text-white font-semibold 
                                         shadow-lg flex items-center justify-center gap-2
                                         disabled:opacity-50 disabled:cursor-not-allowed
                                         hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                            >
                                {form.processing ? (
                                    <>
                                        <i className="bi bi-arrow-repeat animate-spin"></i>
                                        <span>Sending Notification...</span>
                                    </>
                                ) : (
                                    <>
                                        <i className="bi bi-bell-fill"></i>
                                        <span>Request Password Reset</span>
                                    </>
                                )}
                            </button>
                        </div>
                    </form>

                    <div className="mt-8 p-4 bg-gray-50 rounded-xl">
                        <h3 className="text-sm font-semibold text-gray-700 mb-2">
                            <i className="bi bi-info-circle me-2"></i>
                            What happens next?
                        </h3>
                        <ol className="text-sm text-gray-600 space-y-2 list-inside">
                            <li>1. The administrator will receive a notification</li>
                            <li>2. They will verify and process your request</li>
                            <li>3. You will receive further instructions</li>
                            <li>4. Please allow some time for processing</li>
                        </ol>
                    </div>
                </div>
            </div>
        </>
    );
}
