@extends('layouts.vertical', ['pageTitle' => 'NIC Compliance Analysis'])

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Compliance Report</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Analysis Results</h1>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">
            <i class="fal fa-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>

    @php
        $percentage = $totalUploaded > 0 ? round(($foundCount / $totalUploaded) * 100) : 0;
        $isPerfect = $percentage == 100;
    @endphp

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <span class="text-uppercase fw-bold text-muted small">Overall Compliance</span>
                    </div>
                    
                    <div class="position-relative d-inline-block mb-4">
                        <h1 class="display-2 fw-900 mb-0 {{ $isPerfect ? 'text-success' : 'text-primary' }}">
                            {{ $percentage }}<span class="h4 fw-bold text-muted">%</span>
                        </h1>
                    </div>

                    <progress
                        class="w-100 mb-4"
                        value="{{ $percentage }}"
                        max="100"
                        aria-label="Overall compliance"
                        style="height: 10px; border-radius: 999px; background-color: #eaecf4; accent-color: {{ $isPerfect ? '#198754' : '#0d6efd' }};"
                    >
                        {{ $percentage }}%
                    </progress>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <h4 class="fw-bold mb-0 text-success">{{ $foundCount }}</h4>
                                <small class="text-muted text-uppercase fw-bold">Verified</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <h4 class="fw-bold mb-0 text-danger">{{ count($missing) }}</h4>
                                <small class="text-muted text-uppercase fw-bold">Missing</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Report Actions</h6>
                    <button onclick="window.print()" class="btn btn-outline-dark w-100 rounded-pill mb-2">
                        <i class="fal fa-print me-2"></i> Print Report
                    </button>
                    @if(count($missing) > 0)
                        <button id="copyAll" class="btn btn-outline-primary w-100 rounded-pill">
                            <i class="fal fa-copy me-2"></i> Copy Missing NICs
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">
                            @if($isPerfect)
                                <span class="text-success"><i class="fas fa-check-circle me-2"></i>Full Compliance Found</span>
                            @else
                                <span class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>Non-Compliant NICs</span>
                            @endif
                        </h5>
                        @if(!$isPerfect)
                        <input type="text" id="searchMissing" class="form-control form-control-sm rounded-pill px-3 bg-light border-0"
                               placeholder="Search list..." style="width: 200px;">
                        @endif
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(count($missing) > 0)
                        <div class="alert alert-warning border-0 rounded-4 p-3 small mb-4" style="background-color: rgba(246, 194, 62, 0.1); color: #856404;">
                            <i class="fal fa-lightbulb me-2"></i> These individuals are present in your Excel file but have no corresponding records in the current level.
                        </div>
                        
                        <div id="missingList" class="d-flex flex-wrap gap-2" style="max-height: 400px; overflow-y: auto;">
                            @foreach($missing as $nic)
                                <div class="nic-item badge bg-white text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold shadow-sm" data-nic="{{ $nic }}">
                                    {{ $nic }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fal fa-party-horn display-1 text-success opacity-25"></i>
                            </div>
                            <h4 class="fw-bold">Awesome! No Missing Records</h4>
                            <p class="text-muted">Every person in your uploaded Excel file has been verified in the system.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // 1. Search functionality for the missing list
        $('#searchMissing').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#missingList .nic-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // 2. Copy to Clipboard Utility
        $('#copyAll').on('click', function() {
            var nics = [];
            $('.nic-item').each(function() {
                nics.push($(this).data('nic'));
            });
            
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = nics.join(", ");
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);

            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: nics.length + ' NIC numbers copied to clipboard.',
                timer: 2000,
                showConfirmButton: false
            });
        });
    });
</script>
@endsection
