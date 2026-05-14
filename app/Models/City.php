<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'country_id',
        'province_id',
        'district_id',
        'city_name_en',
        'city_name_si',
        'city_name_ta',
        'status',
        'is_delete'
    ];

    public function countries()
    {
        return $this->belongsTo(Country::class, 'country_id')->withDefault();
    }

    public function provinces()
    {
        return $this->belongsTo(Province::class, 'province_id')->withDefault();
    }

    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }
}
