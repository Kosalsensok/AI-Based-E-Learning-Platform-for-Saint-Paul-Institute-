<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_email',
        'daily_limit',
        'used_today',
        'status',
    ];
}
