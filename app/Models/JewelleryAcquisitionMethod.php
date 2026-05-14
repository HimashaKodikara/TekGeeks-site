<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JewelleryAcquisitionMethod extends Model
{
    protected $fillable = [
        'mode_name_en',
        'mode_name_si',
        'mode_name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
