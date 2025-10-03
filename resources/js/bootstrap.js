import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

// Make sure userId is defined in your Blade like:
// <script>window.userId = {{ auth()->user()->id }};</script>

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    forceTLS: true,
});

window.Echo.channel('notifications')
    .listen('.new-notification', (e) => {
        const list = document.getElementById('notificationList');
        const badge = document.getElementById('notifCount');

        if (list) {
            const li = document.createElement('li');
            li.className = "px-3 py-2 border-b";
            li.innerHTML = e.message +
                '<span class="text-gray-500 text-xs float-right">just now</span>';
            list.prepend(li);
        }

        if (badge) {
            badge.innerText = parseInt(badge.innerText) + 1;
        }
    });