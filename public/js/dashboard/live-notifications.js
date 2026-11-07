/*
 * Live dashboard notifications.
 *
 * Polls the feed while the tab is visible (and right away when it comes
 * back into view), keeps the bell, its list and the order/message counters
 * current, and pops a toast with a soft chime for anything new.
 */
(function () {
    'use strict';

    var config = window.CS_LIVE_NOTIFICATIONS;
    if (!config || !config.url || !window.fetch) return;

    var INTERVAL = config.interval || 15000;
    var since = config.since || Math.floor(Date.now() / 1000);
    var seen = {};
    var timer = null;
    var busy = false;
    var lastPoll = 0;
    var baseTitle = document.title;

    function $(id) { return document.getElementById(id); }

    function setBadge(el, count) {
        if (!el) return;
        el.textContent = count > 99 ? '99+' : String(count);
        el.hidden = count <= 0;
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function toastStack() {
        var stack = $('cs-toast-stack');
        if (!stack) {
            stack = document.createElement('div');
            stack.id = 'cs-toast-stack';
            stack.className = 'cs-toast-stack';
            stack.setAttribute('aria-live', 'polite');
            document.body.appendChild(stack);
        }
        return stack;
    }

    function showToast(item) {
        var toast = document.createElement('a');
        toast.className = 'cs-toast';
        toast.href = item.url || config.indexUrl;
        toast.innerHTML =
            '<span class="cs-toast-icon bg-' + escapeHtml(item.tone || 'secondary') + '"><i class="fas ' + escapeHtml(item.icon || 'fa-bell') + '" aria-hidden="true"></i></span>' +
            '<span class="cs-toast-body"><strong>' + escapeHtml(item.title) + '</strong>' +
            (item.message ? '<span>' + escapeHtml(item.message) + '</span>' : '') + '</span>' +
            '<button type="button" class="cs-toast-close" aria-label="' + escapeHtml(config.closeLabel || 'Close') + '">&times;</button>';

        toast.querySelector('.cs-toast-close').addEventListener('click', function (event) {
            event.preventDefault();
            dismiss(toast);
        });

        toastStack().appendChild(toast);
        requestAnimationFrame(function () { toast.classList.add('is-visible'); });
        setTimeout(function () { dismiss(toast); }, 8000);
    }

    function dismiss(toast) {
        toast.classList.remove('is-visible');
        setTimeout(function () { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
    }

    function chime() {
        try {
            var AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            var ctx = chime.ctx || (chime.ctx = new AudioContext());
            if (ctx.state === 'suspended') return; // the browser has not allowed sound yet
            [880, 1320].forEach(function (frequency, index) {
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                var start = ctx.currentTime + index * 0.12;
                osc.type = 'sine';
                osc.frequency.value = frequency;
                gain.gain.setValueAtTime(0.0001, start);
                gain.gain.exponentialRampToValueAtTime(0.08, start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, start + 0.35);
                osc.connect(gain).connect(ctx.destination);
                osc.start(start);
                osc.stop(start + 0.4);
            });
        } catch (e) { /* sound is optional */ }
    }

    // Browsers only allow audio after the first interaction with the page.
    document.addEventListener('click', function unlock() {
        try {
            var AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                chime.ctx = chime.ctx || new AudioContext();
                if (chime.ctx.state === 'suspended') chime.ctx.resume();
            }
        } catch (e) { /* ignore */ }
        document.removeEventListener('click', unlock);
    });

    function ring() {
        var bell = $('notif-bell-icon');
        if (!bell) return;
        bell.classList.remove('cs-bell-ring');
        void bell.offsetWidth;
        bell.classList.add('cs-bell-ring');
    }

    function apply(data) {
        setBadge($('notif-count'), data.unread);
        setBadge($('nav-orders-count'), data.pending_orders);
        setBadge($('nav-messages-count'), data.unread_messages);

        var label = $('notif-new-count');
        if (label) label.textContent = data.unread_label;

        var readAll = $('notif-read-all');
        if (readAll) readAll.hidden = data.unread <= 0;

        var list = $('notif-list');
        var menuOpen = list && list.closest('.dropdown-menu') && list.closest('.dropdown-menu').classList.contains('show');
        if (list && typeof data.html === 'string' && !menuOpen) list.innerHTML = data.html;

        var fresh = (data.fresh || []).filter(function (item) {
            if (seen[item.id]) return false;
            seen[item.id] = true;
            return true;
        });

        if (fresh.length) {
            fresh.slice(0, 3).reverse().forEach(showToast);
            ring();
            chime();
            if (document.hidden && data.unread > 0) document.title = '(' + data.unread + ') ' + baseTitle;
        }

        if (data.latest) since = Math.max(since, data.latest);
    }

    function poll() {
        // Background tabs check in once a minute so the tab title still shows new items.
        if (busy || (document.hidden && Date.now() - lastPoll < 60000)) return;
        busy = true;
        lastPoll = Date.now();

        fetch(config.url + '?since=' + encodeURIComponent(since), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (response.status === 401 || response.status === 419) { stop(); return null; }
                return response.ok ? response.json() : null;
            })
            .then(function (data) { if (data) apply(data); })
            .catch(function () { /* offline: try again on the next tick */ })
            .then(function () { busy = false; });
    }

    function start() {
        if (!timer) timer = setInterval(poll, INTERVAL);
    }

    function stop() {
        clearInterval(timer);
        timer = null;
    }

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) return;
        document.title = baseTitle;
        lastPoll = 0;
        poll();
    });

    start();
})();
