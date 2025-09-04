import { useState, useEffect } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import { 
    UserIcon, 
    UserGroupIcon, 
    ShieldCheckIcon, 
    PlusIcon,
    PencilIcon,
    TrashIcon,
    LockClosedIcon,
    EyeIcon,
    EyeSlashIcon,
    InformationCircleIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    CheckIcon,
    XMarkIcon
} from '@heroicons/react/24/outline';
import { toast } from 'react-hot-toast';

export default function UserManagement() {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filterText, setFilterText] = useState('');
    const [selectedRole, setSelectedRole] = useState('all');
    const [selectedStatus, setSelectedStatus] = useState('all');
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);
    const [roles, setRoles] = useState([]);

    // Form states
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        role: 'user',
        password: '',
        confirmPassword: '',
        permissions: []
    });

    const permissions = [
        { id: 'dashboard_view', name: 'View Dashboard', description: 'Access to view dashboard and sensor data' },
        { id: 'watering_control', name: 'Watering Control', description: 'Start/stop watering and manage schedules' },
        { id: 'sensor_history', name: 'Sensor History', description: 'View historical sensor data and reports' },
        { id: 'user_management', name: 'User Management', description: 'Create, edit, and delete user accounts' },
        { id: 'audit_logs', name: 'Audit Logs', description: 'View system audit logs and security events' },
        { id: 'system_settings', name: 'System Settings', description: 'Modify system configuration and settings' }
    ];

    useEffect(() => {
        fetchUsers();
        fetchRoles();
    }, []);

    const fetchUsers = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/users', {
                params: {
                    role: selectedRole,
                    status: selectedStatus,
                    search: filterText
                }
            });
            setUsers(response.data);
        } catch (error) {
            console.error('Error fetching users:', error);
            toast.error('Failed to load users');
        } finally {
            setLoading(false);
        }
    };

    const fetchRoles = async () => {
        try {
            const response = await axios.get('/api/roles');
            setRoles(response.data);
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, [selectedRole, selectedStatus, filterText]);

    const handleCreateUser = async () => {
        if (formData.password !== formData.confirmPassword) {
            toast.error('Passwords do not match');
            return;
        }

        try {
            await axios.post('/api/users', formData);
            toast.success('User created successfully');
            setShowCreateModal(false);
            resetForm();
            fetchUsers();
        } catch (error) {
            console.error('Error creating user:', error);
            toast.error('Failed to create user');
        }
    };

    const handleUpdateUser = async () => {
        try {
            await axios.put(`/api/users/${selectedUser.id}`, formData);
            toast.success('User updated successfully');
            setShowEditModal(false);
            resetForm();
            fetchUsers();
        } catch (error) {
            console.error('Error updating user:', error);
            toast.error('Failed to update user');
        }
    };

    const handleDeleteUser = async () => {
        try {
            await axios.delete(`/api/users/${selectedUser.id}`);
            toast.success('User deleted successfully');
            setShowDeleteModal(false);
            setSelectedUser(null);
            fetchUsers();
        } catch (error) {
            console.error('Error deleting user:', error);
            toast.error('Failed to delete user');
        }
    };

    const resetForm = () => {
        setFormData({
            name: '',
            email: '',
            role: 'user',
            password: '',
            confirmPassword: '',
            permissions: []
        });
    };

    const openEditModal = (user) => {
        setSelectedUser(user);
        setFormData({
            name: user.name,
            email: user.email,
            role: user.role,
            password: '',
            confirmPassword: '',
            permissions: user.permissions || []
        });
        setShowEditModal(true);
    };

    const openDeleteModal = (user) => {
        setSelectedUser(user);
        setShowDeleteModal(true);
    };

    const getRoleColor = (role) => {
        switch (role) {
            case 'admin':
                return 'bg-red-100 text-red-800';
            case 'manager':
                return 'bg-purple-100 text-purple-800';
            case 'user':
                return 'bg-blue-100 text-blue-800';
            case 'viewer':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'inactive':
                return 'bg-red-100 text-red-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const handlePermissionChange = (permissionId) => {
        setFormData(prev => ({
            ...prev,
            permissions: prev.permissions.includes(permissionId)
                ? prev.permissions.filter(id => id !== permissionId)
                : [...prev.permissions, permissionId]
        }));
    };

    return (
        <DashboardLayout>
            <Head title="User Management" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">User Management</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Manage user accounts, roles, and permissions for system access control
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Manage user accounts, roles, and permissions for system access control</p>
                </div>

                {/* Filters and Actions */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex flex-col sm:flex-row gap-3 items-center">
                        {/* Search */}
                        <div className="flex-1">
                            <div className="relative">
                                <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Search users..."
                                    value={filterText}
                                    onChange={e => setFilterText(e.target.value)}
                                    className="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>

                        {/* Role Filter */}
                        <div className="sm:w-32">
                            <select
                                value={selectedRole}
                                onChange={e => setSelectedRole(e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Roles</option>
                                {roles.map(role => (
                                    <option key={role} value={role}>{role.toUpperCase()}</option>
                                ))}
                            </select>
                        </div>

                        {/* Status Filter */}
                        <div className="sm:w-32">
                            <select
                                value={selectedStatus}
                                onChange={e => setSelectedStatus(e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>

                        {/* Create User Button */}
                        <button
                            onClick={() => setShowCreateModal(true)}
                            className="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2"
                        >
                            <PlusIcon className="h-4 w-4" />
                            Add User
                        </button>
                    </div>
                </div>

                {/* Users Table */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    {loading ? (
                        <div className="flex items-center justify-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span className="ml-3 text-blue-600 font-medium">Loading users...</span>
                        </div>
                    ) : users.length === 0 ? (
                        <div className="text-center py-8">
                            <UserGroupIcon className="mx-auto h-12 w-12 text-gray-300 mb-4" />
                            <p className="text-lg font-medium text-gray-500 mb-2">No users found</p>
                            <p className="text-sm text-gray-400">Create your first user account</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-gray-200">
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">User</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Role</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Permissions</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Last Login</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {users.map((user) => (
                                        <tr key={user.id} className="border-b border-gray-100 hover:bg-gray-50">
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <UserIcon className="h-4 w-4 text-gray-600" />
                                                    </div>
                                                    <div>
                                                        <p className="font-medium text-gray-900">{user.name}</p>
                                                        <p className="text-xs text-gray-500">{user.email}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRoleColor(user.role)}`}>
                                                    {user.role.toUpperCase()}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(user.status)}`}>
                                                    {user.status.toUpperCase()}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex flex-wrap gap-1">
                                                    {user.permissions?.slice(0, 3).map(permission => (
                                                        <span key={permission} className="px-1 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">
                                                            {permission}
                                                        </span>
                                                    ))}
                                                    {user.permissions?.length > 3 && (
                                                        <span className="px-1 py-0.5 bg-gray-100 text-gray-800 rounded text-xs">
                                                            +{user.permissions.length - 3}
                                                        </span>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="text-xs text-gray-500">
                                                    {user.last_login ? new Date(user.last_login).toLocaleDateString() : 'Never'}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex gap-2">
                                                    <button
                                                        onClick={() => openEditModal(user)}
                                                        className="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors"
                                                        title="Edit user"
                                                    >
                                                        <PencilIcon className="h-4 w-4" />
                                                    </button>
                                                    <button
                                                        onClick={() => openDeleteModal(user)}
                                                        className="p-1 text-red-600 hover:bg-red-100 rounded transition-colors"
                                                        title="Delete user"
                                                    >
                                                        <TrashIcon className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>

            {/* Create User Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Create New User</h3>
                            
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input
                                        type="text"
                                        value={formData.name}
                                        onChange={e => setFormData({...formData, name: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input
                                        type="email"
                                        value={formData.email}
                                        onChange={e => setFormData({...formData, email: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <select
                                        value={formData.role}
                                        onChange={e => setFormData({...formData, role: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="user">User</option>
                                        <option value="manager">Manager</option>
                                        <option value="admin">Admin</option>
                                        <option value="viewer">Viewer</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input
                                        type="password"
                                        value={formData.password}
                                        onChange={e => setFormData({...formData, password: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                    <input
                                        type="password"
                                        value={formData.confirmPassword}
                                        onChange={e => setFormData({...formData, confirmPassword: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                                    <div className="space-y-2 max-h-32 overflow-y-auto">
                                        {permissions.map(permission => (
                                            <label key={permission.id} className="flex items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    checked={formData.permissions.includes(permission.id)}
                                                    onChange={() => handlePermissionChange(permission.id)}
                                                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <div>
                                                    <span className="text-sm font-medium text-gray-900">{permission.name}</span>
                                                    <p className="text-xs text-gray-500">{permission.description}</p>
                                                </div>
                                            </label>
                                        ))}
                                    </div>
                                </div>
                            </div>
                            
                            <div className="flex justify-end gap-3 mt-6">
                                <button
                                    onClick={() => { setShowCreateModal(false); resetForm(); }}
                                    className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={handleCreateUser}
                                    className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                                >
                                    Create User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Edit User Modal */}
            {showEditModal && selectedUser && (
                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Edit User</h3>
                            
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input
                                        type="text"
                                        value={formData.name}
                                        onChange={e => setFormData({...formData, name: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input
                                        type="email"
                                        value={formData.email}
                                        onChange={e => setFormData({...formData, email: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <select
                                        value={formData.role}
                                        onChange={e => setFormData({...formData, role: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="user">User</option>
                                        <option value="manager">Manager</option>
                                        <option value="admin">Admin</option>
                                        <option value="viewer">Viewer</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">New Password (optional)</label>
                                    <input
                                        type="password"
                                        value={formData.password}
                                        onChange={e => setFormData({...formData, password: e.target.value})}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Leave blank to keep current password"
                                    />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                                    <div className="space-y-2 max-h-32 overflow-y-auto">
                                        {permissions.map(permission => (
                                            <label key={permission.id} className="flex items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    checked={formData.permissions.includes(permission.id)}
                                                    onChange={() => handlePermissionChange(permission.id)}
                                                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <div>
                                                    <span className="text-sm font-medium text-gray-900">{permission.name}</span>
                                                    <p className="text-xs text-gray-500">{permission.description}</p>
                                                </div>
                                            </label>
                                        ))}
                                    </div>
                                </div>
                            </div>
                            
                            <div className="flex justify-end gap-3 mt-6">
                                <button
                                    onClick={() => { setShowEditModal(false); resetForm(); }}
                                    className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={handleUpdateUser}
                                    className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                                >
                                    Update User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Delete User Modal */}
            {showDeleteModal && selectedUser && (
                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Delete User</h3>
                            <p className="text-sm text-gray-600 mb-6">
                                Are you sure you want to delete <strong>{selectedUser.name}</strong>? This action cannot be undone.
                            </p>
                            
                            <div className="flex justify-end gap-3">
                                <button
                                    onClick={() => { setShowDeleteModal(false); setSelectedUser(null); }}
                                    className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={handleDeleteUser}
                                    className="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
                                >
                                    Delete User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
} 