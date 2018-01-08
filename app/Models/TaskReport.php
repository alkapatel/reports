<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReport extends Model
{
    public $timestamps = true;
    protected $table = TBL_TASK_REPORT;

    /**
     * @var array
     */
    protected $fillable = ['user_id','task_date'];

    public static function NotAddedTask($daily_tasks,$yesterday_holiday)
    {
    	$day = date('D');
    	if($day != 'Mon' && $yesterday_holiday == 0)
    	{
    		if(!empty($daily_tasks)){
    			foreach ($daily_tasks as $task ) {
    				$report = new TaskReport();
    				$report->user_id = $task->userid;
    				$report->task_date = date('Y-m-d');
    				$report->save();
    			}
    		}
    	}
    }

}
