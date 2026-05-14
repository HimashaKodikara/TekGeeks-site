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
    
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>


@endsection
