<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeProject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'banner-image',
        'content_image',
        'teckstack',
        'company_logo',
        'awards',
        'case_study_link',
        'website',
    ];

    protected $casts = [
        'teckstack' => 'array',
        'awards' => 'array',
    ];
}
