<?php

namespace App\Models;

// use App\Traits\SyncsToRemote;
use Illuminate\Database\Eloquent\Model;

class PublicAuthority extends Model
{
    // use SyncsToRemote;
    protected $fillable = [
        'designation_class_id',
        'name_en',
        'name_si',
        'name_ta',
        'status',
        'is_delete'
    ];


    public function publicAuthorities()
    {
        return $this->hasMany(District::class, 'province_id');

    }
}
