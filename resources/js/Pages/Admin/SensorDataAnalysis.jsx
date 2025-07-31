import { Line, Bar, Pie } from 'react-chartjs-2';

export default function SensorDataAnalysis({ historicalData, sensorData, chartOptions }) {
    return (
        <div className="bg-white rounded-lg shadow-sm w-full">
            <div className="bg-white border-b border-gray-100 px-5 py-2 flex items-center justify-between">
                <h2 className="text-sm font-semibold text-gray-900">Sensor Data Analysis</h2>
            </div>

            <div className="p-4 grid grid-cols-3 gap-4">
                {/* Main Trend Chart - Takes full width */}
                <div className="col-span-3 bg-white rounded-lg border border-gray-100 p-3 shadow-sm">
                    <h3 className="text-xs font-medium text-gray-600 mb-2">All Sensors Trend</h3>
                    <div className="h-[300px]">
                        <Line 
                            options={{
                                ...chartOptions,
                                maintainAspectRatio: false,
                                plugins: {
                                    ...chartOptions.plugins,
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        align: 'start',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 6,
                                            boxWidth: 6,
                                            font: {
                                                size: 11
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    ...chartOptions.scales,
                                    x: {
                                        ...chartOptions.scales.x,
                                        ticks: {
                                            ...chartOptions.scales.x.ticks,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    },
                                    y: {
                                        ...chartOptions.scales.y,
                                        ticks: {
                                            ...chartOptions.scales.y.ticks,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    }
                                }
                            }}
                            data={{
                                labels: historicalData.labels,
                                datasets: [
                                    {
                                        label: 'Water Level',
                                        data: historicalData.datasets.water_level,
                                        borderColor: 'rgb(59, 130, 246)',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        fill: true,
                                        borderWidth: 1.5,
                                        tension: 0.4
                                    },
                                    {
                                        label: 'Soil Moisture',
                                        data: historicalData.datasets.soil_moisture,
                                        borderColor: 'rgb(34, 197, 94)',
                                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                        fill: true,
                                        borderWidth: 1.5,
                                        tension: 0.4
                                    },
                                    {
                                        label: 'Temperature',
                                        data: historicalData.datasets.temperature,
                                        borderColor: 'rgb(239, 68, 68)',
                                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                        fill: true,
                                        borderWidth: 1.5,
                                        tension: 0.4
                                    },
                                    {
                                        label: 'Humidity',
                                        data: historicalData.datasets.humidity,
                                        borderColor: 'rgb(147, 51, 234)',
                                        backgroundColor: 'rgba(147, 51, 234, 0.1)',
                                        fill: true,
                                        borderWidth: 1.5,
                                        tension: 0.4
                                    }
                                ]
                            }}
                        />
                    </div>
                </div>

                {/* Current Values Chart */}
                <div className="col-span-3 md:col-span-2 bg-white rounded-lg border border-gray-100 p-3 shadow-sm">
                    <h3 className="text-xs font-medium text-gray-600 mb-2">Current Values</h3>
                    <div className="h-[250px]">
                        <Bar 
                            options={{
                                ...chartOptions,
                                maintainAspectRatio: false,
                                plugins: {
                                    ...chartOptions.plugins,
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    ...chartOptions.scales,
                                    x: {
                                        ...chartOptions.scales.x,
                                        ticks: {
                                            ...chartOptions.scales.x.ticks,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    },
                                    y: {
                                        ...chartOptions.scales.y,
                                        min: 0,
                                        max: 100,
                                        ticks: {
                                            ...chartOptions.scales.y.ticks,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    }
                                }
                            }}
                            data={{
                                labels: ['Water Level', 'Soil Moisture', 'Temperature', 'Humidity'],
                                datasets: [{
                                    data: [
                                        sensorData.water_level,
                                        sensorData.soil_moisture,
                                        sensorData.temperature,
                                        sensorData.humidity
                                    ],
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)',
                                        'rgba(34, 197, 94, 0.8)',
                                        'rgba(239, 68, 68, 0.8)',
                                        'rgba(147, 51, 234, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgb(59, 130, 246)',
                                        'rgb(34, 197, 94)',
                                        'rgb(239, 68, 68)',
                                        'rgb(147, 51, 234)'
                                    ],
                                    borderWidth: 1,
                                    borderRadius: 3
                                }]
                            }}
                        />
                    </div>
                </div>

                {/* Value Distribution Chart */}
                <div className="col-span-3 md:col-span-1 bg-white rounded-lg border border-gray-100 p-3 shadow-sm">
                    <h3 className="text-xs font-medium text-gray-600 mb-2">Value Distribution</h3>
                    <div className="h-[250px]">
                        <Pie 
                            options={{
                                ...chartOptions,
                                maintainAspectRatio: false,
                                plugins: {
                                    ...chartOptions.plugins,
                                    legend: {
                                        display: true,
                                        position: 'right',
                                        align: 'center',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 6,
                                            boxWidth: 6,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    }
                                }
                            }}
                            data={{
                                labels: ['Water Level', 'Soil Moisture', 'Temperature', 'Humidity'],
                                datasets: [{
                                    data: [
                                        sensorData.water_level,
                                        sensorData.soil_moisture,
                                        sensorData.temperature,
                                        sensorData.humidity
                                    ],
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)',
                                        'rgba(34, 197, 94, 0.8)',
                                        'rgba(239, 68, 68, 0.8)',
                                        'rgba(147, 51, 234, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgb(59, 130, 246)',
                                        'rgb(34, 197, 94)',
                                        'rgb(239, 68, 68)',
                                        'rgb(147, 51, 234)'
                                    ],
                                    borderWidth: 1
                                }]
                            }}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
} 