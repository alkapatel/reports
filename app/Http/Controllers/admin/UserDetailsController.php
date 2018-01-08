<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Datatables;
use App\Models\UserDetail;
use App\Models\UserType;
use App\Models\AdminAction;
use Mail;
class UserDetailsController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "user-details";
        $this->moduleViewName = "admin.UserDetails";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "List User Details";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new UserDetail();  

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
    public function index( Request $request)
    {
        $data = array();        
        $data['page_title'] = "Manage User Detail";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_PROJECT);
        
       return view($this->moduleViewName.".index", $data); 
    }

    /**
     * Show the form for creating a new resource.   
     * 
     * @return \Illuminate\Http\Response 
     */
    public function create()
    {
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST"; 

        return view($this->moduleViewName.'.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\m_responsekeys(conn, identifier)
     */
    public function store(Request $request)
    {
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|min:2',  
            'lastname' => 'required|min:2',  
            'phone' => 'required|numeric',  
            'whats_app_phone' => 'required|numeric',  
            'village_name' => 'required|min:2',  
            'address' => 'required|min:2',  
            'professional' => 'required|min:2',  
           
            
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
            
            session()->flash('success_message', $msg);                    
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data]; 
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
    public function edit($id, Request $request)
    {
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
    
    public function update(Request $request, $id)
    {
        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [            
            'firstname' => 'required|min:2',  
            'lastname' => 'required|min:2',  
            'phone' => 'required|numeric',  
            'whats_app_phone' => 'required|numeric',  
            'village_name' => 'required|min:2',  
            'address' => 'required|min:2',  
            'professional' => 'required|min:2',                   
            
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
        $modelObj = $this->modelObj->find($id); 

        if($modelObj) 
        {
            try 
            {             
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg);
                
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
        $model = UserDetail::query();

        return Datatables::eloquent($model)        
               
            ->addColumn('action', function(UserDetail $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>1,
                        'isDelete' => 1,                                                         
                    ]
                )->render();
            })
            ->editColumn('phone', function($row){
                return '<i class="fa fa-phone" aria-hidden="true"></i> '.$row->phone."<br/> <i class='fa fa-whatsapp' aria-hidden='true'></i> ".$row->whats_app_phone;
            })

            

            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action','phone'])             
            ->filter(function ($query) 
            {                                                    
                $search_start_date = request()->get("search_start_date");     
                $search_end_date = request()->get("search_end_date");  
                $search_id = request()->get("search_id");
                $search_firstname = request()->get("search_firstname");
                $search_lastname = request()->get("search_lastname");    
                $search_mobile = request()->get("search_mobile");  
                $search_no = request()->get("search_no");
                $search_address = request()->get("search_address");
                $search_village = request()->get("search_village");      
                $search_professional = request()->get("search_professional");

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_USER_DETAILS.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_USER_DETAILS.".created_at","<=",addslashes($convertToDate));
                }
                if(!empty($search_id))
                {
                    $idArr = explode(',', $search_id);
                    $idArr = array_filter($idArr);                
                    if(count($idArr)>0)
                    {
                        $query = $query->whereIn(TBL_USER_DETAILS.".id",$idArr);
                    } 
                }
                if(!empty($search_firstname))
                {
                   $query = $query->where(TBL_USER_DETAILS.".firstname", 'LIKE', '%'.$search_firstname.'%');
                }
                if(!empty($search_lastname))
                {
                    $query = $query->where(TBL_USER_DETAILS.".lastname", 'LIKE', '%'.$search_lastname.'%');
                }
                if(!empty($search_mobile))
                {
                    $query = $query->where(TBL_USER_DETAILS.".phone", 'LIKE', '%'.$search_mobile.'%');
                }
                if(!empty($search_no))
                {
                    $query = $query->where(TBL_USER_DETAILS.".whats_app_phone", 'LIKE', '%'.$search_no.'%');
                }
                if(!empty($search_address))
                {
                    $query = $query->where(TBL_USER_DETAILS.".address", 'LIKE', '%'.$search_address.'%');
                }
                if(!empty($search_village))
                {
                    $query = $query->where(TBL_USER_DETAILS.".village_name", 'LIKE', '%'.$search_village.'%');
                }
                if(!empty($search_professional))
                {
                    $query = $query->where(TBL_USER_DETAILS.".professional", 'LIKE', '%'.$search_professional.'%');
                }
            })
            ->make(true);  
    }        
    
}
