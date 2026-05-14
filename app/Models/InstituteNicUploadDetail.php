<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteNicUploadDetail extends Model
{
    protected $fillable = [
        'institute_nic_upload_id',
        'nic',
        'uploaded_name',
        'uploaded_email',
        'uploaded_designation',
        'uploaded_institution_name',
        'system_name',
        'email',
        'designation',
        'is_found',
        'name_match',
        'email_match',
    ];

    protected $casts = [
        'is_found'    => 'boolean',
        'name_match'  => 'boolean',
        'email_match' => 'boolean',
    ];

    public function upload()
    {
        return $this->belongsTo(InstituteNicUpload::class, 'institute_nic_upload_id');
    }
}
