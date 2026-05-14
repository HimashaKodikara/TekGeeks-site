<?php

namespace App\Http\Controllers\Backend\InstitutePortal;

use App\Http\Controllers\Controller;
use App\Models\MonetaryInstituteDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class InstituteDashboardPortalController extends Controller
{
    public function index(Request $request)
    {

        return view('backend.institute-portal.dashboard');
    }

    public function getAjaxDesignationsUser()
    {
        $monetaryInstituteId = Auth::user()->designation_id;

        $inner = MonetaryInstituteDetail::query()
            ->from('monetary_institute_details as mid')
            ->where('mid.monetary_institute_id', $monetaryInstituteId)
            ->leftJoin('public_authorities as pa', 'pa.id', '=', 'mid.public_authority_id')
            ->leftJoin('status_of_declarations as sod', fn($j) => $j
                ->on('sod.institution_id', '=', 'mid.public_authority_id')
                ->on('sod.designation_id', '=', 'mid.designation_id')
                ->where('sod.is_delete', '=', 0)
            )
            ->selectRaw("DISTINCT ON (mid.public_authority_id)
                mid.id,
                mid.public_authority_id,
                pa.name_en as institute_name,
                COUNT(DISTINCT sod.declarant_registration_id) as total_count,
                COUNT(CASE WHEN sod.declaration_type_id = 2 AND sod.status IN ('S', 'E') THEN 1 END) as annual_started_count,
                COUNT(CASE WHEN sod.declaration_type_id = 2 AND sod.status IN ('C', 'R') THEN 1 END) as annual_completed_count,
                COUNT(CASE WHEN sod.declaration_type_id != 2 AND sod.status IN ('S', 'E') THEN 1 END) as other_started_count,
                COUNT(CASE WHEN sod.declaration_type_id != 2 AND sod.status IN ('C', 'R') THEN 1 END) as other_completed_count
            ")
            ->groupBy('mid.id', 'mid.public_authority_id', 'pa.name_en')
            ->orderBy('mid.public_authority_id')
            ->orderBy('mid.id');

        $data = DB::table(DB::raw("({$inner->toSql()}) as institutes"))
            ->mergeBindings($inner->getQuery())
            ->selectRaw('*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('publicAuthorityName', function ($data) {
                return $data->institute_name;
            })
            ->filterColumn('publicAuthorityName', function ($query, $keyword) {
                $query->where('institute_name', 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('publicAuthorityName', 'institute_name $1')
            ->addColumn('instituteCount', function ($data) {
                return '<span class="badge text-dark badge-secondary text-center">'.$data->total_count.'</span>';
            })
            ->addColumn('annualStatus', function ($data) {
                return '<span class="badge badge-warning text-dark px-2 py-1 m-1">Started: ' . ($data->annual_started_count ?? 0) . '</span>' .
                    '<span class="badge badge-success text-dark px-2 py-1 m-1">Completed: ' . ($data->annual_completed_count ?? 0) . '</span>';
            })
            ->addColumn('otherStatus', function ($data) {
                return '<span class="badge badge-warning text-dark px-2 py-1 m-1">Started: ' . ($data->other_started_count ?? 0) . '</span>' .
                    '<span class="badge badge-success text-dark px-2 py-1 m-1">Completed: ' . ($data->other_completed_count ?? 0) . '</span>';
            })
            ->addColumn('edit', function ($data) {
                $route = route('institute.check-designtation', encrypt($data->public_authority_id));
                return '<a href="'.$route.'" class="btn btn-sm btn-outline-primary btn-icon rounded-circle waves-effect waves-themed">
                            <i class="fal fa-arrow-right"></i>
                        </a>';
            })
            ->rawColumns(['publicAuthorityName', 'instituteCount', 'annualStatus', 'otherStatus', 'edit'])
            ->toJson();
    }
}
