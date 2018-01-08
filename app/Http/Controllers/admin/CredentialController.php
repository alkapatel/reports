<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Project;
use App\Models\Credential;
use App\Models\ShareWithUser;
use App\Models\User;
use App\Models\ClientUser;
use App\Models\ClientEmployee;

class CredentialController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "credentials";
        $this->moduleViewName = "admin.credentials";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Credential";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Credential();  

        $this->addMsg = $module . " has been added successfully!";
        $this->updateMsg = $module . " has been updated successfully!";
        $this->deleteMsg = $module . " has been deleted successfully!";
        $this->deleteErrorMsg = $module . " can not deleted!";       

        view()->share("list_url", $this->list_url);
        view()->share("moduleRouteText", $this->moduleRouteText);
        view()->share("moduleViewName", $this->moduleViewName);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT_CREDENTIAL);

        if ($checkrights) {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->user()->user_type_id;

        $data = array();        
        $data['page_title'] = "Manage Project Credentials";
        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_PROJECT_CREDENTIAL);
        $data['projects'] = Project::pluck("title","id")->all();
		$data['types'] = ['FTP'=>'FTP','CPANEL'=>'CPANEL','SSH'=>'SSH','ADMIN/WP-ADMIN'=>'ADMIN/WP-ADMIN','FRONT-END'=>'FRONT-END','HOSTING'=>'HOSTING','EXTRA'=>'EXTRA'];
        $data['environment'] = ['Live'=>'Live','Dev'=>'Dev'];
		 
		if($auth_id == NORMAL_USER){
            $viewName = $this->moduleViewName.".userIndex";
        }
		if($auth_id == CLIENT_USER){
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_type =0;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }
            $data['projects'] = \App\Models\Project::getProjectList($client_type);
            $viewName = $this->moduleViewName.".clientIndex";
        }
        if($auth_id == ADMIN_USER_TYPE){
            $viewName = $this->moduleViewName.".index";
        }

       return view($viewName, $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST"; 
        $data['projects'] = Project::pluck("title","id")->all();
		$data['users'] = User::pluck("name","id")->all();
		$data['modes'] = ['Default'=>'Default','Active'=>'Active','Passive'=>'Passive'];
		
       $auth_type = \Auth::guard('admins')->user()->user_type_id;

        if($auth_type == CLIENT_USER){
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_type =0;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }
            $data['action_url'] = $this->moduleRouteText.".clientStore";
            $data['projects'] = Project::where('client_id',$client_type)->pluck("title","id")->all();
            $viewName = $this->moduleViewName.".clientAdd";
        }else{

            $viewName = $this->moduleViewName.".add";
        }

        return view($viewName, $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $i = 0;
        foreach($request->get("group-a") as $r)
        {
			$auth_id = \Auth::guard('admins')->user()->id;
            $obj = new Credential();
            $obj->protocol = isset($r['protocol']) ? $r['protocol']:'';
            $obj->project_id = isset($r['project_id']) ? $r['project_id']:'';
            $obj->hostname = isset($r['hostname']) ? $r['hostname']:'';
            $obj->username = isset($r['username']) ? $r['username']:'';
            $obj->password = isset($r['password']) ? $r['password']:'';
            $obj->port = isset($r['port']) ? $r['port']:'';
            $obj->url = isset($r['url']) ? $r['url']:'';
            $obj->description = isset($r['description']) ? $r['description']:'';
			$obj->title = isset($r['title']) ? $r['title']:'';
            $obj->environment = isset($r['environment']) ? $r['environment']:'';
			$obj->key_file_password = isset($r['key_file_password']) ? $r['key_file_password']:'';
			$obj->mode = isset($r['mode']) ? $r['mode']:'';
            $obj->created_by = $auth_id;
            $obj->save();
			$credential_id = $obj->id;
			$project_id = $obj->project_id;
            $project = Project::find($project_id);
			
			if(isset($r['share_users'])){
                $users =$r['share_users'];
                if(is_array($users))
                {
                    foreach($users as $user)
                    {   
                        $share = new ShareWithUser();
                        $share->credential_id=$credential_id;
                        $share->user_id=$user;
                        $share->save();
                    }
					foreach ($users as $user)
                    {
                        $detail = User::find($user);

                        $subject = "Reports PHPDots: Shared Project Credential";
                        $description = $project->title." Project Credentials has been shared with you. please find below link, for it.";
                        $Path = url('/')."/credentials?popup_id=".$credential_id;

                        $message = array();
                        $message['name'] = $detail->name;
                        $message['description'] = $description;
                        $message['link'] = $Path;
                        
                        $returnHTML = view('emails.credential_temp',$message)->render();
                        $auth_id = \Auth::guard('admins')->user();
                        $empName = ucfirst($auth_id->firstname)." ".ucfirst($auth_id->lastname);

                        $params["to"]=$detail->email;
                        $params["subject"] = $subject;
                        $params["from"] = $auth_id->email;
                        $params["from_name"] = $empName;  
                        $params["body"] = $returnHTML;
                        sendHtmlMail($params);
                    }
                }                 
            }
			
            $id = $obj->id;
            $arry = $request->file("group-a");            
            if(!empty($request->file("group-a")) && isset($arry[$i]['key_file']))
            {                
                
                $key_file = $arry[$i]['key_file'];
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'project_credentials'.DIRECTORY_SEPARATOR.$id;          
                
                $doc_name =$key_file->getClientOriginalName();
                
                // echo $destinationPath.' -> '.$doc_name;

                $key_files =$key_file->move($destinationPath,$doc_name);
                
                $obj->key_file = $doc_name;
                $obj->save();
            }

            //store logs detail
            $params=array();
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_PROJECT_CREDENTIAL;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Credential::".$id;
            $logs= \App\Models\AdminLog::writeadminlog($params);                        
            $i++;
        }
            session()->flash('success_message', $msg);

        return ['status' => $status, 'msg' => $msg, 'data' => $data];              
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function clientStore(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:'.TBL_PROJECT.',id',
            'protocol' => ['required',Rule::in(['FTP','CPANEL','SSH','ADMIN/WP-ADMIN','FRONT-END','HOSTING','EXTRA'])],
            'hostname' => 'min:2',
            'username' => 'min:2',
            'port' => 'numeric',
            'password' => 'min:2',
            'description' => 'min:2',
            'title' => 'min:2',
            'environment' => Rule::in(['Dev','Live']),
			'mode' => Rule::in(['Default','Active','Passive']),
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
            $auth_id = \Auth::guard('admins')->user()->id;
            
            $project_id = $request->get('project_id');
            $protocol = $request->get('protocol');
            $port = $request->get('port');
            $hostname = $request->get('hostname');
            $username = $request->get('username');
            $password = $request->get('password');
            $description = $request->get('description');
            $title = $request->get('title');
            $url = $request->get('url');
            $key_file = $request->file('key_file');
            $environment = $request->get('environment');
			$mode = $request->get('mode');
            $key_file_password = $request->get('key_file_password');

            $credential = new Credential();
             $id =$credential->id;   
                if(!empty($key_file))
                {
                    $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'project_credentials'.DIRECTORY_SEPARATOR.$id;          
                
                    $doc_name =$key_file->getClientOriginalName();
                    $key_files =$key_file->move($destinationPath,$doc_name);

                    $credential->key_file = $doc_name;
                }
                    $credential->project_id = $project_id;
                    $credential->protocol = $protocol;
                    $credential->port = $port;
                    $credential->hostname = $hostname;
                    $credential->username = $username;
                    $credential->password = $password;
                    $credential->description = $description;
                    $credential->title = $title;
                    $credential->url = $url;
                    $credential->environment = $environment;
                    $credential->key_file_password = $key_file_password;
                    $credential->created_by = $auth_id;
					$credential->mode = $mode;
                    $credential->save();

            $id = $credential->id;

            //store logs detail
            $params=array();
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_PROJECT_CREDENTIAL;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Credential::".$id;
            $logs= \App\Models\AdminLog::writeadminlog($params);                        
      
            session()->flash('success_message', $msg);
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $formObj = $this->modelObj->find($id);

        if(!$formObj)
        {
            abort(404);
        }   

        $data = array();
        $data['formObj'] = $formObj;
        $data['page_title'] = "Edit ".$this->module;
        $data['buttonText'] = "Update";

        $data['action_url'] = $this->moduleRouteText.".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT";
        $data['projects'] = project::pluck("title","id")->all();
		$data['share_users'] = \DB::table(TBL_USERS)->orderBy("name","ASC")->where('id','!=',\Auth::guard('admins')->user()->id)->get();
        $data['list_users'] =$formObj->getUsers(1);
		$data['modes'] = ['Default'=>'Default','Active'=>'Active','Passive'=>'Passive'];
		
        return view($this->moduleViewName.'.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);
        
        $status = 1;
        $msg = $this->updateMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:'.TBL_PROJECT.',id',
            'protocol' => ['required',Rule::in(['FTP','CPANEL','SSH','ADMIN/WP-ADMIN','FRONT-END','HOSTING','EXTRA'])],
            'hostname' => 'min:2',
            'username' => 'min:2',
            'port' => 'numeric',
            'password' => 'min:2',
            'description' => 'min:2',
			'title' => 'min:2',
            'environment' => Rule::in(['Dev','Live']),
            'users'=> 'exists:'.TBL_USERS.',id',
			'mode' => Rule::in(['Default','Active','Passive']),
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
        {   if(\Auth::guard('admins')->user()->id ==  $model->created_by){
                $auth_id = \Auth::guard('admins')->user()->id;
            }
            else{
                $auth_id = $model->created_by;
            }         
            $project_id = $request->get('project_id');
            $protocol = $request->get('protocol');
            $port = $request->get('port');
            $hostname = $request->get('hostname');
            $username = $request->get('username');
            $password = $request->get('password');
            $description = $request->get('description');
		 	$title = $request->get('title');
            $url = $request->get('url');
            $key_file = $request->file('key_file');
            $environment = $request->get('environment');
			$key_file_password = $request->get('key_file_password');
		 	$mode = $request->get('mode');


                if(!empty($key_file))
                {
                    $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'project_credentials'.DIRECTORY_SEPARATOR.$id;          
                
                    $doc_name =$key_file->getClientOriginalName();
                    $key_files =$key_file->move($destinationPath,$doc_name);

                    $model->key_file = $doc_name;
                }
                    $model->project_id = $project_id;
                    $model->protocol = $protocol;
                    $model->port = $port;
                    $model->hostname = $hostname;
                    $model->username = $username;
                    $model->password = $password;
                    $model->description = $description;
		 			$model->title = $title;
                    $model->url = $url;
		 			$model->mode = $mode;
                    $model->environment = $environment;
					$model->key_file_password = $key_file_password;
		 			$model->created_by = $auth_id;
                    $model->save();
		 			if(\Auth::guard('admins')->user()->user_type_id == ADMIN_USER_TYPE)
					{
						  //send mail
                        if($request->has('users'))
                        {
                            $users = $request->get('users');
                            if (is_array($users)) {
                                
                                $share_users = ShareWithUser::select('user_id')->where('credential_id',$model->id)->get()->toArray();
                                $old_users = array();
                                $i =0;
                                foreach ($share_users as $key => $value) {
                                    $old_users[$value['user_id']] = $value['user_id'];
                                    $i++;
                                }

                                $new_users = $users;
                                foreach($new_users as $key => $user_id)
                                {
                                    if(isset($old_users[$user_id]))
                                        unset($new_users[$key]);
                                }
                            }
                        }

                    \DB::table(TBL_SHARE_USER)->where('credential_id',$id)->delete();

                    if($request->has('users'))
                    {
                        $users = $request->get('users');
                        if(is_array($users))
                        {
                            foreach($users as $user)
                            {                 
                                $share = new ShareWithUser();
                                $share->credential_id=$model->id;
                                $share->user_id=$user;
                                $share->save();
                            }
                        }
						$max = count($new_users);
                        if(!empty($new_users) && $max>0)
                        {
                            foreach ($new_users as $key => $value)
                            {
                                $project = Project::find($project_id);
                                $detail = User::find($value);

                                $subject = "Reports PHPDots: Shared Project Credential";
                                $description = $project->title." Project Credentials has been shared with you. please find below link, for it.";
                                $Path = url('/')."/credentials?popup_id=".$id;

                                $message = array();
                                $message['name'] = $detail->name;
                                $message['description'] = $description;
                                $message['link'] = $Path;
                                
                                $returnHTML = view('emails.credential_temp',$message)->render();

                                $auth_id = \Auth::guard('admins')->user();
                                $empName = ucfirst($auth_id->firstname)." ".ucfirst($auth_id->lastname);

                                $params["to"]=$detail->email;
                                $params["subject"] = $subject;
                                $params["from"] = $auth_id->email;
                                $params["from_name"] = $empName;  
                                $params["body"] = $returnHTML;
                                sendHtmlMail($params);
                            }
                        }
                    }
                }

            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_PROJECT_CREDENTIAL;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Credential::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);                    
            
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data];              
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $modelObj = $this->modelObj->find($id); 

        if($modelObj) 
        {
            try 
            {             
                $backUrl = $request->server('HTTP_REFERER');
				\DB::table(TBL_SHARE_USER)->where('credential_id',$id)->delete();
                $key_file = $modelObj->key_file;
                if(!empty($key_file))
                {
                    $destinationPath = public_path().'/uploads/project_credentials'.$key_file;          
                    //unlink($destinationPath);
                }
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                    $params=array();
                    
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_PROJECT_CREDENTIAL;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Project Credential::".$id;

                    $logs=\App\Models\AdminLog::writeadminlog($params);    

                return redirect($backUrl);
            } 
            catch (Exception $e) 
            {
                session()->flash('error_message', $this->deleteErrorMsg);
                return redirect($this->list_url);
            }
        } 
        else 
        {
            session()->flash('error_message', "Record not exists");
            return redirect($this->list_url);
        }
    }
	public function getUsersList(Request $request)
    {        
        $data = array();   
        $project_id = $request->get('project_id');

        $project = Project::where('id',$project_id)->first();
        $client_id = $project->client_id;

        $client_users = ClientUser::where('client_id',$client_id)        
        ->join(TBL_USERS,TBL_USERS.".client_user_id","=",TBL_CLIENT_USER.".id")
        ->pluck(TBL_USERS.'.name',TBL_USERS.'.id')
        ->all();

        $client_emps = User::where('status',1)->whereNUll('client_user_id')->pluck('name','id')->all();

        $options = "";
        
        if(!empty($client_users) && count($client_users) != 0){
            $options .= "<option value=''>select Users</option>";
            foreach ($client_users as $key => $user) {
                    $options .= "<option value='$key'>$user</option>";
            }
        }
        if(!empty($client_emps) && count($client_emps) != 0){
            foreach ($client_emps as $key => $user) {
                    $options .= "<option value='$key'>$user</option>";
            }
        }else{
            $options .= "<option value=''>No Users</option>";
        }
        echo $options;exit;
        
    }
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = Credential::select(TBL_CREDENTIAL.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_CREDENTIAL.".project_id");

        //$model = ProjectCredential::query();

        return \Datatables::eloquent($model)        
               
            ->addColumn('action', function(Credential $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_PROJECT_CREDENTIAL),
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_PROJECT_CREDENTIAL),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_PROJECT_CREDENTIAL),                                                  
                    ]
                )->render();
            })
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action'])
            
            ->filter(function ($query) 
            {                              
                $search_project = request()->get("search_project");                                
                $search_protocol = request()->get("search_protocol");
                $search_env = request()->get("search_env");

                if(!empty($search_protocol))
                {
                    $query = $query->where(TBL_CREDENTIAL.".protocol", $search_protocol);
                }
                if(!empty($search_env))
                {
                    $query = $query->where(TBL_CREDENTIAL.".environment",$search_env);
                }
                if(!empty($search_project))
                {
                    $query = $query->where(TBL_CREDENTIAL.".project_id", $search_project);
                }
            })
            ->make(true);        
    }
	public function userData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id();
        $model = Credential::select(TBL_CREDENTIAL.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_CREDENTIAL.".project_id") 
                ->leftJoin(TBL_SHARE_USER,TBL_SHARE_USER.".credential_id","=",TBL_CREDENTIAL.".id")               
                ->where(TBL_CREDENTIAL.".created_by",$auth_id)
                ->orWhere(TBL_SHARE_USER.".user_id",$auth_id)
                ;

        return \Datatables::eloquent($model)
               
            ->addColumn('action', function(Credential $row) {

                $editFlag = 0;
                $deleteFlag = 0;
                if($row->created_by == \Auth::guard('admins')->id())
                {
                    $deleteFlag = \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_PROJECT_CREDENTIAL);
                    $editFlag= \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_PROJECT_CREDENTIAL);
                }                
                    
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_PROJECT_CREDENTIAL),
                        'isEdit' => $editFlag,
                        'isDelete' => $deleteFlag,
                    ]
                )->render();
            })
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action'])
            
            ->filter(function ($query) 
            {                              
                $search_project = request()->get("search_project");                                
                $search_protocol = request()->get("search_protocol");
                $search_env = request()->get("search_env");

                if(!empty($search_protocol))
                {
                    $query = $query->where(TBL_CREDENTIAL.".protocol", $search_protocol);
                }
                if(!empty($search_env))
                {
                    $query = $query->where(TBL_CREDENTIAL.".environment", $search_env);
                }
                if(!empty($search_project))
                {
                    $query = $query->where(TBL_CREDENTIAL.".project_id", $search_project);
                }
            })
            ->make(true);        
    }
    public function viewData(Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $id = $request->get('credential_id');

        if(!empty($id)){
            
            $credential = Credential::select(TBL_CREDENTIAL.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_CREDENTIAL.".project_id")
                ->where(TBL_CREDENTIAL.".id",$id)
                ->get();
        }
        return view("admin.credentials.viewData", ['views'=>$credential]);
    }

    public function downloadFile($id,Request $request)
    {
        $obj = Credential::find($id);
        if($obj)
        {
            $id = $obj->id;
            $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'project_credentials'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$obj->key_file;

            downloadFile($obj->key_file,$destinationPath);
            exit;
        }
        else
        {
            abort(404);
        }
    }
	public function clientData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT_CREDENTIAL);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id();
        $model = Credential::select(TBL_CREDENTIAL.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_CREDENTIAL.".project_id") 
                ->leftJoin(TBL_SHARE_USER,TBL_SHARE_USER.".credential_id","=",TBL_CREDENTIAL.".id")               
                ->where(TBL_CREDENTIAL.".created_by",$auth_id)
                ->orWhere(TBL_SHARE_USER.".user_id",$auth_id)
                ;

        return \Datatables::eloquent($model)
               
            ->addColumn('action', function(Credential $row) {
     
                return view("admin.credentials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_PROJECT_CREDENTIAL),
                    ]
                )->render();
            })
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action'])
            
            ->filter(function ($query) 
            {                              
                $search_project = request()->get("search_project");                                
                $search_protocol = request()->get("search_protocol");
                $search_env = request()->get("search_env");

                if(!empty($search_protocol))
                {
                    $query = $query->where(TBL_CREDENTIAL.".protocol", $search_protocol);
                }
                if(!empty($search_env))
                {
                    $query = $query->where(TBL_CREDENTIAL.".environment", $search_env);
                }
                if(!empty($search_project))
                {
                    $query = $query->where(TBL_CREDENTIAL.".project_id", $search_project);
                }
            })
            ->make(true);        
    }
}
