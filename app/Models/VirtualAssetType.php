<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAssetType extends Model
{
    protected $fillable = [
        'asset_type_name_en',
        'asset_type_name_si',
        'asset_type_name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
