<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Datatables;
use App\modells\AdminLog;
use App\Models\EmailSentLog;
use App\Models\AdminAction;

class EmailSentController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "sent-email";
        $this->moduleViewName = "admin.sent-email";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Email Sent Log";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new EmailSentLog();  

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
    public function index()
    {        
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_EMAIL_SENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Manage Sent Email"; 

        $data['add_url'] = route($this->moduleRouteText.'.create');
        return view($this->moduleViewName.".index", $data);         
    }

    /**
     * Show the form for creating a new resource.   
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\m_responsekeys(conn, identifier)
     */
    public function store(Request $request)
    {
              
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $idate(format)
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
               
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function viewEmailData($id)
    { 
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$VIEW_EMAIL_SENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        if($id)
        {
            $emailSent = EmailSentLog::find($id);

            if($emailSent)
            {
                echo $emailSent->email_body;
            }
            else
            {
                echo ("NO ID Found !");
            }
        }

    }   

    public function destroy($id,Request $request)
    {
       //        
    }


    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_EMAIL_SENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = EmailSentLog::query();

        return Datatables::eloquent($model)
               
            ->addColumn('action', function(EmailSentLog $row) {
                return view("admin.sent-email.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                
                        'btnView' => \App\Models\Admin::isAccess(\App\Models\Admin::$VIEW_EMAIL_SENT),                               
                    ]
                )->render();
            })                
            ->filter(function ($query) 
            {
                $search_start_date = request()->get("search_start_date");                                      
                $search_end_date = request()->get("search_end_date");                                      
                $search_id = request()->get("search_id");                                      
                $search_formemail = request()->get("search_formemail");                                      
                $search_toemail = request()->get("search_toemail");                                      
                $search_ccemail = request()->get("search_ccemail");                                      
                $search_sub = request()->get("search_sub");                                      

                if (!empty($search_start_date)) {

                    $from_date = $search_start_date . ' 00:00:00';
                    $convertFromDate = $from_date;

                    $query = $query->where(TBL_EMAIL_SENT_LOG . ".created_at", ">=", addslashes($convertFromDate));
                }
                if (!empty($search_end_date)) {

                    $to_date = $search_end_date . ' 23:59:59';
                    $convertToDate = $to_date;

                    $query = $query->where(TBL_EMAIL_SENT_LOG . ".created_at", "<=", addslashes($convertToDate));
                }
                if(!empty($search_id))
                {
                    $idArr = explode(',', $search_id);
                    $idArr = array_filter($idArr);                
                    if(count($idArr)>0)
                    {
                        $query = $query->whereIn(TBL_EMAIL_SENT_LOG.".id",$idArr);
                    } 
                }
                if(!empty($search_formemail))
                {
                    $query = $query->where(TBL_EMAIL_SENT_LOG.".from_email", 'LIKE', '%'.$search_formemail.'%');
                }
                if(!empty($search_toemail))
                {
                    $query = $query->where(TBL_EMAIL_SENT_LOG.".to_email", 'LIKE', '%'.$search_toemail.'%');
                }
                if(!empty($search_ccemail))
                {
                    $query = $query->where(TBL_EMAIL_SENT_LOG.".cc_emails", 'LIKE', '%'.$search_ccemail.'%');
                }
                if(!empty($search_sub))
                {
                    $query = $query->where(TBL_EMAIL_SENT_LOG.".email_subject", 'LIKE', '%'.$search_sub.'%');
                }
            })
            ->make(true);       
    } 

       
}
