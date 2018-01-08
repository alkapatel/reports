<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    public $timestamps = true;
    protected $table = TBL_CLIENT_USER;

    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'client_id','send_email','status'];

}
