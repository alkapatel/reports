<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClientEmployee extends Model
{
    public $timestamps = true;
    protected $table = TBL_CLIENT_EMPLOYEE;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'client_id'];

    public static function getUserList($client_type){

        $users = ClientEmployee::join(TBL_USERS,TBL_CLIENT_EMPLOYEE.".user_id","=",TBL_USERS.".id")
                ->where(TBL_CLIENT_EMPLOYEE.'.client_id',$client_type)->orderby(TBL_USERS.'.name')->pluck(TBL_USERS.".name",TBL_USERS.".id")->all();

        return $users;
    }
    

}
