@extends('layouts.vertical', ['pageTitle' => 'Project Management Dashboard'])

@section('page-title')
    <div class="d-flex align-items-end mb-4">
        <div>
            @include('layouts.partials/app-pagetitle', ['pageTitle'=> 'Project Management', 'pageSubTitle1' => 'Insights', 'pageSubTitle2'=> 'Dashboards'])
        </div>
        <div class="ms-auto d-none d-sm-flex align-items-center">
            <div class="d-flex align-items-center">
                <div class="d-inline-flex flex-column justify-content-center me-2">
                                    <span class="fw-500 fs-xs d-block">
                                        <small>COMPLETION RATE</small>
                                    </span>
                    <span class="fw-500 fs-xl d-flex align-items-center text-success"> 80% <svg
                            class="sa-icon sa-bold sa-icon-success ms-1">
                                            <use href="/img/sprite.svg#trending-up"></use>
                                        </svg>
                                    </span>
                </div>
            </div>
            <div
                class="d-flex align-items-center border-faded border-dashed border-top-0 border-bottom-0 border-end-0 ms-3 ps-3">
                <div class="d-inline-flex flex-column justify-content-center me-2">
                                    <span class="fw-500 fs-xs d-block">
                                        <small>TASKS DUE TODAY</small>
                                    </span>
                    <span class="fw-500 fs-xl d-flex align-items-center text-danger"> 12 <svg
                            class="sa-icon sa-bold ms-1 sa-icon-danger">
                                            <use href="/img/sprite.svg#alert-circle"></use>
                                        </svg>
                                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Summary cards -->
    

    
@endsection

@section('scripts')
    @vite(['resources/scripts/pages/projectmanagementdashboard.js'])
@endsection
