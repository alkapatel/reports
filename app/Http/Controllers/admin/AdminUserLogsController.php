<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Datatables;
use App\Models\AdminLog;
use App\Models\AdminAction;

class AdminUserLogsController extends Controller {

    public function __construct() {

        $this->moduleRouteText = "user-logs";
        $this->moduleViewName = "admin.AdminUserLoges";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Admin Log";
        $this->module = $module;  

        $this->modelObj = new AdminLog();  

        $this->adminAction= new AdminAction;        

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ADMIN_LOG_ACTIONS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "List Admin User Logs";       
       
        $data['users'] = \App\Models\Admin::pluck("name","id")->all();
        $data['userAction'] = \App\Models\AdminAction::pluck("description","id")->all();
     
        return view($this->moduleViewName.".index", $data);         
    }

    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ADMIN_LOG_ACTIONS);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        
        $model = AdminLog::select(TBL_ADMIN_LOG.".*",TBL_USERS.".name",TBL_ADMIN_ACTION.".description")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_ADMIN_LOG.".adminuserid")
                ->join(TBL_ADMIN_ACTION,TBL_ADMIN_ACTION.".id","=",TBL_ADMIN_LOG.".actionid"); 
               
        return Datatables::eloquent($model)

            ->editColumn('actionid', function($row){
                return "# ".$row->description."<br /># ".$row->actionvalue;  
            })
            
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))                    
                    
            return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->rawColumns(['actionid'])                    
            ->filter(function ($query) 
            {
                $search_start_date = trim(request()->get("search_start_date"));                    
                $search_end_date = trim(request()->get("search_end_date"));
                $search_adminuserid = request()->get("search_adminuserid");
                $search_actionid = request()->get("search_actionid");
                $search_actionvalue = request()->get("search_actionvalue");                    
                $search_remark = request()->get("search_remark");                                   
                $search_ipaddress = request()->get("search_ipaddress");                          

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_ADMIN_LOG.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_ADMIN_LOG.".created_at","<=",addslashes($convertToDate));
                }

                if(!empty($search_adminuserid))
                {
                    $query = $query->where('adminuserid', $search_adminuserid);
                }

                if(!empty($search_actionid))
                {
                    $query = $query->where('actionid', $search_actionid);
                }

                if(!empty($search_actionvalue))
                {
                    $query = $query->where('actionvalue', $search_actionvalue);
                }

                if(!empty($search_remark))
                {
                    $query = $query->where(TBL_ADMIN_LOG.".remark", 'LIKE', '%'.$search_remark.'%');
                }

                if(!empty($search_ipaddress))
                {
                    $query = $query->where('ipaddress', 'LIKE', '%'.$search_ipaddress.'%');
                }      
            })
            ->make(true);        
    }
    public function logout_index()
    {    
        $data = array();        
        $data['page_title'] = "List Admin User Logs";       
       
        $data['users'] = \App\Models\Admin::pluck("firstname","id")->all();
        $data['userAction'] = \App\Models\AdminAction::pluck("description","id")->all();
     
        return view($this->moduleViewName.".logout_index", $data);         
    }
    public function logout_data(Request $request)
    {
        
        $model = AdminLog::select(TBL_ADMIN_LOG.".*",TBL_USERS.".name",TBL_ADMIN_ACTION.".description")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_ADMIN_LOG.".adminuserid")
                ->join(TBL_ADMIN_ACTION,TBL_ADMIN_ACTION.".id","=",TBL_ADMIN_LOG.".actionid"); 
               
        return Datatables::eloquent($model)

            ->editColumn('actionid', function($row){
                return "# ".$row->description."<br /># ".$row->actionvalue;  
            })
            
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))                    
                    
            return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->rawColumns(['actionid'])                    
            ->filter(function ($query) 
            {
                $search_start_date = trim(request()->get("search_start_date"));                    
                $search_end_date = trim(request()->get("search_end_date"));
                $search_adminuserid = request()->get("search_adminuserid");
                $search_actionid = request()->get("search_actionid");
                $search_actionvalue = request()->get("search_actionvalue");                    
                $search_remark = request()->get("search_remark");                                   
                $search_ipaddress = request()->get("search_ipaddress");                          

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_ADMIN_LOG.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_ADMIN_LOG.".created_at","<=",addslashes($convertToDate));
                }

                if(!empty($search_adminuserid))
                {
                    $query = $query->where('adminuserid', $search_adminuserid);
                }

                if(!empty($search_actionid))
                {
                    $query = $query->where('actionid', $search_actionid);
                }

                if(!empty($search_actionvalue))
                {
                    $query = $query->where('actionvalue', $search_actionvalue);
                }

                if(!empty($search_remark))
                {
                    $query = $query->where(TBL_ADMIN_LOG.".remark", 'LIKE', '%'.$search_remark.'%');
                }

                if(!empty($search_ipaddress))
                {
                    $query = $query->where('ipaddress', 'LIKE', '%'.$search_ipaddress.'%');
                }      
            })
            ->make(true);        
    } 
}
