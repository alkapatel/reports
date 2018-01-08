<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use App\Models\SmsSentLog;
use App\Models\User;
use App\Models\Member;

class SmsSentLogController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "sms-sent-log";
        $this->moduleViewName = "admin.sms_sent_log";
        $this->list_url = route($this->moduleRouteText.".index");  

        $this->modelObj = new SmsSentLog();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SMS_SENT_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage SMS Sent Log";
        $data['members'] = Member::getMembers();
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SMS_SENT_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $model = SmsSentLog::selectRaw(TBL_SMS_SENT_LOG.".*".",CONCAT(bopal_members.firstname,' ',bopal_members.middlename,' ',bopal_members.lastname) as user_name")
                ->leftJoin(TBL_MEMBER,TBL_SMS_SENT_LOG.".member_id","=",TBL_MEMBER.".id");

        $data = \Datatables::eloquent($model)

            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:m:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->rawColumns(['created_at'])
          
            ->filter(function ($query) 
            {
                $search_mobile = request()->get("search_mobile");
                $search_member = request()->get("search_member");
                $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date");

                if (!empty($search_start_date)) {

                    $from_date = $search_start_date . ' 00:00:00';
                    $convertFromDate = $from_date;

                    $query = $query->where(TBL_SMS_SENT_LOG . ".created_at", ">=", addslashes($convertFromDate));
                }
                if (!empty($search_end_date)) {

                    $to_date = $search_end_date . ' 23:59:59';
                    $convertToDate = $to_date;

                    $query = $query->where(TBL_SMS_SENT_LOG . ".created_at", "<=", addslashes($convertToDate));
                }
                if (!empty($search_member)) {
                    $query = $query->where(TBL_SMS_SENT_LOG . ".member_id", $search_member);
                }
                if (!empty($search_mobile)) {
                    $query = $query->where(TBL_SMS_SENT_LOG . ".mobile",'LIKE' ,'%'.$search_mobile.'%');
                }
            });
            
            $data = $data->make(true);

            return $data;        
    }
}
