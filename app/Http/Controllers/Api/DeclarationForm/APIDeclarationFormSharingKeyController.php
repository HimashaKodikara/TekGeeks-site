<?php

namespace App\Http\Controllers\Api\DeclarationForm;

use App\Helpers\APIResponseMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeclarantCoveredPersonOtherCountryInfo;
use App\Models\DeclarantOtherCountryInfo;
use App\Models\DeclarantPersonalInfo;
use App\Models\SharingKeyDetail;
use App\Models\SharingKeySharedDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class APIDeclarationFormSharingKeyController extends Controller
{
    // public function getSharingKeyDetails(Request $request)
    // {
    //     $request->validate([
    //         'shared_key' => ['required','string','max:100'],
    //         'declarant_registration_id' => ['required','integer'],
    //         'lang' => ['nullable','string','max:5'],
    //     ]);

    //     $sharedKey = trim($request->shared_key);
    //     $targetDeclarantId = (int) $request->declarant_registration_id;

    //     $sharedKeyDetail = SharingKeyDetail::where('sharing_key', $sharedKey)->first();

    //     if (!$sharedKeyDetail) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Invalid sharing key.',
    //         ], 404);
    //     }

    //     // Optional: reject already-used keys
    //     if ($sharedKeyDetail->status === 'U') {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'This sharing key has already been used.',
    //         ], 422);
    //     }

    //     // Load shared detail records
    //     $sharedRows = SharingKeySharedDetail::with(['coveredPersons', 'relationshipwithdeclarant'])
    //         ->where('sharing_key_detail_id', $sharedKeyDetail->id)
    //         ->get();

    //     $preview = [];

    //     foreach ($sharedRows as $row) {

    //         // declarant included
    //         if ((int)$row->is_declarant_included === 1) {
    //             $srcDeclarant = DeclarantPersonalInfo::where('declarant_registration_id', $row->covered_person_id)->first();
    //             if (!$srcDeclarant) continue;

    //             $foreign = DeclarantOtherCountryInfo::where('declarant_registration_id', $row->covered_person_id)
    //                 ->where('is_delete', 0)
    //                 ->get();

    //             $preview[] = [
    //                 'preview_ref' => 'SKSD-'.$row->id,
    //                 'is_declarant' => 1,
    //                 'relationship_with_declarant' => null,
    //                 'relationship_with_declarant_id' => null,

    //                 'full_name' => $srcDeclarant->full_name,
    //                 'name_with_initials' => $srcDeclarant->name_with_initials,
    //                 'date_of_birth' => $srcDeclarant->date_of_birth,
    //                 'nationality_id' => $srcDeclarant->nationality_id,
    //                 'nic' => $srcDeclarant->nic,
    //                 'passport' => $srcDeclarant->passport,
    //                 'tin' => $srcDeclarant->tin,
    //                 'sl_unique_digital_id_number' => $srcDeclarant->sl_unique_digital_id_number,

    //                 'country_code_personal_mobile' => $srcDeclarant->country_code_personal_mobile,
    //                 'personal_mobile_number' => $srcDeclarant->personal_mobile_number,
    //                 'country_code_fixed_mobile' => $srcDeclarant->country_code_fixed_mobile,
    //                 'fixed_mobile_number' => $srcDeclarant->fixed_mobile_number,
    //                 'personal_email' => $srcDeclarant->personal_email,

    //                 'permanent_country_id' => $srcDeclarant->permanent_country_id,
    //                 'permanent_block_house_number' => $srcDeclarant->permanent_block_house_number,
    //                 'permanent_street_name' => $srcDeclarant->permanent_street_name,
    //                 'permanent_district_id' => $srcDeclarant->permanent_district_id,
    //                 'permanent_city_id' => $srcDeclarant->permanent_city_id,
    //                 'permanent_postal_code' => $srcDeclarant->permanent_postal_code,
    //                 'permanent_residential_address' => $srcDeclarant->permanent_residential_address,
    //                 'is_same_as_permanent_address' => $srcDeclarant->is_same_as_permanent_address ?? 0,

    //                 'current_country_id' => $srcDeclarant->current_country_id,
    //                 'current_block_house_number' => $srcDeclarant->current_block_house_number,
    //                 'current_street_name' => $srcDeclarant->current_street_name,
    //                 'current_district_id' => $srcDeclarant->current_district_id,
    //                 'current_city_id' => $srcDeclarant->current_city_id,
    //                 'current_postal_code' => $srcDeclarant->current_postal_code,
    //                 'current_residential_address' => $srcDeclarant->current_residential_address,

    //                 'do_hold_other_country_residency' => $srcDeclarant->do_hold_other_country_residency ?? 'N',

    //                 'foreign_residency' => $foreign->map(function($x){
    //                     return [
    //                         'countryId' => $x->foreign_country_id,
    //                         'countryName' => $x->foreign_country_name,
    //                         'statusId' => $x->residency_status_id,
    //                         'statusName' => $x->residency_status_name,
    //                     ];
    //                 })->values(),
    //             ];

    //             continue;
    //         }

    //         // normal covered person included
    //         $srcCp = $row->coveredPersons; // DeclarantCoveredPersonPersonalInfo (source user)
    //         if (!$srcCp) continue;

    //         $foreign = DeclarantCoveredPersonOtherCountryInfo::where('declarant_covered_person_personal_info_id', $srcCp->id)
    //             ->where('is_delete', 0)
    //             ->get();

    //         $preview[] = [
    //             'preview_ref' => 'SKSD-'.$row->id,
    //             'is_declarant' => 0,

    //             'relationship_with_declarant_id' => $srcCp->relationship_with_declarant,
    //             'relationship_with_declarant' => optional($row->relationshipwithdeclarant)->name,

    //             'full_name' => $srcCp->full_name,
    //             'name_with_initials' => $srcCp->name_with_initials,
    //             'date_of_birth' => $srcCp->dob,
    //             'nationality_id' => $srcCp->nationality_id,
    //             'nic' => $srcCp->nic,
    //             'passport' => $srcCp->passport,
    //             'tin' => $srcCp->tin,
    //             'sl_unique_digital_id_number' => $srcCp->sl_unique_digital_id_number,

    //             'country_code_personal_mobile' => $srcCp->country_code_personal_mobile,
    //             'personal_mobile_number' => $srcCp->personal_mobile_number,
    //             'country_code_fixed_mobile' => $srcCp->country_code_fixed_mobile,
    //             'fixed_mobile_number' => $srcCp->fixed_mobile_number,
    //             'personal_email' => $srcCp->personal_email,

    //             'permanent_country_id' => $srcCp->permanent_country_id,
    //             'permanent_block_house_number' => $srcCp->permanent_block_house_number,
    //             'permanent_street_name' => $srcCp->permanent_street_name,
    //             'permanent_district_id' => $srcCp->permanent_district_id,
    //             'permanent_city_id' => $srcCp->permanent_city_id,
    //             'permanent_postal_code' => $srcCp->permanent_postal_code,
    //             'permanent_residential_address' => $srcCp->permanent_residential_address,
    //             'is_same_as_permanent_address' => $srcCp->is_same_as_permanent_address ?? 0,

    //             'current_country_id' => $srcCp->current_country_id,
    //             'current_block_house_number' => $srcCp->current_block_house_number,
    //             'current_street_name' => $srcCp->current_street_name,
    //             'current_district_id' => $srcCp->current_district_id,
    //             'current_city_id' => $srcCp->current_city_id,
    //             'current_postal_code' => $srcCp->current_postal_code,
    //             'current_residential_address' => $srcCp->current_residential_address,

    //             'do_hold_other_country_residency' => $srcCp->is_have_other_country_residency ?? 'N',

    //             'foreign_residency' => $foreign->map(function($x){
    //                 return [
    //                     'countryId' => $x->foreign_country_id,
    //                     'countryName' => $x->foreign_country_name,
    //                     'statusId' => $x->residency_status_id,
    //                     'statusName' => $x->residency_status_name,
    //                 ];
    //             })->values(),
    //         ];
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => [
    //             'shared_key' => $sharedKey,
    //             'preview' => $preview,
    //         ]
    //     ], 200);
    // }

    public function importSharingKeyDetailsPreview(Request $request)
    {
        $request->validate([
            'shared_key' => 'required|string|max:100',
            'declarant_registration_id' => 'required|integer',
            'lang' => 'nullable|string|max:10',
        ]);

        $sharingKey = trim($request->shared_key);

        $sharedKeyDetail = SharingKeyDetail::where('sharing_key', $sharingKey)->first();

        if (!$sharedKeyDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid sharing key.'
            ], 422);
        }

        if($sharedKeyDetail->status == "U") {
            return response()->json([
                'status' => 'success',
                'message' => 'This sharing key is already used.'
            ], 423);
        }


        // ✅ ONLY READ + BUILD PREVIEW DATA
        // Here you already have logic to find which CPs are shared using SharingKeySharedDetail.
        // Instead of inserting DeclarantCoveredPersonPersonalInfo, we "compose preview rows".

        $preview = [];

        $sharingKeyDeclarantSharedDetails = SharingKeySharedDetail::where('sharing_key_detail_id', $sharedKeyDetail->id)
            ->where('is_declarant_included', 1)
            ->first();

        if ($sharingKeyDeclarantSharedDetails) {
            $declarantCPDetails = DeclarantPersonalInfo::where('declarant_registration_id', $sharingKeyDeclarantSharedDetails->covered_person_id)->first();

            if ($declarantCPDetails) {

                $declarantOtherCountries = DeclarantOtherCountryInfo::where('declarant_registration_id', $sharingKeyDeclarantSharedDetails->covered_person_id)
                    ->where('is_delete', 0)
                    ->get();

                $preview[] = [
                    'relationship_with_declarant_id' => null,
                    'relationship_with_declarant_name' => 'Declarant',
                    'full_name' => $declarantCPDetails->full_name,
                    'name_with_initials' => $declarantCPDetails->name_with_initials,
                    'name_with_initials_eng' => $declarantCPDetails->name_with_initials_eng,
                    'date_of_birth' => $declarantCPDetails->date_of_birth,
                    'nationality_id' => $declarantCPDetails->nationality_id,
                    'nationality_name' => $declarantCPDetails->nationality_name,
                    'nic' => $declarantCPDetails->nic,
                    'passport' => $declarantCPDetails->passport,
                    'tin' => $declarantCPDetails->tin,
                    'sl_unique_digital_id_number' => $declarantCPDetails->sl_unique_digital_id_number,

                    'country_code_personal_mobile' => $declarantCPDetails->country_code_personal_mobile,
                    'personal_mobile_number' => $declarantCPDetails->personal_mobile_number,
                    'country_code_fixed_mobile' => $declarantCPDetails->country_code_fixed_mobile,
                    'fixed_mobile_number' => $declarantCPDetails->fixed_mobile_number,
                    'personal_email' => $declarantCPDetails->personal_email,

                    'permanent_country_id' => $declarantCPDetails->permanent_country_id,
                    'permanent_country_name' => $declarantCPDetails->permanent_country_name,
                    'permanent_district_id' => $declarantCPDetails->permanent_district_id,
                    'permanent_district_name' => $declarantCPDetails->permanent_district_name,
                    'permanent_city_id' => $declarantCPDetails->permanent_city_id,
                    'permanent_city_name' => $declarantCPDetails->permanent_city_name,
                    'permanent_postal_code' => $declarantCPDetails->permanent_postal_code,
                    'permanent_street_name' => $declarantCPDetails->permanent_street_name,
                    'permanent_apartment_house_name' => $declarantCPDetails->permanent_apartment_house_name,
                    'permanent_block_house_number' => $declarantCPDetails->permanent_block_house_number,
                    'permanent_residential_address' => $declarantCPDetails->permanent_residential_address,

                    'is_same_as_permanent_address' => $declarantCPDetails->is_same_as_permanent_address,

                    'current_country_id' => $declarantCPDetails->current_country_id,
                    'current_country_name' => $declarantCPDetails->current_country_name,
                    'current_district_id' => $declarantCPDetails->current_district_id,
                    'current_district_name' => $declarantCPDetails->current_district_name,
                    'current_city_id' => $declarantCPDetails->current_city_id,
                    'current_city_name' => $declarantCPDetails->current_city_name,
                    'current_postal_code' => $declarantCPDetails->current_postal_code,
                    'current_street_name' => $declarantCPDetails->current_street_name,
                    'current_apartment_house_name' => $declarantCPDetails->current_apartment_house_name,
                    'current_block_house_number' => $declarantCPDetails->current_block_house_number,
                    'current_residential_address' => $declarantCPDetails->current_residential_address,

                    'do_hold_other_country_residency' => $declarantOtherCountries->count() ? 'Y' : 'N',
                    'foreign_residency' => $this->mapForeignResidencies($declarantOtherCountries),
                ];
            }
        }

        $sharingKeyCPSharedDetails = SharingKeySharedDetail::with('coveredPersons')
            ->where('sharing_key_detail_id', $sharedKeyDetail->id)
            ->where('is_declarant_included', 0)
            ->get();

        foreach ($sharingKeyCPSharedDetails as $sharedrecord) {
            $cp = $sharedrecord->coveredPersons;
            if (!$cp) continue;


            $cpOtherCountries = DeclarantCoveredPersonOtherCountryInfo::where('declarant_covered_person_personal_info_id', $cp->id)
                ->where('is_delete', 0)
                ->get();


            $preview[] = [
                'relationship_with_declarant_id' => null,
                'relationship_with_declarant_name' => 'Declarant',
                'full_name' => $cp->full_name,
                'name_with_initials' => $cp->name_with_initials,
                'name_with_initials_eng' => $cp->name_with_initials_eng,
                'date_of_birth' => $cp->date_of_birth,
                'nationality_id' => $cp->nationality_id,
                'nationality_name' => $cp->nationality_name,
                'nic' => $cp->nic,
                'passport' => $cp->passport,
                'tin' => $cp->tin,
                'sl_unique_digital_id_number' => $cp->sl_unique_digital_id_number,

                'country_code_personal_mobile' => $cp->country_code_personal_mobile,
                'personal_mobile_number' => $cp->personal_mobile_number,
                'country_code_fixed_mobile' => $cp->country_code_fixed_mobile,
                'fixed_mobile_number' => $cp->fixed_mobile_number,
                'personal_email' => $cp->personal_email,

                'permanent_country_id' => $cp->permanent_country_id,
                'permanent_country_name' => $cp->permanent_country_name,
                'permanent_district_id' => $cp->permanent_district_id,
                'permanent_district_name' => $cp->permanent_district_name,
                'permanent_city_id' => $cp->permanent_city_id,
                'permanent_city_name' => $cp->permanent_city_name,
                'permanent_postal_code' => $cp->permanent_postal_code,
                'permanent_street_name' => $cp->permanent_street_name,
                'permanent_apartment_house_name' => $cp->permanent_apartment_house_name,
                'permanent_block_house_number' => $cp->permanent_block_house_number,
                'permanent_residential_address' => $cp->permanent_residential_address,

                'is_same_as_permanent_address' => $cp->is_same_as_permanent_address,

                'current_country_id' => $cp->current_country_id,
                'current_country_name' => $cp->current_country_name,
                'current_district_id' => $cp->current_district_id,
                'current_district_name' => $cp->current_district_name,
                'current_city_id' => $cp->current_city_id,
                'current_city_name' => $cp->current_city_name,
                'current_postal_code' => $cp->current_postal_code,
                'current_street_name' => $cp->current_street_name,
                'current_apartment_house_name' => $cp->current_apartment_house_name,
                'current_block_house_number' => $cp->current_block_house_number,
                'current_residential_address' => $cp->current_residential_address,

                'do_hold_other_country_residency' => $cpOtherCountries->count() ? 'Y' : 'N',
                'foreign_residency' => $this->mapForeignResidencies($cpOtherCountries),
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'covered_person_details' => $preview
            ]
        ], 200);
    }

    private function mapForeignResidencies($rows)
    {
        return collect($rows)->map(function ($r) {
            return [
                // 'id'         => $r->id,
                'countryId'   => (string) ($r->foreign_country_id ?? ''),
                'countryName' => (string) ($r->foreign_country_name ?? ''),
                'statusId'    => (string) ($r->residency_status_id ?? ''),
                'statusName'  => (string) ($r->residency_status_name ?? ''),
            ];
        })->values()->all();
    }

    public function getSharingKeyDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shared_key' => 'required|string|max:100',
            'nic' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $sharingKey = trim($request->shared_key);

        $sharedKeyDetail = SharingKeyDetail::where('sharing_key', $sharingKey)->where('recipient_nic', $request->nic)->first();

        if (!$sharedKeyDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid sharing key or does not match the recipient nic.',
                'status_code' => 422
            ], 200); // ✅ important (consistent)
        }

        $expiryTime = Carbon::parse($sharedKeyDetail->key_expiration);

        if (!$sharedKeyDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid sharing key or does not match the recipient nic.',
                'status_code' => 422
            ]);
        }

        if($sharedKeyDetail->status == "U") {
            return response()->json([
                'status' => 'error',
                'message' => 'This sharing key is already used.',
                'status_code' => 423
            ]);
        }

        if ($expiryTime->lessThanOrEqualTo(Carbon::now())) {
            return response()->json([
                'status' => 'error',
                'message' => 'This sharing key has expired.',
                'status_code' => 424
            ]);
        }

        if($sharedKeyDetail)
        {
            $sharedDetails = SharingKeySharedDetail::where('sharing_key_detail_id', $sharedKeyDetail->id)->get();

            // For now, we just return the shared key details. The frontend can call the preview API to get the actual CP details.
            return response()->json([
                'status' => 'success',
                'data' => [
                    'shared_key' => $sharingKey,
                    'declarant_registration_id' => $sharedKeyDetail->declarant_registration_id,
                    'shared_details' => $sharedDetails,
                    'status_code' => 200
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No sharing key details found.',
                'status_code' => 404
            ]);
        }
    }

    public function updateSharingKeyStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'shared_key' => 'required|string|max:100',
            'nic' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $sharingKey = trim($request->shared_key);

        $sharedKeyDetailUpdate = SharingKeyDetail::where('sharing_key', $sharingKey)->where('recipient_nic', $request->nic)->update([
            'status' => 'U',
            'used_date' => Carbon::now()
        ]);

        if ($sharedKeyDetailUpdate) {
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully updated the status'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No matching record found'
            ], 404);
        }
    }
}
