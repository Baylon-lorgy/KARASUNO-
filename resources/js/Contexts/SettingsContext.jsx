import { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const SettingsContext = createContext();

export const useSettings = () => {
    const context = useContext(SettingsContext);
    if (!context) {
        throw new Error('useSettings must be used within a SettingsProvider');
    }
    return context;
};

export const SettingsProvider = ({ children }) => {
    const [settings, setSettings] = useState({
        theme: 'light',
        notifications: {
            email: true,
            push: true,
            sound: true
        },
        dashboard: {
            autoRefresh: true,
            refreshInterval: 30000,
            compactMode: false
        },
        system: {
            language: 'en',
            timezone: 'UTC',
            dateFormat: 'MM/DD/YYYY'
        }
    });

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadSettings();
    }, []);

    const loadSettings = async () => {
        try {
            setLoading(true);
            setError(null);
            
            // load from localstorage first for fast ui
            const cached = localStorage.getItem('app_settings');
            if (cached) {
                try {
                    const parsed = JSON.parse(cached);
                    setSettings(prev => ({ ...prev, ...parsed }));
                } catch {}
            }
            
            // try loading from backend if available
            try {
                const response = await axios.get('/api/settings');
                if (response.data) {
                    setSettings(prevSettings => ({
                        ...prevSettings,
                        ...response.data
                    }));
                    localStorage.setItem('app_settings', JSON.stringify(response.data));
                }
            } catch (apiErr) {
                // silently fallback to localstorage when api endpoint is missing
            }
        } catch (err) {
            console.error('Error loading settings:', err);
            setError('Failed to load settings');
        } finally {
            setLoading(false);
        }
    };

    const updateSettings = async (newSettings) => {
        try {
            setError(null);
            
            // optimistic local update
            setSettings(prevSettings => ({
                ...prevSettings,
                ...newSettings
            }));
            localStorage.setItem('app_settings', JSON.stringify({ ...settings, ...newSettings }));
            
            // fire and forget to backend if exists
            try {
                await axios.post('/api/settings', newSettings);
            } catch {}
            
            return { success: true };
        } catch (err) {
            console.error('Error updating settings:', err);
            setError('Failed to update settings');
            return { success: false, error: err.message };
        }
    };

    const updateNotificationSettings = async (notificationSettings) => {
        try {
            const response = await axios.post('/api/notifications/settings', notificationSettings);
            
            if (response.data.success) {
                setSettings(prevSettings => ({
                    ...prevSettings,
                    notifications: {
                        ...prevSettings.notifications,
                        ...notificationSettings
                    }
                }));
            }
            
            return response.data;
        } catch (err) {
            console.error('Error updating notification settings:', err);
            return { success: false, error: err.message };
        }
    };

    const resetSettings = async () => {
        try {
            setError(null);
            
            // Reset to default settings
            const defaultSettings = {
                theme: 'light',
                notifications: {
                    email: true,
                    push: true,
                    sound: true
                },
                dashboard: {
                    autoRefresh: true,
                    refreshInterval: 30000,
                    compactMode: false
                },
                system: {
                    language: 'en',
                    timezone: 'UTC',
                    dateFormat: 'MM/DD/YYYY'
                }
            };
            
            await axios.post('/api/settings/reset');
            setSettings(defaultSettings);
            
            return { success: true };
        } catch (err) {
            console.error('Error resetting settings:', err);
            setError('Failed to reset settings');
            return { success: false, error: err.message };
        }
    };

    const value = {
        settings,
        loading,
        error,
        updateSettings,
        updateNotificationSettings,
        resetSettings,
        loadSettings
    };

    return (
        <SettingsContext.Provider value={value}>
            {children}
        </SettingsContext.Provider>
    );
};

export default SettingsContext; 