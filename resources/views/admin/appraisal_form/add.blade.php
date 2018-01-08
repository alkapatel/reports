@extends('admin.layouts.app')
@section('styles')
 
@endsection

@section('content')
<div class="page-content">
    <div class="container">
        
        <div class="col-md-12">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                    <i class="fa fa-file"></i>{{ $page_title }}
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table">

                        {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                        <table class="table table-bordered table-hover" id="invoice_table" width="100%">
                                <tr>
                                    <td colspan="2"><b style="font-size: 26px"><center>Appraisal Form</center></b></td>
                                </tr>
                                <tr>
                                    <td><b> Rate your Past year </b>(0-3 Bad, 4-7 Average, 8-10 Good)</td>
                                    <td> {!! Form::select('past_year_rate',[''=>'Select Rate']+$past_year_rate,null,['class' => 'form-control', 'data-required' => true]) !!} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> What are some of the things you have achieved last year? </b></td>
                                    <td> {!! Form::textarea('past_year_achieved',null,['class' => 'form-control','data-required'=>true,'rows'=>3,'placeholder'=>'Describe at least 3']) !!} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Rate your job satisfaction </b>(0-3 Bad, 4-7 Average, 8-10 Good)</td>
                                    <td> {!! Form::select('job_satisfaction',[''=>'Select Rate']+$job_satisfaction,null,['class' => 'form-control', 'data-required' => true]) !!} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Do you feel that your achievements were recognized and rewarded? </b></td>
                                    <td> {!! Form::textarea('achievements',null,['class' => 'form-control','data-required'=>true,'rows'=>3,'placeholder'=>'Type Here...']) !!} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> State some of the goals you have set for the next year? </b></td>
                                    <td> {!! Form::textarea('goal',null,['class' => 'form-control','data-required'=>true,'rows'=>3,'placeholder'=>'Type Here...']) !!} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Describe your duties and responsibilities. </b></td>
                                    <td> {!! Form::textarea('duty_responsibility',null,['class' => 'form-control','data-required'=>true,'rows'=>3,'placeholder'=>'Type Here...']) !!} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> What things can the company do to better your working environment? </b></td>
                                    <td> {!! Form::textarea('suggestion',null,['class' => 'form-control','data-required'=>true,'rows'=>3,'placeholder'=>'Type Here...']) !!} 
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Total experience as on 31st December 2017. </b> (Ex. 5 years and 6 months)</td>
                                    <td> 
                                    <table>
                                        <tr>
                                            <td> {!! Form::text('years',null,['class' => 'form-control','data-required'=>true,'placeholder'=>'Year']) !!} </td>
                                            <td> - Years </td>
                                            <td> {!! Form::text('months',null,['class' => 'form-control','data-required'=>true,'placeholder'=>'Month']) !!} </td>
                                            <td> - Months </td>
                                        </tr>
                                    </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Current Salary </td>
                                    <td> {!! Form::number('current_salary',null,['class' => 'form-control','data-required'=>true,'placeholder'=>'Type Current Salary','id'=>'current_salary']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Expected Salary (Year - 2018) </td>
                                    <td> {!! Form::number('expected_salary',null,['class' => 'form-control','data-required'=>true,'placeholder'=>'Type Expected Salary','id'=>'expected_salary']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Raise (%) </td>
                                    <td> <input type="hidden" name="raise" id="raise_val">
                                        {!! Form::text('raise',null,['class' => 'form-control','disabled'=>'disabled','id'=>'raise_val_display']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="right">
                                        <input type="hidden" id="raise_salary" onclick="raise_salary_cal()">
                                        <input type="hidden" id="is_submit" name="is_submit">
                                        <button type="button" name="save" data-id="0" class="btn btn-success save_submit">
                                        Save</button>
                                        <button type="button" name="save_submit" data-id="1" class="btn btn-primary save_submit">
                                        Save & Submit</button>
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
    function raise_salary_cal(){
        var current_salary = parseInt($('#current_salary').val()) || 0;
        var expected_salary = parseInt($('#expected_salary').val()) || 0;
       
        var inc_amount = expected_salary - current_salary;
        inc_amount = parseInt(inc_amount);
        var raise = (inc_amount*100)/current_salary;

        //$('#raise_val_display').val(raise);
        $('#raise_val').val(raise.toFixed(2));
        $("#raise_val_display").val(raise.toFixed(2));
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {

        $(document).on('change','#current_salary',function(){
            $('#AjaxLoaderDiv').fadeIn('slow');
            $('#raise_salary').trigger('click');
            $('#AjaxLoaderDiv').fadeOut('slow');
        });
        $(document).on('change','#expected_salary',function(){
            $('#AjaxLoaderDiv').fadeIn('slow');
            $('#raise_salary').trigger('click');
            $('#AjaxLoaderDiv').fadeOut('slow');
        }); 
        $(document).on('click','.save_submit',function(){
            var data_id = $(this).attr("data-id");
            $('#is_submit').val(data_id);
            $('#main-frm1').submit();
        });
         $('#main-frm1').submit(function () {
            
            if ($(this).parsley('isValid'))
            {
                $('#submit_btn').attr("disabled", true);
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
                            window.location.reload();    
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                            $('#submit_btn').attr("disabled", false);
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
</script>
 

@endsection