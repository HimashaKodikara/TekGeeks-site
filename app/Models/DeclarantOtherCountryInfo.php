<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class DeclarantOtherCountryInfo extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'declarant_registration_id',
        'foreign_country_id',
        'foreign_country_name',
        'residency_status_id',
        'residency_status_name',
        'status',
        'is_delete'
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
