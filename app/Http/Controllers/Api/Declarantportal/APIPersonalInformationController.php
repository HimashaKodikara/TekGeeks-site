<?php

namespace App\Http\Controllers\Api\Declarantportal;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\ConfirmChangeEmailUpdateJob;
use App\Jobs\SendOtpChangeEmailPortalJob;
use App\Jobs\SendSmsJob;
use App\Mail\OtpMail;
use App\Models\DeclarantEmploymentInfo;
use App\Models\DeclarantOtherCountryInfo;
use App\Models\DeclarantPersonalInfo;
use App\Models\DeclarantRegistration;
use App\Models\DesignationClass;
use App\Models\EmailChangeLog;
use App\Models\MobileNumberChangeLog;
use App\Models\PublicAuthority;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class APIPersonalInformationController extends Controller
{
    public function getProfileInfo(Request $request)
    {
        $profileInfo = DeclarantRegistration::select('id','surname','other_names','nic','mobile_no','email','nationality_id','country_code')
            ->where('id', $request->declarant_registration_id)
            ->first();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'profileInfo' => $profileInfo
            ],
        ], 200);
    }

    public function updateProfileInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'surname' => 'required',
            'other_names' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $profileInfo = DeclarantRegistration::findOrFail($request->declarant_registration_id);

            $profileInfo->update($request->only([
                'surname',
                'other_names'
            ]));

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Profile information updated successfully.',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getPersonalInfo(Request $request)
    {
        $personalInfo = DeclarantPersonalInfo::where('declarant_registration_id', $request->declarant_registration_id)
            ->first();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'personalInfo' => $personalInfo // will be null if not found
            ],
        ], 200);
    }

    public function updatePersonalInfo(Request $request)
    {
        $personal = $request->input('personal_info');
        $foreign = $request->input('foreign_residencies', []);
        $deleted = $request->input('deleted_foreign_residencies', []);
        $employment = $request->input('employments', []);
        $deletedEmp = $request->input('deleted_employments', []);

        $validator = Validator::make($personal, [
            'declarant_registration_id' => 'required|integer',
            'full_name' => 'required|string|max:250',
            'name_with_initials' => 'required|string|max:250',
            'date_of_birth' => 'required|date',
            'permanent_country_id' => 'required',
            'current_country_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (($personal['do_hold_other_country_residency'] ?? 'N') !== 'Y') {
            $foreign = [];
            $deleted = [];
        }

        $doHold = $personal['do_hold_other_country_residency'] ?? 'N';

        if (($personal['do_hold_employment_details'] ?? 'N') !== 'Y') {
            $employment = [];
            $deletedEmp = [];
        }

        $doHoldEmp = $personal['do_hold_employment_details'] ?? 'N';

        DB::beginTransaction();

        try {

            $profileInfo = DeclarantPersonalInfo::firstOrNew(
                ['declarant_registration_id' => $personal['declarant_registration_id']]
            );

            if (!$profileInfo->exists) {
                $profileInfo->created_by = $personal['created_by'] ?? null;
            }

            $profileInfo->fill($personal);
            $profileInfo->save();

            if ($doHold !== 'Y') {
                DeclarantOtherCountryInfo::where('declarant_registration_id', $profileInfo->declarant_registration_id)
                    ->update(['is_delete' => 1]);
            }


            if (!empty($deleted)) {
                DeclarantOtherCountryInfo::where('declarant_registration_id', $profileInfo->declarant_registration_id)
                    ->whereIn('id', $deleted)
                    ->update(['is_delete' => 1]);
            }

            foreach ($foreign as $r) {

                // skip invalid rows
                if (empty($r['country_id']) || empty($r['status_id'])) {
                    continue;
                }

                // ✅ if it already exists (saved row), DO NOTHING (no duplicates)
                if (!empty($r['id'])) {
                    continue;
                }

                // ✅ new row only
                DeclarantOtherCountryInfo::create([
                    'declarant_registration_id' => $profileInfo->declarant_registration_id,
                    'foreign_country_id' => $r['country_id'],
                    'foreign_country_name' => $r['country_name'] ?? null,
                    'residency_status_id' => $r['status_id'],
                    'residency_status_name' => $r['residency_status_name'] ?? null,
                    'is_delete' => 0,
                ]);
            }

            if ($doHoldEmp !== 'Y') {
                DeclarantEmploymentInfo::where('declarant_registration_id', $profileInfo->declarant_registration_id)
                    ->update(['is_delete' => 1]);
            }


            if (!empty($deletedEmp)) {
                DeclarantEmploymentInfo::where('declarant_registration_id', $profileInfo->declarant_registration_id)
                    ->whereIn('id', $deletedEmp)
                    ->update(['is_delete' => 1]);
            }

            foreach ($employment as $emp) {

                if (empty($emp['institution_id']) || empty($emp['designation_id'])) {
                    continue;
                }

                // ✅ already saved → DO NOTHING
                if (!empty($emp['id'])) {
                    continue;
                }

                // ✅ new row only
                DeclarantEmploymentInfo::create([
                    'declarant_registration_id' => $profileInfo->declarant_registration_id,
                    'institution_id' => $emp['institution_id'],
                    'institution_name' => $emp['institution_name'] ?? null,
                    'designation_id' => $emp['designation_id'],
                    'designation_name' => $emp['designation_name'] ?? null,
                    'office_address' => $emp['office_address'] ?? null,
                    'country_code_office_mobile' => $emp['country_code_office_mobile'] ?? null,
                    'office_mobile_no' => $emp['office_mobile_no'] ?? null, // ✅ mapping fix
                    'is_delete' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Profile information updated successfully.',
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllOtherCountryInfo(Request $request)
    {

        $otherCountryInfo = DeclarantOtherCountryInfo::with('countries', 'visaTypes')
                ->where('declarant_registration_id', $request->declarant_registration_id)
                ->where('is_delete', 0)
                ->get();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'otherCountryInfo' => $otherCountryInfo,
            ],
        ], 200);

    }

    public function getEmploymentInfo(Request $request)
    {

        $employmentInfo = DeclarantEmploymentInfo::where('declarant_registration_id', $request->declarant_registration_id)
                ->where('is_delete', 0)
                ->get();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'employment_info' => $employmentInfo,
            ],
        ], 200);

    }

    //NEW FUNCTIONS AFTER CHANGE THE SYSTEM
    public function sendRequestChangeMobile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'mobile_no' => 'required|string|digits_between:9,15',
            'country_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $mobileNo = $request->input('mobile_no');
        $generatedOtp = $this->generateOtp();
        $expiresAt = now()->addMinutes(5);

        DB::beginTransaction();
        try {
            $newMobileNumberChangeLog = MobileNumberChangeLog::where('new_mobile_no', $mobileNo)->whereNull('verified_at')->latest()->first();

            if (!$newMobileNumberChangeLog) {

                $newMobileNumberChangeLog = new MobileNumberChangeLog();
                $newMobileNumberChangeLog->declarant_registration_id = $request->declarant_registration_id;
                $newMobileNumberChangeLog->country_code = $request->country_code;
                $newMobileNumberChangeLog->new_mobile_no = $mobileNo;
            }

            $newMobileNumberChangeLog->mobile_otp = $generatedOtp;
            $newMobileNumberChangeLog->mobile_otp_expires_at = $expiresAt;
            $newMobileNumberChangeLog->save();

            $mobileNumber = $newMobileNumberChangeLog->country_code . $newMobileNumberChangeLog->new_mobile_no;

            $message = "Dear Declarant,\n\n" .
                "A request has been made to verify a new mobile number for your Declaration of Assets & Liabilities profile. \n\n" .
                "Your One-Time Password (OTP) is: " . $generatedOtp . "\n\n" .
                "Please enter this code to confirm the new mobile number.\n\n" .
                "For security reasons, do not share this OTP with anyone.";

            SendSmsJob::dispatch($mobileNumber, $message);

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'OTP has been successfully sent to your new mobile number.',
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Could not send OTP. Please try again later.'
            ], 500);
        }
    }

    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function checkMobileNoExistence(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $mobileNo = $request->mobile_no;

        try {
            $declarant = DeclarantRegistration::where('mobile_no', $mobileNo)->first();

            // $declarant = DeclarantRegistration::whereRaw('LOWER(nic) = ?', [strtolower($nic)])->where('mobile_otp_verification','V')->where('email_otp_verification','V')->first();

            return response()->json([

                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'exists' => $declarant ? true : false,
                ],
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyNewMobileOtp(Request $request): JsonResponse
    {
        Log::info('request to verify change mobile number');

        Log::info($request->all());

        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'mobile_no' => 'required|string|max:10',
            'otp' => 'required|string|size:6',
        ]);

        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'mobile_no' => 'required|string|max:10',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $mobileNumberChangeLog = MobileNumberChangeLog::where('declarant_registration_id', $request->declarant_registration_id)->where('new_mobile_no', $request->mobile_no)->latest()->first();

        Log::info($mobileNumberChangeLog);

        if (!$mobileNumberChangeLog) {
            Log::info('A');
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'No mobile number change request found for the provided mobile number.',
                'statusCode' => 404,
            ], 404);
        }

        if ($mobileNumberChangeLog->mobile_otp === $request->otp && now()->lessThanOrEqualTo($mobileNumberChangeLog->mobile_otp_expires_at)) {
            $mobileNumberChangeLog->verified_at = Carbon::now();
            $mobileNumberChangeLog->save();

            Log::info('B');

            $updateDecRegMobileNo = DeclarantRegistration::find($request->declarant_registration_id);
            $updateDecRegMobileNo->mobile_no = $request->mobile_no;
            $updateDecRegMobileNo->save();

            $mobileNumber = $updateDecRegMobileNo->country_code . $updateDecRegMobileNo->mobile_no;

            $message = "Dear Declarant,\n\n" .
                "Your registered mobile number in the Declaration of Assets & Liabilities system has been successfully updated. \n\n" .
                "Please ensure that your contact details remain accurate and secure for future communications. \n\n" .
                "If this change was not made by you, please report it immediately to the relevant authorities.";

            // SendSmsJob::dispatch($mobileNumber, $message);

            Log::info('D');

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'OTP verified successfully.',
                'statusCode' => 200,
            ], 200);
        } else {
            if(now()->greaterThan($mobileNumberChangeLog->mobile_otp_expires_at)) {
                Log::info('C');
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Your OTP has expired. Please request a new one.',
                    'statusCode' => 401,
                ]);
            } else {
                Log::info('E');
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Invalid OTP.',
                    'statusCode' => 400,
                ]);
            }
        }
    }

    public function sendRequestChangeEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');
        $generatedOtp = $this->generateOtp();
        $expiresAt = now()->addMinutes(5);

        DB::beginTransaction();
        try {
            $newEmailNumberChangeLog = EmailChangeLog::where('new_email', $email)->whereNull('verified_at')->latest()->first();

            if (!$newEmailNumberChangeLog) {
                $newEmailNumberChangeLog = new EmailChangeLog();
                $newEmailNumberChangeLog->declarant_registration_id = $request->declarant_registration_id;
                $newEmailNumberChangeLog->new_email = $email;
            }

            $newEmailNumberChangeLog->email_otp = $generatedOtp;
            $newEmailNumberChangeLog->email_otp_expires_at = $expiresAt;
            $newEmailNumberChangeLog->save();

            SendOtpChangeEmailPortalJob::dispatch($email,$generatedOtp);

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'OTP has been successfully sent to your new email.',
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Could not send OTP. Please try again later.'
            ], 500);
        }
    }

    public function verifyNewEmailOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'email' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $EmailChangeLog = EmailChangeLog::where('declarant_registration_id', $request->declarant_registration_id)->where('new_email', $request->email)->latest()->first();

        if (!$EmailChangeLog) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'No any email change request found for the provided email.',
                'statusCode' => 404,
            ], 404);
        }

        if ($EmailChangeLog->email_otp === $request->otp && now()->lessThanOrEqualTo($EmailChangeLog->email_otp_expires_at)) {
            $EmailChangeLog->verified_at = Carbon::now();
            $EmailChangeLog->save();

            $updateDecRegEmail = DeclarantRegistration::find($request->declarant_registration_id);
            $updateDecRegEmail->email = $request->email;
            $updateDecRegEmail->save();

            $updatedDate = Carbon::now();
            $newEmail = $updateDecRegEmail->email;

            // Mail::to('ayodhya@tekgeeks.net')->send(new OtpMail($generatedOtp));
            ConfirmChangeEmailUpdateJob::dispatch($updatedDate, $newEmail);

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'OTP verified successfully.',
                'statusCode' => 200,
            ], 200);

        } else {
            if(now()->greaterThan($EmailChangeLog->email_otp_expires_at)) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Your OTP has expired. Please request a new one.',
                    'statusCode' => 401,
                ]);
            } else {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Invalid OTP.',
                    'statusCode' => 400,
                ]);
            }
        }
    }

}
