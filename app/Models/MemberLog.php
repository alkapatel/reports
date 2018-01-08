<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLog extends Model
{
    public $timestamps = true;
    protected $table = TBL_MEMBER_LOG;
    /**
     * @var array
     */
    protected $fillable = ['user_id','member_id', 'actionid', 'actionvalue', 'remark', 'ipaddress'];


    public static function writeadminlog($params){
        $obj = new \App\Models\MemberLog();

        $obj->user_id	= (isset($params['user_id'])) ? $params['user_id'] : NULL;
        $obj->member_id	= (isset($params['member_id'])) ? $params['member_id'] : NULL;
        $obj->actionid		= (isset($params['actionid'])) ? $params['actionid'] : '';
        $obj->actionvalue	= (isset($params['actionvalue'])) ? $params['actionvalue'] : '';
        $obj->ipaddress		= GetUserIp();
        $obj->remark		= (isset($params['remark'])) ? $params['remark'] : '';

        if((!empty($obj->member_id) || !empty($obj->user_id)) && !empty($obj->actionid)){
            $obj->save();
        }
    }
}
