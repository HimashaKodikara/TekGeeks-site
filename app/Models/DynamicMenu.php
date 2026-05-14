<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicMenu extends Model
{
    protected $fillable = [
        'icon',
        'title',
        'page_id',
        'url',
        'parent_id',
        'order',
        'is_parent',
        'show_menu',
        'parent_order',
        'child_order',
        'fOrder'
    ];
}
