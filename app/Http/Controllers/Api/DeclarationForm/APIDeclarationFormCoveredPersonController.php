<?php

namespace App\Http\Controllers\Api\DeclarationForm;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\DeclarantCoveredPersonOtherCountryInfo;
use App\Models\DeclarantCoveredPersonPersonalInfo;
use App\Models\DeclarantEmploymentInfo;
use App\Models\DeclarantOtherCountryInfo;
use App\Models\DeclarantPersonalInfo;
use App\Models\DeclarantRegistration;
use Illuminate\Support\Facades\DB;

class APIDeclarationFormCoveredPersonController extends Controller
{
    public function getAllCoveredPersons(Request $request)
    {
        $coveredPersons = DeclarantCoveredPersonPersonalInfo::query()
            ->where('declarant_registration_id', $request->declarant_registration_id)
            ->where('is_delete', 0)
            ->with('otherCountries') // nested children
            ->get();

        return response()->json([
            'status'  => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data'    => [
                'coveredPersons' => $coveredPersons,
            ],
        ], 200);
    }

    public function updateCoveredPersonRecords(Request $request) {
        DB::beginTransaction();

        try {

            $personalInfo = $request->personalInformation;
            $declarationOtherCountryInfo = $request->declarationOtherCountryInfo;
            $coveredPersonRecords = $request->coveredPersonData;
            $declarantResgistrationId = $request->declarant_registration_id;
            $purposeOfDeclarationInfo = $request->purposeOfTheDeclarationInfo;
            $declarationEmploymentInfo = $request->declarationEmploymentInfo;

            DeclarantRegistration::where('id', $declarantResgistrationId)
                ->update([
                    'institute_id' => $purposeOfDeclarationInfo['institution_id'],
                    'designation_id' => $purposeOfDeclarationInfo['designation_id'],
                ]);

            DeclarantPersonalInfo::updateOrCreate([
                    'declarant_registration_id' => $declarantResgistrationId
                ],[
                'full_name' => $personalInfo['full_name'],
                'name_with_initials' => $personalInfo['name_with_initials'],
                'name_with_initials_eng' => $personalInfo['name_with_initials_eng'],
                'date_of_birth' => $personalInfo['date_of_birth'],
                'nationality_id' => $personalInfo['nationality_id'],
                'nationality_name' => $personalInfo['nationality_name'],
                'nic' => $personalInfo['nic'],
                'passport' => $personalInfo['passport'],
                'tin' => $personalInfo['tin'],
                'sl_unique_digital_id_number' => $personalInfo['sl_unique_digital_id_number'],
                'country_code_personal_mobile' => $personalInfo['country_code_personal_mobile'],
                'personal_mobile_number' => $personalInfo['personal_mobile_number'],
                'country_code_fixed_mobile' => $personalInfo['country_code_fixed_mobile'],
                'fixed_mobile_number' => $personalInfo['fixed_mobile_number'],
                'personal_email' => $personalInfo['personal_email'],
                'permanent_country_id' => $personalInfo['permanent_country_id'],
                'permanent_country_name' => $personalInfo['permanent_country_name'],
                'permanent_block_house_number' => $personalInfo['permanent_block_house_number'],
                'permanent_apartment_house_name' => $personalInfo['permanent_apartment_house_name'],
                'permanent_street_name' => $personalInfo['permanent_street_name'],
                'permanent_province_id' => $personalInfo['permanent_province_id'],
                'permanent_district_id' => $personalInfo['permanent_district_id'],
                'permanent_district_name' => $personalInfo['permanent_district_name'],
                'permanent_city_id' => $personalInfo['permanent_city_id'],
                'permanent_city_name' => $personalInfo['permanent_city_name'],
                'permanent_postal_code' => $personalInfo['permanent_postal_code'],
                'permanent_residential_address' => $personalInfo['permanent_residential_address'],
                'is_same_as_permanent_address' => $personalInfo['is_same_as_permanent_address'],
                'current_country_id' => $personalInfo['current_country_id'],
                'current_country_name' => $personalInfo['current_country_name'],
                'current_block_house_number' => $personalInfo['current_block_house_number'],
                'current_apartment_house_name' => $personalInfo['current_apartment_house_name'],
                'current_street_name' => $personalInfo['current_street_name'],
                'current_province_id' => $personalInfo['current_province_id'],
                'current_district_id' => $personalInfo['current_district_id'],
                'current_district_name' => $personalInfo['current_district_name'],
                'current_city_id' => $personalInfo['current_city_id'],
                'current_city_name' => $personalInfo['current_city_name'],
                'current_postal_code' => $personalInfo['current_postal_code'],
                'current_residential_address' => $personalInfo['current_residential_address'],
                'do_hold_employment_details' => $personalInfo['do_hold_employment_details'],
                'designation_class_id' => $purposeOfDeclarationInfo['designation_class_id'],
                'designation_class_name' => $purposeOfDeclarationInfo['designation_class_name'],
                'designation_id' => $purposeOfDeclarationInfo['designation_id'],
                'other_designation' => $purposeOfDeclarationInfo['other_designation'],
                'institution_id' => $purposeOfDeclarationInfo['institution_id'],
                'other_institution' => $purposeOfDeclarationInfo['other_institution'],
                'country_code_office_mobile' => $purposeOfDeclarationInfo['country_code_office_mobile'],
                'office_mobile_no' => $purposeOfDeclarationInfo['office_mobile_no'],
                'office_address' => $purposeOfDeclarationInfo['office_address'],
                'do_hold_other_country_residency' => $personalInfo['do_hold_other_country_residency'],
                'created_by' => $declarantResgistrationId
            ]);

            DeclarantOtherCountryInfo::where('declarant_registration_id', $declarantResgistrationId)->delete();

            foreach($declarationOtherCountryInfo as $otherCountry) {
                DeclarantOtherCountryInfo::create([
                    'declarant_registration_id' => $declarantResgistrationId,
                    'foreign_country_id' => $otherCountry['foreign_country_id'],
                    'foreign_country_name' => $otherCountry['foreign_country_name'],
                    'residency_status_id' => $otherCountry['residency_status_id'],
                    'residency_status_name' => $otherCountry['residency_status_name'],
                    'is_delete' => $otherCountry['is_delete'],
                ]);
            }

            DeclarantEmploymentInfo::where('declarant_registration_id', $declarantResgistrationId)->delete();

            foreach($declarationEmploymentInfo as $employmentInfo) {
                DeclarantEmploymentInfo::create([
                    'declarant_registration_id' => $declarantResgistrationId,
                    'institution_id' => $employmentInfo['institution_id'],
                    'institution_name' => $employmentInfo['institution_name'],
                    'designation_id' => $employmentInfo['designation_id'],
                    'designation_name' => $employmentInfo['designation_name'],
                    'office_address' => $employmentInfo['office_address'],
                    'country_code_office_mobile' => $employmentInfo['country_code_office_mobile'],
                    'office_mobile_no' => $employmentInfo['office_mobile_no'],
                    'is_delete' => $employmentInfo['is_delete'],
                ]);
            }

            DeclarantCoveredPersonOtherCountryInfo::where('declarant_registration_id', $declarantResgistrationId)->delete();
            DeclarantCoveredPersonPersonalInfo::where('declarant_registration_id', $declarantResgistrationId)->delete();

            foreach($coveredPersonRecords as $cp) {
                $coveredPerson = DeclarantCoveredPersonPersonalInfo::create([
                    'declarant_registration_id' => $declarantResgistrationId,
                    'declaration_remote_id' => $cp['id'], // 🔥 store remote id here
                    'cp_added_method' => $cp['cp_added_method'],
                    'sharing_key' => $cp['sharing_key'],
                    'relationship_with_declarant_id' => $cp['relationship_with_declarant_id'],
                    'relationship_with_declarant_name' => $cp['relationship_with_declarant_name'],
                    'full_name' => $cp['full_name'],
                    'name_with_initials' => $cp['name_with_initials'],
                    'name_with_initials_eng' => $cp['name_with_initials_eng'],
                    'date_of_birth' => $cp['date_of_birth'],
                    'nationality_id' => $cp['nationality_id'],
                    'nationality_name' => $cp['nationality_name'],
                    'nic' => $cp['nic'],
                    'passport' => $cp['passport'],
                    'tin' => $cp['tin'],
                    'sl_unique_digital_id_number' => $cp['sl_unique_digital_id_number'],
                    'country_code_personal_mobile' => $cp['country_code_personal_mobile'],
                    'personal_mobile_number' => $cp['personal_mobile_number'],
                    'country_code_fixed_mobile' => $cp['country_code_fixed_mobile'],
                    'fixed_mobile_number' => $cp['fixed_mobile_number'],
                    'personal_email' => $cp['personal_email'],
                    'permanent_country_id' => $cp['permanent_country_id'],
                    'permanent_country_name' => $cp['permanent_country_name'],
                    'permanent_province_id' => $cp['permanent_province_id'],
                    'permanent_district_id' => $cp['permanent_district_id'],
                    'permanent_district_name' => $cp['permanent_district_name'],
                    'permanent_city_id' => $cp['permanent_city_id'],
                    'permanent_city_name' => $cp['permanent_city_name'],
                    'permanent_postal_code' => $cp['permanent_postal_code'],
                    'permanent_street_name' => $cp['permanent_street_name'],
                    'permanent_apartment_house_name' => $cp['permanent_apartment_house_name'],
                    'permanent_block_house_number' => $cp['permanent_block_house_number'],
                    'permanent_residential_address' => $cp['permanent_residential_address'],
                    'is_same_as_permanent_address' => $cp['is_same_as_permanent_address'],
                    'current_country_id' => $cp['current_country_id'],
                    'current_country_name' => $cp['current_country_name'],
                    'current_province_id' => $cp['current_province_id'],
                    'current_district_id' => $cp['current_district_id'],
                    'current_district_name' => $cp['current_district_name'],
                    'current_city_id' => $cp['current_city_id'],
                    'current_city_name' => $cp['current_city_name'],
                    'current_postal_code' => $cp['current_postal_code'],
                    'current_street_name' => $cp['current_street_name'],
                    'current_apartment_house_name' => $cp['current_apartment_house_name'],
                    'current_block_house_number' => $cp['current_block_house_number'],
                    'current_residential_address' => $cp['current_residential_address'],
                    'do_hold_other_country_residency' => $cp['do_hold_other_country_residency'],
                    'created_by' => $declarantResgistrationId,
                    'is_delete' => $cp['is_delete'],
                ]);

                if (!empty($cp['other_countries'])) {

                    foreach ($cp['other_countries'] as $country) {

                        DeclarantCoveredPersonOtherCountryInfo::create([
                            'declarant_registration_id' => $declarantResgistrationId,
                            'declarant_covered_person_personal_info_id' => $coveredPerson->id,
                            'declaration_covered_person_remote_id' => $cp['id'],
                            'foreign_country_id' => $country['foreign_country_id'],
                            'foreign_country_name' => $country['foreign_country_name'],
                            'residency_status_id' => $country['residency_status_id'],
                            'residency_status_name' => $country['residency_status_name'],
                            'created_by' => $declarantResgistrationId,
                            'is_delete' => $country['is_delete'],
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'Covered persons synced successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollBack();

            return response()->json([
                'success' => APIResponseMessage::FAILED_STATUS,
                'message' => $e->getMessage()
            ]);
        }

    }
}
