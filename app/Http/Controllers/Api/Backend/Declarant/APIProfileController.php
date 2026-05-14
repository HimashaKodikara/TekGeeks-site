<?php

namespace App\Http\Controllers\Api\Backend\Declarant;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\DeclarantRegistration;
use App\Models\DeclarantCoveredPersonPersonalInfo;
use Illuminate\Support\Facades\Log;

class APIProfileController extends Controller
{
    public function getList()
    {
        $data = DeclarantRegistration::select('id', 'surname', 'other_names', 'nic', 'mobile_no', 'email', 'status')
            ->get();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'data' => $data
            ],
        ], 200);
    }

    public function getRecord($id)
    {
        $profile = DeclarantRegistration::select('id', 'surname', 'other_names', 'nic', 'mobile_no', 'email', 'status')
            ->where('id', $id)
            ->first();

        if (!$profile) {
            return response()->json([
                'status' => APIResponseMessage::FAILED_STATUS,
                'message' => 'Profile not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'data' => $profile
            ],
        ], 200);
    }

    public function updateRecord(Request $request)
    {
        // Validate request
        $request->validate([
            'id'       => 'required|exists:declarant_registrations,id',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'
            ],
            'status' => 'nullable|in:P,V,D',
        ]);

        // Fetch declarant
        $declarant = DeclarantRegistration::find($request->id);

        if (!$declarant) {
            return response()->json([
                'status' => APIResponseMessage::FAILED_STATUS,
                'message' => 'Declarant not found',
                'data' => null
            ], 404);
        }

        // Update record
        if ($request->filled('password')) {
            $declarant->password = bcrypt($request->password);
        }

        if ($request->filled('status')) {
            $declarant->status = $request->status;
        }

        $declarant->save();

        return response()->json([
            'status'  => APIResponseMessage::SUCCESS_STATUS,
            'message' => 'Profile updated successfully',
            'data'    => [
                'id'    => $declarant->id
            ]
        ], 200);
    }

    public function getCoveredPersons(Request $request)
    {
        // Validate request
        $request->validate([
            'declarant_registration_id' => 'required|exists:declarant_registrations,id',
        ]);

        // Fetch declarant
        $data = DeclarantCoveredPersonPersonalInfo::where('declarant_registration_id', $request->declarant_registration_id)->get();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'data' => $data
            ],
        ], 200);
        
    }
    
}
