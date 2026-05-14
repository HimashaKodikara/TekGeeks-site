@extends('layouts.vertical', ['pageTitle' => 'Dashboard'])

@section('css')
<style>
    /* Table Styling */
    .table-danger-soft { background-color: #fff5f5 !important; transition: background-color 0.3s ease; }
    .table-hover tbody tr.table-danger-soft:hover { background-color: #fee2e2 !important; }
    .table-danger-soft td { border-color: #fecaca !important; }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    
    /* Navigation Pill Styling */
    .nav-pills-custom .nav-link { transition: all 0.3s ease; border: 1px solid transparent; }
    .nav-pills-custom .nav-link.active {
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.25);
        background-color: #4e73df !important;
    }
    
    /* Summary Card Styling */
    .summary-box {
        border: 2px dashed #e3e6f0;
        background-color: #f8f9fc;
        border-radius: 20px;
        padding: 3rem;
        transition: transform 0.2s;
    }
    .summary-box:hover { transform: translateY(-5px); }

   /* Only these few lines needed */
/* Static Card Styles */
    .dashboard-card {
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        /* Standard shadow that stays the same */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }

    /* Vibrant Gradients (Same as before) */
    .bg-gradient-indigo {
        background: linear-gradient(45deg, #4f46e5, #818cf8);
    }

    .bg-gradient-emerald {
        background: linear-gradient(45deg, #059669, #34d399);
    }

    .bg-gradient-amber {
        background: linear-gradient(45deg, #d97706, #fbbf24);
    }

    /* Glass Effect for Icons */
    .icon-box-glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Text & Utilities */
    .text-white-70 { color: rgba(255, 255, 255, 0.7) !important; }
    .bg-white-20 { background-color: rgba(255, 255, 255, 0.2); }
    .fw-900 { font-weight: 900; font-size: 1.8rem; letter-spacing: -0.5px; }
    .fs-11 { font-size: 11px; letter-spacing: 0.5px; }
    .progress-xs { height: 4px; border-radius: 10px; }

    .bg-gradient-rose {
        background: linear-gradient(45deg, #f43f5e, #fb7185);
    }

    /* Keep your existing card styles */
    .dashboard-card {
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    .icon-box-glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .text-white-70 { color: rgba(255, 255, 255, 0.7) !important; }
    .bg-white-20 { background-color: rgba(255, 255, 255, 0.2); }
    .fw-900 { font-weight: 900; font-size: 1.8rem; letter-spacing: -0.5px; }
    .fs-11 { font-size: 11px; letter-spacing: 0.5px; }
    .progress-xs { height: 4px; border-radius: 10px; }
</style>
@endsection

@section('content')
    <!-- Summary cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-g border-0 dashboard-card bg-gradient-indigo text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-70 text-uppercase fs-11 fw-700 mb-1">Registered Users</h6>
                            <h2 class="fw-900 mb-0">{{ $declarantRegistrationCount }}</h2>
                        </div>
                        <div class="icon-box-glass">
                            <i class="fal fa-users fs-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress progress-xs bg-white-20">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-g border-0 dashboard-card bg-gradient-emerald text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-70 text-uppercase fs-11 fw-700 mb-1">Submission Completed</h6>
                            <h2 class="fw-900 mb-0">{{ $currentMonthCompleteCount + $lastMonthCompleteCount }}</h2>
                        </div>
                        <div class="icon-box-glass">
                            <i class="fal fa-check-double fs-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress progress-xs bg-white-20">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-g border-0 dashboard-card bg-gradient-rose text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-70 text-uppercase fs-11 fw-700 mb-1">Submission Editing</h6>
                            <h2 class="fw-900 mb-0">{{ $currentMonthEditCount }}</h2>
                        </div>
                        <div class="icon-box-glass">
                            <i class="fal fa-edit fs-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress progress-xs bg-white-20">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-g border-0 dashboard-card bg-gradient-amber text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-70 text-uppercase fs-11 fw-700 mb-1">Submission Ongoing</h6>
                            <h2 class="fw-900 mb-0">{{ $currentMonthSaveCount + $lastMonthSaveCount }}</h2>
                        </div>
                        <div class="icon-box-glass">
                            <i class="fal fa-spinner fs-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress progress-xs bg-white-20">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="fw-700 text-dark mb-1">Registrations & Declaration Submissions — {{ date('Y') }}</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">Monthly count overview for {{ date('Y') }}</p>
                </div>
                <div id="chartLegend" class="d-flex flex-column gap-2"></div>
            </div>
            <hr class="mt-3 mb-0">
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <canvas id="mainHistogram" style="height: 340px;"></canvas>
        </div>
    </div>

    <div class="container mt-4">
    <h3>Declaration Type Summary</h3>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Declaration Type</th>
                <th class="text-center text-success">Submission Completed</th>
                <th class="text-center text-primary">Submission Editing</th>
                <th class="text-center text-warning">Submission Ongoing</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($declarationTypes as $type)
                <tr>
                    <td>{{ $type->type_name_en }}</td>
                    <td class="text-center">
                        <span class="badge badge-success text-dark">{{ $type->complete_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-primary text-dark">{{ $type->edit_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning text-dark">{{ $type->saved_count }}</span>
                    </td>
                    <td class="text-center font-weight-bold">
                        {{ $type->complete_count + $type->edit_count + $type->saved_count }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No active declarations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

    const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const currentMonthIndex = new Date().getMonth();
    const labels = allMonths.slice(0, currentMonthIndex + 1);

    const rawRegistered = @json($monthlyRegistered);
    const monthlyByType = @json($monthlyByType);

    const palette = ['#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#84cc16','#ec4899','#0ea5e9','#a855f7'];

    function hexToRgb(hex) {
        return [parseInt(hex.slice(1,3),16), parseInt(hex.slice(3,5),16), parseInt(hex.slice(5,7),16)];
    }

    function prepareData(raw) {
        const data = raw.slice(0, currentMonthIndex + 1);
        let running = 0;
        return data.map(v => { running += v; return running; });
    }

    const ctx = document.getElementById('mainHistogram').getContext('2d');
    const h = ctx.canvas.offsetHeight || 340;

    function makeGradient(hex) {
        const [r, g, b] = hexToRgb(hex);
        const g1 = ctx.createLinearGradient(0, 0, 0, h);
        g1.addColorStop(0, `rgba(${r},${g},${b},0.15)`);
        g1.addColorStop(1, `rgba(${r},${g},${b},0.00)`);
        return g1;
    }

    function makeDataset(label, rawData, color) {
        return {
            label,
            data: prepareData(rawData),
            borderColor: color,
            backgroundColor: makeGradient(color),
            borderWidth: 3,
            tension: 0.45,
            spanGaps: true,
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            fill: true
        };
    }

    const datasets = [makeDataset('Registrations', rawRegistered, '#4f46e5')];
    monthlyByType.forEach(function(type, i) {
        datasets.push(makeDataset(type.name, type.data, palette[i % palette.length]));
    });

    // Build legend dynamically
    const legendEl = document.getElementById('chartLegend');
    datasets.forEach(function(ds) {
        const span = document.createElement('span');
        span.className = 'd-flex align-items-center gap-1';
        span.style.cssText = `font-size:12px;color:${ds.borderColor};font-weight:600;`;
        span.innerHTML = `<span style="width:12px;height:12px;border-radius:3px;background:${ds.borderColor};display:inline-block;"></span> ${ds.label}`;
        legendEl.appendChild(span);
    });

    new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            animation: { duration: 1200, easing: 'easeOutCubic' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.92)',
                    padding: { x: 16, y: 12 },
                    cornerRadius: 12,
                    titleFont: { size: 13, weight: '700' },
                    bodyFont: { size: 13 },
                    bodySpacing: 6,
                    callbacks: {
                        title: function(items) {
                            return items[0].label + ' {{ date("Y") }}';
                        },
                        label: function(context) {
                            return '  ' + context.dataset.label + ':  ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(226,232,240,0.7)' },
                    border: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 12 }, padding: 8, precision: 0 }
                },
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#64748b', font: { size: 12, weight: '600' }, padding: 8 }
                }
            }
        }
    });
});
</script>
@endsection
