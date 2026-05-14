<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionModel extends Model
{
    protected $table = 'user_sessions'; // Adjust table name as needed
    protected $primaryKey = 'session_id';
    public $incrementing = false; // If the primary key is not auto-incrementing
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'session_id'
    ];
}
