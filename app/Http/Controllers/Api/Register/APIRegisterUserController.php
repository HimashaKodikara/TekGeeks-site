<?php

namespace App\Http\Controllers\Api\Register;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\DeclarantRegistration;
use Illuminate\Support\Facades\Validator;

class APIRegisterUserController extends Controller
{

    public function updateAttentionNoticeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nic' => 'required|string|max:15',
            'attention_notice_status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $declarantReg = DeclarantRegistration::where('nic', $request->nic)->where('status', '!=', 'S')->first();

            if (!$declarantReg) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Registration record not found.',
                ], 404);
            }

            $declarantReg->attention_notice_status = $request->attention_notice_status;
            $declarantReg->save();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Attention notice status updated successfully.',
                'data' => [
                    'nic' => $declarantReg->nic,
                    // 'status' => $declarantRec->status,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error("Attention notice status update failed: " . $e->getMessage(), ['nic' => $request->nic]);

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function createUserProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nic' => 'required|string|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $declarantRec = DeclarantRegistration::where('nic', $request->nic)->where('status', '!=', 'S')->first();

            if (!$declarantRec) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Registration record not found for the provided NIC.',
                ], 404);
            }

            $mobileVerified = $declarantRec->mobile_otp_verification === "V";
            $emailVerified = $declarantRec->email_otp_verification === "V";

            if ($mobileVerified && $emailVerified) {
                // $declarantRec->status = "V";
                // $declarantRec->save();

                return response()->json([
                    'status' => APIResponseMessage::SUCCESS_STATUS,
                    'message' => 'User profile successfully completed and verified.',
                    'data' => [
                        'nic' => $declarantRec->nic,
                        // 'status' => $declarantRec->status,
                    ],
                ], 200);

            } else {
                $missingVerification = [];
                if (!$mobileVerified) $missingVerification[] = 'Mobile OTP';
                if (!$emailVerified) $missingVerification[] = 'Email OTP';

                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Verification incomplete. Please verify the following: ' . implode(' and ', $missingVerification),
                ], 403);
            }

        } catch (\Throwable $e) {
            Log::error("User profile creation failed: " . $e->getMessage(), ['nic' => $request->nic]);

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
