<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Project;
use App\Models\Client;
use App\Models\ClientUser;

class ProjectsController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "projects";
        $this->moduleViewName = "admin.projects";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Project";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Project();  

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
		$checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Projects";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_PROJECT);
		
		$auth_id = \Auth::guard('admins')->user()->user_type_id;
        if($auth_id == CLIENT_USER){
            $data['clients']='';
            $viewName = $this->moduleViewName.".clientIndex";
        }else{
            $data['clients'] = Client::pluck("name","id")->all();
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_PROJECT);
        
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
        $data['clients'] = Client::pluck("name","id")->all();
		
		$auth_id = \Auth::guard('admins')->user()->user_type_id;
        if($auth_id == CLIENT_USER)
            $data['action_url'] = $this->moduleRouteText . ".clientStore"; 
        else
            $data['action_url'] = $this->moduleRouteText.".store";

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_PROJECT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2|unique:'.TBL_PROJECT.',title',
            'status' => ['required', Rule::in([0,1])],
            'client_id' => 'required|exists:'.TBL_CLIENT.',id',
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
 
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_PROJECT ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Project::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);                    
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data];              
    }
	public function clientStore(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_PROJECT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2|unique:'.TBL_PROJECT.',title'
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
            $client_type = 0;
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }

            $title = $request->get('title');
            $project = new Project();
            
            $project->title = $title;
            $project->client_id = $client_type;
            $project->status = 1;
            $project->save();

            $id = $project->id;
 
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_PROJECT;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Project::".$id;
                                    
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_PROJECT);
        
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
        $data['clients'] = Client::pluck("name","id")->all();

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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_PROJECT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2|unique:'.TBL_PROJECT.',title,'.$id,
            'status' => ['required', Rule::in([0,1])],
            'client_id' => 'required|exists:'.TBL_CLIENT.',id',
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
                $params['actionid']     = $this->adminAction->EDIT_PROJECT;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Project::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_PROJECT);
        
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
                    $params['actionid']     = $this->adminAction->DELETE_PROJECT;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Project::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = Project::select(TBL_PROJECT.".*",TBL_CLIENT.".name as client_name")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_PROJECT.".client_id");

        return \Datatables::eloquent($model)        
               
            ->addColumn('action', function(Project $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_PROJECT),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_PROJECT),                                           
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
            })->rawColumns(['status','action'])             
            
            ->filter(function ($query) 
            {                              
                $search_title = request()->get("search_title");                                
                $search_status = request()->get("search_status");                                         
                $search_client = request()->get("search_client");                                         
                if(!empty($search_title))
                {
                    $query = $query->where(TBL_PROJECT.".title", 'LIKE', '%'.$search_title.'%');
                }
                if($search_status == "1" || $search_status == "0")
                {
                    $query = $query->where(TBL_PROJECT.".status", $search_status);
                }
                if(!empty($search_client))
                {
                    $query = $query->where(TBL_PROJECT.".client_id", $search_client);
                }                   
            })
            ->make(true);        
    }
	public function clientData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_PROJECT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
            $client_type = 0;
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }
        $model = Project::select(TBL_PROJECT.".*")->where(TBL_PROJECT.".client_id",$client_type);

        return \Datatables::eloquent($model) 

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
            })->rawColumns(['status'])             
            
            ->filter(function ($query) 
            {                              
                $search_title = request()->get("search_title");                                
                $search_status = request()->get("search_status");                          
                
                if(!empty($search_title))
                {
                    $query = $query->where(TBL_PROJECT.".title", 'LIKE', '%'.$search_title.'%');
                }
                if($search_status == "1" || $search_status == "0")
                {
                    $query = $query->where(TBL_PROJECT.".status", $search_status);
                }                  
            })
            ->make(true);        
    }
}
