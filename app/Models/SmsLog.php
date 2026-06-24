<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'customer_name',
        'phone',
        'message',
        'type',
        'job_id',
        'status_code',
        'status_message',
    ];
}
