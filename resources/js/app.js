import './bootstrap';
import "intl-tel-input/build/css/intlTelInput.css";
import intlTelInput from "intl-tel-input";

window.intlTelInput = intlTelInput;

// window.Echo is set up in echo.js (loaded through bootstrap.js) when a
// Reverb key is configured.
if (window.Echo && window.Laravel?.userId) {
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
                // Built with text nodes: notification text is never parsed as HTML.
                const title = document.createElement('strong');
                title.textContent = notification.title ?? 'Notification';

                const message = document.createElement('div');
                message.className = 'text-muted text-sm';
                message.textContent = notification.message ?? '';

                item.append(title, message);

                const divider = document.createElement('div');
                divider.className = 'dropdown-divider';

                dropdown.prepend(divider);
                dropdown.prepend(item);
            }
        });
}
