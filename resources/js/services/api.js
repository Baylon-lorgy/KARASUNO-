import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    },
    withCredentials: true
});

// Function to get CSRF token
const getCSRFToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

// Function to get auth token
const getAuthToken = () => {
    return localStorage.getItem('auth_token');
};

// Function to initialize CSRF protection
const initCSRF = async () => {
    try {
        await axios.get('/sanctum/csrf-cookie');
    } catch (error) {
        console.error('Failed to get CSRF cookie:', error);
    }
};

// Add request interceptor for CSRF token and auth token
api.interceptors.request.use(async config => {
    // Get CSRF token
    const token = getCSRFToken();
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }

    // Get auth token
    const authToken = getAuthToken();
    if (authToken) {
        config.headers['Authorization'] = `Bearer ${authToken}`;
    }

    return config;
}, error => {
    return Promise.reject(error);
});

// Add response interceptor for handling auth errors
api.interceptors.response.use(
    response => response,
    async error => {
        if (error.response?.status === 401) {
            // Try to get a new CSRF token
            try {
                await initCSRF();
                // Retry the original request
                const retryConfig = error.config;
                // Update the CSRF token for the retry
                const newToken = getCSRFToken();
                if (newToken) {
                    retryConfig.headers['X-CSRF-TOKEN'] = newToken;
                }
                return api(retryConfig);
            } catch (retryError) {
                // If retry fails, redirect to login
                window.location.href = '/login';
                return Promise.reject(retryError);
            }
        }
        return Promise.reject(error);
    }
);

// Initialize CSRF protection
initCSRF();

export default api; 