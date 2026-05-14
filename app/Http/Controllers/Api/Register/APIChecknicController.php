<?php

namespace App\Http\Controllers\Api\Register;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\DeclarantRegistration;
use App\Models\Nationality;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class APIChecknicController extends Controller
{
    public function checkNic(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'nic' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';

        $nic = $request->nic;
        $nationalityId = $request->nationality_id;

        if($request->validityToken ===  $validityToken) {

            $declarant = DeclarantRegistration::where('nationality_id', $nationalityId)->where('nic', $nic)->where('mobile_otp_verification','V')->where('email_otp_verification','V')->where('status', 'V')->first();

            // $declarant = DeclarantRegistration::whereRaw('LOWER(nic) = ?', [strtolower($nic)])->where('mobile_otp_verification','V')->where('email_otp_verification','V')->first();

            return response()->json([

                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'exists' => $declarant ? true : false,
                    'mobile_no' => $declarant->mobile_no ?? null,
                    'email' => $declarant->email ?? null,
                ],
            ], 200);
        }else{
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ], 401);
        }
    }

    public function getRegistrationMasterData(Request $request)
    {
        $lang = $request->lang;

        $nationalities = Nationality::where('status', 'Y')->where('is_delete', 0)->get();

        $countryCodes = Country::where('status', 'Y')->where('is_delete', 0)->select('country_code')->orderBy('id')->get();

        if ($nationalities->isEmpty()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::NODATA,
            ], 200);
        }

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'nationalities' => $nationalities,
                'countryCodes' => $countryCodes
                ],
        ], 200);
    }

}
