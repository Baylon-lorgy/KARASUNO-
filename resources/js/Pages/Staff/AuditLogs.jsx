import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import StaffLayout from '@/Layouts/StaffLayout';
import { 
    DocumentTextIcon,
    CalendarIcon,
    InformationCircleIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    ClockIcon,
    UserIcon,
    ShieldCheckIcon,
    CogIcon,
    UserGroupIcon,
    LockClosedIcon
} from '@heroicons/react/24/outline';
import { toast } from 'react-hot-toast';

export default function StaffAuditLogs({ auth }) {
    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [dateRange, setDateRange] = useState('7d');
    const [logType, setLogType] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');
    const [users, setUsers] = useState([]);
    const [actions, setActions] = useState([]);

    const staffUser = auth.staffUser;

    // Helper function to check permissions
    const hasPermission = (permission) => {
        return staffUser.permissions.includes(permission);
    };

    useEffect(() => {
        fetchAuditLogs();
        fetchUsers();
        fetchActions();
    }, []);

    const fetchAuditLogs = async () => {
        setLoading(true);
        try {
            const response = await fetch(`/staff/api/audit-logs?range=${dateRange}&type=${logType}&search=${searchTerm}`);
            if (response.ok) {
                const data = await response.json();
                setLogs(data.logs || []);
            }
        } catch (error) {
            console.error('Error fetching audit logs:', error);
            toast.error('Failed to load audit logs');
        } finally {
            setLoading(false);
        }
    };

    const fetchUsers = async () => {
        try {
            const response = await fetch('/staff/api/users');
            if (response.ok) {
                const data = await response.json();
                setUsers(data.users || []);
            }
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    };

    const fetchActions = async () => {
        try {
            const response = await fetch('/staff/api/audit-actions');
            if (response.ok) {
                const data = await response.json();
                setActions(data.actions || []);
            }
        } catch (error) {
            console.error('Error fetching actions:', error);
        }
    };

    useEffect(() => {
        fetchAuditLogs();
    }, [dateRange, logType, searchTerm]);

    const getActionIcon = (action) => {
        switch (action) {
            case 'login':
                return <UserIcon className="h-4 w-4 text-blue-500" />;
            case 'logout':
                return <UserIcon className="h-4 w-4 text-gray-500" />;
            case 'watering_start':
                return <CogIcon className="h-4 w-4 text-green-500" />;
            case 'watering_stop':
                return <CogIcon className="h-4 w-4 text-red-500" />;
            case 'schedule_create':
                return <CalendarIcon className="h-4 w-4 text-purple-500" />;
            case 'schedule_delete':
                return <CalendarIcon className="h-4 w-4 text-orange-500" />;
            case 'settings_change':
                return <CogIcon className="h-4 w-4 text-yellow-500" />;
            case 'user_create':
                return <UserGroupIcon className="h-4 w-4 text-indigo-500" />;
            case 'user_delete':
                return <UserGroupIcon className="h-4 w-4 text-red-500" />;
            case 'permission_change':
                return <LockClosedIcon className="h-4 w-4 text-red-600" />;
            default:
                return <InformationCircleIcon className="h-4 w-4 text-blue-500" />;
        }
    };

    const getActionColor = (action) => {
        switch (action) {
            case 'login':
                return 'bg-blue-50 border-blue-200';
            case 'logout':
                return 'bg-gray-50 border-gray-200';
            case 'watering_start':
                return 'bg-green-50 border-green-200';
            case 'watering_stop':
                return 'bg-red-50 border-red-200';
            case 'schedule_create':
                return 'bg-purple-50 border-purple-200';
            case 'schedule_delete':
                return 'bg-orange-50 border-orange-200';
            case 'settings_change':
                return 'bg-yellow-50 border-yellow-200';
            case 'user_create':
                return 'bg-indigo-50 border-indigo-200';
            case 'user_delete':
                return 'bg-red-50 border-red-200';
            case 'permission_change':
                return 'bg-red-50 border-red-200';
            default:
                return 'bg-blue-50 border-blue-200';
        }
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleString();
    };

    const getSeverityColor = (severity) => {
        switch (severity) {
            case 'high':
                return 'bg-red-100 text-red-800';
            case 'medium':
                return 'bg-yellow-100 text-yellow-800';
            case 'low':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <StaffLayout title="Audit Logs">
            <Head title="Audit Logs" />
            
            <div className="space-y-6">
                {/* Header */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h2 className="text-xl font-semibold text-gray-900">System Audit Logs</h2>
                            <p className="text-gray-600 mt-1">Monitor system activities and security events</p>
                        </div>
                        <DocumentTextIcon className="h-8 w-8 text-red-600" />
                    </div>
                </div>

                {/* Filters */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-lg font-semibold text-gray-900">Filters</h3>
                        <FunnelIcon className="h-6 w-6 text-gray-600" />
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Search Logs
                            </label>
                            <div className="relative">
                                <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input
                                    type="text"
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                    placeholder="Search logs..."
                                    className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Date Range
                            </label>
                            <select
                                value={dateRange}
                                onChange={(e) => setDateRange(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="1d">Last 24 Hours</option>
                                <option value="7d">Last 7 Days</option>
                                <option value="30d">Last 30 Days</option>
                                <option value="90d">Last 90 Days</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Log Type
                            </label>
                            <select
                                value={logType}
                                onChange={(e) => setLogType(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Types</option>
                                <option value="info">Information</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                User Filter
                            </label>
                            <select
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Users</option>
                                {users.map(user => (
                                    <option key={user.id} value={user.id}>{user.name}</option>
                                ))}
                            </select>
                        </div>
                    </div>
                </div>

                {/* Audit Logs */}
                {loading ? (
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center justify-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span className="ml-3 text-blue-600 font-medium">Loading audit logs...</span>
                        </div>
                    </div>
                ) : (
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center justify-between mb-6">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900">
                                    System Logs ({logs.length})
                                </h3>
                                <p className="text-sm text-gray-600">Recent system activities and events</p>
                            </div>
                            <DocumentTextIcon className="h-6 w-6 text-red-600" />
                        </div>
                        
                        {logs.length > 0 ? (
                            <div className="space-y-4">
                                {logs.map((log, index) => (
                                    <div key={index} className={`p-4 rounded-lg border ${getActionColor(log.type)}`}>
                                        <div className="flex items-start justify-between">
                                            <div className="flex items-start space-x-3">
                                                {getActionIcon(log.type)}
                                                <div className="flex-1">
                                                    <div className="flex items-center space-x-2 mb-2">
                                                        <span className="text-sm font-medium text-gray-900">
                                                            {log.title}
                                                        </span>
                                                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full capitalize ${
                                                            log.type === 'error' ? 'bg-red-100 text-red-800' :
                                                            log.type === 'warning' ? 'bg-yellow-100 text-yellow-800' :
                                                            'bg-blue-100 text-blue-800'
                                                        }`}>
                                                            {log.type}
                                                        </span>
                                                        {log.severity && (
                                                            <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getSeverityColor(log.severity)}`}>
                                                                {log.severity}
                                                            </span>
                                                        )}
                                                    </div>
                                                    <p className="text-sm text-gray-600 mb-2">{log.message}</p>
                                                    <div className="flex items-center space-x-4 text-xs text-gray-500">
                                                        <span className="flex items-center">
                                                            <ClockIcon className="h-3 w-3 mr-1" />
                                                            {formatDate(log.timestamp)}
                                                        </span>
                                                        {log.user && (
                                                            <span className="flex items-center">
                                                                <UserIcon className="h-3 w-3 mr-1" />
                                                                {log.user}
                                                            </span>
                                                        )}
                                                        {log.ip && (
                                                            <span className="flex items-center">
                                                                <ShieldCheckIcon className="h-3 w-3 mr-1" />
                                                                {log.ip}
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <DocumentTextIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                <p className="text-gray-500">No audit logs found for the selected criteria.</p>
                            </div>
                        )}
                    </div>
                )}

                {/* Information */}
                <div className="bg-red-50 rounded-xl p-6 border border-red-200">
                    <h4 className="font-medium text-red-900 mb-2">Audit Log Information</h4>
                    <ul className="text-sm text-red-800 space-y-1">
                        <li>• Logs are automatically generated for system activities</li>
                        <li>• Error logs indicate system issues that need attention</li>
                        <li>• Warning logs show potential problems</li>
                        <li>• Information logs track normal system operations</li>
                        <li>• Logs are retained for 90 days for security purposes</li>
                    </ul>
                </div>
            </div>
        </StaffLayout>
    );
} 