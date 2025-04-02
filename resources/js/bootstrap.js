import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Initialize Pusher and Echo
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '4ec8cc2390a0852e67a0',
    cluster: 'ap1',
    forceTLS: true
});
