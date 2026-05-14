<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MonetaryInstituteDetail extends Model
{
    protected $fillable = [
        'monetary_institute_id',
        'designation_class_id',
        'public_authority_id',
        'designation_id'
    ];


    public function designations()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function institute()
    {
        return $this->belongsTo(PublicAuthority::class, 'public_authority_id');
    }

    public function designationClass()
    {
        return $this->belongsTo(DesignationClass::class, 'designation_class_id');
    }
}
