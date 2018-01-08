<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareWithUser extends Model
{
     public $timestamps = true;
    protected $table = TBL_SHARE_USER;

    protected $fillable = ['credential_id','user_id'];


}
