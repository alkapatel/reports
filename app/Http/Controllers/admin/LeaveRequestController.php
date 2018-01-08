<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator;
use App\Models\AdminAction;
use App\Models\LeaveRequest;
use App\Models\LeaveDetail;
use App\Models\User;

class LeaveRequestController extends Controller {

    public function __construct() {

        $this->moduleRouteText = "leave-request";
        $this->moduleViewName = "admin.leave_request";
        $this->list_url = route($this->moduleRouteText . ".index");

        $module = "Leave Request";
        $this->module = $module;

        $this->adminAction = new AdminAction;

        $this->modelObj = new LeaveRequest();

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
    public function index(Request $request) {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $data = array();
        $data['page_title'] = "Manage Leave Requests";

        $auth_id = \Auth::guard('admins')->user()->user_type_id;

        if ($auth_id == NORMAL_USER) {
            $data['users'] = '';
            $data['add_url'] = route($this->moduleRouteText . '.userCreate');
        } else {
            $data['users'] = User::getList();
			$data['months'] = ['2017-10'=>'Oct - 2017','2017-11'=>'Nov - 2017','2017-12'=>'Dec - 2017','2018-01'=>'Jan - 2018','2018-02'=>'Feb - 2018','2018-03'=>'Mar - 2018','2018-04'=>'Apr - 2018','2018-05'=>'May - 2018','2018-06'=>'Jun - 2018','2018-07'=>'Jul - 2018','2018-08'=>'Aug - 2018','2018-09'=>'Sep - 2018','2018-10'=>'Oct - 2018','2018-11'=>'Nov - 2018','2018-12'=>'Dec - 2018',];
            $data['add_url'] = route($this->moduleRouteText . '.create');
        }

        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_LEAVE_REPORT);

        if ($auth_id == NORMAL_USER) {
            $viewName = $this->moduleViewName . ".userIndex";
        } else {

            $is_download = $request->get("isDownload");

            if ($is_download == 1) {

                $list_params['is_download'] = 1;
                $list_params = array(
                    'search_start_date' => $request->get('search_start_date'),
                    'search_end_date' => $request->get('search_end_date'),
                    'search_user' => $request->get('search_user'),
                    'search_status' => $request->get('search_status'),
                    'search_start_date' => $request->get('search_start_date'),
                    'search_end_date' => $request->get('search_end_date')
                );
                $rows = LeaveRequest::getLeaveList($list_params);
                //dd($rows);
                unset($list_params['is_download']);

                $records[] = array("ID", "UserName", "FromDate", "ToDate", "Description", "Status", "CreatedAt");

                foreach ($rows as $row) {
                    $records[] = [$row->id, $row->username, $row->from_date, $row->to_date, $row->description, $row->status, $row->created_at];
                }

                $filename = "LeaveRequestList_" . date("YmdHis");

                \Excel::create($filename, function($excel) use($records) {
                    $excel->sheet('Sheetname', function($sheet) use($records) {
                        $sheet->fromArray($records, null, 'A1', false, false);
                    });
                })->export('csv');
            }

            $viewName = $this->moduleViewName . ".index";
        }

        return view($viewName, $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $auth_id = \Auth::guard('admins')->user()->user_type_id;

        if ($auth_id == NORMAL_USER) {
            return redirect('leave-request/leave-create');
        }

        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add " . $this->module;
        $data['action_url'] = $this->moduleRouteText . ".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";
        $data["users"] = \App\Models\User::getList();
        $data["is_half_first"] = null;
        $data["is_half_last"] = null;
        $data["leave_sataus"] = 1;

        return view($this->moduleViewName . '.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:' . TBL_USERS . ',id',
            'status' => ['required', Rule::in([0, 1, 2])],
            'from_date' => 'required',
            'from_date_leave' => ['required', Rule::in([1, 0])],
            'to_date_leave' => Rule::in([1, 0]),
            'description' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();

            $status = 0;
            $msg = "";

            foreach ($messages->all() as $message) {
                $msg .= $message . "<br />";
            }
        } else {
            $obj = $this->modelObj;
            $description = $request->get('description');
            $from_date_leave = $request->get('from_date_leave');
            $to_date_leave = $request->get('to_date_leave');

            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');

            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));

            if ($from_date > $to_date) {
                $status = 0;
                $msg = "Please enater valid date";
                return ['status' => $status, 'msg' => $msg, 'data' => $data];
            }

            $begin = new \DateTime($from_date);
            $end = new \DateTime($to_date);
            $end = $end->modify('+1 day');

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $dates = [];
            foreach ($period as $d) {
                $dt = $d->format('Y-m-d');
                if (!in_array($dt, $dates)) {
                    $dates[] = $dt;
                }
            }

            $obj->user_id = $request->get('user_id');
            $obj->from_date = $request->get('from_date');
            $obj->description = $request->get('description');
            $obj->status = $request->get('status');
            $obj->created_by = \Auth::guard('admins')->id();

            if (!empty($to_date)) {
                $obj->to_date = $to_date;
            }
            $obj->save();
            $leave_id = $obj->id;

            if (is_array($dates)) {
                foreach ($dates as $date) {
                    $detail = new LeaveDetail();

                    $detail->leave_id = $leave_id;
                    $detail->date = $date;

                    if ($date == $from_date)
                        $detail->is_half = $from_date_leave;
                    else if ($date == $to_date)
                        $detail->is_half = $to_date_leave;
                    else
                        $detail->is_half = 0;

                    $detail->save();
                }
            }

            $id = $obj->id;
            $user_id = $obj->user_id;

            $user_detail = User::find($user_id);

            // send leave request email to Admin
            $from_half = ($from_date_leave == 1 ? "( Half )" : "");
            $to_half = ($to_date_leave == 1 ? "( Half )" : "");

            if ($from_date == $to_date)
            {                
                $subject = "Leave Request $from_date ";
            } 
            else 
            {
                $subject = "Leave Request $from_date To $to_date";
            }
            $Path = public_path()."leave-request?search_start_leave=&search_end_leave=&search_status=all&search_start_date=&search_end_date=&search_user=&search_id=".$leave_id."&isDownload=";

            $message = array();             
            $message['firstname'] = $user_detail->firstname;
            $message['lastname'] = $user_detail->lastname;
            $message['from_date'] = $from_date;
            $message['to_date'] = $to_date;
            $message['from_half'] = $from_half;
            $message['to_half'] = $to_half;
            $message['description'] = $description;
            $message['link'] = $Path;

            $returnHTML = view('emails.leave_request_temp',$message)->render();
//            return $returnHTML;exit;           
            
            $emails = \App\Models\User::getAdminEmails();
            
            $toEmail = "jitendra.rathod@phpdots.com";
            /*if(isset($emails[0]))
            {
                $toEmail = $emails[0];
                unset($emails[0]);
            }*/
			$empName = ucfirst($user_detail->firstname)." ".ucfirst($user_detail->lastname);
			
            $params["to"] = $toEmail;
            $params["ccEmails"] = $emails;
            $params["from"] = $user_detail->email;
			$params["from_name"] = $empName;
            $params["subject"] = $subject;
            $params["body"] = $returnHTML;
            sendHtmlMail($params);

            //store logs detail
            $params = array();

            $params['adminuserid'] = \Auth::guard('admins')->id();
            $params['actionid'] = $this->adminAction->ADD_LEAVE_REQUEST;
            $params['actionvalue'] = $id;
            $params['remark'] = "Add Leave Request::" . $id;

            $logs = \App\Models\AdminLog::writeadminlog($params);

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
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request) {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $formObj = $this->modelObj->find($id);

        if (!$formObj) {
            abort(404);
        }
        $data = array();
        $data['formObj'] = $formObj;
        $data['page_title'] = "Edit " . $this->module;
        $data['buttonText'] = "Update";

        $data['action_url'] = $this->moduleRouteText . ".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT";
        $data["leave_sataus"] = 1;
        $data["users"] = \App\Models\User::pluck('name', 'id')->all();

        $leave_detail = \App\Models\LeaveDetail::where('leave_id', $id)->get();
        $data["is_half_first"] = $leave_detail[0]->is_half;

        $last = count($leave_detail) - 1;
        $data["is_half_last"] = $leave_detail[$last]->is_half;

        return view($this->moduleViewName . '.add', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required|exists:' . TBL_USERS . ',id',
                    'status' => ['required', Rule::in([0, 1, 2])],
                    'from_date' => 'required',
                    'from_date_leave' => ['required', Rule::in([1, 0])],
                    'to_date_leave' => Rule::in([1, 0]),
                    'description' => 'required|min:5',
        ]);

        // check validations
        if (!$model) {
            $status = 0;
            $msg = "Record not found !";
        } else if ($validator->fails()) {
            $messages = $validator->messages();

            $status = 0;
            $msg = "";

            foreach ($messages->all() as $message) {
                $msg .= $message . "<br />";
            }
        } else {

            $from_date_leave = $request->get('from_date_leave');
            $to_date_leave = $request->get('to_date_leave');

            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');

            if ($from_date > $to_date) {
                $status = 0;
                $msg = "Please enater valid date";
                return ['status' => $status, 'msg' => $msg, 'data' => $data];
            }

            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));

            $begin = new \DateTime($from_date);
            $end = new \DateTime($to_date);
            $end = $end->modify('+1 day');

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $dates = [];
            foreach ($period as $d) {
                $dt = $d->format('Y-m-d');
                if (!in_array($dt, $dates)) {
                    $dates[] = $dt;
                }
            }

            $model->user_id = $request->get('user_id');
            $model->from_date = $request->get('from_date');
            $model->description = $request->get('description');
            $model->status = $request->get('status');
            $model->created_by = \Auth::guard('admins')->id();

            if (!empty($to_date)) {
                $model->to_date = $to_date;
            }
            $model->save();
            $leave_id = $model->id;

            $table = TBL_LEAVE_DETAIL;

            // delete old records
            \DB::table($table)->where('leave_id', $id)->delete();

            if (is_array($dates)) {
                foreach ($dates as $date) {
                    $detail = new LeaveDetail();

                    $detail->leave_id = $leave_id;
                    $detail->date = $date;

                    if ($date == $from_date)
                        $detail->is_half = $from_date_leave;
                    else if ($date == $to_date)
                        $detail->is_half = $to_date_leave;
                    else
                        $detail->is_half = 0;

                    $detail->save();
                }
            }

            //store logs detail
            $params = array();

            $params['adminuserid'] = \Auth::guard('admins')->id();
            $params['actionid'] = $this->adminAction->EDIT_LEAVE_REQUEST;
            $params['actionvalue'] = $id;
            $params['remark'] = "Edit Leave Request::" . $id;

            $logs = \App\Models\AdminLog::writeadminlog($params);
        }

        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request) {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }
        $modelObj = $this->modelObj->find($id);

        if ($modelObj) {
            try {
                $leaveData = LeaveDetail::where('leave_id', $id);
                $leaveData->delete();
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg);

                //store logs detail
                $params = array();

                $params['adminuserid'] = \Auth::guard('admins')->id();
                $params['actionid'] = $this->adminAction->DELETE_LEAVE_REQUEST;
                $params['actionvalue'] = $id;
                $params['remark'] = "Delete Leave Request::" . $id;

                $logs = \App\Models\AdminLog::writeadminlog($params);

                return redirect($backUrl);
            } catch (Exception $e) {
                session()->flash('error_message', $this->deleteErrorMsg);
                return redirect($this->list_url);
            }
        } else {
            session()->flash('error_message', "Record not exists");
            return redirect($this->list_url);
        }
    }

    public function data(Request $request) {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $model = LeaveRequest::select(TBL_LEAVE_REQUEST . ".*", TBL_USERS . ".name as username")
                ->join(TBL_USERS, TBL_USERS . ".id", "=", TBL_LEAVE_REQUEST . ".user_id");


        return \Datatables::eloquent($model)
            ->addColumn('action', function(LeaveRequest $row) {
                return view("admin.partials.action", [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_LEAVE_REPORT),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_LEAVE_REPORT),
                        'isAccept' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_LEAVE_REPORT),
                        'isReject' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_LEAVE_REPORT),
                    ]
                )->render();
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 1)
                    return "<a class='btn btn-xs btn-success'>Accepted</a>";
                else if ($row->status == 0)
                    return '<a class="btn btn-xs btn-warning">Pending</a>';
                else if ($row->status == 2)
                    return '<a class="btn btn-xs btn-danger">Rejected</a>';
            })
            ->editColumn('created_at', function($row) {

                if (!empty($row->created_at))
                    return date("j M, Y h:i:s A", strtotime($row->created_at));
                else
                    return '-';
            })
            ->editColumn('username', function($row) {
                $username = $row->username;
                $mainID = $row->id;

        $query = LeaveRequest::select(TBL_LEAVE_REQUEST . ".*", TBL_USERS . ".name as createdname")
                ->join(TBL_USERS, TBL_USERS . ".id", "=", TBL_LEAVE_REQUEST . ".created_by")->where(TBL_LEAVE_REQUEST.'.id',$mainID)->first();

                if ($query) {
                    $username.= "<br/><i style='color: blue; font-size: 10px'>".$query->createdname."</i>";
                }

                return $username; 
            })
            ->editColumn('from_date', function($row) {

                $halfVal = "<a class='btn btn-outline btn-xs green'>Full</a>";
                $sDate = date("Y-m-d", strtotime($row->from_date));
                $mainID = $row->id;

                $query = LeaveDetail::where("leave_id", $mainID)
                        ->whereRaw("date_format(date,'%Y-%m-%d') = '" . $sDate . "'")
                        ->first();

                if ($query && $query->is_half == 1) {
                    $halfVal = "<a class='btn btn-outline btn-xs blue'>Half</a>";
                }

                return '' . date("j M, Y", strtotime($row->from_date)) . "<br/>" . $halfVal;

            })
            ->editColumn('days', function($row) {
                $halfVal = "";
                $from_date = date("Y-m-d", strtotime($row->from_date));
                $to_date = date("Y-m-d", strtotime($row->to_date));
                $mainID = $row->id;

                $query = LeaveDetail::where("leave_id", $mainID)
                        ->whereBetween('date', [$from_date, $to_date])
                        ->get();

                $days = 0;
                foreach ($query as $q) {
                    if($q->is_half == 1)                        
                        $day =0.5;
                    else
                        $day =1;
                $days +=$day;
                }
                return $days;
            })
            ->editColumn('to_date', function($row) {

                $halfVal = "<a class='btn btn-outline btn-xs green'>Full</a>";
                $sDate = date("Y-m-d", strtotime($row->to_date));
                $mainID = $row->id;

                $query = LeaveDetail::where("leave_id", $mainID)
                        ->whereRaw("date_format(date,'%Y-%m-%d') = '" . $sDate . "'")
                        ->first();

                if ($query && $query->is_half == 1) {
                    $halfVal = "<a class='btn btn-outline btn-xs blue'>Half</a>";
                }

                return '' . date("j M, Y", strtotime($row->to_date)) . "<br/>" . $halfVal;
            })->rawColumns(['status', 'action', 'from_date', 'to_date','username'])

            ->filter(function ($query) {
                $search_user = request()->get("search_user");
                $search_status = request()->get("search_status");
                $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date");
                $search_start_leave = request()->get("search_start_leave");
                $search_end_leave = request()->get("search_end_leave");
                $search_id = request()->get("search_id");
				$search_month = request()->get("search_month");

                    if (!empty($search_start_date)) {

                        $from_date = $search_start_date . ' 00:00:00';
                        $convertFromDate = $from_date;

                        $query = $query->where(TBL_LEAVE_REQUEST . ".created_at", ">=", addslashes($convertFromDate));
                    }
                    if (!empty($search_end_date)) {

                        $to_date = $search_end_date . ' 23:59:59';
                        $convertToDate = $to_date;

                        $query = $query->where(TBL_LEAVE_REQUEST . ".created_at", "<=", addslashes($convertToDate));
                    }
                    if (!empty($search_user)) {
                        $query = $query->where(TBL_LEAVE_REQUEST . ".user_id", $search_user);
                    }
                    if ($search_status == "1" || $search_status == "0" || $search_status == "2") {
                        $query = $query->where(TBL_LEAVE_REQUEST . ".status", $search_status);
                    }
                    if (!empty($search_start_leave) || !empty($search_end_leave)) {
                        $query = $query->whereBetween(TBL_LEAVE_REQUEST . '.from_date', [$search_start_leave, $search_end_leave])
                                ->whereBetween(TBL_LEAVE_REQUEST . '.to_date', [$search_start_leave, $search_end_leave]);
                    }
					if (!empty($search_month)) {
                        $query = $query->where(TBL_LEAVE_REQUEST . ".from_date",'LIKE','%'.$search_month.'%');
                    }
                    if(!empty($search_id))
                    {
                        $idArr = explode(',', $search_id);
                        $idArr = array_filter($idArr);                
                        if(count($idArr)>0)
                        {
                            $query = $query->whereIn(TBL_LEAVE_REQUEST.".id",$idArr);
                        } 
                    }
                })
                ->make(true);
    }

    public function changeStatus(Request $request)
    {
        
        $flag = 1;
        $msg = "Status Updated Successfully";

        $id = $request->get('leave_id');   
        $reason = $request->get('reason');   
        $status = $request->get('status');   
        $LeaveRequest = \App\Models\LeaveRequest::find($id);
        
        if($LeaveRequest){
            if(!empty($reason)){
                $LeaveRequest->reject_reason = $reason;
            }
                $LeaveRequest->status = $status;
                $LeaveRequest->save();

            
            $Path = url('/')."leave-request?search_start_leave=&search_end_leave=&search_status=all&search_start_date=&search_end_date=&search_user=&search_id=".$id."&isDownload=";

            $message = array();             
            $user_detail = LeaveRequest::select(TBL_LEAVE_REQUEST.".*",TBL_USERS.".firstname as firstname",TBL_USERS.".lastname as lastname",TBL_USERS.".email as email",TBL_LEAVE_REQUEST.'.from_date as from_date',TBL_LEAVE_REQUEST.'.to_date as to_date',TBL_LEAVE_REQUEST.'.description as description',TBL_LEAVE_REQUEST.'.reject_reason as reject_reason')
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_LEAVE_REQUEST.".user_id")
                ->where(TBL_LEAVE_REQUEST.'.id',$id)
                ->first();
                

            $message['firstname'] = $user_detail->firstname;
            $message['lastname'] = $user_detail->lastname;
            $message['from_date'] = $user_detail->from_date;
            $message['to_date'] = $user_detail->to_date;
            $message['description'] = $user_detail->description;
            $message['reject_reason'] = $user_detail->reject_reason;
            $message['status'] = $status;
            $message['link'] = $Path;
                                          
            $subject = "Leave Request Status";
            
            $returnHTML = view('emails.status_leave_request_temp',$message)->render();
            //return $returnHTML;exit;
                $auth_id = \Auth::guard('admins')->user();
				$empName = ucfirst($auth_id->firstname)." ".ucfirst($auth_id->lastname);
                
				$params["to"]=$user_detail->email;
                $params["subject"] = $subject;
			    $params["from_name"] = $empName;  
				//$params["from"] = $auth_id->email;
                $params["body"] = $returnHTML;
                sendHtmlMail($params);    
        }
        return ['flag' => $flag,'msg'=>$msg];
    }

    public function userData(Request $request) {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id();

        $model = LeaveRequest::select(TBL_LEAVE_REQUEST . ".*")
                ->join(TBL_USERS, TBL_USERS . ".id", "=", TBL_LEAVE_REQUEST . ".user_id")
                ->where(TBL_LEAVE_REQUEST . ".user_id", $auth_id);

        return \Datatables::eloquent($model)
                        ->editColumn('status', function ($row) {
                            if ($row->status == 1)
                                return "<a class='btn btn-xs btn-success'>Accepted</a>";
                            else if ($row->status == 0)
                                return '<a class="btn btn-xs btn-warning">Pending</a>';
                            else if ($row->status == 2)
                                return '<a class="btn btn-xs btn-danger">Rejected</a>';
                        })
                        ->editColumn('from_date', function($row) {

                            $halfVal = "<a class='btn btn-outline btn-xs green'>Full</a>";
                            $sDate = date("Y-m-d", strtotime($row->from_date));
                            $mainID = $row->id;

                            $query = LeaveDetail::where("leave_id", $mainID)
                                    ->whereRaw("date_format(date,'%Y-%m-%d') = '" . $sDate . "'")
                                    ->first();

                            if ($query && $query->is_half == 1) {
                                $halfVal = "<a class='btn btn-outline btn-xs blue'>Half</a>";
                            }

                            return '' . date("j M, Y", strtotime($row->from_date)) . "<br/>" . $halfVal;

                        })
                        ->editColumn('to_date', function($row) {

                            $halfVal = "<a class='btn btn-outline btn-xs green'>Full</a>";
                            $sDate = date("Y-m-d", strtotime($row->to_date));
                            $mainID = $row->id;

                            $query = LeaveDetail::where("leave_id", $mainID)
                                    ->whereRaw("date_format(date,'%Y-%m-%d') = '" . $sDate . "'")
                                    ->first();

                            if ($query && $query->is_half == 1) {
                                $halfVal = "<a class='btn btn-outline btn-xs blue'>Half</a>";
                            }

                            return '' . date("j M, Y", strtotime($row->to_date)) . "<br/>" . $halfVal;
                        })
                        ->editColumn('days', function($row) {
                            $halfVal = "";
                            $from_date = date("Y-m-d", strtotime($row->from_date));
                            $to_date = date("Y-m-d", strtotime($row->to_date));
                            $mainID = $row->id;

                            $query = LeaveDetail::where("leave_id", $mainID)
                                ->whereBetween('date', [$from_date, $to_date])
                                ->get();
                                $days = 0;
                                foreach ($query as $q) {
                                    if($q->is_half == 1)                        
                                        $day =0.5;
                                    else
                                        $day =1;
                                $days +=$day;
                                }
                                return $days;
                            })
                        ->editColumn('created_at', function($row) {

                            if (!empty($row->created_at))
                                return date("j M, Y h:i:s A", strtotime($row->created_at));
                            else
                                return '-';
                        })->rawColumns(['status', 'from_date', 'to_date'])
                        ->filter(function ($query) {

                            $search_status = request()->get("search_status");
                            $search_start_date = request()->get("search_start_date");
                            $search_end_date = request()->get("search_end_date");
                            $search_start_leave = request()->get("search_start_leave");
                            $search_end_leave = request()->get("search_end_leave");


                            if (!empty($search_start_date)) {

                                $from_date = $search_start_date . ' 00:00:00';
                                $convertFromDate = $from_date;

                                $query = $query->where(TBL_LEAVE_REQUEST . ".created_at", ">=", addslashes($convertFromDate));
                            }
                            if (!empty($search_end_date)) {

                                $to_date = $search_end_date . ' 23:59:59';
                                $convertToDate = $to_date;

                                $query = $query->where(TBL_LEAVE_REQUEST . ".created_at", "<=", addslashes($convertToDate));
                            }

                            if ($search_status == "1" || $search_status == "0" || $search_status == "2") {
                                $query = $query->where(TBL_LEAVE_REQUEST . ".status", $search_status);
                            }
                            if (!empty($search_start_leave) || !empty($search_end_leave)) {
                                $query = $query->whereBetween(TBL_LEAVE_REQUEST . '.from_date', [$search_start_leave, $search_end_leave])
                                        ->whereBetween(TBL_LEAVE_REQUEST . '.to_date', [$search_start_leave, $search_end_leave]);
                            }
                        })
                        ->make(true);
    }

    public function userCreate() {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add " . $this->module;
        $data['action_url'] = $this->moduleRouteText . ".userStore";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";

        return view($this->moduleViewName . '.userAdd', $data);
    }

    public function userStore(Request $request) {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEAVE_REPORT);

        if ($checkrights) {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();

        $validator = Validator::make($request->all(), [
                    'from_date' => 'required',
                    'from_date_leave' => ['required', Rule::in([1, 0])],
                    'to_date_leave' => Rule::in([1, 0]),
                    'description' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();

            $status = 0;
            $msg = "";

            foreach ($messages->all() as $message) {
                $msg .= $message . "<br />";
            }
        } else {
            $obj = $this->modelObj;

            $from_date_leave = $request->get('from_date_leave');
            $to_date_leave = $request->get('to_date_leave');
            $description = $request->get('description');

            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');

            if ($from_date > $to_date) {
                $status = 0;
                $msg = "Please enater valid date";
                return ['status' => $status, 'msg' => $msg, 'data' => $data];
            }
            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));

            $begin = new \DateTime($from_date);
            $end = new \DateTime($to_date);
            $end = $end->modify('+1 day');

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $dates = [];
            foreach ($period as $d) {
                $dt = $d->format('Y-m-d');
                if (!in_array($dt, $dates)) {
                    $dates[] = $dt;
                }
            }

            $obj->user_id = \Auth::guard('admins')->id();
            $obj->from_date = $request->get('from_date');
            $obj->description = $request->get('description');
            $obj->status = 0;
            $obj->created_by = \Auth::guard('admins')->id();

            if (!empty($to_date)) {
                $obj->to_date = $to_date;
            }
            $obj->save();

            $leave_id = $obj->id;

            if (is_array($dates)) {
                foreach ($dates as $date) {
                    $detail = new LeaveDetail();

                    $detail->leave_id = $leave_id;
                    $detail->date = $date;

                    if ($date == $from_date)
                        $detail->is_half = $from_date_leave;
                    else if ($date == $to_date)
                        $detail->is_half = $to_date_leave;
                    else
                        $detail->is_half = 0;

                    $detail->save();
                }
            }

            $id = $obj->id;
            $user_id = $obj->user_id;

            $user_detail = User::find($user_id);

            $from_half = ($from_date_leave == 1 ? "( Half )" : "");
            $to_half = ($to_date_leave == 1 ? "( Half )" : "");

            if ($from_date == $to_date)
            {                
                $subject = "Reports PHPdots: Leave Request $from_date ";
            } 
            else 
            {
                $subject = "Reports PHPdots: Leave Request $from_date To $to_date";
            }
            $Path = url('/')."leave-request?search_start_leave=&search_end_leave=&search_status=all&search_start_date=&search_end_date=&search_user=&search_id=".$leave_id."&isDownload=";

            $message = array();             
            $message['firstname'] = $user_detail->firstname;
            $message['lastname'] = $user_detail->lastname;
            $message['from_date'] = $from_date;
            $message['to_date'] = $to_date;
            $message['from_half'] = $from_half;
            $message['to_half'] = $to_half;
            $message['description'] = $description;
            $message['link'] = $Path;

            $returnHTML = view('emails.leave_request_temp',$message)->render();
            //return $returnHTML;exit;                       
            
            $emails = \App\Models\User::getAdminEmails();
            
            $toEmail = "";
            if(isset($emails[0]))
            {
                $toEmail = $emails[0];
                unset($emails[0]);
            }
            $params["to"] = $toEmail;
            $params["ccEmails"] = $emails;
            $params["from"] = $user_detail->email;
            $params["subject"] = $subject;
			$params["from_name"] = $user_detail->firstname." ".$user_detail->lastname;
            $params["body"] = $returnHTML;
            sendHtmlMail($params);


            //store logs detail
            $params = array();

            $params['adminuserid'] = \Auth::guard('admins')->id();
            $params['actionid'] = $this->adminAction->ADD_LEAVE_REQUEST;
            $params['actionvalue'] = $id;
            $params['remark'] = "Add Leave Request::" . $id;

            $logs = \App\Models\AdminLog::writeadminlog($params);

            session()->flash('success_message', $msg);
        }

        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

}
