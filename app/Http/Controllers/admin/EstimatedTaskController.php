<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\EstimatedTask;


class EstimatedTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct() {
    
        $this->moduleRouteText = "estimated-tasks";
        $this->moduleViewName = "admin.estimate_tasks";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Estimate Task";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new EstimatedTask();

        $this->addMsg = $module . " has been added successfully!";
        $this->updateMsg = $module . " has been updated successfully!";
        $this->deleteMsg = $module . " has been deleted successfully!";
        $this->deleteErrorMsg = $module . " can not deleted!";       

        view()->share("list_url", $this->list_url);
        view()->share("moduleRouteText", $this->moduleRouteText);
        view()->share("moduleViewName", $this->moduleViewName);

    }
    public function index(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ESTIMATE_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Manage Estimate Tasks";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_ESTIMATE_TASK);
        $data['projects'] = \App\Models\Project::getList();

        $auth_id = \Auth::guard('admins')->user()->user_type_id;

        if($auth_id == NORMAL_USER){
            $data['users']='';
            $viewName = $this->moduleViewName.".userIndex";
        }else{
            $data['users'] = User::getList();
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ESTIMATE_TASK);
        
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
        $data['projects'] = Project::where('status',1)->pluck("title","id")->all();
        $data['users'] = User::where('status',1)->pluck("name","id")->all();
        $data['hours'] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24'];
        $data['mins'] = ['0.00'=>'0.00','0.25'=>'0.25','0.50'=>'0.50','0.75'=>'0.75'];

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ESTIMATE_TASK);
        if($checkrights) 
        {
            return $checkrights;
        }
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        $true = 0;
         
        foreach($request->get('group-a') as $r) {
        $validator = Validator::make($r, [
            'task' => 'required|min:2',
            'user_id' => 'exists:'.TBL_USERS.',id',
            'project_id' => 'required|exists:'.TBL_PROJECT.',id',
            'estimated_hour' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24])],
            'estimated_min' => ['required', Rule::in([0.00,0.25,0.50,0.75])],
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
        else{
            $true = 1;
        }
    }
    if($true == 1){

       foreach($request->get("group-a") as $r)
        {   $hourFlag = 1;
            $hour = $r['estimated_hour'];
             $min = $r['estimated_min'];
            if($hour== 0 && $min== 0.00){

            $hourFlag = 0;
            }

            if($hourFlag == 0){
                $status = 0;
                return ['status' => $status, 'msg' => 'please enter valid time']; 
            }
        }

        $i = 0;
        foreach($request->get("group-a") as $r)
        { 
            $auth_id = \Auth::guard('admins')->user()->id;
               
                $obj = new EstimatedTask();

                if(isset($r['user_id']) && !empty($r['user_id'])){
                    $username = $r['user_id']; 
                }
                else{
                    $username = $auth_id;
                }
                $obj->user_id = $username;
                if(isset($r['task_date']) && !empty($r['task_date'])){
                    $date = $r['task_date']; 
                    $date = date('Y-m-d',strtotime($date));
                }
                else{
                    $date =  date("Y-m-d");
                }

                $obj->project_id = isset($r['project_id']) ? $r['project_id']:'';
                $obj->task = isset($r['task']) ? $r['task']:'';
                $obj->estimated_hour = isset($r['estimated_hour']) ? $r['estimated_hour']:'';
                $obj->estimated_min = isset($r['estimated_min']) ? $r['estimated_min']:'';
                $estimated_total_time = $r['estimated_hour'] + $r['estimated_min'];
                $obj->estimated_total_time = $estimated_total_time;
                $obj->status = 0;
                $obj->task_date = $date;
                $obj->save();

                $id = $obj->id;
         
                //store logs detail
                $params=array();
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_ESTIMATE_TASK;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Estimated Task::".$id;
                $logs= \App\Models\AdminLog::writeadminlog($params);                        
                $i++;
            }
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ESTIMATE_TASK);
        
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
        $data['editMode'] = "";
        $data['projects'] = Project::where('status',1)->pluck("title","id")->all();
        $data['users'] = User::where('status',1)->pluck("name","id")->all();
        $data['hours'] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24'];
        $data['mins'] = ['0.00'=>'0.00','0.25'=>'0.25','0.50'=>'0.50','0.75'=>'0.75'];

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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ESTIMATE_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in([1,2,3])],
            'actual_hour' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24])],
            'actual_min' => ['required', Rule::in([0.00,0.25,0.50,0.75])],
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
            $hourFlag= 1;
            $hour = $request->get('actual_hour');
            $min = $request->get('actual_min');
            if($hour == 0 && $min == 0.00)
                $hourFlag = 0;
            if($hourFlag == 0){
                $status = 0;
                return ['status' => $status, 'msg' => 'please enter valid time']; 
            }
            $actual_hour = $request->get("actual_hour");
            $actual_min = $request->get("actual_min");
            $statuss= $request->get("status");
            $delivery_description= $request->get("delivery_description");
            $actual_total_time = $actual_hour + $actual_min;

            $model->actual_hour = $actual_hour;
            $model->actual_min = $actual_min;
            $model->status = $statuss;
            $model->delivery_description = $delivery_description;
            $model->actual_total_time = $actual_total_time;
            $model->update(); 

            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_ESTIMATE_TASK;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Estimated Task::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_ESTIMATE_TASK);
        
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
                    $params['actionid']     = $this->adminAction->DELETE_ESTIMATE_TASK;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Estimated Task::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ESTIMATE_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = EstimatedTask::select(TBL_ESTIMATED_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_ESTIMATED_TASK.".project_id")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_ESTIMATED_TASK.".user_id");

        $data = \Datatables::eloquent($model) 
               
            ->addColumn('action', function(EstimatedTask $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ESTIMATE_TASK),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_ESTIMATE_TASK),                                                  
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ESTIMATE_TASK),                                                  
                    ]
                )->render();
            })
            ->editColumn('status', function ($row) {
                    if ($row->status == 0)
                        return "<a class='btn btn-xs btn-primary'>Pending</a>";
                    else if($row->status == 1)
                        return '<a class="btn btn-xs btn-success">Completed</a>';
                    else if($row->status == 2)
                        return '<a class="btn btn-xs btn-danger">In Progress</a>';
                    else if($row->status == 3)
                        return '<a class="btn btn-xs btn-warning">Skip</a>';
            })
            ->editColumn('estimated_total_time', function ($row) {
                    $time =  '<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->estimated_total_time;
                    return $time;
            })
            ->editColumn('actual_total_time', function ($row) {
                    $time = '';
                    if (!empty($row->actual_total_time))
                        $time .=  '<i class="fa fa-clock-o" aria-hidden="true"></i> '.$row->actual_total_time;
                    return $time;
            })
             ->editColumn('task_date', function($row){
                if(!empty($row->task_date))          
                    return date("j M, Y",strtotime($row->task_date)).'<br/><span style="color: blue; font-size: 12px">'.date("j M, Y",strtotime($row->created_at))."</span>";
                else
                    return '-';
            })->rawColumns(['status','action','estimated_total_time','actual_total_time','task_date'])             
            
            ->filter(function ($query) 
            {                              
                $query = EstimatedTask::listFilter($query);
            });
            $data = $data->make(true);
            return $data;     
    }
    public function userData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ESTIMATE_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id();
        $model = EstimatedTask::select(TBL_ESTIMATED_TASK.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_ESTIMATED_TASK.".project_id")
                ->where(TBL_ESTIMATED_TASK.'.user_id',$auth_id);

        $data = \Datatables::eloquent($model) 
               
            ->addColumn('action', function(EstimatedTask $row) {
                return view("admin.estimate_tasks.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ESTIMATE_TASK),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_ESTIMATE_TASK),                                                  
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ESTIMATE_TASK),                                                  
                    ]
                )->render();
            })
            ->editColumn('status', function ($row) {
                    if ($row->status == 0)
                        return "<a class='btn btn-xs btn-primary'>Pending</a>";
                    else if($row->status == 1)
                        return '<a class="btn btn-xs btn-success">Completed</a>';
                    else if($row->status == 2)
                        return '<a class="btn btn-xs btn-danger">In Progress</a>';
                    else if($row->status == 3)
                        return '<a class="btn btn-xs btn-warning">Skip</a>';
            })
            ->editColumn('estimated_total_time', function ($row) {
                    $time =  '<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->estimated_total_time;
                    return $time;
            })
            ->editColumn('actual_total_time', function ($row) {
                    $time = '';
                    if (!empty($row->actual_total_time))
                        $time .=  '<i class="fa fa-clock-o" aria-hidden="true"></i> '.$row->actual_total_time;
                    return $time;
            })
            ->editColumn('task_date', function($row){
                
                if(!empty($row->task_date))          
                    return date("j M, Y",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['status','action','estimated_total_time','actual_total_time'])
            
            ->filter(function ($query) 
            {
                $query = EstimatedTask::listFilter($query);
            });
            $data = $data->make(true);
            return $data;       
    }
    public function viewData(Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ESTIMATE_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $id = $request->get('task_id');

        if(!empty($id)){
            $data = array();
            $task = EstimatedTask::select(TBL_ESTIMATED_TASK.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_ESTIMATED_TASK.".project_id")
                ->where(TBL_ESTIMATED_TASK.".id",$id)
                ->first();
            $data['user_name'] = User::select('name')->where('id',$task->user_id)->first();
            $data['view'] = $task;    
        }
        return view("admin.estimate_tasks.viewData",$data);
    }
}
