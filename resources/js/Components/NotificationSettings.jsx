import { useState, useEffect } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import { Fragment } from 'react';
import { 
    BellIcon, 
    CogIcon, 
    CheckIcon, 
    XMarkIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    CloudArrowDownIcon,
    CalendarIcon,
    ShieldCheckIcon
} from '@heroicons/react/24/outline';
import axios from 'axios';
import { toast } from 'react-hot-toast';

export default function NotificationSettings({ isOpen, onClose }) {
    const [settings, setSettings] = useState({
        email_notifications: true,
        system_alerts: true,
        watering_events: true,
        sensor_alerts: true,
        schedule_updates: true,
        security_alerts: true
    });
    const [loading, setLoading] = useState(false);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (isOpen) {
            fetchSettings();
        }
    }, [isOpen]);

    const fetchSettings = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/notifications/settings');
            setSettings(response.data);
        } catch (error) {
            console.error('Error fetching notification settings:', error);
            toast.error('Failed to load notification settings');
        } finally {
            setLoading(false);
        }
    };

    const handleSettingChange = (key, value) => {
        setSettings(prev => ({
            ...prev,
            [key]: value
        }));
    };

    const handleSave = async () => {
        setSaving(true);
        try {
            await axios.post('/api/notifications/settings', settings);
            toast.success('Notification settings updated successfully');
            onClose();
        } catch (error) {
            console.error('Error updating notification settings:', error);
            toast.error('Failed to update notification settings');
        } finally {
            setSaving(false);
        }
    };

    const notificationTypes = [
        {
            key: 'email_notifications',
            title: 'Email Notifications',
            description: 'Receive notifications via email',
            icon: BellIcon,
            color: 'text-blue-600',
            bgColor: 'bg-blue-50'
        },
        {
            key: 'system_alerts',
            title: 'System Alerts',
            description: 'Critical system issues and errors',
            icon: ExclamationTriangleIcon,
            color: 'text-red-600',
            bgColor: 'bg-red-50',
            urgency: 'high'
        },
        {
            key: 'watering_events',
            title: 'Watering Events',
            description: 'Start/stop watering notifications',
            icon: CloudArrowDownIcon,
            color: 'text-green-600',
            bgColor: 'bg-green-50',
            urgency: 'low'
        },
        {
            key: 'sensor_alerts',
            title: 'Sensor Alerts',
            description: 'Sensor threshold warnings',
            icon: InformationCircleIcon,
            color: 'text-yellow-600',
            bgColor: 'bg-yellow-50',
            urgency: 'medium'
        },
        {
            key: 'schedule_updates',
            title: 'Schedule Updates',
            description: 'Watering schedule changes',
            icon: CalendarIcon,
            color: 'text-purple-600',
            bgColor: 'bg-purple-50',
            urgency: 'low'
        },
        {
            key: 'security_alerts',
            title: 'Security Alerts',
            description: 'Security and access events',
            icon: ShieldCheckIcon,
            color: 'text-red-600',
            bgColor: 'bg-red-50',
            urgency: 'critical'
        }
    ];

    const getUrgencyBadge = (urgency) => {
        const urgencyConfig = {
            critical: { text: 'CRITICAL', color: 'bg-red-100 text-red-800' },
            high: { text: 'HIGH', color: 'bg-orange-100 text-orange-800' },
            medium: { text: 'MEDIUM', color: 'bg-yellow-100 text-yellow-800' },
            low: { text: 'LOW', color: 'bg-green-100 text-green-800' }
        };

        const config = urgencyConfig[urgency] || urgencyConfig.low;
        return (
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${config.color}`}>
                {config.text}
            </span>
        );
    };

    return (
        <Transition appear show={isOpen} as={Fragment}>
            <Dialog as="div" className="relative z-50" onClose={onClose}>
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <div className="fixed inset-0 bg-black/30 backdrop-blur-sm" />
                </Transition.Child>

                <div className="fixed inset-0 overflow-y-auto">
                    <div className="flex min-h-full items-center justify-center p-4">
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0 scale-95"
                            enterTo="opacity-100 scale-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100 scale-100"
                            leaveTo="opacity-0 scale-95"
                        >
                            <Dialog.Panel className="w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all">
                                <div className="bg-gradient-to-r from-[#09203f] to-[#537895] px-6 py-4">
                                    <div className="flex items-center justify-between">
                                        <Dialog.Title className="text-lg font-semibold text-white flex items-center gap-2">
                                            <CogIcon className="h-5 w-5" />
                                            Notification Settings
                                        </Dialog.Title>
                                        <button
                                            onClick={onClose}
                                            className="text-white hover:text-gray-200 transition-colors"
                                        >
                                            <XMarkIcon className="h-6 w-6" />
                                        </button>
                                    </div>
                                </div>

                                <div className="px-6 py-4">
                                    {loading ? (
                                        <div className="flex items-center justify-center py-8">
                                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                            <span className="ml-3 text-blue-600 font-medium">Loading settings...</span>
                                        </div>
                                    ) : (
                                        <div className="space-y-4">
                                            <p className="text-sm text-gray-600 mb-4">
                                                Configure which notifications you want to receive via email. 
                                                Critical and high-priority notifications will always be sent.
                                            </p>

                                            {notificationTypes.map((type) => (
                                                <div key={type.key} className="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                                    <div className="flex items-center gap-3">
                                                        <div className={`p-2 rounded-lg ${type.bgColor}`}>
                                                            <type.icon className={`h-5 w-5 ${type.color}`} />
                                                        </div>
                                                        <div>
                                                            <div className="flex items-center gap-2">
                                                                <h3 className="text-sm font-semibold text-gray-900">
                                                                    {type.title}
                                                                </h3>
                                                                {type.urgency && getUrgencyBadge(type.urgency)}
                                                            </div>
                                                            <p className="text-xs text-gray-500 mt-1">
                                                                {type.description}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <label className="relative inline-flex items-center cursor-pointer">
                                                        <input
                                                            type="checkbox"
                                                            checked={settings[type.key]}
                                                            onChange={(e) => handleSettingChange(type.key, e.target.checked)}
                                                            className="sr-only peer"
                                                        />
                                                        <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                    </label>
                                                </div>
                                            ))}

                                            <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                                <div className="flex items-start gap-3">
                                                    <InformationCircleIcon className="h-5 w-5 text-blue-600 mt-0.5" />
                                                    <div>
                                                        <h4 className="text-sm font-semibold text-blue-900 mb-1">
                                                            Email Notification Priority
                                                        </h4>
                                                        <p className="text-xs text-blue-700">
                                                            <strong>Critical & High:</strong> Always sent immediately<br/>
                                                            <strong>Medium:</strong> Sent within 15 minutes<br/>
                                                            <strong>Low:</strong> Sent within 1 hour or batched
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>

                                <div className="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                                    <button
                                        onClick={onClose}
                                        className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        onClick={handleSave}
                                        disabled={saving}
                                        className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    >
                                        {saving ? (
                                            <>
                                                <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                                Saving...
                                            </>
                                        ) : (
                                            <>
                                                <CheckIcon className="h-4 w-4" />
                                                Save Settings
                                            </>
                                        )}
                                    </button>
                                </div>
                            </Dialog.Panel>
                        </Transition.Child>
                    </div>
                </div>
            </Dialog>
        </Transition>
    );
} 