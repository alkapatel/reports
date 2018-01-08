<?php

/**
 * Website General Functions
 *
 */
function getFilename($fullpath, $uploaded_filename) {
    $count = 1;
    $new_filename = $uploaded_filename;
    $firstinfo = pathinfo($fullpath);

    while (file_exists($fullpath)) {
        $info = pathinfo($fullpath);
        $count++;
        $new_filename = $firstinfo['filename'] . '(' . $count . ')' . '.' . $info['extension'];
        $fullpath = $info['dirname'] . '/' . $new_filename;
    }

    return $new_filename;
}

function humanTiming($time) {

    $time = time() - $time; // to get the time since that moment
    $time = ($time < 1) ? 1 : $time;

    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit)
            continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }
}

function removeDir($dir) {
    foreach (glob($dir . "/*.*") as $filename) {
        if (is_file($filename)) {
            unlink($filename);
        }
    }

    if (is_dir($dir . "feature")) {

        foreach (glob($dir . "feature/*.*") as $filename) {
            if (is_file($filename)) {
                unlink($filename);
            }
        }

        rmdir($dir . "feature");
    }

    rmdir($dir);
}

function sendHtmlMail($params) {
    $files = isset($params['files']) ? $params['files'] : array();

    if(isset($params['from']))
        $from = $params['from'];
    else
        $from = "reports.phpdots@gmail.com";

    $params['from'] = $from;
    
    $toEmails[] = $params['to'];   

    if(isset($params['ccEmails']))
    {
        foreach($params['ccEmails'] as $em)
        {
            $toEmails[] =  $em;
        }
    }
    
    $params['to_emails'] = $toEmails;
    

    \Mail::send('emails.index', $params, function($message) use ($params, $files) {
		
		$fromName = "PHPDots";
		if(isset($params['from_name']))
		{
			$fromName = $params['from_name'];
		}	

        if(isset($params['from']))
        {
            $message->from($params['from'], $fromName);
            $message->sender($params['from'], $fromName);
        }

        $message->to($params['to_emails'], '')->subject($params['subject']);

        if (count($files) > 0) 
        {
            foreach ($files as $file) {
                $message->attach($file['path']);
            }
        }
    });

    $dataToInsert = [
            'to_email' => $params['to'],
            'cc_emails' => '',
            'bcc_emails' => '',
            'from_email' => $from,
            'email_subject' => $params['subject'],
            'email_body' => $params['body'],
            'mail_response' => '',
            'status' => 1,
            'ip_address' => '.ip',
            'is_mandrill' => 1,
            'created_at' => \DB::raw('NOW()'),
            'updated_at' => \DB::raw('NOW()')
        ];

        \DB::table(TBL_EMAIL_SENT_LOG)->insert($dataToInsert);
}

// to generate random string
function getRandomNum($len = 6) {
    //$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    $chars = "0123456789";
    $r_str = "";
    for ($i = 0; $i < $len; $i++)
        $r_str .= substr($chars, rand(0, strlen($chars)), 1);

    if (strlen($r_str) != $len) {
        $r_str .= getRandomNum($len - strlen($r_str));
    }

    return $r_str;
}

// to generate random string number
function getRandomStringNumber($len = 30) {
    // $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $chars = "0123456789";
    $r_str = "";
    for ($i = 0; $i < $len; $i++)
        $r_str .= substr($chars, rand(0, strlen($chars)), 1);

    if (strlen($r_str) != $len) {
        $r_str .= getRandomStringNumber($len - strlen($r_str));
    }

    return $r_str;
}

// for table heading sorting link
function getSortingLink($link, $heading, $field, $curSortBy = '', $curSortOrder = 'asc', $search_field = '', $search_val = '', $extra_params = '') {

    $qs = '?';
    if (strpos($link, '?') != false) {
        $qs = '&';
    }



    if ($field != $curSortBy) {
        $link .= $qs . 'sortBy=' . $field . '&sortOrd=asc';
    } elseif ($field == $curSortBy) {
        if ($curSortOrder == "asc") {
            $link .= $qs . 'sortBy=' . $field . '&sortOrd=desc';
        } elseif ($curSortOrder == "desc") {
            $link .= $qs . 'sortBy=' . $field . '&sortOrd=asc';
        } else {
            $link .= $qs . 'sortBy=' . $field . '&sortOrd=asc';
        }
    }

    if ($search_field != "" && $search_val != "") {
        $link .= '&search_field=' . $search_field . "&search_text=" . $search_val;
    }

    if ($extra_params != "") {
        $link .= "&" . $extra_params;
    }


    return '<a href="' . $link . '">' . $heading . '</a>';
}

function dateFormat($date, $format = '', $withTime = false) {


    if ($date == "0000-00-00 00:00:00" || $date == "0000-00-00" || $date == "0000-00-00 00:00:00000000" || $date == "" || is_null($date)) {
        return '-';
    }

    $temp = '';
    if ($withTime) {
        $temp = ' H:i a';
    }

    if ($format == '') {
        return date(env('APP_DATE_FORMAT', 'Y-m-d') . $temp, strtotime($date));
    } else {
        return date($format, strtotime($date));
    }
}

function downloadFile($filename, $filepath) {
    $fsize = filesize($filepath);
    header('Pragma: public');
    header('Cache-Control: public, no-cache');
    header('Content-Type: ' . mime_content_type($filepath));
    header('Content-Length: ' . $fsize);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    readfile($filepath);
    exit;
}

function isDigits($quantity) {
    return preg_match("/[^0-9]/", $quantity);
}

function displayPrice($price, $with_symbol = 1) {
    if ($with_symbol == 1)
        return "$" . number_format($price, 2);
    else
        return number_format($price, 2);
}

function makeDir($path) {
    if (!is_dir($path)) {
        mkdir($path);
        chmod($path, 0777);
    }
}

function get_month_name($month) {
    $months = array(
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    );

    return $months[$month];
}

function NVPToArray($NVPString) {
    $proArray = array();

    while (strlen($NVPString)) {
        // name
        $keypos = strpos($NVPString, '=');
        $keyval = substr($NVPString, 0, $keypos);
        // value
        $valuepos = strpos($NVPString, '&') ? strpos($NVPString, '&') : strlen($NVPString);
        $valval = substr($NVPString, $keypos + 1, $valuepos - $keypos - 1);
        // decoding the respose
        $proArray[$keyval] = urldecode($valval);
        $NVPString = substr($NVPString, $valuepos + 1, strlen($NVPString));
    }

    return $proArray;
}

/**
 * Website General Model Functions
 *
 */
function getRecordsFromSQL($sql, $returnType = "array") {
    $result = \DB::select($sql);

    if ($returnType == "array") {
        $result = json_decode(json_encode($result), true);
    } else {
        return $result;
    }
}

function getRecords($table, $whereArr, $returnType = "array") {
    $result = \DB::table($table)->from($table);

    if (is_array($whereArr) && count($whereArr) > 0) {
        foreach ($whereArr as $field => $value) {
            $result->where($field, $value);
        }
    }

    $result = $result->get();


    if ($returnType == "array") {
        $result = json_decode(json_encode($result), true);
    } else {
        return $result;
    }
}

function GetUserIp() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    /* If Local IP */
    if ($ipaddress == "UNKNOWN" || $ipaddress == "127.0.0.1")
        $ipaddress = '72.229.28.185'; //NY

        /* if($ipaddress == '203.88.138.46') { //GJ
          $ipaddress = '128.101.101.101'; //MN
          $ipaddress = '24.128.151.64'; //Adrian
          $ipaddress = '66.249.69.245'; //CA
          $ipaddress = '72.229.28.185'; //NY
          $ipaddress = '127.0.0.1'; //UNKNOWN
          $ipaddress = '2603:300a:f05:a000:2970:d9ff:9da:ccd6'; //Patrick mobile IPv6
          } */

    return $ipaddress;
}

function getAdminUserTypes()
{
    $array = array();
    
    $rows = \DB::table("admin_user_types")->get();

    foreach($rows as $row)
    {
        $array[$row->id] = $row->title;    
    }    

    return $array;
}

function superAdmin($current_login_user_id){

    if($current_login_user_id == SUPER_ADMIN_ID || $current_login_user_id == 11)
        return 1;
    else
        return 0;
}
function sendSms($mobileNumber,$message,$member_id,$auth_id){

    //Send Message
    $username ='Jrathod';
    $password ='966143';
    $senderId = "DGENIT";
	
	;
    
    //API URL
    $url = 'http://45.114.143.11/api.php?username='.$username.'&password='.$password.'&sender='.$senderId.'&sendto='.$mobileNumber.'&message='.urlencode($message);

    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec ($curl);
    curl_close ($curl);

    $dataToInsert = [
            'login_user_id' => $auth_id,
            'member_id' => $member_id,
            'mobile' => $mobileNumber,
            'sms_body' => $message,
            'sms_response' => $result,
            'created_at' => \DB::raw('NOW()'),
            'updated_at' => \DB::raw('NOW()')
        ];

        return \DB::table(TBL_SMS_SENT_LOG)->insertGetId($dataToInsert);
     
}
