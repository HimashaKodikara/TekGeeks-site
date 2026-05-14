<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'province_id',
        'district_name_en',
        'district_name_si',
        'district_name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
    
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
