<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\BackendAPIAssetsBackendService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    protected BackendAPIAssetsBackendService $apiService;

    public function __construct(BackendAPIAssetsBackendService $apiService)
    {
        $this->apiService = $apiService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:dashboard', only: ['index']),
        ];
    }

    public function index(Request $request)
    {

        return view('backend.dashboard');
    }
}
