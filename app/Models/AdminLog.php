<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
/**
 * @property int $id
 * @property integer $adminuserid
 * @property integer $actionid
 * @property string $actionvalue
 * @property string $remark
 * @property string $ipaddress
 * @property string $created_at
 * @property string $updated_at
 */
class AdminLog extends Model
{
    public $timestamps = true;
    protected $table = TBL_ADMIN_LOG;
    /**
     * @var array
     */
    protected $fillable = ['adminuserid', 'actionid', 'actionvalue', 'remark', 'ipaddress', 'created_at', 'updated_at'];

    public static function writeadminlog($params){
        $obj = new \App\Models\AdminLog();

        $obj->adminuserid	= (isset($params['adminuserid'])) ? $params['adminuserid'] : '';
        $obj->actionid		= (isset($params['actionid'])) ? $params['actionid'] : '';
        $obj->actionvalue	= (isset($params['actionvalue'])) ? $params['actionvalue'] : '';
        $obj->ipaddress		= GetUserIp();
        $obj->remark		= (isset($params['remark'])) ? $params['remark'] : '';

        if(!empty($obj->adminuserid) && !empty($obj->actionid)){
            $obj->save();
        }
    }  

      
}
