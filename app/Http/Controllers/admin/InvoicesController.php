<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Client;
use App\Models\ClientUser;
use PDF;

class InvoicesController extends Controller
{
     public function __construct() {

        $this->moduleRouteText = "invoices";
        $this->moduleViewName = "admin.invoices";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Invoice";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Invoice();  

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
    public function index(Request $request)
    {		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Invoices";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_INVOICE);        
    
        $auth_id = \Auth::guard('admins')->user()->user_type_id;
        if($auth_id == CLIENT_USER){
          
            $viewName = $this->moduleViewName.".clientIndex";
        }else{
			//Check Admin Type
			$auth_id = \Auth::guard("admins")->user()->id;
			$auth_user =  superAdmin($auth_id);
			if($auth_user == 0) 
			{
				return Redirect('/dashboard');
			}
			if($request->get("changeID") > 0)
            {
            $invoice_id = $request->get("changeID");   
            $payment = $request->get("changeStatus");

            $request = Invoice::find($invoice_id);
                if($request)
                {
                    $status = $request->payment;

                    if($status == 0)
                        $status = 1;
                    else
                        $status = 0;
                    

                    $request->payment = $status;
                    $request->save();            

                        session()->flash('success_message', "Payment Status has been changed successfully.");
                        return redirect($this->list_url);
                }
                else
                {
                    session()->flash('success_message', "Payment Status not changed, Please try again");
                    return redirect($this->list_url);
                }

            return redirect("invoices");
            }
        
            $viewName = $this->moduleViewName.".index";
        }
        return view($viewName, $data);        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_INVOICE);
        
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
        $data["currency"] = ['in_rs'=>'In Rs.','in_usd'=>'In USD'];
        $last_invoice = Invoice::select('invoice_no','id')->latest('id')->first();
            $this_year = date('Y');
            $next_year = $this_year + 1;
            if(!empty($last_invoice)){
                $last_no =  explode("/",$last_invoice->invoice_no);
                $no = $last_no[2] + 1;
                $invoice_no = 'PD/'.$this_year.'-'.$next_year.'/'.$no; 
            }else{
                $invoice_no = 'PD/'.$this_year.'-'.$next_year.'/1';
            }
        $data['invoice_no'] = $invoice_no;
        $data['address'] = '103, Pragtya Residency, NR: Sarthi bungalow, NR: BAPS swaminarayan Temple, Bopal, Ahmedabad';
		$data['clients'] = Client::pluck("name","id")->all();
        
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
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'address' => 'required|min:2',
            'to_address' => 'required|min:2',
            'invoice_no' => 'required|unique:'.TBL_INVOICE.',invoice_no',
            'invoice_date' => 'required',
            'sac_code' => 'required',
            'amount.*' => 'required|numeric',
            'particular.*' => 'required|min:2',
            'cgst_amount' => 'required|numeric',
            'sgst_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'require_gst' => Rule::in([1, 0]),
            'currency' => ['required',Rule::in(['in_rs','in_usd'])],
			'client_id' => 'required|exists:'.TBL_CLIENT.',id',
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
            $to_address = $request->get('to_address');
            $invoice_no = $request->get('invoice_no');
            $invoice_date = $request->get('invoice_date');
            $cgst_amount = $request->get('cgst_amount');
            $sgst_amount = $request->get('sgst_amount');
            $total_amount = $request->get('total_amount');
            $total_amount_words = $request->get('total_amount_words');
            $address = $request->get('address');
            $require_gst = $request->get('require_gst');
            $currency = $request->get('currency');
			$client_id = $request->get('client_id');
            $pan_no = 'AAUFP4850D';
            $gst_regn_no = '24AAUFP4850D1Z3';
            $bank_account_no = '201001635127';
            $bank_name = 'Induslnd BANK';
            $bank_swift_code = 'INDBINBBAHA';
            $ifsc_code = 'INDB0000232';
            
            $invoice = new Invoice();
            $invoice->to_address = $to_address;
            $invoice->invoice_no = $invoice_no;
            $invoice->invoice_date = $invoice_date;
            $invoice->cgst_amount = $cgst_amount;
            $invoice->sgst_amount = $sgst_amount;
            $invoice->total_amount = $total_amount;
            $invoice->total_amount_words = $total_amount_words;
            $invoice->address = $address;
            $invoice->pan_no = $pan_no;
            $invoice->gst_regn_no = $gst_regn_no;
            $invoice->bank_account_no = $bank_account_no;
            $invoice->bank_name = $bank_name;
            $invoice->bank_swift_code = $bank_swift_code;
            $invoice->ifsc_code = $ifsc_code;
            $invoice->require_gst = $require_gst;
            $invoice->currency = $currency;
			$invoice->client_id = $client_id;
            $invoice->save();
            $invoice_id = $invoice->id;

            $particular = $request->get('particular');
            $amount = $request->get('amount');
            $max = count($particular);
            
            for ($i=0; $i < $max; $i++) {

                $detail = new InvoiceDetail(); 
                $detail->invoice_id = $invoice_id; 
                $detail->particular = $particular[$i]; 
                $detail->amount = $amount[$i]; 
                $detail->save(); 
            }

            $id = $invoice->id;

            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_INVOICE ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Invoice ::".$id;
                                    
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
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_INVOICE);
        
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
        $data['invoice_detail'] = InvoiceDetail::where('invoice_id',$id)->get();
        $data["currency"] = ['in_rs'=>'In Rs.','in_usd'=>'In USD'];
		$data['clients'] = Client::pluck("name","id")->all();
        
        return view($this->moduleViewName.'.edit', $data);
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
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array(); 
        
        $validator = Validator::make($request->all(), [
            'address' => 'required|min:2',
            'to_address' => 'required|min:2',
            'invoice_no' => 'required|unique:'.TBL_INVOICE.',invoice_no,'.$id,
            'invoice_date' => 'required',
            'sac_code' => 'required',
            'amount.*' => 'required|numeric',
            'particular.*' => 'required|min:2',
            'cgst_amount' => 'required|numeric',
            'sgst_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'require_gst' => Rule::in([1, 0]),
            'currency' => ['required',Rule::in(['in_rs','in_usd'])],
			'client_id' => 'required|exists:'.TBL_CLIENT.',id',
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
        }else
        {
            $address = $request->get('address');
            $to_address = $request->get('to_address');
            $invoice_no = $request->get('invoice_no');
            $invoice_date = $request->get('invoice_date');
            $cgst_amount = $request->get('cgst_amount');
            $sgst_amount = $request->get('sgst_amount');
            $total_amount = $request->get('total_amount');
            $total_amount_words = $request->get('total_amount_words');
            $require_gst = $request->get('require_gst');
            $currency = $request->get('currency');
			$client_id = $request->get('client_id');
            $pan_no = 'AAUFP4850D';
            $gst_regn_no = '24AAUFP4850D1Z3';
            $bank_account_no = '201001635127';
            $bank_name = 'Induslnd BANK';
            $bank_swift_code = 'INDBINBBAHA';
            $ifsc_code = 'INDB0000232';
            
            $model->to_address = $to_address;
            $model->invoice_no = $invoice_no;
            $model->invoice_date = $invoice_date;
            $model->cgst_amount = $cgst_amount;
            $model->sgst_amount = $sgst_amount;
            $model->total_amount = $total_amount;
            $model->total_amount_words = $total_amount_words;
            $model->address = $address;
            $model->pan_no = $pan_no;
            $model->gst_regn_no = $gst_regn_no;
            $model->bank_account_no = $bank_account_no;
            $model->bank_name = $bank_name;
            $model->bank_swift_code = $bank_swift_code;
            $model->ifsc_code = $ifsc_code;
            $model->require_gst = $require_gst;
            $model->currency = $currency;
			$model->client_id = $client_id;
            $model->save(); 

            $invoice_details = InvoiceDetail::where('invoice_id',$id);
            $invoice_details->delete();

            $particular = $request->get('particular');
            $amount = $request->get('amount');
            $max = count($particular);
            
            for ($i=0; $i < $max; $i++) {

                $detail = new InvoiceDetail(); 
                $detail->invoice_id = $id; 
                $detail->particular = $particular[$i]; 
                $detail->amount = $amount[$i]; 
                $detail->save(); 
            }
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_INVOICE;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Invoice ::".$id;
                                    
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
    public function destroy($id, Request $request)
	{
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }

       $modelObj = $this->modelObj->find($id);

        if ($modelObj) {
            try {
                $holiData = InvoiceDetail::where('invoice_id', $id);
                $holiData->delete();
                
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg);

                //store logs detail
                    $params=array();    
                                            
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_INVOICE;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Invoice ::".$id;
                                            
                    $logs=\App\Models\AdminLog::writeadminlog($params);

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
    public function data(Request $request)
    {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $model = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id");

        $amount_query1 = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id");
        $amount_query2 = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id");

        $amount_query1 = Invoice::listFilter($amount_query1);
        $amount_query2 = Invoice::listFilter($amount_query2);
        
        $totalamounts = $amount_query1->where('currency','in_rs')->sum("total_amount");
        $totalamountsUSD = $amount_query2->where('currency','in_usd')->sum("total_amount");
        
        
        $totalamountsUSD = $totalamountsUSD * CURRENCY_USD;
        $totalamounts = $totalamounts + $totalamountsUSD; 
        $totalamounts = number_format($totalamounts,2);

        $data = \Datatables::eloquent($model)
            ->editColumn('created_at', function($row){                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
			->editColumn('payment', function ($row) { 
                if ($row->payment == 1){
                    return "<a class='btn btn-xs btn-success'>Paid</a><br/>";
                }
                else{
                    return '<a class="btn btn-xs btn-danger">UnPaid</a><br/>';
                }
            })
            ->addColumn('action', function(Invoice $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_INVOICE),
                        'inPDF' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_INVOICE),
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE), 
						'payment' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_INVOICE),
                    ]
                )->render();
            })->rawColumns(['action','created_at','payment'])
            ->filter(function ($query) {
				$query = Invoice::listFilter($query);                 
            });
            
            $data = $data->with('amounts',$totalamounts);

            $data = $data->make(true);

            return $data;        
    }
     function download_invoice(Request $request) 
    {
        $auth_id = \Auth::guard('admins')->user()->id;
        $auth_user =  superAdmin($auth_id);

        $invoice_id = $request->get('invoice_id');
        
        $data = array();

        if(!empty($invoice_id)){
            $invoices = Invoice::where('id',$invoice_id)->first();
            $invoice_details = InvoiceDetail::where('invoice_id',$invoice_id)->get();
        
            $client_type = 0;
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }			
			
			if(($invoices && $invoices->client_id == $client_type) || superadmin($auth_id))	
            {
                $name = $invoices->invoice_date;
                $data['invoices'] = $invoices;
                $data['invoice_details'] = $invoice_details;
                $pdf = PDF::loadView('pdf.invoice', $data);

            	return $pdf->download("invoice_".$name.".pdf");
            }
        }
        else{
            abort(404);
        }
    }

     public function viewData(Request $request)
    {   

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->user()->id;
        $auth_user =  superAdmin($auth_id); 
        
        $invoice_id = $request->get('invoice_id');
        
        $data = array();

        if(!empty($invoice_id))
        {
            $invoices = Invoice::where('id',$invoice_id)->first();
            $invoice_details = InvoiceDetail::where('invoice_id',$invoice_id)->get();

            $client_type = 0;
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }
           
           	if(($invoices && $invoices->client_id == $client_type) || $auth_user == 1)
            {    
                $data['invoices'] = $invoices;
                $data['invoice_details'] = $invoice_details;
                return view("pdf.invoice", $data);
            }
            else{
                abort(404);
            }
        }
    }
	public function clientData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $client_type = 0;
        $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                    $client_type = $client_user->client_id;
            }        
        $model = Invoice::select(TBL_INVOICE.".*")
                ->where(TBL_INVOICE.".client_id",$client_type);
        
        $data = \Datatables::eloquent($model)
            ->editColumn('created_at', function($row){
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';
            })
			->editColumn('payment', function ($row) { 
                if ($row->payment == 1){
                    return "<a class='btn btn-xs btn-success'>Paid</a><br/>";
                }
                else{
                    return '<a class="btn btn-xs btn-danger">UnPaid</a><br/>';
                }
            }) 
            ->addColumn('action', function(Invoice $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_INVOICE),
                        'inPDF' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_INVOICE),
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE),                                                     
                    ]
                )->render();
            })->rawColumns(['action','created_at','payment']);
            $data = $data->make(true);

            return $data;        
    }
}
