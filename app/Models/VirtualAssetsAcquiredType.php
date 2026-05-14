<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAssetsAcquiredType extends Model
{
    protected $table = 'virtual_assets_acquired_types';

    protected $fillable = [
        'name_en',
        'name_si',
        'name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
