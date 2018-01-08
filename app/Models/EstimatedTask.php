<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimatedTask extends Model
{
    public $timestamps = true;
    protected $table = TBL_ESTIMATED_TASK;

    /**
     * @var array
     */
    protected $fillable = ['id','user_id','project_id','task','estimated_hour','estimated_min','estimated_total_time','actual_hour','actual_min','actual_total_time','status','delivery_description','task_date'];

    public function Project()
    {
        return $this->hasMany('App\Models\Project');
    }

    public static function listFilter($query){
       $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date"); 
                $search_id = request()->get("search_id");                                
                $search_project = request()->get("search_project");                                
                $search_title = request()->get("search_title");                                
                $search_status = request()->get("search_status");
                $search_esti_hour = request()->get("search_esti_hour");
                $search_esti_min = request()->get("search_esti_min");
                $search_esti_hour_op = request()->get("search_esti_hour_op");
                $search_esti_min_op = request()->get("search_esti_min_op");
                $search_act_hour = request()->get("search_act_hour");
                $search_act_min = request()->get("search_act_min");
                $search_act_hour_op = request()->get("search_act_hour_op");
                $search_act_min_op = request()->get("search_act_min_op");
                $search_user = request()->get("search_user");

                if(!empty($search_esti_hour) && empty($search_esti_min))
                {
                    $search_esti_min ='0.00';
                }
                else if(empty($search_esti_hour) && !empty($search_esti_min))
                {
                    $search_esti_hour = '0.00';
                }
                 if(!empty($search_act_hour) && empty($search_act_min))
                {
                    $search_act_min ='0.00';
                }
                else if(empty($search_act_hour) && !empty($search_act_min))
                {
                    $search_act_hour = '0.00';
                } 
                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_ESTIMATED_TASK.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_ESTIMATED_TASK.".created_at","<=",addslashes($convertToDate));
                }
                if(!empty($search_id))
                {
                    $idArr = explode(',', $search_id);
                    $idArr = array_filter($idArr);                
                    if(count($idArr)>0)
                    {
                        $query = $query->whereIn(TBL_ESTIMATED_TASK.".id",$idArr);
                    } 
                }
                if(!empty($search_project))
                {
                    $query = $query->where(TBL_ESTIMATED_TASK.".project_id", $search_project);
                }
                if(!empty($search_user))
                {
                    $query = $query->where(TBL_ESTIMATED_TASK.".user_id", $search_user);
                }
                if(!empty($search_title))
                {
                    $query = $query->where(TBL_ESTIMATED_TASK.".task", 'LIKE', '%'.$search_title.'%');
                }
                if (!empty($search_esti_hour)) {
                       $query = $query->where(TBL_ESTIMATED_TASK.".estimated_hour", $search_esti_hour_op, $search_esti_hour);
                }
                if (!empty($search_esti_min)) {
                       $query = $query->where(TBL_ESTIMATED_TASK.".estimated_min", $search_esti_min_op, $search_esti_min);
                }
                if (!empty($search_act_hour)) {
                       $query = $query->where(TBL_ESTIMATED_TASK.".actual_hour", $search_act_hour_op, $search_act_hour);
                }
                if (!empty($search_act_min)) {
                       $query = $query->where(TBL_ESTIMATED_TASK.".actual_min", $search_act_min_op, $search_act_min);
                }
                if($search_status == "1" || $search_status == "0"|| $search_status == "2"|| $search_status == "3")
                {
                    $query = $query->where(TBL_ESTIMATED_TASK.".status", $search_status);
                }
        return $query;
    }
}
