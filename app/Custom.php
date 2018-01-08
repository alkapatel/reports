<?php
namespace App;
use App\Models\HolidayDetail;
use App\Models\LeaveDetail;
use App\Models\User;

/**
 * Custom Class.
 *
 * @subpackage custom class
 * @author     
 */
class Custom 
{
	public static function getUserDob()
    {
        $this_month = date('Y-m',strtotime('first day of this month'));
        $dobList = User::select(\DB::raw('DATE_FORMAT(dob, "%M-%d") as dob'),\DB::raw('DATE_FORMAT(dob, "%m") as month'),'name','image','id')
                        ->where('status','!=',0)
                        ->where('dob','!=',null)
						->whereNull('client_user_id')
						->orderBy('dob')
                        ->get();
        return $dobList;
    }

    public static function createThumnails($uploadPath, $filename) 
    {
        $sizes = env('APP_IMAGE_THUMB_SIZES');
        $sizes = explode(',', $sizes);
        $orgFile = $uploadPath . $filename;

        foreach ($sizes as $size) 
        {
            $temp = explode('X', $size);
            $thumbFile = $uploadPath . "thumb_" . $size . "_" . $filename;

            $w = isset($temp[0]) ? $temp[0] : 100;
            $h = isset($temp[1]) ? $temp[1] : 100;

            \Image::make($orgFile)
                    ->resize($w, $h)                    
                    ->save($thumbFile);
        }
    }
    public static function getLeaveRequest()
    {
        $leave_request = \App\Models\LeaveRequest::where('status',0)->get();
        $pending = count($leave_request);
        return $pending;
    }
    public static function getLeaveUser()
    {
        $data = array();
        $today = date('Y-m-d');
        $accept_leave = \App\Models\LeaveDetail::select(TBL_LEAVE_DETAIL.".*",TBL_USERS.".name as username",TBL_USERS.".image as image",TBL_USERS.".id as user_id")
                ->join(TBL_LEAVE_REQUEST,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_LEAVE_REQUEST.".user_id")
                ->where(TBL_LEAVE_REQUEST.'.status',1)
                ->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$today."%") 
                ->get();

        //dd($accept_leave);
        //$data['total_leave'] = count($accept_leave);
        //$data['accept_leave'] = $accept_leave;
                
        return $accept_leave;
    }
	public static function workingDays($this_month,$this_month_days,$sundays)
    { 
        $this_month_holiday = HolidayDetail::join(TBL_HOLIDAYS,TBL_HOLIDAYS.".id","=",TBL_HOLIDAYS_DETAILS.".holiday_id")
            ->where(TBL_HOLIDAYS.'.status',1)
            ->where(TBL_HOLIDAYS_DETAILS.'.date','LIKE',"%".$this_month."%")
            ->where(\DB::raw("DATE_FORMAT(".TBL_HOLIDAYS_DETAILS.".date,'%a')"), '!=', 'Sun')
            ->get();
            
       $this_month_holiday = count($this_month_holiday);
       $working_days = $this_month_days - $sundays - $this_month_holiday;
       return $working_days;
    }
    public static function userleavetaken($user_id,$this_month)
    {
        //$this_month = date('Y-m',strtotime('first day of this month'));
       
        $auth_user_month_leave = LeaveDetail::select(TBL_LEAVE_DETAIL.".*")
                ->join(TBL_LEAVE_REQUEST,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                ->where(TBL_LEAVE_REQUEST.'.status',1)
                ->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$this_month."%")
                ->where(\DB::raw("DATE_FORMAT(".TBL_LEAVE_DETAIL.".date,'%a')"), '!=', 'Sun') 
                ->where(TBL_LEAVE_REQUEST.'.user_id',$user_id)
                ->get();

                $days = 0;
                foreach ($auth_user_month_leave as $q) {
                    if($q->is_half == 1)                        
                        $day =0.5;
                    else
                        $day =1;
                $days +=$day;
                }
            return $days;
    }
    public static function usertotalleave($auth_user)
    {
        $auth_user_leave = LeaveDetail::select(TBL_LEAVE_DETAIL.".*")
                ->join(TBL_LEAVE_REQUEST,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                ->where(TBL_LEAVE_REQUEST.'.status',1)
                ->where(TBL_LEAVE_REQUEST.'.user_id',$auth_user)
                ->where(\DB::raw("DATE_FORMAT(".TBL_LEAVE_DETAIL.".date,'%a')"), '!=', 'Sun')
                ->get();

                $days = 0;
                foreach ($auth_user_leave as $q) {
                    if($q->is_half == 1)                        
                        $day =0.5;
                    else
                        $day =1;
                $days +=$day;
                }
        return $days;
    }
    public static function usermothleave($auth_user)
    {
        $this_month = date('Y-m',strtotime('first day of this month'));
        $auth_user_month_leave = LeaveDetail::select(TBL_LEAVE_DETAIL.".*")
                ->join(TBL_LEAVE_REQUEST,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                ->where(TBL_LEAVE_REQUEST.'.status',1)
                ->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$this_month."%")
                ->where(\DB::raw("DATE_FORMAT(".TBL_LEAVE_DETAIL.".date,'%a')"), '!=', 'Sun') 
                ->where(TBL_LEAVE_REQUEST.'.user_id',$auth_user)
                ->get();
                
                $days = 0;
                foreach ($auth_user_month_leave as $q) {
                    if($q->is_half == 1)                        
                        $day =0.5;
                    else
                        $day =1;
                $days +=$day;
                }
        return $days;
    }

}

if (!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

}

    
?>