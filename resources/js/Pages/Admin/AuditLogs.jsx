import { useState, useEffect } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import { 
    ClockIcon, 
    UserIcon, 
    ShieldCheckIcon, 
    ExclamationTriangleIcon,
    InformationCircleIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    CalendarIcon,
    DocumentTextIcon,
    CogIcon,
    UserGroupIcon,
    LockClosedIcon
} from '@heroicons/react/24/outline';
import { toast } from 'react-hot-toast';

export default function AuditLogs() {
    const [auditLogs, setAuditLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filterText, setFilterText] = useState('');
    const [selectedUser, setSelectedUser] = useState('all');
    const [selectedAction, setSelectedAction] = useState('all');
    const [selectedDate, setSelectedDate] = useState('');
    const [users, setUsers] = useState([]);
    const [actions, setActions] = useState([]);

    useEffect(() => {
        fetchAuditLogs();
        fetchUsers();
        fetchActions();
    }, []);

    const fetchAuditLogs = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/audit-logs', {
                params: {
                    user: selectedUser,
                    action: selectedAction,
                    date: selectedDate,
                    search: filterText
                }
            });
            setAuditLogs(response.data);
        } catch (error) {
            console.error('Error fetching audit logs:', error);
            toast.error('Failed to load audit logs');
        } finally {
            setLoading(false);
        }
    };

    const fetchUsers = async () => {
        try {
            const response = await axios.get('/api/users');
            setUsers(response.data);
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    };

    const fetchActions = async () => {
        try {
            const response = await axios.get('/api/audit-actions');
            setActions(response.data);
        } catch (error) {
            console.error('Error fetching actions:', error);
        }
    };

    useEffect(() => {
        fetchAuditLogs();
    }, [selectedUser, selectedAction, selectedDate, filterText]);

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
                return <InformationCircleIcon className="h-4 w-4 text-gray-500" />;
        }
    };

    const getActionColor = (action) => {
        switch (action) {
            case 'login':
                return 'bg-blue-100 text-blue-800';
            case 'logout':
                return 'bg-gray-100 text-gray-800';
            case 'watering_start':
                return 'bg-green-100 text-green-800';
            case 'watering_stop':
                return 'bg-red-100 text-red-800';
            case 'schedule_create':
                return 'bg-purple-100 text-purple-800';
            case 'schedule_delete':
                return 'bg-orange-100 text-orange-800';
            case 'settings_change':
                return 'bg-yellow-100 text-yellow-800';
            case 'user_create':
                return 'bg-indigo-100 text-indigo-800';
            case 'user_delete':
                return 'bg-red-100 text-red-800';
            case 'permission_change':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleString();
    };

    const getSeverityColor = (severity) => {
        switch (severity) {
            case 'high':
                return 'text-red-600';
            case 'medium':
                return 'text-yellow-600';
            case 'low':
                return 'text-green-600';
            default:
                return 'text-gray-600';
        }
    };

    return (
        <DashboardLayout>
            <Head title="Audit Logs" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">Audit Logs</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Track all system activities and user actions for security monitoring
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Monitor system activities, user actions, and security events</p>
                </div>

                {/* Filters */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex flex-col sm:flex-row gap-3">
                        {/* Search */}
                        <div className="flex-1">
                            <div className="relative">
                                <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Search audit logs..."
                                    value={filterText}
                                    onChange={e => setFilterText(e.target.value)}
                                    className="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>

                        {/* User Filter */}
                        <div className="sm:w-48">
                            <select
                                value={selectedUser}
                                onChange={e => setSelectedUser(e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Users</option>
                                {users.map(user => (
                                    <option key={user.id} value={user.id}>{user.name}</option>
                                ))}
                            </select>
                        </div>

                        {/* Action Filter */}
                        <div className="sm:w-48">
                            <select
                                value={selectedAction}
                                onChange={e => setSelectedAction(e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Actions</option>
                                {actions.map(action => (
                                    <option key={action} value={action}>{action.replace('_', ' ').toUpperCase()}</option>
                                ))}
                            </select>
                        </div>

                        {/* Date Filter */}
                        <div className="sm:w-48">
                            <input
                                type="date"
                                value={selectedDate}
                                onChange={e => setSelectedDate(e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>
                </div>

                {/* Audit Logs Table */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    {loading ? (
                        <div className="flex items-center justify-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span className="ml-3 text-blue-600 font-medium">Loading audit logs...</span>
                        </div>
                    ) : auditLogs.length === 0 ? (
                        <div className="text-center py-8">
                            <ShieldCheckIcon className="mx-auto h-12 w-12 text-gray-300 mb-4" />
                            <p className="text-lg font-medium text-gray-500 mb-2">No audit logs found</p>
                            <p className="text-sm text-gray-400">Try adjusting your filters or check back later</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-gray-200">
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Action</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">User</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Details</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Severity</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Timestamp</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {auditLogs.map((log, index) => (
                                        <tr key={log.id} className="border-b border-gray-100 hover:bg-gray-50">
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-2">
                                                    {getActionIcon(log.action)}
                                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getActionColor(log.action)}`}>
                                                        {log.action.replace('_', ' ').toUpperCase()}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-2">
                                                    <UserIcon className="h-4 w-4 text-gray-400" />
                                                    <span className="font-medium text-gray-900">{log.user_name}</span>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="max-w-xs">
                                                    <p className="text-gray-900 text-sm">{log.description}</p>
                                                    {log.details && (
                                                        <p className="text-gray-500 text-xs mt-1">{log.details}</p>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`text-xs font-medium ${getSeverityColor(log.severity)}`}>
                                                    {log.severity.toUpperCase()}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-1">
                                                    <ClockIcon className="h-3 w-3 text-gray-400" />
                                                    <span className="text-xs text-gray-500">{formatDate(log.timestamp)}</span>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="text-xs text-gray-500 font-mono">{log.ip_address}</span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
} 