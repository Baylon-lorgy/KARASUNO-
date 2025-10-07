import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import StaffLayout from '@/Layouts/StaffLayout';
import { 
    UserGroupIcon,
    PlusIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    UserIcon,
    ShieldCheckIcon,
    PencilIcon,
    TrashIcon,
    LockClosedIcon,
    EyeIcon,
    EyeSlashIcon,
    InformationCircleIcon,
    CheckIcon,
    XMarkIcon,
    EnvelopeIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    UserPlusIcon,
    ClipboardDocumentIcon
} from '@heroicons/react/24/outline';
import { toast } from 'react-hot-toast';

export default function StaffUserManagement({ auth }) {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [roleFilter, setRoleFilter] = useState('all');
    const [statusFilter, setStatusFilter] = useState('all');
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);
    const [stats, setStats] = useState({
        total: 0,
        pending: 0,
        active: 0,
        inactive: 0
    });

    // Form states
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        role: 'staff',
        permissions: []
    });

    const staffUser = auth.staffUser;

    // Helper function to check permissions
    const hasPermission = (permission) => {
        return staffUser.permissions.includes(permission);
    };

    const rolePermissions = {
        staff: [
            { id: 'dashboard_view', name: 'View Dashboard', description: 'Access to view dashboard and sensor data' },
            { id: 'watering_control', name: 'Watering Control', description: 'Start/stop watering and manage schedules' },
            { id: 'sensor_history', name: 'Sensor History', description: 'View historical sensor data and reports' }
        ],
        manager: [
            { id: 'dashboard_view', name: 'View Dashboard', description: 'Access to view dashboard and sensor data' },
            { id: 'watering_control', name: 'Watering Control', description: 'Start/stop watering and manage schedules' },
            { id: 'sensor_history', name: 'Sensor History', description: 'View historical sensor data and reports' },
            { id: 'user_management', name: 'User Management', description: 'Create, edit, and delete user accounts' },
            { id: 'system_settings', name: 'System Settings', description: 'Modify system configuration and settings' }
        ],
        admin: [
            { id: 'dashboard_view', name: 'View Dashboard', description: 'Access to view dashboard and sensor data' },
            { id: 'watering_control', name: 'Watering Control', description: 'Start/stop watering and manage schedules' },
            { id: 'sensor_history', name: 'Sensor History', description: 'View historical sensor data and reports' },
            { id: 'user_management', name: 'User Management', description: 'Create, edit, and delete user accounts' },
            { id: 'audit_logs', name: 'Audit Logs', description: 'View system audit logs and security events' },
            { id: 'system_settings', name: 'System Settings', description: 'Modify system configuration and settings' }
        ]
    };

    useEffect(() => {
        fetchUsers();
    }, []);

    const fetchUsers = async () => {
        setLoading(true);
        try {
            const response = await fetch('/staff/api/users');
            if (response.ok) {
                const data = await response.json();
                setUsers(data.users || []);
                
                // Calculate stats
                const total = data.users?.length || 0;
                const pending = data.users?.filter(u => u.status === 'pending').length || 0;
                const active = data.users?.filter(u => u.status === 'active').length || 0;
                const inactive = data.users?.filter(u => u.status === 'inactive').length || 0;
                
                setStats({ total, pending, active, inactive });
            }
        } catch (error) {
            console.error('Error fetching users:', error);
            toast.error('Failed to load users');
        } finally {
            setLoading(false);
        }
    };

    const filteredUsers = users.filter(user => {
        const matchesSearch = user.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            user.email.toLowerCase().includes(searchTerm.toLowerCase());
        const matchesRole = roleFilter === 'all' || user.role === roleFilter;
        const matchesStatus = statusFilter === 'all' || user.status === statusFilter;
        return matchesSearch && matchesRole && matchesStatus;
    });

    const getRoleColor = (role) => {
        switch (role) {
            case 'admin':
                return 'bg-red-100 text-red-800';
            case 'manager':
                return 'bg-yellow-100 text-yellow-800';
            case 'staff':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'inactive':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusIcon = (status) => {
        switch (status) {
            case 'active':
                return <CheckCircleIcon className="h-4 w-4 text-green-600" />;
            case 'pending':
                return <ClockIcon className="h-4 w-4 text-yellow-600" />;
            case 'inactive':
                return <XMarkIcon className="h-4 w-4 text-gray-600" />;
            default:
                return <InformationCircleIcon className="h-4 w-4 text-gray-600" />;
        }
    };

    return (
        <StaffLayout title="User Management">
            <Head title="User Management" />
            
            <div className="space-y-6">
                {/* Header */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h2 className="text-xl font-semibold text-gray-900">Staff User Management</h2>
                            <p className="text-gray-600 mt-1">Manage staff accounts and permissions</p>
                        </div>
                        <UserGroupIcon className="h-8 w-8 text-purple-600" />
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <UserGroupIcon className="h-8 w-8 text-blue-600" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600">Total Users</p>
                                <p className="text-2xl font-semibold text-gray-900">{stats.total}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <ClockIcon className="h-8 w-8 text-yellow-600" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600">Pending</p>
                                <p className="text-2xl font-semibold text-gray-900">{stats.pending}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <CheckCircleIcon className="h-8 w-8 text-green-600" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600">Active</p>
                                <p className="text-2xl font-semibold text-gray-900">{stats.active}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <XMarkIcon className="h-8 w-8 text-gray-600" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600">Inactive</p>
                                <p className="text-2xl font-semibold text-gray-900">{stats.inactive}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Filters */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-lg font-semibold text-gray-900">Filters</h3>
                        <FunnelIcon className="h-6 w-6 text-gray-600" />
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Search Users
                            </label>
                            <div className="relative">
                                <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input
                                    type="text"
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                    placeholder="Search by name or email..."
                                    className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Filter by Role
                            </label>
                            <select
                                value={roleFilter}
                                onChange={(e) => setRoleFilter(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Roles</option>
                                <option value="staff">Staff</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Filter by Status
                            </label>
                            <select
                                value={statusFilter}
                                onChange={(e) => setStatusFilter(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Users List */}
                {loading ? (
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center justify-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span className="ml-3 text-blue-600 font-medium">Loading users...</span>
                        </div>
                    </div>
                ) : (
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Staff Users ({filteredUsers.length})
                            </h3>
                            {hasPermission('user_management') && (
                                <button className="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                                    <PlusIcon className="h-4 w-4 mr-2" />
                                    Invite Staff
                                </button>
                            )}
                        </div>
                        
                        {filteredUsers.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Role
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {filteredUsers.map((user) => (
                                            <tr key={user.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <div className="h-10 w-10 bg-gradient-to-r from-blue-600 to-green-600 rounded-full flex items-center justify-center">
                                                            <span className="text-white font-bold text-sm">
                                                                {user.name.charAt(0).toUpperCase()}
                                                            </span>
                                                        </div>
                                                        <div className="ml-4">
                                                            <div className="text-sm font-medium text-gray-900">{user.name}</div>
                                                            <div className="text-sm text-gray-500">{user.email}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full capitalize ${getRoleColor(user.role)}`}>
                                                        {user.role}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        {getStatusIcon(user.status)}
                                                        <span className={`ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(user.status)}`}>
                                                            {user.status}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {hasPermission('user_management') && (
                                                        <div className="flex space-x-2">
                                                            <button className="text-blue-600 hover:text-blue-900 transition-colors">
                                                                Edit
                                                            </button>
                                                            {user.status === 'pending' && (
                                                                <button className="text-green-600 hover:text-green-900 transition-colors">
                                                                    Approve
                                                                </button>
                                                            )}
                                                        </div>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <UserGroupIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                <p className="text-gray-500">No users found matching your criteria.</p>
                            </div>
                        )}
                    </div>
                )}

                {/* Information */}
                <div className="bg-purple-50 rounded-xl p-6 border border-purple-200">
                    <h4 className="font-medium text-purple-900 mb-2">User Management Information</h4>
                    <ul className="text-sm text-purple-800 space-y-1">
                        <li>• Only managers and admins can manage users</li>
                        <li>• Pending users need approval to become active</li>
                        <li>• Role permissions are automatically assigned</li>
                        <li>• User status shows current account state</li>
                    </ul>
                </div>
            </div>
        </StaffLayout>
    );
} 