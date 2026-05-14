<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccountType extends Model
{
    protected $fillable = [
        'account_type_name_en',
        'account_type_name_si',
        'account_type_name_ta',
        'display_order',
        'status',
        'is_delete'
    ];
}
