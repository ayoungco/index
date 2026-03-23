<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';
}
