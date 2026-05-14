<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustPropertyType extends Model
{
    protected $fillable = [
        'type_name_en',
        'type_name_si',
        'type_name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
