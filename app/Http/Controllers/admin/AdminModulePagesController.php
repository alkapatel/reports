<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Datatables;
use App\Models\AdminLog;
use App\Models\AdminAction;
use App\Models\AdminGroup;
use App\Models\AdminGroupPage;

class AdminModulePagesController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "module-pages";
        $this->moduleViewName = "admin.module_pages";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Admin Module Page";
        $this->module = $module;  

        $this->adminAction= new AdminAction;        

        $this->modelObj = new AdminGroupPage();  

        $this->addMsg = $module . " has been added successfully!";
        $this->updateMsg = $module . " has been updated successfully!";
        $this->deleteMsg = $module . " has been deleted successfully!";
        $this->deleteErrorMsg = $module . " can not deleted!";
        $this->pageGroup   =  $this->modelObj->GetPageGroup();
        $this->adminUser   =  $this->modelObj->GetAdminUser();
        
        view()->share("list_url", $this->list_url);
        view()->share("moduleRouteText", $this->moduleRouteText);
        view()->share("moduleViewName", $this->moduleViewName);
        view()->share("pageGroups", $this->pageGroup);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ADMIN_MODULES_PAGES);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Admin Module Pages";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_ADMIN_MODULES_PAGES);        
        
        return view($this->moduleViewName.".index", $data);         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ADMIN_MODULES_PAGES);
        
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
        $data['adminUserArr']   = $this->adminUser;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ADMIN_MODULES_PAGES);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
                    'admin_group_id' => 'required|exists:'.TBL_ADMIN_GROUP.',id',
                    'name' 	   => 'required|min:2',
                    'menu_title'   => 'required|min:2',
                    'menu_order'   => 'required|numeric',
                    'is_sub_menu'  => ['required', Rule::in(["Y","N"])],
                    'show_in_menu' => ['required', Rule::in(["Y","N"])]
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
            $input = $request->all();
            $obj = $this->modelObj->create($input);
            $id = $obj->id;                        
            
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_ADMIN_MODULES_PAGES ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Admin Models Page ::".$id;
                                    
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ADMIN_MODULES_PAGES);
        
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
        $data["action_show_hidde"] = 1;
        
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ADMIN_MODULES_PAGES);
        
        if($checkrights) 
        {
            return $checkrights;
        } 

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [
                    'admin_group_id' => 'required|exists:'.TBL_ADMIN_GROUP.',id',
                    'name'     => 'required|min:2',
                    'menu_title'   => 'required|min:2',
                    'menu_order'   => 'required|numeric',
                    'is_sub_menu'  => ['required', Rule::in(["Y","N"])],
                    'show_in_menu' => ['required', Rule::in(["Y","N"])]
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

            $edit_id = $request->get("id");
            if($edit_id)
            {
                $model->id = $request->get("id");
                $model->save();    
            }

            //store logs detail
            $params=array();

            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_ADMIN_MODULES_PAGES;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Admin Module Page::".$id;

            $logs=\App\Models\AdminLog::writeadminlog($params);
            
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
    public function destroy($id,Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DETELE_ADMIN_MODULES_PAGES);
        
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
                $params['actionid']     = $this->adminAction->DETELE_ADMIN_MODULES_PAGES;
                $params['actionvalue']  = $id;
                $params['remark']       = "Delete Admin Module Page::".$id;
                
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ADMIN_MODULES_PAGES);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = AdminGroupPage::select(TBL_ADMIN_GROUP_PAGE.".*",TBL_ADMIN_GROUP.".title as group_name")
                ->join(TBL_ADMIN_GROUP,TBL_ADMIN_GROUP.".id","=",TBL_ADMIN_GROUP_PAGE.".admin_group_id");

        return Datatables::eloquent($model)
               
            ->addColumn('action', function(AdminGroupPage $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ADMIN_MODULES_PAGES),
                        'isDelete' =>\App\Models\Admin::isAccess(\App\Models\Admin::$DETELE_ADMIN_MODULES_PAGES),                                                        
                    ]
                )->render();
            })
            ->filter(function ($query) {
                $search_text = request()->get("search_text");                                         
                $search_module_id = request()->get("search_module_id");                                         

                if(!empty($search_text))
                {
                    $query = $query->where('name', 'LIKE', '%'.$search_text.'%');
                }                                                       
                if(!empty($search_module_id))
                {
                    $query = $query->where('admin_group_id', $search_module_id);
                }                                                       
            })->make(true);        
    }        
}
