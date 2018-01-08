<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use App\Models\MemberLog;
use App\Models\AdminAction;
use App\Models\Member;

class MemberLogsController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "member-logs";
        $this->moduleViewName = "admin.member_logs";
        $this->list_url = route($this->moduleRouteText.".index");

        $this->modelObj = new MemberLog();  

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MEMBER_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "List Member User Logs";
        $data['members'] = Member::getMembers();
        $data['users'] = \App\Models\Admin::pluck("name","id")->all();
        $data['userAction'] = \App\Models\AdminAction::pluck("description","id")->all();
     
        return view($this->moduleViewName.".index", $data);
    }
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MEMBER_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = MemberLog::select(TBL_MEMBER_LOG.".*", \DB::raw("CONCAT(bopal_members.firstname,' ',bopal_members.middlename,' ',bopal_members.lastname) as member_name"), TBL_USERS.".name as username",TBL_ADMIN_ACTION.".description as description")
                ->join(TBL_ADMIN_ACTION,TBL_ADMIN_ACTION.".id","=",TBL_MEMBER_LOG.".actionid") 
                ->leftJoin(TBL_USERS,TBL_USERS.".id","=",TBL_MEMBER_LOG.".user_id")
                ->leftJoin(TBL_MEMBER,TBL_MEMBER.".id","=",TBL_MEMBER_LOG.".member_id");
               
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
                $search_user = request()->get("search_user");
                $search_member = request()->get("search_member");
                $search_actionid = request()->get("search_actionid");
                $search_actionvalue = request()->get("search_actionvalue");                    
                $search_remark = request()->get("search_remark");                                   
                $search_ipaddress = request()->get("search_ipaddress");                          

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_MEMBER_LOG.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_MEMBER_LOG.".created_at","<=",addslashes($convertToDate));
                }

                if(!empty($search_user))
                {
                    $query = $query->where(TBL_MEMBER_LOG.'.user_id', $search_user);
                }
                if(!empty($search_member))
                {
                    $query = $query->where(TBL_MEMBER_LOG.'.member_id', $search_member);
                }

                if(!empty($search_actionid))
                {
                    $query = $query->where(TBL_MEMBER_LOG.'.actionid', $search_actionid);
                }

                if(!empty($search_actionvalue))
                {
                    $query = $query->where(TBL_MEMBER_LOG.'.actionvalue', $search_actionvalue);
                }

                if(!empty($search_remark))
                {
                    $query = $query->where(TBL_MEMBER_LOG.".remark", 'LIKE', '%'.$search_remark.'%');
                }

                if(!empty($search_ipaddress))
                {
                    $query = $query->where(TBL_MEMBER_LOG.'.ipaddress', 'LIKE', '%'.$search_ipaddress.'%');
                }      
            })
            ->make(true);        
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
}
