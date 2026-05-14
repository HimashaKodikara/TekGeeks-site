<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $fillable = [
        'name_en',
        'name_si',
        'name_ta',
        'natonality_code',
        'display_order',
        'status',
        'is_delete'
    ];
}
