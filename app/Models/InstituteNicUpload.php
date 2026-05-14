<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteNicUpload extends Model
{
    protected $fillable = [
        'institute_id',
        'uploaded_by',
        'declaration_year',
        'total_count',
        'found_count',
        'not_found_count',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function details()
    {
        return $this->hasMany(InstituteNicUploadDetail::class, 'institute_nic_upload_id');
    }

    public function foundDetails()
    {
        return $this->hasMany(InstituteNicUploadDetail::class, 'institute_nic_upload_id')
                    ->where('is_found', true);
    }

    public function notFoundDetails()
    {
        return $this->hasMany(InstituteNicUploadDetail::class, 'institute_nic_upload_id')
                    ->where('is_found', false);
    }
}
