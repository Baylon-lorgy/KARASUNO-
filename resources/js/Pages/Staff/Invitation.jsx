import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { 
    UserIcon, 
    ShieldCheckIcon, 
    EyeIcon,
    EyeSlashIcon,
    CheckIcon,
    InformationCircleIcon
} from '@heroicons/react/24/outline';

export default function StaffInvitation({ staffUser, token }) {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    
    const { data, setData, post, processing, errors } = useForm({
        password: '',
        password_confirmation: ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/staff/invitation/${token}/accept`);
    };

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

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
            <Head title="Staff Invitation - RainBasin Pro" />
            
            <div className="max-w-md w-full">
                <div className="bg-white rounded-2xl shadow-xl p-8">
                    {/* Header */}
                    <div className="text-center mb-8">
                        <div className="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <UserIcon className="h-8 w-8 text-white" />
                        </div>
                        <h1 className="text-2xl font-bold text-gray-900 mb-2">Welcome to RainBasin Pro</h1>
                        <p className="text-gray-600">Complete your account setup</p>
                    </div>

                    {/* User Info */}
                    <div className="bg-blue-50 rounded-lg p-4 mb-6">
                        <div className="flex items-center gap-3 mb-3">
                            <ShieldCheckIcon className="h-5 w-5 text-blue-600" />
                            <span className="font-semibold text-gray-900">{staffUser.name}</span>
                        </div>
                        <p className="text-sm text-gray-600 mb-2">{staffUser.email}</p>
                        <div className="flex items-center gap-2">
                            <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                {staffUser.role.toUpperCase()}
                            </span>
                        </div>
                    </div>

                    {/* Permissions */}
                    <div className="mb-6">
                        <h3 className="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <InformationCircleIcon className="h-4 w-4" />
                            Your Permissions
                        </h3>
                        <div className="space-y-2">
                            {rolePermissions[staffUser.role]?.map((permission, index) => (
                                <div key={index} className="flex items-center gap-2 text-sm">
                                    <CheckIcon className="h-4 w-4 text-green-600" />
                                    <span className="text-gray-700">{permission}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Setup Form */}
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Set Your Password
                            </label>
                            <div className="relative">
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter your password"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    {showPassword ? (
                                        <EyeSlashIcon className="h-5 w-5" />
                                    ) : (
                                        <EyeIcon className="h-5 w-5" />
                                    )}
                                </button>
                            </div>
                            {errors.password && (
                                <p className="text-red-600 text-sm mt-1">{errors.password}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <div className="relative">
                                <input
                                    type={showConfirmPassword ? 'text' : 'password'}
                                    value={data.password_confirmation}
                                    onChange={e => setData('password_confirmation', e.target.value)}
                                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Confirm your password"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    {showConfirmPassword ? (
                                        <EyeSlashIcon className="h-5 w-5" />
                                    ) : (
                                        <EyeIcon className="h-5 w-5" />
                                    )}
                                </button>
                            </div>
                            {errors.password_confirmation && (
                                <p className="text-red-600 text-sm mt-1">{errors.password_confirmation}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            {processing ? 'Setting up account...' : 'Complete Setup'}
                        </button>
                    </form>

                    {/* Info */}
                    <div className="mt-6 text-center">
                        <p className="text-xs text-gray-500">
                            After setup, your account will be pending manager approval before you can access the system.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
} 