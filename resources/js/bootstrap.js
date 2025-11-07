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

document.addEventListener("DOMContentLoaded", () => {
    const bell = document.getElementById('notifBell');
    const dropdown = document.getElementById('notifDropdown');
    const notifCount = document.getElementById('notifCount');
    const notifList = document.getElementById('notificationList');
    const bellIcon = document.getElementById('bellIcon');

    // 🟢 Real-time notification listener (keep working logic)
    window.Echo.channel('notifications')
        .listen('.new-notification', (e) => {
            if (notifList) {
                const li = document.createElement('li');
                li.className = "px-3 py-2 border-b";
                li.innerHTML = `
                    <span class="font-medium text-gray-800">${e.message}</span>
                    <span class="text-gray-500 text-xs float-right">just now</span>
                `;
                notifList.prepend(li);
            }

            // 🔔 Update count
            let currentCount = parseInt(notifCount.innerText) || 0;
            notifCount.innerText = currentCount + 1;
            notifCount.classList.remove('hidden');

            // ✅ Switch to active yellow bell + shake animation
            if (bellIcon) {
                bellIcon.classList.remove('fa-bell-slash', 'text-gray-400');
                bellIcon.classList.add('fa-bell', 'text-yellow-500', 'bell-shake');
                //setTimeout(() => bellIcon.classList.remove('bell-shake'), 1500);
            }
            
            setTimeout(() => bellIcon.classList.add('bell-shake') , 500);
        });

    // 🟠 Bell click — open/close dropdown & mark as read when opened
    if (bell && dropdown) {
        bell.addEventListener('click', async (e) => {
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');

            // Toggle visibility properly
            //dropdown.classList.toggle('hidden', !isHidden);
            dropdown.classList.remove('hidden');
            
            notifCount.classList.add('hidden');
            
            //bellIcon.classList.remove('fa-bell', 'text-yellow-500');
            //bellIcon.classList.add('fa-bell-slash', 'text-gray-400');

            // Only mark as read when opening (not closing)
            if (isHidden) {
                try {
                    const response = await fetch("{{ route('notifications.markRead') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        // ✅ Clear count but do NOT show "0"
                        notifCount.innerText = "";
                        /*notifCount.classList.add('hidden');*/

                        // ✅ Change bell to gray only if dropdown opened successfully
                        /*if (bellIcon) {
                            bellIcon.classList.remove('fa-bell', 'text-yellow-500');
                            bellIcon.classList.add('fa-bell-slash', 'text-gray-400');
                        }*/
                    }
                } catch (err) {
                    console.error("Error marking notifications as read:", err);
                }
            }
        });

        // 🟣 Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
});