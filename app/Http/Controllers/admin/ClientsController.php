<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Datatables;
use App\Models\Client;
use App\Models\AdminAction;
use App\Models\ClientUser;
use App\Models\ClientEmployee;
use App\Models\User;
use Illuminate\Validation\Rule;

class ClientsController extends Controller
{

    public function __construct() {

        $this->moduleRouteText = "clients";
        $this->moduleViewName = "admin.clients";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Client";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Client();  

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
    public function index()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_CLIENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Clients";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_CLIENT);        
        
        return view($this->moduleViewName.".index", $data);         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_CLIENT);
        
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
		$data['users'] = \DB::table(TBL_USERS)->orderBy("name","ASC")->get();
        $data['list_tags'] = [];

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_CLIENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:'.TBL_CLIENT.',email',
            'name' => 'required|min:2',
            'send_email' => Rule::in([0,1]),
			'users'=>'exists:'.TBL_USERS.',id',
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
            $input = $request->all();
            $input['send_email'] = isset($input['send_email']) ? 1:0;
            $obj = $this->modelObj->create($input);
            $id = $obj->id; 
			
			 $users = $request->get('users');

            if(is_array($users))
            {
                foreach ($users as $user)
                {
                    $employee = new ClientEmployee();
                        $employee->client_id = $id;
                        $employee->user_id = $user;
                        $employee->save();
                }   
            } 

            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_CLIENT ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Client ::".$id;
                                    
            $logs=\App\Models\AdminLog::writeadminlog($params);

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
    public function edit($id)
    {    
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_CLIENT);
        
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
		$data['list_tags'] = $formObj->getClients(1);
        $data['users'] = \DB::table(TBL_USERS)->orderBy("name","ASC")->get();
        
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
		$client_id = $id;
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_CLIENT);
        
        if($checkrights) 
        {
            return $checkrights;
        } 

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:'.TBL_CLIENT.',email,'.$id,
            'name'     => 'required|min:2',
            'send_email' => Rule::in([0,1]),
			'users'=>'exists:'.TBL_USERS.',id',
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
            $input = $request->all();
            $input['send_email'] = isset($input['send_email']) ? 1:0;
            $model->update($input);
			
			// delete old records
            \DB::table(TBL_CLIENT_EMPLOYEE)->where('client_id',$id)->delete();             

            if($request->has('users'))
            {
				$users = $request->get('users');
                if(is_array($users))
                {
                    foreach($users as $user)
                    {                 
                        $employee = new ClientEmployee();
                        $employee->client_id = $client_id;
                        $employee->user_id = $user;
                        $employee->save();
                    }
                }  
            }

            //store logs detail
            $params=array();

            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_CLIENT;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Clients::".$id;

            $logs=\App\Models\AdminLog::writeadminlog($params);
            
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
    public function destroy($id,Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_CLIENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }   
        $modelObj = $this->modelObj->find($id);

        if($modelObj) 
        {
            try 
            {    
				\DB::table(TBL_CLIENT_EMPLOYEE)->where('client_id',$id)->delete(); 
                $client = ClientUser::where('client_id',$id);
                $client->delete();
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->DELETE_CLIENT;
                $params['actionvalue']  = $id;
                $params['remark']       = "Delete Client::".$id;
                
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_CLIENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = Client::query();

        return \Datatables::eloquent($model)
            ->editColumn('created_at', function($row){                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })   
            ->addColumn('action', function(Client $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_CLIENT),
                        'isDelete' =>\App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_CLIENT),                                                        
                    ]
                )->render();
            })->rawColumns(['action','created_at'])
            ->filter(function ($query) {

                $search_name = request()->get("search_name");
                $search_email = request()->get("search_email");
                $search_phone = request()->get("search_phone");
                $search_city = request()->get("search_city");
                $search_state = request()->get("search_state");                                         

                if(!empty($search_name))
                {
                    $query = $query->where('name', 'LIKE', '%'.$search_name.'%');
                }
                if(!empty($search_email))
                {
                    $query = $query->where('email', 'LIKE', '%'.$search_email.'%');
                }
                if(!empty($search_phone))
                {
                    $query = $query->where('phone', 'LIKE', '%'.$search_phone.'%');
                }
                if(!empty($search_city))
                {
                    $query = $query->where('city', 'LIKE', '%'.$search_city.'%');
                }
                if(!empty($search_state))
                {
                    $query = $query->where('state', 'LIKE', '%'.$search_state.'%');
                }
            })->make(true);        
    }
}
