<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'heading_en',
        'heading_si',
        'heading_ta',
        'description_en',
        'description_si',
        'description_ta',
        'detail_description_en',
        'detail_description_si',
        'detail_description_ta',
        'icon_path',
        'display_order',
        'is_show_in_portal',
        'status',
        'is_delete'
    ];
}
