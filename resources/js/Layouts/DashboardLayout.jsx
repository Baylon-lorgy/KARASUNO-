import { useState, useEffect, Fragment, useRef } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { 
    HomeIcon, 
    ClockIcon,
    DocumentChartBarIcon,
    ComputerDesktopIcon,
    CalendarDaysIcon,
    ArrowLeftOnRectangleIcon,
    UserCircleIcon,
    Cog6ToothIcon,
    DocumentArrowDownIcon,
    BellIcon,
    XMarkIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon,
    InformationCircleIcon,
    CloudArrowDownIcon,
    ChevronDownIcon,
    ShieldCheckIcon,
    UserGroupIcon,
    CalendarIcon,
    TrashIcon,
    UserIcon,
    Bars3Icon,
    ChevronLeftIcon,
    ChevronRightIcon
} from '@heroicons/react/24/outline';
import { Menu, Transition, Dialog } from '@headlessui/react';
import { useSettings } from '../Contexts/SettingsContext';
import axios from 'axios';
import { Toaster } from 'react-hot-toast';
import toast from 'react-hot-toast';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import useAppStore from '../stores/useAppStore';

// Configure axios defaults
axios.defaults.baseURL = 'http://127.0.0.1:8000';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Add a request interceptor to include the CSRF token and auth token
axios.interceptors.request.use(config => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const authToken = localStorage.getItem('auth_token');
    
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    
    if (authToken) {
        config.headers['Authorization'] = `Bearer ${authToken}`;
    }
    
    return config;
}, error => {
    return Promise.reject(error);
});

export default function DashboardLayout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [showLogoutConfirm, setShowLogoutConfirm] = useState(false);
    const { auth } = usePage().props;
    const echo = useRef(null);
    const { settings, updateSettings } = useSettings?.() || { settings: {}, updateSettings: async () => ({ success: true }) };
    const [isSavingSettings, setIsSavingSettings] = useState(false);
    const [showSettingsModal, setShowSettingsModal] = useState(false);
    const [tempSettings, setTempSettings] = useState(settings);

    useEffect(() => {
        setTempSettings(settings);
    }, [settings]);

    // Global store
    const {
        notifications,
        showNotificationBadge,
        isLoadingNotifications,
        wateringStatus,
        addNotification,
        markNotificationAsRead,
        markAllNotificationsAsRead,
        deleteNotification,
        clearNotificationBadge,
        setWateringStatus,
        fetchNotifications,
        setUIState,
        ui
    } = useAppStore();

    const navigation = [
        { name: 'Dashboard', href: '/admin/dashboard', icon: HomeIcon, description: 'Overview and analytics' },
        { name: 'Water Control', href: '/admin/waterschedule', icon: CloudArrowDownIcon, description: 'Manage watering schedules' },
        { name: 'History', href: '/admin/sensor-history', icon: ClockIcon, description: 'View sensor history' },
        { name: 'Reports', href: '/admin/sensor-report', icon: DocumentArrowDownIcon, description: 'Generate reports' },
        { name: 'Audit Logs', href: '/admin/audit-logs', icon: ShieldCheckIcon, description: 'System audit trail' },
        { name: 'User Management', href: '/admin/user-management', icon: UserGroupIcon, description: 'Manage users' },
    ];

    // Set auth token when component mounts or auth changes
    useEffect(() => {
        if (auth?.token) {
            localStorage.setItem('auth_token', auth.token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`;
        }
    }, [auth?.token]);

    // Initialize Laravel Echo for WebSocket connections
    useEffect(() => {
        if (auth?.token) {
            // Initialize Pusher
            window.Pusher = Pusher;
            
            // Initialize Laravel Echo
            echo.current = new Echo({
                broadcaster: 'pusher',
                key: import.meta.env.VITE_PUSHER_APP_KEY || 'your-pusher-key',
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
                forceTLS: true,
                auth: {
                    headers: {
                        'Authorization': `Bearer ${auth.token}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                }
            });

            // Listen for new notifications
            echo.current.private('notifications')
                .listen('NewNotification', (e) => {
                    const newNotification = {
                        ...e.notification,
                        time: new Date().toLocaleString()
                    };
                    addNotification(newNotification);
                    toast.success(`${newNotification.title}: ${newNotification.message}`);
                });

            // Listen for watering events
            echo.current.channel('watering-events')
                .listen('WateringStarted', (e) => {
                    setWateringStatus({
                        active: true,
                        duration: e.duration || 0,
                        remainingTime: (e.duration || 0) * 60
                    });
                    toast.success(`Watering started for ${e.duration} minutes`);
                })
                .listen('WateringStopped', (e) => {
                    setWateringStatus({
                        active: false,
                        remainingTime: 0
                    });
                    toast.info('Watering stopped');
                });

            // Listen for sensor alerts
            echo.current.channel('sensor-alerts')
                .listen('SensorThresholdAlert', (e) => {
                    const alertMessage = `${e.sensorType} sensor alert: ${e.value} (threshold: ${e.threshold})`;
                    toast.error(alertMessage);
                    
                    // Add to notifications
                    addNotification({
                        type: 'sensor_threshold',
                        title: 'Sensor Alert',
                        message: alertMessage,
                        priority: e.severity
                    });
                });

            return () => {
                if (echo.current) {
                    echo.current.disconnect();
                }
            };
        }
    }, [auth?.token, addNotification, setWateringStatus]);

    // Fetch initial notifications
    useEffect(() => {
        if (auth?.token) {
            fetchNotifications();
        }
    }, [auth?.token, fetchNotifications]);

    // Expose addNotification globally for other components
    useEffect(() => {
        window.addNotification = addNotification;
        return () => {
            delete window.addNotification;
        };
    }, [addNotification]);

    // Mark notification as read
    const handleMarkAsRead = async (notificationId) => {
        try {
            await axios.post(`/api/system-notifications/${notificationId}/read`);
            markNotificationAsRead(notificationId);
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    };

    // Mark all notifications as read
    const handleMarkAllAsRead = async () => {
        try {
            await axios.post('/api/system-notifications/mark-all-read');
            markAllNotificationsAsRead();
            clearNotificationBadge();
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    };

    // Delete notification
    const handleDeleteNotification = async (notificationId) => {
        try {
            await axios.delete(`/api/system-notifications/${notificationId}`);
            deleteNotification(notificationId);
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    };

    // Get notification icon based on type
    const getNotificationIcon = (type) => {
        switch (type) {
            case 'watering_started':
                return <CloudArrowDownIcon className="h-4 w-4 text-blue-500" />;
            case 'watering_stopped':
                return <XMarkIcon className="h-4 w-4 text-red-500" />;
            case 'water_level_low':
                return <ExclamationTriangleIcon className="h-4 w-4 text-orange-500" />;
            case 'water_level_critical':
                return <ExclamationTriangleIcon className="h-4 w-4 text-red-500" />;
            case 'sensor_offline':
                return <ExclamationTriangleIcon className="h-4 w-4 text-red-500" />;
            case 'sensor_online':
                return <CheckCircleIcon className="h-4 w-4 text-green-500" />;
            case 'schedule_created':
                return <CalendarIcon className="h-4 w-4 text-purple-500" />;
            case 'schedule_modified':
                return <CalendarIcon className="h-4 w-4 text-yellow-500" />;
            case 'schedule_deleted':
                return <TrashIcon className="h-4 w-4 text-orange-500" />;
            case 'staff_invitation_pending':
                return <UserGroupIcon className="h-4 w-4 text-yellow-500" />;
            case 'staff_invitation_approved':
                return <UserGroupIcon className="h-4 w-4 text-green-500" />;
            case 'staff_account_activated':
                return <UserGroupIcon className="h-4 w-4 text-green-500" />;
            case 'staff_account_deactivated':
                return <UserGroupIcon className="h-4 w-4 text-red-500" />;
            default:
                return <BellIcon className="h-4 w-4 text-gray-500" />;
        }
    };

    // Get notification priority class
    const getNotificationPriorityClass = (priority) => {
        switch (priority) {
            case 'high':
                return 'border-l-4 border-red-500 bg-red-50';
            case 'medium':
                return 'border-l-4 border-yellow-500 bg-yellow-50';
            case 'low':
                return 'border-l-4 border-blue-500 bg-blue-50';
            case 'critical':
                return 'border-l-4 border-red-600 bg-red-100 animate-pulse';
            default:
                return 'border-l-4 border-gray-500 bg-gray-50';
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

            <div className="min-h-screen flex">
                <Toaster 
                    position="bottom-right"
                    toastOptions={{
                        duration: 3000,
                        style: {
                            background: '#fff',
                            color: '#1e293b',
                            border: '1px solid #e5e7eb',
                            boxShadow: '0 2px 8px rgba(0,0,0,0.06)',
                            fontSize: '14px',
                        },
                        success: {
                            duration: 2000,
                            iconTheme: {
                                primary: '#10B981',
                                secondary: '#fff',
                            },
                        },
                        error: {
                            duration: 3000,
                            iconTheme: {
                                primary: '#EF4444',
                                secondary: '#fff',
                            },
                        },
                    }}
                />

                {/* Desktop Sidebar */}
                <div className={`hidden lg:flex flex-col fixed inset-y-0 left-0 z-40 bg-white/95 backdrop-blur-xl shadow-xl transition-all duration-300 ease-in-out ${
                    sidebarCollapsed ? 'w-16' : 'w-64'
                }`}>
                    {/* Sidebar Header */}
                    <div className="flex items-center justify-between h-16 px-3 border-b border-gray-200">
                        {!sidebarCollapsed && (
                            <Link href="/" className="flex items-center space-x-3 text-xl font-semibold bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent">
                                <i className="bi bi-flower1 text-2xl text-[#09203f]"></i>
                                <span>RainBasin Pro</span>
                            </Link>
                        )}
                        {sidebarCollapsed && (
                            <Link href="/" className="flex items-center justify-center w-full">
                                <i className="bi bi-flower1 text-2xl text-[#09203f]"></i>
                            </Link>
                        )}
                        <button
                            onClick={() => setSidebarCollapsed(!sidebarCollapsed)}
                            className="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200"
                            title={sidebarCollapsed ? "Expand sidebar" : "Collapse sidebar"}
                        >
                            {sidebarCollapsed ? (
                                <ChevronRightIcon className="h-4 w-4" />
                            ) : (
                                <ChevronLeftIcon className="h-4 w-4" />
                            )}
                        </button>
                    </div>

                    {/* Navigation Menu */}
                    <nav className="flex-1 px-3 pt-4">
                        <div className="space-y-1">
                            {navigation.map((item) => {
                                const isActive = window.location.pathname === new URL(item.href, window.location.origin).pathname;
                                return (
                                    <div key={item.name} className="relative group">
                                        <Link
                                            href={item.href}
                                            className={`flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ${
                                                isActive 
                                                    ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-md' 
                                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                            }`}
                                        >
                                            <item.icon className={`h-5 w-5 flex-shrink-0 ${isActive ? 'text-white' : 'text-gray-500'}`} />
                                            {!sidebarCollapsed && (
                                                <span className="ml-3 truncate">{item.name}</span>
                                            )}
                                        </Link>
                                        
                                        {/* Tooltip for collapsed sidebar */}
                                        {sidebarCollapsed && (
                                            <div className="absolute left-full ml-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50 shadow-lg">
                                                {item.name}
                                                <div className="absolute top-1/2 right-full transform -translate-y-1/2 border-4 border-transparent border-r-gray-900"></div>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    </nav>

                    {/* Sidebar Footer */}
                    <div className="px-3 pb-4">
                        <div className="border-t border-gray-200 pt-3">
                            {!sidebarCollapsed && (
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 font-medium">RainBasin Pro</p>
                                    <p className="text-xs text-gray-400 mt-1">Smart Garden System</p>
                                </div>
                            )}
                            {sidebarCollapsed && (
                                <div className="flex justify-center">
                                    <div className="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i className="bi bi-flower1 text-sm text-gray-500"></i>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Mobile Sidebar */}
                <div className={`lg:hidden fixed inset-y-0 left-0 z-50 w-64 bg-white/95 backdrop-blur-xl shadow-xl transform transition-transform duration-300 ease-in-out ${
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                }`}>
                    <nav className="h-full flex flex-col">
                        <div className="flex items-center justify-between h-16 px-3 border-b border-gray-200">
                            <Link href="/" className="flex items-center space-x-3 text-xl font-semibold bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent">
                                <i className="bi bi-flower1 text-2xl text-[#09203f]"></i>
                                <span>RainBasin Pro</span>
                            </Link>
                            <button
                                onClick={() => setSidebarOpen(false)}
                                className="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200"
                            >
                                <XMarkIcon className="h-5 w-5" />
                            </button>
                        </div>

                        <div className="flex-1 px-3 pt-4">
                            <div className="space-y-1">
                                {navigation.map((item) => {
                                    const isActive = window.location.pathname === new URL(item.href, window.location.origin).pathname;
                                    return (
                                        <div key={item.name}>
                                            <Link
                                                href={item.href}
                                                onClick={() => setSidebarOpen(false)}
                                                className={`flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ${
                                                    isActive 
                                                        ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-md' 
                                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                                }`}
                                            >
                                                <item.icon className={`h-5 w-5 flex-shrink-0 ${isActive ? 'text-white' : 'text-gray-500'}`} />
                                                <span className="ml-3 truncate">{item.name}</span>
                                            </Link>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                        
                        <div className="px-3 pb-4">
                            <div className="border-t border-gray-200 pt-3">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 font-medium">RainBasin Pro</p>
                                    <p className="text-xs text-gray-400 mt-1">Smart Garden System</p>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>

                {/* Main Content Area */}
                <div className={`flex-1 flex flex-col ${sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'}`}>
                    {/* Top Navigation */}
                    <nav className="bg-white/85 backdrop-blur-lg border-b border-green-100 lg:border-l lg:border-l-green-100 relative z-10">
                        <div className="px-4 sm:px-6 lg:px-8">
                            <div className="flex justify-between items-center h-16">
                                <div className="flex items-center">
                                    {/* Mobile menu button */}
                                    <button
                                        onClick={() => setSidebarOpen(true)}
                                        className="lg:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                                    >
                                        <Bars3Icon className="h-6 w-6" />
                                    </button>
                                    

                                </div>
                                
                                <div className="flex items-center space-x-2 lg:space-x-4 pr-2 lg:pr-4">
                                {/* Notifications */}
                                <Menu as="div" className="relative">
                                    <Menu.Button 
                                        className="relative p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                                        onClick={() => clearNotificationBadge()}
                                    >
                                            <BellIcon className="h-5 w-5 lg:h-6 lg:w-6" />
                                        {showNotificationBadge && (
                                            <span className="absolute -top-1 -right-1 h-4 w-4 lg:h-5 lg:w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                                {notifications.length > 10 ? '10+' : notifications.length}
                                            </span>
                                        )}
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
                                            <Menu.Items className="absolute right-0 mt-3 w-72 lg:w-80 bg-white rounded-2xl shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                            <div className="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                                                <h3 className="text-sm font-semibold text-gray-900">Notifications</h3>
                                                <div className="flex items-center space-x-2">
                                                    {notifications.some(n => !n.read) && (
                                                        <button
                                                            onClick={handleMarkAllAsRead}
                                                            className="text-xs text-blue-600 hover:text-blue-800 font-medium"
                                                        >
                                                            Mark all as read
                                                        </button>
                                                    )}
                                                    {isLoadingNotifications && (
                                                        <ArrowPathIcon className="h-4 w-4 text-gray-500 animate-spin" />
                                                    )}
                                                </div>
                                            </div>
                                            <div className="max-h-64 overflow-y-auto">
                                                {isLoadingNotifications ? (
                                                    <div className="px-4 py-3 text-center text-sm text-gray-500">
                                                        Loading notifications...
                                                    </div>
                                                ) : notifications.length === 0 ? (
                                                    <div className="px-4 py-3 text-center text-sm text-gray-400">
                                                        No notifications
                                                    </div>
                                                ) : (
                                                    notifications.slice(0, 10).map((notification) => (
                                                        <div
                                                            key={notification._id}
                                                            className={`p-3 mb-2 bg-white rounded shadow-sm border-l-4 ${getNotificationPriorityClass(notification.priority)}`}
                                                        >
                                                            <div className="flex items-start justify-between">
                                                                <div className="flex items-start space-x-3 flex-1">
                                                                    <div className="mt-0.5">
                                                                        {getNotificationIcon(notification.type)}
                                                                    </div>
                                                                    <div className="flex-1 min-w-0">
                                                            <div className="flex items-center justify-between">
                                                                            <p className="text-sm font-medium text-gray-900 truncate">
                                                                                {notification.title || notification.type}
                                                                            </p>
                                                                            <span className="text-xs text-gray-400 ml-2">
                                                                                {notification.time}
                                                                            </span>
                                                            </div>
                                                                        <p className="text-xs text-gray-600 mt-1 line-clamp-2">
                                                                            {notification.message}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div className="flex items-center space-x-1 ml-2">
                                                                    {!notification.read && (
                                                                        <button
                                                                            onClick={() => handleMarkAsRead(notification._id)}
                                                                            className="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors"
                                                                            title="Mark as read"
                                                                        >
                                                                            <CheckCircleIcon className="h-3 w-3" />
                                                                        </button>
                                                                    )}
                                                                    <button
                                                                        onClick={() => handleDeleteNotification(notification._id)}
                                                                        className="p-1 text-red-600 hover:bg-red-100 rounded transition-colors"
                                                                        title="Delete notification"
                                                                    >
                                                                        <TrashIcon className="h-3 w-3" />
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ))
                                                )}
                                                {notifications.length > 10 && (
                                                    <div className="px-4 py-2 text-center text-xs text-gray-500 border-t border-gray-100">
                                                        Showing 10 of {notifications.length} notifications
                                                    </div>
                                                )}
                                            </div>
                                        </Menu.Items>
                                    </Transition>
                                </Menu>

                                {/* User menu */}
                                <Menu as="div" className="relative">
                                        <Menu.Button className="flex items-center space-x-2 lg:space-x-3 p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                                            <UserCircleIcon className="h-5 w-5 lg:h-6 lg:w-6" />
                                        <span className="hidden sm:block text-sm font-semibold text-gray-700">{auth?.user?.name || 'Admin'}</span>
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
                                            <Menu.Items className="absolute right-0 mt-3 w-40 lg:w-48 bg-white rounded-2xl shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <div className="py-3">
                                                <Menu.Item>
                                                    {({ active }) => (
                                                        <Link
                                                            href="/profile"
                                                            className={`${active ? 'bg-gray-100' : ''} block px-4 py-3 text-sm font-medium text-gray-700 rounded-lg mx-2`}
                                                        >
                                                            Profile
                                                        </Link>
                                                    )}
                                                </Menu.Item>
                                                <Menu.Item>
                                                    {({ active }) => (
                                                        <button
                                                            onClick={() => setShowSettingsModal(true)}
                                                            className={`${active ? 'bg-gray-100' : ''} block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 rounded-lg mx-2`}
                                                        >
                                                            Settings
                                                        </button>
                                                    )}
                                                </Menu.Item>
                                                <div className="border-t border-gray-100 my-2 mx-2" />
                                                <Menu.Item>
                                                    {({ active }) => (
                                                        <Link
                                                            href="/logout"
                                                            method="post"
                                                            as="button"
                                                            onClick={async (e) => {
                                                                e.preventDefault();
                                                                try {
                                                                    await window.refreshCSRFToken();
                                                                    router.post('/logout', {}, { onSuccess: () => router.visit('/login') });
                                                                } catch (error) {
                                                                    console.error('Logout failed:', error);
                                                                    // Fallback: redirect to logout
                                                                    window.location.href = '/logout';
                                                                }
                                                            }}
                                                            className={`${active ? 'bg-red-50' : ''} block w-full text-left px-4 py-3 text-sm font-medium text-red-600 rounded-lg mx-2`}
                                                        >
                                                            Logout
                                                        </Link>
                                                    )}
                                                </Menu.Item>
                                            </div>
                                        </Menu.Items>
                                    </Transition>
                                </Menu>
                            </div>
                        </div>
                    </div>
                </nav>

                    {/* Main Content */}
                    <main className="flex-1 overflow-auto relative z-0">
                        <div className="px-4 sm:px-6 lg:px-8 pr-8 lg:pr-12 py-6">
                            <div className="max-w-7xl mx-auto">
                                <div className="card-responsive card-mobile animate-fade-in min-h-fit">
                                    {children}
                                </div>
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

                {/* Logout Confirmation Modal */}
                <Transition appear show={showLogoutConfirm} as={Fragment}>
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
                            <div className="fixed inset-0 bg-black/30" aria-hidden="true" />
                        </Transition.Child>
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0 scale-95"
                            enterTo="opacity-100 scale-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100 scale-100"
                            leaveTo="opacity-0 scale-95"
                        >
                            <div className="fixed inset-0 flex items-center justify-center p-4">
                                <Dialog.Panel className="modal-content-responsive">
                                    <div className="p-6 sm:p-8">
                                        <Dialog.Title className="text-lg sm:text-xl font-bold text-gray-900 mb-4">
                                        Confirm Logout
                                    </Dialog.Title>
                                        <Dialog.Description className="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">
                                        Are you sure you want to logout?
                                    </Dialog.Description>
                                        <div className="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                                        <button
                                            onClick={() => setShowLogoutConfirm(false)}
                                                className="btn-responsive bg-gray-100 text-gray-700 hover:bg-gray-200"
                                        >
                                            Cancel
                                        </button>
                                        <Link
                                            href="/logout"
                                            method="post"
                                            as="button"
                                                className="btn-responsive bg-red-600 text-white hover:bg-red-700"
                                            onClick={async (e) => {
                                                e.preventDefault();
                                                try {
                                                    await window.refreshCSRFToken();
                                                    router.post('/logout', {}, { onSuccess: () => router.visit('/login') });
                                                } catch (error) {
                                                    console.error('Logout failed:', error);
                                                    window.location.href = '/login';
                                                }
                                            }}
                                        >
                                            Logout
                                        </Link>
                                        </div>
                                    </div>
                                </Dialog.Panel>
                            </div>
                        </Transition.Child>
                    </Dialog>
                </Transition>

                {/* Settings Modal */}
                <Transition appear show={showSettingsModal} as={Fragment}>
                    <Dialog as="div" className="relative z-50" onClose={() => setShowSettingsModal(false)}>
                        <Transition.Child as={Fragment} enter="ease-out duration-300" enterFrom="opacity-0" enterTo="opacity-100" leave="ease-in duration-200" leaveFrom="opacity-100" leaveTo="opacity-0">
                            <div className="fixed inset-0 bg-black/30" aria-hidden="true" />
                        </Transition.Child>
                        <Transition.Child as={Fragment} enter="ease-out duration-300" enterFrom="opacity-0 scale-95" enterTo="opacity-100 scale-100" leave="ease-in duration-200" leaveFrom="opacity-100 scale-100" leaveTo="opacity-0 scale-95">
                            <div className="fixed inset-0 flex items-center justify-center p-4">
                                <Dialog.Panel className="modal-content-responsive">
                                    <div className="p-6 sm:p-8">
                                        <Dialog.Title className="text-lg sm:text-xl font-bold text-gray-900 mb-4">system settings</Dialog.Title>
                                        <div className="space-y-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">theme</label>
                                                <select value={tempSettings?.theme || 'light'} onChange={(e) => setTempSettings({ ...tempSettings, theme: e.target.value })} className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="light">light</option>
                                                    <option value="dark">dark</option>
                                                </select>
                                            </div>
                                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <div className="p-3 bg-gray-50 rounded-lg">
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">auto refresh</label>
                                                    <input type="checkbox" checked={!!tempSettings?.dashboard?.autoRefresh} onChange={(e) => setTempSettings({ ...tempSettings, dashboard: { ...(tempSettings?.dashboard || {}), autoRefresh: e.target.checked } })} />
                                                </div>
                                                <div className="p-3 bg-gray-50 rounded-lg">
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">refresh interval (ms)</label>
                                                    <input type="number" min="5000" step="1000" value={tempSettings?.dashboard?.refreshInterval || 30000} onChange={(e) => setTempSettings({ ...tempSettings, dashboard: { ...(tempSettings?.dashboard || {}), refreshInterval: Number(e.target.value) } })} className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                                </div>
                                                <div className="p-3 bg-gray-50 rounded-lg">
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">compact mode</label>
                                                    <input type="checkbox" checked={!!tempSettings?.dashboard?.compactMode} onChange={(e) => setTempSettings({ ...tempSettings, dashboard: { ...(tempSettings?.dashboard || {}), compactMode: e.target.checked } })} />
                                                </div>
                                            </div>
                                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">language</label>
                                                    <input type="text" value={tempSettings?.system?.language || 'en'} onChange={(e) => setTempSettings({ ...tempSettings, system: { ...(tempSettings?.system || {}), language: e.target.value } })} className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">timezone</label>
                                                    <input type="text" value={tempSettings?.system?.timezone || 'UTC'} onChange={(e) => setTempSettings({ ...tempSettings, system: { ...(tempSettings?.system || {}), timezone: e.target.value } })} className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">date format</label>
                                                    <input type="text" value={tempSettings?.system?.dateFormat || 'MM/DD/YYYY'} onChange={(e) => setTempSettings({ ...tempSettings, system: { ...(tempSettings?.system || {}), dateFormat: e.target.value } })} className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex justify-end gap-3 mt-6">
                                            <button onClick={() => setShowSettingsModal(false)} className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">cancel</button>
                                            <button onClick={async () => { setIsSavingSettings(true); const res = await updateSettings(tempSettings); setIsSavingSettings(false); if (res?.success) { toast.success('settings saved'); setShowSettingsModal(false); } }} className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">{isSavingSettings ? 'saving...' : 'save'}</button>
                                        </div>
                                    </div>
                                </Dialog.Panel>
                            </div>
                        </Transition.Child>
                    </Dialog>
                </Transition>
            </div>
        </>
    );
}
