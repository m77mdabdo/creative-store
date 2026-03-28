import './bootstrap';
import "intl-tel-input/build/css/intlTelInput.css";
import intlTelInput from "intl-tel-input";

window.intlTelInput = intlTelInput;

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

if (window.Laravel?.userId) {
    window.Echo.private(`users.${window.Laravel.userId}`)
        .notification((notification) => {
            const countEl = document.getElementById('notif-count');
            const dropdown = document.getElementById('notif-dropdown');

            if (countEl) {
                const current = parseInt(countEl.textContent || '0', 10);
                countEl.textContent = current + 1;
            }

            if (dropdown) {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'dropdown-item';
                item.innerHTML = `
                    <strong>${notification.title ?? 'Notification'}</strong>
                    <div class="text-muted text-sm">${notification.message ?? ''}</div>
                `;

                const divider = document.createElement('div');
                divider.className = 'dropdown-divider';

                dropdown.prepend(divider);
                dropdown.prepend(item);
            }
        });
}
