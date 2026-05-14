<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileNumberChangeLog extends Model
{
    protected $fillable = [
        'declarant_registration_id',
        'country_code',
        'new_mobile_no',
        'mobile_otp',
        'mobile_otp_expires_at',
        'verified_at'
    ];
}
