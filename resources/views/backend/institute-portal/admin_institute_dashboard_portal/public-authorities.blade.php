@extends('layouts.vertical', ['pageTitle' => 'Public Authorities'])

@section('css')
@endsection

@section('content')
    <div class="subheader">
        <h1 class="subheader-title">
            <i class="fal fa-university text-primary"></i> Public Authorities
            <small>All registered public authorities</small>
        </h1>
    </div>

    <div class="panel" id="panel-public-authorities">
        <div class="panel-hdr bg-primary-800 bg-success-gradient">
            <h2>
                Public Authorities <span class="fw-300">List</span>
            </h2>
            <div class="panel-toolbar">
                <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            </div>
        </div>
        <div class="panel-container show">
            <div class="panel-content">
                <table id="public-authorities-table" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-900 text-white">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Name (English)</th>
                            <th>Name (Sinhala)</th>
                            <th>Name (Tamil)</th>
                            <th class="text-center" style="width:100px;">Status</th>
                            <th class="text-center" style="width:100px;">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        var table = $('#public-authorities-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin-institute.get-ajax-public-authorities') }}",
            columns: [
                { data: 'DT_RowIndex',   name: 'id',          orderable: false, searchable: false },
                { data: 'name_en',       name: 'name_en' },
                { data: 'name_si',       name: 'name_si' },
                { data: 'name_ta',       name: 'name_ta' },
                { data: 'status_badge',  name: 'status',       className: 'text-center', orderable: false, searchable: false },
                { data: 'action',        name: 'action',       className: 'text-center', orderable: false, searchable: false },
            ],
            order: [[1, 'asc']],
            pageLength: 25,
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                { extend: 'pdfHtml5',   text: 'PDF',   titleAttr: 'Generate PDF',   className: 'btn-outline-danger btn-sm mr-1' },
                { extend: 'excelHtml5', text: 'Excel', titleAttr: 'Generate Excel', className: 'btn-outline-success btn-sm mr-1' }
            ],
            language: {
                search: '_INPUT_',
                searchPlaceholder: 'Search public authorities...'
            }
        });

        table.on('draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });
</script>
@endsection
