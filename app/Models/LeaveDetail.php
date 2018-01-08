<?php

namespace App\Models;
use App\Models\ClientEmployee;

use Illuminate\Database\Eloquent\Model;

class LeaveDetail extends Model
{
    
    public $timestamps = false;
    protected $table = TBL_LEAVE_DETAIL;

    /**
     * @var array
     */
    protected $fillable = ['leave_id', 'is_half', 'date'];

   
    public static function getCurrentMonthLeaves() 
    {
    	$this_month = date('Y-m',strtotime('first day of this month')); 

    	return self::selectRaw("leave_request_details.date,CONCAT(users.firstname,'',users.lastname) as name,leave_request_details.is_half, leave_requests.status as status")
    	       ->join("leave_requests","leave_requests.id","=","leave_request_details.leave_id")
    	       ->join("users","users.id","=","leave_requests.user_id")
    	       //->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$this_month."%") 
    	       ->where("leave_requests.status",'!=',2)
    	       ->get()
    	       ->toArray();
    }

    public function LeaveRequest() 
    {
        return $this->belongsToMany('App\Models\LeaveRequest', TBL_LEAVE_REQUEST, 'id');
    }
	public static function getAuthUserLeaves() 
    {
        $auth_user = \Auth::guard("admins")->user()->id;
        return self::selectRaw("leave_request_details.date,CONCAT(users.firstname,'',users.lastname) as name,leave_request_details.is_half, leave_requests.status as status")
               ->join("leave_requests","leave_requests.id","=","leave_request_details.leave_id")
               ->join("users","users.id","=","leave_requests.user_id")
               ->where(TBL_LEAVE_REQUEST.'.user_id',$auth_user)
               ->get()
               ->toArray();
    }
	  public static function getClientEmpLeaves() 
    {
        $client_type= 0;
        $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                    $client_type = $client_user->client_id;

            }        
        return self::selectRaw("leave_request_details.date,CONCAT(users.firstname,'',users.lastname) as name,leave_request_details.is_half, leave_requests.status as status")
               ->join("leave_requests","leave_requests.id","=","leave_request_details.leave_id")
               ->join("users","users.id","=","leave_requests.user_id")
               ->join(TBL_CLIENT_EMPLOYEE, TBL_CLIENT_EMPLOYEE.".user_id","=","leave_requests.user_id")
               ->where(TBL_CLIENT_EMPLOYEE.'.client_id',$client_type)
               ->where(TBL_LEAVE_REQUEST.'.status',1)
               ->get()
               ->toArray();
    }
}
