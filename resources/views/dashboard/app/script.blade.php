<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script nonce="{{ Vite::cspNonce() }}" src="{{ versioned_asset('js/confirm-actions.js') }}"></script>

<!-- jQuery UI 1.11.4 -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script nonce="{{ Vite::cspNonce() }}">
  $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- Bootstrap 4 -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- ChartJS -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/chart.js/Chart.min.js') }}"></script>

<!-- Sparkline -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/sparklines/sparkline.js') }}"></script>

<!-- JQVMap -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>

<!-- jQuery Knob Chart -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/jquery-knob/jquery.knob.min.js') }}"></script>

<!-- daterangepicker -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/moment/moment.min.js') }}"></script>
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/daterangepicker/daterangepicker.js') }}"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<!-- Summernote -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/summernote/summernote-bs4.min.js') }}"></script>

<!-- overlayScrollbars -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

<!-- AdminLTE App -->
<script nonce="{{ Vite::cspNonce() }}" src="{{ asset('AdminLTE/dist/js/adminlte.js') }}"></script>

@auth
    <script nonce="{{ Vite::cspNonce() }}">
        window.CS_LIVE_NOTIFICATIONS = {
            url: @json(route('dashboard.notifications.feed')),
            indexUrl: @json(route('dashboard.notifications.index')),
            since: {{ now()->getTimestamp() }},
            interval: 15000,
            closeLabel: @json(__('Close')),
        };
    </script>
    <script nonce="{{ Vite::cspNonce() }}" src="{{ versioned_asset('js/dashboard/live-notifications.js') }}" defer></script>
@endauth

<script nonce="{{ Vite::cspNonce() }}">
    // OverlayScrollbars miscalculates its scroll area in RTL (sidebar content
    // shifted, logo cut off, stray horizontal scrollbar). In Arabic the
    // sidebar uses native scrolling instead; see dashboard-rtl.css.
    (function ($) {
        if (document.documentElement.dir !== 'rtl') return;

        function useNativeSidebarScroll() {
            var $sidebar = $('.main-sidebar .sidebar');
            if (!$sidebar.length || !$.fn.overlayScrollbars) return;
            var instance = $sidebar.overlayScrollbars();
            if (instance) instance.destroy();
        }

        $(window).on('load', function () { setTimeout(useNativeSidebarScroll, 0); });
        $(useNativeSidebarScroll);
    })(jQuery);
</script>
