<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
class SendMailController extends Controller
{
    public function sentemail($leaveDetail)
    {
    	// dd($leaveDetail);

        $message = "<b>From Date. ".$leaveDetail->from_date."</b></br/>";
        $message = "<b>To Date. ".$leaveDetail->to_date."</b></br/><br/>";
        $message .= "<span> Dear Sir, </span><br/><br/>";
        $message .= "<p>".$leaveDetail->description."</p><br/>";
        $message .= "<span>Thanking You,</span><br/><br/>";
        $message .= "<span>Yours Sincerely,</span><br/>";
        $message .= "<span>".$leaveDetail->user_id."</span><br/><br/><br/>";
        $message .= "<p>Click the link below to view leave details.</p>";
        $message .= "<a href='".url('/leave-request')."</a>";
                      
        $from_address = "alka.thumar@phpdots.com";        
        $from_address_name = "Reports PHPdots";
        $toEmail = "alka.thumar@phpdots.com";
        $toEmail = "rathodakshay228@gmail.com";
        $subject ="Leave Request";

        $array['from_address'] = $from_address;
        $array['to_emails'] = $toEmail;
        $array['subject'] = $subject;
        $array['body'] = $body;

       /*
       \Mail::send('test', $array, function($message) use ($array)
       {
           $message->from($array['from_address'], "Reports PHPdots");            
           $message->sender($array['from_address'], "Reports PHPdots");
           $message->to($array['to_emails'], '')->subject($array['subject']);

       });
       */ 
        
    }
}
