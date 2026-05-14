<?php

namespace App\Http\Controllers\Api\Declarantportal;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Helpers\LangHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\DeclarantCoveredPersonOtherCountryInfo;
use App\Models\DeclarantCoveredPersonPersonalInfo;
use App\Models\SharingKeyDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class APICoveredPersonController extends Controller
{
    public function createCoveredPersonPersonalInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'save_type' => 'required|in:new,edit',
            'covered_person_id' => 'required_if:save_type,edit|integer|exists:declarant_covered_person_personal_infos,id',

            'covered_person_info' => 'required|array',

            'covered_person_info.declarant_registration_id' => 'required|integer',
            'covered_person_info.relationship_with_declarant_id' => 'required',
            'covered_person_info.full_name' => 'required|string|max:210',
            'covered_person_info.name_with_initials' => 'required|string|max:210',
            'covered_person_info.date_of_birth' => 'required',
            'covered_person_info.nationality_id' => 'required',
            'covered_person_info.nic' => 'nullable|max:20',
            'covered_person_info.passport' => 'nullable|max:20',
            'covered_person_info.tin' => 'nullable|max:50',
            'covered_person_info.sl_unique_digital_id_number' => 'nullable|max:50',
            'covered_person_info.permanent_country_id' => 'required',
            'covered_person_info.current_country_id' => 'required',

            // arrays come as [] normally
            'foreign_residencies' => 'nullable|array',
            'deleted_foreign_residencies' => 'nullable|array',

            // validate each new item structure (only if present)
            'foreign_residencies.*.country_id' => 'required_with:foreign_residencies|integer',
            'foreign_residencies.*.status_id' => 'required_with:foreign_residencies|integer',
            'foreign_residencies.*.country_name' => 'nullable|string|max:255',
            'foreign_residencies.*.status_name' => 'nullable|string|max:255',

            // deleted should be ids
            'deleted_foreign_residencies.*' => 'integer',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $personal = $request->input('covered_person_info');
        $foreign = $request->input('foreign_residencies', []);
        $deleted = $request->input('deleted_foreign_residencies', []);

        if (($personal['do_hold_other_country_residency'] ?? 'N') !== 'Y') {
            $foreign = [];
            $deleted = [];
        }

        DB::beginTransaction();

        try {
            $saveType = $request->input('save_type');
            $coveredPersonId = null;
            $coveredPerson = null;

            if($saveType === 'edit') {
                $coveredPersonId = (int) $request->input('covered_person_id');

                $coveredPerson = DeclarantCoveredPersonPersonalInfo::where('id', $coveredPersonId)->firstOrFail();

                $coveredPerson->update($personal);

            } else {
                $coveredPerson = DeclarantCoveredPersonPersonalInfo::create($personal);
                $coveredPersonId = $coveredPerson->id;
            }

            if (!empty($deleted)) {
                DeclarantCoveredPersonOtherCountryInfo::where('declarant_covered_person_personal_info_id', $coveredPersonId)
                    ->whereIn('id', $deleted)
                    ->update(['is_delete' => 1]);
            }


            foreach ($foreign as $r) {

                // Basic safety
                if (empty($r['country_id']) || empty($r['status_id'])) {
                    continue;
                }

                DeclarantCoveredPersonOtherCountryInfo::create([
                    'declarant_registration_id' => $personal['declarant_registration_id'],
                    'declarant_covered_person_personal_info_id' => $coveredPerson->id,
                    'foreign_country_id' => $r['country_id'],
                    'foreign_country_name' => $r['country_name'] ?? null,
                    'residency_status_id' => $r['status_id'],
                    'residency_status_name' => $r['status_name'] ?? null,
                    'created_by' => $personal['created_by'] ?? null,
                    'is_delete' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => ($saveType === 'edit')
                    ? 'Declarant covered person record updated successfully.'
                    : 'Declarant covered person record created successfully.',
                'data' => [
                    'covered_person_id' => $coveredPersonId
                    // 'status' => $declarantRec->status,
                ],
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getCoveredPersons(Request $request)
    {
        $coveredPersons = DeclarantCoveredPersonPersonalInfo::with('relationshipwithdeclarant')->where('declarant_registration_id', $request->declarant_registration_id)
                        ->where('is_delete',0)
                        ->get();

        $coveredPersonsOtherCountryInfo = DeclarantCoveredPersonOtherCountryInfo::where('declarant_registration_id', $request->declarant_registration_id)->where('is_delete', 0)->get();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'coveredPersons' => $coveredPersons,
                'coveredPresonsOtherCountries' => $coveredPersonsOtherCountryInfo
            ],
        ], 200);
    }

    public function getCoveredPersonDetails(Request $request)
    {
        $lang = $request->lang;
        $coveredPersonPersonalInfo = DeclarantCoveredPersonPersonalInfo::with('relationshipwithdeclarant','nationality','currentCountry',
                        'currentDistrict','currentCity','permanentCountry','permanentDistrict','permanentCity')
                        ->where('id', $request->covered_person_id)
                        ->where('is_delete',0)
                        ->first();

        LangHelper::setLangName($coveredPersonPersonalInfo->relationshipwithdeclarant, 'type_name', $lang);
        LangHelper::setLangName($coveredPersonPersonalInfo->nationality, 'name', $lang);

        LangHelper::setLangName($coveredPersonPersonalInfo->currentCountry, 'country_name', $lang);
        LangHelper::setLangName($coveredPersonPersonalInfo->currentDistrict, 'district_name', $lang);
        LangHelper::setLangName($coveredPersonPersonalInfo->currentCity, 'city_name', $lang);

        LangHelper::setLangName($coveredPersonPersonalInfo->permanentCountry, 'country_name', $lang);
        LangHelper::setLangName($coveredPersonPersonalInfo->permanentDistrict, 'district_name', $lang);
        LangHelper::setLangName($coveredPersonPersonalInfo->permanentCity, 'city_name', $lang);

        $coveredPersonCountryInfo = DeclarantCoveredPersonOtherCountryInfo::with('countries','visaTypes')
                        ->where('declarant_covered_person_personal_info_id', $request->covered_person_id)
                        ->where('is_delete',0)
                        ->get();

        foreach ($coveredPersonCountryInfo as $info) {
            LangHelper::setLangName($info->countries, 'country_name', $lang);
            LangHelper::setLangName($info->visaTypes, 'type_name', $lang);
        }

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'personalInfo' => $coveredPersonPersonalInfo,
                'countryInfo' => $coveredPersonCountryInfo
            ],
        ], 200);
    }

    public function getCoveredPersonPersonalInfo(Request $request)
    {
        $personalInfo = DeclarantCoveredPersonPersonalInfo::where('id', $request->personalInfoId)->first();

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => [
                'personalInfo' => $personalInfo
            ],
        ], 200);
    }

    public function updateCoveredPersonPersonalInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required',
            'covered_person_info.relationship_with_declarant' => 'required',
            'covered_person_info.full_name' => 'required|string|max:210',
            'covered_person_info.name_with_initials' => 'required|string|max:210',
            'covered_person_info.relationship_with_declarant' => 'required',
            'covered_person_info.dob' => 'required',
            'covered_person_info.nationality_id' => 'required',
            'covered_person_info.nic' => 'max:20',
            'covered_person_info.passport' => 'max:20',
            'covered_person_info.tin' => 'max:50',
            'covered_person_info.sl_unique_digital_id_number' => 'max:50',
            'covered_person_info.permanent_country_id' => 'required',
            'covered_person_info.current_country_id' => 'required',
            'covered_person_info.covered_person_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $coveredPerson = DeclarantCoveredPersonPersonalInfo::findOrFail($request->covered_person_info_id);

            $coveredPerson->update($request->only([
                'surname',
                'other_names',
                'is_change_your_covered_person_name_in_last_year',
                'prev_full_name',
                'relationship_with_declarant',
                'dob',
                'master_gender',
                'master_nationality',
                'national_id_number',
                'passport_number',
                'tin_number',
                'master_current_country_id',
                'master_current_province_id',
                'master_current_district_id',
                'current_address_no',
                'current_street_one',
                'current_street_two',
                'master_current_city_id',
                'current_postal_code',
                'current_residential_address',
                'is_same_as_permanent_address',
                'master_permanent_country_id',
                'master_permanent_province_id',
                'master_permanent_district_id',
                'permanent_address_no',
                'permanent_street_one',
                'permanent_street_two',
                'master_permanent_city_id',
                'permanent_postal_code',
                'permanent_residential_address',
                'country_code_home',
                'phone_home',
                'country_code_mobile',
                'phone_mobile',
                'country_code_work',
                'phone_work',
                'personal_email',
                'work_email'
            ]));

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Declarant covered person personal info updated successfully.',
                'data' => [
                    'personal_info_rec_id' => $coveredPerson->id
                    // 'status' => $declarantRec->status,
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

    public function deleteCoveredPersons(Request $request)
    {
        $data = $request->all();

        $data['personal_info_id'] = is_array($data['personal_info_id'])
            ? $data['personal_info_id']
            : [$data['personal_info_id']];

        $validator = Validator::make($data, [
            'personal_info_id' => 'required|array',
            'personal_info_id.*' => 'integer',
        ]);

        try {
            $ids = $data['personal_info_id'];
            DeclarantCoveredPersonPersonalInfo::whereIn('id', $ids)
                                ->update(['is_delete' => 1]);

            DeclarantCoveredPersonOtherCountryInfo::whereIn('declarant_covered_person_personal_info_id', $ids)
                                ->update(['is_delete' => 1]);

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Declarant covered person record deleted successfully.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }

    }

    public function getAllCoveredPersonOtherCountryInfo(Request $request)
    {
        $otherCountryInfo = DeclarantCoveredPersonOtherCountryInfo::with('countries','visaTypes')->where('declarant_covered_person_personal_info_id', $request->personalInfoId)->where('is_delete',0)->get();

        if($otherCountryInfo) {
            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'otherCountryInfo' => $otherCountryInfo
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::DATAFETCHEDFAILED
            ], 200);
        }

    }

    // public function createUpdateMultipleCPRecords(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'declarant_registration_id' => 'required|integer',
    //         'covered_person_updated_details' => 'required|array|min:1',
    //         'covered_person_updated_details.*.source' => 'required|in:saved,new',
    //         'covered_person_updated_details.*.id' => 'nullable|integer',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Validation Error',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     Log::info($request->all());

    //     $userId = (int) $request->declarant_registration_id;
    //     $records = $request->input('covered_person_updated_details', []);

    //     $idMap = []; // for new records: tmpKey => newId (or index => newId)

    //     DB::beginTransaction();

    //     try {

    //         foreach ($records as $index => $record) {

    //             // build cpData once (use for both create & update)
    //             $cpData = [
    //                 'declarant_registration_id' => $userId,
    //                 'cp_method' => $record['cp_method'] ?? 'M',
    //                 'relationship_with_declarant' => $record['relation_with_declarant_id'] ?? null,

    //                 'full_name' => $record['full_name'] ?? '',
    //                 'name_with_initials' => $record['name_with_initials'] ?? '',
    //                 'dob' => $record['date_of_birth'] ?? null,
    //                 'nationality_id' => $record['nationality_id'] ?? null,
    //                 'nic' => $record['nic'] ?? '',
    //                 'passport' => $record['passport'] ?? '',
    //                 'tin' => $record['tin'] ?? '',
    //                 'sl_unique_digital_id_number' => $record['sl_unique_digital_id_number'] ?? '',

    //                 'country_code_personal_mobile' => $record['country_code_personal_mobile'] ?? '',
    //                 'personal_mobile_number' => $record['personal_mobile_number'] ?? '',
    //                 'country_code_fixed_mobile' => $record['country_code_fixed_mobile'] ?? '',
    //                 'fixed_mobile_number' => $record['fixed_mobile_number'] ?? '',
    //                 'personal_email' => $record['personal_email'] ?? '',

    //                 'permanent_country_id' => $record['permanent_country_id'] ?? null,
    //                 'permanent_block_house_number' => $record['permanent_block_house_number'] ?? '',
    //                 'permanent_street_name' => $record['permanent_street_name'] ?? '',
    //                 'permanent_district_id' => $record['permanent_district_id'] ?? null,
    //                 'permanent_city_id' => $record['permanent_city_id'] ?? null,
    //                 'permanent_postal_code' => $record['permanent_postal_code'] ?? '',
    //                 'permanent_residential_address' => $record['permanent_residential_address'] ?? '',

    //                 'is_same_as_permanent_address' => $record['is_same_as_permanent_address'] ?? '0',

    //                 'current_country_id' => $record['current_country_id'] ?? null,
    //                 'current_block_house_number' => $record['current_block_house_number'] ?? '',
    //                 'current_street_name' => $record['current_street_name'] ?? '',
    //                 'current_district_id' => $record['current_district_id'] ?? null,
    //                 'current_city_id' => $record['current_city_id'] ?? null,
    //                 'current_postal_code' => $record['current_postal_code'] ?? '',
    //                 'current_residential_address' => $record['current_residential_address'] ?? '',

    //                 'is_have_other_country_residency' => $record['do_hold_other_country_residency'] ?? 'N',
    //                 'is_delete' => 0,
    //                 'created_by' => $userId
    //             ];

    //             // 1) CREATE or UPDATE covered person
    //             if (($record['source'] ?? '') === 'new') {

    //                 $coveredPerson = DeclarantCoveredPersonPersonalInfo::create($cpData);
    //                 $cpId = (int) $coveredPerson->id;

    //                 // Map tmpKey (recommended) OR index
    //                 $tmpKey = $record['_tmpKey'] ?? null;
    //                 if ($tmpKey) $idMap[$tmpKey] = $cpId;
    //                 else $idMap["index_$index"] = $cpId;

    //             } else {
    //                 $cpId = (int) ($record['id'] ?? 0);

    //                 $coveredPerson = DeclarantCoveredPersonPersonalInfo::where('id', $cpId)
    //                     ->where('declarant_registration_id', $userId)
    //                     ->firstOrFail();

    //                 $coveredPerson->update($cpData);
    //             }

    //             // 2) Foreign residency sync (soft delete old then insert new)
    //             DeclarantCoveredPersonOtherCountryInfo::where('declarant_covered_person_personal_info_id', $cpId)
    //                 ->update(['is_delete' => 1]);

    //             $foreignRows = $record['foreign_residency'] ?? [];
    //             if (!is_array($foreignRows)) $foreignRows = [];

    //             if (($cpData['is_have_other_country_residency'] ?? 'N') === 'Y' && !empty($foreignRows)) {
    //                 foreach ($foreignRows as $fr) {
    //                     DeclarantCoveredPersonOtherCountryInfo::create([
    //                         'declarant_registration_id' => $userId,
    //                         'declarant_covered_person_personal_info_id' => $cpId,
    //                         'foreign_country_id' => $fr['countryId'] ?? null,
    //                         'foreign_country_name' => $fr['countryName'] ?? '',
    //                         'residency_status_id' => $fr['statusId'] ?? null,
    //                         'residency_status_name' => $fr['statusName'] ?? '',
    //                         'created_by' => $userId,
    //                         'is_delete' => 0,
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Covered persons saved successfully',
    //             'data' => [
    //                 'id_map' => $idMap, // <-- Server1 uses this to mirror new IDs
    //             ]
    //         ]);

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error($e);

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to save covered persons',
    //         ], 500);
    //     }
    // }

    public function createUpdateMultipleCPRecords(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required|integer',
            'covered_person_updated_details' => 'required|array|min:1',
            'covered_person_updated_details.*.source' => 'required|in:saved,new',
            'covered_person_updated_details.*.id' => 'nullable|integer',

            // allow longer tmpKey
            'covered_person_updated_details.*._tmpKey' => 'nullable|string|max:150',

            // ✅ make request_id mandatory (best for rollback)
            'request_id' => 'required|uuid',
            'sharing_key' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId    = (int) $request->declarant_registration_id;
        $requestId = (string) $request->request_id;

        $records = $request->input('covered_person_updated_details', []);
        $idMap   = [];

        DB::beginTransaction();

        try {
            foreach ($records as $index => $record) {

                $source = $record['source'] ?? '';
                $tmpKey = $record['_tmpKey'] ?? null;

                if ($source === 'new' && empty($tmpKey)) {
                    throw new \Exception("Missing _tmpKey for new record at index {$index}");
                }

                $cpData = [
                    'declarant_registration_id' => $userId,
                    'request_id' => $requestId,         // ✅ store request id
                    'client_tmp_key' => $tmpKey,        // ✅ store tmp key
                    'cp_method' => $record['cp_method'] ?? 'M',
                    'relationship_with_declarant' => $record['relation_with_declarant_id'] ?? null,

                    'full_name' => $record['full_name'] ?? '',
                    'name_with_initials' => $record['name_with_initials'] ?? '',
                    'name_with_initials_eng' => $record['name_with_initials_eng'] ?? '',
                    'dob' => $record['date_of_birth'] ?? null,
                    'nationality_id' => $record['nationality_id'] ?? null,
                    'nic' => $record['nic'] ?? '',
                    'passport' => $record['passport'] ?? '',
                    'tin' => $record['tin'] ?? '',
                    'sl_unique_digital_id_number' => $record['sl_unique_digital_id_number'] ?? '',

                    'country_code_personal_mobile' => $record['country_code_personal_mobile'] ?? '',
                    'personal_mobile_number' => $record['personal_mobile_number'] ?? '',
                    'country_code_fixed_mobile' => $record['country_code_fixed_mobile'] ?? '',
                    'fixed_mobile_number' => $record['fixed_mobile_number'] ?? '',
                    'personal_email' => $record['personal_email'] ?? '',

                    'permanent_country_id' => $record['permanent_country_id'] ?? null,
                    'permanent_block_house_number' => $record['permanent_block_house_number'] ?? '',
                    'permanent_street_name' => $record['permanent_street_name'] ?? '',
                    'permanent_district_id' => $record['permanent_district_id'] ?? null,
                    'permanent_city_id' => $record['permanent_city_id'] ?? null,
                    'permanent_postal_code' => $record['permanent_postal_code'] ?? '',
                    'permanent_residential_address' => $record['permanent_residential_address'] ?? '',

                    'is_same_as_permanent_address' => $record['is_same_as_permanent_address'] ?? 'N',

                    'current_country_id' => $record['current_country_id'] ?? null,
                    'current_block_house_number' => $record['current_block_house_number'] ?? '',
                    'current_street_name' => $record['current_street_name'] ?? '',
                    'current_district_id' => $record['current_district_id'] ?? null,
                    'current_city_id' => $record['current_city_id'] ?? null,
                    'current_postal_code' => $record['current_postal_code'] ?? '',
                    'current_residential_address' => $record['current_residential_address'] ?? '',

                    'is_have_other_country_residency' => $record['do_hold_other_country_residency'] ?? 'N',
                    'is_delete' => 0,
                    'created_by' => $userId,
                ];

                if ($source === 'new') {
                    $coveredPerson = DeclarantCoveredPersonPersonalInfo::updateOrCreate(
                        ['declarant_registration_id' => $userId, 'client_tmp_key' => $tmpKey],
                        $cpData
                    );

                    $cpId = (int) $coveredPerson->id;
                    $idMap[$tmpKey] = $cpId;

                } else {
                    $cpId = (int) ($record['id'] ?? 0);

                    $coveredPerson = DeclarantCoveredPersonPersonalInfo::where('id', $cpId)
                        ->where('declarant_registration_id', $userId)
                        ->firstOrFail();

                    // keep existing tmpkey; don’t overwrite with null
                    if (empty($coveredPerson->client_tmp_key) && !empty($tmpKey)) {
                        $coveredPerson->client_tmp_key = $tmpKey;
                    }

                    // always tag the latest request_id (helpful for rollback audits)
                    $coveredPerson->request_id = $requestId;

                    $coveredPerson->fill($cpData);
                    $coveredPerson->save();
                }

                // Foreign residency sync
                DeclarantCoveredPersonOtherCountryInfo::where('declarant_covered_person_personal_info_id', $cpId)
                    ->update(['is_delete' => 1]);

                $foreignRows = $record['foreign_residency'] ?? [];
                if (!is_array($foreignRows)) $foreignRows = [];

                if (($cpData['is_have_other_country_residency'] ?? 'N') === 'Y' && !empty($foreignRows)) {
                    foreach ($foreignRows as $fr) {

                        // ✅ allow “same country” duplicates: do NOT updateOrCreate here
                        DeclarantCoveredPersonOtherCountryInfo::create([
                            'request_id' => $requestId, // ✅ store request id
                            'declarant_registration_id' => $userId,
                            'declarant_covered_person_personal_info_id' => $cpId,
                            'foreign_country_id' => $fr['countryId'] ?? null,
                            'foreign_country_name' => $fr['countryName'] ?? '',
                            'residency_status_id' => $fr['statusId'] ?? null,
                            'residency_status_name' => $fr['statusName'] ?? '',
                            'created_by' => $userId,
                            'is_delete' => 0,
                        ]);
                    }
                }
            }

            // Mark sharing key used (only if provided)
            $sharingKey = $request->input('sharing_key');
            if (!empty($sharingKey)) {
                $sharedKeyDetail = SharingKeyDetail::where('sharing_key', $sharingKey)->first();
                if ($sharedKeyDetail) {
                    $sharedKeyDetail->used_date = now();
                    $sharedKeyDetail->status = 'U';
                    $sharedKeyDetail->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Covered persons saved successfully',
                'data' => ['id_map' => $idMap],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save covered persons',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }

    public function rollbackCoveredPersonsByRequest(Request $request)
    {
        $request->validate([
            'declarant_registration_id' => 'required|integer',
            'request_id' => 'required|uuid',
            'tmp_keys' => 'nullable|array',
            'tmp_keys.*' => 'string|max:150',
            'sharing_key' => 'nullable|string|max:100',
        ]);

        $userId    = (int) $request->declarant_registration_id;
        $requestId = (string) $request->request_id;
        $tmpKeys   = $request->tmp_keys ?? [];
        $sharingKey = $request->sharing_key ? trim($request->sharing_key) : null;

        DB::beginTransaction();

        try {
            $cpQuery = DeclarantCoveredPersonPersonalInfo::where('declarant_registration_id', $userId)
                ->where('request_id', $requestId);

            if (!empty($tmpKeys)) {
                $cpQuery->whereIn('client_tmp_key', $tmpKeys);
            }

            $cpIds = $cpQuery->pluck('id')->toArray();

            // delete other-country rows created in this request (safe)
            // DeclarantCoveredPersonOtherCountryInfo::whereIn('request_id', $requestId)->delete();

            // (extra safety) also delete by cp ids
            if (!empty($cpIds)) {
                DeclarantCoveredPersonOtherCountryInfo::whereIn('declarant_covered_person_personal_info_id', $cpIds)->delete();
            }

            // delete CP rows
            $cpQuery->delete();

            // reset sharing key
            if ($sharingKey) {
                $sk = SharingKeyDetail::where('sharing_key', $sharingKey)->first();
                if ($sk) {
                    $sk->status = 'N';     // or whatever your “not used” status is
                    $sk->used_date = null; // optional
                    $sk->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Rollback completed',
                'data' => ['rolled_back_cp_ids' => $cpIds],
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Rollback failed',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }
}
