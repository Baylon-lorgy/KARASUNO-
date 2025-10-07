import { useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head, Link } from '@inertiajs/react';
import { 
    EnvelopeIcon, 
    UserPlusIcon,
    ClockIcon,
    CheckCircleIcon,
    XMarkIcon,
    EyeIcon,
    ArrowPathIcon,
    TrashIcon,
    InformationCircleIcon
} from '@heroicons/react/24/outline';
import { toast } from 'react-hot-toast';
import axios from 'axios';

export default function Invitations({ pendingInvitations, recentInvitations }) {
    const [loading, setLoading] = useState({});

    const handleResend = async (userId) => {
        setLoading(prev => ({ ...prev, [userId]: true }));
        try {
            await axios.post(`/admin/invitations/${userId}/resend`);
            toast.success('Invitation resent successfully');
            window.location.reload();
        } catch (error) {
            console.error('Error resending invitation:', error);
            toast.error('Failed to resend invitation');
        } finally {
            setLoading(prev => ({ ...prev, [userId]: false }));
        }
    };

    const handleCancel = async (userId) => {
        if (!confirm('Are you sure you want to cancel this invitation?')) return;
        
        setLoading(prev => ({ ...prev, [userId]: true }));
        try {
            await axios.delete(`/admin/invitations/${userId}/cancel`);
            toast.success('Invitation cancelled successfully');
            window.location.reload();
        } catch (error) {
            console.error('Error cancelling invitation:', error);
            toast.error('Failed to cancel invitation');
        } finally {
            setLoading(prev => ({ ...prev, [userId]: false }));
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

    const getStatusIcon = (status, accepted) => {
        if (accepted) return <CheckCircleIcon className="h-4 w-4 text-green-600" />;
        if (status === 'pending') return <ClockIcon className="h-4 w-4 text-yellow-600" />;
        return <XMarkIcon className="h-4 w-4 text-red-600" />;
    };

    return (
        <DashboardLayout>
            <Head title="Staff Invitations" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">Staff Invitations</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Manage staff invitations and track their status
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Track and manage staff invitations sent via email</p>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <EnvelopeIcon className="h-5 w-5 text-blue-600" />
                            <div>
                                <p className="text-xs text-gray-600">Pending</p>
                                <p className="text-lg font-bold text-gray-900">{pendingInvitations.length}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <CheckCircleIcon className="h-5 w-5 text-green-600" />
                            <div>
                                <p className="text-xs text-gray-600">Accepted</p>
                                <p className="text-lg font-bold text-gray-900">
                                    {recentInvitations.filter(u => u.invitation_accepted_at).length}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <ClockIcon className="h-5 w-5 text-yellow-600" />
                            <div>
                                <p className="text-xs text-gray-600">Sent Today</p>
                                <p className="text-lg font-bold text-gray-900">
                                    {recentInvitations.filter(u => 
                                        new Date(u.invitation_sent_at).toDateString() === new Date().toDateString()
                                    ).length}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center gap-2">
                            <UserPlusIcon className="h-5 w-5 text-purple-600" />
                            <div>
                                <p className="text-xs text-gray-600">Total Sent</p>
                                <p className="text-lg font-bold text-gray-900">{recentInvitations.length}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Actions */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex justify-between items-center">
                        <h3 className="text-sm font-semibold text-gray-900">Invitation Management</h3>
                        <Link
                            href="/admin/invitations/create"
                            className="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2"
                        >
                            <UserPlusIcon className="h-4 w-4" />
                            Send New Invitation
                        </Link>
                    </div>
                </div>

                {/* Pending Invitations */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <h3 className="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <ClockIcon className="h-4 w-4" />
                        Pending Invitations
                    </h3>
                    
                    {pendingInvitations.length === 0 ? (
                        <div className="text-center py-8">
                            <EnvelopeIcon className="mx-auto h-12 w-12 text-gray-300 mb-4" />
                            <p className="text-lg font-medium text-gray-500 mb-2">No pending invitations</p>
                            <p className="text-sm text-gray-400">All invitations have been accepted or expired</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-gray-200">
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Recipient</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Role</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Sent</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {pendingInvitations.map((invitation) => (
                                        <tr key={invitation.id} className="border-b border-gray-100 hover:bg-gray-50">
                                            <td className="py-3 px-4">
                                                <div>
                                                    <p className="font-medium text-gray-900">{invitation.name}</p>
                                                    <p className="text-xs text-gray-500">{invitation.email}</p>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRoleColor(invitation.role)}`}>
                                                    {invitation.role.toUpperCase()}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="text-xs text-gray-500">
                                                    {new Date(invitation.invitation_sent_at).toLocaleDateString()}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-2">
                                                    {getStatusIcon(invitation.status, invitation.invitation_accepted_at)}
                                                    <span className="text-xs text-gray-600">
                                                        {invitation.invitation_accepted_at ? 'Accepted' : 'Pending'}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex gap-2">
                                                    <Link
                                                        href={`/admin/invitations/${invitation.id}/preview`}
                                                        className="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors"
                                                        title="Preview invitation"
                                                    >
                                                        <EyeIcon className="h-4 w-4" />
                                                    </Link>
                                                    {!invitation.invitation_accepted_at && (
                                                        <button
                                                            onClick={() => handleResend(invitation.id)}
                                                            disabled={loading[invitation.id]}
                                                            className="p-1 text-green-600 hover:bg-green-100 rounded transition-colors disabled:opacity-50"
                                                            title="Resend invitation"
                                                        >
                                                            <ArrowPathIcon className={`h-4 w-4 ${loading[invitation.id] ? 'animate-spin' : ''}`} />
                                                        </button>
                                                    )}
                                                    <button
                                                        onClick={() => handleCancel(invitation.id)}
                                                        disabled={loading[invitation.id]}
                                                        className="p-1 text-red-600 hover:bg-red-100 rounded transition-colors disabled:opacity-50"
                                                        title="Cancel invitation"
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

                {/* Recent Invitations */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <h3 className="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <EnvelopeIcon className="h-4 w-4" />
                        Recent Invitations
                    </h3>
                    
                    {recentInvitations.length === 0 ? (
                        <div className="text-center py-8">
                            <EnvelopeIcon className="mx-auto h-12 w-12 text-gray-300 mb-4" />
                            <p className="text-lg font-medium text-gray-500 mb-2">No recent invitations</p>
                            <p className="text-sm text-gray-400">Send your first invitation to get started</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-gray-200">
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Recipient</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Role</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Sent</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Accepted</th>
                                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {recentInvitations.map((invitation) => (
                                        <tr key={invitation.id} className="border-b border-gray-100 hover:bg-gray-50">
                                            <td className="py-3 px-4">
                                                <div>
                                                    <p className="font-medium text-gray-900">{invitation.name}</p>
                                                    <p className="text-xs text-gray-500">{invitation.email}</p>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRoleColor(invitation.role)}`}>
                                                    {invitation.role.toUpperCase()}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="text-xs text-gray-500">
                                                    {new Date(invitation.invitation_sent_at).toLocaleDateString()}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <span className="text-xs text-gray-500">
                                                    {invitation.invitation_accepted_at 
                                                        ? new Date(invitation.invitation_accepted_at).toLocaleDateString()
                                                        : 'Not accepted'
                                                    }
                                                </span>
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-2">
                                                    {getStatusIcon(invitation.status, invitation.invitation_accepted_at)}
                                                    <span className="text-xs text-gray-600">
                                                        {invitation.status.toUpperCase()}
                                                    </span>
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
        </DashboardLayout>
    );
} 