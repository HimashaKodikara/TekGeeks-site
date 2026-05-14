<?php

namespace App\Models;

// use App\Traits\SyncsToRemote;
use Illuminate\Database\Eloquent\Model;

class DeclarationType extends Model
{
    // use SyncsToRemote;
    protected $fillable = [
        'type_name_en',
        'type_name_si',
        'type_name_ta',
        'declaration_type_prefix',
        'time_period_category_id',
        'enable_on_month',
        'enable_on_day',
        'disable_on_month',
        'disable_on_day',
        'display_order',
        'status',
        'is_delete',
        'max_editable_days'
    ];

    public function statusOfDeclarations() {
        return $this->hasMany(StatusOfDeclaration::class, 'declaration_type_id');
    }
}
