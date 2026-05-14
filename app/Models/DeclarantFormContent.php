<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeclarantFormContent extends Model
{
    protected $fillable = [
        'page_id',
        'question_en',
        'question_si',
        'question_ta',
        'content_en',
        'content_si',
        'content_ta',
        'status',
        'is_delete',
    ];
}
