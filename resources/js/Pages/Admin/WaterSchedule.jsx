import { useState, useEffect, useRef } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import { BeakerIcon, SunIcon, CloudIcon, PlayIcon, StopIcon, ClockIcon, TrashIcon, InformationCircleIcon, CalendarIcon, ChartBarIcon } from '@heroicons/react/24/solid';
import { toast } from 'react-hot-toast';

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Add a request interceptor to include the CSRF token and ensure proper authentication
axios.interceptors.request.use(config => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    // Ensure we're sending the session cookie for web authentication
    config.withCredentials = true;
    
    // Add additional headers for better compatibility
    config.headers['Accept'] = 'application/json';
    config.headers['Content-Type'] = 'application/json';
    
    return config;
});

export default function WaterSchedule() {
    const [isLoading, setIsLoading] = useState(false);
    const [wateringActive, setWateringActive] = useState(false);
    const [showStartConfirmation, setShowStartConfirmation] = useState(false);
    const [showStopConfirmation, setShowStopConfirmation] = useState(false);
    const [wateringDuration, setWateringDuration] = useState(3); // Default 3 minutes
    const [remainingTime, setRemainingTime] = useState(0);
    const [sensorData, setSensorData] = useState({
        soil_moisture: 0,
        temperature: 0,
        humidity: 0
    });
    const [autoWatering, setAutoWatering] = useState(true);
    const [thresholds, setThresholds] = useState({
        soil_moisture: 40, // Default threshold
        temperature: 30,
        humidity: 40
    });
    const [popupMessage, setPopupMessage] = useState('');
    const [schedules, setSchedules] = useState([]);
    const [newScheduleTime, setNewScheduleTime] = useState('08:00');
    const [newScheduleDuration, setNewScheduleDuration] = useState(3);
    const [scheduleLoading, setScheduleLoading] = useState(false);
    const [selectedDays, setSelectedDays] = useState([0,1,2,3,4,5,6]);
    const DAYS = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const [showScheduleModal, setShowScheduleModal] = useState(false);
    const [editScheduleId, setEditScheduleId] = useState(null);
    const [scheduleConflicts, setScheduleConflicts] = useState([]);
    const [checkingConflicts, setCheckingConflicts] = useState(false);
    const prevWateringActive = useRef(wateringActive);
    
    // New state for on/off history
    const [wateringHistory, setWateringHistory] = useState([]);
    const [showHistoryModal, setShowHistoryModal] = useState(false);
    const [historyLoading, setHistoryLoading] = useState(false);

    // Fern-specific soil moisture logic
    // 0-30%: Too dry, 40-70%: Optimal, 80-100%: Too wet
    const FERN_MIN = 40;
    const FERN_MAX = 70;

    const DAY_COLORS = [
        'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-orange-500', 'bg-pink-500', 'bg-purple-500', 'bg-gray-500'
    ];

    useEffect(() => {
        const syncWateringState = async () => {
            try {
                const response = await axios.get('/api/watering-status');
                if (response.data.success) {
                    setWateringActive(response.data.watering_active);
                    // Only update remainingTime if watering just started or stopped
                    if (
                        response.data.watering_active !== prevWateringActive.current ||
                        !wateringActive // first load
                    ) {
                        setRemainingTime(response.data.remaining_time || 0);
                    }
                    prevWateringActive.current = response.data.watering_active;
                }
            } catch (error) {
                // Optionally handle error
            }
        };
        syncWateringState();
        const interval = setInterval(syncWateringState, 10000);
        return () => clearInterval(interval);
    }, [wateringActive]);

    useEffect(() => {
        if (wateringActive && remainingTime > 0) {
            const interval = setInterval(() => {
                setRemainingTime(prev => (prev > 0 ? prev - 1 : 0));
            }, 1000);
            return () => clearInterval(interval);
        }
    }, [wateringActive, remainingTime]);

    useEffect(() => {
        const fetchSensorData = async () => {
            try {
                const response = await axios.get('/api/sensor-data/latest');
                if (response.data) {
                    setSensorData({
                        soil_moisture: response.data.soil_moisture,
                        temperature: response.data.temperature,
                        humidity: response.data.humidity
                    });
                    // Auto-watering logic
                    if (autoWatering && !wateringActive && response.data.soil_moisture < thresholds.soil_moisture) {
                        startWatering(3, 'automatic');
                    }
                }
            } catch (error) {
                // Optionally handle error
            }
        };
        fetchSensorData();
        const interval = setInterval(fetchSensorData, 30000);
        return () => clearInterval(interval);
    }, [autoWatering, wateringActive, thresholds.soil_moisture]);

    // Fetch schedules
    useEffect(() => {
        fetchSchedules();
    }, []);

    // Check for conflicts in existing schedules
    useEffect(() => {
        const checkExistingConflicts = async () => {
            try {
                const response = await axios.get('/api/watering-schedules/conflicts');
                if (response.data.has_conflicts) {
                    toast.error('Warning: Some schedules have conflicts!', {
                        duration: 8000,
                        style: {
                            background: '#fef2f2',
                            color: '#dc2626',
                            border: '1px solid #fecaca'
                        }
                    });
                }
            } catch (error) {
                console.error('Error checking for conflicts:', error);
            }
        };

        if (schedules.length > 0) {
            checkExistingConflicts();
        }
    }, [schedules]);

    useEffect(() => {
        // fetch the current soil moisture threshold on mount
        axios.get('/api/sensor-data/latest').then(res => {
            if (res.data && res.data.thresholds && typeof res.data.thresholds.soil_moisture !== 'undefined') {
                setThresholds(prev => ({
                    ...prev,
                    soil_moisture: res.data.thresholds.soil_moisture
                }));
            }
        });
    }, []);

    const fetchSchedules = async () => {
        setScheduleLoading(true);
        try {
            const res = await axios.get('/api/watering-schedules');
            setSchedules(res.data);
        } catch (e) {
            toast.error('Failed to load schedules');
        } finally {
            setScheduleLoading(false);
        }
    };

    // Fetch watering history
    const fetchWateringHistory = async () => {
        setHistoryLoading(true);
        try {
            const response = await axios.get('/api/watering-history');
            setWateringHistory(response.data || []);
        } catch (error) {
            console.error('Error fetching watering history:', error);
            toast.error('Failed to load watering history');
        } finally {
            setHistoryLoading(false);
        }
    };

    const formatHistoryDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleString();
    };

    const getSourceIcon = (source) => {
        switch (source) {
            case 'manual':
                return <PlayIcon className="h-4 w-4 text-blue-500" />;
            case 'automatic':
                return <CloudIcon className="h-4 w-4 text-green-500" />;
            case 'scheduled':
                return <ClockIcon className="h-4 w-4 text-purple-500" />;
            default:
                return <InformationCircleIcon className="h-4 w-4 text-gray-500" />;
        }
    };

    const getSourceColor = (source) => {
        switch (source) {
            case 'manual':
                return 'bg-blue-100 text-blue-800';
            case 'automatic':
                return 'bg-green-100 text-green-800';
            case 'scheduled':
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const openEditModal = (sch) => {
        setEditScheduleId(sch.id);
        setNewScheduleTime(sch.start_time);
        setNewScheduleDuration(sch.duration);
        setSelectedDays(sch.days_of_week.map(Number));
        setScheduleConflicts([]); // Clear previous conflicts
        setShowScheduleModal(true);
    };

    const checkForConflicts = async () => {
        if (!newScheduleTime || selectedDays.length === 0) {
            setScheduleConflicts([]);
            return;
        }

        setCheckingConflicts(true);
        try {
            const response = await axios.post('/api/watering-schedules/check-conflicts', {
                start_time: newScheduleTime,
                duration: newScheduleDuration,
                days_of_week: selectedDays,
                exclude_id: editScheduleId
            });
            
            setScheduleConflicts(response.data.conflicts || []);
        } catch (error) {
            console.error('Error checking conflicts:', error);
            setScheduleConflicts([]);
        } finally {
            setCheckingConflicts(false);
        }
    };

    // Check for conflicts when schedule parameters change
    useEffect(() => {
        if (showScheduleModal) {
            const timeoutId = setTimeout(checkForConflicts, 500); // Debounce
            return () => clearTimeout(timeoutId);
        }
    }, [newScheduleTime, newScheduleDuration, selectedDays, showScheduleModal]);

    const saveSchedule = async () => {
        if (!newScheduleTime) return toast.error('Please select a time');
        if (selectedDays.length === 0) return toast.error('Select at least one day');
        setScheduleLoading(true);
        try {
            if (editScheduleId) {
                await axios.put(`/api/watering-schedules/${editScheduleId}`, {
                    start_time: newScheduleTime,
                    duration: newScheduleDuration,
                    days_of_week: selectedDays,
                    is_active: true
                });
                toast.success('Schedule updated');
            } else {
                await axios.post('/api/watering-schedules', {
                    start_time: newScheduleTime,
                    duration: newScheduleDuration,
                    days_of_week: selectedDays,
                    is_active: true
                });
                toast.success('Schedule added');
            }
            setNewScheduleTime('08:00');
            setNewScheduleDuration(3);
            setSelectedDays([0,1,2,3,4,5,6]);
            setEditScheduleId(null);
            setScheduleConflicts([]);
            setShowScheduleModal(false);
            fetchSchedules();
        } catch (e) {
            if (e.response?.status === 409 && e.response?.data?.type === 'overlap_conflict') {
                // Handle overlapping schedule error
                const conflicts = e.response.data.conflicts;
                const conflictDetails = conflicts.map(conflict => 
                    `${conflict.time} (${conflict.days})`
                ).join(', ');
                
                toast.error(
                    `Schedule conflicts with: ${conflictDetails}`,
                    {
                        duration: 6000,
                        style: {
                            background: '#fef2f2',
                            color: '#dc2626',
                            border: '1px solid #fecaca'
                        }
                    }
                );
            } else if (e.response?.status === 422) {
                // Handle validation errors
                const errorMessage = e.response.data.message || 'Validation failed';
                toast.error(errorMessage);
            } else {
                toast.error('Failed to save schedule');
            }
        } finally {
            setScheduleLoading(false);
        }
    };

    const deleteSchedule = async (id) => {
        setScheduleLoading(true);
        try {
            await axios.delete(`/api/watering-schedules/${id}`);
            fetchSchedules();
            toast.success('Schedule deleted');
        } catch (e) {
            toast.error('Failed to delete schedule');
        } finally {
            setScheduleLoading(false);
        }
    };

    const startWatering = async (duration, source = 'manual') => {
        if (duration < 1 || duration > 5) {
            setPopupMessage('Duration must be between 1 and 5 minutes.');
            return;
        }
        try {
            setIsLoading(true);
            const response = await axios.post('/api/watering-control', {
                should_water: false,
                duration: duration
            });
            if (response.data.success) {
                setWateringActive(true);
                setRemainingTime(response.data.remaining_time || duration * 60);
                setPopupMessage(`Watering started for ${duration} minutes.`);
                // Send notification for manual or automatic watering
                await axios.post('/api/notifications', {
                    type: 'watering_started',
                    source,
                    duration,
                    status: 'COMPLETED',
                    message: `${source === 'manual' ? 'Manual' : source === 'automatic' ? 'Automatic' : 'Scheduled'} watering started for ${duration} minutes.`
                });
                // Refresh history after starting watering
                fetchWateringHistory();
            } else {
                setPopupMessage(response.data.error || 'Failed to start watering.');
            }
        } catch (error) {
            setPopupMessage('Error starting watering.');
        } finally {
            setIsLoading(false);
            setShowStartConfirmation(false);
        }
    };

    const stopWatering = async (source = 'manual') => {
        try {
            setIsLoading(true);
            const response = await axios.post('/api/watering-control', {
                should_water: true
            });
            if (response.data.success) {
                setWateringActive(false);
                setRemainingTime(0);
                setPopupMessage('Watering stopped successfully.');
                // Send notification for watering stopped
                await axios.post('/api/notifications', {
                    type: 'watering_stopped',
                    source,
                    status: 'COMPLETED',
                    message: `${source === 'manual' ? 'Manual' : source === 'automatic' ? 'Automatic' : 'Scheduled'} watering stopped.`
                });
                // Refresh history after stopping watering
                fetchWateringHistory();
            } else {
                setPopupMessage(response.data.error || 'Failed to stop watering.');
            }
        } catch (error) {
            setPopupMessage('Error stopping watering.');
        } finally {
            setIsLoading(false);
            setShowStopConfirmation(false);
        }
    };

    const formatTime = (seconds) => {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    };

    const checkWateringNeeds = (data) => {
        const { soil_moisture } = data;
        // Only water if below 40%
        return soil_moisture < FERN_MIN;
    };

    const handleThresholdChange = (sensor, value) => {
        setThresholds(prev => ({
            ...prev,
            [sensor]: parseInt(value)
        }));
        // Don't auto-save on slider change - user must press Save button
    };

    const getSensorStatus = (type, value) => {
        switch (type) {
            case 'soil_moisture':
                return value < thresholds.soil_moisture ? 'Dry' : 'Moist';
            case 'temperature':
                return value > thresholds.temperature ? 'Hot' : 'Normal';
            case 'humidity':
                return value < thresholds.humidity ? 'Low' : 'Normal';
            default:
                return 'Normal';
        }
    };

    const getSensorColor = (type, value) => {
        switch (type) {
            case 'soil_moisture':
                return value < thresholds.soil_moisture ? 'text-red-500' : 'text-green-500';
            case 'temperature':
                return value > thresholds.temperature ? 'text-red-500' : 'text-green-500';
            case 'humidity':
                return value < thresholds.humidity ? 'text-red-500' : 'text-green-500';
            default:
                return 'text-gray-500';
        }
    };

    return (
        <DashboardLayout>
            <Head title="Water Control" />
            
            <div className="space-y-2">
                {/* Header */}
                <div className="text-center">
                    <h1 className="text-xl font-bold text-gray-900 mb-1 tracking-tight">Water Control</h1>
                    <p className="text-xs text-gray-600 max-w-2xl mx-auto leading-relaxed">Manage watering schedules and manual controls for your garden</p>
                </div>

                {/* Main Grid Layout */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-2">
                    {/* Left: Timer and Manual Control */}
                    <div className="space-y-2">
                        {/* Timer Card */}
                        <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                            <div className="text-center">
                                <div className="flex items-center justify-center gap-2 mb-2">
                                    <h2 className="text-sm font-bold text-gray-900">Watering Timer</h2>
                                    <div className="relative group">
                                        <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                        <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                            Shows remaining watering time when active
                                            <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                                <div className={`text-2xl font-mono mb-2 ${wateringActive ? 'text-green-600' : 'text-gray-400'}`}>
                                    {wateringActive ? formatTime(remainingTime) : '--:--'}
                                </div>
                                <div className="text-xs text-gray-500">
                                    {wateringActive ? 'Watering in progress...' : 'No active watering'}
                                </div>
                            </div>
                        </div>

                        {/* Manual Control */}
                        <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                            <div className="flex items-center justify-between mb-2">
                                <h2 className="text-sm font-bold text-gray-900">Manual Control</h2>
                                <div className="relative group">
                                    <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                    <div className="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                        Manually start or stop watering for immediate control
                                        <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                            <div className="space-y-2">
                                {!wateringActive && (
                                    <div className="flex items-center gap-2">
                                        <label className="text-xs font-semibold text-gray-700">Duration:</label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="5"
                                            value={wateringDuration}
                                            onChange={e => setWateringDuration(Math.max(1, Math.min(5, parseInt(e.target.value) || 1)))}
                                            className="w-16 px-2 py-1 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        <span className="text-xs text-gray-500">minutes</span>
                                        <div className="relative group">
                                            <InformationCircleIcon className="h-3 w-3 text-gray-400 cursor-help" />
                                            <div className="absolute bottom-full left-0 mb-2 w-32 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                Set watering duration (1-5 minutes)
                                                <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                )}
                                <div className="flex gap-2">
                                    <button
                                        onClick={() => setShowStartConfirmation(true)}
                                        disabled={wateringActive || isLoading}
                                        className={`flex-1 px-3 py-2 rounded-lg text-xs font-bold transition-all duration-300 ${
                                            wateringActive || isLoading
                                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                : 'bg-green-600 text-white hover:bg-green-700 shadow-lg hover:shadow-xl'
                                        }`}
                                    >
                                        Start Watering
                                    </button>
                                    <button
                                        onClick={() => setShowStopConfirmation(true)}
                                        disabled={!wateringActive || isLoading}
                                        className={`flex-1 px-3 py-2 rounded-lg text-xs font-bold transition-all duration-300 ${
                                            !wateringActive || isLoading
                                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                : 'bg-red-600 text-white hover:bg-red-700 shadow-lg hover:shadow-xl'
                                        }`}
                                    >
                                        Stop Watering
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Auto Watering */}
                        <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                            <div className="flex items-center justify-between mb-2">
                                <h2 className="text-sm font-bold text-gray-900">Auto Watering</h2>
                                <div className="relative group">
                                    <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                    <div className="absolute bottom-full right-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                        Automatically water when soil moisture is below threshold
                                        <div className="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-xs font-semibold text-gray-700">Soil Moisture Threshold</span>
                                    <span className="text-sm font-bold text-gray-900">{thresholds.soil_moisture}%</span>
                                </div>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={thresholds.soil_moisture}
                                    onChange={e => handleThresholdChange('soil_moisture', parseInt(e.target.value))}
                                    className="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-500"
                                />
                                <div className="flex justify-between text-xs text-gray-500">
                                    <span>Current: {sensorData.soil_moisture}%</span>
                                    <span className={sensorData.soil_moisture < thresholds.soil_moisture ? 'text-red-500 font-bold' : 'text-green-500 font-bold'}>
                                        {sensorData.soil_moisture < thresholds.soil_moisture ? 'Needs Water' : 'OK'}
                                    </span>
                                </div>
                                <div className="flex justify-end pt-2">
                                    <button
                                        onClick={() => {
                                            // Show loading state
                                            const button = event.target;
                                            const originalText = button.textContent;
                                            button.textContent = 'Saving...';
                                            button.disabled = true;
                                            
                                            axios.post('/api/sensor/thresholds', {
                                                soil_moisture: thresholds.soil_moisture,
                                                temperature: thresholds.temperature,
                                                humidity: thresholds.humidity
                                            }).then((response) => {
                                                toast.success('Soil moisture threshold saved successfully!');
                                                console.log('Save response:', response.data);
                                            }).catch((error) => {
                                                console.error('Save error:', error);
                                                console.error('Error response:', error.response);
                                                
                                                if (error.response?.status === 401) {
                                                    toast.error('Please log in to save settings');
                                                } else if (error.response?.status === 422) {
                                                    toast.error('Invalid threshold values');
                                                } else if (error.response?.status === 500) {
                                                    toast.error('Server error: ' + (error.response?.data?.message || 'Unknown error'));
                                                } else {
                                                    toast.error('Failed to save threshold: ' + (error.response?.data?.message || error.message));
                                                }
                                            }).finally(() => {
                                                // Restore button state
                                                button.textContent = originalText;
                                                button.disabled = false;
                                            });
                                        }}
                                        className="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right: Schedules */}
                    <div className="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg p-3 border border-gray-200">
                        <div className="flex items-center justify-between mb-3">
                            <div className="flex items-center gap-2">
                                <h2 className="text-sm font-bold text-gray-900">Schedules</h2>
                                <div className="relative group">
                                    <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                    <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                        Set automated watering schedules for specific times and days
                                        <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                            <div className="flex gap-2">
                                <button
                                    onClick={() => { setShowHistoryModal(true); fetchWateringHistory(); }}
                                    className="px-3 py-1 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 shadow-lg hover:shadow-xl transition-all duration-300"
                                >
                                    <ChartBarIcon className="h-3 w-3 inline mr-1" />
                                    History
                                </button>
                                <button
                                    onClick={() => { setShowScheduleModal(true); setEditScheduleId(null); }}
                                    className="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all duration-300"
                                >
                                    Add Schedule
                                </button>
                            </div>
                        </div>

                        {schedules.length === 0 ? (
                            <div className="text-center py-4">
                                <ClockIcon className="mx-auto h-8 w-8 text-gray-300 mb-2" />
                                <p className="text-sm text-gray-500 mb-1">No schedules set</p>
                                <p className="text-xs text-gray-400">Add a schedule to automate watering</p>
                            </div>
                        ) : (
                            <div className="space-y-2">
                                {schedules.map(sch => (
                                    <div key={sch.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:shadow-md transition-all duration-300">
                                        <div>
                                            <div className="text-sm font-bold text-gray-900">{sch.start_time}</div>
                                            <div className="text-xs text-gray-500 mt-1">
                                                {sch.duration} min • {sch.days_of_week.map(d => DAYS[parseInt(d)]).join(', ')}
                                            </div>
                                        </div>
                                        <div className="flex gap-2">
                                            <button
                                                onClick={() => openEditModal(sch)}
                                                className="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button
                                                onClick={() => deleteSchedule(sch.id)}
                                                className="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                                            >
                                                <TrashIcon className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Modals */}
                            {showStartConfirmation && (
                                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-[#1e293b] mb-3">Start Watering</h3>
                            <p className="text-sm text-[#64748b] mb-4">Start watering for {wateringDuration} minutes?</p>
                            <div className="flex justify-end space-x-3">
                                                <button
                                                    onClick={() => setShowStartConfirmation(false)}
                                    className="px-4 py-2 text-sm font-medium text-[#64748b] bg-gray-100 rounded-md hover:bg-gray-200"
                                >Cancel</button>
                                                <button
                                    onClick={() => startWatering(wateringDuration)}
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#2563eb] rounded-md hover:bg-[#1d4ed8]"
                                >Start</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {showStopConfirmation && (
                                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-[#1e293b] mb-3">Stop Watering</h3>
                            <p className="text-sm text-[#64748b] mb-4">Stop watering now? {remainingTime > 0 && `(${formatTime(remainingTime)} remaining)`}</p>
                            <div className="flex justify-end space-x-3">
                                                <button
                                                    onClick={() => setShowStopConfirmation(false)}
                                    className="px-4 py-2 text-sm font-medium text-[#64748b] bg-gray-100 rounded-md hover:bg-gray-200"
                                >Cancel</button>
                                                <button
                                                    onClick={() => stopWatering()}
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#dc2626] rounded-md hover:bg-[#b91c1c]"
                                >Stop</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

            {/* Watering History Modal */}
            {showHistoryModal && (
                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
                        <div className="p-6">
                            <div className="flex items-center justify-between mb-4">
                                <div className="flex items-center gap-2">
                                    <h3 className="text-xl font-semibold text-[#1e293b]">Watering History</h3>
                                    <div className="relative group">
                                        <InformationCircleIcon className="h-4 w-4 text-gray-400 cursor-help" />
                                        <div className="absolute bottom-full left-0 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                            Track all watering system activations and deactivations
                                            <div className="absolute top-full left-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setShowHistoryModal(false)}
                                    className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            {historyLoading ? (
                                <div className="flex items-center justify-center py-8">
                                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                    <span className="ml-3 text-blue-600 font-medium">Loading history...</span>
                                </div>
                            ) : wateringHistory.length === 0 ? (
                                <div className="text-center py-8">
                                    <CalendarIcon className="mx-auto h-12 w-12 text-gray-300 mb-4" />
                                    <p className="text-lg font-medium text-gray-500 mb-2">No watering history</p>
                                    <p className="text-sm text-gray-400">Start watering to see history here</p>
                                </div>
                            ) : (
                                <div className="overflow-y-auto max-h-[60vh]">
                                    <div className="space-y-3">
                                        {wateringHistory.map((entry, index) => (
                                            <div key={index} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <div className="flex items-center gap-3">
                                                    {getSourceIcon(entry.source)}
                                                    <div>
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-sm font-bold text-gray-900">
                                                                {entry.action === 'start' ? 'Started' : 'Stopped'} Watering
                                                            </span>
                                                            <span className={`px-2 py-1 rounded-full text-xs font-medium ${getSourceColor(entry.source)}`}>
                                                                {entry.source}
                                                            </span>
                                                        </div>
                                                        <div className="text-xs text-gray-500 mt-1">
                                                            {entry.duration && `Duration: ${entry.duration} minutes`}
                                                            {entry.reason && ` • ${entry.reason}`}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="text-right">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {formatHistoryDate(entry.timestamp)}
                                                    </div>
                                                    <div className="text-xs text-gray-500">
                                                        {entry.action === 'start' ? 'Started' : 'Stopped'}
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {showScheduleModal && (
                <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <h3 className="text-xl font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
                                <ClockIcon className="h-6 w-6 text-blue-500" /> {editScheduleId ? 'Edit' : 'Create New'} Watering Schedule
                            </h3>
                            <p className="text-sm text-gray-600 mb-4">Configure when and how often your plants should be watered automatically.</p>
                            
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-[#64748b] mb-2">Start Time</label>
                                <div className="flex items-center gap-2">
                                    <input
                                        type="time"
                                        value={newScheduleTime}
                                        onChange={e => setNewScheduleTime(e.target.value)}
                                        className="border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 w-full"
                                        title="Select the time when watering should start"
                                    />
                                </div>
                                <p className="text-xs text-gray-500 mt-1">Choose when you want the watering to begin (24-hour format)</p>
                            </div>
                            
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-[#64748b] mb-2">Watering Duration (minutes)</label>
                                <div className="flex items-center gap-3">
                                    <button
                                        type="button"
                                        className="px-3 py-2 rounded border border-gray-300 bg-white text-lg font-bold hover:bg-gray-100"
                                        onClick={() => setNewScheduleDuration(Math.max(1, newScheduleDuration - 1))}
                                        disabled={newScheduleDuration <= 1}
                                        title="Decrease watering duration"
                                    >-</button>
                                    <span className="w-12 text-center text-lg">{newScheduleDuration}</span>
                                    <button
                                        type="button"
                                        className="px-3 py-2 rounded border border-gray-300 bg-white text-lg font-bold hover:bg-gray-100"
                                        onClick={() => setNewScheduleDuration(Math.min(5, newScheduleDuration + 1))}
                                        disabled={newScheduleDuration >= 5}
                                        title="Increase watering duration"
                                    >+</button>
                                </div>
                                <p className="text-xs text-gray-500 mt-1">How long the watering should run (1-5 minutes recommended)</p>
                            </div>
                            
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-[#64748b] mb-2">Active Days of the Week</label>
                                <p className="text-xs text-gray-500 mb-2">Select which days this schedule should run</p>
                                <div className="flex flex-wrap gap-2">
                                    {DAYS.map((d, i) => (
                                        <button
                                            key={d}
                                            type="button"
                                            className={`px-3 py-2 rounded text-sm font-medium border transition-colors duration-150 ${
                                                selectedDays.includes(i)
                                                    ? 'bg-blue-100 text-blue-700 border-blue-300'
                                                    : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100'
                                            }`}
                                            onClick={() => setSelectedDays(selectedDays.includes(i) ? selectedDays.filter(day => day !== i) : [...selectedDays, i])}
                                            disabled={scheduleLoading}
                                            title={`${selectedDays.includes(i) ? 'Remove' : 'Add'} ${d} to schedule`}
                                        >
                                            {d}
                                        </button>
                                    ))}
                                </div>
                                <p className="text-xs text-gray-500 mt-1">At least one day must be selected</p>
                            </div>

                            {/* Conflict Display */}
                            {checkingConflicts && (
                                <div className="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <div className="flex items-center gap-2 text-blue-700">
                                        <svg className="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span className="text-sm">Checking for conflicts...</span>
                                    </div>
                                </div>
                            )}

                            {scheduleConflicts.length > 0 && (
                                <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                    <div className="flex items-start gap-2">
                                        <svg className="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                                        </svg>
                                        <div>
                                            <h4 className="text-sm font-medium text-red-800 mb-1">Schedule Conflicts Detected</h4>
                                            <p className="text-sm text-red-700 mb-2">This schedule conflicts with the following existing schedules:</p>
                                            <ul className="text-sm text-red-700 space-y-1">
                                                {scheduleConflicts.map((conflict, index) => (
                                                    <li key={index} className="flex items-center gap-2">
                                                        <span className="font-mono bg-red-100 px-2 py-1 rounded text-xs">
                                                            {conflict.time} ({conflict.duration} min)
                                                        </span>
                                                        <span className="text-xs">on {conflict.days}</span>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {!checkingConflicts && scheduleConflicts.length === 0 && newScheduleTime && selectedDays.length > 0 && (
                                <div className="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                                    <div className="flex items-center gap-2 text-green-700">
                                        <svg className="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                        <span className="text-sm font-medium">No conflicts detected</span>
                                    </div>
                                </div>
                            )}

                            <div className="flex justify-end gap-3 mt-6">
                                <button
                                    onClick={() => {
                                        setShowScheduleModal(false);
                                        setScheduleConflicts([]);
                                        setEditScheduleId(null);
                                        setNewScheduleTime('08:00');
                                        setNewScheduleDuration(3);
                                        setSelectedDays([0,1,2,3,4,5,6]);
                                    }}
                                    className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200"
                                    disabled={scheduleLoading}
                                >Cancel</button>
                                <button
                                    onClick={saveSchedule}
                                    className={`px-4 py-2 text-sm font-medium rounded-md ${
                                        scheduleConflicts.length > 0 || scheduleLoading
                                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                            : 'text-white bg-blue-600 hover:bg-blue-700'
                                    }`}
                                    disabled={scheduleLoading || scheduleConflicts.length > 0}
                                >
                                    {editScheduleId ? 'Save' : 'Add'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
} 