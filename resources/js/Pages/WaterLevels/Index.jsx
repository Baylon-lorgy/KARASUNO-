import { Head } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function WaterLevels({ auth, waterLevels: initialWaterLevels }) {
    const [waterLevels, setWaterLevels] = useState(initialWaterLevels);
    const [loading, setLoading] = useState(false);
    const [filter, setFilter] = useState('all');

    useEffect(() => {
        const fetchWaterLevels = async () => {
            setLoading(true);
            try {
                const response = await axios.get('/api/water-levels');
                setWaterLevels(response.data);
            } catch (error) {
                console.error('Error fetching water levels:', error);
            } finally {
                setLoading(false);
            }
        };

        const interval = setInterval(fetchWaterLevels, 5000); // Refresh every 5 seconds
        return () => clearInterval(interval);
    }, []);

    const filteredWaterLevels = filter === 'all'
        ? waterLevels
        : waterLevels.filter(wl => wl.sensor_id === filter);

    const uniqueSensors = [...new Set(waterLevels.map(wl => wl.sensor_id))];

    return (
        <DashboardLayout>
            <Head title="Water Levels" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="flex justify-between items-center mb-6">
                                <h1 className="text-2xl font-semibold">Water Level Readings</h1>
                                <select
                                    value={filter}
                                    onChange={(e) => setFilter(e.target.value)}
                                    className="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="all">All Sensors</option>
                                    {uniqueSensors.map(sensor => (
                                        <option key={sensor} value={sensor}>
                                            Sensor {sensor}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Sensor ID
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Location
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Water Level
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Timestamp
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {filteredWaterLevels.map((reading, index) => (
                                            <tr key={index}>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {reading.sensor_id}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {reading.location}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {reading.level} cm
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                                        reading.level > 80
                                                            ? 'bg-red-100 text-red-800'
                                                            : reading.level > 60
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : 'bg-green-100 text-green-800'
                                                    }`}>
                                                        {reading.level > 80
                                                            ? 'High'
                                                            : reading.level > 60
                                                            ? 'Medium'
                                                            : 'Low'}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {new Date(reading.created_at).toLocaleString()}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
} 