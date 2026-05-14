<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'authority_id',
        'designation_name_en',
        'designation_name_si',
        'designation_name_ta',
        'is_visible_public',
        'is_eligible_declarant',
        'status',
        'is_delete'
    ];

    public function authorities()
    {
        return $this->belongsTo(PublicAuthority::class, 'authority_id')->withDefault();
    }

    public function publicAuthorities()
    {
        return $this->hasMany(PublicAuthority::class, 'designation_class_id', 'id')
                    ->where('status', 'Y')
                    ->where('is_delete', 0);
    }

    public function users()
    {
        return $this->hasMany(DeclarantRegistration::class, 'designation_id');
    }
}
