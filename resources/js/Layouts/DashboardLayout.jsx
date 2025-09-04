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
import axios from 'axios';
import { Toaster } from 'react-hot-toast';
import toast from 'react-hot-toast';
import Echo from 'laravel-echo';
import NotificationSettings from '@/Components/NotificationSettings';

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
    const [notifications, setNotifications] = useState([]);
    const [showNotificationBadge, setShowNotificationBadge] = useState(false);
    const [isLoadingNotifications, setIsLoadingNotifications] = useState(true);
    const [notificationError, setNotificationError] = useState(null);
    const [showNotificationSettings, setShowNotificationSettings] = useState(false);
    const { auth } = usePage().props;
    const echo = useRef(null);

    const navigation = [
        { name: 'Dashboard', href: route('admin.dashboard'), icon: HomeIcon, description: 'Overview and analytics' },
        { name: 'Water Control', href: route('admin.waterschedule'), icon: CloudArrowDownIcon, description: 'Manage watering schedules' },
        { name: 'History', href: route('admin.sensor.history'), icon: ClockIcon, description: 'View sensor history' },
        { name: 'Reports', href: route('admin.sensor.report'), icon: DocumentArrowDownIcon, description: 'Generate reports' },
        { name: 'Audit Logs', href: route('admin.audit.logs'), icon: ShieldCheckIcon, description: 'System audit trail' },
        { name: 'User Management', href: route('admin.user.management'), icon: UserGroupIcon, description: 'Manage users' },
    ];

    // Set auth token when component mounts or auth changes
    useEffect(() => {
        if (auth?.token) {
            localStorage.setItem('auth_token', auth.token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`;
        }
    }, [auth?.token]);

    // Initialize Laravel Echo
    useEffect(() => {
        if (auth?.token) {
            echo.current = new Echo({
                broadcaster: 'pusher',
                key: import.meta.env.VITE_PUSHER_APP_KEY,
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
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
                        time: new Date(e.notification.created_at.$date).toLocaleString()
                    };
                    handleNewNotification(newNotification);
                });

            return () => {
                if (echo.current) {
                    echo.current.disconnect();
                }
            };
        }
    }, [auth?.token]);

    useEffect(() => {
        const fetchNotifications = async () => {
            try {
                setNotificationError(null);
                const response = await axios.get('/api/system-notifications');
                let formattedNotifications = [];
                try {
                    formattedNotifications = response.data.map(notification => ({
                        ...notification,
                        time: new Date(
                            notification.created_at?.$date || notification.created_at
                        ).toLocaleString()
                    }));
                } catch (mapErr) {
                    console.error('Error mapping notifications:', mapErr);
                }
                setNotifications(formattedNotifications);
                setShowNotificationBadge(formattedNotifications.some(n => !n.read));
            } catch (error) {
                console.error('Error fetching notifications:', error);
                setNotificationError('Failed to load notifications');
                toast.error('Failed to load notifications');
            } finally {
                setIsLoadingNotifications(false);
            }
        };

        if (auth?.token) {
            fetchNotifications();
            const interval = setInterval(fetchNotifications, 30000);
            return () => clearInterval(interval);
        }
    }, [auth?.token]);

    const markAsRead = async (notificationId) => {
        try {
            await toast.promise(
                axios.post(`/api/system-notifications/${notificationId}/read`),
                {
                    loading: 'Marking as read...',
                    success: 'Marked as read',
                    error: 'Failed to mark as read'
                }
            );
            
            setNotifications(prevNotifications =>
                prevNotifications.map(notification =>
                    notification._id === notificationId
                        ? { ...notification, read: true }
                        : notification
                )
            );
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    };

    const markAllAsRead = async () => {
        try {
            await toast.promise(
                axios.post('/api/system-notifications/mark-all-read'),
                {
                    loading: 'Marking all as read...',
                    success: 'All notifications marked as read',
                    error: 'Failed to mark all as read'
                }
            );
            
            setNotifications(prevNotifications =>
                prevNotifications.map(notification => ({ ...notification, read: true }))
            );
            setShowNotificationBadge(false);
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    };

    const deleteNotification = async (notificationId) => {
        try {
            await toast.promise(
                axios.delete(`/api/system-notifications/${notificationId}`),
                {
                    loading: 'Deleting notification...',
                    success: 'Notification deleted',
                    error: 'Failed to delete notification'
                }
            );
            
            setNotifications(prevNotifications =>
                prevNotifications.filter(notification => notification._id !== notificationId)
            );
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    };

    const refreshNotifications = async () => {
        setIsLoadingNotifications(true);
        try {
            const response = await axios.get('/api/system-notifications');
            let formattedNotifications = [];
            try {
                formattedNotifications = response.data.map(notification => ({
                    ...notification,
                    time: new Date(
                        notification.created_at?.$date || notification.created_at
                    ).toLocaleString()
                }));
            } catch (mapErr) {
                console.error('Error mapping notifications:', mapErr);
            }
            setNotifications(formattedNotifications);
            setShowNotificationBadge(formattedNotifications.some(n => !n.read));
            setNotificationError(null);
            toast.success('Notifications refreshed');
        } catch (error) {
            console.error('Error refreshing notifications:', error);
            setNotificationError('Failed to refresh notifications');
            toast.error('Failed to refresh notifications');
        } finally {
            setIsLoadingNotifications(false);
        }
    };

    const getNotificationIcon = (type) => {
        switch (type) {
            case 'sensor_threshold':
                return <ExclamationTriangleIcon className="h-5 w-5 text-yellow-500" />;
            case 'water_availability':
                return <CheckCircleIcon className="h-5 w-5 text-green-500" />;
            case 'system_alert':
                return <ExclamationTriangleIcon className="h-5 w-5 text-red-500" />;
            case 'system_info':
                return <InformationCircleIcon className="h-5 w-5 text-blue-500" />;
            case 'watering_started':
                return <CloudArrowDownIcon className="h-5 w-5 text-blue-500" />;
            case 'watering_stopped':
                return <XMarkIcon className="h-5 w-5 text-red-500" />;
            case 'schedule_created':
                return <CalendarIcon className="h-5 w-5 text-purple-500" />;
            case 'schedule_deleted':
                return <TrashIcon className="h-5 w-5 text-orange-500" />;
            case 'user_activity':
                return <UserIcon className="h-5 w-5 text-indigo-500" />;
            case 'security_alert':
                return <ShieldCheckIcon className="h-5 w-5 text-red-600" />;
            default:
                return <BellIcon className="h-5 w-5 text-gray-500" />;
        }
    };

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

    const getNotificationUrgency = (type, priority) => {
        // Determine urgency based on notification type and priority
        const urgencyMap = {
            'system_alert': 'high',
            'security_alert': 'critical',
            'sensor_threshold': priority === 'high' ? 'high' : 'medium',
            'watering_started': 'low',
            'watering_stopped': 'low',
            'schedule_created': 'low',
            'schedule_deleted': 'medium',
            'user_activity': 'low',
            'water_availability': 'medium',
            'system_info': 'low'
        };
        
        return urgencyMap[type] || 'low';
    };

    const sendEmailNotification = async (notification) => {
        try {
            const urgency = getNotificationUrgency(notification.type, notification.priority);
            
            // Only send emails for medium, high, and critical priority notifications
            if (['medium', 'high', 'critical'].includes(urgency)) {
                await axios.post('/api/notifications/send-email', {
                    notification_id: notification._id,
                    urgency: urgency,
                    type: notification.type,
                    title: notification.title || notification.type,
                    message: notification.message,
                    priority: notification.priority
                });
            }
        } catch (error) {
            console.error('Error sending email notification:', error);
        }
    };

    const handleNewNotification = (notification) => {
        // Add to notifications list
        setNotifications(prev => [notification, ...prev]);
        setShowNotificationBadge(true);
        
        // Send email notification based on urgency
        sendEmailNotification(notification);
        
        // Show toast with urgency-based styling
        const urgency = getNotificationUrgency(notification.type, notification.priority);
        const toastOptions = {
            duration: urgency === 'critical' ? 5000 : 3000,
            style: {
                background: urgency === 'critical' ? '#fef2f2' : 
                           urgency === 'high' ? '#fffbeb' : 
                           urgency === 'medium' ? '#eff6ff' : '#f0fdf4',
                color: urgency === 'critical' ? '#dc2626' : 
                       urgency === 'high' ? '#d97706' : 
                       urgency === 'medium' ? '#2563eb' : '#16a34a',
                border: urgency === 'critical' ? '1px solid #fecaca' : 
                        urgency === 'high' ? '1px solid #fed7aa' : 
                        urgency === 'medium' ? '1px solid #bfdbfe' : '1px solid #bbf7d0',
                fontSize: '14px',
                fontWeight: urgency === 'critical' ? 'bold' : 'normal'
            }
        };
        
        toast.success(`${notification.title || notification.type}: ${notification.message}`, toastOptions);
    };

    return (
        <>
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
                    <div className="flex items-center justify-between h-16 px-4 border-b border-green-100">
                        {!sidebarCollapsed && (
                            <Link href="/" className="flex items-center space-x-2 text-xl font-semibold bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent">
                                <svg viewBox="0 0 24 24" className="w-8 h-8" fill="currentColor">
                                    <path d="M12,2 A10,10 0 0,1 12,22 A10,10 0 0,1 12,2 M12,5 A3,3 0 0,0 9,8 A3,3 0 0,0 12,11 A3,3 0 0,0 15,8 A3,3 0 0,0 12,5 M12,8 A3,3 0 0,0 15,11 A3,3 0 0,0 18,8 A3,3 0 0,0 15,5 A3,3 0 0,0 12,8 M12,8 A3,3 0 0,1 9,11 A3,3 0 0,1 6,8 A3,3 0 0,1 9,5 A3,3 0 0,1 12,8 M12,11 A3,3 0 0,1 15,14 A3,3 0 0,1 12,17 A3,3 0 0,1 9,14 A3,3 0 0,1 12,11" />
                                </svg>
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
                            className="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                            title={sidebarCollapsed ? "Expand sidebar" : "Collapse sidebar"}
                        >
                            {sidebarCollapsed ? (
                                <ChevronRightIcon className="h-5 w-5" />
                            ) : (
                                <ChevronLeftIcon className="h-5 w-5" />
                            )}
                        </button>
                    </div>

                    {/* Navigation Menu */}
                    <nav className="flex-1 px-4 pt-6">
                        <div className="space-y-2">
                            {navigation.map((item) => {
                                const isActive = window.location.pathname === new URL(item.href, window.location.origin).pathname;
                                return (
                                    <div key={item.name} className="relative group">
                                        <Link
                                            href={item.href}
                                            className={`flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ${
                                                isActive 
                                                    ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-lg' 
                                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                            }`}
                                        >
                                            <item.icon className={`h-5 w-5 ${isActive ? 'text-white' : 'text-gray-400'}`} />
                                            {!sidebarCollapsed && (
                                                <span className="ml-3">{item.name}</span>
                                            )}
                                        </Link>
                                        
                                        {/* Tooltip for collapsed sidebar */}
                                        {sidebarCollapsed && (
                                            <div className="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
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
                    <div className="px-4 pb-6">
                        <div className="border-t border-green-100 pt-4">
                            {!sidebarCollapsed && (
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 font-medium">RainBasin Pro</p>
                                    <p className="text-xs text-gray-400 mt-1">Smart Garden System</p>
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
                        <div className="flex items-center justify-between h-16 px-4 border-b border-green-100">
                            <Link href="/" className="flex items-center space-x-2 text-xl font-semibold bg-gradient-to-r from-[#09203f] to-[#537895] bg-clip-text text-transparent">
                                <svg viewBox="0 0 24 24" className="w-8 h-8" fill="currentColor">
                                    <path d="M12,2 A10,10 0 0,1 12,22 A10,10 0 0,1 12,2 M12,5 A3,3 0 0,0 9,8 A3,3 0 0,0 12,11 A3,3 0 0,0 15,8 A3,3 0 0,0 12,5 M12,8 A3,3 0 0,0 15,11 A3,3 0 0,0 18,8 A3,3 0 0,0 15,5 A3,3 0 0,0 12,8 M12,8 A3,3 0 0,1 9,11 A3,3 0 0,1 6,8 A3,3 0 0,1 9,5 A3,3 0 0,1 12,8 M12,11 A3,3 0 0,1 15,14 A3,3 0 0,1 12,17 A3,3 0 0,1 9,14 A3,3 0 0,1 12,11" />
                                </svg>
                                <i className="bi bi-flower1 text-2xl text-[#09203f]"></i>
                                <span>RainBasin Pro</span>
                            </Link>
                            <button
                                onClick={() => setSidebarOpen(false)}
                                className="p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors"
                            >
                                <XMarkIcon className="h-6 w-6" />
                            </button>
                        </div>

                        <div className="flex-1 px-4 pt-6">
                            <div className="space-y-2">
                                {navigation.map((item) => {
                                    const isActive = window.location.pathname === new URL(item.href, window.location.origin).pathname;
                                    return (
                                        <Link
                                            key={item.name}
                                            href={item.href}
                                            onClick={() => setSidebarOpen(false)}
                                            className={`flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ${
                                                isActive 
                                                    ? 'bg-gradient-to-r from-[#09203f] to-[#537895] text-white shadow-lg' 
                                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                            }`}
                                        >
                                            <item.icon className={`h-5 w-5 ${isActive ? 'text-white' : 'text-gray-400'}`} />
                                            <span className="ml-3">{item.name}</span>
                                        </Link>
                                    );
                                })}
                            </div>
                        </div>
                        
                        <div className="px-4 pb-6">
                            <div className="border-t border-green-100 pt-4">
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
                                    
                                    {/* Desktop breadcrumb */}
                                    <div className="hidden lg:block ml-4">
                                        <h1 className="text-lg font-semibold text-gray-900">Dashboard</h1>
                                        <p className="text-sm text-gray-500">Manage your smart garden system</p>
                                    </div>
                                </div>
                                
                                <div className="flex items-center space-x-2 lg:space-x-4 pr-2 lg:pr-4">
                                {/* Notifications */}
                                <Menu as="div" className="relative">
                                    <Menu.Button 
                                        className="relative p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                                        onClick={() => setShowNotificationBadge(false)}
                                    >
                                            <BellIcon className="h-5 w-5 lg:h-6 lg:w-6" />
                                        {showNotificationBadge && (
                                                <span className="absolute -top-1 -right-1 h-4 w-4 lg:h-5 lg:w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold animate-pulse">
                                                {notifications.filter(n => !n.read).length}
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
                                                    <button
                                                        onClick={() => setShowNotificationSettings(true)}
                                                        className="p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors"
                                                        title="Notification Settings"
                                                    >
                                                        <Cog6ToothIcon className="h-4 w-4" />
                                                    </button>
                                                    {notifications.some(n => !n.read) && (
                                                        <button
                                                            onClick={markAllAsRead}
                                                            className="text-xs text-blue-600 hover:text-blue-800 font-medium"
                                                        >
                                                            Mark all as read
                                                        </button>
                                                    )}
                                                    <button
                                                        onClick={refreshNotifications}
                                                        className={`p-1 rounded-full hover:bg-gray-100 ${isLoadingNotifications ? 'animate-spin' : ''}`}
                                                        disabled={isLoadingNotifications}
                                                    >
                                                        <ArrowPathIcon className="h-4 w-4 text-gray-500" />
                                                    </button>
                                                </div>
                                            </div>
                                            <div className="max-h-64 overflow-y-auto">
                                                {isLoadingNotifications ? (
                                                    <div className="px-4 py-3 text-center text-sm text-gray-500">
                                                        Loading notifications...
                                                    </div>
                                                ) : notificationError ? (
                                                    <div className="px-4 py-3 text-center text-sm text-red-500">
                                                        {notificationError}
                                                        <button
                                                            onClick={refreshNotifications}
                                                            className="block mx-auto mt-2 text-blue-600 hover:text-blue-800"
                                                        >
                                                            Try again
                                                        </button>
                                                    </div>
                                                ) : notifications.length === 0 ? (
                                                    <div className="px-4 py-3 text-center text-sm text-gray-400">
                                                        No notifications
                                                    </div>
                                                ) : (
                                                    notifications.map((notification) => (
                                                        <div
                                                            key={notification._id}
                                                            className={`p-3 mb-2 bg-white rounded shadow-sm border-l-4 ${getNotificationPriorityClass(notification.priority)}`}
                                                        >
                                                            <div className="flex items-center justify-between">
                                                                <div className="font-semibold text-sm">{notification.title || notification.type}</div>
                                                                <span className="text-xs text-gray-400">{notification.time}</span>
                                                            </div>
                                                            <div className="text-xs text-gray-700 mt-1">{notification.message}</div>
                                                        </div>
                                                    ))
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
                                                            href={route('profile.edit')}
                                                            className={`${active ? 'bg-gray-100' : ''} block px-4 py-3 text-sm font-medium text-gray-700 rounded-lg mx-2`}
                                                        >
                                                            Profile
                                                        </Link>
                                                    )}
                                                </Menu.Item>
                                                <div className="border-t border-gray-100 my-2 mx-2" />
                                                <Menu.Item>
                                                    {({ active }) => (
                                                        <Link
                                                            href={route('logout')}
                                                            method="post"
                                                            as="button"
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
                                            href={route('logout')}
                                            method="post"
                                            as="button"
                                                className="btn-responsive bg-red-600 text-white hover:bg-red-700"
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

                {/* Notification Settings Modal */}
                <NotificationSettings 
                    isOpen={showNotificationSettings} 
                    onClose={() => setShowNotificationSettings(false)} 
                />
            </div>
        </>
    );
}
