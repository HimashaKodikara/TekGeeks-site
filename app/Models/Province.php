<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'province_name_en',
        'province_name_si',
        'province_name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
    
    public function districts()
    {
        return $this->hasMany(District::class, 'province_id');
    }
}
