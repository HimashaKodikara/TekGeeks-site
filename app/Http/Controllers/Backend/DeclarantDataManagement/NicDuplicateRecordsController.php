<?php

namespace App\Http\Controllers\Backend\DeclarantDataManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\ApiDeclarantPortalService;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Throwable;

class NicDuplicateRecordsController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $mainTitle;
    private $title;

    protected ApiDeclarantPortalService $api;
    private $apiKey;

    public function __construct(ApiDeclarantPortalService $api)
    {
        $this->mainTitle = 'Duplicate NIC Records';
        $this->title = 'Duplicate NIC Record';
        $this->api = $api;
        $this->apiKey = config('services.ciaboc_backend_api.key');
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:nic-duplicate-records-list|nic-duplicate-records-view', only: ['list']),
            new Middleware('permission:nic-duplicate-records-view', only: ['index']),
        ];
    }

    public function list(Request $request)
    {
        $loggedUser = Auth::user();
        $loggeduserrole = $loggedUser->roles()->first();

        $mainTitle = $this->mainTitle;
        $title = $this->title;

        $postData = [
            'validityToken' => $this->apiKey,
        ];

        // API call
        $responseNicDuplicateComplaints = $this->api->get('get-duplicate-nic-complaints', $postData);

        if($responseNicDuplicateComplaints['status'] == 'success') {
            $complaints = $responseNicDuplicateComplaints['data']['duplicateComplaints'];
        } else {
            $complaints = [];
        }

        if ($request->ajax()) {

            $complaintsCollection = collect($complaints);

            return DataTables::of($complaintsCollection)
                ->addIndexColumn()
                ->addColumn('contact_number', function ($row) {
                    return $row['country_code'] . ' ' . $row['mobile_number'];
                })
                ->addColumn('view', function ($row) {
                    $view_url = url('adminpanel/declarant-data-management/nic-duplicate-records/view', encrypt($row['id']));

                    return '<a href="' . $view_url . '"><span class="btn btn-info btn-xs btn-icon rounded-circle"><i class="fal fa-eye"></i></span></a>';

                })

                ->rawColumns(['view'])
                ->make(true);
        }


        return view('backend.webportalmanagement.duplicatenicreports.list', compact('mainTitle','title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {

        $id = decrypt($id);

        $postData = [
            'validityToken' => $this->apiKey,
            'complaint_id' => $id,
        ];

        $response = $this->api->get('get-duplicate-nic-detail', $postData);

        $complaintDetail = $response['data']['complaintDetail'] ?? [];

        $mainTitle = $this->mainTitle;
        $title = $this->title;

        return view('backend.webportalmanagement.duplicatenicreports.view', compact('complaintDetail','mainTitle','title'));
    }

}
