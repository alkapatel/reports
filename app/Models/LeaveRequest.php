<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;
class LeaveRequest extends Model
{
    public $timestamps = true;
    protected $table = TBL_LEAVE_REQUEST;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'from_date', 'to_date', 'status', 'description', 'created_by','reject_reason'];

   
    public function User() {
        return $this->belongsToMany('App\Models\User', TBL_USER, 'id');
    }

    public static function getLeaveList($params)
    {
            $search_start_leave = isset($params['search_start_leave']) ? trim($params['search_start_leave']) : '';
            $search_end_leave = isset($params['search_end_leave']) ? trim($params['search_end_leave']) : '';
            $from_date = isset($params['search_start_date']) ? trim($params['search_start_date']) : '';
            $to_date = isset($params['search_end_date']) ? trim($params['search_end_date']) : '';
   
            $search_user = isset($params['search_user']) ? trim($params['search_user']) : '';
            $search_status = isset($params['search_status']) ? trim($params['search_status']) : '';

            $query = \DB::table(TBL_LEAVE_REQUEST)
	                ->leftJoin(TBL_USERS,TBL_USERS.".id","=",TBL_LEAVE_REQUEST.".user_id")
                     ->select
                     (
                        \DB::raw(TBL_LEAVE_REQUEST.'.*,'.TBL_USERS.'.name as username')
                     );

            // filter query 
            if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_LEAVE_REQUEST.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_LEAVE_REQUEST.".created_at","<=",addslashes($convertToDate));
                }
                if (!empty($search_start_leave) || !empty($search_end_leave)){

                    $query = $query->whereBetween(TBL_LEAVE_REQUEST.'.from_date', [$search_start_leave, $search_end_leave])
                                    ->orwhereBetween(TBL_LEAVE_REQUEST.'.to_date', [$search_start_leave,$search_end_leave]);
                }                                         
                if(!empty($search_user))
                {
                    $query = $query->where(TBL_LEAVE_REQUEST.".user_id",$search_user);
                }
                if($search_status == "1" || $search_status == "0" || $search_status == "2")
                {
                    $query = $query->where(TBL_LEAVE_REQUEST.".status", $search_status);
                }

            return (isset($params['is_download']) && $params['is_download'] == 1)  ? $query->limit(1000)->get():$query->paginate(10);
    }
	public static function yesterdayOnLeave(){

        $yesterday =  date("Y-m-d",strtotime("yesterday"));
        $yesterday_leave = \App\Models\LeaveDetail::select(TBL_LEAVE_DETAIL.".*",TBL_LEAVE_REQUEST.'.user_id as user_id')
                ->join(TBL_LEAVE_REQUEST,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                ->where(TBL_LEAVE_REQUEST.'.status',1)
                ->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$yesterday."%") 
                ->get();
         return $yesterday_leave;       
    }
}
