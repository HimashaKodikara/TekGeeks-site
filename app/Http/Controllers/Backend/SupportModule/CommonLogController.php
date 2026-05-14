<?php

namespace App\Http\Controllers\Backend\SupportModule;

use App\Http\Controllers\Controller;
use App\Models\CommonLog;
use App\Models\DeclarantRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class CommonLogController extends Controller
{
        public function index()
    {
        return view('backend.support-module.index');
    }

    public function getAjaxSupportModule()
    {
        $requestedYear = trim((string) request('year', ''));
        $currentYear = preg_match('/^\d{4}$/', $requestedYear) ? (int) $requestedYear : now()->year;
        
        $passportColumn = Schema::hasColumn('declarant_personal_infos', 'passwprt') ? 'passwprt' : 'passport';

        $logSummary = DB::table('common_logs')
            ->select('user_id')
            ->selectRaw('MAX(created_at) as last_seen_at')
            ->selectRaw('COUNT(*) as total_logs')
            ->whereYear('created_at', $currentYear)
            ->groupBy('user_id');

        // 2. MAIN QUERY
        $model = DeclarantRegistration::query()
            ->select('declarant_registrations.*', 'summary.last_seen_at', 'summary.total_logs')
            ->leftJoinSub($logSummary, 'summary', function ($join) {
                $join->on('declarant_registrations.id', '=', 'summary.user_id');
            })
            ->with(['personalInfo'])
            ->orderByRaw('summary.last_seen_at DESC NULLS LAST')
            ->with(['logs' => function ($query) use ($currentYear) {
                $query->whereYear('created_at', $currentYear)->latest()->limit(1);
            }]);

        return DataTables::eloquent($model)
            ->filter(function ($query) use ($passportColumn): void {
                $searchAll = request('search_all');
                $email = trim((string) request('email', ''));
                $passport = trim((string) request('passport', ''));
                $mobile = trim((string) request('mobile', ''));
                
                if (!empty($searchAll)) {
                    $keyword = '%' . strtolower(trim($searchAll)) . '%';
                    $query->where(function($q) use ($keyword, $passportColumn) {
                        $q->whereRaw('LOWER(declarant_registrations.nic) LIKE ?', [$keyword])
                        ->orWhereRaw("LOWER(CONCAT(COALESCE(declarant_registrations.surname, ''), ' ', COALESCE(declarant_registrations.other_names, ''))) LIKE ?", [$keyword])
                        ->orWhereRaw('LOWER(declarant_registrations.email) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(declarant_registrations.mobile_no) LIKE ?', [$keyword]);

                        $q->orWhereHas('personalInfo', function ($sub) use ($keyword, $passportColumn) {
                            $sub->whereRaw("LOWER({$passportColumn}) LIKE ?", [$keyword]);
                        });
                    });
                }

                // INDIVIDUAL FILTERS
                if ($nic = trim((string) request('nic', ''))) {
                    $query->whereRaw('LOWER(declarant_registrations.nic) LIKE ?', ["%".strtolower($nic)."%"]);
                }
                if ($name = trim((string) request('name', ''))) {
                    $query->whereRaw("LOWER(CONCAT(COALESCE(declarant_registrations.surname, ''), ' ', COALESCE(declarant_registrations.other_names, ''))) LIKE ?", ["%".strtolower($name)."%"]);
                }

                if ($email !== '') {
                    $searchTerm = strtolower(trim($email));
                    $query->whereRaw('LOWER(declarant_registrations.email) LIKE ?', ["%{$searchTerm}%"]);
                }

                if ($passport !== '') {
                    $query->whereHas('personalInfo', function ($q) use ($passport, $passportColumn) {
                        $q->whereRaw("LOWER({$passportColumn}) LIKE ?", ['%' . strtolower($passport) . '%']);
                    });
                }

                if ($mobile !== '') {
                    $cleanMobile = preg_replace('/[^0-9]/', '', (string)$mobile);
                    $normalizedMobile = preg_replace('/^(94|0)/', '', $cleanMobile);
                    $query->where('declarant_registrations.mobile_no', 'LIKE', "%{$normalizedMobile}%");
                }
            })
            ->addIndexColumn()
            ->editColumn('name', fn($user) => trim(($user->surname ?? '') . ' ' . ($user->other_names ?? '')))
            
            ->orderColumn('last_seen', function ($query, $order) {
                $query->orderByRaw("last_seen_at $order NULLS LAST");
            })
            ->orderColumn('total_activities', function ($query, $order) {
                $query->orderBy('total_logs', $order);
            })
            
            ->addColumn('total_activities', fn($user) => $user->total_logs ?? 0)
            ->addColumn('last_activity_type', fn($user) => $user->logs->first()->activity_type ?? 'N/A')
            ->editColumn('last_seen', fn($user) => $user->last_seen_at 
                ? \Carbon\Carbon::parse($user->last_seen_at)->format('Y-m-d H:i:s') 
                : 'N/A'
            )
            ->addColumn('view', function ($row) {
                return '<button type="button" class="btn btn-info btn-xs btn-icon rounded-circle view-logs" data-id="' . encrypt($row->id) . '">
                            <i class="fal fa-eye"></i>
                        </button>';
            })
            ->rawColumns(['view'])
            ->toJson();
    }


    public function getUserLogs($id)
    {
        try {
            $userId = decrypt($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid ID'], 400);
        }

        $currentYear = now()->year;

        $logs = CommonLog::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->latest()
            ->get();
        
        $user = DeclarantRegistration::where('id', $userId)->first();
        return response()->json(['data' => $logs , 
        'user' => $user]);
    }

}
