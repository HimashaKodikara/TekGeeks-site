@extends('layouts.vertical', ['pageTitle' => 'Dashboard'])

@section('css')
<style>
    .nav-tabs .nav-link { border-radius: 8px 8px 0 0; font-weight: 600; color: #6c757d; }
    .nav-tabs .nav-link.active { color: #4e73df; border-bottom-color: #fff; }
</style>
@endsection

@section('content')
    <div class="subheader">
        <h1 class="subheader-title">
            <i class="fal fa-users-cog text-primary"></i> Dashboard
            <small>Review submitted declarations</small>
        </h1>
    </div>

    <ul class="nav nav-tabs border-bottom mb-0" id="adminDashTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-all-institutes-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-all-institutes"
                    type="button" role="tab">
                <i class="fal fa-university me-1"></i> All Institutes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-institute-mgmt-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-institute-mgmt"
                    type="button" role="tab">
                <i class="fal fa-building me-1"></i> Institute Management
            </button>
        </li>
    </ul>

    <div class="tab-content bg-white border border-top-0 rounded-bottom shadow-sm" id="adminDashTabsContent">

        {{-- Tab 1: All Institutes (existing) --}}
        <div class="tab-pane fade show active" id="tab-all-institutes" role="tabpanel">
            <div class="p-3">
                <table id="public-authorities-user-table" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-900 text-white">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Institute</th>
                            <th>Total Registrants</th>
                            <th class="text-center">Annual Declaration</th>
                            <th class="text-center">Other Declarations</th>
                            <th class="text-center">File Uploads / Found / Not Found</th>
                            <th style="width:100px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- Tab 2: Institute Management (designation class-wise from monetary_institute_details) --}}
        <div class="tab-pane fade" id="tab-institute-mgmt" role="tabpanel">
            <div class="p-3">
                <p class="text-muted small mb-3">
                    Designation class-wise view based on monetary institute assignments.
                    Only designations assigned in the monetary institute details are shown.
                </p>
                <table id="institute-mgmt-table" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-900 text-white">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Monetary Institute</th>
                            <th>Designation Class</th>
                            <th class="text-center">Annual Declaration</th>
                            <th class="text-center">Other Declarations</th>
                            <th style="width:80px;" class="text-center">Actions</th>
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
        var table1 = $('#public-authorities-user-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin-institute.get-ajax-designations-user') }}",
            columns: [
                { data: 'DT_RowIndex',        name: 'id',                   orderable: false, searchable: false },
                { data: 'publicAuthorityName', name: 'publicAuthorityName',  orderable: true,  searchable: true },
                { data: 'instituteCount',      name: 'instituteCount',       orderable: false },
                { data: 'annualStatus',        name: 'annualStatus',         className: 'text-center', orderable: false },
                { data: 'otherStatus',         name: 'otherStatus',          className: 'text-center', orderable: false },
                { data: 'uploadCount',         name: 'uploadCount',          className: 'text-center', orderable: false },
                { data: 'edit',                name: 'edit',                 orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[1, 'asc']],
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                { extend: 'pdfHtml5',   text: 'PDF',   titleAttr: 'Generate PDF',   className: 'btn-outline-danger btn-sm mr-1' },
                { extend: 'excelHtml5', text: 'Excel', titleAttr: 'Generate Excel', className: 'btn-outline-success btn-sm mr-1' }
            ]
        });

        table1.on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        var table2 = null;

        document.getElementById('tab-institute-mgmt-btn').addEventListener('shown.bs.tab', function () {
            if (!table2) {
                table2 = $('#institute-mgmt-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin-institute.get-ajax-institute-management') }}",
                    columns: [
                        { data: 'DT_RowIndex',             name: 'id',                     orderable: false, searchable: false },
                        { data: 'monetary_institute_name', name: 'monetary_institute_name', orderable: true,  searchable: true },
                        { data: 'designation_class_name',  name: 'designation_class_name', orderable: true,  searchable: true },
                        { data: 'annual_declarations',     name: 'annual_declarations',    className: 'text-center', orderable: false, searchable: false },
                        { data: 'other_declarations',      name: 'other_declarations',     className: 'text-center', orderable: false, searchable: false },
                        { data: 'action',                  name: 'action',                 orderable: false, searchable: false, className: 'text-center' },
                    ],
                    order: [[1, 'asc']],
                    dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                         "<'row'<'col-sm-12'tr>>" +
                         "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        { extend: 'pdfHtml5',   text: 'PDF',   titleAttr: 'Generate PDF',   className: 'btn-outline-danger btn-sm mr-1' },
                        { extend: 'excelHtml5', text: 'Excel', titleAttr: 'Generate Excel', className: 'btn-outline-success btn-sm mr-1' }
                    ]
                });
            } else {
                table2.columns.adjust().responsive.recalc();
            }
        });
    });
</script>
@endsection
