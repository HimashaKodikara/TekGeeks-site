<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DeclarantRegistration;
use App\Models\DeclarationType;
use App\Models\StatusOfDeclaration;
use App\Services\BackendAPIAssetsBackendService;
use Carbon\Carbon;
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
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $year = $now->year;

        // Counts for Stats Cards
        $currentMonthSaveCount = StatusOfDeclaration::where('status', 'S')
            ->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();

        $lastMonthSaveCount = StatusOfDeclaration::where('status', 'S')
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();

        $currentMonthCompleteCount = StatusOfDeclaration::whereIn('status', ['C', 'E'])
            ->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();

        $lastMonthCompleteCount = StatusOfDeclaration::whereIn('status', ['C', 'E'])
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();

        // Difference Calculation (Current - Last)
        $differenceSave = $currentMonthSaveCount - $lastMonthSaveCount;
        $differenceComplete = $currentMonthCompleteCount - $lastMonthCompleteCount;

        $currentMonthEditCount = StatusOfDeclaration::where('status', 'R')
            ->whereYear('created_at', $now->year)->count();

        // Total Verified Registrations
        $declarantRegistrationCount = DeclarantRegistration::where('status', 'V')->count();

        // Declaration Types with Counts for the Histogram
        $declarationTypes = DeclarationType::where('status', 'Y')
            ->where('is_delete', 0)
            ->withCount([
                'statusOfDeclarations as complete_count' => fn($q) => $q->whereIn('status', ['C', 'E']),
                'statusOfDeclarations as edit_count' => fn($q) => $q->where('status', 'R'),
                'statusOfDeclarations as saved_count' => fn($q) => $q->where('status', 'S'),
            ])
            ->get();

        $months = range(1, 12);

        $monthlyRegistered = [];
        foreach ($months as $month) {
            $monthlyRegistered[] = DeclarantRegistration::where('status', 'V')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();
        }

        $monthlyByType = [];
        foreach ($declarationTypes as $type) {
            $monthlyCounts = [];
            foreach ($months as $month) {
                $monthlyCounts[] = StatusOfDeclaration::where('declaration_type_id', $type->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count();
            }
            $monthlyByType[] = [
                'name' => $type->type_name_en,
                'data' => $monthlyCounts,
            ];
        }

        return view('backend.dashboard', compact(
            'currentMonthCompleteCount', 'lastMonthCompleteCount', 'differenceComplete',
            'currentMonthSaveCount', 'lastMonthSaveCount', 'differenceSave',
            'declarantRegistrationCount', 'declarationTypes', 'currentMonthEditCount',
            'monthlyRegistered',
            'monthlyByType'
        ));
    }

}
