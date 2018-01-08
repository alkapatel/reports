<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "expense";
        $this->moduleViewName = "admin.expense";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Expense";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Expense();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();
        $data['page_title'] = "Expense Details";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_EXPENSE);
        
       return view($this->moduleViewName.".index", $data);
      
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_EXPENSE);
        
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
        $data["editMode"] = 1; 

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        } 
        $status = 1;
        $msg = $this->addMsg;
        $data = array();

        $title = request()->get('title');
        $date = request()->get('date');
        $amount = request()->get('amount');
        $scanned_bill = request()->file('scanned_bill');
        $description_bill = request()->get('description_bill');

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',            
            'date' => 'required',            
            'amount' => 'required',            
            'scanned_bill' => 'image|max:4000',            
            'description_bill' => 'min:2',
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
            $image_record= new Expense(); 

            if(!empty($scanned_bill)){

                $destinationPath = public_path().'/uploads/expense/';
                $bill_images=$scanned_bill->getClientOriginalName();
                $extension =$scanned_bill->getClientOriginalExtension();
                $bill_images=md5($bill_images);
                $image_name= $bill_images.'.'.$extension;

                $file =$scanned_bill->move($destinationPath,$image_name);
                $image_record->scanned_bill=$image_name;                                  
            } 
                $image_record->title=$title;            
                $image_record->date=$date;            
                $image_record->amount=$amount;            
                $image_record->description_bill=$description_bill;            
                $image_record->save();

                $id = $image_record->id;
               //store logs detail
                $params=array();    
                                        
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_EXPENSE ;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Expense::".$id;
                                        
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
    public function edit($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_EXPENSE);
        
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array(); 

        $title = request()->get('title');
        $date = request()->get('date');
        $amount = request()->get('amount');
        $scanned_bill = request()->file('scanned_bill');
        $description_bill = request()->get('description_bill'); 

        $Validator=\Validator::make($request->all(),
            [   
                'title' => 'required|min:2',            
            'date' => 'required',            
            'amount' => 'required',            
            'scanned_bill' => 'image|max:4000',            
            'description_bill' => 'min:2',
            ]);

         // check validations
        if(!$model)
        {
            $status = 0;
            $msg = "Record not found !";
        }
        else
        {

            $image_record = Expense::find($id);
            if($request->hasFile('scanned_bill'))
            {
                if(!empty($scanned_bill)){
                    $destinationPath = public_path().'/uploads/expense/';
                    $bill_images=$scanned_bill->getClientOriginalName();
                    $extension =$scanned_bill->getClientOriginalExtension();
                    $bill_images=md5($bill_images);
                    $image_name= $bill_images.'.'.$extension;
                    $file =$scanned_bill->move($destinationPath,$image_name);
                    $image_record->scanned_bill=$image_name;
                }
            }                      
            $image_record->title=$title;
            $image_record->date=$date;
            $image_record->amount=$amount;
            
            $image_record->description_bill=$description_bill;
            $image_record->save();

            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_EXPENSE ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Expense::".$id;
                                    
            $logs=\App\Models\AdminLog::writeadminlog($params);
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_EXPENSE);
        
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
                $url = public_path().'/uploads/expense/'.$modelObj->scanned_bill;
                if(!empty($url)){
                    unlink($url);
                }

                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->DELETE_EXPENSE;
                $params['actionvalue']  = $id;
                $params['remark']       = "Delete Expense::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = Expense::query();

        $amount_query = Expense::query();

        $amount_query = Expense::listFilter($amount_query);

        $totalAmount = $amount_query->sum("amount");
        $totalAmount = number_format($totalAmount,2);

        $data = \Datatables::eloquent($model)

            ->addColumn('scanned_bill', function (Expense $data) {
                $path = asset("themes/admin/assets/expense/".$data->scanned_bill);
                return '<img src="'.$path.'" class="img-responsive" style="width:100px; height:50px" />';
            })         
               
            ->addColumn('action', function(Expense $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_EXPENSE),                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_EXPENSE),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_EXPENSE),                                                         
                    ]
                )->render();
            })

            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['scanned_bill','action']) 

            ->filter(function ($query) 
            {                                                    
                $query = Expense::listFilter($query); 
            });

            $data = $data->with('amount',$totalAmount);

            $data = $data->make(true);

            return $data; 
    }
    public function viewData(Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $id = $request->get('expense_id');

        if(!empty($id)){
             $expense = Expense::select(TBL_EXPENSES.".*")
                ->where(TBL_EXPENSES.".id",$id)
                ->get();

        }
        return view("admin.expense.viewData", ['views'=>$expense]);
    }

    public function downloadFile($id,Request $request)
    {
        $obj = Expense::find($id);
        
        if($obj)
        {
            $id = $obj->id;
            $destinationPath = public_path().'/uploads/expense/'.$obj->scanned_bill;

            downloadFile($obj->scanned_bill,$destinationPath);
            exit;
        }
        else
        {
            abort(404);
        }
    }
    
}
