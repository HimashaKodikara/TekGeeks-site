<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CreateDynamicMenuSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(CreateAdminUserSeeder::class);

        //Master Data Seeders
        $this->call(AcquisitionModesTableSeeder::class);
        $this->call(BankAccountTypesTableSeeder::class);
        $this->call(BankFinanceCompaniesTableSeeder::class);
        $this->call(CommercialsableIntangibleAssetsTypesTableSeeder::class);
        $this->call(CooperateEntityTypesTableSeeder::class);
        $this->call(CorporateCompaniesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(ProvincesTableSeeder::class);
        $this->call(DistrictsTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(DeclarantRelationshipTypesTableSeeder::class);
        $this->call(DeclarantFormContentsTableSeeder::class);
        $this->call(DeclarantFormPagesTableSeeder::class);
        $this->call(DeclarationTypesTableSeeder::class);
        $this->call(DesignationClassesTableSeeder::class);
        $this->call(PublicAuthoritiesTableSeeder::class);
        $this->call(DesignationsTableSeeder::class);
        $this->call(ExpenseTypesTableSeeder::class);
        $this->call(ImmovableAssetTypesTableSeeder::class);
        $this->call(IncomeTypesTableSeeder::class);
        $this->call(InsuranceCompanyIssuersTableSeeder::class);
        $this->call(IntangibleAcquisitionMethodsTableSeeder::class);
        $this->call(InterestTypesTableSeeder::class);
        $this->call(JewelleryAcquisitionMethodsTableSeeder::class);
        $this->call(LiabilityTypesTableSeeder::class);
        $this->call(LoanFacilityTypesTableSeeder::class);
        $this->call(NationalitiesTableSeeder::class);
        $this->call(NatureOfDepositsTableSeeder::class);
        $this->call(NatureOfInterestPositionHeldsTableSeeder::class);
        $this->call(NatureOfInvestmentsTableSeeder::class);
        $this->call(OtherIncomeTypesTableSeeder::class);
        $this->call(SecurityOfferedTableSeeder::class);
        $this->call(TrustPropertyTypesTableSeeder::class);
        $this->call(TypeOfInvestmentsTableSeeder::class);
        $this->call(ValuableItemCategoriesTableSeeder::class);
        $this->call(VehicleTypesTableSeeder::class);
        $this->call(VirtualAssetPlatformsTableSeeder::class);
        $this->call(VirtualAssetTypesTableSeeder::class);
        $this->call(VirtualAssetsAcquiredTypesTableSeeder::class);
        $this->call(VisaTypesTableSeeder::class);
        $this->call(FaqsTableSeeder::class);
        $this->call(InsurancePolicyTypesTableSeeder::class);
        $this->call(ElectionsTableSeeder::class);
        $this->call(DeclarationConsecutiveYearsTableSeeder::class);
    }
}
