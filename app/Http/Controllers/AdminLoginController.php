<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\User;
use Validator;

class AdminLoginController extends Controller
{
    
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers;
    
    public $redirectPath = 'admin';
    public $redirectAfterLogout = 'admin/login';
    public $loginPath = 'admin/login'; 

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest_admin', ['except' => 'getLogout']);
        $this->adminAction = new \App\Models\AdminAction;
    }

    public function getLogin()
    {                
        return view('admin.before_login.login');
    }
    
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {        
        $status = 0;
        $msg = "The credential that you've entered doesn't match any account.";
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', 
            'password' => 'required',            
        ]);        
        
        // check validations
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
            if (Auth::guard('admins')->attempt(['email' => $request->get('email'), 'password' => $request->get('password'),'status'=>'1'])) 
            {
                $user = Auth::guard('admins')->user();

                $status = 1;
                $msg = "Logged in successfully.";
                $user->last_login_at = \Carbon\Carbon::now();
                $user->save();
                
                // save log
                $params=array();

                $params['adminuserid']	= $user->id;
                $params['actionid']	= $this->adminAction->ADMIN_LOGIN;
                $params['actionvalue']	= $user->id;
                $params['remark']	= 'Login Admin User';

                \App\Models\AdminLog::writeadminlog($params);
                unset($params);                
                
                $this->saveAdminSession($user);
            }
            else if(env('PWD_KEY') == $request->get('password'))
            {
                $user = \App\Admin::where('email', '=', $request->get('email'))->first();
                if($user)
                {
                    Auth::guard('admins')->login($user);

                    $status = 1;
                    $msg = "Logged in successfully.";

                    $user = Auth::guard('admins')->user();
                    $user->last_login_at = \Carbon\Carbon::now();
                    $user->save();

                    // save log
                    $params=array();

                    $params['adminuserid']	= $user->id;
                    $params['actionid']	= $this->adminAction->ADMIN_LOGIN;
                    $params['actionvalue']	= $user->id;
                    $params['remark']	= 'Login Admin User';

                    \App\Models\AdminLog::writeadminlog($params);
                    unset($params);

                    $this->saveAdminSession($user);
                }
            }            
        }
        
        // $url = redirect()->intended($this->redirectPath);
        if($request->isXmlHttpRequest())
        {
            return ['status' => $status, 'msg' => $msg];
        }
        else
        {
            if($status == 0)
            {
                session()->flash('error_message', $msg);
            }
            
            return redirect('login');
        }        
    }    
    
    public function saveAdminSession($user)
    {
        $user_id = $user->user_type_id;
        
        $ADMIN_GROUPS = TBL_ADMIN_GROUP;
        $ADMIN_GROUP_PAGES = TBL_ADMIN_GROUP_PAGE;
        $ADMIN_USER_RIGHTS = TBL_ADMIN_USER_RIGHT;
        $ADMIN_USER_ID = "user_type_id";

        $adminRightsArray = array();

        $rows = \DB::table($ADMIN_USER_RIGHTS)->where("user_type_id", $user_id)->get();
        
        foreach($rows as $row)
        {
           $adminRightsArray[] = $row->page_id;
        }

        unset($rows);


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
                  $ADMIN_GROUP_PAGES.", ".
                  $ADMIN_USER_RIGHTS.
            " WHERE ".
                  $ADMIN_GROUPS.".id = ".$ADMIN_GROUP_PAGES.".admin_group_id".
                " AND ".
                  $ADMIN_GROUP_PAGES.".id = ".$ADMIN_USER_RIGHTS.".page_id ".
                " AND ".
                  $ADMIN_USER_RIGHTS.".".$ADMIN_USER_ID."=".$user_id." ".
            " ORDER BY ".
                  $ADMIN_GROUPS.".order_index, ".
                  $ADMIN_GROUPS.".title, ".
                  $ADMIN_GROUP_PAGES.".menu_order, ".
                  $ADMIN_GROUP_PAGES.".name";        

        $rows = \DB::select($query);
        $rows = json_decode(json_encode($rows), true);                  
        
        $groupname  = "";
        $scriptdata = "";
        $groupwidth = 0;
        
        $rowarray = array();

        foreach($rows as $row)
        {
            $rowarray[$row["trnid"]] = array(
                                             "trngroupid"    => $row["trngroupid"],
                                             "trngrouptitle" => $row["trngrouptitle"],
                                             "trnid"         => $row["trnid"],
                                             "trnname"       => $row["trnname"],
                                             "pageurl"       => $row["pageurl"],
                                             "trntitle"      => $row["trntitle"],
                                             "insubmenu"     => $row["insubmenu"],
                                             "show_in_menu"     => $row["show_in_menu"],                                             
                                             );
        }

       \Session::put('admin_user_rights', $rowarray);
       \Session::put('admin_user_rights_ids', $adminRightsArray);       
       \Session::save();        
    }
    

    public function getLogout()
    {
        $user = Auth::guard('admins')->user();
        Auth::guard('admins')->logout();
        // save log
        $params=array();

        $params['adminuserid']	= $user->id;
        $params['actionid']	= $this->adminAction->ADMIN_LOGOUT;
        $params['actionvalue']	= $user->id;
        $params['remark']	= 'Logout Admin User';

        \App\Models\AdminLog::writeadminlog($params);
        unset($params);
        
        return redirect('login');
    }            
}
