import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-hot-toast';
import {
    ExclamationTriangleIcon,
    InformationCircleIcon,
    BugAntIcon,
    ClockIcon,
    FunnelIcon
} from '@heroicons/react/24/outline';

export default function SystemLogs() {
    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');

    useEffect(() => {
        fetchLogs();
        const interval = setInterval(fetchLogs, 30000); // Refresh every 30 seconds
        return () => clearInterval(interval);
    }, [filter]);

    const fetchLogs = async () => {
        try {
            const response = await axios.get(`/api/system/logs?filter=${filter}`);
            setLogs(response.data.logs);
        } catch (error) {
            console.error('Failed to fetch logs:', error);
            toast.error('Failed to load system logs');
        } finally {
            setLoading(false);
        }
    };

    const getLogLevelColor = (level) => {
        switch (level.toLowerCase()) {
            case 'error':
                return 'bg-red-100 text-red-800';
            case 'warning':
                return 'bg-yellow-100 text-yellow-800';
            case 'info':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getLogLevelIcon = (level) => {
        switch (level.toLowerCase()) {
            case 'error':
                return <BugAntIcon className="h-5 w-5 text-red-500" />;
            case 'warning':
                return <ExclamationTriangleIcon className="h-5 w-5 text-yellow-500" />;
            case 'info':
                return <InformationCircleIcon className="h-5 w-5 text-blue-500" />;
            default:
                return <InformationCircleIcon className="h-5 w-5 text-gray-500" />;
        }
    };

    const filteredLogs = logs.filter(log => {
        const matchesSearch = searchQuery === '' || 
            log.message.toLowerCase().includes(searchQuery.toLowerCase()) ||
            log.context.toLowerCase().includes(searchQuery.toLowerCase());
        return matchesSearch;
    });

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
                <h3 className="text-lg font-semibold">System Logs</h3>
                <div className="flex items-center space-x-4">
                    <div className="relative">
                        <input
                            type="text"
                            placeholder="Search logs..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        />
                        <FunnelIcon className="h-5 w-5 text-gray-400 absolute left-3 top-2.5" />
                    </div>
                    <select
                        value={filter}
                        onChange={(e) => setFilter(e.target.value)}
                        className="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="all">All Logs</option>
                        <option value="error">Errors</option>
                        <option value="warning">Warnings</option>
                        <option value="info">Info</option>
                    </select>
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Level
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Message
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Context
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {filteredLogs.map((log) => (
                            <tr key={log.id} className="hover:bg-gray-50">
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div className="flex items-center">
                                        <ClockIcon className="h-4 w-4 mr-2 text-gray-400" />
                                        {new Date(log.timestamp).toLocaleString()}
                                    </div>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <div className="flex items-center">
                                        {getLogLevelIcon(log.level)}
                                        <span className={`ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getLogLevelColor(log.level)}`}>
                                            {log.level}
                                        </span>
                                    </div>
                                </td>
                                <td className="px-6 py-4 text-sm text-gray-500">
                                    {log.message}
                                </td>
                                <td className="px-6 py-4 text-sm text-gray-500">
                                    {log.context}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {filteredLogs.length === 0 && (
                <div className="text-center py-8 text-gray-500">
                    No logs found matching your criteria.
                </div>
            )}
        </div>
    );
} 