<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	public $timestamps = true;
    protected $table = TBL_TASK;

    /**
     * @var array
     */
    protected $fillable = ['id','title','description','total_time','','status','project_id','ref_link','user_id','task_date'];

    public function Project()
    {
        return $this->hasMany('App\Models\Project');
    }
	public static function listFilter($query)
    {
        $search_start_date = request()->get("search_start_date");
        $search_end_date = request()->get("search_end_date");                                
        $search_id = request()->get("search_id");                                
        $search_project = request()->get("search_project");                                
        $search_title = request()->get("search_title");                                
        $search_status = request()->get("search_status");                            
        $search_user = request()->get("search_user");
        $search_client = request()->get("search_client");
        $search_hour = request()->get("search_hour");                                
        $search_hour_op = request()->get("search_hour_op");                                
        $search_min = request()->get("search_min");
        $search_min_op = request()->get("search_min_op");
		$is_download = request()->get("isDownload");

        if(!empty($search_hour) && empty($search_min))
        {
            $search_min ='0.00';   
        }
        else if(empty($search_hour) && !empty($search_min))
        {
            $search_hour = '0.00';
        } 
        if (!empty($search_start_date)){

            $from_date=$search_start_date.' 00:00:00';
            $convertFromDate= $from_date;

            $query = $query->where(TBL_TASK.".task_date",">=",addslashes($convertFromDate));
        }
        if (!empty($search_end_date)){

            $to_date=$search_end_date.' 23:59:59';
            $convertToDate= $to_date;

            $query = $query->where(TBL_TASK.".task_date","<=",addslashes($convertToDate));
        }
        if(!empty($search_id))
        {
            $idArr = explode(',', $search_id);
            $idArr = array_filter($idArr);                
            if(count($idArr)>0)
            {
                $query = $query->whereIn(TBL_TASK.".id",$idArr);
            } 
        }
        if(!empty($search_project))
        {
            $query = $query->where(TBL_TASK.".project_id", $search_project);
        }
        if(!empty($search_client))
        {
            $query = $query->where(TBL_PROJECT.".client_id", $search_client);
        }
        if(!empty($search_title))
        {
            $query = $query->where(TBL_TASK.".title", 'LIKE', '%'.$search_title.'%');
        }
        if (!empty($search_hour)) {
               $query = $query->where(TBL_TASK.".hour", $search_hour_op, $search_hour);
        }
        if (!empty($search_min)) {
               $query = $query->where(TBL_TASK.".min", $search_min_op, $search_min);
        }
        if($search_status == "1" || $search_status == "0")
        {
            $query = $query->where(TBL_TASK.".status", $search_status);
        }
        if(!empty($search_user))
        {
            $query = $query->where(TBL_TASK.".user_id",$search_user);
        }
		if(!empty($is_download) && $is_download == 1)
        {
            $query = $query->limit(1000)->get();
        }

        return $query;
    }
	public static function halfLeaveUsers($yesterday)
    {
        $users = \DB::table(TBL_USERS)
                ->select([TBL_USERS.'.name',TBL_USERS.'.id as userid'])
                ->join(TBL_LEAVE_REQUEST,TBL_USERS.".id","=",TBL_LEAVE_REQUEST.".user_id")
                ->join(TBL_LEAVE_DETAIL,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                ->where(TBL_LEAVE_REQUEST.'.status',1)
                ->where(TBL_LEAVE_DETAIL.'.is_half',1)
                ->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$yesterday."%")
                ->get();

        $ids = [];

        if($users)
        {
            foreach($users as $user)
            {
                $ids[] = $user->userid;
            }                
        }

        return $ids;
    }
    public static function fullLeaveUsers($yesterday)
    {
        $users = \DB::table(TBL_USERS)
                ->select([TBL_USERS.'.name',TBL_USERS.'.id as userid'])
                ->join(TBL_LEAVE_REQUEST,TBL_USERS.".id","=",TBL_LEAVE_REQUEST.".user_id")
                ->join(TBL_LEAVE_DETAIL,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                ->where(TBL_LEAVE_REQUEST.'.status',1)
                ->where(TBL_LEAVE_DETAIL.'.is_half',0)
                ->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$yesterday."%")
                ->get();

        $ids = [];

        if($users)
        {
            foreach($users as $user)
            {
                $ids[] = $user->userid;
            }                
        }

        return $ids;
    }
    public static function BelowEightHrs($notUsers,$yesterday){

        $query = \DB::table(TBL_TASK)
                ->select([TBL_USERS.'.name',TBL_USERS.'.image',TBL_USERS.'.id as userid',TBL_TASK.'.task_date as date',
                    \DB::raw("sum(".TBL_TASK.".total_time) as total")
                ])
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_TASK.".user_id")
                ->where(TBL_TASK.'.task_date','LIKE',"%".$yesterday."%");

        if(count($notUsers) > 0)
        {
            $query = $query->whereNotin(TBL_USERS.".id",$notUsers);
        }       
        $query = $query->groupBy(TBL_TASK.'.user_id') 
                ->having('total','<','8')
                ->get();
        return $query;
    }
    public static function NotAdded($fullLeaveUsers,$yesterday){

        $query = \DB::table(TBL_USERS)
                ->select(TBL_USERS.".name as name",TBL_USERS.".id as userid",TBL_USERS.".email as email" )
                ->whereNotIn(TBL_USERS.'.id', function ($query) use ($yesterday) {

                    return $query->select(TBL_TASK.'.user_id')->from(TBL_TASK)
                    ->where(TBL_TASK.'.task_date','LIKE',"%".$yesterday."%");
                });
         
        if(count($fullLeaveUsers) > 0)
        {
            $query = $query->whereNotin(TBL_USERS.".id",$fullLeaveUsers);
        }
            $query = $query->where(TBL_USERS.'.id','!=',SUPER_ADMIN_ID)
				->where(TBL_USERS.'.id','!=',1)
                ->where(TBL_USERS.'.is_add_task','!=',0)
                ->whereNull(TBL_USERS.'.client_user_id')
                ->where(TBL_USERS.'.status',ACTIVE_USER)
                ->get();
                 
        return $query;
    }
    public static function BelowFourHrs($users,$yesterday)
    {
        $below_four_hour = false;
        if(count($users) > 0)
        {
            
            $below_four_hour = \DB::table(TBL_TASK)
                    ->select([TBL_USERS.'.name',TBL_USERS.'.id as userid',TBL_TASK.'.task_date as date',TBL_USERS.'.name as username',
                        \DB::raw("sum(".TBL_TASK.".total_time) as total")
                    ])
                    ->join(TBL_USERS,TBL_USERS.".id","=",TBL_TASK.".user_id")
                    ->where(TBL_TASK.'.task_date','LIKE',"%".$yesterday."%")
                    ->whereIn(TBL_USERS.".id",$users)
                    ->having('total','<','4')
                    ->groupBy(TBL_TASK.'.user_id')
                    ->get();            
        }    

        return $below_four_hour;
    }
	public static function final_belows($below_eight_hour,$below_four_hour){
        $below_8 = array();
        $i =0;
        foreach ($below_eight_hour as $key) {
            $i = $key->userid;
            $below_8[$i]['user_id'] = $key->userid;
            $below_8[$i]['name'] = $key->name;
            $below_8[$i]['total'] = $key->total;
            $below_8[$i]['date'] = $key->date; 
            $below_8[$i]['below'] = 8; 
        }
        $below_4 = array();
        $i=0;

        if($below_four_hour)
        {
            foreach ($below_four_hour as $key) {
                $i = $key->userid;
                $below_4[$i]['user_id'] = $key->userid;
                $below_4[$i]['name'] = $key->name;
                $below_4[$i]['total'] = $key->total;
                $below_4[$i]['date'] = $key->date;
                $below_4[$i]['below'] = 4;
            }            
        }
        $final_belows = array();
      
        $final_belows = array_merge($below_8,$below_4);
        return $final_belows;
    }
	public static function yesterdayHoliday($yesterday)
    {
        $holi = 0;
        $date = \DB::table(TBL_HOLIDAYS)
                ->select([TBL_HOLIDAYS_DETAILS.'.date as holidate'])
                ->join(TBL_HOLIDAYS_DETAILS,TBL_HOLIDAYS.".id","=",TBL_HOLIDAYS_DETAILS.".holiday_id")
                ->where(TBL_HOLIDAYS.'.status',1)
                ->where(TBL_HOLIDAYS_DETAILS.'.date','LIKE',"%".$yesterday."%")
                ->first();
        if($date){
            $holi = 1;
        }
        return $holi;
    }
	public static function AuthTaskHrs(){
        $auth_user = \Auth::guard("admins")->user()->id;
        $query =  \DB::table(TBL_TASK)
                ->select([
                    \DB::raw("sum(".TBL_TASK.".total_time) as total")
                ])
                ->where(TBL_TASK.'.user_id',$auth_user)
                ->get();
        return $query;
    }
    public static function AuthMonthlyHrs(){
        $auth_user = \Auth::guard("admins")->user()->id;
        $this_month = date('Y-m',strtotime('first day of this month')); 

        $query = \DB::table(TBL_TASK)
                ->select([
                    \DB::raw("sum(".TBL_TASK.".total_time) as total")
                ])
                ->where(TBL_TASK.'.task_date','LIKE',"%".$this_month."%")
                ->where(TBL_TASK.'.user_id',$auth_user)
                ->get();
        return $query;
    }
    public static function AuthTodayHrs(){
        $auth_user = \Auth::guard("admins")->user()->id;
        $today =  date("Y-m-d",strtotime("today"));

        $query = \DB::table(TBL_TASK)
                ->select([
                    \DB::raw("sum(".TBL_TASK.".total_time) as total")
                ])
                ->where(TBL_TASK.'.task_date','LIKE',"%".$today."%")
                ->where(TBL_TASK.'.user_id',$auth_user)
                ->get();
        return $query;
    }
}
