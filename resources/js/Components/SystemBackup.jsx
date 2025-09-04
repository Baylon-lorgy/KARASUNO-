import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-hot-toast';
import {
    CloudArrowUpIcon,
    CloudArrowDownIcon,
    Cog6ToothIcon,
    TrashIcon,
    ArrowPathIcon
} from '@heroicons/react/24/outline';

export default function SystemBackup() {
    const [backups, setBackups] = useState([]);
    const [loading, setLoading] = useState(true);
    const [creatingBackup, setCreatingBackup] = useState(false);
    const [restoringBackup, setRestoringBackup] = useState(false);
    const [showSettings, setShowSettings] = useState(false);
    const [settings, setSettings] = useState({
        autoBackup: false,
        backupInterval: 24,
        keepBackups: 7,
        backupLocation: 'local'
    });

    useEffect(() => {
        fetchBackups();
        fetchSettings();
    }, []);

    const fetchBackups = async () => {
        try {
            const response = await axios.get('/api/system/backups');
            setBackups(response.data.backups);
        } catch (error) {
            console.error('Failed to fetch backups:', error);
            toast.error('Failed to load backups');
        } finally {
            setLoading(false);
        }
    };

    const fetchSettings = async () => {
        try {
            const response = await axios.get('/api/system/backup-settings');
            setSettings(response.data.settings);
        } catch (error) {
            console.error('Failed to fetch backup settings:', error);
            toast.error('Failed to load backup settings');
        }
    };

    const createBackup = async () => {
        setCreatingBackup(true);
        try {
            const response = await axios.post('/api/system/backups/create');
            if (response.data.success) {
                toast.success('Backup created successfully');
                fetchBackups();
            }
        } catch (error) {
            console.error('Failed to create backup:', error);
            toast.error('Failed to create backup');
        } finally {
            setCreatingBackup(false);
        }
    };

    const restoreBackup = async (backupId) => {
        if (!window.confirm('Are you sure you want to restore this backup? This will overwrite current data.')) {
            return;
        }

        setRestoringBackup(true);
        try {
            const response = await axios.post(`/api/system/backups/${backupId}/restore`);
            if (response.data.success) {
                toast.success('Backup restored successfully');
                fetchBackups();
            }
        } catch (error) {
            console.error('Failed to restore backup:', error);
            toast.error('Failed to restore backup');
        } finally {
            setRestoringBackup(false);
        }
    };

    const deleteBackup = async (backupId) => {
        if (!window.confirm('Are you sure you want to delete this backup?')) {
            return;
        }

        try {
            const response = await axios.delete(`/api/system/backups/${backupId}`);
            if (response.data.success) {
                toast.success('Backup deleted successfully');
                fetchBackups();
            }
        } catch (error) {
            console.error('Failed to delete backup:', error);
            toast.error('Failed to delete backup');
        }
    };

    const saveSettings = async () => {
        try {
            const response = await axios.post('/api/system/backup-settings', settings);
            if (response.data.success) {
                toast.success('Backup settings saved successfully');
                setShowSettings(false);
            }
        } catch (error) {
            console.error('Failed to save backup settings:', error);
            toast.error('Failed to save backup settings');
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
            </div>
        );
    }

    return (
        <div className="bg-white rounded-lg shadow-sm p-6">
            <div className="flex items-center justify-between mb-6">
                <h3 className="text-lg font-semibold">System Backup</h3>
                <div className="flex space-x-3">
                    <button
                        onClick={() => setShowSettings(true)}
                        className="flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200"
                    >
                        <Cog6ToothIcon className="h-5 w-5 mr-2" />
                        Settings
                    </button>
                    <button
                        onClick={createBackup}
                        disabled={creatingBackup}
                        className={`flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md ${
                            creatingBackup ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'
                        }`}
                    >
                        <CloudArrowUpIcon className="h-5 w-5 mr-2" />
                        {creatingBackup ? 'Creating...' : 'Create Backup'}
                    </button>
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Size
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {backups.map((backup) => (
                            <tr key={backup.id}>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {new Date(backup.created_at).toLocaleString()}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {backup.size}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {backup.type}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                        backup.status === 'completed' ? 'bg-green-100 text-green-800' :
                                        backup.status === 'failed' ? 'bg-red-100 text-red-800' :
                                        'bg-yellow-100 text-yellow-800'
                                    }`}>
                                        {backup.status}
                                    </span>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div className="flex justify-end space-x-3">
                                        <button
                                            onClick={() => restoreBackup(backup.id)}
                                            disabled={restoringBackup}
                                            className={`flex items-center text-indigo-600 hover:text-indigo-900 ${
                                                restoringBackup ? 'opacity-50 cursor-not-allowed' : ''
                                            }`}
                                        >
                                            <CloudArrowDownIcon className="h-5 w-5 mr-1" />
                                            Restore
                                        </button>
                                        <button
                                            onClick={() => deleteBackup(backup.id)}
                                            className="flex items-center text-red-600 hover:text-red-900"
                                        >
                                            <TrashIcon className="h-5 w-5 mr-1" />
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Settings Modal */}
            {showSettings && (
                <div className="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
                    <div className="bg-white rounded-lg p-6 max-w-md w-full">
                        <h3 className="text-lg font-medium mb-4">Backup Settings</h3>
                        <div className="space-y-4">
                            <div>
                                <label className="flex items-center">
                                    <input
                                        type="checkbox"
                                        checked={settings.autoBackup}
                                        onChange={(e) => setSettings({ ...settings, autoBackup: e.target.checked })}
                                        className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span className="ml-2 text-sm text-gray-700">Enable Automatic Backups</span>
                                </label>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Backup Interval (hours)
                                </label>
                                <input
                                    type="number"
                                    value={settings.backupInterval}
                                    onChange={(e) => setSettings({ ...settings, backupInterval: parseInt(e.target.value) })}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    min="1"
                                    max="168"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Keep Backups (days)
                                </label>
                                <input
                                    type="number"
                                    value={settings.keepBackups}
                                    onChange={(e) => setSettings({ ...settings, keepBackups: parseInt(e.target.value) })}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    min="1"
                                    max="365"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Backup Location
                                </label>
                                <select
                                    value={settings.backupLocation}
                                    onChange={(e) => setSettings({ ...settings, backupLocation: e.target.value })}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                    <option value="local">Local Storage</option>
                                    <option value="cloud">Cloud Storage</option>
                                </select>
                            </div>
                            <div className="flex justify-end space-x-3">
                                <button
                                    onClick={() => setShowSettings(false)}
                                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={saveSettings}
                                    className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                >
                                    Save Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
} 