import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import Modal from '@/Components/Modal';
import AboutModal from '@/Components/AboutModal';
import ContactModal from '@/Components/ContactModal';
import PrivacyModal from '@/Components/PrivacyModal';
import { Bars3Icon, XMarkIcon } from '@heroicons/react/24/outline';

export default function Welcome({ auth }) {
    const [showAboutModal, setShowAboutModal] = useState(false);
    const [showContactModal, setShowContactModal] = useState(false);
    const [showPrivacyModal, setShowPrivacyModal] = useState(false);
    const [fbLoaded, setFbLoaded] = useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    useEffect(() => {
        // Load Facebook SDK
        window.fbAsyncInit = function() {
            FB.init({
                appId: 'YOUR_APP_ID', // Replace with your actual Facebook App ID
                autoLogAppEvents: true,
                xfbml: true,
                version: 'v18.0'
            });
            setFbLoaded(true);
        };

        // Load the SDK asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        return () => {
            // Cleanup
            delete window.fbAsyncInit;
        };
    }, []);

    return (
        <>
            <Head>
                <title>Botanical Garden & Herbarium - Rainwater Catch Basin</title>
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            </Head>

            <style jsx global>{`
                :root {
                    --color-sage: #E8F3E9;
                    --color-mint: #DCE8E0;
                    --color-sky: #E3EDF2;
                    --color-mist: rgba(255, 255, 255, 0.95);
                    --primary-color: #09203f;
                    --secondary-color: #537895;
                    --accent-color: #81C784;
                    --text-primary: #2F3B4C;
                    --text-secondary: #546E7A;
                    --transition: all 0.3s ease;
                }

                body {
                    font-family: 'Outfit', sans-serif;
                    background: linear-gradient(to top,rgb(155, 155, 223) 0%,rgb(178, 215, 243) 100%);
                    color: var(--text-primary);
                    min-height: 100vh;
                }

                @media (max-width: 640px) {
                    .hero-title {
                        font-size: 2rem;
                        line-height: 1.2;
                    }
                    
                    .hero-subtitle {
                        font-size: 1rem;
                        line-height: 1.5;
                    }
                }
            `}</style>

            {/* Navigation */}
            <nav className="nav-mobile">
                <div className="container-responsive">
                    <div className="flex justify-between items-center py-4">
                        <Link href="/" className="flex items-center space-x-2 text-xl sm:text-2xl font-semibold bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent">
                            <svg viewBox="0 0 24 24" className="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor">
                                <path d="M12,2 A10,10 0 0,1 12,22 A10,10 0 0,1 12,2 M12,5 A3,3 0 0,0 9,8 A3,3 0 0,0 12,11 A3,3 0 0,0 15,8 A3,3 0 0,0 12,5 M12,8 A3,3 0 0,0 15,11 A3,3 0 0,0 18,8 A3,3 0 0,0 15,5 A3,3 0 0,0 12,8 M12,8 A3,3 0 0,1 9,11 A3,3 0 0,1 6,8 A3,3 0 0,1 9,5 A3,3 0 0,1 12,8 M12,11 A3,3 0 0,1 15,14 A3,3 0 0,1 12,17 A3,3 0 0,1 9,14 A3,3 0 0,1 12,11" />
                            </svg>
                            <i className="bi bi-flower1 text-xl sm:text-2xl text-[#09203f]"></i>
                            <span className="hidden sm:inline">RainBasin Pro</span>
                            <span className="sm:hidden">RainBasin</span>
                        </Link>
                        
                        {/* Desktop Navigation */}
                        <div className="hidden md:flex items-center space-x-4">
                            {auth?.user ? (
                                <Link
                                    href={route('admin.dashboard')}
                                    className="btn-responsive bg-gradient-to-r from-[#09203f] to-[#537895] text-white hover:-translate-y-0.5 hover:shadow-lg"
                                >
                                    <i className="bi bi-grid-1x2-fill mr-2"></i>
                                    <span className="hidden sm:inline">Dashboard</span>
                                    <span className="sm:hidden">Dash</span>
                                </Link>
                            ) : (
                                <Link
                                    href={route('login')}
                                    className="btn-responsive bg-gradient-to-r from-[#09203f] to-[#537895] text-white hover:-translate-y-0.5 hover:shadow-lg"
                                >
                                    <i className="bi bi-box-arrow-in-right mr-2"></i>
                                    <span className="hidden sm:inline">Admin Login</span>
                                    <span className="sm:hidden">Login</span>
                                </Link>
                            )}
                        </div>

                        {/* Mobile menu button */}
                        <button
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="md:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                        >
                            {mobileMenuOpen ? (
                                <XMarkIcon className="h-6 w-6" />
                            ) : (
                                <Bars3Icon className="h-6 w-6" />
                            )}
                        </button>
                    </div>

                    {/* Mobile Navigation Menu */}
                    {mobileMenuOpen && (
                        <div className="md:hidden py-4 border-t border-green-100 animate-slide-down">
                            <div className="space-y-3">
                                {auth?.user ? (
                                    <Link
                                        href={route('admin.dashboard')}
                                        onClick={() => setMobileMenuOpen(false)}
                                        className="block w-full text-center btn-responsive bg-gradient-to-r from-[#09203f] to-[#537895] text-white hover:-translate-y-0.5 hover:shadow-lg"
                                    >
                                        <i className="bi bi-grid-1x2-fill mr-2"></i>
                                        Dashboard
                                    </Link>
                                ) : (
                                    <Link
                                        href={route('login')}
                                        onClick={() => setMobileMenuOpen(false)}
                                        className="block w-full text-center btn-responsive bg-gradient-to-r from-[#09203f] to-[#537895] text-white hover:-translate-y-0.5 hover:shadow-lg"
                                    >
                                        <i className="bi bi-box-arrow-in-right mr-2"></i>
                                        Admin Login
                                    </Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </nav>

            {/* Hero Section */}
            <section className="pt-24 sm:pt-32 pb-12 sm:pb-20 relative overflow-hidden">
                <div className="container-responsive">
                    <div className="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                        <div className="space-y-4 sm:space-y-6 animate-fade-in text-center lg:text-left">
                            <h1 className="heading-responsive leading-tight bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent hero-title">
                                Botanical Garden & Herbarium
                            </h1>
                            <p className="text-responsive text-gray-600 max-w-xl mx-auto lg:mx-0 hero-subtitle">
                                Experience the beauty of nature with our smart garden management system. Monitor, maintain, and nurture your garden with precision and care.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                <Link
                                    href={auth?.user ? route('admin.dashboard') : route('login')}
                                    className="btn-responsive bg-gradient-to-r from-[#09203f] to-[#537895] text-white hover:-translate-y-0.5 hover:shadow-lg"
                                >
                                    <i className="bi bi-arrow-right mr-2"></i>
                                    Get Started
                                </Link>
                                <button
                                    onClick={() => setShowAboutModal(true)}
                                    className="btn-responsive bg-white/90 backdrop-blur-sm text-gray-700 border border-gray-200 hover:bg-gray-50 hover:-translate-y-0.5 hover:shadow-lg"
                                >
                                    <i className="bi bi-info-circle mr-2"></i>
                                    Learn More
                                </button>
                            </div>
                        </div>
                        <div className="relative animate-fade-in animation-delay-200">
                            <div className="card-responsive card-mobile max-w-xl mx-auto">
                                <div className="flex items-center space-x-3 mb-4 pb-3 border-b border-green-100">
                                    <i className="bi bi-flower1 text-xl sm:text-2xl text-[#09203f]"></i>
                                    <h3 className="text-lg sm:text-xl font-semibold bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent">
                                        BukSU BGH Community
                                    </h3>
                                </div>
                                <div className="w-full bg-gray-50 rounded-lg overflow-hidden">
                                    {fbLoaded ? (
                                        <iframe
                                            src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fprofile.php%3Fid%3D100068682045391&tabs=timeline&width=500&height=450&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                                            className="w-full"
                                            height="450"
                                            style={{ border: 'none', overflow: 'hidden' }}
                                            scrolling="no"
                                            frameBorder="0"
                                            allowFullScreen={true}
                                            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                                            onError={(e) => {
                                                console.error('Facebook iframe failed to load:', e);
                                                setFbLoaded(false);
                                            }}
                                        ></iframe>
                                    ) : (
                                        <div className="flex items-center justify-center h-[450px] bg-gray-100 rounded-lg">
                                            <div className="text-center">
                                                <i className="bi bi-facebook text-4xl text-blue-600 mb-2"></i>
                                                <p className="text-gray-600">Loading Facebook feed...</p>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Features Section */}
            <section className="py-12 sm:py-20 bg-gradient-to-b from-white to-gray-50">
                <div className="container-responsive">
                    <div className="text-center mb-12 sm:mb-16 animate-fade-in">
                        <h2 className="heading-responsive bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent mb-4">
                            Smart Garden Features
                        </h2>
                        <p className="text-responsive text-gray-600 max-w-2xl mx-auto">Discover the power of intelligent garden management</p>
                    </div>
                    <div className="grid-responsive">
                        {[
                            {
                                icon: 'bi-droplet-fill',
                                title: 'Smart Irrigation',
                                description: 'Automated watering system that ensures optimal moisture levels for your plants.'
                            },
                            {
                                icon: 'bi-graph-up',
                                title: 'Real-time Monitoring',
                                description: 'Track environmental conditions and plant health with advanced sensors.'
                            },
                            {
                                icon: 'bi-calendar-check',
                                title: 'Scheduling System',
                                description: 'Create and manage watering schedules for different zones of your garden.'
                            }
                        ].map((feature, index) => (
                            <div key={index} className="group animate-fade-in" style={{ animationDelay: `${index * 200}ms` }}>
                                <div className="card-responsive card-mobile text-center hover-responsive">
                                    <div className="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-r from-[#09203f] to-[#537895] text-white mb-4 sm:mb-6 group-hover:scale-110 transition-all">
                                        <i className={`bi ${feature.icon} text-lg sm:text-2xl`}></i>
                                    </div>
                                    <h3 className="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">{feature.title}</h3>
                                    <p className="text-responsive text-gray-600 leading-relaxed">{feature.description}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer className="py-8 sm:py-12 text-white" style={{ background: 'linear-gradient(to right, #09203f 0%, #537895 100%)' }}>
                <div className="container-responsive">
                    <div className="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
                        <Link href="/" className="flex items-center space-x-2 text-xl sm:text-2xl font-semibold">
                            <i className="bi bi-flower1 text-xl sm:text-2xl text-[#ffffff]"></i>
                            <span className="hidden sm:inline">RainBasin Pro</span>
                            <span className="sm:hidden">RainBasin</span>
                        </Link>
                        <div className="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-8">
                            <button 
                                onClick={() => setShowAboutModal(true)} 
                                className="hover:text-green-300 transition-colors text-responsive"
                            >
                                About Us
                            </button>
                            <button 
                                onClick={() => setShowContactModal(true)} 
                                className="hover:text-green-300 transition-colors text-responsive"
                            >
                                Contact
                            </button>
                            <button 
                                onClick={() => setShowPrivacyModal(true)} 
                                className="hover:text-green-300 transition-colors text-responsive"
                            >
                                Privacy Policy
                            </button>
                        </div>
                        <div className="text-xs sm:text-sm text-gray-300 text-center md:text-left">
                            © {new Date().getFullYear()} RainBasin Pro. All rights reserved.
                        </div>
                    </div>
                </div>
            </footer>

            {/* Modals */}
            <Modal show={showAboutModal} onClose={() => setShowAboutModal(false)}>
                <AboutModal onClose={() => setShowAboutModal(false)} />
            </Modal>

            <Modal show={showContactModal} onClose={() => setShowContactModal(false)}>
                <ContactModal onClose={() => setShowContactModal(false)} />
            </Modal>

            <Modal show={showPrivacyModal} onClose={() => setShowPrivacyModal(false)}>
                <PrivacyModal onClose={() => setShowPrivacyModal(false)} />
            </Modal>
        </>
    );
}
