<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class DailyReportController extends Controller
{
	public function cronTaskNotification(Request $request ){
		$fromcron = $request->get('fromcron');
	if(!empty($fromcron) && $fromcron == 1){
		//Task Query
        //$today =  date("Y-m-d");
		$today =  date("Y-m-d",strtotime("today"));

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
            $table .= "</table><br/>";
            $table .='<p><i>Thanks & Regards</i></p>';
            $table .='<p>PHPdots</p>';
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
                $url = url('/tasks/create');
				
                $message = '<p><b>HI, '.$task->name.'</</p>';
                $message .='<p>You have not added task on '.$today.'.</p>';
                $message .='<p>please add once.</p>';
                $message .='<p>Link : <a href="'.$url.'">Add Task </a></p><br/>';
                $message .='<p><i>Thanks & Regards</i></p>';
                $message .='<p>PHPdots</p>';
				
                $params["to"] = $task->email;
                $params["subject"] = $subject;
                $params["from"] = 'jitendra.rathod@phpdots.com';
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
            $table .= '<td width="80%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">User Name</td>';
            $table .= '<td width="10%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Hours</td><tr/>';
            $i=1;
        foreach ($final_belows as $key => $task)
        { 
            $empName = $task['name'];
            
            $table .= "<tr>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$i."</td>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$empName."</td>";
            $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$task['total']."</td><tr/>";
            $i++;
        }
            $table .= "</table><br/>";
            $table .='<p><i>Thanks & Regards</i></p>';
            $table .='<p>PHPdots</p>';
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
                $url = url('/tasks/create');
                $user_details = User::find($task['user_id']);

                $message = '<p><b>HI, '.$user_details->name.'</</p>';
                $message .= '<p><b>Total Tasks Hours : '.$task['total'].'</</p>';
                $message .='<p>You have added tasks below eight hours on '.$today.'.</p>';
                $message .='<p>please add tasks up to eight hours once.</p>';
                $message .='<p>Link : <a href="'.$url.'">Add Task </a></p><br/>';
                $message .='<p><i>Thanks & Regards</i></p>';
                $message .='<p>PHPdots</p>';
				
                $params["to"] = $user_details->email;
                $params["subject"] = $subject;
                $params["from"] ='jitendra.rathod@phpdots.com';
                $params["from_name"] ='Task Notification';
                $params["body"] = $message;
                sendHtmlMail($params);
            }
        }
		}
    }

	public function cronGeneral(Request $request )
	{
		/*$params["to"]= "kishan.lashkari@phpdots.com";
		$params["subject"] = "TEST ".date('Y-m-d H:i:s');
		$params["body"] = "<html><body>HI</body></html>";
		sendHtmlMail($params);
		exit;*/		
	    	
		if($request->get('fromcron') == 1){

		$date = date("Y-m-d");
		// $date= '2017-12-05';

        // Get Tasks
      $sql = "
              SELECT  tasks.*,users.firstname,users.lastname,users.email as user_email,projects.title as project_name 
              FROM tasks
              JOIN users ON users.id = tasks.user_id
              JOIN projects ON tasks.project_id = projects.id              
              WHERE date_format(tasks.task_date, '%Y-%m-%d') = '".$date."'
              ORDER BY users.firstname
            ";
      $rows = \DB::select($sql);
      // Get Admin Emails
      $admin_emails = User::getAdminEmails();

    $user_id = 0;
    $client_id = 0;
    $clients = array();
    if(count($rows) > 0 ) {
        foreach ($rows as $task_detail) {
          # code...
          if(!empty($client_id) && !empty($user_id) && ($user_id!=$task_detail->user_id))
          {

          }

          $user_id = $task_detail->user_id;
          $client_id = $task_detail->user_id;
          if(!in_array($task_detail->user_id, $clients)) 
          {
            $clients[] = $task_detail->user_id;
          }
          $report_data[$task_detail->user_id][$task_detail->user_id][]= $task_detail;
        }

        //print_r($report_data);exit;
        
        foreach ($report_data as $user_id => $user_report) 
        {
          $i = 1;

          foreach ($user_report as $client_id => $user_client_reportRow) 
          {

            $user_client_reportRow = json_decode(json_encode($user_client_reportRow),1);

            $empName = "";

            $table = "<p><b>Hello Sir,</b></p>";
            $table .= "<p>Please find daily report below.</p>";

            $table .= '<table width="100%" border="0" cellspacing="0" cellpadding="3" style="font-size:13px; border-top:1px solid #666; border-left:1px solid #666; font-family:Arial, Helvetica, sans-serif;">';
            $table .= "<tr>";
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Sr. No.</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Project</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task/Feature</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Date</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Hour</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Status</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task Link</td>';
            $table .= "</tr>";            $totalHours = 0;

            foreach($user_client_reportRow as $user_client_report )
            {
              $from_email = $user_client_report['user_email'];
              $empName = ucfirst($user_client_report['firstname'])." ".ucfirst($user_client_report['lastname']);
              $status = $user_client_report['status'] == 1 ? "DONE":"In Progress";

              $table .= "<tr>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$i."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['project_name']."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['title']."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.date("m/d/Y",strtotime($user_client_report['task_date']))."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['total_time']."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$status."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['ref_link']."</td>";
              $table .= "</tr>";
              $i++;

              $totalHours += floatval($user_client_report['total_time']);
            }  

            $table .= "</table>";

          $toEmails = $admin_emails;                
          // echo "Send Emails To:<br />";
          // echo "<pre>";
          // print_r($toEmails);
          // echo "<pre>";
          // echo "HTML";

            $table .= "<p>Thanks & Regards,<br />".$empName."</p>";
            $subject = "Daily Report - (Hr-".$totalHours.") - ".date("j M, Y")." - $empName";
            // echo "<p>Subject: $subject</p>";

            // $tmp = ["kishan.lashkari@phpdots.com","jitendra.rathod@phpdots.com"];
            $toEmail = [];
            // $toEmails[0] = "kishan.lashkari@phpdots.com";
			$toEmails[0] = "jitendra.rathod@phpdots.com";
            $params["to"]= $toEmails[0];
            unset($toEmails[0]);
            // $params["ccEmails"]= $toEmails;
            $params["subject"] = $subject;
            $params["from"] = $from_email;
            $params["from_name"] = $empName;  
            $params["body"] = "<html><body>".$table."</body></html>";
            
            $data =array();
            $data['body']= $table;
            // if($from_email != 'mayur.devmurari@phpdots.com')
            sendHtmlMail($params);
            $returnHTML = view('emails.index',$data)->render();
            echo $returnHTML;
            

           // echo $table;          
          }          
        }
      }
		}
      exit;

	}
    public function cron(Request $request){
		
		if($request->get('fromcron') == 1){
			// ok
		}
		else
		{
			abort(404);
		}

        // Get Tasks
  		$sql = '
              SELECT  tasks.*,users.firstname,users.lastname,users.email as user_email,projects.title as project_name,projects.client_id 
              FROM tasks
              JOIN users ON users.id = tasks.user_id
              JOIN projects ON tasks.project_id = projects.id
              JOIN clients ON clients.id = projects.client_id
              WHERE tasks.report_sent = 0 AND clients.send_email = 1
              AND tasks.task_date >= ( CURDATE() - INTERVAL 2 DAY )
              ORDER BY users.firstname, projects.client_id
            ';

      $rows = \DB::select($sql);

      // Get Admin Emails
      $admin_emails = User::getAdminEmails();


      $user_id = 0;
	  $client_id = 0;
      $clients = array();
	  if(count($rows) > 0 ) {
		  
		$updateSQL = "UPDATE tasks SET report_sent = 1, report_sent_date = NOW();";  
		\DB::update($updateSQL);  
		  
        foreach ($rows as $task_detail) {
          # code...
          if(!empty($client_id) && !empty($user_id) && ($user_id!=$task_detail->user_id || $client_id!=$task_detail->client_id))
          {

          }

          $user_id = $task_detail->user_id;
          $client_id = $task_detail->client_id;
          if(!in_array($task_detail->client_id, $clients)) 
          {
            $clients[] = $task_detail->client_id;
          }
          $report_data[$task_detail->user_id][$task_detail->client_id][]= $task_detail;
        }

        // echo "<pre>";print_r($report_data);

        foreach ($report_data as $user_id => $user_report) 
        {                   

          $i = 1;


          

          foreach ($user_report as $client_id => $user_client_reportRow) 
          {

            $user_client_reportRow = json_decode(json_encode($user_client_reportRow),1);

            $empName = "";

            $table = "<p><b>Hi All,</b></p>";
            $table .= "<p>Please find daily report below.</p>";

            $table .= '<table width="100%" border="0" cellspacing="0" cellpadding="3" style="font-size:13px; border-top:1px solid #666; border-left:1px solid #666; font-family:Arial, Helvetica, sans-serif;">';
            $table .= "<tr>";
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Sr. No.</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Project</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task/Feature</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Date</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Hour</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Status</td>';
            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task Link</td>';
            $table .= "</tr>";            $totalHours = 0;

            foreach($user_client_reportRow as $user_client_report )
            {
              $from_email = $user_client_report['user_email'];
              $empName = ucfirst($user_client_report['firstname'])." ".ucfirst($user_client_report['lastname']);
              $status = $user_client_report['status'] == 1 ? "DONE":"In Progress";

              $table .= "<tr>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$i."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['project_name']."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['title']."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.date("m/d/Y",strtotime($user_client_report['task_date']))."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['total_time']."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$status."</td>";
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['ref_link']."</td>";
              $table .= "</tr>";
              $i++;

              $totalHours += floatval($user_client_report['total_time']);
            }  


            $table .= "</table>";

          $clientUsers = \DB::table("client_users")                          
                          ->where("client_id",$client_id)
                          ->where("send_email",1)
                          ->pluck("email")
                          ->toArray();

          $toEmails = array_merge($clientUsers,$admin_emails);                
          echo "Send Emails To:<br />";
          echo "<pre>";
          print_r($toEmails);
          echo "<pre>";
          echo "HTML";




            $table .= "<p>Thanks & Regards,<br />".$empName."</p>";
            $subject = "Daily Report - (Hr-".$totalHours.") - ".date("j M, Y");
            echo "<p>Subject: $subject</p>";
			  
			foreach($toEmails as $k => $v){
				if($v == 'admin@gmail.com')
				{
					unset($toEmails[$k]);
				}
			}  

            // $tmp = ["kishan.lashkari@phpdots.com","jitendra.rathod@phpdots.com"];
			// $toEmail = [];
			// $toEmails[0] = "kishan.lashkari@phpdots.com";
            $params["to"]= $toEmails[0];
            unset($toEmails[0]);
            $params["ccEmails"]= $toEmails;
            $params["subject"] = $subject;
            $params["from"] = $from_email;
			$params["from_name"] = $empName;  
            $params["body"] = "<html><body>".$table."</body></html>";
            
            // if($from_email != 'mayur.devmurari@phpdots.com')
            sendHtmlMail($params);


            echo $table;          
          }          



        }
		  }

      exit;
    }

	public function getReport()
    {
    	
	$tom = new \DateTime();
	$today = $tom->modify('-1 day');

	$user_task = \DB::table('tasks')
                    ->select(\DB::raw('count(user_id) as task_count,user_id'))
					->where('created_at','>=',$today)
                    ->groupBy('user_id')
                    ->get();

       	foreach ($user_task as $row) {
       		echo $row->user_id;
       		$count = $row->task_count;
       		echo $count;
       		echo "<br/>";
       	}
       dd($count);
 
    }
}
