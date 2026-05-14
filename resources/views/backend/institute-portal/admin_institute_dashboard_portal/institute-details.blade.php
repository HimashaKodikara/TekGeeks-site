@extends('layouts.vertical', ['pageTitle' => 'Institute Details'])

@section('css')
<style>
    .designation-group-row {
        background-color: #f8f9fa !important;
        font-weight: 700;
        color: #4e73df;
        border-left: 4px solid #4e73df;
    }
    .upload-card { border-radius: 12px; border: 1px solid #e3e6f0; }
    .upload-card .upload-header {
        background: #f8f9fc;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #e3e6f0;
        padding: 14px 18px;
    }
    .uploader-avatar {
        width: 36px; height: 36px;
        background: #4e73df;
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem;
        flex-shrink: 0;
    }
    .nav-tabs .nav-link { border-radius: 8px 8px 0 0; font-weight: 600; color: #6c757d; }
    .nav-tabs .nav-link.active { color: #4e73df; border-bottom-color: #fff; }
    .email-search-wrapper { position: relative; }
    .email-search-wrapper .fa-search {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); color: #aaa; pointer-events: none;
    }
    .email-search-wrapper input { padding-left: 34px; }
    .stat-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 600;
    }
    .stat-pill.total   { background: #e8eaf6; color: #3949ab; }
    .stat-pill.found   { background: #e8f5e9; color: #2e7d32; }
    .stat-pill.missing { background: #fce4e4; color: #c62828; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- Breadcrumb / Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin-institute.index') }}" class="btn btn-sm btn-light rounded-pill me-2">
                <i class="fal fa-arrow-left me-1"></i> Back
            </a>
            <span class="h5 fw-bold text-gray-800 mb-0">
                {{ $institute->name_en ?? 'Institute' }}
            </span>
            <p class="text-muted small mb-0 mt-1">Admin view — declaration list &amp; upload history</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs border-bottom mb-0" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-users-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-users" type="button" role="tab">
                <i class="fal fa-layer-group me-1"></i> Designation Users
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-uploads-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-uploads" type="button" role="tab">
                <i class="fal fa-history me-1"></i> Upload History
                @if($uploads->count())
                    <span class="badge bg-primary rounded-pill ms-1" style="font-size:.7rem;">
                        {{ $uploads->count() }}
                    </span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content bg-white border border-top-0 rounded-bottom shadow-sm" id="adminTabsContent">

        {{-- ── Tab 1: Designation Users ──────────────────────────────── --}}
        <div class="tab-pane fade show active" id="tab-users" role="tabpanel">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <p class="text-muted small mb-0">All registered users grouped by designation</p>
                    <div class="email-search-wrapper">
                        <i class="fal fa-search"></i>
                        <input type="text" id="email-search"
                               class="form-control form-control-sm rounded-pill bg-light border-0"
                               placeholder="Search by email..." style="width:220px; padding-left:34px;">
                    </div>
                </div>

                @if($groupedData->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fal fa-users fa-3x mb-3 d-block"></i>
                        No registered users found for this institute.
                    </div>
                @else
                <div class="table-responsive">
                    <table id="dt-designation-users" class="table table-hover align-middle w-100">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th>Designation</th>
                                <th class="ps-3">User Details</th>
                                <th>Email</th>
                                <th class="text-center">Declaration Type</th>
                                <th class="text-center">Year Status</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedData as $designationName => $users)
                                @foreach($users as $user)
                                    @if($user->declarationStatuses->isEmpty())
                                        <tr>
                                            <td>{{ $designationName }}</td>
                                            <td class="ps-3">
                                                <div class="fw-bold">
                                                    {{ $user->personalInfo->full_name ?? $user->surname . ' ' . $user->other_names }}
                                                </div>
                                                <small class="text-muted font-monospace">{{ $user->nic }}</small>
                                            </td>
                                            <td>
                                                @if($user->email)
                                                    <a href="mailto:{{ $user->email }}" class="text-muted small">{{ $user->email }}</a>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center"><span class="text-muted small">—</span></td>
                                            <td class="text-center"><span class="badge rounded-pill bg-danger px-3">Not Started</span></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill px-3 py-2 bg-light text-muted border">
                                                    <i class="fal fa-times-circle me-1"></i> Not Started
                                                </span>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($user->declarationStatuses as $sod)
                                            <tr>
                                                <td>{{ $designationName }}</td>
                                                <td class="ps-3">
                                                    <div class="fw-bold">
                                                        {{ $user->personalInfo->full_name ?? $user->surname . ' ' . $user->other_names }}
                                                    </div>
                                                    <small class="text-muted font-monospace">{{ $user->nic }}</small>
                                                </td>
                                                <td>
                                                    @if($user->email)
                                                        <a href="mailto:{{ $user->email }}" class="text-muted small">{{ $user->email }}</a>
                                                    @else
                                                        <span class="text-muted small">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border px-2">{{ $sod->declarationType->type_name_en ?? '—' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($sod->status === 'C')
                                                        <span class="badge rounded-pill bg-success px-3">Completed</span>
                                                    @elseif($sod->status === 'S')
                                                        <span class="badge rounded-pill bg-warning text-dark px-3">
                                                            Started ({{ $sod->created_at->format('Y-m-d') }})
                                                        </span>
                                                    @elseif($sod->status === 'R')
                                                        <span class="badge rounded-pill bg-info px-3">Restarted</span>
                                                    @elseif($sod->status === 'E')
                                                        <span class="badge rounded-pill bg-primary px-3">Edit Approved</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-danger px-3">Not Started</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($sod->status === 'C')
                                                        <span class="badge rounded-pill px-3 py-2 bg-success text-white">
                                                            <i class="fal fa-check-circle me-1"></i>
                                                            {{ !empty($sod->recompleted_date) ? 'Re-completed' : 'Completed' }}
                                                            <small class="d-block" style="font-size:.75rem;">
                                                                @if(!empty($sod->recompleted_date))
                                                                    {{ \Carbon\Carbon::parse($sod->recompleted_date)->format('Y-m-d') }}
                                                                @elseif(!empty($sod->completed_date))
                                                                    {{ \Carbon\Carbon::parse($sod->completed_date)->format('Y-m-d') }}
                                                                @endif
                                                            </small>
                                                        </span>
                                                    @elseif($sod->status === 'S')
                                                        <span class="badge rounded-pill px-3 py-2 bg-warning text-dark">
                                                            <i class="fal fa-spinner-third fa-spin me-1"></i> Started
                                                        </span>
                                                    @elseif($sod->status === 'R')
                                                        <span class="badge rounded-pill px-3 py-2 bg-info text-white">Restarted</span>
                                                    @elseif($sod->status === 'E')
                                                        <span class="badge rounded-pill px-3 py-2 bg-primary text-white">Edit Approved</span>
                                                    @else
                                                        <span class="badge rounded-pill px-3 py-2 bg-light text-muted border">
                                                            <i class="fal fa-times-circle me-1"></i> Not Started
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Tab 2: Upload History ─────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-uploads" role="tabpanel">
            <div class="p-3">
                @if($uploads->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fal fa-cloud-upload fa-3x mb-3 d-block"></i>
                        No files have been uploaded for this institute yet.
                    </div>
                @else
                    <p class="text-muted small mb-3">
                        {{ $uploads->count() }} upload{{ $uploads->count() > 1 ? 's' : '' }} recorded for this institute.
                    </p>

                    @foreach($uploads as $upload)
                    <div class="upload-card mb-3">
                        {{-- Upload Header --}}
                        <div class="upload-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="uploader-avatar">
                                    {{ strtoupper(substr($upload->uploader->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:.92rem;">
                                        {{ $upload->uploader->name ?? 'Unknown User' }}
                                        @if($upload->declaration_year)
                                            <span class="badge rounded-pill ms-2 px-2 py-1" style="background:#e8eaf6;color:#3949ab;font-size:.72rem;">
                                                <i class="fal fa-calendar me-1"></i>{{ $upload->declaration_year }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        {{ $upload->uploader->email ?? '' }}
                                        &nbsp;&bull;&nbsp;
                                        {{ $upload->created_at->format('d M Y, H:i') }}
                                        <span class="text-muted">({{ $upload->created_at->diffForHumans() }})</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="stat-pill total">
                                    <i class="fal fa-list"></i> {{ $upload->total_count }} Total
                                </span>
                                <span class="stat-pill found">
                                    <i class="fal fa-check"></i> {{ $upload->found_count }} Found
                                </span>
                                <span class="stat-pill missing">
                                    <i class="fal fa-times"></i> {{ $upload->not_found_count }} Not Found
                                </span>
                            </div>
                        </div>

                        {{-- Found / Not Found sub-tabs --}}
                        <div class="p-3">
                            <ul class="nav nav-tabs nav-tabs-sm mb-0" id="uploadTabs{{ $upload->id }}" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 px-3 small"
                                            data-bs-toggle="tab"
                                            data-bs-target="#found-{{ $upload->id }}"
                                            type="button">
                                        <i class="fal fa-check-circle me-1 text-success"></i>
                                        Found
                                        <span class="badge bg-success rounded-pill ms-1" style="font-size:.65rem;">
                                            {{ $upload->found_count }}
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 small"
                                            data-bs-toggle="tab"
                                            data-bs-target="#notfound-{{ $upload->id }}"
                                            type="button">
                                        <i class="fal fa-times-circle me-1 text-danger"></i>
                                        Not Found
                                        <span class="badge bg-danger rounded-pill ms-1" style="font-size:.65rem;">
                                            {{ $upload->not_found_count }}
                                        </span>
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content border border-top-0 rounded-bottom p-0">

                                {{-- Found Users --}}
                                <div class="tab-pane fade show active p-2" id="found-{{ $upload->id }}">
                                    @if($upload->foundDetails->isEmpty())
                                        <p class="text-muted small text-center py-3 mb-0">No found users in this upload.</p>
                                    @else
                                    <div class="d-flex justify-content-end mb-2">
                                        <a href="{{ route('admin-institute.download-upload-report', [$upload->id, 'type' => 'found']) }}"
                                           class="btn btn-sm btn-success rounded-pill px-3">
                                            <i class="fal fa-file-excel me-1"></i> Download Found List
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                                <tr>
                                                    <th style="width:40px">#</th>
                                                    <th>NIC</th>
                                                    <th>Name (from file)</th>
                                                    <th>System Name</th>
                                                    <th>Email (file)</th>
                                                    <th>Email (system)</th>
                                                    <th>Designation</th>
                                                    <th class="text-center">Name Match</th>
                                                    <th class="text-center">Email Match</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($upload->foundDetails as $i => $row)
                                                <tr>
                                                    <td class="text-muted">{{ $i + 1 }}</td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border fw-bold font-monospace">
                                                            {{ $row->nic }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $row->uploaded_name ?? '—' }}</td>
                                                    <td class="fw-bold">{{ $row->system_name ?? '—' }}</td>
                                                    <td class="small text-muted">{{ $row->uploaded_email ?? '—' }}</td>
                                                    <td class="small">
                                                        @if($row->email && $row->email !== 'N/A')
                                                            <a href="mailto:{{ $row->email }}">{{ $row->email }}</a>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted small">{{ $row->designation ?? '—' }}</td>
                                                    <td class="text-center">
                                                        @if(is_null($row->name_match))
                                                            <span class="badge bg-secondary rounded-pill">No name</span>
                                                        @elseif($row->name_match)
                                                            <span class="badge bg-success rounded-pill">
                                                                <i class="fal fa-check me-1"></i> Match
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning text-dark rounded-pill">
                                                                <i class="fal fa-exclamation me-1"></i> Mismatch
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(is_null($row->email_match))
                                                            <span class="badge bg-secondary rounded-pill">No email</span>
                                                        @elseif($row->email_match)
                                                            <span class="badge bg-success rounded-pill">
                                                                <i class="fal fa-check me-1"></i> Match
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning text-dark rounded-pill">
                                                                <i class="fal fa-exclamation me-1"></i> Mismatch
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>

                                {{-- Not Found Users --}}
                                <div class="tab-pane fade p-2" id="notfound-{{ $upload->id }}">
                                    @if($upload->notFoundDetails->isEmpty())
                                        <p class="text-muted small text-center py-3 mb-0">All users were found in this upload.</p>
                                    @else
                                    <div class="d-flex justify-content-end mb-2">
                                        <a href="{{ route('admin-institute.download-upload-report', [$upload->id, 'type' => 'not_found']) }}"
                                           class="btn btn-sm btn-danger rounded-pill px-3">
                                            <i class="fal fa-file-excel me-1"></i> Download Not Found List
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                                <tr>
                                                    <th style="width:40px">#</th>
                                                    <th>NIC</th>
                                                    <th>Name (from file)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($upload->notFoundDetails as $i => $row)
                                                <tr>
                                                    <td class="text-muted">{{ $i + 1 }}</td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border fw-bold font-monospace">
                                                            {{ $row->nic }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $row->uploaded_name ?? '—' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>

                            </div>{{-- /tab-content --}}
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>{{-- /adminTabsContent --}}
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // ── Designation DataTable ─────────────────────────────────────────────
    var designationTable = $('#dt-designation-users').DataTable({
        responsive: true,
        pageLength: 50,
        columnDefs: [{ visible: false, targets: 0 }],
        order: [[0, 'asc']],
        drawCallback: function (settings) {
            var api  = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var last = null;
            api.column(0, { page: 'current' }).data().each(function (group, i) {
                if (last !== group) {
                    $(rows).eq(i).before(
                        '<tr class="designation-group-row"><td colspan="6" class="py-2 ps-3">'
                        + '<i class="fal fa-layer-group me-2"></i>' + group + '</td></tr>'
                    );
                    last = group;
                }
            });
        },
        dom: "<'row mb-3'<'col-md-6'f><'col-md-6 text-end'B>>" +
             "tr" +
             "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-light border rounded-pill' },
            { extend: 'pdf',   className: 'btn btn-sm btn-light border rounded-pill' }
        ],
        language: { search: '', searchPlaceholder: 'Search name, NIC...' }
    });

    $('.dataTables_filter input')
        .addClass('form-control rounded-pill bg-light border-0 px-3')
        .css('width', '220px');

    document.getElementById('tab-users-btn').addEventListener('shown.bs.tab', function () {
        designationTable.columns.adjust().responsive.recalc();
    });

    // Email column search
    $('#email-search').on('keyup', function () {
        designationTable.column(2).search(this.value).draw();
    });

});
</script>
@endsection
