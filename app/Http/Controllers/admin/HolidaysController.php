<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator;
use App\Models\AdminAction;
use App\Models\HolidayDetail;
use App\Models\Holiday;
use App\Models\User;

class HolidaysController extends Controller {

    public function __construct() {

        $this->moduleRouteText = "holidays";
        $this->moduleViewName = "admin.holidays";
        $this->list_url = route($this->moduleRouteText . ".index");

        $module = "Holiday";
        $this->module = $module;

        $this->adminAction = new AdminAction;

        $this->modelObj = new Holiday();

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
    public function index(Request $request) {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_HOLIDAYS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Manage Holiday";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_TASKS);
        $data['projects'] = \App\Models\Project::getList();
     
        return view($this->moduleViewName.".index", $data);         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_HOLIDAYS);
        
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

        return view($this->moduleViewName.'.add', $data);
    }

    /** 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_HOLIDAYS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();

        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in([0, 1])],
            'from_date' => 'required',
            'to_date' => 'required',
            'holiday_title' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();

            $status = 0;
            $msg = "";

            foreach ($messages->all() as $message) {
                $msg .= $message . "<br />";
            }
        } else {

            $obj = $this->modelObj;
            $holiday_title = $request->get('holiday_title');
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');

            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));

            $begin = new \DateTime($from_date);
            $end = new \DateTime($to_date);
            $end = $end->modify('+1 day');

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $dates = [];
            foreach ($period as $d) {
                $dt = $d->format('Y-m-d');
                if (!in_array($dt, $dates)) {
                    $dates[] = $dt;
                }
            }
            
            $obj->from_date = $request->get('from_date');
            $obj->holiday_title = $request->get('holiday_title');
            $obj->status = $request->get('status');

            if (!empty($to_date)) {
                $obj->to_date = $to_date;
            }
            $obj->save();
            $holiday_id = $obj->id;

            if (is_array($dates)) {
                foreach ($dates as $date) {
                    $detail = new HolidayDetail();

                    $detail->holiday_id = $holiday_id;
                    $detail->date = $date;

                    $detail->save();
                }
            }

            $id = $obj->id;
           
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_HOLIDAYS ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Holiday ::".$id;
                                    
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
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request) {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_HOLIDAYS);
        
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

        return view($this->moduleViewName.'.add', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_HOLIDAYS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();

        $validator = Validator::make($request->all(), [
                    'status' => ['required', Rule::in([0, 1])],
                    'from_date' => 'required',
                    'to_date' => 'required',
                    'holiday_title' => 'required|min:5',
        ]);

        // check validations
        if (!$model) {
            $status = 0;
            $msg = "Record not found !";
        } else if ($validator->fails()) {
            $messages = $validator->messages();

            $status = 0;
            $msg = "";

            foreach ($messages->all() as $message) {
                $msg .= $message . "<br />";
            }
        } else {

            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');

            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));

            $begin = new \DateTime($from_date);
            $end = new \DateTime($to_date);
            $end = $end->modify('+1 day');

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $dates = [];
            foreach ($period as $d) {
                $dt = $d->format('Y-m-d');
                if (!in_array($dt, $dates)) {
                    $dates[] = $dt;
                }
            }
            
            $model->from_date = $request->get('from_date');
            $model->holiday_title = $request->get('holiday_title');
            $model->status = $request->get('status');
            
            if (!empty($to_date)) {
                $model->to_date = $to_date;
            }
            $model->save();
            $holiday_id = $model->id;

            $table = TBL_HOLIDAYS_DETAILS;

            // delete old records
            \DB::table($table)->where('holiday_id', $id)->delete();

            if (is_array($dates)) {
                foreach ($dates as $date) {
                    $detail = new HolidayDetail();

                    $detail->holiday_id = $holiday_id;
                    $detail->date = $date;

                    $detail->save();

                }
            }
                //store logs detail
                $params=array();    
                                        
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_HOLIDAYS ;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Holiday ::".$id;
                                        
                $logs=\App\Models\AdminLog::writeadminlog($params);
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request) {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_HOLIDAYS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

       $modelObj = $this->modelObj->find($id);

        if ($modelObj) {
            try {
                $holiData = HolidayDetail::where('holiday_id', $id);
                $holiData->delete();
                
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg);

                //store logs detail
                    $params=array();    
                                            
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_HOLIDAYS;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Holiday ::".$id;
                                            
                    $logs=\App\Models\AdminLog::writeadminlog($params);

                return redirect($backUrl);
            } catch (Exception $e) {
                session()->flash('error_message', $this->deleteErrorMsg);
                return redirect($this->list_url);
            }
        } else {
            session()->flash('error_message', "Record not exists");
            return redirect($this->list_url);
        }
    }

    public function data(Request $request) {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_HOLIDAYS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = Holiday::query();

        return \Datatables::eloquent($model)
                        ->addColumn('action', function(Holiday $row) {
                            return view("admin.partials.action", [
                                        'currentRoute' => $this->moduleRouteText,
                                        'row' => $row,
                                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_HOLIDAYS),
                                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_HOLIDAYS),
                                            ]
                                    )->render();
                        })
                        
                        ->editColumn('created_at', function($row) {

                            if (!empty($row->created_at))
                                return date("j M, Y h:i:s A", strtotime($row->created_at));
                            else
                                return '-';
                        })
                         ->editColumn('status', function ($row) {
                            if ($row->status == 1){
                                $html = "<a class='btn btn-xs btn-success'>Active</a><br/>";
                                return $html;
                            }
                            else{
                                $html ='<a class="btn btn-xs btn-danger">In Active</a><br/>';
                                return $html;
                            }
                        })
                        
                       ->rawColumns(['created_at', 'action', 'status', 'to_date'])
                        ->filter(function ($query) {
                            $search_start_date = request()->get("search_start_date");
                            $search_end_date = request()->get("search_end_date");
                            $search_id = request()->get("search_id");
                            $search_title = request()->get("search_title");
                            $search_start_leave = request()->get("search_start_leave");
                            $search_end_leave = request()->get("search_end_leave");
                            $search_status = request()->get("search_status");

                            if (!empty($search_start_date)) {

                                $from_date = $search_start_date . ' 00:00:00';
                                $convertFromDate = $from_date;

                                $query = $query->where(TBL_HOLIDAYS . ".created_at", ">=", addslashes($convertFromDate));
                            }
                            if (!empty($search_end_date)) {

                                $to_date = $search_end_date . ' 23:59:59';
                                $convertToDate = $to_date;

                                $query = $query->where(TBL_HOLIDAYS . ".created_at", "<=", addslashes($convertToDate));
                            }
                            if(!empty($search_id))
                            {
                                $idArr = explode(',', $search_id);
                                $idArr = array_filter($idArr);                
                                if(count($idArr)>0)
                                {
                                    $query = $query->whereIn(TBL_HOLIDAYS.".id",$idArr);
                                } 
                            }
                            if(!empty($search_title))
                            {
                                $query = $query->where(TBL_HOLIDAYS.".holiday_title", 'LIKE', '%'.$search_title.'%');
                            }
                            if (!empty($search_start_leave) || !empty($search_end_leave)) {
                                $query = $query->whereBetween(TBL_HOLIDAYS . '.from_date', [$search_start_leave, $search_end_leave])
                                        ->whereBetween(TBL_HOLIDAYS . '.to_date', [$search_start_leave, $search_end_leave]);
                            }
                            if ($search_status == "1" || $search_status == "0") {
                                $query = $query->where(TBL_HOLIDAYS . ".status", $search_status);
                            }

                        })
                        ->make(true);
    }
}

    