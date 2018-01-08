<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\TaskReport;
use App\Models\User;

class UserTaskReportsController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "task-report";
        $this->moduleViewName = "admin.task_report";
        $this->list_url = route($this->moduleRouteText.".index");  

        $this->modelObj = new TaskReport();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_NOT_ADDED_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Not Added Task Report";
        $data['users'] = User::whereNull('client_user_id')->pluck('name','id')->all();
        return view($this->moduleViewName.".index", $data);         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_NOT_ADDED_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = TaskReport::select(TBL_TASK_REPORT.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK_REPORT.".user_id","=",TBL_USERS.".id");
        $data = \Datatables::eloquent($model)

            ->editColumn('task_date', function($row){
                
                if(!empty($row->task_date))          
                    return date("j M, Y",strtotime($row->task_date));
                else
                    return '-';    
            })
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:m:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
             
            ->rawColumns(['task_date','created_at'])
          
            ->filter(function ($query) 
            {
                $search_user = request()->get("search_user");
                $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date");

                if (!empty($search_start_date)) {

                    $from_date = $search_start_date . ' 00:00:00';
                    $convertFromDate = $from_date;

                    $query = $query->where(TBL_TASK_REPORT . ".task_date", ">=", addslashes($convertFromDate));
                }
                if (!empty($search_end_date)) {

                    $to_date = $search_end_date . ' 23:59:59';
                    $convertToDate = $to_date;

                    $query = $query->where(TBL_TASK_REPORT . ".task_date", "<=", addslashes($convertToDate));
                }
                if (!empty($search_user)) {
                    $query = $query->where(TBL_TASK_REPORT . ".user_id", $search_user);
                }
            });
            
            $data = $data->make(true);

            return $data;        
    }
}
