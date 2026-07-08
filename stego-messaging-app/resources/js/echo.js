import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'df2da99b67cc59faf863', // 🌟 Hardcoded directly so Docker build can't miss it
    cluster: 'mt1',
    forceTLS: true
});
