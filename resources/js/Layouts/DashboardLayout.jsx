import { useState, useEffect, Fragment } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { 
    HomeIcon, 
    ClockIcon,
    DocumentChartBarIcon,
    ComputerDesktopIcon,
    CalendarDaysIcon,
    ArrowLeftOnRectangleIcon,
    BellIcon,
    MoonIcon,
    UserCircleIcon,
    Cog6ToothIcon
} from '@heroicons/react/24/outline';
import { Menu, Transition, Dialog } from '@headlessui/react';

export default function DashboardLayout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [darkMode, setDarkMode] = useState(false);
    const [showLogoutConfirm, setShowLogoutConfirm] = useState(false);
    const { auth } = usePage().props;

    const navigation = [
        { name: 'Dashboard', href: '/admin/dashboard', icon: HomeIcon },
        { name: 'System Logs', href: '/admin/systemlogs', icon: ComputerDesktopIcon },
        { name: 'Water Schedule', href: '/admin/waterschedule', icon: CalendarDaysIcon },
        { name: 'History', href: '/admin/history', icon: ClockIcon },
        { name: 'Reports', href: '/admin/reports', icon: DocumentChartBarIcon },
    ];

    useEffect(() => {
        // Check Dark Mode Preference
        const savedDarkMode = localStorage.getItem('darkMode') === 'enabled';
        setDarkMode(savedDarkMode);
        if (savedDarkMode) {
            document.body.classList.add('dark-mode');
        }
    }, []);

    const toggleDarkMode = () => {
        setDarkMode(!darkMode);
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', !darkMode ? 'enabled' : 'disabled');
    };

    return (
        <div className={`min-h-screen transition-all duration-300 ${darkMode ? 'dark' : ''}`}>
            <style jsx="true" global="true">{`
                :root {
                    --primary-color: #2E7D32;
                    --secondary-color: #43A047;
                    --accent-color: #81C784;
                    --success-color: #66BB6A;
                    --warning-color: #FFA726;
                    --danger-color: #EF5350;
                    --background-light: #F5F9F6;
                    --text-primary: #2C3E50;
                    --text-secondary: #607D8B;
                    --transition: all 0.3s ease;
                }

                body {
                    background: linear-gradient(135deg, #E8F3E9 0%, #DCE8E0 25%, #E3EDF2 50%, #E9EDF5 100%);
                }

                body::before {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: 
                        linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                        linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
                    background-size: 20px 20px;
                    pointer-events: none;
                    z-index: 0;
                }

                .dark-mode {
                    background: var(--background-dark);
                    color: white;
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .fade-in {
                    animation: fadeIn 0.3s ease forwards;
                }

                @keyframes notification-pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }
            `}</style>

            {/* Sidebar */}
            <div className={`fixed inset-y-0 left-0 z-50 w-64 transform ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'} transition-transform duration-200 ease-in-out lg:translate-x-0`}>
                <div className="flex flex-col h-full bg-gradient-to-br from-[#1B5E20] to-[#388E3C] rounded-r-2xl shadow-xl">
                    <div className="flex items-center justify-center h-20 px-4">
                        <div className="flex items-center space-x-3">
                            <i className="bi bi-flower1 text-2xl text-white"></i>
                            <h1 className="text-xl font-bold text-white">Botanical Garden</h1>
                        </div>
                    </div>

                    <nav className="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                        {navigation.map((item) => {
                            const isActive = window.location.pathname === item.href;
                            return (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    className={`flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group ${
                                        isActive 
                                            ? 'bg-white text-[#1B5E20] shadow-lg ring-2 ring-white/50 ring-offset-2 ring-offset-[#1B5E20]' 
                                            : 'text-white/90 hover:bg-white/10'
                                    }`}
                                >
                                    <item.icon className={`w-5 h-5 mr-3 ${isActive ? 'text-[#1B5E20]' : 'text-white/90'}`} />
                                    {item.name}
                                </Link>
                            );
                        })}
                    </nav>
                </div>
            </div>

            {/* Main content */}
            <div className="lg:pl-64">
                {/* Header */}
                <div className="sticky top-0 z-40 bg-white/85 backdrop-blur-lg border-b border-green-100 shadow-sm">
                    <div className="flex h-20 items-center justify-between px-4">
                        <button
                            type="button"
                            className="lg:hidden inline-flex h-12 w-12 items-center justify-center rounded-lg text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#1B5E20]"
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                        >
                            <span className="sr-only">Open sidebar</span>
                            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <div className="flex flex-1 items-center justify-between px-4 sm:px-6 lg:px-8">
                            <h1 className="text-2xl font-semibold bg-gradient-to-r from-[#1B5E20] to-[#388E3C] bg-clip-text text-transparent">
                                {navigation.find(item => item.href === window.location.pathname)?.name || 'Dashboard'}
                            </h1>
                            <div className="flex items-center space-x-4">
                                {/* Notifications */}
                                <div className="relative">
                                    <button className="p-2 text-gray-600 hover:text-gray-900 rounded-full hover:bg-gray-100 transition-all duration-200">
                                        <BellIcon className="w-6 h-6" />
                                    </button>
                                </div>

                                {/* Dark Mode Toggle */}
                                <button 
                                    onClick={toggleDarkMode}
                                    className="p-2 text-gray-600 hover:text-gray-900 rounded-full hover:bg-gray-100 transition-all duration-200"
                                >
                                    <MoonIcon className="w-6 h-6" />
                                </button>

                                {/* User Menu */}
                                <Menu as="div" className="relative">
                                    <Menu.Button className="flex items-center space-x-3 pl-4 border-l border-gray-200 hover:opacity-80 transition-opacity">
                                        <div className="h-10 w-10 rounded-full bg-gradient-to-r from-[#1B5E20] to-[#388E3C] flex items-center justify-center text-white font-semibold">
                                            {auth?.user?.name?.charAt(0)}
                                        </div>
                                        <span className="text-sm font-medium text-gray-700">
                                            {auth?.user?.name}
                                        </span>
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
                                        <Menu.Items className="absolute right-0 mt-2 w-48 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none divide-y divide-gray-100">
                                            <div className="px-1 py-1">
                                                <Menu.Item>
                                                    {({ active }) => (
                                                        <Link
                                                            href={route('profile.edit')}
                                                            className={`${
                                                                active ? 'bg-gray-50' : ''
                                                            } group flex w-full items-center rounded-lg px-2 py-2 text-sm text-gray-700`}
                                                        >
                                                            <UserCircleIcon className="mr-2 h-5 w-5 text-gray-500" />
                                                            Profile
                                                        </Link>
                                                    )}
                                                </Menu.Item>
                                                <Menu.Item>
                                                    {({ active }) => (
                                                        <button
                                                            className={`${
                                                                active ? 'bg-gray-50' : ''
                                                            } group flex w-full items-center rounded-lg px-2 py-2 text-sm text-gray-700`}
                                                            onClick={() => setShowLogoutConfirm(true)}
                                                        >
                                                            <ArrowLeftOnRectangleIcon className="mr-2 h-5 w-5 text-gray-500" />
                                                            Logout
                                                        </button>
                                                    )}
                                                </Menu.Item>
                                            </div>
                                        </Menu.Items>
                                    </Transition>
                                </Menu>

                                {/* Logout Confirmation Dialog */}
                                <Transition show={showLogoutConfirm} as={Fragment}>
                                    <Dialog as="div" className="relative z-50" onClose={() => setShowLogoutConfirm(false)}>
                                        <Transition.Child
                                            as={Fragment}
                                            enter="ease-out duration-300"
                                            enterFrom="opacity-0"
                                            enterTo="opacity-100"
                                            leave="ease-in duration-200"
                                            leaveFrom="opacity-100"
                                            leaveTo="opacity-0"
                                        >
                                            <div className="fixed inset-0 bg-black/25 backdrop-blur-sm" />
                                        </Transition.Child>

                                        <div className="fixed inset-0 overflow-y-auto">
                                            <div className="flex min-h-full items-center justify-center p-4 text-center">
                                                <Transition.Child
                                                    as={Fragment}
                                                    enter="ease-out duration-300"
                                                    enterFrom="opacity-0 scale-95"
                                                    enterTo="opacity-100 scale-100"
                                                    leave="ease-in duration-200"
                                                    leaveFrom="opacity-100 scale-100"
                                                    leaveTo="opacity-0 scale-95"
                                                >
                                                    <Dialog.Panel className="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                                                        <Dialog.Title
                                                            as="h3"
                                                            className="text-lg font-medium leading-6 text-gray-900"
                                                        >
                                                            Confirm Logout
                                                        </Dialog.Title>
                                                        <div className="mt-2">
                                                            <p className="text-sm text-gray-500">
                                                                Are you sure you want to logout? You will need to login again to access your account.
                                                            </p>
                                                        </div>

                                                        <div className="mt-4 flex space-x-3">
                                                            <button
                                                                type="button"
                                                                className="inline-flex justify-center rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
                                                                onClick={() => {
                                                                    setShowLogoutConfirm(false);
                                                                    router.post(route('logout'));
                                                                }}
                                                            >
                                                                Yes, Logout
                                                            </button>
                                                            <button
                                                                type="button"
                                                                className="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                                                                onClick={() => setShowLogoutConfirm(false)}
                                                            >
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </Dialog.Panel>
                                                </Transition.Child>
                                            </div>
                                        </div>
                                    </Dialog>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Main content area */}
                <main className="py-8">
                    <div className="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div className="bg-white/95 backdrop-blur-xl shadow-lg rounded-2xl p-6 border border-green-100 fade-in">
                            {children}
                        </div>
                    </div>
                </main>
            </div>

            {/* Mobile sidebar backdrop */}
            {sidebarOpen && (
                <div 
                    className="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}
        </div>
    );
}
