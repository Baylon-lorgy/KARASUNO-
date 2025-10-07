import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import StaffLayout from '@/Layouts/StaffLayout';
import { Line, Bar } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler,
    BarElement
} from 'chart.js';
import { 
    ChartBarIcon,
    CalendarIcon,
    InformationCircleIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    ClockIcon,
    SunIcon,
    BeakerIcon
} from '@heroicons/react/24/outline';
import { toast } from 'react-hot-toast';

// Alias for non-existent icons
const DropletIcon = BeakerIcon;
const ThermometerIcon = BeakerIcon;

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

export default function StaffSensorHistory({ auth }) {
    const [sensorData, setSensorData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [dateRange, setDateRange] = useState('7d');
    const [chartType, setChartType] = useState('line');
    const [selectedSensor, setSelectedSensor] = useState('water_level');
    const [chartData, setChartData] = useState({
        labels: [],
        datasets: []
    });

    const staffUser = auth.staffUser;

    // Helper function to check permissions
    const hasPermission = (permission) => {
        return staffUser.permissions.includes(permission);
    };

    useEffect(() => {
        fetchSensorHistory();
    }, [dateRange]);

    const fetchSensorHistory = async () => {
        setLoading(true);
        try {
            const response = await fetch(`/staff/api/sensor-history?range=${dateRange}`);
            if (response.ok) {
                const data = await response.json();
                setSensorData(data.sensorData || []);
                
                // Generate chart data
                if (data.sensorData && data.sensorData.length > 0) {
                    const labels = data.sensorData.map(item => 
                        new Date(item.timestamp).toLocaleString()
                    );
                    const waterLevelData = data.sensorData.map(item => item.waterLevel || 0);
                    const temperatureData = data.sensorData.map(item => item.temperature || 0);
                    const humidityData = data.sensorData.map(item => item.humidity || 0);
                    
                    setChartData({
                        labels,
                        datasets: [
                            {
                                label: 'Water Level (%)',
                                data: waterLevelData,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            },
                            {
                                label: 'Temperature (°C)',
                                data: temperatureData,
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4
                            },
                            {
                                label: 'Humidity (%)',
                                data: humidityData,
                                borderColor: 'rgb(245, 158, 11)',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.4
                            }
                        ]
                    });
                }
            }
        } catch (error) {
            console.error('Error fetching sensor history:', error);
            toast.error('Failed to load sensor history');
        } finally {
            setLoading(false);
        }
    };

    const getSensorIcon = (type) => {
        switch (type) {
            case 'water_level':
                return <DropletIcon className="h-5 w-5 text-blue-500" />;
            case 'temperature':
                return <ThermometerIcon className="h-5 w-5 text-red-500" />;
            case 'humidity':
                return <SunIcon className="h-5 w-5 text-yellow-500" />;
            default:
                return <BeakerIcon className="h-5 w-5 text-gray-500" />;
        }
    };

    const getSensorColor = (type) => {
        switch (type) {
            case 'water_level':
                return 'text-blue-600';
            case 'temperature':
                return 'text-red-600';
            case 'humidity':
                return 'text-yellow-600';
            default:
                return 'text-gray-600';
        }
    };

    return (
        <StaffLayout title="Sensor History">
            <Head title="Sensor History" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex items-center justify-between">
                        <div>
                            <h2 className="text-xl font-semibold text-gray-900">Sensor Data History</h2>
                            <p className="text-gray-600 mt-1">View historical sensor readings and trends</p>
                        </div>
                        <ChartBarIcon className="h-8 w-8 text-blue-600" />
                    </div>
                </div>

                {/* Filters */}
                <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-lg font-semibold text-gray-900">Filters</h3>
                        <FunnelIcon className="h-6 w-6 text-gray-600" />
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Date Range
                            </label>
                            <select
                                value={dateRange}
                                onChange={(e) => setDateRange(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="1d">Last 24 Hours</option>
                                <option value="7d">Last 7 Days</option>
                                <option value="30d">Last 30 Days</option>
                                <option value="90d">Last 90 Days</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Chart Type
                            </label>
                            <select
                                value={chartType}
                                onChange={(e) => setChartType(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="line">Line Chart</option>
                                <option value="bar">Bar Chart</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Sensor Type
                            </label>
                            <select
                                value={selectedSensor}
                                onChange={(e) => setSelectedSensor(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="water_level">Water Level</option>
                                <option value="temperature">Temperature</option>
                                <option value="humidity">Humidity</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Chart */}
                {!loading && sensorData.length > 0 && (
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between mb-6">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900">Sensor Trends</h3>
                                <p className="text-sm text-gray-600">Historical data visualization</p>
                            </div>
                            <ChartBarIcon className="h-6 w-6 text-blue-600" />
                        </div>
                        <div className="h-80">
                            {chartType === 'line' ? (
                                <Line 
                                    data={chartData}
                                    options={{
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'top',
                                            },
                                            title: {
                                                display: false,
                                            },
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                            },
                                        },
                                    }}
                                />
                            ) : (
                                <Bar 
                                    data={chartData}
                                    options={{
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'top',
                                            },
                                            title: {
                                                display: false,
                                            },
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                            },
                                        },
                                    }}
                                />
                            )}
                        </div>
                    </div>
                )}

                {/* Sensor Data */}
                {loading ? (
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span className="ml-3 text-blue-600 font-medium">Loading sensor data...</span>
                        </div>
                    </div>
                ) : (
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold text-gray-900">Sensor Readings</h3>
                            <ChartBarIcon className="h-6 w-6 text-blue-600" />
                        </div>
                        {sensorData.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date/Time
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Water Level
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Temperature
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Humidity
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {sensorData.map((reading, index) => (
                                            <tr key={index} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <div className="flex items-center">
                                                        <ClockIcon className="h-4 w-4 text-gray-400 mr-2" />
                                                        {new Date(reading.timestamp).toLocaleString()}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <DropletIcon className="h-4 w-4 text-blue-500 mr-2" />
                                                        <span className="text-sm font-medium text-gray-900">
                                                            {reading.waterLevel}%
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <ThermometerIcon className="h-4 w-4 text-red-500 mr-2" />
                                                        <span className="text-sm font-medium text-gray-900">
                                                            {reading.temperature}°C
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <SunIcon className="h-4 w-4 text-yellow-500 mr-2" />
                                                        <span className="text-sm font-medium text-gray-900">
                                                            {reading.humidity}%
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                        reading.waterLevel > 50 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                                    }`}>
                                                        {reading.waterLevel > 50 ? 'Normal' : 'Low'}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="text-center py-6">
                                <InformationCircleIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                <p className="text-gray-500">No sensor data available for the selected time range.</p>
                            </div>
                        )}
                    </div>
                )}

                {/* Information */}
                <div className="bg-blue-50 rounded-lg p-3 border border-blue-200">
                    <h4 className="font-medium text-blue-900 mb-2">Sensor History Information</h4>
                    <ul className="text-sm text-blue-800 space-y-1">
                        <li>• Data is updated every 15 minutes</li>
                        <li>• Water level is measured as a percentage</li>
                        <li>• Temperature and humidity are environmental readings</li>
                        <li>• Historical data is stored for 90 days</li>
                        <li>• Charts show trends over the selected time period</li>
                    </ul>
                </div>
            </div>
        </StaffLayout>
    );
} 