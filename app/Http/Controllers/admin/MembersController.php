<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Datatables;
use App\Models\AdminAction;
use App\Models\Member;
use App\Models\FamilyMember;

class MembersController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "members";
        $this->moduleViewName = "admin.members";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Member";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Member();  

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
        $data['page_title'] = "Manage Member Details";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['add_family_url'] = route('members-family.create');
        $btnAdd =0;
        $auth_user = \Auth::guard("admins")->check();
            if($auth_user)
            {
                $auth_id = \Auth::guard("admins")->user()->id;
                if($auth_id == SUPER_ADMIN_ID)
                $btnAdd = 1;
            }
            $member_id = session('member_id');
            if(!empty($member_id)){
                $btnAdd = 1;
            }
        $data['btnAdd'] = $btnAdd;
        $data['villages'] = Member::getVillages();
		$auth_user = \Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = \Auth::guard("admins")->user()->id;
            if($auth_id == SUPER_ADMIN_ID){

            if($request->get("changeID") > 0)
            {
                $member_id = $request->get("changeID");   
                $status = $request->get("changeStatus");

                $request = \App\Models\Member::find($member_id);
                    if($request)
                    {
                        $status = $request->status;

                        if($status == 0)
                            $status = 1;
                        else
                            $status = 0;
                        
                        $request->status = $status;
                        $request->save();   
						
						$auth_id = NULL;
                        $auth_user = Auth::guard("admins")->check();
                        if($auth_user)
                        {
                            $auth_id = Auth::guard("admins")->user()->id;
                        }

                        //store logs detail
                        $params=array();

                        $params['user_id']      = $auth_id;
                        $params['actionid']     = $this->adminAction->MEMBER_STATUS;
                        $params['actionvalue']  = $member_id;
                        if($status == 0)
                            $params['remark']       = "Change Member Status Active to Inactive::".$member_id;
                        else
                            $params['remark']       = "Change Member Status Inactive to Active::".$member_id;

                        $logs= \App\Models\MemberLog::writeadminlog($params);
						
                            session()->flash('success_message', "Status has been changed successfully.");
                            return redirect($this->list_url);
                    }
                    else
                    {
                        session()->flash('success_message', "Status not changed, Please try again");
                        return redirect($this->list_url);
                    }

                return redirect("/members");
            }
            }
            
        }
		
        $is_download = $request->get("isDownload");

            if (!empty($is_download) && $is_download == 1) {

                $query = Member::select(TBL_MEMBER.".*",TBL_BLOOD_GROUP.".title as blood_group",TBL_VILLAGE.".title as village")
                ->leftJoin(TBL_BLOOD_GROUP,TBL_MEMBER.".blood_group_id","=",TBL_BLOOD_GROUP.".id")
                ->leftJoin(TBL_VILLAGE,TBL_MEMBER.".village_id","=",TBL_VILLAGE.".id");

                $rows = Member::listFilter($query);

                $records[] = array("No","Form Number","Firstname","Middlename","Lastname","Mobile","Village","Blood Group","Locality","Address","Building","Profession","Organization","Industry","Family Member Count","Group Leader",'Status');            
                $i = 1;
                foreach($rows as $row)
                {
					$leader_name ='';
                    if(!empty($row->group_leader)){
                        $detail = Member::find($row->group_leader);
                        $leader_name = ucfirst($detail->firstname).' '.ucfirst($detail->middlename).' '.ucfirst($detail->lastname);
                    }
                    if($row->status == 1)
                        $status = 'Active';
                    else
                        $status = 'Inactive';
					
                    $records[] = [$i,$row->form_number,$row->firstname,$row->middlename,$row->lastname,$row->mobile,$row->village,$row->blood_group,$row->locality,$row->address,$row->building,$row->profession,$row->organization,$row->industry,$row->family_member_count,$leader_name,$status];
                $i++;
                }

                $file_name = 'MembersDetail';
                header("Content-type: text/csv; charset=utf-8");
                header("Content-Disposition: attachment; filename=".$file_name.".csv");
                
                $fp = fopen('php://output', 'w');                
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
                    foreach ($records as $fields) {
                        fputcsv($fp, $fields);
                    }

                fclose($fp);                
                $path = public_path().'/'.$file_name.'.csv';
                exit;
            }

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
        if($createAdd == 1){

            $data = array();
            $data['formObj'] = $this->modelObj;
            $data['page_title'] = "Add ".$this->module;
            $data['action_url'] = $this->moduleRouteText.".store";
            $data['action_params'] = 0;
            $data['buttonText'] = "Save";
            $data["method"] = "POST";
            $data['blood_groups'] = Member::getBloodGroups();
            $data['villages'] = Member::getVillages();
			$data['members'] = Member::getMembers();
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
            'firstname' => 'required|min:2',  
            'lastname' => 'required|min:2',
            'middlename' => 'required|min:2',
            'mobile' => 'required|numeric|unique:'.TBL_MEMBER.',mobile',
            'village_id'=>'exists:'.TBL_VILLAGE.',id',  
            'blood_group_id'=>'exists:'.TBL_BLOOD_GROUP.',id',  
            'locality' => 'min:2',  
            'address' => 'min:2',  
            'building' => 'min:2',
            'profession' => 'min:2',
            'organization' => 'min:2',
            'industry' => 'min:2',
            'family_member_count' => 'min:0',
            'image' => 'image|max:4000',
			'group_leader' => 'exists:'.TBL_MEMBER.',id',
            'status' => ['required', Rule::in([0,1])],
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
        else{

            $member = new Member();

            $form_number = $request->get('form_number');
            $firstname = $request->get('firstname');
            $middlename = $request->get('middlename');
            $lastname = $request->get('lastname');
            $village_id = $request->get('village_id');
            $address = $request->get('address');
            $building = $request->get('building');
            $locality = $request->get('locality');
            $mobile = $request->get('mobile');
            $blood_group_id = $request->get('blood_group_id');
            $profession = $request->get('profession');
            $organization = $request->get('organization');
            $family_member_count = $request->get('family_member_count');
            $industry = $request->get('industry');
			$group_leader = $request->get('group_leader');
            $statuss = $request->get('status');
            $name = ucfirst($firstname).' '.ucfirst($lastname);
            $image = $request->file('image');

            $member->form_number = $form_number;
            $member->firstname = $firstname;
            $member->middlename = $middlename;
            $member->lastname = $lastname;
            $member->address = $address;
            $member->building = $building;
            $member->locality = $locality;
            $member->mobile = $mobile;
            if(!empty($blood_group_id)){
                $member->blood_group_id = $blood_group_id;
            }
            if(!empty($village_id)){
                $member->village_id = $village_id;
            }
            $member->profession = $profession;
            $member->organization = $organization;
            $member->family_member_count = $family_member_count;
            $member->industry = $industry;
			$member->group_leader = $group_leader;
            $member->status = $statuss;
            $member->name = $name;
            $member->save();
            $member_id = $member->id;

            if(!empty($image)){
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'members'.DIRECTORY_SEPARATOR.$member_id;

                   $image_name =$image->getClientOriginalName();
                   $extension =$image->getClientOriginalExtension();
                   $image_name=md5($image_name);
                   $profile_image= $image_name.'.'.$extension;
                   $file =$image->move($destinationPath,$profile_image);

                $member->image = $profile_image;
                $member->save();
            }
			$auth_id = NULL;
            $auth_user = Auth::guard("admins")->check();
            if($auth_user)
            {
                $auth_id = Auth::guard("admins")->user()->id;
            }

            //store logs detail
            $params=array();

            $params['user_id']      = $auth_id;
            $params['actionid']     = $this->adminAction->ADD_MEMBER;
            $params['actionvalue']  = $member_id;
            $params['remark']       = "Add Member User::".$member_id;
                                    
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
    public function edit($id, Request $request)
    {
        $formObj = $this->modelObj->find($id);
        $auth_user = Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = Auth::guard("admins")->user()->id;
            if($auth_id == SUPER_ADMIN_ID)
            {
                $data = array();
                $data['formObj'] = $formObj;
                $data['page_title'] = "Edit ".$this->module;
                $data['buttonText'] = "Update";

                $data['action_url'] = $this->moduleRouteText.".update";
                $data['action_params'] = $formObj->id;
                $data['method'] = "PUT"; 
                $data['blood_groups'] = Member::getBloodGroups();
                $data['villages'] = Member::getVillages();
				$data['members'] = Member::getMembers();
				
                return view($this->moduleViewName.'.add', $data);
            }
        }

        if(!$formObj)
        {
            abort(404);
        }
        $member_id = session('member_id');
        if(empty($member_id))
        {
            return redirect('/members/otpform');
        }
        if($member_id != $id){
            return redirect('/members/otpform');
        }
        $formObj = $this->modelObj->find($id);

        $data = array();
        $data['formObj'] = $formObj;
        $data['page_title'] = "Edit ".$this->module;
        $data['buttonText'] = "Update";

        $data['action_url'] = $this->moduleRouteText.".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT"; 
        $data['blood_groups'] = Member::getBloodGroups();
        $data['villages'] = Member::getVillages();
		$data['members'] = Member::getMembers();
		
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
            'middlename' => 'required|min:2',
            'mobile' => 'required|numeric|unique:'.TBL_MEMBER.',mobile,'.$id, 
            'village_id'=>'exists:'.TBL_VILLAGE.',id',  
            'blood_group_id'=>'exists:'.TBL_BLOOD_GROUP.',id', 
            'locality' => 'min:2',  
            'address' => 'min:2',  
            'building' => 'min:2',
            'profession' => 'min:2',
            'organization' => 'min:2',
            'industry' => 'min:2',
            'family_member_count' => 'min:0',
            'image' => 'image|max:4000',
			'group_leader' => 'exists:'.TBL_MEMBER.',id',
            'status' => Rule::in([0,1]),
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
            $form_number = $request->get('form_number');
            $firstname = $request->get('firstname');
            $middlename = $request->get('middlename');
            $lastname = $request->get('lastname');
            $village_id = $request->get('village_id');
            $address = $request->get('address');
            $building = $request->get('building');
            $locality = $request->get('locality');
            $mobile = $request->get('mobile');
            $blood_group_id = $request->get('blood_group_id');
            $profession = $request->get('profession');
            $organization = $request->get('organization');
            $family_member_count = $request->get('family_member_count');
            $industry = $request->get('industry');
			$group_leader = $request->get('group_leader');
            $statuss = $request->get('status');
            $name = ucfirst($firstname).' '.ucfirst($lastname);
            $image = $request->file('image');

            if(!empty($image))
            {
                $old_image = $model->image;
                if(!empty($old_image))
                {
                    $url = public_path().'/uploads/members/'.$model->id.'/'.$old_image;
                    if(!empty($url))
                    {
                        if (is_file($url)) {
                            unlink($url);
                        }
                    }
                }
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'members'.DIRECTORY_SEPARATOR.$id;

                   $image_name =$image->getClientOriginalName();              
                   $extension =$image->getClientOriginalExtension();
                   $image_name=md5($image_name);
                   $profile_image= $image_name.'.'.$extension;
                   $file =$image->move($destinationPath,$profile_image);

                $model->image = $profile_image;
            }
            $model->form_number = $form_number;
            $model->firstname = $firstname;
            $model->middlename = $middlename;
            $model->lastname = $lastname;
            $model->address = $address;
            $model->building = $building;
            $model->locality = $locality;
            $model->mobile = $mobile;
            if(!empty($blood_group_id)){
                $model->blood_group_id = $blood_group_id;
            }
            if(!empty($village_id)){
                $model->village_id = $village_id;
            }
            $model->profession = $profession;
            $model->organization = $organization;
            $model->family_member_count = $family_member_count;
            $model->industry = $industry;
			$model->group_leader = $group_leader;
            $auth_user = \Auth::guard("admins")->check();
            if($auth_user)
            {
                $auth_id = \Auth::guard("admins")->user()->id;
                if($auth_id == SUPER_ADMIN_ID)
                    $model->status = $statuss;
            }
            $model->name = $name;
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
            $params['actionid']     = $this->adminAction->EDIT_MEMBER;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Member User::".$id;
                                    
            $logs= \App\Models\MemberLog::writeadminlog($params);
            session()->flash('success_message', $msg);
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

        $DeleteId = 0;
        $auth_user = \Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = \Auth::guard("admins")->user()->id;
            if($auth_id == SUPER_ADMIN_ID)
            $DeleteId = 1;
        }

        if($modelObj && $DeleteId == 1) 
        {
            try 
            {   
                $old_image = $modelObj->image;
                if(!empty($old_image))
                {
                    $url = public_path().'/uploads/members/'.$modelObj->id.'/'.$old_image;
                    if(!empty($url))
                    {
                        if (is_file($url)) {
                            unlink($url);
                        }
                    }
                }
                $family_mem = FamilyMember::where('member_id',$modelObj->id)->first();
				if(!empty($family_mem)){
					$old_image = $family_mem->image;
					if(!empty($old_image))
					{
						$url = public_path().'/uploads/members/'.$family_mem->member_id.'/family_members'.$family_mem->id.'/'.$old_image;
						if(!empty($url))
						{
							if (is_file($url)) {
								unlink($url);
							}
						}
					}
				}
                $family = FamilyMember::where('member_id',$modelObj->id);
                $family->delete();
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
				
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
                $params['actionid']     = $this->adminAction->DELETE_MEMBER;
                $params['actionvalue']  = $id;
                $params['remark']       = "Delete Member User::".$id;
                                        
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
    }
    public function data(Request $request)
    {
        $model = Member::select(TBL_MEMBER.".*",TBL_VILLAGE.".title as village")
                ->leftJoin(TBL_VILLAGE,TBL_VILLAGE.".id","=",TBL_MEMBER.".village_id");

        return Datatables::eloquent($model)
               
            ->addColumn('action', function(Member $row) {
                $delete = 0;
				$status_link = 0;
                $auth_user = Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = Auth::guard("admins")->user()->id;
                    if($auth_id == SUPER_ADMIN_ID)
                        $delete = 1;
						$status_link = 1;
                }
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>1,
                        'isDelete' => $delete,                                                         
                        'isShowMem' => 1,
                        'isView' => 1,
						'member_status_link' =>$status_link,
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
                $query = Member::listFilter($query);
            })
            ->make(true);  
    } 
    public function otpForm()
    {
        return view($this->moduleViewName.'.otpForm');
    }
    public function checkMobile(Request $request)
    {
        $status = 1;
        $msg = "";

        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|numeric|min:2',
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
        else{

            $mobile_no = $request->get('mobile_no');
            $row = Member::where('mobile',$mobile_no)->where('status',1)->first();
            if(empty($row))
            {
                $status = 0;
                $msg = 'Your mobile number does not exists.<br/> Update your profile, you can contact this number : <b>9825096687</b>'; 
            return ['status' => $status,'msg' => $msg];   
            }

            if(!empty($row))
            {
                $final_count = $row->otp_counter;
                if($final_count >=OTP_COUNTER)
                {
                    $status = 0;
                    $msg = 'You have send many request.<br/>Now, you can contact this number : <b> 9825096687 </b> for Update your profile.'; 
                return ['status' => $status,'msg' => $msg];
                }

                $final_count = $final_count +1;

                $row->otp_counter = $final_count;
                $row->save();

                $otp_random = getRandomNum(6);                
                $row->otp_number = $otp_random;
                $row->save();
				
            	$auth_id ='';
                $auth_user = Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = Auth::guard("admins")->user()->id;
                }
                $member_id = $row->id;
                //$message = urlencode("otp number: ".$otp_random);
                // $message = "otp number: ".$otp_random;
				$message = "$otp_random is your Bopal member data edit OTP.";				

                $sms_id = sendSms($mobile_no,$message,$member_id,$auth_id);
                 
                $status = 1;
                $msg = 'OTP Number has been sent successfully!'; 
				
				//store logs detail
                $params=array();

                $params['user_id']      = $auth_id;
                $params['member_id']    = $member_id;
                $params['actionid']     = $this->adminAction->GENERATE_OTP;
                $params['actionvalue']  = $sms_id;
                $params['remark']       = "Generate OTP::".$sms_id;

                $logs= \App\Models\MemberLog::writeadminlog($params); 
            }
        }
        return ['status' => $status,'msg' => $msg];
    }
    public function checkOtpNum( Request $request)
    {
        $status = 1;
        $msg = "";
        $url = "";
        $fullname = "";
        $number = "";

        $validator = Validator::make($request->all(), [
            'otp_no' => 'required|numeric|min:0',
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
        }else{

            $otp_no = $request->get('otp_no');
            $row = Member::where('otp_number',$otp_no)->where('status',1)->first();
            if(empty($row)){

                $status = 0;
                $msg = 'Please, Enter Valid OTP Number.'; 
            return ['status' => $status,'msg' => $msg];
            }
            else{
                if(!empty($row))
                {
                    $row->otp_status = 1;
                    $row->otp_counter = 0;
                    $row->save();

                    $fullname = ucfirst($row->firstname).' '.ucfirst($row->middlename).' '.ucfirst($row->lastname);
                    $number = $row->mobile;

                    session(['member_id' => $row->id]);
                    $status = 1;
                    $msg = 'You can update your record. <br/>please click below.';
                    $url = route('members.edit',["id" => $row->id]);
                }
            }
        }
        return ['status' => $status,'msg' => $msg,'url' => $url,'fullname' => $fullname,'number' => $number];
    }
    
    public function viewData(Request $request)
    {   
        $id = $request->get('member_id');

        if(!empty($id)){
            
            $members = Member::select(TBL_MEMBER.".*",TBL_BLOOD_GROUP.".title as blood_group",TBL_VILLAGE.".title as village")
                ->leftJoin(TBL_BLOOD_GROUP,TBL_MEMBER.".blood_group_id","=",TBL_BLOOD_GROUP.".id")
                ->leftJoin(TBL_VILLAGE,TBL_MEMBER.".village_id","=",TBL_VILLAGE.".id")
                ->where(TBL_MEMBER.".id",$id)->first();
        }
        return view("admin.members.viewData", ['view'=>$members]);
    }
    public function memberEdit($id)
    {
        $member_id = session('member_id');
        $formObj = $this->modelObj->find($id);
        
        if($member_id != $id){
            abort(404);
        }

        if(!$formObj)
        {
            abort(404);
        }   

        if($formObj->otp_status != 1)
        {
            // error message
            abort(404);
        }    

        $data = array();
        $data['formObj'] = $formObj;
        $data['page_title'] = "Edit ".$this->module;
        $data['buttonText'] = "Update";

        $data['action_url'] = $this->moduleRouteText.".memberUpdate";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT"; 
        $data['blood_groups'] = Member::getBloodGroups();
        $data['villages'] = Member::getVillages();

        if($formObj->otp_status == 1)
            return view($this->moduleViewName.'.add', $data);
        else
            abort(404);
    }
    public function memberUpdate(Request $request, $id)
    {
        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|min:2',  
            'lastname' => 'required|min:2',
            'middlename' => 'required|min:2',
            'mobile' => 'required|numeric|unique:'.TBL_MEMBER.',mobile,'.$id, 
            'village_id'=>'exists:'.TBL_VILLAGE.',id',  
            'blood_group_id'=>'exists:'.TBL_BLOOD_GROUP.',id', 
            'locality' => 'min:2',  
            'address' => 'min:2',  
            'building' => 'min:2',
            'profession' => 'min:2',
            'organization' => 'min:2',
            'industry' => 'min:2',
            'family_member_count' => 'min:0',
            'image' => 'image|max:4000',
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
            $form_number = $request->get('form_number');
            $firstname = $request->get('firstname');
            $middlename = $request->get('middlename');
            $lastname = $request->get('lastname');
            $village_id = $request->get('village_id');
            $address = $request->get('address');
            $building = $request->get('building');
            $locality = $request->get('locality');
            $mobile = $request->get('mobile');
            $blood_group_id = $request->get('blood_group_id');
            $profession = $request->get('profession');
            $organization = $request->get('organization');
            $family_member_count = $request->get('family_member_count');
            $industry = $request->get('industry');
            $name = ucfirst($firstname).' '.ucfirst($lastname);
            $image = $request->file('image');

            if(!empty($image)){
                $old_image = $model->image;
                if(!empty($old_image))
                {
                    $url = public_path().'/uploads/members/'.$model->id.'/'.$old_image;
                    if(!empty($url))
                    {
                        if (is_file($url)) {
                            unlink($url);
                        }
                    }
                }
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'members'.DIRECTORY_SEPARATOR.$id;

                   $image_name =$image->getClientOriginalName();              
                   $extension =$image->getClientOriginalExtension();
                   $image_name=md5($image_name);
                   $profile_image= $image_name.'.'.$extension;
                   $file =$image->move($destinationPath,$profile_image);

                $model->image = $profile_image;
            }
            $model->form_number = $form_number;
            $model->firstname = $firstname;
            $model->middlename = $middlename;
            $model->lastname = $lastname;
            $model->village_id = $village_id;
            $model->address = $address;
            $model->building = $building;
            $model->locality = $locality;
            $model->mobile = $mobile;
            $model->blood_group_id = $blood_group_id;
            $model->profession = $profession;
            $model->organization = $organization;
            $model->family_member_count = $family_member_count;
            $model->industry = $industry;
            $model->name = $name;
            $model->otp_status = 0;
            $model->save();
        }
        
        return ['status' => $status,'msg' => $msg, 'data' => $data];             
    }
	public function SmsForm()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$SEND_SMS_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_user = Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = Auth::guard("admins")->user()->id;
            $auth_user =  superAdmin($auth_id);
            if($auth_user == 0) 
            {
                return Redirect('/dashboard');
            }
        }

        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".send-sms";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";
        $data['members'] = Member::orderBy('firstname')->where('status',1)->get();
        return view($this->moduleViewName.'.sendSms',$data);
    }
    public function sendSms(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$SEND_SMS_FORM);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        
        $status = 1;
        $true = 0;
        $msg = 'SMS has been send successfully!';
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'sms_body' => 'required|min:5|max:160',
			'members' => 'required',
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
        else{
            $auth_id ='';
            $auth_user = Auth::guard("admins")->check();
            if($auth_user)
            {
                $auth_id = Auth::guard("admins")->user()->id;
            }
            $members = $request->get('members');
            $new_mobile = $request->get('new_mobile');
            $sms_body = $request->get('sms_body');

            if(is_array($members))
            {
                foreach($members as $mid => $phone)
                {                       
                    $member_detail = Member::where('mobile',$phone)->first();
                    $member_id = $member_detail->id;
                    $newmob = isset($new_mobile[$mid])?$new_mobile[$mid]:'';
                    
                    if(!empty($newmob))
                    {
                        $phone = $newmob;
                    }     
                    $sms_id = sendSms($phone,$sms_body,$member_id,$auth_id);
					//store logs detail
                    $params=array();

                    $params['user_id']      = $auth_id;
                    $params['member_id']    = $member_id;
                    $params['actionid']     = $this->adminAction->SEND_SMS;
                    $params['actionvalue']  = $sms_id;
                    $params['remark']       = "Send SMS::".$sms_id;

                    $logs= \App\Models\MemberLog::writeadminlog($params);
                }
            }
        }
            session()->flash('success_message', $msg);
        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }
}
