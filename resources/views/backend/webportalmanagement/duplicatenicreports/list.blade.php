@extends('layouts.vertical', ['pageTitle' => 'Duplicate NIC Complaints List'])

@section('css')
<!-- Add any theme-specific CSS here -->
@endsection

@section('content')

    <div class="mb-2 d-flex justify-content-between align-items-center">

        <h1 class="subheader-title">
            <span>{{ $mainTitle }}</span>
        </h1>

        <div>
            <a href="{{ route('nic-duplicate-records.nic-duplicate-record-list') }}" class="btn btn-sm btn-outline-info">
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
                                <th>National Id Number</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th style="min-width: 80px;">View</th>
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
            ajax: "{{ route('nic-duplicate-records.nic-duplicate-record-list') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'national_id_number', name: 'national_id_number' },
                { data: 'email', name: 'email' },
                { data: 'contact_number', name: 'contact_number' },
                { data: 'view', name: 'view', orderable: false, searchable: false, className: 'text-center' },
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search duplicate NIC records..."
            },
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],          // -1 means "All"
                [10, 25, 50, 100, "All"]        // the labels shown in dropdown
            ],
            order: [[1, 'asc']]
        });

    });

</script>
@endsection
