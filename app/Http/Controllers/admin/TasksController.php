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
use App\Models\Client;
use App\Models\ClientUser;
use Excel;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
    
        $this->moduleRouteText = "tasks";
        $this->moduleViewName = "admin.tasks";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Task";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Task();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Manage Tasks";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_TASKS);
        $data['projects'] = \App\Models\Project::getList();

        $auth_id = \Auth::guard('admins')->user()->user_type_id;

        if($auth_id == NORMAL_USER){
            $data['users']='';
			$data['clients']='';
            $viewName = $this->moduleViewName.".userIndex";
        }
        else if($auth_id == ADMIN_USER_TYPE){
            $data['users'] = User::getList();
            $data['clients'] = Client::pluck("name","id")->all();
			
			$is_download = $request->get("isDownload");
			$is_download_xls = $request->get("isDownloadXls");

            if (!empty($is_download) && $is_download == 1) {
	
				$total = $request->get("is_total");
				
                $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");
                $rows = Task::listFilter($query);
                

                $records[] = array("No","User Name","Project","Task","Date","Hours","Status","Reference Link");
                $i = 1;
                foreach($rows as $row)
                {
                    if($row->status == 1) $sts = "Completed"; else $sts = "In Progress";
                    $task_date = date("j M, Y",strtotime($row->task_date));
                    $records[] = [$i,$row->user_name,$row->project_name,$row->title,$task_date,$row->total_time,$sts,$row->ref_link];
                $i++;
                }
				$records[] = array("total","","","","",$total,"","");
                $file_name = 'TasksDetails';
                header("Content-type: text/csv; charset=utf-8");
                header("Content-Disposition: attachment; filename=".$file_name.".csv");
                
                $fp = fopen('php://output', 'w');                
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
                    foreach ($records as $fields) {
                        fputcsv($fp, $fields);
                    }

                fclose($fp);                
                $path = public_path().'/'.$file_name.'.csv';
                exit;
            }
			if (!empty($is_download_xls) && $is_download_xls == 1) {
            
                $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");
                $query = Task::listFilter($query);
                $rows = $query->get();

                $userWiseTasks= [];
                
                 if(count($rows) > 0 ) {
                    $xls_sheet = Excel::create('TasksDetails', function($excel) use ($rows) {
                         
                    $i = 1;
                   
                    foreach($rows as $row)
                    {
                    if($row->status == 1) $sts = "Completed"; else $sts = "In Progress";

                    $task_date = date("j M, Y",strtotime($row->task_date));
                     
                    $userWiseTasks[$row->user_id]['name'] = $row->user_name;
                    $userWiseTasks[$row->user_id]['time'][] = $row->total_time;

                    $time_total = (float)$row->total_time;
                    $userWiseTasks[$row->user_id]['tasks'][] = [$i,$row->project_name,$row->title,$task_date,$time_total,$sts,$row->ref_link];

                    $i++;
                    }
                   
                    $task_title[] = array("No","Project","Task","Date","Hours","Status","Reference Link");

                    foreach($userWiseTasks as $k => $v)
                    {
                        $total_time = array_sum($userWiseTasks[$k]['time']);
                        $time[$k][] = array("Total","","","",$total_time,"","");
                        $i =1;
                        foreach ($userWiseTasks[$k]['tasks'] as $key => $value) {
                            $userWiseTasks[$k]['tasks'][$key][0] = $i;
                            $i++;
                        }
                            $task_count = count($userWiseTasks[$k]['tasks']);
                            $task_count = $task_count+2;
                            //$userWiseTasks[$k]['count'] = $task_count;
                        //Bill Details 
                        $bill_detail[] = ['',$userWiseTasks[$k]['name'],$total_time,1,$total_time];
                        $j =1;
                        $bill_total = 0;
                        foreach ($bill_detail as $key =>$v) {
                            $bill_detail[$key][0] = $j;
                            $j++;
                            $bill_total += $bill_detail[$key][4];
                        
                        }                          
                        //echo "<pre/>"; print_r($userWiseTasks);exit;

                        $excel->sheet($userWiseTasks[$k]['name'], function($sheet) use ($userWiseTasks,$k,$task_title,$time,$task_count) {
                            $sheet->setSize(array(
                                'A1' => array('width'=> 7,'height'=> 15),
                                'B1' => array('width'=> 25,'height' => 15),
                                'C1' => array('width'=> 50,'height' => 15),
                                'D1' => array('width'=> 15,'height' => 15),
                                'E1' => array('width'=> 10,'height' => 15),
                                'F1' => array('width'=> 13,'height' => 15),
                                'G1' => array('width'=> 60,'height' => 15),
                            ));
                            $sheet->cell('A1:G1', function($cell) {
                                $cell->setAlignment('center');
                                $cell->setBackground('#aebbc2');
                                $cell->setFont(array('family'=> 'Calibri','size'=>'12','bold'=>true));
                            });
                            $sheet->setBorder('A1:G'.$task_count, 'thin');
                            $sheet->mergeCells('A'.$task_count.':D'.$task_count);
                            $sheet->cell('A'.$task_count.':D'.$task_count, function($cell) {
                                $cell->setAlignment('center');
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->cell('E'.$task_count, function($cell) {
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->freezeFirstRow();
                            $sheet->fromArray($task_title, null, 'A1', false, false);
                            $sheet->fromArray($userWiseTasks[$k]['tasks'], null, 'A1', false, false);
                            $sheet->fromArray($time[$k], null, 'A1', true, false);
                        });                        
                    }
                    $merg = count($bill_detail); $merg = $merg+2;  
                    $border = count($bill_detail); $border = $border+2;  
                    $bill_totals[] = array('Total','','','',$bill_total);
                    $excel->sheet('Bill Detail', function($sheet) use ($bill_detail,$bill_totals,$merg,$border) {
                            $sheet->setSize(array(
                                'A1' => array('width'=> 10,'height'=> 15),
                                'B1' => array('width'=> 30,'height' => 15),
                                'C1' => array('width'=> 10,'height' => 15),
                                'D1' => array('width'=> 10,'height' => 15),
                                'E1' => array('width'=> 20,'height' => 15),
                            ));
                            $sheet->row(1, array('No','Name','Hours','Rate','Total'));
                            $sheet->mergeCells('A'.$merg.':D'.$merg);
                            $sheet->setBorder('A1:E'.$border, 'thin');
                            $sheet->cell('A1:E1', function($cell) {
                                $cell->setBackground('#aebbc2');
                                $cell->setAlignment('center');
                                $cell->setFont(array('family'=>'Calibri','size'=>'12','bold'=>true));
                            });
                            $sheet->cell('A'.$merg.':D'.$merg, function($cell) {
                                $cell->setAlignment('center');
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->cell('E'.$merg, function($cell) {
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->fromArray($bill_detail, null, 'A2', false, false);
                            $sheet->fromArray($bill_totals, null, 'A2', false, false);
                        });    

                    });
                    $xls_sheet->download('xlsx');
                }
            }
			
            $viewName = $this->moduleViewName.".index";
        }
		else if($auth_id == CLIENT_USER){
            $client_type = 0;
			$client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                    $client_type = $client_user->client_id;
            }
            $data['projects'] = \App\Models\Project::getProjectList($client_type);
            $data['users'] = \App\Models\ClientEmployee::getUserList($client_type);
            $data['clients']='';
            $viewName = $this->moduleViewName.".clientIndex";
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_TASKS);
        
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
        $data["editMode"] = 1; 
        $data['projects'] = Project::where('status',1)->pluck("title","id")->all();
        $data['users'] = User::where('status',1)->pluck("name","id")->all();
        //$data['users'] = User::getList();
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'project_id.*' => 'required|exists:'.TBL_PROJECT.',id',
            'user_id.*' => 'exists:'.TBL_USERS.',id',
            'title.*' => 'required|min:2',
            //'description' => 'required',
            'hour.*' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24])],
            'min.*' => ['required', Rule::in([0.00,0.25,0.50,0.75])],
            'status.*' => ['required', Rule::in([0,1])]
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
            $hourFlag= 1;
            $hour = $request->get('hour');
            $min = $request->get('min');
            $hour_count = count($hour);
            for ($i=0; $i <$hour_count; $i++) { 
                if($hour[$i] == 0 && $min[$i] == 0.00)
                    $hourFlag = 0;
            }
            if($hourFlag == 0){
                $status = 0;
                return ['status' => $status, 'msg' => 'please enter valid time']; 
            }


            $project_id = $request->get('project_id');
            $title = $request->get('title');
            $description = $request->get('description');
            //$hour = $request->get('hour');
            //$min = $request->get('min');
            $statuss = $request->get('status');
            $ref_link = $request->get('ref_link');

            $auth_id = \Auth::guard('admins')->user()->user_type_id;
            $user = $request->get('user_id');
            //dd($user);
            if(!empty($user) && $auth_id == 1 && is_array($user))
            {
                $user_id = $request->get('user_id');
                $task_date = $request->get('task_date');
                $count = count($project_id);
                
                for($i=0; $i<$count; $i++)
                {
                    $obj = new Task();
                    $obj->user_id = $user_id[$i];
                    $obj->project_id = $project_id[$i];
                    $obj->title = $title[$i];
                    $obj->description = $description[$i];
                    $obj->hour = $hour[$i];
                    $obj->min = $min[$i];
                    $obj->total_time = $hour[$i] + $min[$i];
                    $obj->status = $statuss[$i];
                    $obj->ref_link = $ref_link[$i];
                    if(!empty($task_date[$i]))
                    {
                        $task_dates = $task_date[$i]; 
                    }
                    else{
                        $task_dates =  date("Y-m-d h:i:sa");
                    }

                    $obj->task_date = $task_dates;
                    $obj->save();

                    $id = $obj->id;
                    
                    //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_TASKS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Task::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params);
            
                }
            }
            else{
                $user_id = \Auth::guard('admins')->id();
                $task_date = date("Y-m-d h:i:sa");

                $count = count($project_id);
                for($i=0; $i<$count; $i++)
                {
                    $obj = new Task();
                    $obj->user_id = $user_id;
                    $obj->project_id = $project_id[$i];
                    $obj->title = $title[$i];
                    $obj->description = $description[$i];
                    $obj->hour = $hour[$i];
                    $obj->min = $min[$i];
                    $obj->total_time = $hour[$i] + $min[$i];
                    $obj->status = $statuss[$i];
                    $obj->ref_link = $ref_link[$i];
                    $obj->task_date = $task_date;
                    $obj->save();

                    $id = $obj->id;
                    
                    //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_TASKS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Task::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params);
                }
            }

            
 
            
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_TASKS);
        
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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_TASKS);
        
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
            'title' => 'required',            
            'hour.*' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24])],
            'min.*' => ['required', Rule::in([0.00,0.25,0.50,0.75])],
            'status.*' => ['required', Rule::in([0,1])]
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
            $hour = $request->get('hour');
            $min = $request->get('min');
            $hour_count = count($hour);
            for ($i=0; $i <$hour_count; $i++) { 
                if($hour[$i] == 0 && $min[$i] == 0.00)
                    $hourFlag = 0;
            }
            if($hourFlag == 0){
                $status = 0;
                return ['status' => $status, 'msg' => 'please enter valid time']; 
            }

            $project_id = $request->get('project_id');
            $title = $request->get('title');
            $description = $request->get('description');
            //$hour = $request->get('hour');
            //$min = $request->get('min');
            $statuss = $request->get('status');
            $ref_link = $request->get('ref_link');

            $auth_id = \Auth::guard('admins')->user()->user_type_id;
            $user = $request->get('user_id');

            if(!empty($user) && $auth_id == 1 && is_array($user))
            {
                $user_id = $request->get('user_id');
                $task_date = $request->get('task_date');
                $count = count($project_id);

                $model->user_id = $user_id[0];
                $model->project_id = $project_id[0];
                $model->title = $title[0];
                $model->description = $description[0];
                $model->hour = $hour[0];
                $model->min = $min[0];
                $model->total_time = $hour[0] + $min[0];
                $model->status = $statuss[0];
                $model->ref_link = $ref_link[0];
                if(!empty($task_date[0]))
                    $task_date = $task_date[0];
                else
                    $task_date =  date("Y-m-d h:i:sa");

                $model->task_date = $task_date;
                $model->update();                
            }else
            {
                $task_date = date("Y-m-d h:i:sa");
                
                $model->user_id = \Auth::guard('admins')->id();
                $model->project_id = $project_id[0];
                $model->title = $title[0];
                $model->description = $description[0];
                $model->hour = $hour[0];
                $model->min = $min[0];
                $model->total_time = $hour[0] + $min[0]; 
                $model->status = $statuss[0];
                $model->ref_link = $ref_link[0];
                $model->task_date = $task_date;
                $model->update();
            }
            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_TASKS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Tasks::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_TASKS);
        
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
                    $params['actionid']     = $this->adminAction->DELETE_TASKS;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Task::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");
		
		$hours_query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");

        $hours_query = Task::listFilter($hours_query);        

        $totalHours = $hours_query->sum("total_time");
        $totalHours = number_format($totalHours,2);

        $data = \Datatables::eloquent($model)        
               
            ->addColumn('action', function(Task $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),                                                  
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_TASKS),
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),
                                                  
                    ]
                )->render();
            })
            ->editColumn('status', function ($row) { 
                if ($row->status == 1){
                    $html = "<a class='btn btn-xs btn-success'>Completed</a><br/>";
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
                else{
                    $html ='<a class="btn btn-xs btn-danger">In Progress</a><br/>';
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
            })
            ->editColumn('task_date', function($row){
                if(!empty($row->task_date))          
                    return date("j M, Y",strtotime($row->task_date)).'<br/><span style="color: blue; font-size: 12px">'.date("j M, Y",strtotime($row->created_at))."</span>";
                else
                    return '-';    
            })
            ->editColumn('ref_link', function($row){
                $html='';

                if(!empty($row->ref_link))
                {
                  $label = strlen($row->ref_link) > 15 ? substr($row->ref_link,0,15)."...":$row->ref_link; 
                  $html = "<a href='".$row->ref_link."' target='_blank'>".$label."</a>";  
                }
                return $html;  
            })            
            ->rawColumns(['status','action','ref_link','description','task_date'])             
          
            ->filter(function ($query) 
            {                              
                $query = Task::listFilter($query);                  
            });
		$data = $data->with('hours',$totalHours);
        $data = $data->make(true);
		return $data;
    }

    public function viewData(Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $id = $request->get('task_id');

        if(!empty($id)){
            
            $task = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_TASK.".id",$id)
                ->get();
                //dd($task);
        }
        return view("admin.tasks.viewData", ['views'=>$task]);
    }

    public function userData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id();

        $model = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_TASK.".user_id",$auth_id);

		$hours_query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_TASK.".user_id",$auth_id);

	    $hours_query = Task::listFilter($hours_query);        
        $totalHours = $hours_query->sum("total_time");
        $totalHours = number_format($totalHours,2);

        $data = \Datatables::eloquent($model)        
               
            ->editColumn('status', function ($row) {
                    if ($row->status == 1)
                        return "<a class='btn btn-xs btn-success'>Completed</a>";
                    else
                        return '<a class="btn btn-xs btn-danger">In Progress</a>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 1){
                    $html = "<a class='btn btn-xs btn-success'>Completed</a><br/>";
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
                else{
                    $html ='<a class="btn btn-xs btn-danger">In Progress</a><br/>';
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
            })
            ->addColumn('action', function(Task $row) {
                return view("admin.tasks.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),                                                  
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_TASKS),                                                  					'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),
                    ]
                )->render();
            })
            ->editColumn('task_date', function($row){
                
                if(!empty($row->task_date))          
                    return date("j M, Y",strtotime($row->task_date));
                else
                    return '-';    
            })
            ->editColumn('ref_link', function($row){
                $html='';

                if(!empty($row->ref_link))
                {
                  $label = strlen($row->ref_link) > 15 ? substr($row->ref_link,0,15)."...":$row->ref_link; 
                  $html = "<a href='".$row->ref_link."' target='_blank'>".$label."</a>";  
                }
                return $html;  
            }) 
            
            ->rawColumns(['status','ref_link','action'])             
            
            ->filter(function ($query) 
            {                              
                $query = Task::listFilter($query);                  
            });
			$data = $data->with('hours',$totalHours);
            $data = $data->make(true);
            return $data;
    }
	public function clientData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
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

        $model = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_PROJECT.".client_id",$client_type);

        $hours_query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_PROJECT.".client_id",$client_type);

        $hours_query = Task::listFilter($hours_query);
        $totalHours = $hours_query->sum("total_time");
        $totalHours = number_format($totalHours,2);

        $data = \Datatables::eloquent($model)
               
            ->editColumn('status', function ($row) {
                    if ($row->status == 1)
                        return "<a class='btn btn-xs btn-success'>Completed</a>";
                    else
                        return '<a class="btn btn-xs btn-danger">In Progress</a>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 1){
                    $html = "<a class='btn btn-xs btn-success'>Completed</a><br/>";
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
                else{
                    $html ='<a class="btn btn-xs btn-danger">In Progress</a><br/>';
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
            })
            ->addColumn('action', function(Task $row) {
                return view("admin.tasks.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => 0,
                        'isDelete' =>0,
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_TASKS),
                    ]
                )->render();
            })
            ->editColumn('task_date', function($row){
                
                if(!empty($row->task_date))
                    return date("j M, Y",strtotime($row->task_date));
                else
                    return '-';    
            })
            ->editColumn('ref_link', function($row){
                $html='';

                if(!empty($row->ref_link))
                {
                  $label = strlen($row->ref_link) > 15 ? substr($row->ref_link,0,15)."...":$row->ref_link; 
                  $html = "<a href='".$row->ref_link."' target='_blank'>".$label."</a>";  
                }
                return $html;  
            }) 
            ->rawColumns(['status','ref_link','action','task_date'])
            
            ->filter(function ($query) 
            {
                $query = Task::listFilter($query);
            });
            $data = $data->with('hours',$totalHours);

            $data = $data->make(true);

            return $data;        
    }
	
	public function getMonthlyReport(Request $request)
    {
		$checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DOWNLOAD_MONTHLY_REPORT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();
        $data['users'] = User::getList();
        $data['clients'] = Client::pluck("name","id")->all();
        $data['months'] = ['1017-10'=>'OCTOBER - 2017','2017-11'=>'NOVEMBER - 2017','2017-12'=>'DECEMBER - 2017','2018-01'=>'JANUARY - 2018','2018-02'=>'FEBRUARY - 2018','2018-03'=>'MARCH - 2018','2018-04'=>'APRIL - 2018','2018-05'=>'MAY - 2018','2018-06'=>'JUNE - 2018','2018-07'=>'JULY - 2018','2018-08'=>'AUGUST - 2018','2018-09'=>'SEPTEMBER - 2018','2018-10'=>'OCTOBER - 2018','2018-11'=>'NOVEMBER - 2018','2018-12'=>'DECEMBER - 2018'];
            
        return view('admin.tasks.DownloadMonthlyReport',$data);
    }

    public function PreviewMonthlyReport(Request $request)
    {
        $data = array();
        $user_id = $request->get('user_id');
        $client_id = $request->get('client_id');
        $months = $request->get('months');

        $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->join(TBL_CLIENT,TBL_PROJECT.".client_id","=",TBL_CLIENT.".id")
                ->where(TBL_TASK.'.user_id',$user_id)
                ->where(TBL_CLIENT.'.id',$client_id)
                ->where(TBL_TASK.'.task_date','LIKE','%'.$months.'%');
        $query = $query->get();
        $total = $query->sum('total_time');

        $user = User::find($user_id);
        $fullname = $user->firstname.' '.$user->lastname;
        
        $data['reports'] = $query;
        $data['total'] = $total;
        $data['months'] = $months;
        $data['fullname'] = $fullname;
        return view('admin.tasks.previewReport',$data); 
    }

    public function DownloadMonthlyReport(Request $request)
    {       
        $status = 1;
        $msg = "Downloaded Monthly Report Successfully!";
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:'.TBL_USERS.',id',
            'client_id' => 'required|exists:'.TBL_CLIENT.',id',
            'months' => 'required',
        ]);
        if ($validator->fails())
        {
            return back()->withInput()->withErrors($validator->errors());
        } 
        else{
            $user_id = request()->get('user_id');
            $client_id = request()->get('client_id');
            $months = request()->get('months');

                $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                        ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                        ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                        ->join(TBL_CLIENT,TBL_PROJECT.".client_id","=",TBL_CLIENT.".id")
                        ->where(TBL_TASK.'.user_id',$user_id)
                        ->where(TBL_CLIENT.'.id',$client_id)
                        ->where(TBL_TASK.'.task_date','LIKE','%'.$months.'%');
                $query = $query->get();
                $total = $query->sum('total_time');

                $data['reports'] = $query;
                $data['total'] = $total;

                $user = User::find($user_id);
                $fullname = $user->firstname.'_'.$user->lastname;
                $file_name = $months.'_'.$fullname.'_Billind_hours_'.$total;     

                //$path = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'MonthlyReports';

                $records[] = array("No","Project","Task","Date","Hours","Status","Reference Link");
                $i = 1;
                foreach($query as $row)
                {
                    if($row->status == 1) $sts = "Completed"; else $sts = "In Progress";
                    $task_date = date("j M, Y",strtotime($row->task_date));
                    $records[] = [$i,$row->project_name,$row->title,$task_date,$row->total_time,$sts,$row->ref_link];
                $i++;
                }

                header("Content-type: text/csv; charset=utf-8");
                header("Content-Disposition: attachment; filename=".$file_name.".csv");
                //header('Pragma: no-cache');
                //header("Expires: 0");
                
                $records[] = array("Total","","","",$total,"","");
                $fp = fopen('php://output', 'w');                
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
                //$fp = fopen($file_name.'.csv', 'w');
                    foreach ($records as $fields) {
                        fputcsv($fp, $fields);
                    }

                fclose($fp);                
                $path = public_path().'/'.$file_name.'.csv';
                //downloadFile($file_name.'.csv',$path);
                exit;                
                // return redirect('/download-monthly-reports');
        }
        return redirect('/download-monthly-reports');
    }
}
