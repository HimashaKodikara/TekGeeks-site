<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CooperateEntityType extends Model
{
    protected $fillable = [
        'name_en',
        'name_si',
        'name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
