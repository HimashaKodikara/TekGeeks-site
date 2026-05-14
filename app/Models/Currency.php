<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'currency_name_en',
        'currency_name_si',
        'currency_name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
