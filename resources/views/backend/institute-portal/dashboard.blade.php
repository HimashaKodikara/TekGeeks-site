@extends('layouts.vertical', ['pageTitle' => 'Dashboard'])

@section('css')
<!-- Add any theme-specific CSS here -->
@endsection

@section('content')
    <div class="subheader">
        <h1 class="subheader-title">
            <i class="fal fa-users-cog text-primary"></i> Institute Management
            <small>Review submitted decleration</small>
        </h1>
    </div>

    <div class="panel" id="panel-public-authorities-user">
        <div class="panel-hdr bg-primary-800 bg-success-gradient">
            <h2>
                Submitted  <span class="fw-300">Declarations</span>
            </h2>
            <div class="panel-toolbar">
                <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            </div>
        </div>
        <div class="panel-container show">
            <div class="panel-content">
                <table id="public-authorities-user-table" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-900 text-white">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Institute</th>
                            <th>Total Registrants</th>
                            <th class="text-center">Annual Declaration</th>
                            <th class="text-center">Other Declarations</th>
                            <th style="width: 100px;" class="text-center">Actions</th>
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
        var table = $('#public-authorities-user-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('institute.get-ajax-designations-user') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'publicAuthorityName', name: 'publicAuthorityName', orderable: true, searchable: true },
                { data: 'instituteCount', name: 'instituteCount', orderable: false },
                { data: 'annualStatus', name: 'annualStatus', className: 'text-center', orderable: false },
                { data: 'otherStatus', name: 'otherStatus', className: 'text-center', orderable: false },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[1, 'asc']],
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                { extend: 'pdfHtml5', text: 'PDF', titleAttr: 'Generate PDF', className: 'btn-outline-danger btn-sm mr-1' },
                { extend: 'excelHtml5', text: 'Excel', titleAttr: 'Generate Excel', className: 'btn-outline-success btn-sm mr-1' }
            ]
        });

        table.on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });
</script>
@endsection
