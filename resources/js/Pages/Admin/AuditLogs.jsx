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
    LockClosedIcon,
    BellIcon,
    ChartBarIcon,
    GlobeAltIcon,
    DevicePhoneMobileIcon,
    EyeIcon,
    EyeSlashIcon,
    ArrowDownTrayIcon,
    ExclamationCircleIcon,
    CheckCircleIcon,
    XCircleIcon
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
    
    // enhanced features
    const [realTimeMode, setRealTimeMode] = useState(false);
    const [alertSettings, setAlertSettings] = useState({
        highSeverity: true,
        failedLogins: true,
        unauthorizedAccess: true,
        systemChanges: true,
        suspiciousPatterns: true
    });
    const [analyticsData, setAnalyticsData] = useState({
        totalEvents: 0,
        securityIncidents: 0,
        uniqueUsers: 0,
        topActions: [],
        riskScore: 0
    });
    const [selectedTimeRange, setSelectedTimeRange] = useState('24h');
    const [showAdvancedFilters, setShowAdvancedFilters] = useState(false);
    const [exportFormat, setExportFormat] = useState('csv');

    useEffect(() => {
        fetchAuditLogs();
        fetchUsers();
        fetchActions();
        fetchAnalytics();
        
        // set up real-time monitoring if enabled
        if (realTimeMode) {
            const eventSource = new EventSource('/api/audit-logs/stream');
            eventSource.onmessage = (event) => {
                const newLog = JSON.parse(event.data);
                setAuditLogs(prev => [newLog, ...prev.slice(0, 999)]); // keep last 1000 logs
                
                // check for alerts
                checkForAlerts(newLog);
            };
            
            return () => eventSource.close();
        }
    }, [realTimeMode, selectedTimeRange]);

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

    const fetchAnalytics = async () => {
        try {
            const response = await axios.get('/api/audit-analytics', {
                params: { timeRange: selectedTimeRange }
            });
            setAnalyticsData(response.data);
        } catch (error) {
            console.error('Error fetching analytics:', error);
        }
    };

    const checkForAlerts = (log) => {
        const alerts = [];
        
        if (log.severity === 'high' && alertSettings.highSeverity) {
            alerts.push(`High severity event: ${log.action} by ${log.user_name}`);
        }
        
        if (log.action === 'login_failed' && alertSettings.failedLogins) {
            alerts.push(`Failed login attempt from ${log.ip_address}`);
        }
        
        if (log.action === 'unauthorized_access' && alertSettings.unauthorizedAccess) {
            alerts.push(`Unauthorized access attempt by ${log.user_name}`);
        }
        
        alerts.forEach(alert => {
            toast.error(alert, { duration: 5000 });
            // could also send to notification system
        });
    };

    const exportLogs = async () => {
        try {
            const response = await axios.get('/api/audit-logs/export', {
                params: {
                    format: exportFormat,
                    user: selectedUser,
                    action: selectedAction,
                    date: selectedDate,
                    search: filterText,
                    timeRange: selectedTimeRange
                },
                responseType: 'blob'
            });
            
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `audit-logs-${new Date().toISOString().split('T')[0]}.${exportFormat}`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            
            toast.success(`Audit logs exported successfully`);
        } catch (error) {
            console.error('Error exporting logs:', error);
            toast.error('Failed to export audit logs');
        }
    };

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

    const getRiskScore = (log) => {
        let score = 0;
        
        // severity scoring
        if (log.severity === 'high') score += 10;
        else if (log.severity === 'medium') score += 5;
        else score += 1;
        
        // action scoring
        const highRiskActions = ['user_delete', 'permission_change', 'system_config_change'];
        if (highRiskActions.includes(log.action)) score += 8;
        
        // time-based scoring (off-hours activity)
        const hour = new Date(log.timestamp).getHours();
        if (hour < 6 || hour > 22) score += 3;
        
        return Math.min(score, 20); // max score of 20
    };

    const getRiskColor = (score) => {
        if (score >= 15) return 'text-red-600 bg-red-100';
        if (score >= 10) return 'text-yellow-600 bg-yellow-100';
        if (score >= 5) return 'text-orange-600 bg-orange-100';
        return 'text-green-600 bg-green-100';
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
                {/* Header with enhanced features */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">Audit Logs</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Advanced system monitoring with real-time alerts and analytics
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">
            
                    </p>
                </div>

                {/* Analytics Dashboard */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-4 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500">Total Events</p>
                                <p className="text-lg font-semibold text-gray-900">{analyticsData.totalEvents}</p>
                            </div>
                            <ChartBarIcon className="h-6 w-6 text-blue-500" />
                        </div>
                    </div>
                    
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-4 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500">Security Incidents</p>
                                <p className="text-lg font-semibold text-red-600">{analyticsData.securityIncidents}</p>
                            </div>
                            <ExclamationTriangleIcon className="h-6 w-6 text-red-500" />
                        </div>
                    </div>
                    
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-4 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500">Active Users</p>
                                <p className="text-lg font-semibold text-gray-900">{analyticsData.uniqueUsers}</p>
                            </div>
                            <UserGroupIcon className="h-6 w-6 text-green-500" />
                        </div>
                    </div>
                    
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-4 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500">Risk Score</p>
                                <p className="text-lg font-semibold text-gray-900">{analyticsData.riskScore}/20</p>
                            </div>
                            <ShieldCheckIcon className="h-6 w-6 text-purple-500" />
                        </div>
                    </div>
                    
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-4 border border-gray-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500">Real-time Mode</p>
                                <button
                                    onClick={() => setRealTimeMode(!realTimeMode)}
                                    className={`px-2 py-1 text-xs rounded-full ${
                                        realTimeMode 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-gray-100 text-gray-800'
                                    }`}
                                >
                                    {realTimeMode ? 'ON' : 'OFF'}
                                </button>
                            </div>
                            <BellIcon className="h-6 w-6 text-blue-500" />
                        </div>
                    </div>
                </div>

                {/* Enhanced Filters */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex flex-col gap-3">
                        {/* Basic Filters */}
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

                        {/* Advanced Filters */}
                        <div className="flex items-center justify-between">
                            <button
                                onClick={() => setShowAdvancedFilters(!showAdvancedFilters)}
                                className="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700"
                            >
                                {showAdvancedFilters ? <EyeSlashIcon className="h-4 w-4" /> : <EyeIcon className="h-4 w-4" />}
                                Advanced Filters
                            </button>
                            
                            <div className="flex gap-2">
                                <select
                                    value={selectedTimeRange}
                                    onChange={e => setSelectedTimeRange(e.target.value)}
                                    className="px-3 py-1 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="1h">Last Hour</option>
                                    <option value="24h">Last 24 Hours</option>
                                    <option value="7d">Last 7 Days</option>
                                    <option value="30d">Last 30 Days</option>
                                    <option value="90d">Last 90 Days</option>
                                </select>
                                
                                <button
                                    onClick={exportLogs}
                                    className="flex items-center gap-1 px-3 py-1 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                >
                                    <ArrowDownTrayIcon className="h-3 w-3" />
                                    Export
                                </button>
                            </div>
                        </div>

                        {/* Advanced Filter Options */}
                        {showAdvancedFilters && (
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-2">Alert Settings</label>
                                    <div className="space-y-2">
                                        {Object.entries(alertSettings).map(([key, value]) => (
                                            <label key={key} className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={value}
                                                    onChange={e => setAlertSettings(prev => ({
                                                        ...prev,
                                                        [key]: e.target.checked
                                                    }))}
                                                    className="mr-2"
                                                />
                                                <span className="text-xs text-gray-600">
                                                    {key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())}
                                                </span>
                                            </label>
                                        ))}
                                    </div>
                                </div>
                                
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-2">Export Format</label>
                                    <select
                                        value={exportFormat}
                                        onChange={e => setExportFormat(e.target.value)}
                                        className="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="csv">CSV</option>
                                        <option value="json">JSON</option>
                                        <option value="pdf">PDF</option>
                                        <option value="xml">XML</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-2">Risk Threshold</label>
                                    <input
                                        type="range"
                                        min="0"
                                        max="20"
                                        defaultValue="10"
                                        className="w-full"
                                    />
                                    <p className="text-xs text-gray-500 mt-1">Filter by risk score</p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                {/* Enhanced Audit Logs Table */}
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
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Risk</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Action</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">User</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Details</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Severity</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Location</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Timestamp</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {auditLogs.map((log, index) => {
                                        const riskScore = getRiskScore(log);
                                        return (
                                            <tr key={log.id} className="border-b border-gray-100 hover:bg-gray-50">
                                                <td className="py-3 px-4">
                                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRiskColor(riskScore)}`}>
                                                        {riskScore}
                                                    </span>
                                                </td>
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
                                                        <GlobeAltIcon className="h-3 w-3 text-gray-400" />
                                                        <span className="text-xs text-gray-500">{log.location || 'Unknown'}</span>
                                                    </div>
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
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
} 