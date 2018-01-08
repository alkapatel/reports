<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
class mailController extends Controller
{
    public function sentemail()
    {
              
        $from_address = "alka.thumar@phpdots.com";        
        $from_address_name = "Reports PHPdots";
        $toEmail = "alka.thumar@phpdots.com";
        $subject ="Leave Request";      
        $body ="Leave Request";

        $array['from_address'] = $from_address;
        $array['to_emails'] = $toEmail;
        $array['subject'] = $subject;
        $array['body'] = $body;

        \Mail::send('test', $array, function($message) use ($array)
       {
           $message->from($array['from_address'], "Reports PHPdots");            
           $message->sender($array['from_address'], "Reports PHPdots");
           $message->to($array['to_emails'], '')->subject($array['subject']);

       });
        
    }
}
