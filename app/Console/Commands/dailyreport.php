<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class dailyreport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:dailyreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation Process For sending daily work report to client.';

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
        // Get Tasks
      $sql = '
              SELECT  tasks.*,users.firstname,users.lastname,users.email as user_email,projects.title as project_name,projects.client_id 
              FROM tasks
              JOIN users ON users.id = tasks.user_id
              JOIN projects ON tasks.project_id = projects.id
              JOIN clients ON clients.id = projects.client_id
              WHERE tasks.report_sent = 0 AND clients.send_email = 1
              AND tasks.created_at >= ( CURDATE() - INTERVAL 2 DAY )
              ORDER BY users.firstname, projects.client_id
            ';

      $rows = \DB::select($sql);

      // Get Admin Emails
      $admin_emails = User::getAdminEmails();


      $user_id = 0;
    $client_id = 0;
      $clients = array();
    if(count($rows) > 0 ) {
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
              $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.date("m/d/Y",strtotime($user_client_report['created_at']))."</td>";
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
            $subject = $empName.": Daily Report - (Hr-".$totalHours.") - ".date("j M, Y");
            echo "<p>Subject: $subject</p>";

            // $tmp = ["kishan.lashkari@phpdots.com","jitendra.rathod@phpdots.com"];
      $toEmail = [];
      $toEmails[0] = "kishan.lashkari@phpdots.com";
            $params["to"]= $toEmails[0];
            unset($toEmails[0]);
            // $params["ccEmails"]= $toEmails;
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
    
}
