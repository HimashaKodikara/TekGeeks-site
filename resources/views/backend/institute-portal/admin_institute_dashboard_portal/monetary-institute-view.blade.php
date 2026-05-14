@extends('layouts.vertical', ['pageTitle' => 'Institute Management Details'])

@section('css')
<style>
    .info-card { border-radius: 10px; border: none; }
    .section-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .designation-group-row {
        background-color: #eef2ff !important;
        font-weight: 700;
        color: #4e73df;
        border-left: 4px solid #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin-institute.index') }}" class="btn btn-sm btn-light rounded-pill me-2">
                <i class="fal fa-arrow-left me-1"></i> Back
            </a>
            <span class="h5 fw-bold text-gray-800 mb-0">
                {{ $monetaryInstitute->monetary_institute_name ?? 'Institute' }}
            </span>
            <p class="text-muted small mb-0 mt-1">Read-only — Other declaration users by designation</p>
        </div>
        @if($designationClass)
            <span class="badge rounded-pill px-3 py-2" style="background:#e8eaf6;color:#3949ab;font-size:.85rem;">
                <i class="fal fa-layer-group me-1"></i> {{ $designationClass->name_en }}
            </span>
        @endif
    </div>

    {{-- Info Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card info-card p-3" style="background:#f0f4ff;">
                <div class="section-label mb-1">Monetary Institute</div>
                <div class="fw-bold text-dark">{{ $monetaryInstitute->monetary_institute_name ?? '—' }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card info-card p-3" style="background:#f0fff4;">
                <div class="section-label mb-1">Designation Class</div>
                <div class="fw-bold text-dark">{{ $designationClass->name_en ?? 'Unclassified' }}</div>
            </div>
        </div>
    </div>

    {{-- Users Panel --}}
    <div class="panel">
        <div class="panel-hdr bg-primary-800 bg-fusion-gradient">
            <h2>
                <i class="fal fa-users me-2"></i> Declaration Users
                <span class="fw-300">(Designation-wise, Other Declarations Only)</span>
            </h2>
            <div class="panel-toolbar">
                <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            </div>
        </div>
        <div class="panel-container show">
            <div class="panel-content">

                @if($declarations->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fal fa-users fa-3x mb-3 d-block"></i>
                        No designations have other declarations for this class.
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="users-view-table" class="table table-hover align-middle w-100">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th style="width:45px;">#</th>
                                    <th>Designation</th>
                                    <th>User Details</th>
                                    <th>Email</th>
                                    <th class="text-center">Declaration Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowIndex = 0; @endphp
                                @foreach($declarations as $row)
                                    @php $rowIndex++; @endphp
                                    <tr>
                                        <td class="text-muted">{{ $rowIndex }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 small">
                                                {{ $row->designation_name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ trim($row->surname . ' ' . $row->other_names) }}</div>
                                            <small class="text-muted font-monospace">{{ $row->nic }}</small>
                                        </td>
                                        <td>
                                            @if($row->email)
                                                <a href="mailto:{{ $row->email }}" class="text-muted small">{{ $row->email }}</a>
                                            @else
                                                <span class="text-muted small">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2">
                                                {{ $row->declaration_type ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($row->status === 'C')
                                                <span class="badge rounded-pill bg-success px-3">Completed</span>
                                            @elseif($row->status === 'S')
                                                <span class="badge rounded-pill bg-warning text-dark px-3">Started</span>
                                            @elseif($row->status === 'R')
                                                <span class="badge rounded-pill bg-info px-3">Restarted</span>
                                            @elseif($row->status === 'E')
                                                <span class="badge rounded-pill bg-primary px-3">Edit Approved</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger px-3">Not Started</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($row->status === 'C' && !empty($row->completed_date))
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($row->completed_date)->format('Y-m-d') }}
                                                </small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($('#users-view-table tbody tr').length > 0) {
        var table = $('#users-view-table').DataTable({
            responsive: true,
            pageLength: 25,
            columnDefs: [],
            order: [[1, 'asc']],
            dom: "<'row mb-3'<'col-md-6'f><'col-md-6 text-end'B>>" +
                 "tr" +
                 "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'excel', className: 'btn btn-sm btn-light border rounded-pill',
                  title: '{{ addslashes(($monetaryInstitute->monetary_institute_name ?? "") . " — " . ($designationClass->name_en ?? "")) }}' },
                { extend: 'pdf',   className: 'btn btn-sm btn-light border rounded-pill',
                  title: '{{ addslashes(($monetaryInstitute->monetary_institute_name ?? "") . " — " . ($designationClass->name_en ?? "")) }}' }
            ],
            language: { search: '', searchPlaceholder: 'Search name, NIC...' }
        });

        $('.dataTables_filter input')
            .addClass('form-control rounded-pill bg-light border-0 px-3')
            .css('width', '220px');
    }

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
