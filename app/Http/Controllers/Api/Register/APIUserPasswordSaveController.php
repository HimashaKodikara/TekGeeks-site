<?php

namespace App\Http\Controllers\Api\Register;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Jobs\SendSuccessfullEmail;
use App\Mail\SuccessfullyRegisteredEmail;
use App\Models\DeclarantRegistration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class APIUserPasswordSaveController extends Controller
{
    public function storePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nic' => 'required|string',
            'password' => 'required|string|min:8',
            'validityToken' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';

        if($request->validityToken ===  $validityToken) {

            try{
                DB::beginTransaction();

                $declarantRegistration = DeclarantRegistration::where('nic', $request->nic)->where('email_otp_verification', 'V')->where('status', '!=', 'S')->first();

                if($declarantRegistration->status == "P") {
                    $declarantRegistration->status = "V";

                    // Mail::to($declarantRegistration->email)->send(new SuccessfullyRegisteredEmail($declarantRegistration->nic));
                    SendSuccessfullEmail::dispatch($declarantRegistration->email, $declarantRegistration->nic);
                }

                $declarantRegistration->password = bcrypt($request->password);
                $declarantRegistration->save();

                DB::commit();

                return response()->json([
                    'status' => APIResponseMessage::SUCCESS_STATUS,
                    'message' => APIResponseMessage::DATAFETCHED,
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => APIResponseMessage::UNAUTHORIZED,
                ], 500);
            }

        }else{
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ], 401);

        }
    }
}
