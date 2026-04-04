<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewaySetting extends Model
{
    protected $fillable = [
        'project_name',
        'waseet_username',
        'waseet_password',
        'api_key',
        'is_connected',
        'last_sync_at',
    ];

    protected $casts = [
        'is_connected' => 'boolean',
        'waseet_password' => 'encrypted',
        'last_sync_at' => 'datetime',
    ];
}
