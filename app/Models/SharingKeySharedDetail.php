<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharingKeySharedDetail extends Model
{
    protected $fillable = [
        'sharing_key_detail_id',
        'covered_person_id',
        'sharing_sections_id',
        'is_declarant_included'
    ];

    public function sharingKeyDetail()
    {
        return $this->belongsTo(SharingKeyDetail::class, 'sharing_key_detail_id');
    }

    public function coveredPersons()
    {
        return $this->belongsTo(DeclarantCoveredPersonPersonalInfo::class, 'covered_person_id');
    }
}
