<?php

namespace App\Http\Controllers\Api\Login;

use App\Events\EmailEvent;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmail;
use App\Jobs\SendSmsJob;
use App\Mail\OtpLoginMail;
use App\Mail\OtpMail;
use App\Models\DeclarantRegistration;
use App\Trait\GeneratesOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class APILoginUserController extends Controller
{
    use GeneratesOtp;
    public function loginUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nationality_id' => 'required',
                'nic' => 'required|string|min:5',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => APIResponseMessage::Validation_Error,
                    'validation_errors' => $validator->errors(), // Changed key to 'validation_errors' to match your controller
                ], 422);
            }

            $user = DeclarantRegistration::where('nationality_id', $request->nationality_id)
                ->where('nic', $request->nic)
                ->where('status', 'V')
                ->first();


            if (!$user) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => APIResponseMessage::DATAFETCHEDFAILED,
                    'error' => [
                        'status' => 'no_user',
                        'message' => 'Please Register First',
                        'status_code' => 401
                    ],
                ], 401);
            }

            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => APIResponseMessage::DATAFETCHEDFAILED,
                    'error' => [
                        'status' => 'incorrect_credentials',
                        'message' => 'Incorrect credentials. Please try again.',
                        'status_code' => 401
                    ],
                ], 401);
            }

            $generatedOtp = $this->generateOtp();
            $user->mobile_otp = $generatedOtp;
            $user->email_otp = $generatedOtp;
            $user->mobile_otp_expires_at = now()->addMinutes(5);
            $user->email_otp_expires_at = now()->addMinutes(5);
            $user->save();

            $mobileNumber = $user->country_code . $user->mobile_no;

            $message = "Dear Declarant,\n\n" .
                    "A login attempt was made to access your Declaration of Assets & Liabilities profile.\n\n" .
                    "Your One-Time Password (OTP) is: " . $generatedOtp . "\n\n" .
                    "Please enter this code to securely proceed with the login.\n\n" .
                    "For security reasons, do not share this OTP with anyone.";

            SendSmsJob::dispatch($mobileNumber, $message);

            SendOtpEmail::dispatch(
                $user,
                $generatedOtp
            );

            // Event::dispatch(new EmailEvent(
            //     $user->email,
            //     $generatedOtp,
            //     'login',
            // ));

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'status_code' => 200,
                'message' => APIResponseMessage::DATAFETCHED,
                'token' => $token,
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'status' => 'error',
                    'message' => APIResponseMessage::DATAFETCHEDFAILED,
                    'error' => [
                        'message' => $e->getMessage(),
                        'status_code' => 500
                    ],
                ]
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $user = $request->user();

            $user->tokens->each(function ($token) {
                $token->delete();
            });

            $user->save();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'User logged out successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::DATAFETCHEDFAILED,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::DATAFETCHEDFAILED,
                'error' => $e->getMessage(),
            ], 500);
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

        $declarant = DeclarantRegistration::where('nic', $request->nic)->where('status', '!=', 'S')->first();

        if (!$declarant) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'No registration found for the provided NIC.',
                'statusCode' => 404,
            ]);
        }

        if ($declarant->mobile_otp !== $request->otp) {
            return response()->json([
                'status' => 'invalid_otp',
                'message' => 'Your OTP has.',
                'statusCode' => 401,
            ]);
        }

        if (now()->greaterThan($declarant->mobile_otp_expires_at)) {
            return response()->json([
                'status' => 'otp_expire',
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
    }

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

                SendOtpEmail::dispatch(
                    $declarant,
                    $generatedOtp
                );

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

}
