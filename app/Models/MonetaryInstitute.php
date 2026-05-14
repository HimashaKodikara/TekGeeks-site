<?php

namespace App\Models;
use App\Traits\SyncsToRemote;
use Illuminate\Database\Eloquent\Model;

class MonetaryInstitute extends Model
{
    // use SyncsToRemote;
    protected $fillable = [
        'monetary_institute_name',
        'status',
        'is_delete',
    ];

    public function institute()
    {
        return $this->belongsTo(PublicAuthority::class, 'institute_id');
    }

    public function designationClass()
    {
        return $this->belongsTo(DesignationClass::class, 'class_id');
    }

}
