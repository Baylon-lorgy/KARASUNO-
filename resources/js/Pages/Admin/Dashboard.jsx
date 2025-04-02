import { useEffect, useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard() {
    const [sensorData, setSensorData] = useState({
        water_level: 0,
        soil_moisture: 0,
        timestamp: new Date().toLocaleTimeString()
    });

    useEffect(() => {
        console.log('Setting up Echo listener...');
        
        if (!window.Echo) {
            console.error('Echo is not initialized!');
            return;
        }

        const channel = window.Echo.channel('sensor-data');
        console.log('Subscribing to channel: sensor-data');
        
        channel.listen('.SensorDataUpdated', (data) => {
            console.log('Received sensor data:', data);
            setSensorData({
                water_level: data.water_level,
                soil_moisture: data.soil_moisture,
                timestamp: new Date(data.timestamp).toLocaleTimeString()
            });
        });

        // Log connection status
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('Connected to Pusher!');
        });

        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('Pusher connection error:', error);
        });

        return () => {
            console.log('Cleaning up Echo listener...');
            window.Echo.leave('sensor-data');
        };
    }, []);

    return (
        <DashboardLayout>
            <Head title="Dashboard" />
            
            <div className="space-y-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 className="text-xl font-semibold text-gray-800 mb-4">Real-Time Sensor Data</h2>
                    <p className="text-gray-600">Last Updated: {sensorData.timestamp}</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Water Level Sensor */}
                    <div className="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl shadow-md">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-medium text-blue-800">Water Level</h3>
                            <span className="text-sm text-blue-600">{sensorData.timestamp}</span>
                        </div>
                        <div className="text-4xl font-bold text-blue-700">
                            {sensorData.water_level}%
                        </div>
                        <div className="mt-4">
                            <div className="w-full bg-blue-200 rounded-full h-2.5">
                                <div 
                                    className="bg-blue-600 h-2.5 rounded-full transition-all duration-500"
                                    style={{ width: `${sensorData.water_level}%` }}
                                ></div>
                            </div>
                        </div>
                    </div>

                    {/* Soil Moisture Sensor */}
                    <div className="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl shadow-md">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-medium text-green-800">Soil Moisture</h3>
                            <span className="text-sm text-green-600">{sensorData.timestamp}</span>
                        </div>
                        <div className="text-4xl font-bold text-green-700">
                            {sensorData.soil_moisture}%
                        </div>
                        <div className="mt-4">
                            <div className="w-full bg-green-200 rounded-full h-2.5">
                                <div 
                                    className="bg-green-600 h-2.5 rounded-full transition-all duration-500"
                                    style={{ width: `${sensorData.soil_moisture}%` }}
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Status Indicators */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="bg-white rounded-lg p-4 shadow-md">
                        <div className="flex items-center space-x-2">
                            <div className="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                            <span className="text-sm text-gray-600">System Online</span>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg p-4 shadow-md">
                        <div className="flex items-center space-x-2">
                            <div className="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div>
                            <span className="text-sm text-gray-600">Receiving Data</span>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg p-4 shadow-md">
                        <div className="flex items-center space-x-2">
                            <div className="w-3 h-3 rounded-full bg-yellow-500 animate-pulse"></div>
                            <span className="text-sm text-gray-600">Last Update: {sensorData.timestamp}</span>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
