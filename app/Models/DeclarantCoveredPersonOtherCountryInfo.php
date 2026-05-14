<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class DeclarantCoveredPersonOtherCountryInfo extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'declarant_registration_id',
        'declarant_covered_person_personal_info_id',
        'declaration_covered_person_remote_id',
        'foreign_country_id',
        'foreign_country_name',
        'residency_status_id',
        'residency_status_name',
        'is_delete',
        'created_by'
    ];

    public function countries()
    {
        return $this->belongsTo(Country::class, 'foreign_country_id')->withDefault();
    }

    public function visaTypes()
    {
        return $this->belongsTo(VisaType::class, 'residency_status_id')->withDefault();
    }
}
