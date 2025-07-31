import { useState } from 'react';
import { Link } from '@inertiajs/react';
import { Bars3Icon, XMarkIcon } from '@heroicons/react/24/outline';

export default function ResponsiveNavigation({ auth, navigation = [] }) {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    return (
        <nav className="nav-mobile">
            <div className="container-responsive">
                <div className="flex justify-between items-center py-4">
                    <div className="flex items-center space-x-4">
                        {/* Mobile menu button */}
                        <button
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="lg:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                            aria-label="Toggle mobile menu"
                        >
                            {mobileMenuOpen ? (
                                <XMarkIcon className="h-6 w-6" />
                            ) : (
                                <Bars3Icon className="h-6 w-6" />
                            )}
                        </button>
                        
                        <Link href="/" className="flex items-center space-x-2 text-xl sm:text-2xl font-semibold bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent">
                            <svg viewBox="0 0 24 24" className="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor">
                                <path d="M12,2 A10,10 0 0,1 12,22 A10,10 0 0,1 12,2 M12,5 A3,3 0 0,0 9,8 A3,3 0 0,0 12,11 A3,3 0 0,0 15,8 A3,3 0 0,0 12,5 M12,8 A3,3 0 0,0 15,11 A3,3 0 0,0 18,8 A3,3 0 0,0 15,5 A3,3 0 0,0 12,8 M12,8 A3,3 0 0,1 9,11 A3,3 0 0,1 6,8 A3,3 0 0,1 9,5 A3,3 0 0,1 12,8 M12,11 A3,3 0 0,1 15,14 A3,3 0 0,1 12,17 A3,3 0 0,1 9,14 A3,3 0 0,1 12,11" />
                            </svg>
                            <i className="bi bi-flower1 text-xl sm:text-2xl text-[#09203f]"></i>
                            <span className="hidden sm:inline">RainBasin Pro</span>
                            <span className="sm:hidden">RainBasin</span>
                        </Link>
                    </div>
                    
                    {/* Desktop Navigation */}
                    <div className="hidden lg:flex items-center space-x-4">
                        {navigation.map((item) => {
                            const isActive = window.location.pathname === new URL(item.href, window.location.origin).pathname;
                            return (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    className={`btn-responsive ${
                                        isActive 
                                            ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-lg' 
                                            : 'bg-white/90 backdrop-blur-sm text-gray-700 border border-gray-200 hover:bg-gray-50'
                                    } hover:-translate-y-0.5 hover:shadow-lg`}
                                >
                                    <item.icon className="mr-2 h-4 w-4" />
                                    {item.name}
                                </Link>
                            );
                        })}
                        
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
                </div>

                {/* Mobile Navigation Menu */}
                {mobileMenuOpen && (
                    <div className="lg:hidden py-4 border-t border-green-100 animate-slide-down">
                        <div className="space-y-3">
                            {navigation.map((item) => {
                                const isActive = window.location.pathname === new URL(item.href, window.location.origin).pathname;
                                return (
                                    <Link
                                        key={item.name}
                                        href={item.href}
                                        onClick={() => setMobileMenuOpen(false)}
                                        className={`block w-full text-center btn-responsive ${
                                            isActive 
                                                ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-lg' 
                                                : 'bg-white/90 backdrop-blur-sm text-gray-700 border border-gray-200 hover:bg-gray-50'
                                        } hover:-translate-y-0.5 hover:shadow-lg`}
                                    >
                                        <item.icon className="mr-2 h-4 w-4 inline" />
                                        {item.name}
                                    </Link>
                                );
                            })}
                            
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
    );
} 