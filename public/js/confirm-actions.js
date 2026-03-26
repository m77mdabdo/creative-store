/*
 * Confirmation prompts without inline event handlers (the Content-Security-
 * Policy blocks onclick/onsubmit attributes). Put data-confirm="Question?"
 * on a form, or on the button or link that should ask before it acts.
 */
(function () {
    'use strict';

    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (form instanceof HTMLFormElement && form.hasAttribute('data-confirm')
            && ! window.confirm(form.getAttribute('data-confirm'))) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);

    document.addEventListener('click', function (event) {
        var trigger = event.target instanceof Element
            ? event.target.closest('[data-confirm]:not(form)')
            : null;

        if (trigger && ! window.confirm(trigger.getAttribute('data-confirm'))) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);
})();
