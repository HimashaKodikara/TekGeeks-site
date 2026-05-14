@extends('layouts.vertical', ['pageTitle' => 'Designation Dashboard'])

@section('css')
<style>
    .designation-group-row {
        background-color: #f8f9fa !important;
        font-weight: 700;
        color: #4e73df;
        border-left: 4px solid #4e73df;
    }
    .upload-drop-zone {
        border: 2px dashed #c3c8d4;
        border-radius: 10px;
        padding: 28px;
        transition: border-color 0.2s;
        cursor: pointer;
    }
    .upload-drop-zone:hover { border-color: #4e73df; }
    .upload-drop-zone.dragover { border-color: #4e73df; background: #f0f4ff; }
    .email-search-wrapper { position: relative; }
    .email-search-wrapper .fa-search {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); color: #aaa; pointer-events: none;
    }
    .email-search-wrapper input { padding-left: 34px; }
    .nav-tabs .nav-link { border-radius: 8px 8px 0 0; font-weight: 600; color: #6c757d; }
    .nav-tabs .nav-link.active { color: #4e73df; border-bottom-color: #fff; }
    .tab-badge { font-size: 0.7rem; vertical-align: middle; }
    .upload-card { border-radius: 12px; border: 1px solid #e3e6f0; }
    .upload-header { background: #f8f9fc; border-radius: 12px 12px 0 0; border-bottom: 1px solid #e3e6f0; padding: 14px 18px; }
    .uploader-avatar { width: 36px; height: 36px; background: #4e73df; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; flex-shrink: 0; }
    .stat-pill { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 600; }
    .stat-pill.total  { background: #e8eaf6; color: #3949ab; }
    .stat-pill.found  { background: #e8f5e9; color: #2e7d32; }
    .stat-pill.missing { background: #fce4e4; color: #c62828; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Declaration List</h1>
            <p class="text-muted small mb-0">Manage staff submissions and verify registrants</p>
        </div>
    </div>

    {{-- ── Tab Navigation ──────────────────────────────────────────────── --}}
    <ul class="nav nav-tabs border-bottom mb-0" id="portalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ !session('nic_check_results') ? 'active' : '' }}"
                    id="tab-users-btn" data-bs-toggle="tab" data-bs-target="#tab-users"
                    type="button" role="tab">
                <i class="fal fa-layer-group me-1"></i> Submitted Users
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ !session('nic_check_results') && $errors->has('excel_file') ? 'active' : '' }}"
                    id="tab-upload-btn" data-bs-toggle="tab" data-bs-target="#tab-upload"
                    type="button" role="tab">
                <i class="fal fa-file-upload me-1"></i> Excel Upload
            </button>
        </li>
        @if(($isAdmin ?? false) && isset($uploads))
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-history-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-history"
                    type="button" role="tab">
                <i class="fal fa-history me-1"></i> Upload History
                @if($uploads->count())
                    <span class="badge bg-primary rounded-pill tab-badge ms-1">{{ $uploads->count() }}</span>
                @endif
            </button>
        </li>
        @endif
        @if(session('nic_check_results'))
        @php $results = session('nic_check_results'); @endphp
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-results-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-results"
                    type="button" role="tab">
                <i class="fal fa-table me-1"></i> Upload Results
                <span class="badge bg-primary rounded-pill tab-badge ms-1">{{ $results['total'] }}</span>
                <span class="badge bg-success rounded-pill tab-badge">{{ $results['found_count'] }} found</span>
                <span class="badge bg-danger rounded-pill tab-badge">{{ $results['not_found_count'] }} not found</span>
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content bg-white border border-top-0 rounded-bottom shadow-sm" id="portalTabsContent">

        {{-- ── Tab 1: Designation Users ───────────────────────────────── --}}
        <div class="tab-pane fade {{ !session('nic_check_results') ? 'show active' : '' }}"
             id="tab-users" role="tabpanel">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <p class="text-muted small mb-0">All registered users grouped by designation</p>
                    </div>
                    <div class="email-search-wrapper">
                        <i class="fal fa-search"></i>
                        <input type="text" id="email-search"
                               class="form-control form-control-sm rounded-pill bg-light border-0"
                               placeholder="Search by email..." style="width:220px; padding-left:34px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="dt-designation-users" class="table table-hover align-middle w-100">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th>Designation</th>
                                <th class="ps-3">User Details</th>
                                <th>Email</th>
                                <th class="text-center">Declaration Type</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Submitted Date</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedData as $designationName => $users)
                                @foreach($users as $user)
                                    @if($user->declarationStatuses->isEmpty())
                                        <tr>
                                            <td>{{ $designationName }}</td>
                                            <td class="ps-3">
                                                <div class="fw-bold">{{ $user->personalInfo->full_name ?? $user->surname . ' ' . $user->other_names }}</div>
                                                <small class="text-muted font-monospace">{{ $user->nic }}</small>
                                            </td>
                                            <td>
                                                @if($user->email)
                                                    <a href="mailto:{{ $user->email }}" class="text-muted small">{{ $user->email }}</a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center"><span class="text-muted small">—</span></td>
                                            <td class="text-center"><span class="badge rounded-pill bg-danger px-3">Not Started</span></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill px-2 py-1 bg-light text-muted border">
                                                    <i class="fal fa-times-circle me-1"></i> Not Started
                                                </span>
                                            </td>
                                            <td class="text-end pe-3"><span class="text-muted small">—</span></td>
                                        </tr>
                                    @else
                                        @foreach($user->declarationStatuses as $sod)
                                            <tr>
                                                <td>{{ $designationName }}</td>
                                                <td class="ps-3">
                                                    <div class="fw-bold">{{ $user->personalInfo->full_name ?? $user->surname . ' ' . $user->other_names }}</div>
                                                    <small class="text-muted font-monospace">{{ $user->nic }}</small>
                                                </td>
                                                <td>
                                                    @if($user->email)
                                                        <a href="mailto:{{ $user->email }}" class="text-muted small">{{ $user->email }}</a>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border px-2">{{ $sod->declarationType->type_name_en ?? '—' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($sod->status === 'C')
                                                        <span class="badge rounded-pill bg-success px-2" style="font-size:14px;">Completed</span>
                                                    @elseif($sod->status === 'S')
                                                        <span class="badge rounded-pill bg-warning text-dark px-2" style="font-size:14px;">Started</span>
                                                    @elseif($sod->status === 'R')
                                                        <span class="badge rounded-pill bg-info px-2" style="font-size:14px;">Restarted</span>
                                                    @elseif($sod->status === 'E')
                                                        <span class="badge rounded-pill bg-primary px-2" style="font-size:14px;">Editing</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($sod->status === 'C')
                                                        <span class="badge rounded-pill px-2 py-1 bg-success text-white">
                                                            @if($sod->completed_date)
                                                                <small class="d-block" style="font-size:12px;">{{ \Carbon\Carbon::parse($sod->completed_date)->format('Y-m-d') }}</small>
                                                            @endif
                                                        </span>
                                                    @elseif($sod->status === 'S')
                                                        <span class="badge rounded-pill px-2 py-1 bg-warning text-dark" style="font-size:14px;">
                                                            <i class="fal fa-spinner-third fa-spin me-1"></i> Started
                                                        </span>
                                                    @elseif($sod->status === 'R')
                                                        <span class="badge rounded-pill px-2 py-1 bg-info text-white" style="font-size:14px;">Restarted</span>
                                                    @elseif($sod->status === 'E')
                                                        <span class="badge rounded-pill px-2 py-1 bg-primary text-white" style="font-size:14px;">Edit Approved</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    @if($sod->report_status === 'R')
                                                        <span class="badge rounded-pill px-2 py-1 bg-light text-danger border">
                                                            <i class="fal fa-flag me-1"></i> Reported
                                                        </span>
                                                    @else
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger rounded-pill report-btn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#reportModal"
                                                                data-id="{{ $sod->purpose_of_declaration_id ?? '' }}"
                                                                data-status_id="{{ $sod->id ?? '' }}"
                                                                data-nic="{{ $user->nic }}"
                                                                data-name="{{ $user->surname }}">
                                                            <i class="fal fa-exclamation-triangle me-1"></i> Report
                                                        </button>
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
            </div>
        </div>

        {{-- ── Tab 2: Check Users (Upload Form) ──────────────────────── --}}
        <div class="tab-pane fade {{ ($errors->has('excel_file') || $errors->has('declaration_year')) && !session('nic_check_results') ? 'show active' : '' }}"
             id="tab-upload" role="tabpanel">
            <div class="p-4">
                <p class="text-muted small mb-4">
                    Upload an Excel/CSV file with the required columns below
                    to verify which users are registered in this institute.
                </p>

                @if ($errors->has('excel_file') && $errors->first('excel_file') !== 'This field is required.')
                    <div class="alert alert-danger rounded-3 py-2 small mb-3">
                        <i class="fal fa-exclamation-circle me-1"></i> {{ $errors->first('excel_file') }}
                    </div>
                @endif

                <form id="upload-form" action="{{ route($routePrefix . '.check-designation-nics', $id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4 align-items-start">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="declaration_year" class="form-label fw-bold small text-uppercase text-muted">
                                    Declaration Year <span class="text-danger">*</span>
                                </label>
                                <select name="declaration_year" id="declaration_year" class="form-select rounded-pill bg-light border-0 @error('declaration_year') is-invalid @enderror">
                                    <option value="">-- Select Year --</option>
                                    @foreach($declarationYears ?? [] as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                <div id="year-error" class="text-danger small mt-1 {{ $errors->has('declaration_year') ? '' : 'd-none' }}">
                                    <i class="fal fa-exclamation-circle me-1"></i> This field is required.
                                </div>
                            </div>
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                Upload File <span class="text-danger">*</span>
                            </label>
                            <div class="upload-drop-zone text-center @error('excel_file') border-danger @enderror" id="drop-zone"
                                 onclick="document.getElementById('excel_file_input').click()">
                                <i class="fal fa-file-excel fa-3x text-success mb-2 d-block"></i>
                                <p class="mb-1 fw-semibold text-muted" id="file-label">
                                    Click to select or drag &amp; drop file
                                </p>
                                <p class="text-muted small mb-0">Supported: .xlsx, .xls, .csv — max 5 MB</p>
                                <input type="file" name="excel_file" id="excel_file_input"
                                       accept=".xlsx,.xls,.csv" class="d-none">
                            </div>
                            <div id="file-error" class="text-danger small mt-1 {{ $errors->has('excel_file') ? '' : 'd-none' }}">
                                <i class="fal fa-exclamation-circle me-1"></i> This field is required.
                            </div>
                        </div>
                        <div class="col-md-5 d-flex flex-column gap-2 pt-2">
                            <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4">
                                <i class="fal fa-search me-2"></i> Check NICs
                            </button>
                            <a href="{{ route($routePrefix . '.download-excel') }}"
                               class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fal fa-download me-2"></i> Download Sample File
                            </a>
                            <div class="mt-2 p-3 bg-light rounded-3">
                                <p class="small fw-bold mb-1 text-muted text-uppercase">Required columns</p>
                                <code class="small">National Identity Card No</code> — NIC number<br>
                                <code class="small">Name in Full</code> — person's full name<br>
                                <code class="small">Designation</code> — job designation<br>
                                <code class="small">Email Address</code> — email <span class="text-muted">(optional, for match check)</span><br>
                                <code class="small">Institution Name</code> — institution name
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Tab 3: Upload Results (only when session has data) ──────── --}}
        @if(session('nic_check_results'))
        <div class="tab-pane fade show active" id="tab-results" role="tabpanel">
            <div class="p-3">

                {{-- Actions bar --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <p class="text-muted small mb-0">
                            Results from the most recent upload — {{ $results['total'] }} rows processed
                        </p>
                        @if(!empty($results['declaration_year']))
                            <span class="badge rounded-pill px-3 py-1 mt-1" style="background:#e8eaf6;color:#3949ab;font-size:.78rem;">
                                <i class="fal fa-calendar me-1"></i> Year: {{ $results['declaration_year'] }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route($routePrefix . '.download-nic-report', ['type' => 'not_found']) }}"
                           class="btn btn-danger rounded-pill fw-bold px-3">
                            <i class="fal fa-file-excel me-2"></i> Not Found List
                            <span class="badge bg-white text-danger ms-1">{{ $results['not_found_count'] }}</span>
                        </a>
                        <a href="{{ route($routePrefix . '.download-nic-report', ['type' => 'found']) }}"
                           class="btn btn-success rounded-pill fw-bold px-3">
                            <i class="fal fa-file-excel me-2"></i> Found List
                            <span class="badge bg-white text-success ms-1">{{ $results['found_count'] }}</span>
                        </a>
                        <a href="{{ route($routePrefix . '.download-nic-report', ['type' => 'all']) }}"
                           class="btn btn-outline-secondary rounded-pill fw-bold px-3">
                            <i class="fal fa-file-excel me-2"></i> Full Report
                        </a>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 bg-light text-center py-3 rounded-3">
                            <div class="fs-2 fw-bold text-dark">{{ $results['total'] }}</div>
                            <div class="text-muted small">Total Uploaded</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 text-center py-3 rounded-3" style="border-left:4px solid #198754 !important; background:#f0faf4;">
                            <div class="fs-2 fw-bold text-success">{{ $results['found_count'] }}</div>
                            <div class="text-muted small">Found in System</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 text-center py-3 rounded-3" style="border-left:4px solid #dc3545 !important; background:#fff5f5;">
                            <div class="fs-2 fw-bold text-danger">{{ $results['not_found_count'] }}</div>
                            <div class="text-muted small">Not Found</div>
                        </div>
                    </div>
                </div>

                {{-- Results Table --}}
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle" id="upload-results-table">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th style="width:45px">#</th>
                                <th>NIC</th>
                                <th>Name (file)</th>
                                <th>Designation (file)</th>
                                <th>Institution (file)</th>
                                <th>Email (file)</th>
                                <th>System Name</th>
                                <th>Email (system)</th>
                                <th>Designation (system)</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Name Match</th>
                                <th class="text-center">Email Match</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['rows'] as $row)
                            @php
                                $nameMatch = $row['found']
                                    && !empty($row['uploaded_name'])
                                    && (stripos($row['system_name'] ?? '', $row['uploaded_name']) !== false
                                        || stripos($row['uploaded_name'], $row['system_name'] ?? '') !== false);
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $row['index'] }}</td>
                                <td><span class="badge bg-light text-dark border fw-bold font-monospace">{{ $row['nic'] }}</span></td>
                                <td>{{ $row['uploaded_name'] ?? '—' }}</td>
                                <td class="small text-muted">{{ $row['uploaded_designation'] ?? '—' }}</td>
                                <td class="small text-muted">{{ $row['uploaded_institution_name'] ?? '—' }}</td>
                                <td class="small text-muted">{{ $row['uploaded_email'] ?? '—' }}</td>
                                <td class="fw-bold">{{ $row['system_name'] ?? '—' }}</td>
                                <td class="small">
                                    @if(!empty($row['email']) && $row['email'] !== 'N/A')
                                        <a href="mailto:{{ $row['email'] }}">{{ $row['email'] }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $row['designation'] ?? '—' }}</td>
                                <td class="text-center">
                                    @if($row['found'])
                                        <span class="badge bg-success rounded-pill px-2">
                                            <i class="fal fa-check me-1"></i> Found
                                        </span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-2">
                                            <i class="fal fa-times me-1"></i> Not Found
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$row['found'])
                                        <span class="badge bg-light text-muted rounded-pill">—</span>
                                    @elseif(empty($row['uploaded_name']))
                                        <span class="badge bg-secondary rounded-pill">No name</span>
                                    @elseif($nameMatch)
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
                                    @if(!$row['found'])
                                        <span class="badge bg-light text-muted rounded-pill">—</span>
                                    @elseif(empty($row['uploaded_email']))
                                        <span class="badge bg-secondary rounded-pill">No email</span>
                                    @elseif($row['email_match'])
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

                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill"
                            onclick="document.getElementById('tab-upload-btn').click()">
                        <i class="fal fa-upload me-1"></i> Upload another file
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Tab 4: Upload History (admin only) ─────────────────────── --}}
        @if(($isAdmin ?? false) && isset($uploads))
        <div class="tab-pane fade" id="tab-history" role="tabpanel">
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
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <span class="stat-pill total"><i class="fal fa-list"></i> {{ $upload->total_count }} Total</span>
                                <span class="stat-pill found"><i class="fal fa-check"></i> {{ $upload->found_count }} Found</span>
                                <span class="stat-pill missing"><i class="fal fa-times"></i> {{ $upload->not_found_count }} Not Found</span>
                                <a href="{{ route('admin-institute.download-upload-report', [$upload->id, 'type' => 'not_found']) }}"
                                   class="btn btn-sm btn-danger rounded-pill px-2 py-1" style="font-size:.75rem;" title="Download Not Found List">
                                    <i class="fal fa-file-excel me-1"></i> Not Found
                                </a>
                                <a href="{{ route('admin-institute.download-upload-report', [$upload->id, 'type' => 'found']) }}"
                                   class="btn btn-sm btn-success rounded-pill px-2 py-1" style="font-size:.75rem;" title="Download Found List">
                                    <i class="fal fa-file-excel me-1"></i> Found
                                </a>
                                <a href="{{ route('admin-institute.download-upload-report', [$upload->id, 'type' => 'all']) }}"
                                   class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size:.75rem;" title="Download Full Report">
                                    <i class="fal fa-file-excel me-1"></i> Full Report
                                </a>
                            </div>
                        </div>

                        <div class="p-3">
                            <ul class="nav nav-tabs nav-tabs-sm mb-0" id="uploadTabs{{ $upload->id }}" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 px-3 small"
                                            data-bs-toggle="tab" data-bs-target="#found-{{ $upload->id }}" type="button">
                                        <i class="fal fa-check-circle me-1 text-success"></i> Found
                                        <span class="badge bg-success rounded-pill ms-1" style="font-size:.65rem;">{{ $upload->found_count }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 small"
                                            data-bs-toggle="tab" data-bs-target="#notfound-{{ $upload->id }}" type="button">
                                        <i class="fal fa-times-circle me-1 text-danger"></i> Not Found
                                        <span class="badge bg-danger rounded-pill ms-1" style="font-size:.65rem;">{{ $upload->not_found_count }}</span>
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content border border-top-0 rounded-bottom p-0">
                                <div class="tab-pane fade show active p-2" id="found-{{ $upload->id }}">
                                    @if($upload->foundDetails->isEmpty())
                                        <p class="text-muted small text-center py-3 mb-0">No found users in this upload.</p>
                                    @else
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                                <tr>
                                                    <th style="width:40px">#</th>
                                                    <th>NIC</th>
                                                    <th>Name (file)</th>
                                                    <th>Designation (file)</th>
                                                    <th>Institution (file)</th>
                                                    <th>Email (file)</th>
                                                    <th>System Name</th>
                                                    <th>Email (system)</th>
                                                    <th>Designation (system)</th>
                                                    <th class="text-center">Name Match</th>
                                                    <th class="text-center">Email Match</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($upload->foundDetails as $i => $row)
                                                <tr>
                                                    <td class="text-muted">{{ $i + 1 }}</td>
                                                    <td><span class="badge bg-light text-dark border fw-bold font-monospace">{{ $row->nic }}</span></td>
                                                    <td>{{ $row->uploaded_name ?? '—' }}</td>
                                                    <td class="small text-muted">{{ $row->uploaded_designation ?? '—' }}</td>
                                                    <td class="small text-muted">{{ $row->uploaded_institution_name ?? '—' }}</td>
                                                    <td class="small text-muted">{{ $row->uploaded_email ?? '—' }}</td>
                                                    <td class="fw-bold">{{ $row->system_name ?? '—' }}</td>
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
                                                            <span class="badge bg-success rounded-pill"><i class="fal fa-check me-1"></i> Match</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark rounded-pill"><i class="fal fa-exclamation me-1"></i> Mismatch</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if(is_null($row->email_match))
                                                            <span class="badge bg-secondary rounded-pill">No email</span>
                                                        @elseif($row->email_match)
                                                            <span class="badge bg-success rounded-pill"><i class="fal fa-check me-1"></i> Match</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark rounded-pill"><i class="fal fa-exclamation me-1"></i> Mismatch</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>

                                <div class="tab-pane fade p-2" id="notfound-{{ $upload->id }}">
                                    @if($upload->notFoundDetails->isEmpty())
                                        <p class="text-muted small text-center py-3 mb-0">All users were found in this upload.</p>
                                    @else
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                                <tr>
                                                    <th style="width:40px">#</th>
                                                    <th>NIC</th>
                                                    <th>Name (file)</th>
                                                    <th>Designation (file)</th>
                                                    <th>Institution (file)</th>
                                                    <th>Email (file)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($upload->notFoundDetails as $i => $row)
                                                <tr>
                                                    <td class="text-muted">{{ $i + 1 }}</td>
                                                    <td><span class="badge bg-light text-dark border fw-bold font-monospace">{{ $row->nic }}</span></td>
                                                    <td>{{ $row->uploaded_name ?? '—' }}</td>
                                                    <td class="small text-muted">{{ $row->uploaded_designation ?? '—' }}</td>
                                                    <td class="small text-muted">{{ $row->uploaded_institution_name ?? '—' }}</td>
                                                    <td class="small text-muted">{{ $row->uploaded_email ?? '—' }}</td>
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
                    @endforeach
                @endif
            </div>
        </div>
        @endif

    </div>{{-- /tab-content --}}
</div>

{{-- Report Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="reportModalLabel">
                    <i class="fal fa-flag me-2"></i> Report Discrepancy
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('institute.declaration-report') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="declaration_id" id="modal_declaration_id">
                    <input type="hidden" name="status_id"      id="modal_status_name">
                    <input type="hidden" name="nic"            id="modal_nic_name">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Reporting For</label>
                        <input type="text" id="modal_user_name" class="form-control bg-light border-0 fw-bold" readonly>
                    </div>
                    <div class="mb-0">
                        <label for="comments" class="form-label fw-bold small text-uppercase">
                            Comments / Details
                        </label>
                        <textarea name="comments" id="comments" class="form-control" rows="3"
                                  placeholder="Please provide specific details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="confirmReportBtn">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // ── Designation-wise DataTable ────────────────────────────────────────
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
                        '<tr class="designation-group-row"><td colspan="7" class="py-2 ps-3">' +
                        '<i class="fal fa-layer-group me-2"></i>' + group + '</td></tr>'
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

    // Recalculate layout when users tab is shown (fixes DataTable width in hidden tab)
    document.getElementById('tab-users-btn').addEventListener('shown.bs.tab', function () {
        designationTable.columns.adjust().responsive.recalc();
    });

    // Email column search (column index 2 after hidden designation col)
    $('#email-search').on('keyup', function () {
        designationTable.column(2).search(this.value).draw();
    });

    // ── Upload Results DataTable ──────────────────────────────────────────
    if ($('#upload-results-table').length) {
        $('#upload-results-table').DataTable({
            pageLength: 25,
            responsive: true,
            order: [[6, 'asc']],   // Not Found rows first
            dom: "<'row mb-2'<'col-md-6'f><'col-md-6 text-end'B>>" +
                 "tr" +
                 "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'excel', className: 'btn btn-sm btn-light border rounded-pill',
                  title: 'NIC Check Results' },
                { extend: 'pdf',   className: 'btn btn-sm btn-light border rounded-pill',
                  title: 'NIC Check Results' }
            ],
            language: { search: '', searchPlaceholder: 'Search NIC, name, email...' }
        });
    }

    // ── Upload form validation ────────────────────────────────────────────
    var uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (e) {
            var fileInput  = document.getElementById('excel_file_input');
            var fileError  = document.getElementById('file-error');
            var dropZone   = document.getElementById('drop-zone');
            var yearSelect = document.getElementById('declaration_year');
            var yearError  = document.getElementById('year-error');
            var valid = true;

            if (!yearSelect.value) {
                yearError.classList.remove('d-none');
                yearSelect.classList.add('is-invalid');
                valid = false;
            }

            if (!fileInput.files || fileInput.files.length === 0) {
                fileError.classList.remove('d-none');
                dropZone.style.borderColor = '#dc3545';
                valid = false;
            }

            if (!valid) e.preventDefault();
        });

        document.getElementById('declaration_year').addEventListener('change', function () {
            document.getElementById('year-error').classList.add('d-none');
            this.classList.remove('is-invalid');
        });
    }

    // ── Drag & drop file label ────────────────────────────────────────────
    var dropZone = document.getElementById('drop-zone');
    var fileInput = document.getElementById('excel_file_input');

    if (dropZone && fileInput) {
        fileInput.addEventListener('change', function () {
            var name = this.files[0]?.name;
            if (name) {
                document.getElementById('file-label').textContent = name;
                document.getElementById('file-error').classList.add('d-none');
                dropZone.style.borderColor = '';
            }
        });

        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', function () {
            this.classList.remove('dragover');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                document.getElementById('file-label').textContent = e.dataTransfer.files[0].name;
            }
        });
    }

    // ── Report modal population ───────────────────────────────────────────
    $(document).on('click', '.report-btn', function () {
        $('#modal_declaration_id').val($(this).data('id'));
        $('#modal_user_name').val($(this).data('name'));
        $('#modal_status_name').val($(this).data('status_id'));
        $('#modal_nic_name').val($(this).data('nic'));
    });

    // ── Report submit confirmation ────────────────────────────────────────
    $('#confirmReportBtn').on('click', function () {
        Swal.fire({
            title: 'Submit Report?',
            text: 'Are you sure you want to report this discrepancy?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Submit',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#confirmReportBtn').closest('form').submit();
            }
        });
    });

});
</script>
@endsection
