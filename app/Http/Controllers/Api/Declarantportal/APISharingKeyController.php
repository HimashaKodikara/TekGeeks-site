<?php

namespace App\Http\Controllers\Api\Declarantportal;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Helpers\LangHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\SharingKeyMail;
use App\Models\DeclarantFormPage;
use App\Models\DeclarantPersonalInfo;
use App\Models\DeclarantRegistration;
use App\Models\SharingKeyDetail;
use App\Models\SharingKeySharedDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class APISharingKeyController extends Controller
{
    public function createSharingKey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_nic' => 'required',
            'master_relationship' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $declarantNic = DeclarantRegistration::select('nic')->where('id', $request->declarant_registration_id)->first();

            $declarantNic = $declarantNic->nic;
            $declarantCleanNic = preg_replace('/[VXvx]$/', '', $declarantNic);
            $declarantLastTwoDigits = substr($declarantCleanNic, -2);

            $receipientNic = $request->recipient_nic;
            $recipientCleanNic = preg_replace('/[VXvx]$/', '', $receipientNic);
            $recipientLastTwoDigits = substr($recipientCleanNic, -2);

            $sharingKey = $this->generateSharingKey($declarantLastTwoDigits, $recipientLastTwoDigits);

            $newSharingKey = new SharingKeyDetail();
            $newSharingKey->declarant_registration_id = $request->declarant_registration_id;
            $newSharingKey->recipient_nic = $request->recipient_nic;
            $newSharingKey->master_declarant_relationship = $request->master_relationship;
            $newSharingKey->recipient_email = $request->recipient_email;
            $newSharingKey->sharing_key = $sharingKey;
            $newSharingKey->key_expiration = Carbon::now()->addHours(48)->format('Y-m-d H:i:s');
            $newSharingKey->shared_date = Carbon::now();
            $newSharingKey->created_by = $request->created_by;
            $newSharingKey->save();

            $shareIndividuals = $request->share_individuals;
            $shareSections    = $request->share_sections;

            foreach($shareIndividuals as $individual) {

                [$type, $coveredPersonId] = explode('-', $individual);
                $sections = $shareSections[$individual] ?? [];

                $newShareCoveredPerson = new SharingKeySharedDetail();
                $newShareCoveredPerson->sharing_key_detail_id = $newSharingKey->id;
                $newShareCoveredPerson->covered_person_id = $coveredPersonId;
                $newShareCoveredPerson->is_declarant_included = $type=="D" ? 1:0;
                $newShareCoveredPerson->sharing_sections_id = implode(',', $sections);
                $newShareCoveredPerson->save();
            }

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Sharing key created successfully.',
                'data' => [
                    'sharing_key' => $sharingKey,
                    'key_expiration' => $newSharingKey->key_expiration,
                    'recipient_email' => $request->recipient_email,
                    'sharing_key_detail_id' => $newSharingKey->id
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

    private function generateSharingKey($declarantNic, $recipientNic)
    {
        $randomString = bin2hex(random_bytes(5)); // 10 characters hex (0–9A–F)

        $generatedSharingKey = $declarantNic . strtoupper($randomString) . $recipientNic;

        return $generatedSharingKey;
    }

    public function sendGeneratedSharedKey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sharing_key_detail_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $sharingKeyDetails = SharingKeyDetail::find($request->sharing_key_detail_id);

            if (!$sharingKeyDetails) {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Sharing key record not found.',
                ], 404);
            }

            $declarantDetails = DeclarantPersonalInfo::where('declarant_registration_id',$sharingKeyDetails->declarant_registration_id)->first();

            $mailBodyContent = [
                'sharing_key' => $sharingKeyDetails->sharing_key,
                'expiring_at' => $sharingKeyDetails->key_expiration,
                'generated_at' => $sharingKeyDetails->created_at,
                'sent_by' => $declarantDetails->full_name
            ];

            if($request->recipient_email) {
                Mail::to($request->recipient_email)->send(new SharingKeyMail($mailBodyContent));
            } else {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'An unexpected server error occurred. Please try again.'
                ], 500);
            }

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Sharing key successfully sent to email.',
            ], 200);

        } catch (\Throwable $e) {
            Log::info($e->getMessage());
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllSharingKeys(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $revokeExpiredSharingKeys = $this->revokeExpiredSharingKeys();

            $getSharingKeyDetails = SharingKeyDetail::with('relationshipwithdeclarant')->where('declarant_registration_id', $request->declarant_registration_id)
                                    ->latest()
                                    ->take(10)
                                    ->get();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'sharingKeys' => $getSharingKeyDetails
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

    public function revokeSharedKey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sharing_key_detail_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $sharedKeyDetail = SharingKeyDetail::findOrFail($request->sharing_key_detail_id);
            $sharedKeyDetail->status = "R";
            $sharedKeyDetail->save();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Shared key record deleted successfully.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }

    }

    public function getSharedKeyDetails(Request $request)
    {
        $lang = $request->lang;

        $sharedKeyDetail = SharingKeyDetail::with('relationshipwithdeclarant')
                        ->where('sharing_key', $request->shared_key)
                        ->first();

        LangHelper::setLangName($sharedKeyDetail->relationshipwithdeclarant, 'type_name', $lang);

        $sharingKeySharedDetailDeclarant = SharingKeySharedDetail::with('coveredPersons')
                        ->where('sharing_key_detail_id', $sharedKeyDetail->id)
                        ->where('is_declarant_included', 1)
                        ->first();


        $sharingKeySharedDetailCoveredPersons = SharingKeySharedDetail::with('coveredPersons')
                        ->where('sharing_key_detail_id', $sharedKeyDetail->id)
                        ->where('is_declarant_included', 0)
                        ->get();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'sharedKeyDetail' => $sharedKeyDetail,
                'sharingKeySharedDetailDeclarant' => $sharingKeySharedDetailDeclarant,
                'sharingKeySharedDetailCoveredPerson' => $sharingKeySharedDetailCoveredPersons,
            ],
        ], 200);
    }

    public function getDeclarationPages(Request $request)
    {
        $lang = $request->lang ?? 'en';

        $declarationPages = DeclarantFormPage::where('status', 'Y')->where('is_delete', 0)->whereNotIn('id',[1,3])->orderBy('order')->get();

        // Use the helper for collection
        $declarationPages = LangHelper::setLangName($declarationPages, 'page_name', $lang);

        // Log::info($declarationPages);

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'declarationPages' => $declarationPages
            ],
        ], 200);

    }

    public function revokeExpiredSharingKeys()
    {
        SharingKeyDetail::whereNotIn('status', ['R','E'])->where('key_expiration', '<=', Carbon::now())->update(['status' => 'E']);
    }

    public function checkSharingKeyRecipientValidity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'recipient_nic' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $checkSharingKeyValidity = SharingKeyDetail::where('declarant_registration_id', $request->declarant_registration_id)->where('recipient_nic', $request->recipient_nic)->where('status', 'N')->exists();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'is_exist' => $checkSharingKeyValidity
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
}
