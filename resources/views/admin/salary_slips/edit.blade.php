@extends('admin.layouts.app')
@section('styles')
 
@endsection
@section('content')
<div class="page-content">
    <div class="container">
        
        <div class="col-md-12">
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                    <i class="fa fa-picture"></i>{{ $page_title }}
                    </div>
                    <a href="javascript:;" class="reload" onclick="Salary_cal()" id="reload_id"> </a>
                    <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                </div>
                <div class="portlet-body">
                    <div class="table">
                        {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                           
                        <div class="row">
                            <div class="col-md-6">
                            <label class="control-label">User Name:</label>
                                {!! Form::select('user_id',[''=>'Select User Name']+$users,null,['class' => 'form-control user_list', 'data-required' => false,'id'=>'user_id']) !!}
                            </div>
                            <div class="col-md-6">
                             <label class="control-label">CTC</label>
                             {!! Form::text('ctc',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter CTC','id'=>'ctc_id']) !!}   
                            </div>
                        </div>
                        <div class="clearfix">&nbsp;</div><br/><div class="clearfix">&nbsp;</div>
                        <table class="table table-bordered table-hover">
                                <tr>
                                    <td colspan="2">Company Logo  </td>
                                    <td colspan="2"><img src="{{ asset("images/pd-logo.png")}}" alt="logo" class="logo-default" style="max-width: 100px;margin-top: 15px !important; max-height: 250px"></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Company Name </td>
                                    <td colspan="2"><b>PHPDots Technologies</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Address</td>
                                    <td colspan="2">C-103, Pragtya Residency, Nr. Sarthi Bunglows, Bopal - Ahmedabad        </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Phone</td>
                                    <td colspan="2">9825096687</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center" style="font-size: 18px;"><b>Pay Slip For the Month of                                       
                                        </b>
                                    </td>
                                    <td>
                                        {!! Form::select('month',[''=>'Select Month']+$months,null,['class' => 'form-control', 'data-required' => true,'id'=>'month_id']) !!}
                                    </td>
                                    <td>
                                        {!! Form::select('year',[''=>'Select year']+$years,null,['class' => 'form-control', 'data-required' => true,'id'=>'year_id']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"> </td>
                                </tr>
                                <tr>
                                    <td> Employee Name</td>
                                    <td id="username"><b>  </b></td>
                                    <td> Bank A/C No. </td>
                                    <td> {!! Form::text('account_num',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type Account No.','id'=>'account_no']) !!} </td>
                                </tr>
                                <tr>
                                    <td> Date Of Joining </td>
                                    <td> {!! Form::text('joining_date',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select Joining Date','id'=>'start_date']) !!} </td>
                                    <td> Bank Name </td>
                                    <td> {!! Form::text('bank_name',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type Bank Name','id'=>'bank_nm']) !!} </td>
                                </tr>
                                <tr>
                                    <td> Department </td>
                                    <td> Software Development </td>
                                    <td> Working Days </td>
                                    <td> {!! Form::text('working_days',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type Working Days','id'=>'working_days']) !!} </td>
                                </tr>
                                <tr>
                                    <td> Designation</td>
                                    <td> {!! Form::text('designation',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type Designation','id'=>'designation']) !!} </td>
                                    <td> Leave Taken </td>
                                    <td> {!! Form::number('leave_taken',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Leave Taken','id'=>'leave_taken']) !!} </td>
                                </tr>
                                <tr>
                                    <td> PAN No. </td>
                                    <td> {!! Form::text('pan_num',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type PAN No.','id'=>'pan_num']) !!} </td>
                                    <td> </td>
                                    <td>  </td>
                                </tr>
                                <tr>
                                    <td colspan="4"> </td>
                                </tr>
                                <tr>
                                    <td width="25%"><b> Particular </b></td>
                                    <td width="25%"><b> Amount </b></td>
                                    <td width="25%"><b> Deduction </b></td>
                                    <td width="25%"><b> Amount </b></td>
                                </tr>
                                <tr>
                                    <td> Basic Salary</td>
                                    <td> {!! Form::text('basic_salary',null,['class' => 'form-control', 'data-required' => true,'id'=>'basic_salary']) !!} </td>
                                    <td> Advance</td>
                                    <td>  {!! Form::text('advance',null,['class' => 'form-control', 'data-required' => true,'id'=>'advance']) !!}</td>
                                </tr>
                                <tr>
                                    <td> HRA</td>
                                    <td> {!! Form::text('hra',null,['class' => 'form-control', 'data-required' => true,'id'=>'hra']) !!} </td>
                                    <td> Leave Deduction</td>
                                    <td>  {!! Form::text('leave_deduction',null,['class' => 'form-control', 'data-required' => true,'id'=>'leave_deduction']) !!}</td>
                                </tr>
                                <tr>
                                    <td> Conveyance Allowance</td>
                                    <td> {!! Form::text('conveyance_allowance',null,['class' => 'form-control', 'data-required' => true,'id'=>'conveyance_allow']) !!} </td>
                                    <td> Other Deduction</td>
                                    <td>  {!! Form::text('other_deduction',null,['class' => 'form-control', 'data-required' => true,'id'=>'other_deduction']) !!}</td>
                                </tr><tr>
                                    <td> Telephone Allowance</td>
                                    <td> {!! Form::text('telephone_allowance',null,['class' => 'form-control', 'data-required' => true,'id'=>'telephone_allow']) !!} </td>
                                    <td> TDS</td>
                                    <td>  {!! Form::text('tds',0,['class' => 'form-control', 'data-required' => true,'id'=>'tds']) !!}</td>
                                </tr><tr>
                                    <td> Medical Allowance</td>
                                    <td> {!! Form::text('medical_allowance',null,['class' => 'form-control', 'data-required' => true,'id'=>'medical_allow']) !!} </td>
                                    <td> </td>
                                    <td>  </td>
                                </tr><tr>
                                    <td> Uniform Allowance</td>
                                    <td> {!! Form::text('uniform_allowance',null,['class' => 'form-control', 'data-required' => true,'id'=>'uniform_allow']) !!}</td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td> Special Allowance</td>
                                    <td> {!! Form::text('special_allowance',null,['class' => 'form-control', 'data-required' => true,'id'=>'special_allow']) !!} </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td> Bonus / Incentive</td>
                                    <td> {!! Form::text('bonus',null,['class' => 'form-control', 'data-required' => true,'id'=>'bonus']) !!} </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td> Arrear Salary</td>
                                    <td> {!! Form::text('arrear_salary',null,['class' => 'form-control', 'data-required' => true,'id'=>'arrear_salary']) !!} </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td> Advance Given</td>
                                    <td> {!! Form::text('advance_given',null,['class' => 'form-control', 'data-required' => true,'id'=>'advance_given']) !!} </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td> Leave Encashment</td>
                                    <td> {!! Form::text('leave_encashment',null,['class' => 'form-control', 'data-required' => true,'id'=>'leave_encashment']) !!} </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td> Total Earnings</td>
                                    <td> {!! Form::text('total_earning',null,['class' => 'form-control', 'data-required' => true,'id'=>'total_earnings']) !!}</td>
                                    <td> Total Deductions</td>
                                    <td> {!! Form::text('total_deduction',null,['class' => 'form-control', 'data-required' => true,'id'=>'total_deductions']) !!}</td>
                                </tr>
                                <tr>
                                    <td colspan="4"> </td>
                                </tr>
                                <tr>
                                    <td>Net Pay</td>
                                    <td><b> {!! Form::text('net_pay',null,['class' => 'form-control', 'data-required' => true,'id'=>'net_pay']) !!} </b></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Net Pay In Words</td>
                                    <td colspan="3"><span id="pay_words"></span>
                                        <input type="hidden" name="net_pay_words" id="net_pay_words">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">This is system-generated document and doesn't need signature. </td>
                                </tr>                                
                                <tr>
                                    <td colspan="3"></td>
                                    <td>
                                        <input type="submit" class="btn btn-primary" name="submit" value="Save">
                                    </td>
                                </tr>
                        </table>
                    {!!Form::close()!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

    @section('scripts')

<script type="text/javascript">

    function Salary_cal()
    {
        $('#AjaxLoaderDiv').fadeIn('slow');
        var ctc_id = parseInt($('#ctc_id').val());

                if(ctc_id != '')
                {
                    //var basic_salary = ctc_id/2;
                    //left side
                    var basic_salary_val = parseInt($('#basic_salary').val());
                    var hra_val = parseInt($('#hra').val());
                    var conveyance_allow_val = parseInt($('#conveyance_allow').val());
                    var telephone_allow_val = parseInt($('#telephone_allow').val());
                    var medical_allow_val = parseInt($('#medical_allow').val());
                    var uniform_allow_val = parseInt($('#uniform_allow').val());

                    var total =  basic_salary_val+hra_val+conveyance_allow_val+telephone_allow_val+medical_allow_val+uniform_allow_val;
                    var special_total = ctc_id - total;
                    $('#special_allow').val(special_total);

                    var bonus_val = parseInt($('#bonus').val());
                    var arrear_salary_val = parseInt($('#arrear_salary').val());
                    var advance_given_val = parseInt($('#advance_given').val());
                    var leave_encashment_val = parseInt($('#leave_encashment').val());
                    
                    var earning_ttl = total+special_total+bonus_val+arrear_salary_val+advance_given_val+leave_encashment_val;
                    var total_earnings_val = parseInt($('#total_earnings').val(earning_ttl));
                    
                    //right side
                    var advance_val = parseInt($('#advance').val());
                    var leave_deduction_val = parseInt($('#leave_deduction').val());
                    var other_deduction_val = parseInt($('#other_deduction').val());
                    var tds_val = parseInt($('#tds').val());

                    var  deduction_ttl = advance_val+leave_deduction_val+other_deduction_val+tds_val;
                    $('#total_deductions').val(deduction_ttl);
                    
                    var final_earnings = parseInt($('#total_earnings').val());
                    var final_deductions = parseInt($('#total_deductions').val());
                    var net_pay = final_earnings-final_deductions;
                    $('#net_pay').val(net_pay);
                    
                    var num =  net_pay;
                    //pay to words
                    var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
                    var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

    if ((num = num.toString()).length > 9) return 'overflow';
n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
    if (!n) return; var str = '';
str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'only ' : '';
$('#pay_words').html(str);
$('#net_pay_words').val(str);

                    $('#AjaxLoaderDiv').fadeOut('slow');
                }
    }
    $(document).ready(function(){

        $('#ctc_id').change( function() {  
			var ctc_id = parseInt($('#ctc_id').val());
            var basic_salary = ctc_id/2;
            var basic_salary = parseInt(basic_salary);
            $('#basic_salary').val(basic_salary);
            $("#reload_id").trigger('click'); 
		});
        $('#basic_salary').change( function() {  $("#reload_id").trigger('click'); });
        $('#hra').change( function() {  $("#reload_id").trigger('click'); });
        $('#conveyance_allow').change( function() { $("#reload_id").trigger('click'); });
        $('#telephone_allow').change( function() { $("#reload_id").trigger('click'); });
        $('#medical_allow').change( function() { $("#reload_id").trigger('click'); });
        $('#uniform_allow').change( function() { $("#reload_id").trigger('click'); });
        //$('#special_allow').change( function() { $("#reload_id").trigger('click'); });
        $('#bonus').change( function() { $("#reload_id").trigger('click'); });
        $('#arrear_salary').change( function() { $("#reload_id").trigger('click'); });
        $('#advance_given').change( function() { $("#reload_id").trigger('click'); });
        $('#leave_encashment').change( function() { $("#reload_id").trigger('click'); });

        $('#advance').change( function() { $("#reload_id").trigger('click'); });
        $('#leave_deduction').change( function() { $("#reload_id").trigger('click'); });
        $('#other_deduction').change( function() { $("#reload_id").trigger('click'); });
        $('#tds').change( function() {  $("#reload_id").trigger('click'); });

    	$('.user_list').on("change",function() {
            getUserDetail();
            });
        $('#month_id').on("change",function() {
            getWorkingDay();
            });
        $('#year_id').on("change",function() {
            getWorkingDay();
            });
		$("#user_id").select2({
                placeholder: "Search User Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
            });
        $("#year_id").select2({
                placeholder: "Search Year",
                allowClear: true,
                minimumInputLength: 2,
                width: null
            });
        $("#month_id").select2({
                placeholder: "Search Month",
                allowClear: true,
                minimumInputLength: 2,
                width: null
            });
    $('#main-frm1').submit(function () {
            
            if ($(this).parsley('isValid'))
            {
                $('#AjaxLoaderDiv').fadeIn('slow');
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function (result)
                    {
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        if (result.status == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                            window.location = '{{ $list_url }}';    
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                        }
                    },
                    error: function (error) {
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                    }
                });
            }            
            return false;
        });
    });
	function getUserDetail(){
        var user_id = $('#user_id').val();        
        var month = $('#month_id').val();
        var year = $('#year_id').val();

        $('#AjaxLoaderDiv').fadeIn('slow');
       
        $.ajax({
            type: "POST",
            url: "{{route('getuserdetail')}}",
           data: {
                "_token": "{{ csrf_token() }}",
                "user_id" : user_id,
                "month" : month,
                "year" : year,
            },
            success: function(data) {
                $('#start_date').val(data.joining_date);
                $('#account_no').val(data.account_no);
                $('#bank_nm').val(data.bank_nm);
                $('#designation').val(data.designation);
                $('#pan_num').val(data.pan_num);
                $('#username').html(data.name);
                $('#AjaxLoaderDiv').fadeOut('slow');
            }
        });
    }
    function getWorkingDay(){
        var user_id = $('#user_id').val();        
        var month = $('#month_id').val();
        var year = $('#year_id').val();

        $('#AjaxLoaderDiv').fadeIn('slow');
       
        $.ajax({
            type: "POST",
            url: "{{route('getuserdetail')}}",
           data: {
                "_token": "{{ csrf_token() }}",
                "user_id" : user_id,
                "month" : month,
                "year" : year,
            },
            success: function(data) {
                $('#working_days').val(data.working_days);
                $('#leave_taken').val(data.leave_taken);
                $('#AjaxLoaderDiv').fadeOut('slow');
            }
        });
    }
</script>

    <script src="{{ asset("themes/admin/assets/")}}/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="{{ asset("themes/admin/assets/")}}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="{{ asset("themes/admin/assets/")}}/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
    <script src="{{ asset("themes/admin/assets/")}}/pages/scripts/form-wizard.min.js" type="text/javascript"></script>
    @endsection