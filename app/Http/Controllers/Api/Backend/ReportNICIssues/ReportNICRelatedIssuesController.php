<?php

namespace App\Http\Controllers\Api\Backend\ReportNICIssues;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Jobs\AccountSuspendReportOwnerEmailJob;
use App\Jobs\AcknowledgeNicMisuseReportEmailJob;
use App\Jobs\MisUseReportInvalidEmailJob;
use App\Models\DeclarantRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReportNICRelatedIssuesController extends Controller
{
    public function sendReceiveReportConfirmationEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'national_id_number' => 'required',
            'email' => 'required',
            'country_code' => 'required',
            'mobile_number' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $reportedMisuseComplaintData = $request->all();

        try {

            // Mail::to($request->email)->send(new AcknowledgeNICMisuseReportEmail($reportedMisuseComplaintData));

            AcknowledgeNicMisuseReportEmailJob::dispatch($request->email, $reportedMisuseComplaintData);

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [],
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    public function sendAccountSuspendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $accountSuspendDetails = $request->input('data');

        DB::beginTransaction();
        try {

            $misusedDeclarantDetails = DeclarantRegistration::where('nic', $accountSuspendDetails['national_id_number'])
                ->where('status', 'V')
                ->first();

            if (!$misusedDeclarantDetails) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => "Couldn't find the received NIC/Passport requested to suspend the account",
                ], 404);
            }

            $misusedDeclarantDetails->status = 'S';
            $misusedDeclarantDetails->mobile_otp_verification = 'S';
            $misusedDeclarantDetails->email_otp_verification = 'S';
            $misusedDeclarantDetails->save();

            $complaintRelatedDeclarantDetails = $misusedDeclarantDetails->toArray();

            $complaintRelatedDeclarantDetails['reference_no'] = $accountSuspendDetails['reference_no'];

            AccountSuspendReportOwnerEmailJob::dispatch($misusedDeclarantDetails->email,$complaintRelatedDeclarantDetails,$accountSuspendDetails['request_send_user_email'],$accountSuspendDetails);

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
            ], 200);

        } catch (\Throwable $e) {

            Log::error($e);

            DB::rollBack();

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendComplaintInvalidEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $accountSuspendDetails = $request->input('data');

        try {

            $sendEmail = $accountSuspendDetails['request_send_user_email'];

            // Mail::to($accountSuspendDetails['request_send_user_email'])->send(new MisuseReportInvalidEmail($accountSuspendDetails));
            MisUseReportInvalidEmailJob::dispatch($sendEmail, $accountSuspendDetails);

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
            ], 200);

        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
