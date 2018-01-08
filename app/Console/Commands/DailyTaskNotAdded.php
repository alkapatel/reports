<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\LeaveRequest;

class DailyTaskNotAdded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:taskNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation sending mail Process For not added task on yesterday to admin.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Task Query
        $today =  date("Y-m-d");

        $halfLeaveUsers = Task::halfLeaveUsers($today);
        
        $fullLeaveUsers = Task::fullLeaveUsers($today);

        //Admin Not Added
        $not_added_task = Task::NotAdded($fullLeaveUsers,$today);
         
        //Admin Below 8 hours Details
        $below_eight_hour = Task::BelowEightHrs($halfLeaveUsers,$today);

        // Admin Below 4 hours Details
        $below_four_hour = Task::BelowFourHrs($halfLeaveUsers,$today);

        $final_belows = Task::final_belows($below_eight_hour,$below_four_hour);

        $empName = ''; 
        //echo "</pre>";print_r($final_belows);exit;

        //Email For Not Added Users
        if(!empty($not_added_task))
        {
            $today =  date("j,M Y");

            $table = "<p><b>Hi All,</b></p>";
            $table .= "<p>Please find Users not added tasks on ".$today." below.</p>";
            $table .= '<table width="100%" border="0" cellspacing="0" cellpadding="3" style="font-size:13px; border-top:1px solid #666; border-left:1px solid #666; font-family:Arial, Helvetica, sans-serif;">';
            $table .= "<tr>";
            $table .= '<td width="10%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Sr. No.</td>';
            $table .= '<td width="90%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">User Name</td><tr/>';
            $i=1;
        foreach ($not_added_task as $key => $task)
        {   
            $empName = $task->name;
            
            $table .= "<tr>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$i."</td>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$empName."</td><tr/>";
            $i++;
        }
            $table .= "</table>";
            $subject = 'Reports PHPdots: users not added tasks on '.$today;

            $params["to"] = 'jitendra.rathod@phpdots.com';
            $params["subject"] = $subject;
            $params["from"] = 'jitendra.rathod@phpdots.com';
            $params["from_name"] ='Not Added Tasks Notification';
            $params["body"] = $table;
            sendHtmlMail($params);
            
            foreach ($not_added_task as $key => $task)
            {
                $subject = 'Reports PHPdots: You have not added task on '.$today;
                
                $message = '<p><b>HI, '.$task->name.'</</p>';
                $message .='<p>You have not added task on '.$today.'.</p>';
                $message .='<p>please add once.</p>';
                
                $params["to"] = 'jitendra.rathod@phpdots.com';
                $params["subject"] = $subject;
                $params["from"] = $task->email;
                $params["from_name"] ='Task Notification';
                $params["body"] = $message;
                sendHtmlMail($params);
            }
        }
        //Email For Below 8/4 Hours Users
        if(!empty($final_belows))
        {
            $today =  date("j,M Y");

            $table = "<p><b>Hi All,</b></p>";
            $table .= "<p>Please find Users added tasks below eight hours on ".$today." below.</p>";
            $table .= '<table width="100%" border="0" cellspacing="0" cellpadding="3" style="font-size:13px; border-top:1px solid #666; border-left:1px solid #666; font-family:Arial, Helvetica, sans-serif;">';
            $table .= "<tr>";
            $table .= '<td width="10%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Sr. No.</td>';
            $table .= '<td width="80%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">User Name</td><tr/>';
            $table .= '<td width="10%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Hours</td><tr/>';
            $i=1;
        foreach ($final_belows as $key => $task)
        { 
            $empName = $task['name'];
            
            $table .= "<tr>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$i."</td>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$empName."</td><tr/>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$task['total']."</td><tr/>";
            $i++;
        }
            $table .= "</table>";
            $subject = 'Reports PHPdots: users added tasks below eight hours on '.$today;

            $params["to"] = 'jitendra.rathod@phpdots.com';
            $params["subject"] = $subject;
            $params["from"] = 'jitendra.rathod@phpdots.com';
            $params["from_name"] ='Below Eight Hours - Tasks Notification';
            $params["body"] = $table;
            sendHtmlMail($params);

            foreach ($final_belows as $key => $task)
            {
                $subject = 'Reports PHPdots: Task Notification on '.$today;
                
                $user_details = User::find($task['user_id']);

                $message = '<p><b>HI, '.$user_details->name.'</</p>';
                $message = '<p><b>Total Tasks Hours : '.$task['total'].'</</p>';
                $message .='<p>You have added tasks below eight hours on '.$today.'.</p>';
                $message .='<p>please add tasks up to eight hours once.</p>';
                
                $params["to"] = 'jitendra.rathod@phpdots.com';
                $params["subject"] = $subject;
                $params["from"] = $user_details->email;
                $params["from_name"] ='Task Notification';
                $params["body"] = $message;
                sendHtmlMail($params);
            }
        }
    }
}
