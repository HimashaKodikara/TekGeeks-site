@yield('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css">

<!-- SweetAlert for activation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- Vendor css -->
@vite(['node_modules/node-waves/dist/waves.min.css'])
@vite(['resources/webfonts/fontawesome/fontawesome.scss', 'resources/webfonts/smartadmin/sa-icons.scss'])
@vite(['resources/sass/smartapp.scss'])

<!-- Save/Load functionality JavaScript -->
@vite(['resources/scripts/core/saveloadscript.js'])

{{-- @vite(['resources/scripts/pages/smarttableminimal.js']) --}}
<!-- jQuery (if not already in smarttableminimal.js) -->

<style>
    /* Custom styles for the users list page */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #ccc;
        padding: 5px 10px;
    }

    .dataTables_length select {
        border-radius: 8px;
    }

    /* Make Parsley error messages red and cleaner */
    .parsley-errors-list {
        margin: 4px 0 0;
        padding: 0;
        list-style: none;
        color: #dc3545; /* Bootstrap red */
        font-size: 0.875rem; /* slightly smaller */
    }

    .parsley-errors-list.filled {
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    .parsley-error {
        border-color: #dc3545 !important;
    }

    /* wrapper must be relative so the toggle is placed against this box */
    .password-wrapper { position: relative; }

    /* reserve room inside the input for the icon */
    .password-wrapper > .form-control {
    padding-right: 2.25rem;                /* space for the button */
    }

    /* place the button neatly on the right, vertically centered */
    .password-wrapper > .toggle-password {
    position: absolute;
    top: 50%;
    right: .625rem;                         /* adjust to taste (10px) */
    transform: translateY(-50%);
    line-height: 1;                         /* avoid vertical wobble */
    width: 1.75rem;                         /* larger click target */
    height: 1.75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;                         /* muted by default */
    }

    /* focus/hover states */
    .password-wrapper > .toggle-password:hover,
    .password-wrapper > .toggle-password:focus {
    color: #495057;
    outline: none;
    }

    /* keep the icon from shifting the input height on some themes */
    .password-wrapper > .toggle-password i {
    pointer-events: none;
    font-size: 1rem;
    }



</style>
