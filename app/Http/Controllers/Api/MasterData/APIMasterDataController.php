<?php

namespace App\Http\Controllers\Api\MasterData;

use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\AcquisitionMode;
use App\Models\BankAccountType;
use App\Models\BankFinanceCompany;
use App\Models\City;
use App\Models\CommercialsableIntangibleAssetsType;
use App\Models\CooperateEntityType;
use App\Models\CorporateCompany;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DeclarantFormPage;
use App\Models\DeclarantRelationshipType;
use App\Models\DeclarationType;
use App\Models\Designation;
use App\Models\DesignationClass;
use App\Models\District;
use App\Models\ExpenseType;
use App\Models\ImmovableAssetType;
use App\Models\IncomeType;
use App\Models\InsuranceCompanyIssuer;
use App\Models\InsurancePolicyType;
use App\Models\IntangibleAcquisitionMethod;
use App\Models\InterestType;
use App\Models\JewelleryAcquisitionMethod;
use App\Models\LiabilityType;
use App\Models\LoanFacilityType;
use App\Models\Nationality;
use App\Models\NatureOfDeposit;
use App\Models\NatureOfInterestPositionHeld;
use App\Models\NatureOfInvestment;
use App\Models\OtherIncomeType;
use App\Models\Province;
use App\Models\PublicAuthority;
use App\Models\SecurityOffered;
use App\Models\TrustPropertyType;
use App\Models\TypeOfInvestment;
use App\Models\ValuableItemCategory;
use App\Models\VehicleType;
use App\Models\VirtualAssetPlatform;
use App\Models\VirtualAssetsAcquiredType;
use App\Models\VirtualAssetType;
use App\Models\VisaType;
use App\Models\Election;
use App\Models\DeclarationConsecutiveYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PhpParser\Builder\Declaration;

class APIMasterDataController extends Controller
{
    public function getMasterData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'masterData' => 'required|string',
            'lang'       => 'required|string|in:en,si,ta',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        $allowedModels = [
            'City' => [
                'class' => City::class,
                'en' => 'city_name_en',
                'si' => 'city_name_si',
                'ta' => 'city_name_ta',
            ],
            'Country' => [
                'class' => Country::class,
                'en' => 'country_name_en',
                'si' => 'country_name_si',
                'ta' => 'country_name_ta',
            ],
            'Currency' => [
                'class' => Currency::class,
                'en' => 'currency_name_en',
                'si' => 'currency_name_si',
                'ta' => 'currency_name_ta',
            ],
            'DeclarationType' => [
                'class' => DeclarationType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'PublicAuthority' => [
                'class' => PublicAuthority::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'Designation' => [
                'class' => Designation::class,
                'en' => 'designation_name_en',
                'si' => 'designation_name_si',
                'ta' => 'designation_name_ta',
            ],
            'VisaType' => [
                'class' => VisaType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'AcquisitionMode' => [
                'class' => AcquisitionMode::class,
                'en' => 'mode_name_en',
                'si' => 'mode_name_si',
                'ta' => 'mode_name_ta',
            ],
            'VehicleType' => [
                'class' => VehicleType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'BankAccountType' => [
                'class' => BankAccountType::class,
                'en' => 'account_type_name_en',
                'si' => 'account_type_name_si',
                'ta' => 'account_type_name_ta',
            ],
            'VirtualAssetType' => [
                'class' => VirtualAssetType::class,
                'en' => 'asset_type_name_en',
                'si' => 'asset_type_name_si',
                'ta' => 'asset_type_name_ta',
            ],
            'IncomeType' => [
                'class' => IncomeType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'ExpenseType' => [
                'class' => ExpenseType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'InterestType' => [
                'class' => InterestType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'ImmovableAssetType' => [
                'class' => ImmovableAssetType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'Province' => [
                'class' => Province::class,
                'en' => 'province_name_en',
                'si' => 'province_name_si',
                'ta' => 'province_name_ta',
            ],
            'District' => [
                'class' => District::class,
                'en' => 'district_name_en',
                'si' => 'district_name_si',
                'ta' => 'district_name_ta',
            ],
            'DeclarantRelationshipType' => [
                'class' => DeclarantRelationshipType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'Nationality' => [
                'class' => Nationality::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'ValuableItemCategory' => [
                'class' => ValuableItemCategory::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'CommercialsableIntangibleAssetsType' => [
                'class' => CommercialsableIntangibleAssetsType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'BankFinanceCompany' => [
                'class' => BankFinanceCompany::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'VirtualAssetPlatform' => [
                'class' => VirtualAssetPlatform::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'LoanFacilityType' => [
                'class' => LoanFacilityType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'LiabilityType' => [
                'class' => LiabilityType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'CooperateEntityType' => [
                'class' => CooperateEntityType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'NatureOfInterestPositionHeld' => [
                'class' => NatureOfInterestPositionHeld::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'InsuranceCompanyIssuer' => [
                'class' => InsuranceCompanyIssuer::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'InsurancePolicyType' => [
                'class' => InsurancePolicyType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'CorporateCompany' => [
                'class' => CorporateCompany::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'SecurityOffered' => [
                'class' => SecurityOffered::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'DeclarantFormPage' => [
                'class' => DeclarantFormPage::class,
                'en' => 'page_name_en',
                'si' => 'page_name_si',
                'ta' => 'page_name_ta',
            ],
            'OtherIncomeType' => [
                'class' => OtherIncomeType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'JewelleryAcquisitionMethod' => [
                'class' => JewelleryAcquisitionMethod::class,
                'en' => 'mode_name_en',
                'si' => 'mode_name_si',
                'ta' => 'mode_name_ta',
            ],
            'IntangibleAcquisitionMethod' => [
                'class' => IntangibleAcquisitionMethod::class,
                'en' => 'mode_name_en',
                'si' => 'mode_name_si',
                'ta' => 'mode_name_ta',
            ],
            'DesignationClass' => [
                'class' => DesignationClass::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'TrustPropertyType' => [
                'class' => TrustPropertyType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'NatureOfDeposit' => [
                'class' => NatureOfDeposit::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'TypeOfInvestment' => [
                'class' => TypeOfInvestment::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'NatureOfInvestment' => [
                'class' => NatureOfInvestment::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'VirtualAssetsAcquiredType' => [
                'class' => VirtualAssetsAcquiredType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'Election' => [
                'class' => Election::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'DeclarationConsecutiveYear' => [
                'class' => DeclarationConsecutiveYear::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],

        ];

        $inputKey = $request->masterData;

        if (!array_key_exists($inputKey, $allowedModels)) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Invalid Master Data Type provided.',
            ], 400);
        }

        $config = $allowedModels[$inputKey];
        $modelClass = $config['class'];

        $lang = $request->input('lang', 'en');


        $targetColumn = $config[$lang] ?? $config['en'];

        $table = (new $modelClass)->getTable();

        $orderColumn = Schema::hasColumn($table, 'display_order')
                        ? 'display_order'
                        : $targetColumn;

        $masterData = $modelClass::where('status', 'Y')
            ->where('is_delete', '0')
            ->select('id', "$targetColumn as name")
            ->orderBy($orderColumn, 'asc')
            ->get();

        if ($masterData->isEmpty()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::NODATA,
            ], 200);
        }

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => $masterData,
        ], 200);
    }


    public function getMasterDataDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'masterData' => 'required|string',
            'masterDataIdColumnName' => 'required|string',
            'masterDataIdValue' => 'required',
            'lang'       => 'required|string|in:en,si,ta',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }


        $allowedModels = [
            'City' => [
                'class' => City::class,
                'en' => 'city_name_en',
                'si' => 'city_name_si',
                'ta' => 'city_name_ta',
            ],
            'Country' => [
                'class' => Country::class,
                'en' => 'country_name_en',
                'si' => 'country_name_si',
                'ta' => 'country_name_ta',
            ],
            'Currency' => [
                'class' => Currency::class,
                'en' => 'currency_name_en',
                'si' => 'currency_name_si',
                'ta' => 'currency_name_ta',
            ],
            'DeclarationType' => [
                'class' => DeclarationType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'PublicAuthority' => [
                'class' => PublicAuthority::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'Designation' => [
                'class' => Designation::class,
                'en' => 'designation_name_en',
                'si' => 'designation_name_si',
                'ta' => 'designation_name_ta',
            ],
            'VisaType' => [
                'class' => VisaType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'AcquisitionMode' => [
                'class' => AcquisitionMode::class,
                'en' => 'mode_name_en',
                'si' => 'mode_name_si',
                'ta' => 'mode_name_ta',
            ],
            'VehicleType' => [
                'class' => VehicleType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'BankAccountType' => [
                'class' => BankAccountType::class,
                'en' => 'account_type_name_en',
                'si' => 'account_type_name_si',
                'ta' => 'account_type_name_ta',
            ],
            'VirtualAssetType' => [
                'class' => VirtualAssetType::class,
                'en' => 'asset_type_name_en',
                'si' => 'asset_type_name_si',
                'ta' => 'asset_type_name_ta',
            ],
            'IncomeType' => [
                'class' => IncomeType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'ExpenseType' => [
                'class' => ExpenseType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'InterestType' => [
                'class' => InterestType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'ImmovableAssetType' => [
                'class' => ImmovableAssetType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'Province' => [
                'class' => Province::class,
                'en' => 'province_name_en',
                'si' => 'province_name_si',
                'ta' => 'province_name_ta',
            ],
            'District' => [
                'class' => District::class,
                'en' => 'district_name_en',
                'si' => 'district_name_si',
                'ta' => 'district_name_ta',
            ],
            'DeclarantRelationshipType' => [
                'class' => DeclarantRelationshipType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'Nationality' => [
                'class' => Nationality::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'ValuableItemCategory' => [
                'class' => ValuableItemCategory::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'CommercialsableIntangibleAssetsType' => [
                'class' => CommercialsableIntangibleAssetsType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'BankFinanceCompany' => [
                'class' => BankFinanceCompany::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'VirtualAssetPlatform' => [
                'class' => VirtualAssetPlatform::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'LoanFacilityType' => [
                'class' => LoanFacilityType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'LiabilityType' => [
                'class' => LiabilityType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'CooperateEntityType' => [
                'class' => CooperateEntityType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'NatureOfInterestPositionHeld' => [
                'class' => NatureOfInterestPositionHeld::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'NatureOfInvestment' => [
                'class' => NatureOfInvestment::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'TypeOfInvestment' => [
                'class' => TypeOfInvestment::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'InsuranceCompanyIssuer' => [
                'class' => InsuranceCompanyIssuer::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'InsurancePolicyType' => [
                'class' => InsurancePolicyType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'CorporateCompany' => [
                'class' => CorporateCompany::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'SecurityOffered' => [
                'class' => SecurityOffered::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'DeclarantFormPage' => [
                'class' => DeclarantFormPage::class,
                'en' => 'page_name_en',
                'si' => 'page_name_si',
                'ta' => 'page_name_ta',
            ],
            'OtherIncomeType' => [
                'class' => OtherIncomeType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'JewelleryAcquisitionMethod' => [
                'class' => JewelleryAcquisitionMethod::class,
                'en' => 'mode_name_en',
                'si' => 'mode_name_si',
                'ta' => 'mode_name_ta',
            ],
            'IntangibleAcquisitionMethod' => [
                'class' => IntangibleAcquisitionMethod::class,
                'en' => 'mode_name_en',
                'si' => 'mode_name_si',
                'ta' => 'mode_name_ta',
            ],
            'DesignationClass' => [
                'class' => DesignationClass::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'TrustPropertyType' => [
                'class' => TrustPropertyType::class,
                'en' => 'type_name_en',
                'si' => 'type_name_si',
                'ta' => 'type_name_ta',
            ],
            'NatureOfDeposit' => [
                'class' => NatureOfDeposit::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'VirtualAssetsAcquiredType' => [
                'class' => VirtualAssetsAcquiredType::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'Election' => [
                'class' => Election::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
            'DeclarationConsecutiveYear' => [
                'class' => DeclarationConsecutiveYear::class,
                'en' => 'name_en',
                'si' => 'name_si',
                'ta' => 'name_ta',
            ],
        ];

        $inputKey = $request->masterData;

        if (!array_key_exists($inputKey, $allowedModels)) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Invalid Master Data Type provided.',
            ], 400);
        }

        $config = $allowedModels[$inputKey];
        $modelClass = $config['class'];
        $idCheck = $config['chacked_relation'] ?? null;

        $lang = $request->input('lang', 'en');


        $targetColumn = $config[$lang] ?? $config['en'];

        $table = (new $modelClass)->getTable();

        $orderColumn = Schema::hasColumn($table, 'display_order')
                        ? 'display_order'
                        : $targetColumn;

        $masterData = $modelClass::where('status', 'Y')
            ->where($request->masterDataIdColumnName, $request->masterDataIdValue)
            ->where('is_delete', '0')
            // The query inside this closure only runs if $idCheck is true
            ->when($idCheck, function ($query) use ($idCheck, $request) {
                return $query->where($idCheck, $request->masterDataIdValue);
            })
            ->select('id', "$targetColumn as name")
            ->orderBy($orderColumn, 'asc')
            ->get();

        if ($masterData->isEmpty()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::NODATA,
            ], 200);
        }
        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => $masterData,
        ], 200);
    }

    public function getCountryCodeDetails(Request $request)
    {

        $countryCodes = Country::where('status', 'Y')->where('is_delete', 0)->select('country_code')->orderBy('id')->get();

        if ($countryCodes->isEmpty()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::NODATA,
            ], 200);
        }

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => $countryCodes,
        ], 200);

    }

    public function getDeclarationTypeDetails(Request $request)
    {
        $lang = $request->get('lang', 'en');

        $column = match ($lang) {
            'si' => 'type_name_si',
            'ta' => 'type_name_ta',
            default => 'type_name_en',
        };

        $declarationTypes = DeclarationType::where('status', 'Y')
            ->where('is_delete', 0)
            ->orderBy('display_order')
            ->get([
                'id',
                'time_period_category_id',
                'enable_on_month',
                'enable_on_day',
                'disable_on_month',
                'disable_on_day',
                'display_order',
                "$column as name"
            ]);

        if ($declarationTypes->isEmpty()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::NODATA,
            ], 200);
        }

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => $declarationTypes,
        ], 200);
    }

    public function getSecondaryEmpDesignationList(Request $request)
    {
        $lang = $request->get('lang', 'en');

        $column = match ($lang) {
            'si' => 'designation_name_si',
            'ta' => 'designation_name_ta',
            default => 'designation_name_en',
        };

        $designationList = Designation::where('status', 'Y')
                                    ->where('is_delete', 0)
                                    ->whereNotIn('id', [2, 3])
                                    ->orderBy('designation_name_en')
                                    ->get([
                                        'id',
                                        "$column as name"
                                    ]);

        return response()->json([
            'status' => APIResponseMessage::SUCCESS_STATUS,
            'message' => APIResponseMessage::DATAFETCHED,
            'data' => $designationList,
        ], 200);
    }
}
