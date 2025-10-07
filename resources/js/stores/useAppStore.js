import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import axios from 'axios';

const useAppStore = create(
    persist(
        (set, get) => ({
            // Notifications
            notifications: [],
            showNotificationBadge: false,
            isLoadingNotifications: false,

            // Watering Status
            wateringStatus: {
                active: false,
                remainingTime: 0,
                duration: 0,
                startTime: null
            },

            // Sensor Data
            sensorData: {
                soil_moisture: null,
                temperature: null,
                humidity: null,
                water_level: null,
                lastUpdated: null
            },

            // System Settings
            settings: {
                thresholds: {
                    soil_moisture: 30,
                    temperature: 35,
                    humidity: 40
                },
                autoWatering: true,
                notifications: true
            },

            // UI State
            ui: {
                sidebarCollapsed: false,
                currentPage: 'dashboard',
                loadingStates: {}
            },

            // Actions
            addNotification: (notification) => {
                const newNotification = {
                    ...notification,
                    id: Date.now(),
                    time: new Date().toLocaleString(),
                    read: false
                };
                
                set((state) => ({
                    notifications: [newNotification, ...state.notifications.slice(0, 9)], // Keep only latest 10
                    showNotificationBadge: true
                }));
            },

            markNotificationAsRead: (notificationId) => {
                set((state) => ({
                    notifications: state.notifications.map(notification =>
                        notification.id === notificationId
                            ? { ...notification, read: true }
                            : notification
                    )
                }));
            },

            markAllNotificationsAsRead: () => {
                set((state) => ({
                    notifications: state.notifications.map(notification => ({
                        ...notification,
                        read: true
                    }))
                }));
            },

            deleteNotification: (notificationId) => {
                set((state) => ({
                    notifications: state.notifications.filter(
                        notification => notification.id !== notificationId
                    )
                }));
            },

            clearNotificationBadge: () => {
                set({ showNotificationBadge: false });
            },

            setWateringStatus: (status) => {
                set({ wateringStatus: { ...get().wateringStatus, ...status } });
            },

            updateSensorData: (data) => {
                set((state) => ({
                    sensorData: {
                        ...state.sensorData,
                        ...data,
                        lastUpdated: new Date().toISOString()
                    }
                }));
            },

            updateSettings: (newSettings) => {
                set((state) => ({
                    settings: {
                        ...state.settings,
                        ...newSettings
                    }
                }));
            },

            setUIState: (key, value) => {
                set((state) => ({
                    ui: {
                        ...state.ui,
                        [key]: value
                    }
                }));
            },

            setLoadingState: (key, isLoading) => {
                set((state) => ({
                    ui: {
                        ...state.ui,
                        loadingStates: {
                            ...state.ui.loadingStates,
                            [key]: isLoading
                        }
                    }
                }));
            },

            // API Actions
            fetchNotifications: async () => {
                set({ isLoadingNotifications: true });
                try {
                    const response = await axios.get('/api/system-notifications');
                    const formattedNotifications = response.data.map(notification => ({
                        ...notification,
                        time: new Date(notification.created_at?.$date || notification.created_at).toLocaleString()
                    }));
                    set({ 
                        notifications: formattedNotifications,
                        showNotificationBadge: formattedNotifications.length > 0,
                        isLoadingNotifications: false
                    });
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                    set({ isLoadingNotifications: false });
                }
            },

            fetchSensorData: async () => {
                try {
                    const response = await axios.get('/api/sensor-data');
                    get().updateSensorData(response.data);
                } catch (error) {
                    console.error('Error fetching sensor data:', error);
                }
            },

            fetchWateringStatus: async () => {
                try {
                    const response = await axios.get('/api/watering-status');
                    get().setWateringStatus(response.data);
                } catch (error) {
                    console.error('Error fetching watering status:', error);
                }
            },

            // Reset store (for logout)
            resetStore: () => {
                set({
                    notifications: [],
                    showNotificationBadge: false,
                    isLoadingNotifications: false,
                    wateringStatus: {
                        active: false,
                        remainingTime: 0,
                        duration: 0,
                        startTime: null
                    },
                    sensorData: {
                        soil_moisture: null,
                        temperature: null,
                        humidity: null,
                        water_level: null,
                        lastUpdated: null
                    },
                    settings: {
                        thresholds: {
                            soil_moisture: 30,
                            temperature: 35,
                            humidity: 40
                        },
                        autoWatering: true,
                        notifications: true
                    },
                    ui: {
                        sidebarCollapsed: false,
                        currentPage: 'dashboard',
                        loadingStates: {}
                    }
                });
            }
        }),
        {
            name: 'rainbasin-store',
            partialize: (state) => ({
                settings: state.settings,
                ui: state.ui
            })
        }
    )
);

export default useAppStore; 