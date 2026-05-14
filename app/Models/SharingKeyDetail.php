<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharingKeyDetail extends Model
{
    protected $fillable = [
        'declarant_registration_id',
        'recipient_nic',
        'master_declarant_relationship',
        'recipient_email',
        'sharing_key',
        'key_expiration',
        'created_by'
    ];

    public function sharedDetails()
    {
        return $this->hasMany(SharingKeySharedDetail::class, 'sharing_key_detail_id');
    }

    public function relationshipwithdeclarant()
    {
        return $this->belongsTo(DeclarantRelationshipType::class, 'master_declarant_relationship')->withDefault();
    }

}
