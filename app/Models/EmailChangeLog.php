<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailChangeLog extends Model
{
    protected $fillable = [
        'declarant_registration_id',
        'new_email',
        'email_otp',
        'email_otp_expires_at',
        'verified_at'
    ];
}
