<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSentLog extends Model
{
    public $timestamps = true;
    protected $table = TBL_SMS_SENT_LOG;
}
