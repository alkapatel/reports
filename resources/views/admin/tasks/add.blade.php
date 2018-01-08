@extends('admin.layouts.app')

<?php 
$auth = Auth::guard('admins')->user()->user_type_id;
$current_user = Auth::guard('admins')->user()->id;

$user_id = $formObj->user_id;
if(!empty($user_id))
    $user_id = $user_id;
else
    $user_id = $current_user;

$task_date = $formObj->task_date;
$date = date('Y-m-d');
if(!empty($task_date))
    $task_date = $task_date;
else
    $task_date = $date;

?>
@section('styles')
 
@endsection
@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-tasks"></i>
                           {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                            {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!}                           
                        
                        <div class="add_row clearfix">
                        <div class="col-sm-4 nopadding">
                            <div class="input-group" style="width:100%;">
                              {!! Form::select('project_id[]',[''=>'Select Project']+$projects,null,['class' => 'form-control', 'data-required' => true,'id'=>'project_id']) !!}
                              </select>
                          </div>
                        </div>
                        <div class="col-sm-4 nopadding">
                          <div class="form-group">
                            {!! Form::text('title[]',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Task Title']) !!}
                          </div>
                        </div>
                        <div class="col-sm-2 nopadding">
                          <div class="form-group">
                            {!! Form::select('status[]',['1'=>'Completed','0'=>'In Progress'],null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select Status']) !!}
                          </div>
                        </div>
                        <div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;">
                          <div class="form-group">
                            {!! Form::select('hour[]',[''=>'Hour']+$hours,null,['class' => 'form-control', 'data-required' => true,'id'=>'hour_id']) !!}
                          </div>
                        </div>
                        <div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;padding-right: 10px;">
                          <div class="form-group">
                            {!! Form::select('min[]',$mins,null,['class' => 'form-control', 'data-required' => true,'id'=>'min_id']) !!}
                          </div>
                        </div>                        
                        <div class="col-sm-6 nopadding">
                          <div class="form-group">
                            {!! Form::textarea('description[]',null,['class' => 'form-control','placeholder' => 'Enter Description','rows'=>'3']) !!}
                          </div>
                        </div>
                        <div class="col-sm-6 nopadding">
                          <div class="form-group">
                            <div class="input-group">
                               {!! Form::text('ref_link[]',null,['class' => 'form-control','placeholder' => 'Enter Ref. Link']) !!}
                            @if(!empty($editMode))
                              <div class="input-group-btn btn-add-task" style="padding-left:5px">
                                <button class="btn btn-success pull-right" type="button"  onclick="project_task();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                              </div>
                            @endif
                            </div>
                          </div>
                        </div>
                        @if(!empty($auth) && $auth == 1)
                        <div class="col-sm-4 nopadding">
                            {!! Form::select('user_id[]',[''=>'Select User']+$users,$user_id,['class' => 'form-control', 'data-required' => true,'id'=>'user_id']) !!}
                        </div>
                        <div class="col-sm-1 nopadding">
                                {!! Form::text('task_date[]',null,['class' => 'task_date input-group form-control input-small date-picker  data-date-format="dd/mm/yyyy" ', 'data-required' => false,'id'=>'','placeholder'=>'Task Date']) !!}
                        </div>                        
                        @endif
                    </div>
                    <div id="project_task">
                    </div>
                     <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
									{!! Form::hidden('current_date',$task_date,['id' => 'current_date']) !!}
                                    <input type="submit" value="Save" class="btn btn-success pull-right" id="submit_btn" />
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>                 
            </div>
        </div>
    </div>
</div>            

@endsection
<style type="text/css">
.add_row_new{
    border: solid 1px #e3e6e8;
    padding-top: 15px;
    background-color: #f7f8f9;
    margin-top: 15px;
}
.add_row{
    border: solid 1px #e3e6e8;
    padding-top: 15px;
    background-color: #f7f8f9;
}
.btn.btn-danger,
.btn.btn-success{
    padding: 9px 10px;
}
</style>
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
		var current_date = $('#current_date').val();
        $('.task_date').val(current_date);
	 	
		/*$(document).on("click",".btn-add-task",function(){
            setTimeout(function(){
                $("select[name='user_id[]']").each(function(){
                    if($.trim($(this).val()) == '')
                    {
                        $(this).val('{{ Auth::guard('admins')->user()->id }}');
                    }
                });
            },1000);
        });*/
		
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
                            window.location = '{{ $list_url }}';    
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

