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
    XMarkIcon,
    EnvelopeIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    UserPlusIcon,
    ClipboardDocumentIcon
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
    const [showInvitationModal, setShowInvitationModal] = useState(false);
    const [showReactivateModal, setShowReactivateModal] = useState(false);
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [confirmAction, setConfirmAction] = useState(null);
    const [selectedUser, setSelectedUser] = useState(null);
    const [roles] = useState(['staff', 'manager', 'admin']);
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
            // Use the new invitation system to get users
            const response = await fetch('/admin/invitations', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                
                // Combine and deduplicate users by ID to prevent duplicates
                const allUsersMap = new Map();
                
                // Add pending invitations first
                data.pendingInvitations.forEach(user => {
                    allUsersMap.set(user._id || user.id, user);
                });
                
                // Add recent invitations (will overwrite if duplicate)
                data.recentInvitations.forEach(user => {
                    allUsersMap.set(user._id || user.id, user);
                });
                
                const allUsers = Array.from(allUsersMap.values());
                
                // Filter users based on current filters
                let filteredUsers = allUsers;
                
                if (selectedRole !== 'all') {
                    filteredUsers = filteredUsers.filter(u => u.role === selectedRole);
                }
                
                if (selectedStatus !== 'all') {
                    filteredUsers = filteredUsers.filter(u => u.status === selectedStatus);
                }
                
                if (filterText) {
                    filteredUsers = filteredUsers.filter(u => 
                        u.name.toLowerCase().includes(filterText.toLowerCase()) ||
                        u.email.toLowerCase().includes(filterText.toLowerCase())
                    );
                }
                
                setUsers(filteredUsers);
                
                // Calculate stats
                const stats = {
                    total: allUsers.length,
                    pending: allUsers.filter(u => u.status === 'pending').length,
                    active: allUsers.filter(u => u.status === 'active').length,
                    inactive: allUsers.filter(u => u.status === 'inactive').length
                };
                setStats(stats);
            } else {
                toast.error('Failed to load users');
            }
        } catch (error) {
            console.error('Error fetching users:', error);
            toast.error('Failed to load users');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, [selectedRole, selectedStatus, filterText]);

    const handleCreateUser = async () => {
        try {
            // Use the new invitation system instead of API
            const response = await fetch('/admin/invitations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Raw error:', text);
                
                try {
                    const errorData = JSON.parse(text);
                    if (errorData.errors) {
                        // Handle validation errors
                        const errorMessages = Object.values(errorData.errors).flat();
                        toast.error(errorMessages.join(', '));
                    } else {
                        toast.error(errorData.message || errorData.error || 'Failed to send invitation');
                    }
                } catch (parseError) {
                    console.error('Failed to parse error response:', parseError);
                    toast.error('Failed to send invitation');
                }
                return;
            }

            const data = await response.json();
            toast.success(data.message || 'Staff invitation sent successfully');
            setShowCreateModal(false);
            resetForm();
            fetchUsers();
        } catch (error) {
            console.error('Error creating user:', error);
            toast.error('Failed to send invitation');
        }
    };

    const handleUpdateUser = async () => {
        try {
            const response = await fetch(`/admin/invitations/${selectedUser.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Raw error:', text);
                
                try {
                    const errorData = JSON.parse(text);
                    toast.error(errorData.message || errorData.error || 'Failed to update user');
                } catch (parseError) {
                    console.error('Failed to parse error response:', parseError);
                    toast.error('Failed to update user');
                }
                return;
            }

            const data = await response.json();
            toast.success(data.message || 'Staff user updated successfully');
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
            const response = await fetch(`/admin/invitations/${selectedUser.id}/cancel`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Raw error:', text);
                
                try {
                    const errorData = JSON.parse(text);
                    toast.error(errorData.message || errorData.error || 'Failed to delete user');
                } catch (parseError) {
                    console.error('Failed to parse error response:', parseError);
                    toast.error('Failed to delete user');
                }
                return;
            }

            const data = await response.json();
            toast.success(data.message || 'Staff user deleted successfully');
            setShowDeleteModal(false);
            setSelectedUser(null);
            fetchUsers();
        } catch (error) {
            console.error('Error deleting user:', error);
            toast.error('Failed to delete user');
        }
    };

    const handleDeactivateUser = async () => {
        try {
            const response = await fetch(`/admin/invitations/${selectedUser.id}/deactivate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Raw error:', text);
                
                try {
                    const errorData = JSON.parse(text);
                    toast.error(errorData.message || errorData.error || 'Failed to deactivate user');
                } catch (parseError) {
                    console.error('Failed to parse error response:', parseError);
                    toast.error('Failed to deactivate user');
                }
                return;
            }

            const data = await response.json();
            toast.success(data.message || 'Staff user deactivated successfully');
            setShowDeleteModal(false);
            setSelectedUser(null);
            fetchUsers();
        } catch (error) {
            console.error('Error deactivating user:', error);
            toast.error('Failed to deactivate user');
        }
    };

    const handleReactivateUser = async () => {
        try {
            const response = await fetch(`/admin/invitations/${selectedUser.id}/reactivate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Raw error:', text);
                
                try {
                    const errorData = JSON.parse(text);
                    toast.error(errorData.message || errorData.error || 'Failed to reactivate user');
                } catch (parseError) {
                    console.error('Failed to parse error response:', parseError);
                    toast.error('Failed to reactivate user');
                }
                return;
            }

            const data = await response.json();
            toast.success(data.message || 'Staff user reactivated successfully');
            setShowReactivateModal(false);
            setSelectedUser(null);
            fetchUsers();
        } catch (error) {
            console.error('Error reactivating user:', error);
            toast.error('Failed to reactivate user');
        }
    };

    const handleApproveUser = async (userId) => {
        try {
            const response = await fetch(`/admin/invitations/${userId}/approve`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Raw error:', text);
                
                try {
                    const errorData = JSON.parse(text);
                    toast.error(errorData.message || errorData.error || 'Failed to approve user');
                } catch (parseError) {
                    console.error('Failed to parse error response:', parseError);
                    toast.error('Failed to approve user');
                }
                return;
            }

            const data = await response.json();
            toast.success(data.message || 'Staff user approved successfully');
            setShowConfirmModal(false);
            setConfirmAction(null);
            fetchUsers();
        } catch (error) {
            console.error('Error approving user:', error);
            toast.error('Failed to approve user');
        }
    };

    const handleResendInvitation = async (userId) => {
        try {
            const response = await fetch(`/admin/invitations/${userId}/resend`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Raw error:', text);
                
                try {
                    const errorData = JSON.parse(text);
                    toast.error(errorData.message || errorData.error || 'Failed to resend invitation');
                } catch (parseError) {
                    console.error('Failed to parse error response:', parseError);
                    toast.error('Failed to resend invitation');
                }
                return;
            }

            const data = await response.json();
            toast.success(data.message || 'Invitation resent successfully');
            setShowConfirmModal(false);
            setConfirmAction(null);
        } catch (error) {
            console.error('Error resending invitation:', error);
            toast.error('Failed to resend invitation');
        }
    };

    const resetForm = () => {
        setFormData({
            name: '',
            email: '',
            role: 'staff',
            permissions: []
        });
    };

    const openEditModal = (user) => {
        setSelectedUser(user);
        setFormData({
            name: user.name,
            email: user.email,
            role: user.role,
            permissions: user.permissions || []
        });
        setShowEditModal(true);
    };

    const openDeleteModal = (user) => {
        setSelectedUser(user);
        setShowDeleteModal(true);
    };

    const openReactivateModal = (user) => {
        setSelectedUser(user);
        setShowReactivateModal(true);
    };

    const openInvitationModal = (user) => {
        setSelectedUser(user);
        setShowInvitationModal(true);
    };

    const openConfirmModal = (user, action) => {
        setSelectedUser(user);
        setConfirmAction(action);
        setShowConfirmModal(true);
    };

    const executeConfirmedAction = () => {
        if (!selectedUser || !confirmAction) return;

        switch (confirmAction) {
            case 'approve':
                handleApproveUser(selectedUser.id);
                break;
            case 'resend':
                handleResendInvitation(selectedUser.id);
                break;
            default:
                break;
        }
    };

    const getRoleColor = (role) => {
        switch (role) {
            case 'admin':
                return 'bg-red-100 text-red-800';
            case 'manager':
                return 'bg-purple-100 text-purple-800';
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
            case 'inactive':
                return 'bg-red-100 text-red-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusIcon = (status) => {
        switch (status) {
            case 'active':
                return <CheckCircleIcon className="h-4 w-4 text-green-600" />;
            case 'inactive':
                return <XMarkIcon className="h-4 w-4 text-red-600" />;
            case 'pending':
                return <ClockIcon className="h-4 w-4 text-yellow-600" />;
            default:
                return <ExclamationTriangleIcon className="h-4 w-4 text-gray-600" />;
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

    const handleRoleChange = (role) => {
        setFormData(prev => ({
            ...prev,
            role: role,
            permissions: rolePermissions[role]?.map(p => p.id) || []
        }));
    };

    return (
        <DashboardLayout>
            <Head title="User Management" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">Staff Management</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Manage staff accounts with email invitations and approval workflow
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Invite staff members via email and manage their access permissions</p>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <UserGroupIcon className="h-5 w-5 text-blue-600" />
                            <div>
                                <p className="text-xs text-gray-600">Total Staff</p>
                                <p className="text-lg font-bold text-gray-900">{stats.total}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <ClockIcon className="h-5 w-5 text-yellow-600" />
                            <div>
                                <p className="text-xs text-gray-600">Pending</p>
                                <p className="text-lg font-bold text-gray-900">{stats.pending}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <CheckCircleIcon className="h-5 w-5 text-green-600" />
                            <div>
                                <p className="text-xs text-gray-600">Active</p>
                                <p className="text-lg font-bold text-gray-900">{stats.active}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <XMarkIcon className="h-5 w-5 text-red-600" />
                            <div>
                                <p className="text-xs text-gray-600">Inactive</p>
                                <p className="text-lg font-bold text-gray-900">{stats.inactive}</p>
                            </div>
                        </div>
                    </div>
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
                            <UserPlusIcon className="h-4 w-4" />
                            Invite Staff
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
                            <p className="text-lg font-medium text-gray-500 mb-2">No staff members found</p>
                            <p className="text-sm text-gray-400">Invite your first staff member</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-gray-200">
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Staff Member</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Role</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Invitation</th>
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
                                                <div className="flex items-center gap-2">
                                                    {user.invitation_sent_at ? (
                                                        <>
                                                            <EnvelopeIcon className="h-4 w-4 text-blue-600" />
                                                            <span className="text-xs text-gray-600">
                                                                Sent: {new Date(user.invitation_sent_at).toLocaleDateString()}
                                                            </span>
                                                        </>
                                                    ) : (
                                                        <span className="text-xs text-gray-500">Not sent</span>
                                                    )}
                                                    {user.invitation_accepted_at && (
                                                        <span className="text-xs text-green-600 font-medium">
                                                            ✓ Accepted
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
                                                    {user.status === 'pending' && user.invitation_accepted_at && (
                                                        <button
                                                            onClick={() => openConfirmModal(user, 'approve')}
                                                            className="p-1 text-green-600 hover:bg-green-100 rounded transition-colors"
                                                            title="Approve user"
                                                        >
                                                            <CheckCircleIcon className="h-4 w-4" />
                                                        </button>
                                                    )}
                                                    {!user.invitation_accepted_at && (
                                                        <button
                                                            onClick={() => openConfirmModal(user, 'resend')}
                                                            className="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors"
                                                            title="Resend invitation"
                                                        >
                                                            <EnvelopeIcon className="h-4 w-4" />
                                                        </button>
                                                    )}
                                                    <button
                                                        onClick={() => openEditModal(user)}
                                                        className="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors"
                                                        title="Edit user"
                                                    >
                                                        <PencilIcon className="h-4 w-4" />
                                                    </button>
                                                    {user.status === 'active' ? (
                                                        <button
                                                            onClick={() => openDeleteModal(user)}
                                                            className="p-1 text-orange-600 hover:bg-orange-100 rounded transition-colors"
                                                            title="Deactivate user"
                                                        >
                                                            <LockClosedIcon className="h-4 w-4" />
                                                        </button>
                                                    ) : user.status === 'inactive' ? (
                                                        <button
                                                            onClick={() => openReactivateModal(user)}
                                                            className="p-1 text-green-600 hover:bg-green-100 rounded transition-colors"
                                                            title="Reactivate user"
                                                        >
                                                            <CheckCircleIcon className="h-4 w-4" />
                                                        </button>
                                                    ) : null}
                                                    
                                                    {/* Always show delete button for all users */}
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
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">Invite Staff Member</h3>
                            
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
                                        onChange={e => handleRoleChange(e.target.value)}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="staff">Staff</option>
                                        <option value="manager">Manager</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                                    <div className="space-y-2 max-h-32 overflow-y-auto">
                                        {rolePermissions[formData.role]?.map(permission => (
                                            <div key={permission.id} className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                                                <CheckIcon className="h-4 w-4 text-green-600" />
                                                <div>
                                                    <span className="text-sm font-medium text-gray-900">{permission.name}</span>
                                                    <p className="text-xs text-gray-500">{permission.description}</p>
                                                </div>
                                            </div>
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
                                    Send Invitation
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
                                        onChange={e => handleRoleChange(e.target.value)}
                                        className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="staff">Staff</option>
                                        <option value="manager">Manager</option>
                                        <option value="admin">Admin</option>
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
                                        {rolePermissions[formData.role]?.map(permission => (
                                            <div key={permission.id} className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                                                <CheckIcon className="h-4 w-4 text-green-600" />
                                                <div>
                                                    <span className="text-sm font-medium text-gray-900">{permission.name}</span>
                                                    <p className="text-xs text-gray-500">{permission.description}</p>
                                                </div>
                                            </div>
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
                            <div className="flex items-center gap-3 mb-4">
                                <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                                    selectedUser.status === 'active' ? 'bg-orange-100' : 'bg-red-100'
                                }`}>
                                    <TrashIcon className={`h-5 w-5 ${
                                        selectedUser.status === 'active' ? 'text-orange-600' : 'text-red-600'
                                    }`} />
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900">
                                        {selectedUser.status === 'active' ? 'Deactivate Staff Member' : 'Delete Staff Member'}
                                    </h3>
                                    <p className="text-sm text-gray-500">
                                        {selectedUser.status === 'active' ? 'This will disable their access' : 'This action cannot be undone'}
                                    </p>
                                </div>
                            </div>
                            
                            <div className={`border rounded-lg p-4 mb-6 ${
                                selectedUser.status === 'active' ? 'bg-orange-50 border-orange-200' : 'bg-red-50 border-red-200'
                            }`}>
                                <div className="flex items-start gap-3">
                                    <ExclamationTriangleIcon className={`h-5 w-5 mt-0.5 ${
                                        selectedUser.status === 'active' ? 'text-orange-600' : 'text-red-600'
                                    }`} />
                                    <div>
                                        <p className={`text-sm font-medium mb-2 ${
                                            selectedUser.status === 'active' ? 'text-orange-800' : 'text-red-800'
                                        }`}>
                                            {selectedUser.status === 'active' 
                                                ? 'Are you sure you want to deactivate this staff member?'
                                                : 'Are you absolutely sure you want to delete this staff member?'
                                            }
                                        </p>
                                        <div className={`bg-white rounded p-3 border ${
                                            selectedUser.status === 'active' ? 'border-orange-200' : 'border-red-200'
                                        }`}>
                                            <p className="text-sm font-medium text-gray-900">{selectedUser.name}</p>
                                            <p className="text-xs text-gray-600">{selectedUser.email}</p>
                                            <div className="flex items-center gap-2 mt-1">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRoleColor(selectedUser.role)}`}>
                                                    {selectedUser.role.toUpperCase()}
                                                </span>
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(selectedUser.status)}`}>
                                                    {selectedUser.status.toUpperCase()}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="text-sm text-gray-600 mb-6">
                                <p className="mb-2"><strong>This will:</strong></p>
                                {selectedUser.status === 'active' ? (
                                    <ul className="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Disable this staff member's access to the system</li>
                                        <li>Revoke all active permissions immediately</li>
                                        <li>Keep their account data for audit purposes</li>
                                        <li>They can be reactivated later if needed</li>
                                    </ul>
                                ) : (
                                    <ul className="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Permanently remove this staff member from the system</li>
                                        <li>Cancel any pending invitations</li>
                                        <li>Revoke all access permissions immediately</li>
                                        <li>This action cannot be undone</li>
                                    </ul>
                                )}
                            </div>
                            
                            <div className="flex justify-end gap-3">
                                <button
                                    onClick={() => { setShowDeleteModal(false); setSelectedUser(null); }}
                                    className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={selectedUser.status === 'active' ? handleDeactivateUser : handleDeleteUser}
                                    className={`px-4 py-2 text-sm font-medium text-white rounded-md transition-colors flex items-center gap-2 ${
                                        selectedUser.status === 'active' 
                                            ? 'bg-orange-600 hover:bg-orange-700' 
                                            : 'bg-red-600 hover:bg-red-700'
                                    }`}
                                >
                                    <TrashIcon className="h-4 w-4" />
                                    {selectedUser.status === 'active' ? 'Yes, Deactivate Staff Member' : 'Yes, Delete Staff Member'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Reactivate User Modal */}
            {showReactivateModal && selectedUser && (
                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-10 h-10 rounded-full flex items-center justify-center bg-green-100">
                                    <CheckCircleIcon className="h-5 w-5 text-green-600" />
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900">Reactivate Staff Member</h3>
                                    <p className="text-sm text-gray-500">This will restore their access to the system</p>
                                </div>
                            </div>
                            
                            <div className="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                                <div className="flex items-start gap-3">
                                    <CheckCircleIcon className="h-5 w-5 mt-0.5 text-green-600" />
                                    <div>
                                        <p className="text-sm font-medium text-green-800 mb-2">
                                            Are you sure you want to reactivate this staff member?
                                        </p>
                                        <div className="bg-white rounded p-3 border border-green-200">
                                            <p className="text-sm font-medium text-gray-900">{selectedUser.name}</p>
                                            <p className="text-xs text-gray-600">{selectedUser.email}</p>
                                            <div className="flex items-center gap-2 mt-1">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRoleColor(selectedUser.role)}`}>
                                                    {selectedUser.role.toUpperCase()}
                                                </span>
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(selectedUser.status)}`}>
                                                    {selectedUser.status.toUpperCase()}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="text-sm text-gray-600 mb-6">
                                <p className="mb-2"><strong>This will:</strong></p>
                                <ul className="list-disc list-inside space-y-1 text-gray-600">
                                    <li>Restore this staff member's access to the system</li>
                                    <li>Reactivate all their permissions</li>
                                    <li>Allow them to log in and use the system again</li>
                                </ul>
                            </div>
                            
                            <div className="flex justify-end gap-3">
                                <button
                                    onClick={() => { setShowReactivateModal(false); setSelectedUser(null); }}
                                    className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={handleReactivateUser}
                                    className="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors flex items-center gap-2"
                                >
                                    <CheckCircleIcon className="h-4 w-4" />
                                    Yes, Reactivate Staff Member
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Confirmation Modal */}
            {showConfirmModal && selectedUser && confirmAction && (
                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <div className="flex items-center gap-3 mb-4">
                                <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                                    confirmAction === 'approve' ? 'bg-green-100' : 'bg-blue-100'
                                }`}>
                                    {confirmAction === 'approve' ? (
                                        <CheckCircleIcon className="h-5 w-5 text-green-600" />
                                    ) : (
                                        <EnvelopeIcon className="h-5 w-5 text-blue-600" />
                                    )}
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900">
                                        {confirmAction === 'approve' ? 'Approve Staff Member' : 'Resend Invitation'}
                                    </h3>
                                    <p className="text-sm text-gray-500">
                                        {confirmAction === 'approve' 
                                            ? 'This will activate their account' 
                                            : 'This will send a new invitation email'
                                        }
                                    </p>
                                </div>
                            </div>
                            
                            <div className={`border rounded-lg p-4 mb-6 ${
                                confirmAction === 'approve' ? 'bg-green-50 border-green-200' : 'bg-blue-50 border-blue-200'
                            }`}>
                                <div className="flex items-start gap-3">
                                    {confirmAction === 'approve' ? (
                                        <CheckCircleIcon className="h-5 w-5 mt-0.5 text-green-600" />
                                    ) : (
                                        <EnvelopeIcon className="h-5 w-5 mt-0.5 text-blue-600" />
                                    )}
                                    <div>
                                        <p className={`text-sm font-medium mb-2 ${
                                            confirmAction === 'approve' ? 'text-green-800' : 'text-blue-800'
                                        }`}>
                                            {confirmAction === 'approve' 
                                                ? 'Are you sure you want to approve this staff member?'
                                                : 'Are you sure you want to resend the invitation?'
                                            }
                                        </p>
                                        <div className={`bg-white rounded p-3 border ${
                                            confirmAction === 'approve' ? 'border-green-200' : 'border-blue-200'
                                        }`}>
                                            <p className="text-sm font-medium text-gray-900">{selectedUser.name}</p>
                                            <p className="text-xs text-gray-600">{selectedUser.email}</p>
                                            <div className="flex items-center gap-2 mt-1">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRoleColor(selectedUser.role)}`}>
                                                    {selectedUser.role.toUpperCase()}
                                                </span>
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(selectedUser.status)}`}>
                                                    {selectedUser.status.toUpperCase()}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="text-sm text-gray-600 mb-6">
                                <p className="mb-2"><strong>This will:</strong></p>
                                {confirmAction === 'approve' ? (
                                    <ul className="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Activate this staff member's account</li>
                                        <li>Grant them access to the system</li>
                                        <li>Send them a welcome notification</li>
                                    </ul>
                                ) : (
                                    <ul className="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Send a new invitation email to this staff member</li>
                                        <li>Update the invitation timestamp</li>
                                        <li>Allow them to complete their registration</li>
                                    </ul>
                                )}
                            </div>
                            
                            <div className="flex justify-end gap-3">
                                <button
                                    onClick={() => { setShowConfirmModal(false); setConfirmAction(null); setSelectedUser(null); }}
                                    className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={executeConfirmedAction}
                                    className={`px-4 py-2 text-sm font-medium text-white rounded-md transition-colors flex items-center gap-2 ${
                                        confirmAction === 'approve' 
                                            ? 'bg-green-600 hover:bg-green-700' 
                                            : 'bg-blue-600 hover:bg-blue-700'
                                    }`}
                                >
                                    {confirmAction === 'approve' ? (
                                        <>
                                            <CheckCircleIcon className="h-4 w-4" />
                                            Yes, Approve Staff Member
                                        </>
                                    ) : (
                                        <>
                                            <EnvelopeIcon className="h-4 w-4" />
                                            Yes, Resend Invitation
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
} 