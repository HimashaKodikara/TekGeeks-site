<?php

namespace App\Http\Controllers\Api\Register;

use App\Events\EmailEvent;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmail;
use App\Jobs\SendOtpEmailRegistration;
use App\Jobs\SendSmsJob;
use App\Mail\OtpMail;
use App\Mail\SuccessfullyRegisteredEmail;
use App\Models\DeclarantRegistration;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class APIRegisterOTPController extends Controller
{
    public function sendMobileOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nationality_id' => 'sometimes|required',
            'nic' => 'required|string|max:12',
            'country_code' => 'sometimes|required',
            'mobile_no' => 'required|string|digits_between:7,15',
            'surname' => 'sometimes|required|string|max:255',
            'other_names' => 'sometimes|required|string|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ]);
        }

        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';

        if($request->validityToken ===  $validityToken) {

            $nationalityId = $request->input('nationality_id');
            $countryCode = $request->input('country_code');
            $mobileNo = $request->input('mobile_no');
            $nic = $request->nic;
            $generatedOtp = $this->generateOtp();
            $expiresAt = now()->addMinutes(5);

            DB::beginTransaction();
            try {

                $declarant = DeclarantRegistration::where('nic', $nic)
                ->where('status', '!=', 'S')
                ->firstOrNew();

                if (!$declarant->exists) {
                    $declarant->nic = $request->nic;
                }

                if($request->surname) {
                    $declarant->surname = $request->surname;
                }

                if($request->other_names) {
                    $declarant->other_names = $request->other_names;
                }

                if($nationalityId) {
                    $declarant->nationality_id = $nationalityId;
                }

                if($countryCode) {
                    $declarant->country_code = $countryCode;
                }

                if($mobileNo) {
                    $declarant->mobile_no = $mobileNo;
                }

                $declarant->mobile_otp = $generatedOtp;
                $declarant->mobile_otp_expires_at = $expiresAt;
                $declarant->save();

                $mobileNumber = $declarant->country_code.$declarant->mobile_no;

                $message = "Dear Declarant,\n\n" .
                    "A request has been made in the Declaration of Assets & Liabilities system.\n\n" .
                    "Your One-Time Password (OTP) is: " . $generatedOtp . "\n\n" .
                    "Please use this code to proceed with your request.\n\n" .
                    "For security reasons, do not share this OTP with anyone.";

                SendSmsJob::dispatch($mobileNumber, $message);

                // if($declarant->status == 'V') {
                //     SendOtpEmail::dispatch(
                //         $declarant,
                //         $generatedOtp
                //     );
                // }

                DB::commit();
                return response()->json([
                    'status' => APIResponseMessage::SUCCESS_STATUS,
                    'message' => 'OTP has been successfully sent to your mobile number.',
                    'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Could not send OTP. Please try again later.'
                ]);
            }
        }else{
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ]);
        }
    }

    public function verifyMobileOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nic' => 'required|string|max:12',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ]);
        }

        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';

        if($request->validityToken === $validityToken) {

            $declarant = DeclarantRegistration::where('nic', $request->nic)->where('status', '!=', 'S')->first();

            if (!$declarant) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'No registration found for the provided NIC.',
                    'statusCode' => 404,
                ]);
            }

            if (now()->greaterThan($declarant->mobile_otp_expires_at)) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Your OTP has expired. Please request a new one.',
                    'statusCode' => 401,
                ]);
            }

            if ($declarant->mobile_otp === $request->otp && now()->lessThanOrEqualTo($declarant->mobile_otp_expires_at)) {

                if($declarant->mobile_otp_verification === "P") {
                    $declarant->mobile_otp_verification = "V";
                    $declarant->save();
                }

                return response()->json([
                    'status' => APIResponseMessage::SUCCESS_STATUS,
                    'message' => 'OTP verified successfully.',
                    'statusCode' => 200,
                ]);
            } else {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Invalid or expired OTP.',
                    'statusCode' => 400,
                ]);
            }
        } else {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ]);
        }
    }

    public function sendEmailOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nic' => 'required|string|max:12',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ]);
        }

        $nic = $request->input('nic');
        $email = $request->input('email');

        try {

            $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';

            if($request->validityToken !== $validityToken) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => APIResponseMessage::UNAUTHORIZED,
                ]);
            }

            $declarantProfileInfo = DeclarantRegistration::where('nic', $nic)->where('status', '!=', 'S')->first();

            if (!$declarantProfileInfo) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Registration record not found for the provided NIC.',
                ]);
            }

            if (empty($declarantProfileInfo->email) || $declarantProfileInfo->email !== $email) {
                $declarantProfileInfo->email = $email;
            }

            $generatedOtp = $this->generateOtp();
            $expiresAt = now()->addMinutes(5);

            $declarantProfileInfo->email_otp = $generatedOtp;
            $declarantProfileInfo->email_otp_expires_at = $expiresAt;
            $declarantProfileInfo->save();

            $declarantEmail = $declarantProfileInfo->email;

            // Mail::to($declarantProfileInfo->email)->send(new OtpMail($generatedOtp));
            if($declarantProfileInfo->status == 'V') {
                SendOtpEmail::dispatch($declarantProfileInfo, $generatedOtp);
                // Event::dispatch(new EmailEvent(
                //     $declarantProfileInfo->email,
                //     $generatedOtp,
                //     'login',
                // ));

            } else {
                SendOtpEmailRegistration::dispatch($declarantEmail, $generatedOtp);
                // Event::dispatch(new EmailEvent(
                //     $declarantProfileInfo->email,
                //     $generatedOtp,
                //     'registration',
                // ));
            }

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Verification code successfully sent to email.',
                'expires_at' => $expiresAt->toIso8601String(),
            ]);

        } catch (\Throwable $e) {
            Log::error("Email OTP sending failed for NIC {$nic}: " . $e->getMessage());

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An error occurred while attempting to send the email OTP.',
            ]);
        }
    }


    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nic' => ['required', 'string', 'max:12'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ]);
        }

        try{

            $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';

            if($request->validityToken !== $validityToken) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => APIResponseMessage::UNAUTHORIZED,
                ]);
            }

            $declarant = DeclarantRegistration::where('nic', $request->nic)->where('status', '!=', 'S')->first();

            if (!$declarant) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'No registration found for the provided NIC.',
                    'statusCode' => 404,
                ]);
            }

            if ($declarant->email_otp === $request->otp && now()->lessThanOrEqualTo($declarant->email_otp_expires_at)) {

                $declarant->email_otp_verification = "V";
                $declarant->save();

                // Mail::to($declarant->email)->send(new SuccessfullyRegisteredEmail($declarant->nic));

                return response()->json([
                    'status' => APIResponseMessage::SUCCESS_STATUS,
                    'message' => 'OTP verified successfully.',
                    'statusCode' => 200,
                ]);
            } else {
                Log::info("Failed email OTP verification for NIC {$request->nic}: Provided OTP - {$request->otp}, Expected OTP - {$declarant->email_otp}, Expiry Time - {$declarant->email_otp_expires_at}, Current Time - " . now());

                if (now()->greaterThan($declarant->email_otp_expires_at)) {
                    return response()->json([
                        'status' => APIResponseMessage::SUCCESS_STATUS,
                        'message' => 'Your OTP has expired. Please request a new one.',
                        'statusCode' => 401,
                    ]);
                }

                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Invalid or expired OTP.',
                    'statusCode' => 400,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Email OTP verification failed for NIC {$request->nic}: " . $e->getMessage());

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An error occurred while attempting to verify the email OTP.',
            ]);
        }
    }


    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
