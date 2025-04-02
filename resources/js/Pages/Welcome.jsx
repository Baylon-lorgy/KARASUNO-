import { Head, Link } from '@inertiajs/react';

export default function Welcome({ auth }) {
    return (
        <>
            <Head>
                <title>Botanical Garden & Herbarium - Rainwater Catch Basin</title>
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
            </Head>

            <style jsx global>{`
                :root {
                    --color-sage: #E8F3E9;
                    --color-mint: #DCE8E0;
                    --color-sky: #E3EDF2;
                    --color-mist: rgba(255, 255, 255, 0.95);
                    --primary-color: #1B5E20;
                    --secondary-color: #388E3C;
                    --accent-color: #81C784;
                    --text-primary: #2F3B4C;
                    --text-secondary: #546E7A;
                    --transition: all 0.3s ease;
                }

                body {
                    font-family: 'Outfit', sans-serif;
                    background: linear-gradient(135deg, 
                        var(--color-sage) 0%,
                        var(--color-mint) 50%,
                        var(--color-sky) 100%
                    );
                    color: var(--text-primary);
                    min-height: 100vh;
                }
            `}</style>

            {/* Navigation */}
            <nav className="fixed top-0 left-0 right-0 z-50 bg-white/85 backdrop-blur-lg border-b border-green-100">
                <div className="container mx-auto px-4 py-4">
                    <div className="flex justify-between items-center">
                        <Link href="/" className="flex items-center space-x-2 text-2xl font-semibold bg-gradient-to-r from-[#1B5E20] to-[#388E3C] bg-clip-text text-transparent">
                            <i className="bi bi-flower1"></i>
                            <span>Botanical Garden & Herbarium</span>
                        </Link>
                        <div>
                            {auth?.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#1B5E20] to-[#388E3C] text-white font-medium rounded-lg transition-all hover:-translate-y-0.5 hover:shadow-lg"
                                >
                                    <i className="bi bi-grid-1x2-fill mr-2"></i>
                                        Dashboard
                                    </Link>
                                ) : (
                                        <Link
                                            href={route('login')}
                                    className="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#1B5E20] to-[#388E3C] text-white font-medium rounded-lg transition-all hover:-translate-y-0.5 hover:shadow-lg"
                                >
                                    <i className="bi bi-box-arrow-in-right mr-2"></i>
                                    Admin Login
                                </Link>
                            )}
                        </div>
                                    </div>
                                            </div>
            </nav>

            {/* Hero Section */}
            <section className="pt-32 pb-20 relative overflow-hidden">
                <div className="container mx-auto px-4">
                    <div className="grid md:grid-cols-2 gap-12 items-center">
                        <div className="space-y-6 animate-fadeIn">
                            <h1 className="text-5xl md:text-6xl font-bold leading-tight bg-gradient-to-r from-[#1B5E20] to-[#388E3C] bg-clip-text text-transparent">
                                Rainwater <br />Catch Basin
                            </h1>
                            <p className="text-lg text-gray-600 max-w-xl">
                                Experience the beauty of nature with our smart garden management system. Monitor, maintain, and nurture your garden with precision and care.
                                                </p>
                                            </div>
                        <div className="relative animate-fadeIn animation-delay-200">
                            <div className="bg-white/95 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all hover:-translate-y-2 backdrop-blur-xl border border-green-100">
                                <div className="flex items-center space-x-3 mb-4 pb-3 border-b border-green-100">
                                    <i className="bi bi-flower1 text-2xl text-[#1B5E20]"></i>
                                    <h3 className="text-xl font-semibold bg-gradient-to-r from-[#1B5E20] to-[#388E3C] bg-clip-text text-transparent">
                                        BukSU BGH Community
                                    </h3>
                                </div>
                                <div className="aspect-video w-full bg-gray-50 rounded-lg overflow-hidden">
                                    <iframe
                                        src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fprofile.php%3Fid%3D100068682045391&tabs=timeline&width=500&height=450&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                                        width="100%"
                                        height="450"
                                        style={{ border: 'none', overflow: 'hidden' }}
                                        scrolling="no"
                                        frameBorder="0"
                                        allowFullScreen={true}
                                        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                                    ></iframe>
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
            </section>

            {/* Features Section */}
            <section className="py-20 bg-gradient-to-b from-white to-gray-50">
                <div className="container mx-auto px-4">
                    <div className="text-center mb-16 animate-fadeIn">
                        <h2 className="text-4xl font-bold bg-gradient-to-r from-[#1B5E20] to-[#388E3C] bg-clip-text text-transparent mb-4">
                            Smart Garden Features
                                        </h2>
                        <p className="text-gray-600">Discover the power of intelligent garden management</p>
                                    </div>
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                            <div key={index} className="group animate-fadeIn" style={{ animationDelay: `${index * 200}ms` }}>
                                <div className="bg-white/90 backdrop-blur-xl rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-all hover:-translate-y-2 border border-green-100">
                                    <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-[#1B5E20] to-[#388E3C] text-white mb-6 group-hover:scale-110 transition-all">
                                        <i className={`bi ${feature.icon} text-2xl`}></i>
                                    </div>
                                    <h3 className="text-xl font-semibold text-gray-900 mb-4">{feature.title}</h3>
                                    <p className="text-gray-600 leading-relaxed">{feature.description}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer className="bg-gradient-to-r from-[#2F3B4C] to-[#1B5E20] text-white py-12">
                <div className="container mx-auto px-4">
                    <div className="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
                        <Link href="/" className="flex items-center space-x-2 text-2xl font-semibold">
                            <i className="bi bi-flower1"></i>
                            <span>Botanical Garden</span>
                        </Link>
                        <div className="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-8">
                            <Link href="#" className="hover:text-green-300 transition-colors">About Us</Link>
                            <Link href="#" className="hover:text-green-300 transition-colors">Contact</Link>
                            <Link href="#" className="hover:text-green-300 transition-colors">Privacy Policy</Link>
                        </div>
                        <div className="text-sm text-gray-300">
                            © {new Date().getFullYear()} Botanical Garden & Herbarium. All rights reserved.
                    </div>
                </div>
            </div>
            </footer>
        </>
    );
}
