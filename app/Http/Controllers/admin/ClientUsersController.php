<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\ClientUser;
use App\Models\Client;
use App\Models\User;

class ClientUsersController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "client-users";
        $this->moduleViewName = "admin.client_users";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Client User";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new ClientUser();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_CLIENT_USER);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Client Users";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_CLIENT_USER);
        $data['clients'] = Client::pluck("name","id")->all();

       return view($this->moduleViewName.".index", $data);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_CLIENT_USER);
        
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
        $data["clients"] = \App\Models\Client::pluck('name','id')->all();
        
        return view($this->moduleViewName.'.add', $data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_CLIENT_USER);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'client_id'=>'required|exists:'.TBL_CLIENT.',id',
            'name' => 'required',
            'email' => 'required|email',
            'send_email' => Rule::in([0,1]),
            'status' => ['required', Rule::in([0,1])],
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
			$name = $request->get('name');
            $email = $request->get('email');
            $statuss = $request->get('status');
            $exists = User::where('email',$email)->first();
            if(!empty($exists)){
                $status = 0;
                $msg = "Email Already exists on Users table !";

                return ['status' => $status, 'msg' => $msg, 'data' => $data];
            }
			else{
				$input = $request->all();
				$input['send_email'] = isset($input['send_email']) ? 1:0;
				$obj = $this->modelObj->create($input);
				$client_user_id = $obj->id;
				
				$user = new User();
                $user->client_user_id = $client_user_id;
                $user->name = $name;
                $user->email = $email;
				// $password = getRandomStringNumber(10);
                $password = getRandomStringNumber(6);
                $bcry_password = bcrypt($password);
                
                $user->password = $bcry_password;
                $user->user_type_id = CLIENT_USER;
                $user->status = $statuss;
                $user->save();
				
				$this->sendRegisterEmail($user, $password);
            }
 
            //store logs detail
            $params=array();

            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_CLIENT_USER;
            $params['actionvalue']  = $client_user_id;
            $params['remark']       = "Add Client USer::".$client_user_id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data];              
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_CLIENT_USER);
        
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
        $data["clients"] = \App\Models\Client::pluck('name','id')->all();

        return view($this->moduleViewName.'.add', $data);
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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_CLIENT_USER);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [
           'client_id'=>'required|exists:'.TBL_CLIENT.',id',
            'name' => 'required',
            'email' => 'required|email',
            'send_email' => Rule::in([0,1]),
            'status' => ['required', Rule::in([0,1])],
        ]);
        
        // check validations
        if(!$model)
        {
            $status = 0;
            $msg = "Record not found !";
        }
        else if ($validator->fails()) 
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
			$name = $request->get('name');
            $email = $request->get('email');
            $statuss = $request->get('status');
            $exists = User::where('email',$email)->where('client_user_id','!=',$id)->first();
            if(!empty($exists)){
                $status = 0;
                $msg = "Email Already exists on Users table !";

                return ['status' => $status, 'msg' => $msg, 'data' => $data];
            }
            else{
				$input = $request->all();
				$input['send_email'] = isset($input['send_email']) ? 1:0;
				$model->update($input); 
			    
				$user = User::where('user_type_id','=',CLIENT_USER)->where('client_user_id','=',$id)->first();
                if(!empty($user))
                {
                    $user->name = $name;
                    $user->email = $email;
                    $user->status = $statuss;
                    $user->save();
                }
                else{
                    $user = new User();
                    $user->client_user_id = $id;
                    $user->name = $name;
                    $user->email = $email;
                    //$password = getRandomStringNumber(10);
					$password = getRandomStringNumber(6);
					$bcry_password = bcrypt($password);

					$user->password = $bcry_password;
                    $user->user_type_id = CLIENT_USER;
                    $user->status = $statuss;
                    $user->save();       
					
					$this->sendRegisterEmail($user, $password);
                }
            }
			
            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_CLIENT_USER;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Client User::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params);         
        }
        
        return ['status' => $status,'msg' => $msg, 'data' => $data];               
    }
	
	public function sendRegisterEmail($user, $password)
	{
			$subject = "Reports PHPdots: Account Details";
		
			$message = array();             
			$message['firstname'] = $user->name;
			$message['lastname'] = '';
			$message['email'] = $user->email;
			$message['password'] = $password;
			$message['link'] = url('/');

			$returnHTML = view('emails.create_user_temp',$message)->render();			

			$params["to"] = $user->email;
			$params["subject"] = $subject;
			$params["body"] = $returnHTML;
			sendHtmlMail($params);		
	}
	

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_CLIENT_USER);
        
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
				User::where('user_type_id','=',CLIENT_USER)->where('client_user_id','=',$id)->delete();
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                    $params=array();
                    
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_CLIENT_USER;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Client User::".$id;

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
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_CLIENT_USER);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = ClientUser::select(TBL_CLIENT_USER.".*",TBL_CLIENT.".name as client")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_CLIENT_USER.".client_id");

        return \Datatables::eloquent($model)        
               
            ->addColumn('action', function(ClientUser $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_CLIENT_USER),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_CLIENT_USER),                                                  
                    ]
                )->render();
            })
            ->editColumn('status', function ($row) {
                    if ($row->status == 1)
                        return "<a class='btn btn-xs btn-success'>Active</a>";
                    else
                        return '<a class="btn btn-xs btn-danger">Inactive</a>';
            })
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action','status'])             
            
            ->filter(function ($query) 
            {                              
                $search_name = request()->get("search_name");                                
                $search_email = request()->get("search_email");
                $search_client = request()->get("search_client");
                $search_status = request()->get("search_status");                                         
                if(!empty($search_name))
                {
                    $query = $query->where(TBL_CLIENT_USER.".name", 'LIKE', '%'.$search_name.'%');
                }
                if(!empty($search_email))
                {
                    $query = $query->where(TBL_CLIENT_USER.".email", 'LIKE', '%'.$search_email.'%');
                }
                if(!empty($search_client))
                {
                    $query = $query->where(TBL_CLIENT_USER.".client_id", $search_client);
                }
                if($search_status == "1" || $search_status == "0")
                {
                    $query = $query->where(TBL_CLIENT_USER.".status", $search_status);
                }                   
            })
            ->make(true);        
    }
}
