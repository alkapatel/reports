<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;

class comman extends Model
{
    public function sentemail($emailArray)
    {
        /*$message = "<b>From Date. ".$leaveDetail->from_date."</b></br/>";
        $message .= "<b>To Date. ".$leaveDetail->to_date."</b></br/><br/>";
        $message .= "<span> Dear Sir, </span><br/><br/>";
        $message .= "<p>".$leaveDetail->description."</p><br/>";
        $message .= "<span>Thanking You,</span><br/><br/>";
        $message .= "<span>Yours Sincerely,</span><br/>";
        $message .= "<span>".$leaveDetail->username."</span><br/><br/><br/>";
        $message .= "<p>Click the link below to view leave details.</p>";
        $message .= "<a href='".url('/leave-request')."</a>";
        */              
        //$from_address = "alkathumar91@gmail.com";        
        //$from_address_name = "Reports PHPdots";
        //$toEmail = "alkathumar91@gmail.com";
        //$ccEmail = "rathodakshay228@gmail.com";
        //$bccEmail = "";
        //$subject ="Leave Request";

        //$array['from_address'] = $from_address;
        //$array['to_emails'] = $toEmail;
        //$array['subject'] = $subject;
        //$array['body'] = $body;

       
       \Mail::send('test', $array, function($message) use ($array)
       {
           $message->from($array['from_address'], "Reports PHPdots");            
           $message->sender($array['from_address'], "Reports PHPdots");
           $message->to($array['to_emails'], '')->subject($array['subject']);

       });
        
        $dataToInsert = [
            'to_email' => $array['toEmail'],
            'cc_emails' => $array['ccEmail'],
            'bcc_emails' => $array['bccEmail'],
            'from_email' => $from_address,
            'email_subject' => $array['subject'],
            'email_body' => $array['body'],
            'mail_response' => $result,
            'status' => 1,
            'ip_address' => '.ip',
            'is_mandrill' => 1,
            'created_at' => \DB::raw('NOW()'),
            'updated_at' => \DB::raw('NOW()')
        ];

        \DB::table(TBL_EMAIL_SENT_LOG)->insert($dataToInsert);
    }
}
