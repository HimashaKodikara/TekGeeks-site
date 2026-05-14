<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class DeclarantCoveredPersonPersonalInfo extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'declarant_registration_id',
        'declaration_remote_id',
        'cp_added_method',
        'sharing_key',
        'relationship_with_declarant_id',
        'relationship_with_declarant_name',
        'full_name',
        'name_with_initials',
        'name_with_initials_eng',
        'date_of_birth',
        'nationality_id',
        'nationality_name',
        'nic',
        'passport',
        'tin',
        'sl_unique_digital_id_number',
        'country_code_personal_mobile',
        'personal_mobile_number',
        'country_code_fixed_mobile',
        'fixed_mobile_number',
        'personal_email',
        'permanent_country_id',
        'permanent_country_name',
        'permanent_province_id',
        'permanent_district_id',
        'permanent_district_name',
        'permanent_city_id',
        'permanent_city_name',
        'permanent_postal_code',
        'permanent_street_name',
        'permanent_apartment_house_name',
        'permanent_block_house_number',
        'permanent_residential_address',
        'is_same_as_permanent_address',
        'current_country_id',
        'current_country_name',
        'current_province_id',
        'current_district_id',
        'current_district_name',
        'current_city_id',
        'current_city_name',
        'current_postal_code',
        'current_street_name',
        'current_apartment_house_name',
        'current_block_house_number',
        'current_residential_address',
        'do_hold_other_country_residency',
        'is_delete',
        'created_by'
    ];

    public function relationshipwithdeclarant()
    {
        return $this->belongsTo(DeclarantRelationshipType::class, 'relationship_with_declarant_id')->withDefault();
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id')->withDefault();
    }

    public function permanentCountry()
    {
        return $this->belongsTo(Country::class, 'permanent_country_id')->withDefault();
    }

    public function permanentDistrict()
    {
        return $this->belongsTo(District::class, 'permanent_district_id')->withDefault();
    }

    public function permanentCity()
    {
        return $this->belongsTo(City::class, 'permanent_city_id')->withDefault();
    }

    public function currentCountry()
    {
        return $this->belongsTo(Country::class, 'current_country_id')->withDefault();
    }

    public function currentDistrict()
    {
        return $this->belongsTo(District::class, 'current_district_id')->withDefault();
    }

    public function currentCity()
    {
        return $this->belongsTo(City::class, 'current_city_id')->withDefault();
    }

    public function otherCountries()
    {
        return $this->hasMany(
            DeclarantCoveredPersonOtherCountryInfo::class,
            'declarant_covered_person_personal_info_id',   // FK column in child table
            'id'                  // PK column in parent table
        )->where('is_delete', 0);
    }
}
