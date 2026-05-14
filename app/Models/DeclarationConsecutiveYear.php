<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeclarationConsecutiveYear extends Model
{
    protected $fillable = [
        'name_en',
        'name_si',
        'name_ta',
        'status',
        'display_order',
        'is_delete'
    ];


}
