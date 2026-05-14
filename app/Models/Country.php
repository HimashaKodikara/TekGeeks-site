<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_name_en',
        'country_name_si',
        'country_name_ta',
        'nationality_en',
        'nationality_si',
        'nationality_ta',
        'status',
        'is_delete'
    ];
}
