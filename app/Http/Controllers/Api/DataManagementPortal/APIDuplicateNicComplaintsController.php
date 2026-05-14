<?php

namespace App\Http\Controllers\Api\DataManagementPortal;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Helpers\LangHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\DeclarantCoveredPersonEmploymentInfo;
use App\Models\DeclarantCoveredPersonOtherCountryInfo;
use App\Models\DeclarantCoveredPersonPersonalInfo;
use Illuminate\Support\Facades\Validator;

class APIDuplicateNicComplaintsController extends Controller
{
    public function getDuplicateNicComplaint(Request $request)
    {
        $coveredPersons = DeclarantCoveredPersonPersonalInfo::with('relationshipwithdeclarant')->where('declarant_registration_id', $request->declarant_registration_id)
                        ->where('is_delete',0)
                        ->get();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'coveredPersons' => $coveredPersons
            ],
        ], 200);
    }
}
