<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Datatables;
use Validator; 
use App\Models\AdminUser;
use App\Models\AdminAction;
use App\Models\AdminUserType;

class AdminUserController extends Controller
{
	
    public function __construct() {
    
        $this->moduleRouteText = "admin-users";
        $this->moduleViewName = "admin.admin_users";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Admin User";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new AdminUser();  

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
        $data = array();        
        $data['page_title'] = "Manage Admin Users";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_ADMIN_USERS);
        $data['userTypeList'] = \App\Models\AdminUserType::pluck("title","id")->all(); 

       return view($this->moduleViewName.".index", $data);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ADMIN_USERS);
        
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
        $data['show_password'] = 1;

        $data['userTypeList'] = \App\Models\AdminUserType::pluck("title","id")->all(); 

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ADMIN_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = "Adminuser has been created successfully.";
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'email' => 'required|email|unique:admin_users,email',
            'user_type_id' => 'required|exists:admin_user_types,id',
            'password' => 'required|min:8|same:password',            
            'confirm_password' => 'required|min:8|same:password',
            
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
            $input = $request->all();
            $obj = $this->modelObj->create($input);
            $id = $obj->id;

            $obj->password = bcrypt($obj->password);
            $obj->save();
            
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_ADMIN_USERS ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Admin User::".$id;
                                    
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ADMIN_USERS);
        
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
        $data['show_password'] = 0; 
        $data['userTypeList'] = AdminUserType::pluck("title","id")->all(); 

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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ADMIN_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'email' => 'required|email|unique:admin_users,email,'.$id,
            'user_type_id' => 'required|exists:admin_user_types,id',            
            
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
            $model->update($input); 

            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_ADMIN_USERS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Admin User::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params);         
        }
        
        return ['status' => $status,'msg' => $msg, 'data' => $data];               
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_ADMIN_USERS);
        
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
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                    $params=array();
                    
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_ADMIN_USERS;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Admin User::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ADMIN_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = AdminUser::select(TBL_ADMIN_USERS.".*",TBL_ADMIN_USER_TYPE.".title as user_type")
                ->join(TBL_ADMIN_USER_TYPE,TBL_ADMIN_USER_TYPE.".id","=",TBL_ADMIN_USERS.".user_type_id");

        return Datatables::eloquent($model)        
               
            ->addColumn('action', function(AdminUser $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ADMIN_USERS),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_ADMIN_USERS),                                                         
                    ]
                )->render();
            })

            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })             
            ->filter(function ($query) 
            {
                $search_start_date = request()->get("search_start_date");                 
                $search_end_date = request()->get("search_end_date");                              
                $search_id = request()->get("search_id");                                         
                $user_type_id = request()->get("user_type_id");        
                $search_fnm = request()->get("search_fnm");                                         
                $search_email = request()->get("search_email");                                    

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_ADMIN_USERS.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_ADMIN_USERS.".created_at","<=",addslashes($convertToDate));
                }
                if(!empty($search_id))
                {
                    $idArr = explode(',', $search_id);
                    $idArr = array_filter($idArr);                
                    if(count($idArr)>0)
                    {
                        $query = $query->whereIn(TBL_ADMIN_USERS.".id",$idArr);
                    } 
                }
                if(!empty($user_type_id))
                {
                    $query = $query->where(TBL_ADMIN_USERS.".user_type_id", $user_type_id);
                } 
                if(!empty($search_fnm))
                {
                    $query = $query->where(TBL_ADMIN_USERS.".name", 'LIKE', '%'.$search_fnm.'%');
                } 
                 if(!empty($search_email))
                {
                    $query = $query->where(TBL_ADMIN_USERS.".email", 'LIKE', '%'.$search_email.'%');
                }  
            })
            ->make(true);        
    }   

}
    
