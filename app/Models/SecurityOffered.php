<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityOffered extends Model
{
    protected $table = "security_offered";
    protected $fillable = [
        'name_en',
        'name_si',
        'name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
