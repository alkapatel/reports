<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\AppraisalForm;
use App\Models\User;

class AppraisalFormController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "appraisal-form";
        $this->moduleViewName = "admin.appraisal_form";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Appraisal Form";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new AppraisalForm();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_APPRAISAL_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Appraisal Forms";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_APPRAISAL_FORM);
        $data['users'] = User::where('status',1)->whereNull('client_user_id')->pluck("name","id")->all();

       return view($this->moduleViewName.".index", $data);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_APPRAISAL_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_type = \Auth::guard('admins')->user()->user_type_id;
        $auth_id = \Auth::guard('admins')->user()->id;
        $user = User::find($auth_id);
        if($user->is_show_appraisal_form == 0)
        {
            $error_msg = "You are not authorised to view this page.";
            session()->flash('error_message',$error_msg);
            return redirect('dashboard');
        }

        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST"; 
        $data["past_year_rate"] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10']; 
        $data["job_satisfaction"] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10']; 
        $data['users'] = User::where('status',1)->whereNull('client_user_id')->pluck("name","id")->all();
        $appraisal = AppraisalForm::where('user_id',$auth_id)->first();
        if(!empty($appraisal))
        {
            $formObj=$appraisal; 
            if(!empty($formObj) && $formObj->user_id == $auth_id && $formObj->is_submit == 0)
            {
                $data = array();
                $data['formObj'] = $formObj;
                $data['page_title'] = "Edit ".$this->module;
                $data['buttonText'] = "Update";

                $data['action_url'] = $this->moduleRouteText.".update";
                $data['action_params'] = $formObj->id;
                $data['method'] = "PUT"; 
                $data["past_year_rate"] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10']; 
                $data["job_satisfaction"] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10'];    

                return view($this->moduleViewName.'.add', $data);
            }
            else
            {
                    $id = $appraisal->id;
                    $appraisal = AppraisalForm::find($id);
                    $auth_id = \Auth::guard('admins')->user()->id;
                    
                    if(!$appraisal)
                    {
                        abort(404);
                    }
                    if(!empty($appraisal) && $appraisal->user_id == $auth_id && $appraisal->is_submit == 1)
                    {
                        return view('admin.appraisal_form.viewData',['appraisal'=>$appraisal]);
                    }
                    if($auth_id == 1){
                        $user = User::find($appraisal->user_id);
                        $username = $user->name;
                        return view('admin.appraisal_form.view',['appraisal'=>$appraisal,'name'=>$username]);
                    }
                    else
                    {
                        abort(404);   
                    }                   
            }

        }        
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_APPRAISAL_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        $url = '';
        $validator = Validator::make($request->all(), [
            'past_year_achieved' => 'required|min:5',
            'job_satisfaction' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10])],
            'past_year_rate' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10])],
            'achievements' => 'required|min:5',
            'goal' => 'required|min:5',
            'duty_responsibility' => 'required|min:5',
            'suggestion' => 'required|min:5',
            'years' => 'required|numeric|min:0',
            'months' => 'required|numeric|min:0|max:11',
            'current_salary' => 'required|numeric|min:0',
            'expected_salary' => 'required|numeric|min:0',
            'raise' => 'required',
            'is_submit' => ['required', Rule::in([0,1])],
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
            $user_id = \Auth::guard('admins')->user()->id;
            $past_year_achieved = $request->get('past_year_achieved');
            $job_satisfaction = $request->get('job_satisfaction');
            $past_year_rate = $request->get('past_year_rate');
            $achievements = $request->get('achievements');
            $goal = $request->get('goal');
            $duty_responsibility = $request->get('duty_responsibility');
            $suggestion = $request->get('suggestion');
            $years = $request->get('years');
            $months = $request->get('months');
            $current_salary = $request->get('current_salary');
            $expected_salary = $request->get('expected_salary');
            $raise = $request->get('raise');
            $is_submit = $request->get('is_submit');

            $appraisal = new AppraisalForm();
            if($is_submit == 1){
                $submited_at = date('Y-m-d h:m:s');
                $appraisal->submited_at = $submited_at;
            }
            $appraisal->user_id = $user_id;    
            $appraisal->past_year_achieved = $past_year_achieved;    
            $appraisal->job_satisfaction = $job_satisfaction;    
            $appraisal->past_year_rate = $past_year_rate;    
            $appraisal->achievements = $achievements;    
            $appraisal->goal = $goal;    
            $appraisal->duty_responsibility = $duty_responsibility;    
            $appraisal->suggestion = $suggestion;    
            $appraisal->years = $years;    
            $appraisal->months = $months;    
            $appraisal->current_salary = $current_salary;    
            $appraisal->expected_salary = $expected_salary;    
            $appraisal->raise = $raise;    
            $appraisal->is_submit = $is_submit;
            $appraisal->save();

            $id = $appraisal->id;
            $is_submit = $appraisal->is_submit;
            if($is_submit == 0)
                $url = url('/').'/appraisal-form/'.$id.'/edit';
            else
                $url = url('/').'/appraisal-form/'.$id.'/view';

            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_APPRAISAL_FORM ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Appraisal Form::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);                    
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data,'url' => $url];              
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_APPRAISAL_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $formObj = $this->modelObj->find($id);
        $auth_id = \Auth::guard('admins')->user()->id;
        if(!$formObj)
        {
            abort(404);
        }   
        if(!empty($formObj) && $formObj->user_id == $auth_id && $formObj->is_submit == 0)
        {
            $data = array();
            $data['formObj'] = $formObj;
            $data['page_title'] = "Edit ".$this->module;
            $data['buttonText'] = "Update";

            $data['action_url'] = $this->moduleRouteText.".update";
            $data['action_params'] = $formObj->id;
            $data['method'] = "PUT"; 
            $data["past_year_rate"] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10']; 
            $data["job_satisfaction"] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10'];    

            return view($this->moduleViewName.'.add', $data);
        }
        else{
            $url = url('/').'/appraisal-form/'.$id.'/view';
            return redirect($url);
        }
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_APPRAISAL_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array(); 
        $url = '';
        $Validator=\Validator::make($request->all(),[   
            'past_year_achieved' => 'required|min:5',
            'job_satisfaction' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10])],
            'past_year_rate' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10])],
            'achievements' => 'required|min:5',
            'goal' => 'required|min:5',
            'duty_responsibility' => 'required|min:5',
            'suggestion' => 'required|min:5',
            'years' => 'required|numeric',
            'months' => 'required|numeric|min:0|max:11',
            'current_salary' => 'required|numeric',
            'expected_salary' => 'required|numeric',
            'raise' => 'required',
            'is_submit' => ['required', Rule::in([0,1])],
            ]);

         // check validations
        if(!$model)
        {
            $status = 0;
            $msg = "Record not found !";
        }
        else
        {   
            $past_year_achieved = $request->get('past_year_achieved');
            $job_satisfaction = $request->get('job_satisfaction');
            $past_year_rate = $request->get('past_year_rate');
            $achievements = $request->get('achievements');
            $goal = $request->get('goal');
            $duty_responsibility = $request->get('duty_responsibility');
            $suggestion = $request->get('suggestion');
            $years = $request->get('years');
            $months = $request->get('months');
            $current_salary = $request->get('current_salary');
            $expected_salary = $request->get('expected_salary');
            $raise = $request->get('raise');
            $is_submit = $request->get('is_submit');

            if($is_submit == 1){
                $submited_at = date('Y-m-d h:m:s');
                $model->submited_at = $submited_at;
            }   
            $model->past_year_achieved = $past_year_achieved;    
            $model->job_satisfaction = $job_satisfaction;    
            $model->past_year_rate = $past_year_rate;    
            $model->achievements = $achievements;    
            $model->goal = $goal;    
            $model->duty_responsibility = $duty_responsibility;    
            $model->suggestion = $suggestion;    
            $model->years = $years;    
            $model->months = $months;    
            $model->current_salary = $current_salary;    
            $model->expected_salary = $expected_salary;
            if(!empty($raise)){   
                $model->raise = $raise;    
            }    
            $model->is_submit = $is_submit;    
            $model->save();

            $is_submit = $model->is_submit;
            if($is_submit == 0)
                $url = url('/').'/appraisal-form/'.$id.'/edit';
            else
                $url = url('/').'/appraisal-form/'.$id.'/view';

            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_APPRAISAL_FORM;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Appraisal Form::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'url' => $url]; 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_APPRAISAL_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = AppraisalForm::select(TBL_APPRAISAL_FORM.".*",TBL_USERS.".name as username")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_APPRAISAL_FORM.".user_id");

        return \Datatables::eloquent($model)        
               
            ->addColumn('action', function(AppraisalForm $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEdit'=>0,
                        'isDelete'=>0,
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_APPRAISAL_FORM),
                    ]
                )->render();
            })
            ->editColumn('is_submit', function ($row) {
                    if ($row->is_submit == 1)
                        return "<a class='btn btn-xs btn-success'>Yes</a>";
                    else
                        return '<a class="btn btn-xs btn-danger">No</a>';
            })
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action','is_submit'])             
            
            ->filter(function ($query) 
            {                              
                $search_name = request()->get("search_name");                                
                $search_submit = request()->get("search_submit");                                         
                if(!empty($search_name))
                {
                    $query = $query->where(TBL_APPRAISAL_FORM.".user_id", $search_name);
                }
                if($search_submit == "1" || $search_submit == "0")
                {
                    $query = $query->where(TBL_APPRAISAL_FORM.".is_submit", $search_submit);
                }                   
            })
            ->make(true);        
    }
    public function viewData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$VIEW_APPRAISAL_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $id = $request->id;
        $appraisal = AppraisalForm::find($id);
        $auth_id = \Auth::guard('admins')->user()->id;
        
        if(!$appraisal)
        {
            abort(404);
        }
		
		// exit("here: ".$appraisal->user_id);
        
		if($appraisal && $appraisal->user_id == $auth_id && $appraisal->is_submit == 1)
        {
		   if(superAdmin($auth_id))
			{
				$user = User::find($appraisal->user_id);
				$username = $user->name;
				return view('admin.appraisal_form.view',['appraisal'=>$appraisal,'name'=>$username]);
			}

            return view('admin.appraisal_form.viewData',['appraisal'=>$appraisal]);
        }
		
        if(superAdmin($auth_id))
		{
            $user = User::find($appraisal->user_id);
            $username = $user->name;
            return view('admin.appraisal_form.view',['appraisal'=>$appraisal,'name'=>$username]);
        }
        else
        {
            abort(404);   
        }   
    }
}
