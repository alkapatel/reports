<?php                                                

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\EmployessDocument;
use App\Models\DocumentsType;
use App\Models\User;


class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
    
        $this->moduleRouteText = "users-documents";
        $this->moduleViewName = "admin.employess-document";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Users Document";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new EmployessDocument();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_EMP_DOCUMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Users Documents";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_EMP_DOCUMENT);
        $data['users'] = User::where('status',1)->pluck("name","id")->all();
        $data["document"] = \App\Models\DocumentsType::pluck("title","id")->all();
       return view($this->moduleViewName.".index", $data);  
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_EMP_DOCUMENT);
        
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
        $data['users'] = User::where('status',1)->pluck("name","id")->all();        
        $data["document"] = \App\Models\DocumentsType::pluck("title","id")->all();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_EMP_DOCUMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        } 

        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:'.TBL_USERS.',id',
            'doc_type_id' => 'exists:'.TBL_DOCUMENTS_TYPE.',id',  
            'filename' => 'required|max:4000',
        ]);
        
        // check validations
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
            $user_id = $request->get("user_id");
            $doc_type_id = $request->get("doc_type_id");            
            $filename = $request->file("filename");            

            $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$user_id.DIRECTORY_SEPARATOR.'docs';          
            
                   $doc_name =$filename->getClientOriginalName();              
                   $extension =$filename->getClientOriginalExtension();
                   $doc_name=md5($doc_name);
                   $file_name= $doc_name.'.'.$extension;
                   $file =$filename->move($destinationPath,$file_name);

                   $employess= new EmployessDocument();

                    $employess->filename = $file_name;                
                    $employess->user_id = $user_id;               
                    $employess->doc_type_id = $doc_type_id;
                    $employess->save();

                $id = $employess->id;
                
            
            //store logs detail 
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_EMP_DOCUMENT ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Users Documents::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_EMP_DOCUMENT);
        
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
        $data['users'] = User::where('status',1)->pluck("name","id")->all();        
        $data['document'] = \App\Models\DocumentsType::pluck("title","id")->all();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_EMP_DOCUMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        

        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:'.TBL_USERS.',id',
            'doc_type_id' => 'exists:'.TBL_DOCUMENTS_TYPE.',id',  
            'filename' => 'required',
        ]);

        // check validations 
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
            $user_id = $request->get("user_id");
            $doc_type_id = $request->get("doc_type_id");            
            $filename = $request->file("filename"); 
            
                $destinationPath = public_path().'/uploads/users/'.$user_id.'/docs/';    
            
                   $doc_name =$filename->getClientOriginalName();              
                   $extension =$filename->getClientOriginalExtension();
                   $doc_name=md5($doc_name);
                   $file_name= $doc_name.'.'.$extension;
                   $file =$filename->move($destinationPath,$file_name);

                    $model->filename = $file_name;                
                    $model->user_id = $user_id;               
                    $model->doc_type_id = $doc_type_id;
                    $model->update(); 
            
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_EMP_DOCUMENT;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Users Documents::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);                    
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_EMP_DOCUMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
       
        $user_id = $request->get("user_id");

        $employess = EmployessDocument::find($id);
        $user_id = $employess->user_id;

        $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$user_id.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$employess->filename;
       
       if(is_file($destinationPath))     
            unlink($destinationPath);
        
        $employess->delete();
        return redirect($this->list_url);
    }

    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_EMP_DOCUMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }  

        $model = EmployessDocument::select(TBL_EMPLOYESS_DOCUMENTS.".*",TBL_USERS.".name as user_name",TBL_DOCUMENTS_TYPE.".title")
                ->join(TBL_USERS,TBL_EMPLOYESS_DOCUMENTS.".user_id","=",TBL_USERS.".id")
                ->join(TBL_DOCUMENTS_TYPE,TBL_EMPLOYESS_DOCUMENTS.".doc_type_id","=",TBL_DOCUMENTS_TYPE.".id");

        return \Datatables::eloquent($model)        
               
            ->addColumn('action', function(EmployessDocument $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_EMP_DOCUMENT),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_EMP_DOCUMENT),                                                  
                    ]
                )->render();
            })  

            ->editColumn('filename', function($row){
                $url = url("users-documents/download/".$row->id);
                return "<a class='btn btn-xs btn-success' href='".$url."'>Download</a>";                                 
            })

            
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['status','action','filename'])             
            
            ->filter(function ($query) 
            {                              
                $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date");
                $search_id = request()->get("search_id");
                $search_emp_nm= request()->get("search_emp_nm");
                $search_type= request()->get("search_type");

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_EMPLOYESS_DOCUMENTS.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_EMPLOYESS_DOCUMENTS.".created_at","<=",addslashes($convertToDate));
                }

                if(!empty($search_id))
                {
                    $idArr = explode(',', $search_id);
                    $idArr = array_filter($idArr);                
                    if(count($idArr)>0)
                    {
                        $query = $query->whereIn(TBL_EMPLOYESS_DOCUMENTS.".id",$idArr);
                    } 
                }

                if(!empty($search_emp_nm))
                {
                    $query = $query->where(TBL_EMPLOYESS_DOCUMENTS.".user_id",$search_emp_nm);
                }
                if(!empty($search_type))
                {
                    $query = $query->where(TBL_EMPLOYESS_DOCUMENTS.".doc_type_id",$search_type);
                }             
            })
            ->make(true);        
    }

    public function downloadFile($id, Request $request)
    {
        $obj = EmployessDocument::find($id);
        if($obj)
        {
            $user_id = $obj->user_id;
            $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$user_id.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$obj->filename;

            downloadFile($obj->filename,$destinationPath);
            exit;
        }
        else
        {
            abort(404);
        }
    }   
}
