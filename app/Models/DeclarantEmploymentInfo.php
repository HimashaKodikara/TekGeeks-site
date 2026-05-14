<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class DeclarantEmploymentInfo extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'declarant_registration_id',
        'institution_id',
        'institution_name',
        'designation_id',
        'designation_name',
        'office_address',
        'country_code_office_mobile',
        'office_mobile_no',
        'is_delete'
    ];

    public function designations()
    {
        return $this->belongsTo(Designation::class, 'designation_id')->withDefault();
    }

    public function authorities()
    {
        return $this->belongsTo(PublicAuthority::class, 'institution_id')->withDefault();
    }
}
