@extends('layouts.vertical', ['pageTitle' => 'FAQ List'])

@section('css')
<!-- Add any theme-specific CSS here -->
@endsection

@section('content')

    <div class="mb-2 d-flex justify-content-between align-items-center">

        <h1 class="subheader-title">
            <span>{{ $mainTitle }}</span>
        </h1>

        <div>
            <a href="{{ route('faqs.faq-list') }}" class="btn btn-sm btn-outline-info">
                <i class="fa fa-list me-1"></i> View All
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show col-12" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show col-12" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>


    <div class="panel panel-default" id="panel-faq">
        <div class="panel-hdr">
            {{-- <h2>{{ $title }} <span class="fw-light"><i>List</i></span></h2> --}}
        </div>
        <div class="panel-container">
            <div class="panel-content table-responsive">
                <table id="faq-table" class="table table-striped table-hover table-bordered table-sm align-middle nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th style="min-width: 40px;">#</th>
                                <th>Heading (English)</th>
                                <th>Heading (Sinhala)</th>
                                <th>Heading (Tamil)</th>
                                <th>Display Order</th>
                                <th style="min-width: 80px;">Edit</th>
                                <th style="min-width: 80px;">Visibility Status</th>
                                <th style="min-width: 80px;">Activation</th>
                                {{-- <th style="min-width: 80px;">Delete</th> --}}
                            </tr>
                        </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

<script>
    $(document).ready(function() {

        $('#faq-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('faqs.faq-list') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'heading_en', name: 'heading_en' },
                { data: 'heading_si', name: 'heading_si' },
                { data: 'heading_ta', name: 'heading_ta' },
                { data: 'display_order', name: 'display_order' },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center' },
                { data: 'visibility_status', name: 'visibility_status', orderable: false, searchable: false, className: 'text-center' },
                { data: 'activation', name: 'activation', orderable: false, searchable: false, className: 'text-center' },
                // { data: 'delete', name: 'delete', orderable: false, searchable: false, className: 'text-center' },
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search faq..."
            },
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],          // -1 means "All"
                [10, 25, 50, 100, "All"]        // the labels shown in dropdown
            ],
            order: [[4, 'asc']]
        });

    });

</script>
@endsection
