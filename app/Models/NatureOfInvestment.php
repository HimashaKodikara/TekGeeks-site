<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NatureOfInvestment extends Model
{
    protected $table = "nature_of_investments";
    protected $fillable = [
        'name_en',
        'name_si',
        'name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
