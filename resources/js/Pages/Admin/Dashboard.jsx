import { useEffect, useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import { Line, Doughnut, Bar } from 'react-chartjs-2';
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
    ArcElement,
    BarElement
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
    InformationCircleIcon
} from '@heroicons/react/24/outline';

// Alias icons for our use
const DropletIcon = BeakerIcon;
const LeafIcon = BeakerIcon;
const ThermometerIcon = SunIcon;

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

export default function Dashboard() {
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
        // Set up polling to fetch data from the API
        const fetchData = async () => {
            try {
                const response = await fetch('http://192.168.1.9:8000/api/sensor-data/latest');
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const data = await response.json();
                if (data) {
                    // Update sensor data
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

                    // Update historical data
                    setHistoricalData(prev => {
                        const newLabels = [...prev.labels.slice(1), new Date().toLocaleTimeString()];
                        const newDatasets = {
                            water_level: [...prev.datasets.water_level.slice(1), newSensorData.water_level],
                            soil_moisture: [...prev.datasets.soil_moisture.slice(1), newSensorData.soil_moisture],
                            temperature: [...prev.datasets.temperature.slice(1), newSensorData.temperature],
                            humidity: [...prev.datasets.humidity.slice(1), newSensorData.humidity]
                        };
                        
                        const updatedData = {
                            labels: newLabels,
                            datasets: newDatasets
                        };
                        
                        localStorage.setItem('lastHistoricalData', JSON.stringify(updatedData));
                        return updatedData;
                    });
                }
            } catch (error) {
                console.error('Error fetching sensor data:', error);
            }
        };

        // Fetch data immediately
        fetchData();

        // Set up polling every 30 seconds
        const interval = setInterval(fetchData, 30000);

        return () => clearInterval(interval);
    }, []);

    const getSensorConfig = (type) => {
        const configs = {
            soil_moisture: {
                title: 'Soil Moisture',
                value: sensorData.soil_moisture,
                unit: '%',
                icon: LeafIcon,
                color: 'text-green-600',
                bgColor: 'bg-green-100',
                status: sensorData.soil_moisture > 30 ? 'Good' : 'Low',
                statusColor: sensorData.soil_moisture > 30 ? 'text-green-600 bg-green-100' : 'text-yellow-600 bg-yellow-100',
                tooltip: 'Current soil moisture level',
                chartColor: '#059669',
                data: historicalData.datasets.soil_moisture
            },
            temperature: {
                title: 'Temperature',
                value: sensorData.temperature,
                unit: '°C',
                icon: ThermometerIcon,
                color: 'text-red-600',
                bgColor: 'bg-red-100',
                status: sensorData.temperature < 30 ? 'Normal' : 'High',
                statusColor: sensorData.temperature < 30 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100',
                tooltip: 'Current temperature reading',
                chartColor: '#dc2626',
                data: historicalData.datasets.temperature
            },
            humidity: {
                title: 'Humidity',
                value: sensorData.humidity,
                unit: '%',
                icon: CloudIcon,
                color: 'text-blue-600',
                bgColor: 'bg-blue-100',
                status: sensorData.humidity > 40 ? 'Good' : 'Low',
                statusColor: sensorData.humidity > 40 ? 'text-green-600 bg-green-100' : 'text-yellow-600 bg-yellow-100',
                tooltip: 'Current humidity level',
                chartColor: '#6366f1',
                data: historicalData.datasets.humidity
            }
        };
        return configs[type];
    };

    const renderSensorCard = (type) => {
        const config = getSensorConfig(type);
        const Icon = config.icon;
        return (
            <motion.div
                key={type}
                className="card-responsive card-mobile hover-responsive cursor-pointer"
                onClick={() => setSelectedSensor(type)}
                whileHover={{ scale: 1.02 }}
                whileTap={{ scale: 0.98 }}
            >
                <div className="flex items-center justify-between mb-2 sm:mb-3">
                    <div className={`p-2 rounded-md ${config.bgColor} shadow-md`}> 
                        <Icon className={`h-4 w-4 sm:h-5 sm:w-5 ${config.color}`} /> 
                    </div>
                    <div className="flex items-center gap-1">
                        <span className={`px-1 py-0.5 rounded-full text-xs font-bold ${config.statusColor} bg-opacity-15 shadow-sm`}>
                            {config.status}
                        </span>
                        <div className="relative group">
                            <InformationCircleIcon className="h-3 w-3 sm:h-4 sm:w-4 text-gray-400 cursor-help" />
                            <div className="absolute bottom-full right-0 mb-2 w-36 sm:w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                {config.tooltip}
                                <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 className="text-xs font-bold text-gray-900 mb-1">{config.title}</h3>
                <div className="flex items-baseline gap-1">
                    <div className="text-sm sm:text-base font-bold text-gray-900">{config.value}</div>
                    <div className="text-xs text-gray-500">{config.unit}</div>
                </div>
            </motion.div>
        );
    };

    const renderWaterFloatStatus = () => {
        const isWaterDetected = sensorData.water_float_status === 'water_detected';
        return (
            <motion.div
                className={`card-responsive card-mobile ${isWaterDetected ? 'border-green-200' : 'border-yellow-200'}`}
                whileHover={{ scale: 1.02 }}
                whileTap={{ scale: 0.98 }}
            >
                <div className="flex items-center justify-center mb-2">
                    <span className={`inline-flex items-center justify-center h-6 w-6 rounded-full ${isWaterDetected ? 'bg-green-100 shadow-lg' : 'bg-yellow-100 shadow-lg'}`}>
                        {isWaterDetected ? (
                            <CheckCircleIcon className="h-3 w-3 text-green-600" />
                        ) : (
                            <ExclamationTriangleIcon className="h-3 w-3 text-yellow-600" />
                        )}
                    </span>
                </div>
                <div className="text-center">
                    <h3 className="text-xs font-bold text-gray-900 mb-1">Water Tank</h3>
                    <div className="text-xs text-gray-600">
                        {isWaterDetected ? 'Water Detected' : 'No Water'}
                    </div>
                </div>
            </motion.div>
        );
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                titleColor: '#1F2937',
                bodyColor: '#4B5563',
                borderColor: '#E5E7EB',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: true,
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    };

    return (
        <DashboardLayout>
            <Head title="Dashboard" />
            
            <div className="space-responsive">
                {/* Header */}
                <div className="text-center mb-8">
                    <h1 className="text-3xl sm:text-4xl font-bold text-gray-900 mb-2 tracking-tight">Smart Garden Dashboard</h1>
                    <p className="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">Monitor and manage your garden's health and system status in real-time</p>
                </div>

                {/* Main Grid Layout - Desktop Focused */}
                <div className="grid grid-cols-1 xl:grid-cols-5 gap-6 lg:gap-8">
                    {/* Left: Sensor Cards */}
                    <div className="xl:col-span-1 space-y-4 lg:space-y-6">
                        {/* Sensor Cards */}
                        {['soil_moisture', 'temperature', 'humidity'].map((type) => renderSensorCard(type))}
                        
                        {/* Water Status Cards */}
                        <div className="grid grid-cols-2 gap-4">
                            {/* Enclosure Status */}
                            <div className={`card-responsive card-mobile ${sensorData.water_level === 0 ? 'border-green-200' : 'border-red-200'} hover-responsive`}>
                                <div className="flex items-center justify-center mb-2">
                                    <span className={`inline-flex items-center justify-center h-8 w-8 rounded-full ${sensorData.water_level === 0 ? 'bg-green-100 shadow-lg' : 'bg-red-100 shadow-lg'}`}>
                                        {sensorData.water_level === 0 ? (
                                            <CheckCircleIcon className="h-4 w-4 text-green-600" />
                                        ) : (
                                            <ExclamationTriangleIcon className="h-4 w-4 text-red-600" />
                                        )}
                                    </span>
                                </div>
                                <div className="text-center">
                                    <h3 className="text-sm font-bold text-gray-900 mb-1">Enclosure</h3>
                                    <div className="text-sm text-gray-600">
                                        {sensorData.water_level === 0 ? 'Safe' : 'Alert'}
                                    </div>
                                </div>
                            </div>
                            
                            {/* Water Float Status */}
                            {renderWaterFloatStatus()}
                        </div>
                    </div>

                    {/* Right: Charts and Summary - Desktop Enhanced */}
                    <div className="xl:col-span-4 space-y-6 lg:space-y-8">
                        {/* Main Chart Section */}
                        <div className="card-responsive card-mobile">
                            <div className="flex items-center justify-between mb-6">
                                <div>
                                    <h2 className="text-2xl font-bold text-gray-900">Sensor Trends</h2>
                                    <p className="text-gray-600 mt-1">Real-time sensor data trends over the last 10 minutes</p>
                                </div>
                                <div className="relative group">
                                    <InformationCircleIcon className="h-6 w-6 text-gray-400 cursor-help" />
                                    <div className="absolute bottom-full right-0 mb-2 w-64 p-3 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                        Monitor your garden's environmental conditions with live sensor data
                                        <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                            <div className="h-80 lg:h-96">
                                <Line 
                                    options={{
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: true,
                                                position: 'top',
                                                labels: {
                                                    usePointStyle: true,
                                                    padding: 20,
                                                    font: { size: 14, weight: '600' }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                grid: {
                                                    display: true,
                                                    color: 'rgba(0, 0, 0, 0.05)'
                                                },
                                                ticks: { font: { size: 12 } }
                                            },
                                            x: {
                                                grid: { display: false },
                                                ticks: { font: { size: 12 } }
                                            }
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
                                                borderWidth: 3
                                            },
                                            {
                                                label: 'Temperature',
                                                data: historicalData.datasets.temperature,
                                                borderColor: '#dc2626',
                                                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                                                tension: 0.4,
                                                fill: true,
                                                borderWidth: 3
                                            },
                                            {
                                                label: 'Humidity',
                                                data: historicalData.datasets.humidity,
                                                borderColor: '#6366f1',
                                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                                tension: 0.4,
                                                fill: true,
                                                borderWidth: 3
                                            }
                                        ]
                                    }}
                                />
                            </div>
                        </div>
                        
                        {/* Summary Cards - Desktop Enhanced */}
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                            <div className="card-responsive card-mobile">
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-xl font-bold text-gray-900">Current Status</h3>
                                    <div className="relative group">
                                        <InformationCircleIcon className="h-5 w-5 text-gray-400 cursor-help" />
                                        <div className="absolute bottom-full right-0 mb-2 w-64 p-3 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                            Current sensor readings and their status indicators
                                            <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                                <div className="space-y-3">
                                    <div className="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span className="text-sm font-semibold text-gray-600">Soil Moisture</span>
                                        <span className="text-lg font-bold text-gray-900">{sensorData.soil_moisture}%</span>
                                    </div>
                                    <div className="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span className="text-sm font-semibold text-gray-600">Temperature</span>
                                        <span className="text-lg font-bold text-gray-900">{sensorData.temperature}°C</span>
                                    </div>
                                    <div className="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span className="text-sm font-semibold text-gray-600">Humidity</span>
                                        <span className="text-lg font-bold text-gray-900">{sensorData.humidity}%</span>
                                    </div>
                                    <div className="flex justify-between items-center py-2">
                                        <span className="text-sm font-semibold text-gray-600">Water Level</span>
                                        <span className="text-lg font-bold text-gray-900">{sensorData.water_level}cm</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="card-responsive card-mobile">
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-xl font-bold text-gray-900">System Status</h3>
                                    <div className="relative group">
                                        <InformationCircleIcon className="h-5 w-5 text-gray-400 cursor-help" />
                                        <div className="absolute bottom-full right-0 mb-2 w-64 p-3 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                            System component status and last update information
                                            <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                                <div className="space-y-3">
                                    <div className="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span className="text-sm font-semibold text-gray-600">Enclosure</span>
                                        <span className={`text-lg font-bold ${sensorData.water_level === 0 ? 'text-green-600' : 'text-red-600'}`}>
                                            {sensorData.water_level === 0 ? 'Safe' : 'Alert'}
                                        </span>
                                    </div>
                                    <div className="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span className="text-sm font-semibold text-gray-600">Water Tank</span>
                                        <span className={`text-lg font-bold ${sensorData.water_float_status === 'water_detected' ? 'text-green-600' : 'text-yellow-600'}`}>
                                            {sensorData.water_float_status === 'water_detected' ? 'Full' : 'Low'}
                                        </span>
                                    </div>
                                    <div className="flex justify-between items-center py-2">
                                        <span className="text-sm font-semibold text-gray-600">Last Update</span>
                                        <span className="text-sm text-gray-500">{sensorData.timestamp}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Detailed Sensor Modal */}
            {selectedSensor && selectedSensor !== 'water_float_status' && (
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    className="modal-responsive"
                    onClick={() => setSelectedSensor(null)}
                >
                    <motion.div
                        initial={{ scale: 0.9, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        exit={{ scale: 0.9, opacity: 0 }}
                        className="modal-content-responsive"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="p-6 lg:p-8">
                            <div className="flex items-center justify-between mb-6">
                                <h2 className="text-xl lg:text-2xl font-bold text-[#1e293b]">
                                    {getSensorConfig(selectedSensor).title} History
                                </h2>
                                <button
                                    onClick={() => setSelectedSensor(null)}
                                    className="p-2 rounded-md hover:bg-gray-100"
                                >
                                    <XMarkIcon className="h-6 w-6 text-[#64748b]" />
                                </button>
                            </div>
                            <div className="h-96 lg:h-[500px]">
                                <Line 
                                    options={{
                                        ...chartOptions,
                                        plugins: {
                                            ...chartOptions.plugins,
                                            legend: {
                                                ...chartOptions.plugins.legend,
                                                labels: {
                                                    ...chartOptions.plugins.legend.labels,
                                                    boxWidth: 12,
                                                    padding: 15,
                                                    font: {
                                                        size: 14
                                                    }
                                                }
                                            }
                                        }
                                    }}
                                    data={{
                                        labels: historicalData.labels,
                                        datasets: [{
                                            label: getSensorConfig(selectedSensor).title,
                                            data: getSensorConfig(selectedSensor).data,
                                            borderColor: getSensorConfig(selectedSensor).chartColor,
                                            backgroundColor: `${getSensorConfig(selectedSensor).chartColor}1A`,
                                            tension: 0.4,
                                            fill: true,
                                            borderWidth: 3
                                        }]
                                    }}
                                />
                            </div>
                        </div>
                    </motion.div>
                </motion.div>
            )}
        </DashboardLayout>
    );
}
