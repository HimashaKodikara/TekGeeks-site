@extends('layouts.vertical', ['pageTitle' => 'Common Log'])

@section('content')

    <div class="mb-2 d-flex justify-content-between align-items-center">

        <h1 class="subheader-title">
            <span>Support Module</span>
        </h1>
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


    <div class="panel panel-default" id="panel-users">
        <div class="panel-hdr">
            <h2>Support Module <span class="fw-light"><i>List</i></span></h2>
        </div>
        <div class="panel-container">
            <div class="panel-content table-responsive">
                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <input type="text" id="filter-name" class="form-control form-control-sm" placeholder="Name">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="filter-nic" class="form-control form-control-sm" placeholder="NIC">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="filter-mobile" class="form-control form-control-sm" placeholder="Mobile">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="filter-email" class="form-control form-control-sm" placeholder="Email">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="filter-year" class="form-control form-control-sm" placeholder="Year">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="filter-passport" class="form-control form-control-sm" placeholder="Passport">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="custom-search" class="form-control" placeholder="Search by Name, NIC, or Email...">
                    </div>
                    <div class="col-6 d-flex gap-2">
                        <button type="button" id="btn-apply-filters" class="btn btn-sm btn-primary">Search</button>
                        <button type="button" id="btn-clear-filters" class="btn btn-sm btn-outline-secondary">Clear</button>
                    </div>
                </div>

                <table id="users-table" class="table table-striped table-hover table-bordered table-sm align-middle nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th style="min-width: 40px;">#</th>
                                <th>Name</th>
                                <th>Total Activities</th>
                                <th>Last Activity Type</th>
                                <th>Last Seen</th>
                                <th>View</th>
                            </tr>
                        </thead>
                </table>
            </div>
        </div>
    </div>

    <dialog class="modal fade" id="logModal" aria-labelledby="logModalTitle">
        <div class="modal-dialog modal-xl modal-left">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="logModalTitle">Activity Logs ({{ now()->year }})</h5>
                    <button type="button" class="close text-white" data-dialog-close aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-4 mb-3">
                            <div class="form-group">
                                <label class="form-label" style="min-width: 100px" for="example-textarea">Name</label>
                                <span>: &nbsp; <span id="log-user-name">-</span></span>
                    <button type="button" class="close text-white" data-dialog-close aria-label</span>
                            </div>
                        </div>

                        <div class="col-4 mb-3">
                            <div class="form-group">
                                <label class="form-label" style="min-width: 100px" for="example-textarea">NIC</label>
                                <span>: &nbsp; <span id="log-user-nic">-</span></span>
                            </div>
                        </div>
                        
                        <div class="col-4 mb-3">
                            <div class="form-group">
                                <label class="form-label" style="min-width: 100px" for="example-textarea">Mobile</label>
                                <span>: &nbsp; <span id="log-user-mobile">-</span></span>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="form-group">
                                <label class="form-label" style="min-width: 100px" for="example-textarea">Email</label>
                                <span>: &nbsp; <span id="log-user-email">-</span></span>
                            </div>
                        </div>

                    </div>
                    <hr class="mb-5 mt-2" style="border-bottom: 1px solid rgba(0,0,0,.5)">
                    <div class="table-responsive">
                        <table id="modalUserLogsTable" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action Type</th>
                                    <th>Description</th>
                                    <th>Page</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dialog-close>Close</button>
                </div>
            </div>
        </div>
    </dialog>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const logModal = document.getElementById('logModal');

        // Match previous static modal behavior: do not close on Esc.
        logModal.addEventListener('cancel', function (event) {
            event.preventDefault();
        });

        function openLogModal() {
            if (!logModal.open) {
                logModal.showModal();
            }
            logModal.classList.add('show');
        }

        function closeLogModal() {
            logModal.classList.remove('show');
            if (logModal.open) {
                logModal.close();
            }
        }

        const table = $('#users-table').DataTable({
            dom: 'lrtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('common-log.get-common-log') }}",
                data: function (d) {
                    d.nic = $('#filter-nic').val();
                    d.mobile = $('#filter-mobile').val();
                    d.email = $('#filter-email').val();
                    d.name = $('#filter-name').val();
                    d.year = $('#filter-year').val();
                    d.passport = $('#filter-passport').val();
                    d.search_all = $('#custom-search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
                { data: 'name', name: 'name', orderable: true, searchable: true },
                { data: 'total_activities', name: 'total_activities',orderable: true, searchable: true  },
                { data: 'last_activity_type', name: 'last_activity_type',orderable: true, searchable: true  },
                { data: 'last_seen', name: 'last_seen',orderable: true, searchable: true },
                { data: 'view', name: 'view',orderable: false, searchable: false  },
            ],
            order: [[4, 'desc']],
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            order: []
        });

        $('#btn-apply-filters').on('click', function () {
            table.draw();
        });

        $('#custom-search').on('keyup', function(e) {
            if (e.keyCode === 13) {
                table.draw();
            }
        });

        $('#btn-clear-filters').on('click', function () {
            $('#filter-nic').val('');
            $('#filter-mobile').val('');
            $('#filter-email').val('');
            $('#filter-name').val('');
            $('#filter-year').val('');
            $('#filter-passport').val('');
            $('#custom-search').val('');
            table.draw();
        });

        $('#filter-nic, #filter-mobile, #filter-email, #filter-name, #filter-year, #filter-passport, #custom-search').on('keydown', function (e) {
            if (e.key === 'Enter') {
                table.draw();
            }
        });

        $(document).on('click', '.view-logs', function() {
            var userId = $(this).data('id');
            var url = "{{ route('common-log.get-user-logs', ':id') }}".replace(':id', userId);

            openLogModal();
            // Reset user info before loading
            $('#log-user-name').text('-');
            $('#log-user-nic').text('-');
            $('#log-user-mobile').text('-');
            $('#log-user-email').text('-');

            if ($.fn.DataTable.isDataTable('#modalUserLogsTable')) {
                $('#modalUserLogsTable').DataTable().destroy();
                $('#modalUserLogsTable tbody').empty();
            }

            $('#modalUserLogsTable').DataTable({
                ajax: {
                    url: url,
                    type: 'GET',
                    dataSrc: function(json) {
                if (json.user) {
                    $('#log-user-name').text(
                        [
                            json.user.surname,
                            json.user.other_names
                        ].filter(Boolean).join(' ') || '-'
                    );

                    $('#log-user-nic').text(json.user.nic ?? '-');
                    $('#log-user-mobile').text(json.user.mobile_no ?? '-');
                    $('#log-user-email').text(json.user.email ?? '-');
                }

                return json.data || [];
            }
        },
                columns: [
                    {
                        data: 'created_at',
                        render: function(data) {
                            return moment(data).format('YYYY-MM-DD HH:mm:ss');
                        }
                    },
                    { data: 'activity_type' },
                    { data: 'description' },
                    { data: 'action_page' }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });
        });

        $(document).on('click', '[data-dialog-close]', function() {
            closeLogModal();
        });
    });
</script>
<style>
    #logModal {
        border: 0;
        max-width: 100%;
        padding: 0;
        background: transparent;
    }

    #logModal[open] {
        display: block;
    }

    #logModal::backdrop {
        background: rgba(0, 0, 0, 0.5);
    }
</style>
@endsection
