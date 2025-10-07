import { useState, useEffect, Fragment, useRef } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { 
    HomeIcon,
    ChartBarIcon,
    CogIcon,
    UserGroupIcon,
    DocumentTextIcon,
    BellIcon,
    UserCircleIcon,
    ChevronDownIcon,
    XMarkIcon,
    Bars3Icon,
    ArrowRightOnRectangleIcon,
    ClockIcon,
    CloudArrowDownIcon,
    DocumentArrowDownIcon,
    ShieldCheckIcon,
    InformationCircleIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon
} from '@heroicons/react/24/outline';
import { Menu, Transition, Dialog } from '@headlessui/react';
import { Toaster } from 'react-hot-toast';
import toast from 'react-hot-toast';

export default function StaffLayout({ children, title }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const [notifications, setNotifications] = useState([]);
    const [showNotificationBadge, setShowNotificationBadge] = useState(false);
    const [isLoadingNotifications, setIsLoadingNotifications] = useState(true);
    const [notificationError, setNotificationError] = useState(null);

    const staffUser = auth.staffUser;

    // Helper function to check permissions
    const hasPermission = (permission) => {
        return staffUser.permissions.includes(permission);
    };

    // Role-based navigation items with descriptions
    const getNavigationItems = () => {
        const baseItems = [
            { 
                name: 'Dashboard', 
                href: '/staff/dashboard', 
                icon: HomeIcon, 
                permission: 'dashboard_view',
                description: 'Overview and analytics'
            },
            { 
                name: 'Water Control', 
                href: '/staff/water-control', 
                icon: CloudArrowDownIcon, 
                permission: 'watering_control',
                description: 'Manage watering schedules'
            },
            { 
                name: 'Sensor History', 
                href: '/staff/sensor-history', 
                icon: ClockIcon, 
                permission: 'sensor_history',
                description: 'View sensor history'
            },
        ];

        // Add manager/admin specific items
        if (staffUser.role === 'manager' || staffUser.role === 'admin') {
            baseItems.push({
                name: 'User Management', 
                href: '/staff/user-management', 
                icon: UserGroupIcon, 
                permission: 'user_management',
                description: 'Manage staff accounts'
            });
        }

        if (staffUser.role === 'admin') {
            baseItems.push({
                name: 'Audit Logs', 
                href: '/staff/audit-logs', 
                icon: ShieldCheckIcon, 
                permission: 'audit_logs',
                description: 'System audit trail'
            });
        }

        return baseItems.filter(item => hasPermission(item.permission));
    };

    const navigationItems = getNavigationItems();

    const handleLogout = async () => {
        try {
            await window.refreshCSRFToken();
            router.post('/staff/logout', {}, { onSuccess: () => router.visit('/staff/login') });
        } catch (error) {
            console.error('Logout failed:', error);
            // Fallback: redirect to staff login
            window.location.href = '/staff/login';
        }
    };

    return (
        <>
            <style jsx="true" global="true">{`
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
            `}</style>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
            <div className="min-h-screen bg-gray-50">
            <Toaster position="top-right" />
            
            {/* Mobile sidebar */}
            <div className={`fixed inset-0 z-50 lg:hidden ${sidebarOpen ? 'block' : 'hidden'}`}>
                <div className="fixed inset-0 bg-gray-600 bg-opacity-75" onClick={() => setSidebarOpen(false)} />
                <div className="fixed inset-y-0 left-0 flex w-64 flex-col bg-white shadow-xl">
                    <div className="flex h-16 items-center justify-between px-4 border-b border-gray-200">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="h-8 w-8 bg-gradient-to-r from-blue-600 to-green-600 rounded-lg flex items-center justify-center">
                                    <span className="text-white font-bold text-sm">RB</span>
                                </div>
                            </div>
                            <div className="ml-3">
                                <h1 className="text-lg font-semibold text-gray-900">RainBasin Pro</h1>
                                <p className="text-xs text-gray-500">Staff Portal</p>
                            </div>
                        </div>
                        <button
                            onClick={() => setSidebarOpen(false)}
                            className="text-gray-400 hover:text-gray-600"
                        >
                            <XMarkIcon className="h-6 w-6" />
                        </button>
                    </div>
                    <nav className="flex-1 space-y-1 px-2 py-4">
                        {navigationItems.map((item) => (
                            <Link
                                key={item.name}
                                href={item.href}
                                className={`group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 ${
                                    route().current(item.href)
                                        ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-md'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                }`}
                            >
                                <item.icon className={`mr-3 h-5 w-5 flex-shrink-0 ${route().current(item.href) ? 'text-white' : 'text-gray-500'}`} />
                                <div className="flex-1">
                                    <div className="font-medium">{item.name}</div>
                                    <div className="text-xs text-gray-500">{item.description}</div>
                                </div>
                            </Link>
                        ))}
                    </nav>
                </div>
            </div>

            {/* Desktop sidebar */}
            <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
                <div className="flex flex-col flex-grow bg-white shadow-xl border-r border-gray-200">
                    <div className="flex h-16 items-center px-4 border-b border-gray-200">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="h-8 w-8 bg-gradient-to-r from-blue-600 to-green-600 rounded-lg flex items-center justify-center">
                                    <span className="text-white font-bold text-sm">RB</span>
                                </div>
                            </div>
                            <div className="ml-3">
                                <h1 className="text-lg font-semibold text-gray-900">RainBasin Pro</h1>
                                <p className="text-xs text-gray-500">Staff Portal</p>
                            </div>
                        </div>
                    </div>
                    <nav className="flex-1 space-y-1 px-2 py-4">
                        {navigationItems.map((item) => (
                            <Link
                                key={item.name}
                                href={item.href}
                                className={`group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 ${
                                    route().current(item.href)
                                        ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-md'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                }`}
                            >
                                <item.icon className={`mr-3 h-5 w-5 flex-shrink-0 ${route().current(item.href) ? 'text-white' : 'text-gray-500'}`} />
                                <div className="flex-1">
                                    <div className="font-medium">{item.name}</div>
                                    <div className="text-xs text-gray-500">{item.description}</div>
                                </div>
                            </Link>
                        ))}
                    </nav>
                </div>
            </div>

            {/* Main content */}
            <div className="lg:pl-64">
                {/* Top navigation */}
                <div className="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        className="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <Bars3Icon className="h-6 w-6" />
                    </button>

                    <div className="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div className="flex flex-1" />
                        <div className="flex items-center gap-x-4 lg:gap-x-6">
                            {/* Notifications */}
                            <button className="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 relative">
                                <BellIcon className="h-6 w-6" />
                                {showNotificationBadge && (
                                    <span className="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full"></span>
                                )}
                            </button>

                            {/* User menu */}
                            <Menu as="div" className="relative">
                                <Menu.Button className="flex items-center gap-x-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <div className="h-8 w-8 bg-gradient-to-r from-blue-600 to-green-600 rounded-full flex items-center justify-center">
                                        <span className="text-white font-bold text-sm">
                                            {staffUser.name.charAt(0).toUpperCase()}
                                        </span>
                                    </div>
                                    <span className="hidden lg:block">{staffUser.name}</span>
                                    <ChevronDownIcon className="h-4 w-4" />
                                </Menu.Button>

                                <Transition
                                    as={Fragment}
                                    enter="transition ease-out duration-100"
                                    enterFrom="transform opacity-0 scale-95"
                                    enterTo="transform opacity-100 scale-100"
                                    leave="transition ease-in duration-75"
                                    leaveFrom="transform opacity-100 scale-100"
                                    leaveTo="transform opacity-0 scale-95"
                                >
                                    <Menu.Items className="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                                        <div className="px-4 py-2 text-sm text-gray-700 border-b border-gray-100">
                                            <div className="font-medium">{staffUser.name}</div>
                                            <div className="text-gray-500">{staffUser.email}</div>
                                            <div className="text-xs text-gray-400 capitalize mt-1">
                                                {staffUser.role} • {staffUser.status}
                                            </div>
                                        </div>
                                        <Menu.Item>
                                            {({ active }) => (
                                                <button
                                                    onClick={handleLogout}
                                                    className={`${
                                                        active ? 'bg-gray-50' : ''
                                                    } w-full text-left px-4 py-2 text-sm text-gray-700 flex items-center`}
                                                >
                                                    <ArrowRightOnRectangleIcon className="h-4 w-4 mr-2" />
                                                    Sign out
                                                </button>
                                            )}
                                        </Menu.Item>
                                    </Menu.Items>
                                </Transition>
                            </Menu>
                        </div>
                    </div>
                </div>

                {/* Page content */}
                <main className="py-6">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        {title && (
                            <div className="mb-6">
                                <h1 className="text-2xl font-bold text-gray-900">{title}</h1>
                            </div>
                        )}
                        {children}
                    </div>
                </main>
            </div>
            </div>
        </>
    );
} 