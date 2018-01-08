<?php
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\Admin;
use App\Models\LeaveRequest;
use App\Models\LeaveDetail;
use App\Models\Task;
use App\Models\HolidayDetail;
use App\Custom;
use App\Models\User;
use App\Models\TaskReport;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->adminAction = new \App\Models\AdminAction;
    }

    public function index(Request $request)
    {                          
        $data = array();
        $this_month = date('Y-m',strtotime('first day of this month')); 
        $today =  date("Y-m-d",strtotime("today"));
        $yesterday =  date("Y-m-d",strtotime("yesterday"));
        
        $auth_user = Auth::guard("admins")->user()->id;

        $userOnLeaves = LeaveDetail::getCurrentMonthLeaves();
        
		//Holiday List
        $user_holidays = HolidayDetail::select(TBL_HOLIDAYS_DETAILS.".*",TBL_HOLIDAYS_DETAILS.".date as holy_date",TBL_HOLIDAYS.'.holiday_title as holiday_title')
                ->join(TBL_HOLIDAYS,TBL_HOLIDAYS.".id","=",TBL_HOLIDAYS_DETAILS.".holiday_id")
                ->where(TBL_HOLIDAYS.'.status',1)
                ->get()
				->toArray();
		 
		$calendar_leave = array();

            $i =0;
            foreach ($user_holidays as $key => $value) {
                
                $calendar_leave [$i]['name'] = $value['holiday_title']; 
                $calendar_leave [$i]['date'] = $value['holy_date']; 
				$calendar_leave [$i]['is_hoilday'] = 1;
                $calendar_leave [$i]['is_half'] = ''; 
                $calendar_leave [$i]['status'] = ''; 
                $i++;
            }		

    //Working Days
        $this_month_days = date("t");
        $first = date('Y-m-d',strtotime('first day of this month')); 
        $last = date('Y-m-d',strtotime('last day of this month'));

        $begin = new \DateTime($first);
        $end = new \DateTime($last);
        $end = $end->modify('+1 day');

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $sundays = 0;
        foreach ($period as $d) {
            $dt = $d->format('D');
            if ($dt == 'Sun') {
                $sundays += 1;
            }
        }

        $data['working_days'] = Custom::workingDays($this_month,$this_month_days,$sundays);
		
        $auth_id = \Auth::guard('admins')->user()->user_type_id;
        if($auth_id == ADMIN_USER_TYPE)
        {
        //User Leave
            $pending_leave = LeaveRequest::select(TBL_LEAVE_REQUEST.".*",TBL_USERS.".name as username",TBL_USERS.".image as image")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_LEAVE_REQUEST.".user_id")
                ->where(TBL_LEAVE_REQUEST.'.status',0)->get();

            $user_leave = LeaveDetail::select(TBL_LEAVE_DETAIL.".*",TBL_USERS.".name as username",TBL_LEAVE_REQUEST.'.from_date as from_date',TBL_LEAVE_REQUEST.'.to_date as to_date',TBL_USERS.".image as image" )
                    ->join(TBL_LEAVE_REQUEST,TBL_LEAVE_REQUEST.".id","=",TBL_LEAVE_DETAIL.".leave_id")
                    ->join(TBL_USERS,TBL_USERS.".id","=",TBL_LEAVE_REQUEST.".user_id")
                    ->where(TBL_LEAVE_REQUEST.'.status',1)
                    ->where(TBL_LEAVE_DETAIL.'.date','LIKE',"%".$this_month."%") 
                    ->groupBy(TBL_LEAVE_DETAIL.'.leave_id')
                    ->get();

            $data['pending_leave'] = $pending_leave;
            $data['user_leave'] = $user_leave;
        //Admin yesterday Task Details
            $halfLeaveUsers = Task::halfLeaveUsers($yesterday);
            $fullLeaveUsers = Task::fullLeaveUsers($yesterday);
            
        //yesterday on holiday
            $yesterday_holiday = Task::yesterdayHoliday($yesterday);
            $data['yesterday_holiday']= $yesterday_holiday;
        
        //Admin Not Added
            $daily_tasks = Task::NotAdded($fullLeaveUsers,$yesterday);
            $data['daily_tasks']= $daily_tasks;

        //Admin Below 8 hours Details
            $below_eight_hour = Task::BelowEightHrs($halfLeaveUsers,$yesterday);

        // Admin Below 4 hours Details
            $below_four_hour = Task::BelowFourHrs($halfLeaveUsers,$yesterday);

            $final_belows = Task::final_belows($below_eight_hour,$below_four_hour);
            $data['daily_tasks_hours'] = $final_belows;
        
            $data['yesterday_leave'] = LeaveRequest::yesterdayOnLeave();
       
        //Admin Calendar
            $calendar_holy = array();
            $i =0;
            foreach ($userOnLeaves as $key => $value) {
                
                $calendar_holy[$i]['name'] = $value['name']; 
                $calendar_holy[$i]['date'] = $value['date'];
                $calendar_holy[$i]['is_half'] = $value['is_half']; 
                $calendar_holy[$i]['status'] = $value['status']; 
                $i++;
            }
         
            $calendar= array_merge($calendar_leave,$calendar_holy);
            $data['userOnLeaves'] = $calendar;

        $viewName = "dashboard";
        }
        if($auth_id == NORMAL_USER)
        {
        //User Leave
            $data['auth_user_leave'] = Custom::usertotalleave($auth_user);        
            $data['auth_user_month_leave'] = Custom::usermothleave($auth_user);               
        //User Task
            $auth_user_hour = \DB::table(TBL_TASK)
                ->select([
                    \DB::raw("sum(".TBL_TASK.".total_time) as total")
                ])
                ->where(TBL_TASK.'.user_id',$auth_user)
                ->get();
            $data['auth_user_hours'] = $auth_user_hour;
            
            $auth_user_month_hour = \DB::table(TBL_TASK)
                    ->select([
                        \DB::raw("sum(".TBL_TASK.".total_time) as total")
                    ])
                    ->where(TBL_TASK.'.task_date','LIKE',"%".$this_month."%")
                    ->where(TBL_TASK.'.user_id',$auth_user)
                    ->get();
            $data['auth_user_month_hour'] = $auth_user_month_hour;
            $today =  date("Y-m-d",strtotime("today"));
            $auth_user_today_hour = \DB::table(TBL_TASK)
                    ->select([
                        \DB::raw("sum(".TBL_TASK.".total_time) as total")
                    ])
                    ->where(TBL_TASK.'.task_date','LIKE',"%".$today."%")
                    ->where(TBL_TASK.'.user_id',$auth_user)
                    ->get();
            $data['auth_user_today_hour'] = $auth_user_today_hour;
        
        //User Calendar
            $authOnLeaves = LeaveDetail::getAuthUserLeaves();
            $calendar_user_leave = array();
                $i =0;
                foreach ($authOnLeaves as $key => $value) {
                    
                    $calendar_user_leave[$i]['name'] = $value['name']; 
                    $calendar_user_leave[$i]['date'] = $value['date'];
                    $calendar_user_leave[$i]['is_half'] = $value['is_half']; 
                    $calendar_user_leave[$i]['status'] = $value['status']; 
                    $i++;
                }
            
            $user_calendar= array_merge($calendar_user_leave,$calendar_leave);
            $data['userOnHoliday'] = $user_calendar;
            

        $viewName = "userDashboard";
        }
        if($auth_id == CLIENT_USER)
        {
            //client calendar
             
            //Leave list
            $clientEmpOnLeave = LeaveDetail::getClientEmpLeaves();

            $calendar_clientEmp_leave = array();
                $i =0;
                foreach ($clientEmpOnLeave as $key => $value) {
                    
                    $calendar_clientEmp_leave[$i]['name'] = $value['name']; 
                    $calendar_clientEmp_leave[$i]['date'] = $value['date'];
                    $calendar_clientEmp_leave[$i]['is_half'] = $value['is_half']; 
                    $calendar_clientEmp_leave[$i]['status'] = $value['status']; 
                    $i++;
                }
            $client_calendar = array_merge($calendar_clientEmp_leave,$calendar_leave);
            $data['clientEmpOnLeave'] = $client_calendar;

        $viewName = "clientDashboard";
        }

        return view('admin.'.$viewName,$data);
    }

    public function getWorkingDays(Request $request)
    {
        $workingDays = 0;
        $start_date = $request->get('start_date');
        $end_date = date('Y-m-t',strtotime($start_date));
        
        $this_month = date('Y-m',strtotime($start_date)); 
        $this_month_days = date("t",strtotime($start_date));

        $first = date('Y-m-d',strtotime($start_date));
        $last = date('Y-m-d',strtotime($end_date));

        $begin = new \DateTime($first);
        $end = new \DateTime($last);
        $end = $end->modify('+1 day');

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $sundays = 0;
        foreach ($period as $d) {
            $dt = $d->format('D');
            if ($dt == 'Sun') {
                $sundays += 1;
            }
        }
        $workingDays =  Custom::workingDays($this_month,$this_month_days,$sundays);
        return $workingDays;
    }

    public function changePassword()
    {        
        $data = array();
        return view('admin.changepwd',$data);        
    }    
    
    // post change password
    public function postChangePassword(Request $request)
    {        
        $status = 1;
        $msg = "Your password has been changed successfully.";
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:4',
            'new_password' => 'required|min:4|confirmed',
            'new_password_confirmation' => 'required',
        ]);        
        
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";
            
            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }            
        }
        else
        {
            $user = Auth::guard("admins")->user();
            
            $old_password = $request->get('password');
            
            if(Hash::check($old_password, $user->password))
            {
                $user->password = bcrypt($request->get('new_password'));
                $user->save();
                
                // save log
                $params=array();

                $params['adminuserid']	= $user->id;
                $params['actionid']	= $this->adminAction->UPDATE_CHANGE_PASSWORD;
                $params['actionvalue']	= $user->id;
                $params['remark']	= 'Change Password';

                \App\Models\AdminLog::writeadminlog($params);
                unset($params);                                
            }
            else
            {
                $status = 0;
                $msg = 'old password is incorrect.';
            }
        }       
        
        
        return ['status' => $status, 'msg' => $msg];
    }    
    
    // edit profile
    public function editProfile()
    {        
        $data = array();
        $data['formObj'] = \Auth::guard("admins")->user();

        
        return view('admin.profile',$data);        
    }    
    
    // update profile
    public function updateProfile(Request $request)
    {        
        $status = 1;
        $msg = "Your profile has been updated successfully.";
        
        $validator = Validator::make($request->all(), [
            //'email' => 'required|email|unique:'.TBL_USERS.',email,'.\Auth::guard("admins")->user()->id,
            'firstname' => 'required|min:2|max:255',
            'lastname' => 'required|min:2|max:255',
            'address' => 'required|min:2',
            'phone' => 'required|numeric',
            'image' => 'image|max:6000'
        ]);        
        
        
        if($validator->fails())
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";
            
            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }                        
        }    
        else
        {
            $user = \Auth::guard("admins")->user();            
            $user_id = \Auth::guard("admins")->user()->id;            
            $image = $request->file("image");
            //$email = $request->get("email");
            $firstname = $request->get("firstname");
            $lastname = $request->get("lastname");
            $image = $request->file("image");
            $name = $firstname." ".$lastname;
            
            if($request->hasFile('image'))
            {
                if(!empty($image)){
                    //$destinationPath = public_path().'/images/users/'; 
                    $destinationPath = public_path().'/uploads/users/'.$user_id.'/'; 
         
            
                    $image_name =$image->getClientOriginalName();              
                    $extension =$image->getClientOriginalExtension();
                    $image_name=md5($image_name);
                    $profile_image= $image_name.'.'.$extension;
                    $file =$image->move($destinationPath,$profile_image);
                    
                    $url = public_path().'/uploads/users/'.$user_id.'/'.$user->image;
                    if($url){
                        //unlink($url);
                    }

                $user->image  = $profile_image;            
                }
            }

            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->phone = $request->get("phone");
            $user->address = $request->get("address");
            //$user->email = $email;
            $user->name = $name;
            $user->status = $status;
            $user->save();
            
            //$user->update($input);             
            
            // save log
            $params=array();

            $params['adminuserid']	= $user->id;
            $params['actionid']	= $this->adminAction->UPDATE_PROFILE;
            $params['actionvalue']	= $user->id;
            $params['remark']	= 'Update Profile';

            \App\Models\AdminLog::writeadminlog($params);
            unset($params);                
            
        }
        
        
        return ['status' => $status, 'msg' => $msg];
    }
    public function rights(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ASSIGN_RIGHTS);
        
        if($checkrights) 
        {
            return $checkrights;
        }        
        
        $type_id = $request->get("type_id");


        if($request->isXmlHttpRequest() && $request->get("action") == "update")
        {
            $status = 1;
            $msg = "Rights has been updated successfully.";

            if(intval($type_id) > 0)
            {
                $ids = $request->get("ids");
                
                // Delete old Roles
                \DB::table(TBL_ADMIN_USER_RIGHT)->where("user_type_id", $type_id)->delete();

                if(is_array($ids) && count($ids) > 0)
                {
                    foreach($ids as $page_id)
                    {
                        $dataToInsert = [
                            'user_type_id' => $type_id,
                            'page_id' => $page_id
                        ];
                        
                        \DB::table(TBL_ADMIN_USER_RIGHT)->insert($dataToInsert);

                        unset($dataToInsert);
                    }    
                }                
                
                //store logs detail
                $params=array();                                            
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->UPDATE_RIGHTS;
                $params['actionvalue']  = $type_id;
                $params['remark']       = "Update Rights ::".$type_id;                                        
                \App\Models\AdminLog::writeadminlog($params);                
            }
            else
            {
                $status = 0;
                $msg = "Please select user type.";
            }

            return ['status' => $status, 'msg' => $msg];
        }


        $data = array();
        $data['roles'] = \App\Models\UserType::get();
        $data['ids_selected'] = array();

        if(intval($type_id) > 0)
        {
            $temp = \App\Models\AdminUserRight::where("user_type_id",$type_id)->get();
            foreach($temp as $r)
            {
                $data['ids_selected'][] = $r->page_id;
            }    
        }

        $ADMIN_GROUPS = TBL_ADMIN_GROUP;
        $ADMIN_GROUP_PAGES = TBL_ADMIN_GROUP_PAGE;

        $query= " SELECT ".
                  $ADMIN_GROUPS.".id AS trngroupid, ".
                  $ADMIN_GROUPS.".title AS trngrouptitle, ".
                  $ADMIN_GROUP_PAGES.".id AS trnid, ".
                  $ADMIN_GROUP_PAGES.".name AS trnname, ".
                  $ADMIN_GROUP_PAGES.".url AS pageurl, ".
                  $ADMIN_GROUP_PAGES.".menu_title AS trntitle, ".
                  $ADMIN_GROUP_PAGES.".show_in_menu AS show_in_menu, ".                  
                  $ADMIN_GROUP_PAGES.".is_sub_menu AS insubmenu ".
            " FROM ".
                  $ADMIN_GROUPS.", ".
                  $ADMIN_GROUP_PAGES." ".                  
            " WHERE ".
                  $ADMIN_GROUPS.".id = ".$ADMIN_GROUP_PAGES.".admin_group_id".
            " ORDER BY ".
                  $ADMIN_GROUPS.".order_index, ".
                  $ADMIN_GROUPS.".title, ".
                  $ADMIN_GROUP_PAGES.".menu_order, ".
                  $ADMIN_GROUP_PAGES.".name";


        $rows = \DB::select($query);
        $rows = json_decode(json_encode($rows), true);
        $data['rows'] = $rows;

        return view('admin.rights',$data);
    }
	public function bank_details()
    {
        return view('admin.bankDetails');
    }
}
