<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
	protected $fillable = ['firstname','lastname','phone','whats_app_phone','village_name','address','professional'];
    public $timestamps = true;
    protected $table = TBL_USER_DETAILS;
}
