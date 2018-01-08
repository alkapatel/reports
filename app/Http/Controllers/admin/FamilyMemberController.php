<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Datatables;
use App\Models\AdminAction;
use App\Models\Member;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FamilyMemberController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "members-family";
        $this->moduleViewName = "admin.family_members";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Family Member";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new FamilyMember();  

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
        $data['page_title'] = "Manage Family Members Details";
        $data['members'] = Member::getMembers();
        $data['add_url'] = route($this->moduleRouteText.'.create');
        $btnAdd =0;
        $auth_user = \Auth::guard("admins")->check();
            if($auth_user)
            {
                $auth_id = \Auth::guard("admins")->user()->id;
                if($auth_id == SUPER_ADMIN_ID){
					$btnAdd = 1;
				}
                
            }
            $member_id = session('member_id');
            if(!empty($member_id)){
                $btnAdd = 1;
            }
        $data['btnAdd'] = $btnAdd;
        
       return view($this->moduleViewName.".index", $data); 
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $createAdd = 0;
        $auth_user = \Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = \Auth::guard("admins")->user()->id;
            if($auth_id == SUPER_ADMIN_ID)
            $createAdd = 1;
        }
        $member_id = session('member_id');
        if(!empty($member_id)){
            $createAdd = 1;
        }
        if($createAdd == 1){

            $data = array();
            $data['formObj'] = $this->modelObj;
            $data['page_title'] = "Add ".$this->module;
            $data['action_url'] = $this->moduleRouteText.".store";
            $data['action_params'] = 0;
            $data['buttonText'] = "Save";
            $data["method"] = "POST";
            $data['members'] = Member::getMembers();
            $data['blood_groups'] = Member::getBloodGroups();

            return view($this->moduleViewName.'.add', $data);
        }
        if($createAdd == 0)
        {
            return redirect('/members/otpform');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = 1;
        $true = 0;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'relation_with_primary_member' => 'required|min:2',
            'occupation' => 'required|min:2',
            'image' => 'image|max:4000',
            'member_id'=>'exists:'.TBL_MEMBER.',id',
            'blood_group_id'=>'exists:'.TBL_BLOOD_GROUP.',id',  
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
            $family = new FamilyMember();
            
            $member_id = session('member_id');
            
            $auth_user = \Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = \Auth::guard("admins")->user()->id;
                    if($auth_id == SUPER_ADMIN_ID)
                
                $member_id = $request->get('member_id');
                }
            $name = $request->get('name');
            $relation = $request->get('relation_with_primary_member');
            $blood_group_id = $request->get('blood_group_id');
            $occupation = $request->get('occupation');
             
            $image = $request->file('image');

            $family->member_id = $member_id;
            $family->name = $name;
            $family->relation_with_primary_member = $relation;
            $family->blood_group_id = $blood_group_id;
            $family->occupation = $occupation;
            $family->save();
            $family_id = $family->id;
            
            if(!empty($image)){
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'members'.DIRECTORY_SEPARATOR.$member_id.DIRECTORY_SEPARATOR.'family_members'.DIRECTORY_SEPARATOR.$family_id;

                   $image_name =$image->getClientOriginalName();
                   $extension =$image->getClientOriginalExtension();
                   $image_name=md5($image_name);
                   $profile_image= $image_name.'.'.$extension;
                   $file =$image->move($destinationPath,$profile_image);

                $family->image = $profile_image;
                $family->save();
            }
			//store logs detail
            $auth_id = NULL;
            $auth_user = Auth::guard("admins")->check();
            if($auth_user)
            {
                $auth_id = Auth::guard("admins")->user()->id;
            }

            $member_id = NULL;
            $member = session('member_id');
            if(!empty($member))
            {
                $member_id = $member;
            }

            $params=array();

            $params['user_id']      = $auth_id;
            $params['member_id']    = $member_id;
            $params['actionid']     = $this->adminAction->ADD_FAMILY_MEMBER;
            $params['actionvalue']  = $family_id;
            $params['remark']       = "Add Family Member::".$family_id;
                                    
            $logs= \App\Models\MemberLog::writeadminlog($params);
        }
            session()->flash('success_message', $msg);
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
        $formObj = $this->modelObj->find($id);
        
        if(!$formObj)
        {
            abort(404);
        }
        $EditId = 0;
        $auth_user = \Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = \Auth::guard("admins")->user()->id;
            if($auth_id == SUPER_ADMIN_ID)
            $EditId = 1;
        }
        $member_id = session('member_id');
        if(!empty($member_id) && $formObj->member_id == $member_id){
            $EditId = 1;
        }   
        if($EditId == 1){

            $data = array();
            $data['formObj'] = $formObj;
            $data['page_title'] = "Edit ".$this->module;
            $data['buttonText'] = "Update";
            $data['action_url'] = $this->moduleRouteText.".update";
            $data['action_params'] = $formObj->id;
            $data['method'] = "PUT"; 
            $data['members'] = Member::getMembers();
            $data['blood_groups'] = Member::getBloodGroups();

            return view($this->moduleViewName.'.add', $data);
        }
        if($EditId == 0){
            return redirect('/members/otpform');
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
        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();        
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'relation_with_primary_member' => 'required|min:2',
            'occupation' => 'required|min:2',
            'image' => 'image|max:4000',
            'member_id'=>'exists:'.TBL_MEMBER.',id',
            'blood_group_id'=>'exists:'.TBL_BLOOD_GROUP.',id',  
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
            $member_id = session('member_id');
            $auth_user = \Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = \Auth::guard("admins")->user()->id;
                    if($auth_id == SUPER_ADMIN_ID)
                
                $member_id = $request->get('member_id');
                }
            $name = $request->get('name');
            $relation = $request->get('relation_with_primary_member');
            $blood_group_id = $request->get('blood_group_id');
            $occupation = $request->get('occupation');
             
            $image = $request->file('image');

            if(!empty($image)){
                $old_image = $model->image;
                if(!empty($old_image))
                {
                    $url = public_path().'/uploads/members/'.$model->member_id.'/family_members/'.$model->id.'/'.$old_image;
                    if(!empty($url))
                    {
                        if (is_file($url)) {
                            unlink($url);
                        }
                    }
                }
                    $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'members'.DIRECTORY_SEPARATOR.$member_id.DIRECTORY_SEPARATOR.'family_members'.DIRECTORY_SEPARATOR.$id;

                   $image_name =$image->getClientOriginalName();
                   $extension =$image->getClientOriginalExtension();
                   $image_name=md5($image_name);
                   $profile_image= $image_name.'.'.$extension;
                   $file =$image->move($destinationPath,$profile_image);

                $model->image = $profile_image;
            }
            $model->member_id = $member_id;
            $model->name = $name;
            $model->relation_with_primary_member = $relation;
            $model->blood_group_id = $blood_group_id;
            $model->occupation = $occupation;
            $model->save();
			
			//store logs detail
            $auth_id = NULL;
            $auth_user = Auth::guard("admins")->check();
            if($auth_user)
            {
                $auth_id = Auth::guard("admins")->user()->id;
            }

            $member_id = NULL;
            $member = session('member_id');
            if(!empty($member))
            {
                $member_id = $member;
            }

            $params=array();

            $params['user_id']      = $auth_id;
            $params['member_id']    = $member_id;
            $params['actionid']     = $this->adminAction->EDIT_FAMILY_MEMBER;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Family Member::".$id;
                                    
            $logs= \App\Models\MemberLog::writeadminlog($params);
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
        
		$auth_id =NULL;
        $DeleteId = 0;
        $auth_user = \Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = \Auth::guard("admins")->user()->id;
            if($auth_id == SUPER_ADMIN_ID)
            $DeleteId = 1;
        }
        $member_id = session('member_id');
        if(!empty($member_id) && $modelObj->member_id == $member_id){
            $DeleteId = 1;
        }

        if($modelObj && $DeleteId == 1) 
        {
            try 
            {
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
				
				//store logs detail

                $params=array();

                $params['user_id']      = $auth_id;
                $params['member_id']    = $member_id;
                $params['actionid']     = $this->adminAction->DELETE_FAMILY_MEMBER;
                $params['actionvalue']  = $id;
                $params['remark']       = "Delete Family Member::".$id;
                                        
                $logs= \App\Models\MemberLog::writeadminlog($params);
				
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
        if($DeleteId == 0){
            return redirect('/members/otpform');
        }
    }

    public function data(Request $request)
    {
        $model = FamilyMember::selectRaw(TBL_FAMILY_MEMBER.".*".",CONCAT(bopal_members.firstname,' ',bopal_members.middlename,' ',bopal_members.lastname) as member_name")
                ->join(TBL_MEMBER,TBL_MEMBER.".id","=",TBL_FAMILY_MEMBER.".member_id");

        return Datatables::eloquent($model)
               
            ->addColumn('action', function(FamilyMember $row) {
                $delete = 0;
                $auth_user = \Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = \Auth::guard("admins")->user()->id;
                    if($auth_id == SUPER_ADMIN_ID)
                        $delete = 1;
                }
                else if(session('member_id') == $row->member_id)
                {
                    $delete = 1;   
                }
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEdit' =>1,
                        'isDelete' => $delete,
                    ]
                )->render();
            })
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action'])
            ->filter(function ($query) 
            {                                                    
                $search_start_date = request()->get("search_start_date");     
                $search_end_date = request()->get("search_end_date");  
                $search_id = request()->get("search_id");
                $search_name = request()->get("search_name");
                $search_member = request()->get("search_member");    
                $search_blood = request()->get("search_blood");  
                $search_occupation = request()->get("search_occupation");
                $search_relation = request()->get("search_relation");
                 
                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_FAMILY_MEMBER.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_FAMILY_MEMBER.".created_at","<=",addslashes($convertToDate));
                }
                if(!empty($search_id))
                {
                    $idArr = explode(',', $search_id);
                    $idArr = array_filter($idArr);                
                    if(count($idArr)>0)
                    {
                        $query = $query->whereIn(TBL_FAMILY_MEMBER.".id",$idArr);
                    } 
                }
                if(!empty($search_name))
                {
                   $query = $query->where(TBL_FAMILY_MEMBER.".name", 'LIKE', '%'.$search_name.'%');
                }
                if(!empty($search_member))
                {
                    $query = $query->where(TBL_FAMILY_MEMBER.".member_id",$search_member);
                }
                if(!empty($search_occupation))
                {
                    $query = $query->where(TBL_FAMILY_MEMBER.".occupation", 'LIKE', '%'.$search_occupation.'%');
                }
                if(!empty($search_relation))
                {
                    $query = $query->where(TBL_FAMILY_MEMBER.".relation_with_primary_member", 'LIKE', '%'.$search_relation.'%');
                }
            })
            ->make(true);  
    }
}
