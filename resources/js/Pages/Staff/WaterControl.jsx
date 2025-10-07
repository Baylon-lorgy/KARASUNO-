import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import StaffLayout from '@/Layouts/StaffLayout';
import { 
    PlayIcon,
    StopIcon,
    CogIcon,
    ClockIcon,
    CheckCircleIcon,
    CloudArrowDownIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    CalendarIcon,
    UserIcon
} from '@heroicons/react/24/outline';
import { toast } from 'react-hot-toast';

export default function StaffWaterControl({ auth }) {
    const [wateringStatus, setWateringStatus] = useState('idle');
    const [schedules, setSchedules] = useState([]);
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState('');
    const [recentActivity, setRecentActivity] = useState([]);

    const staffUser = auth.staffUser;

    // Helper function to check permissions
    const hasPermission = (permission) => {
        return staffUser.permissions.includes(permission);
    };

    useEffect(() => {
        fetchWateringData();
    }, []);

    const fetchWateringData = async () => {
        try {
            const response = await fetch('/staff/api/watering-data');
            if (response.ok) {
                const data = await response.json();
                setWateringStatus(data.status);
                setSchedules(data.schedules || []);
                setRecentActivity(data.recentActivity || []);
            }
        } catch (error) {
            console.error('Error fetching watering data:', error);
            toast.error('Failed to load watering data');
        }
    };

    const handleWateringControl = async (action) => {
        setLoading(true);
        setMessage('');

        try {
            const response = await fetch('/staff/api/watering-control', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ action })
            });

            if (response.ok) {
                const data = await response.json();
                setWateringStatus(data.status);
                setMessage(data.message);
                toast.success(data.message);
                
                // Refresh data
                fetchWateringData();
            } else {
                setMessage('Failed to control watering system');
                toast.error('Failed to control watering system');
            }
        } catch (error) {
            console.error('Error controlling watering:', error);
            setMessage('Error controlling watering system');
            toast.error('Error controlling watering system');
        } finally {
            setLoading(false);
        }
    };

    return (
        <StaffLayout title="Water Control">
            <Head title="Water Control" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex items-center justify-between">
                        <div>
                            <h2 className="text-xl font-semibold text-gray-900">Watering System Control</h2>
                            <p className="text-gray-600 mt-1">Manage and monitor watering operations</p>
                        </div>
                        <CloudArrowDownIcon className="h-8 w-8 text-green-600" />
                    </div>
                </div>

                {/* Status Card */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-lg font-semibold text-gray-900">System Status</h3>
                        <CogIcon className="h-6 w-6 text-blue-600" />
                    </div>
                    <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-3">
                            <span className={`px-3 py-1 rounded-full text-sm font-medium ${
                                wateringStatus === 'active' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-gray-100 text-gray-800'
                            }`}>
                                {wateringStatus.toUpperCase()}
                            </span>
                            {wateringStatus === 'active' && (
                                <CheckCircleIcon className="h-5 w-5 text-green-600 animate-pulse" />
                            )}
                        </div>
                        <div className="text-sm text-gray-500">
                            Last updated: {new Date().toLocaleTimeString()}
                        </div>
                    </div>
                </div>

                {/* Control Panel */}
                {hasPermission('watering_control') && (
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold text-gray-900">Manual Control</h3>
                            <CogIcon className="h-6 w-6 text-green-600" />
                        </div>
                        <div className="flex gap-4">
                            <button
                                onClick={() => handleWateringControl('start')}
                                disabled={wateringStatus === 'active' || loading}
                                className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <PlayIcon className="h-5 w-5" />
                                Start Watering
                            </button>
                            <button
                                onClick={() => handleWateringControl('stop')}
                                disabled={wateringStatus === 'idle' || loading}
                                className="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <StopIcon className="h-5 w-5" />
                                Stop Watering
                            </button>
                        </div>
                        {message && (
                            <div className="mt-4 p-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-200">
                                {message}
                            </div>
                        )}
                    </div>
                )}

                {/* Schedules */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-lg font-semibold text-gray-900">Watering Schedules</h3>
                        <ClockIcon className="h-6 w-6 text-gray-600" />
                    </div>
                    {schedules.length > 0 ? (
                        <div className="space-y-3">
                            {schedules.map((schedule, index) => (
                                <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div className="flex items-center space-x-3">
                                        <CalendarIcon className="h-5 w-5 text-blue-500" />
                                        <div>
                                            <p className="font-medium text-gray-900">{schedule.name}</p>
                                            <p className="text-sm text-gray-600">{schedule.time}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                            schedule.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                        }`}>
                                            {schedule.active ? 'Active' : 'Inactive'}
                                        </span>
                                        {schedule.active && (
                                            <CheckCircleIcon className="h-4 w-4 text-green-600" />
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-8">
                            <ClockIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <p className="text-gray-500">No watering schedules configured</p>
                        </div>
                    )}
                </div>

                {/* Recent Activity */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        <InformationCircleIcon className="h-6 w-6 text-blue-600" />
                    </div>
                    {recentActivity.length > 0 ? (
                        <div className="space-y-3">
                            {recentActivity.map((activity, index) => (
                                <div key={index} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div className="flex-shrink-0">
                                        {activity.type === 'start' ? (
                                            <PlayIcon className="h-5 w-5 text-green-600" />
                                        ) : activity.type === 'stop' ? (
                                            <StopIcon className="h-5 w-5 text-red-600" />
                                        ) : (
                                            <InformationCircleIcon className="h-5 w-5 text-blue-600" />
                                        )}
                                    </div>
                                    <div className="flex-1">
                                        <p className="text-sm font-medium text-gray-900">{activity.message}</p>
                                        <p className="text-xs text-gray-500">{activity.timestamp}</p>
                                    </div>
                                    {activity.user && (
                                        <div className="flex items-center text-xs text-gray-500">
                                            <UserIcon className="h-3 w-3 mr-1" />
                                            {activity.user}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-4">
                            <p className="text-gray-500">No recent activity</p>
                        </div>
                    )}
                </div>

                {/* Information */}
                <div className="bg-green-50 rounded-lg p-3 border border-green-200">
                    <h4 className="font-medium text-green-900 mb-2">Water Control Information</h4>
                    <ul className="text-sm text-green-800 space-y-1">
                        <li>• Manual control allows immediate start/stop of the watering system</li>
                        <li>• Schedules provide automated watering at set times</li>
                        <li>• System status shows current watering activity</li>
                        <li>• Contact administrator for schedule modifications</li>
                        <li>• All activities are logged for audit purposes</li>
                    </ul>
                </div>
            </div>
        </StaffLayout>
    );
} 