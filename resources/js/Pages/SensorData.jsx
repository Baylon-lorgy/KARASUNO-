import { useEffect, useState } from 'react';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export default function SensorData() {
    const [sensorData, setSensorData] = useState({
        water_level: 0,
        soil_moisture: 0,
        timestamp: new Date().toLocaleTimeString()
    });

    useEffect(() => {
        // Initialize Pusher
        window.Pusher = Pusher;

        // Initialize Echo
        const echo = new Echo({
            broadcaster: 'pusher',
            key: process.env.MIX_PUSHER_APP_KEY,
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            forceTLS: true
        });

        // Listen for sensor data updates
        echo.channel('sensor-data')
            .listen('SensorDataUpdated', (e) => {
                setSensorData({
                    water_level: e.water_level,
                    soil_moisture: e.soil_moisture,
                    timestamp: new Date().toLocaleTimeString()
                });
            });

        // Cleanup on unmount
        return () => {
            echo.leave('sensor-data');
        };
    }, []);

    return (
        <div className="space-responsive">
            <div className="card-responsive card-mobile">
                <h2 className="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">Real-Time Sensor Data</h2>
                
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    {/* Water Level Card */}
                    <div className="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 sm:p-6 shadow-md hover-responsive">
                        <div className="flex items-center justify-between mb-3 sm:mb-4">
                            <h3 className="text-base sm:text-lg font-medium text-blue-800">Water Level</h3>
                            <span className="text-xs sm:text-sm text-blue-600">{sensorData.timestamp}</span>
                        </div>
                        <div className="data-responsive text-blue-700 mb-3 sm:mb-4">
                            {sensorData.water_level}%
                        </div>
                        <div className="mt-3 sm:mt-4">
                            <div className="progress-responsive bg-blue-200">
                                <div 
                                    className="progress-bar-responsive bg-blue-600 rounded-full"
                                    style={{ width: `${sensorData.water_level}%` }}
                                ></div>
                            </div>
                        </div>
                    </div>

                    {/* Soil Moisture Card */}
                    <div className="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 sm:p-6 shadow-md hover-responsive">
                        <div className="flex items-center justify-between mb-3 sm:mb-4">
                            <h3 className="text-base sm:text-lg font-medium text-green-800">Soil Moisture</h3>
                            <span className="text-xs sm:text-sm text-green-600">{sensorData.timestamp}</span>
                        </div>
                        <div className="data-responsive text-green-700 mb-3 sm:mb-4">
                            {sensorData.soil_moisture}%
                        </div>
                        <div className="mt-3 sm:mt-4">
                            <div className="progress-responsive bg-green-200">
                                <div 
                                    className="progress-bar-responsive bg-green-600 rounded-full"
                                    style={{ width: `${sensorData.soil_moisture}%` }}
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Status Indicators */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                <div className="card-responsive card-mobile">
                    <div className="status-responsive">
                        <div className="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-green-500 animate-pulse"></div>
                        <span className="label-responsive text-gray-600">System Online</span>
                    </div>
                </div>
                <div className="card-responsive card-mobile">
                    <div className="status-responsive">
                        <div className="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-blue-500 animate-pulse"></div>
                        <span className="label-responsive text-gray-600">Receiving Data</span>
                    </div>
                </div>
                <div className="card-responsive card-mobile sm:col-span-2 lg:col-span-1">
                    <div className="status-responsive">
                        <div className="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-yellow-500 animate-pulse"></div>
                        <span className="label-responsive text-gray-600">Last Update: {sensorData.timestamp}</span>
                    </div>
                </div>
            </div>

            {/* Additional Sensor Information */}
            <div className="card-responsive card-mobile">
                <h3 className="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Sensor Information</h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="text-center p-3 bg-gray-50 rounded-lg">
                        <div className="icon-responsive mx-auto mb-2 text-blue-600">
                            <i className="bi bi-droplet-fill"></i>
                        </div>
                        <div className="text-sm font-medium text-gray-700">Water Level</div>
                        <div className="text-lg font-bold text-blue-600">{sensorData.water_level}%</div>
                    </div>
                    <div className="text-center p-3 bg-gray-50 rounded-lg">
                        <div className="icon-responsive mx-auto mb-2 text-green-600">
                            <i className="bi bi-moisture"></i>
                        </div>
                        <div className="text-sm font-medium text-gray-700">Soil Moisture</div>
                        <div className="text-lg font-bold text-green-600">{sensorData.soil_moisture}%</div>
                    </div>
                    <div className="text-center p-3 bg-gray-50 rounded-lg">
                        <div className="icon-responsive mx-auto mb-2 text-green-500">
                            <i className="bi bi-check-circle-fill"></i>
                        </div>
                        <div className="text-sm font-medium text-gray-700">Status</div>
                        <div className="text-lg font-bold text-green-500">Active</div>
                    </div>
                    <div className="text-center p-3 bg-gray-50 rounded-lg">
                        <div className="icon-responsive mx-auto mb-2 text-blue-500">
                            <i className="bi bi-clock-fill"></i>
                        </div>
                        <div className="text-sm font-medium text-gray-700">Last Update</div>
                        <div className="text-xs font-medium text-gray-500">{sensorData.timestamp}</div>
                    </div>
                </div>
            </div>
        </div>
    );
} 