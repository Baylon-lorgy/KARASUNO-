import { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import StaffLayout from '@/Layouts/StaffLayout';
import { Line } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';
import { motion } from 'framer-motion';
import { 
    CloudIcon,
    ArrowUpIcon,
    ArrowDownIcon,
    CalendarIcon,
    ClockIcon,
    BeakerIcon,
    SunIcon,
    XMarkIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    InformationCircleIcon,
    ChartBarIcon
} from '@heroicons/react/24/outline';

// Alias icons for our use (to mirror Admin)
const DropletIcon = BeakerIcon;
const LeafIcon = BeakerIcon;
const ThermometerIcon = SunIcon;

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

export default function StaffDashboard() {
    const [sensorData, setSensorData] = useState(() => {
        const savedData = localStorage.getItem('lastSensorData');
        return savedData ? JSON.parse(savedData) : {
            water_level: 0,
            soil_moisture: 0,
            temperature: 0,
            humidity: 0,
            water_float_status: 'no_water',
            timestamp: new Date().toLocaleTimeString()
        };
    });

    const [historicalData, setHistoricalData] = useState(() => {
        const savedHistory = localStorage.getItem('lastHistoricalData');
        return savedHistory ? JSON.parse(savedHistory) : {
            labels: Array.from({ length: 10 }, (_, i) => new Date(Date.now() - (9 - i) * 1000 * 60).toLocaleTimeString()),
            datasets: {
                water_level: Array.from({ length: 10 }, () => 0),
                soil_moisture: Array.from({ length: 10 }, () => 0),
                temperature: Array.from({ length: 10 }, () => 0),
                humidity: Array.from({ length: 10 }, () => 0)
            }
        };
    });

    const [selectedSensor, setSelectedSensor] = useState(null);

    useEffect(() => {
        const fetchData = async () => {
        try {
                const response = await fetch('http://192.168.1.9:8000/api/sensor-data/latest');
                if (!response.ok) throw new Error('Failed to fetch data');
                const data = await response.json();
                if (data) {
                    const newSensorData = {
                        water_level: parseFloat(data.water_level) || 0,
                        soil_moisture: parseFloat(data.soil_moisture) || 0,
                        temperature: parseFloat(data.temperature) || 0,
                        humidity: parseFloat(data.humidity) || 0,
                        water_float_status: data.water_float_status || 'no_water',
                        timestamp: new Date().toLocaleTimeString()
                    };
                    setSensorData(newSensorData);
                    localStorage.setItem('lastSensorData', JSON.stringify(newSensorData));

                    setHistoricalData(prev => {
                        const newLabels = [...prev.labels.slice(1), new Date().toLocaleTimeString()];
                        const newDatasets = {
                            water_level: [...prev.datasets.water_level.slice(1), newSensorData.water_level],
                            soil_moisture: [...prev.datasets.soil_moisture.slice(1), newSensorData.soil_moisture],
                            temperature: [...prev.datasets.temperature.slice(1), newSensorData.temperature],
                            humidity: [...prev.datasets.humidity.slice(1), newSensorData.humidity]
                        };
                        const updatedData = { labels: newLabels, datasets: newDatasets };
                        localStorage.setItem('lastHistoricalData', JSON.stringify(updatedData));
                        return updatedData;
                });
            }
        } catch (error) {
                // Silent fail; keep last cache
            }
        };
        fetchData();
        const interval = setInterval(fetchData, 30000);
        return () => clearInterval(interval);
    }, []);

    const getTrend = (current, previous) => {
        if (current > previous) return { direction: 'up', color: 'text-green-600', icon: ArrowUpIcon };
        if (current < previous) return { direction: 'down', color: 'text-red-600', icon: ArrowDownIcon };
        return { direction: 'stable', color: 'text-gray-600', icon: ChartBarIcon };
    };

    const getStatus = (type, value) => {
        switch (type) {
            case 'soil_moisture':
                return value > 40 ? { status: 'Optimal', color: 'text-green-600 bg-green-100' } : 
                       value > 20 ? { status: 'Low', color: 'text-yellow-600 bg-yellow-100' } : 
                       { status: 'Critical', color: 'text-red-600 bg-red-100' };
            case 'temperature':
                return value < 25 ? { status: 'Cool', color: 'text-blue-600 bg-blue-100' } : 
                       value < 35 ? { status: 'Normal', color: 'text-green-600 bg-green-100' } : 
                       { status: 'Hot', color: 'text-red-600 bg-red-100' };
            case 'humidity':
                return value > 60 ? { status: 'High', color: 'text-blue-600 bg-blue-100' } : 
                       value > 40 ? { status: 'Normal', color: 'text-green-600 bg-green-100' } : 
                       { status: 'Low', color: 'text-yellow-600 bg-yellow-100' };
            case 'water_level':
                return value === 0 ? { status: 'Safe', color: 'text-green-600 bg-green-100' } : 
                       { status: 'Alert', color: 'text-red-600 bg-red-100' };
            default:
                return { status: 'Normal', color: 'text-gray-600 bg-gray-100' };
        }
    };

    const kpiCards = [
        {
            title: 'Soil Moisture',
            value: sensorData.soil_moisture,
            unit: '%',
            icon: LeafIcon,
            color: 'text-green-600',
            bgColor: 'bg-green-100',
            status: getStatus('soil_moisture', sensorData.soil_moisture),
            trend: getTrend(sensorData.soil_moisture, historicalData.datasets.soil_moisture[historicalData.datasets.soil_moisture.length - 2] || 0)
        },
        {
            title: 'Temperature',
            value: sensorData.temperature,
            unit: '°C',
            icon: ThermometerIcon,
            color: 'text-red-600',
            bgColor: 'bg-red-100',
            status: getStatus('temperature', sensorData.temperature),
            trend: getTrend(sensorData.temperature, historicalData.datasets.temperature[historicalData.datasets.temperature.length - 2] || 0)
        },
        {
            title: 'Humidity',
            value: sensorData.humidity,
            unit: '%',
            icon: CloudIcon,
            color: 'text-blue-600',
            bgColor: 'bg-blue-100',
            status: getStatus('humidity', sensorData.humidity),
            trend: getTrend(sensorData.humidity, historicalData.datasets.humidity[historicalData.datasets.humidity.length - 2] || 0)
        },
        {
            title: 'Water Level',
            value: sensorData.water_level,
            unit: 'cm',
            icon: DropletIcon,
            color: 'text-blue-600',
            bgColor: 'bg-blue-100',
            status: getStatus('water_level', sensorData.water_level),
            trend: getTrend(sensorData.water_level, historicalData.datasets.water_level[historicalData.datasets.water_level.length - 2] || 0)
        }
    ];

    const systemStatus = [
        {
            title: 'Enclosure',
            status: sensorData.water_level === 0 ? 'Safe' : 'Alert',
            color: sensorData.water_level === 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100',
            icon: sensorData.water_level === 0 ? CheckCircleIcon : ExclamationTriangleIcon
        },
        {
            title: 'Water Tank',
            status: sensorData.water_float_status === 'water_detected' ? 'Full' : 'Low',
            color: sensorData.water_float_status === 'water_detected' ? 'text-green-600 bg-green-100' : 'text-yellow-600 bg-yellow-100',
            icon: sensorData.water_float_status === 'water_detected' ? CheckCircleIcon : ExclamationTriangleIcon
        }
    ];

    return (
        <StaffLayout>
            <Head title="Analytics Dashboard" />

            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center gap-2 mb-1">
                        <h1 className="text-xl font-bold text-gray-900 tracking-tight">Garden Analytics Dashboard</h1>
                        <div className="relative group">
                            <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                Real-time monitoring and insights for your smart garden system
                                <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Real-time monitoring and insights for your smart garden system</p>
                </div>

                {/* KPI Cards Grid */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    {kpiCards.map((kpi) => {
                        const Icon = kpi.icon;
                        const TrendIcon = kpi.trend.icon;
                        return (
                            <motion.div
                                key={kpi.title}
                                className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200 hover:shadow-xl transition-all duration-200"
                                whileHover={{ scale: 1.02 }}
                                whileTap={{ scale: 0.98 }}
                            >
                                <div className="flex items-center justify-between mb-2">
                                    <div className={`p-2 rounded-lg ${kpi.bgColor}`}>
                                        <Icon className={`h-5 w-5 ${kpi.color}`} />
                                    </div>
                                    <div className="flex items-center gap-1">
                                        <TrendIcon className={`h-3 w-3 ${kpi.trend.color}`} />
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${kpi.status.color}`}>
                                            {kpi.status.status}
                                        </span>
                                    </div>
                                </div>
                                <h3 className="text-xs font-semibold text-gray-600 mb-1">{kpi.title}</h3>
                                <div className="flex items-baseline gap-1">
                                    <span className="text-lg font-bold text-gray-900">{kpi.value}</span>
                                    <span className="text-xs text-gray-500">{kpi.unit}</span>
                                </div>
                            </motion.div>
                        );
                    })}
                </div>

                {/* Main Content Grid */}
                <div className="grid grid-cols-1 xl:grid-cols-3 gap-2">
                    {/* Left: System Status */}
                    <div className="xl:col-span-1 space-y-2">
                        <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                            <div className="flex items-center justify-between mb-3">
                                <h3 className="text-sm font-bold text-gray-900">System Status</h3>
                                <div className="relative group">
                                    <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                    <div className="absolute bottom-full right-0 mb-2 w-40 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                        Current system component status
                                        <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                                </div>
                            </div>
                            <div className="space-y-2">
                                {systemStatus.map((status) => {
                                    const Icon = status.icon;
                                    return (
                                        <div key={status.title} className="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                            <div className="flex items-center gap-2">
                                                <Icon className="h-4 w-4 text-gray-600" />
                                                <span className="text-xs font-semibold text-gray-600">{status.title}</span>
                                </div>
                                            <span className={`px-2 py-1 rounded-full text-xs font-medium ${status.color}`}>
                                                {status.status}
                                </span>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                            <h3 className="text-sm font-bold text-gray-900 mb-3">Quick Stats</h3>
                            <div className="space-y-2">
                                <div className="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg">
                                    <span className="text-xs font-semibold text-gray-600">Last Update</span>
                                    <span className="text-xs text-gray-500">{sensorData.timestamp}</span>
                            </div>
                                <div className="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg">
                                    <span className="text-xs font-semibold text-gray-600">Data Points</span>
                                    <span className="text-xs text-gray-500">{historicalData.labels.length}</span>
                                </div>
                                <div className="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg">
                                    <span className="text-xs font-semibold text-gray-600">Monitoring</span>
                                    <span className="text-xs text-green-600 font-semibold">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right: Charts */}
                    <div className="xl:col-span-2 space-y-2">
                        <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                            <div className="flex items-center justify-between mb-3">
                            <div>
                                    <h2 className="text-sm font-bold text-gray-900">Sensor Trends</h2>
                                    <p className="text-xs text-gray-600">Real-time data over the last 10 minutes</p>
                            </div>
                                <div className="relative group">
                                    <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                    <div className="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                        Monitor your garden's environmental conditions with live sensor data
                                        <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                        </div>
                        <div className="h-64">
                            <Line 
                                options={{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                                display: true,
                                            position: 'top',
                                                labels: { usePointStyle: true, padding: 15, font: { size: 12, weight: '600' } }
                                            }
                                    },
                                    scales: {
                                            y: { beginAtZero: true, grid: { display: true, color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
                                            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                                        }
                                    }}
                                    data={{
                                        labels: historicalData.labels,
                                        datasets: [
                                            {
                                                label: 'Soil Moisture',
                                                data: historicalData.datasets.soil_moisture,
                                                borderColor: '#059669',
                                                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                                                tension: 0.4,
                                                fill: true,
                                                borderWidth: 2
                                            },
                                            {
                                                label: 'Temperature',
                                                data: historicalData.datasets.temperature,
                                                borderColor: '#dc2626',
                                                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                                                tension: 0.4,
                                                fill: true,
                                                borderWidth: 2
                                            },
                                            {
                                                label: 'Humidity',
                                                data: historicalData.datasets.humidity,
                                                borderColor: '#6366f1',
                                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                                tension: 0.4,
                                                fill: true,
                                                borderWidth: 2
                                            }
                                        ]
                                    }}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </StaffLayout>
    );
} 