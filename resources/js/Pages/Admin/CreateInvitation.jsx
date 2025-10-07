import { useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head, useForm } from '@inertiajs/react';
import { 
    UserPlusIcon,
    EnvelopeIcon,
    ShieldCheckIcon,
    InformationCircleIcon,
    CheckIcon
} from '@heroicons/react/24/outline';

export default function CreateInvitation() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        role: 'staff',
        message: ''
    });

    const rolePermissions = {
        staff: [
            'View Dashboard',
            'Watering Control', 
            'Sensor History'
        ],
        manager: [
            'View Dashboard',
            'Watering Control',
            'Sensor History',
            'User Management',
            'System Settings'
        ],
        admin: [
            'View Dashboard',
            'Watering Control',
            'Sensor History',
            'User Management',
            'Audit Logs',
            'System Settings'
        ]
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/invitations');
    };

    const handleRoleChange = (role) => {
        setData('role', role);
    };

    return (
        <DashboardLayout>
            <Head title="Send Staff Invitation" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">Send Staff Invitation</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Send email invitations to new staff members
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Invite new staff members to join your RainBasin Pro system</p>
                </div>

                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-6 border border-gray-200 max-w-2xl mx-auto">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Recipient Information */}
                        <div className="space-y-4">
                            <h3 className="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <UserPlusIcon className="h-4 w-4" />
                                Recipient Information
                            </h3>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter full name"
                                    required
                                />
                                {errors.name && (
                                    <p className="text-red-600 text-xs mt-1">{errors.name}</p>
                                )}
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter email address"
                                    required
                                />
                                {errors.email && (
                                    <p className="text-red-600 text-xs mt-1">{errors.email}</p>
                                )}
                            </div>
                        </div>

                        {/* Role Selection */}
                        <div className="space-y-4">
                            <h3 className="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <ShieldCheckIcon className="h-4 w-4" />
                                Role & Permissions
                            </h3>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Select Role</label>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    {Object.entries({
                                        staff: 'Staff Member',
                                        manager: 'Manager',
                                        admin: 'Administrator'
                                    }).map(([role, label]) => (
                                        <label
                                            key={role}
                                            className={`relative cursor-pointer rounded-lg border-2 p-4 transition-all ${
                                                data.role === role
                                                    ? 'border-blue-500 bg-blue-50'
                                                    : 'border-gray-200 hover:border-gray-300'
                                            }`}
                                        >
                                            <input
                                                type="radio"
                                                name="role"
                                                value={role}
                                                checked={data.role === role}
                                                onChange={() => handleRoleChange(role)}
                                                className="sr-only"
                                            />
                                            <div className="flex items-center gap-3">
                                                <div className={`w-4 h-4 rounded-full border-2 flex items-center justify-center ${
                                                    data.role === role
                                                        ? 'border-blue-500 bg-blue-500'
                                                        : 'border-gray-300'
                                                }`}>
                                                    {data.role === role && (
                                                        <div className="w-2 h-2 bg-white rounded-full"></div>
                                                    )}
                                                </div>
                                                <div>
                                                    <div className="font-medium text-gray-900">{label}</div>
                                                    <div className="text-xs text-gray-500 capitalize">{role}</div>
                                                </div>
                                            </div>
                                        </label>
                                    ))}
                                </div>
                            </div>

                            {/* Permissions Preview */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Permissions Included</label>
                                <div className="bg-gray-50 rounded-lg p-4">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        {rolePermissions[data.role]?.map((permission, index) => (
                                            <div key={index} className="flex items-center gap-2 text-sm">
                                                <CheckIcon className="h-4 w-4 text-green-600" />
                                                <span className="text-gray-700">{permission}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Optional Message */}
                        <div className="space-y-4">
                            <h3 className="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <EnvelopeIcon className="h-4 w-4" />
                                Optional Message
                            </h3>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Personal Message (Optional)</label>
                                <textarea
                                    value={data.message}
                                    onChange={e => setData('message', e.target.value)}
                                    className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Add a personal message to the invitation email..."
                                    rows={3}
                                    maxLength={500}
                                />
                                <div className="text-xs text-gray-500 mt-1">
                                    {data.message.length}/500 characters
                                </div>
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div className="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button
                                type="button"
                                onClick={() => window.history.back()}
                                className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 flex items-center gap-2"
                            >
                                {processing ? (
                                    <>
                                        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                        Sending...
                                    </>
                                ) : (
                                    <>
                                        <EnvelopeIcon className="h-4 w-4" />
                                        Send Invitation
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
} 