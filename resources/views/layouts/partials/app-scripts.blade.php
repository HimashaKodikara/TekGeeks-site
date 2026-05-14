<!-- jQuery (must load before DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Bootstrap 5 DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>


<!-- Parsley (must be after jQuery; use existing 2.9.2) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/i18n/en.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>window.Parsley && window.Parsley.setLocale('en');</script>

<script>
    $(document).ready(function() {
        // Fade out alert after 3 seconds
        setTimeout(function() {
            $(".alert").fadeOut('slow');
        }, 7000);
    });
</script>


<!-- Then your Vite bundle (no jQuery/Parsley inside) -->
@vite(['resources/js/app.js'])


<!-- Core scripts -->
@vite([ 'resources/scripts/core/smartNavigation.js',
        'resources/scripts/core/smartFilter.js',
        'resources/scripts/core/smartSlimscroll.js'])

<!-- App.js -->
@vite(['resources/scripts/core/smartApp.js'])

@yield('scripts')
