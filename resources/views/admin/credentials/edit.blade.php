@extends('admin.layouts.app')

@section('styles')

    <link href="{{ asset("/themes/admin/assets")}}/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/themes/admin/assets")}}/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
@endsection
<?php 
$auth = Auth::guard('admins')->user()->user_type_id;
?>

@section('content')
        
<div class="page-container">
<div class="page-content-wrapper">

<div class="page-content">
    <div class="container"> 
        <div class="page-content-inner">
            
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift"></i>{{ $page_title }}</div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                </div>
                <div class="portlet-body form">
                    <div class="form-body"> 
                        <div class="form-group">
                                {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'form-horizontal', 'id' => 'main-frm']) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                    <label class="control-label">Protocol<span class="required">*</span></label>
                                        {!! Form::select('protocol',[''=>'Select Protocol','FTP'=>'FTP','CPANEL'=>'CPANEL','SSH'=>'SSH','ADMIN/WP-ADMIN'=>'ADMIN/WP-ADMIN','FRONT-END'=>'FRONT-END','HOSTING'=>'HOSTING','EXTRA'=>'EXTRA'],null,['class' => 'form-control protocol_id','data-required' =>true]) !!}
                                    </div>
                                    <div class="col-md-6">
                                    <label class="control-label">Project<span class="required">*</span></label>
                                        {!! Form::select('project_id',[''=>'Select Project']+$projects,null,['class' => 'project_id form-control', 'data-required' => true,'id'=>'project_val']) !!}
                                    </div>
                                </div>
                                @if($formObj->protocol == 'FTP' || $formObj->protocol == 'SSH')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">HostName<span class="required">*</span></label>
                                    {!! Form::text('hostname',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter HostName']) !!}
                                    </div>
                                </div>
                                @endif
                                @if($formObj->protocol == 'FTP' || $formObj->protocol == 'CPANEL' || $formObj->protocol == 'SSH' || $formObj->protocol == 'ADMIN/WP-ADMIN' || $formObj->protocol == 'FRONT-END' || $formObj->protocol == 'HOSTING')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">Username<span class="required">*</span></label>
                                    {!! Form::text('username',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter UserName']) !!}
                                    </div>
                                </div>
                                @endif
                                @if($formObj->protocol == 'FTP' || $formObj->protocol == 'CPANEL' || $formObj->protocol == 'SSH' || $formObj->protocol == 'ADMIN/WP-ADMIN' || $formObj->protocol == 'FRONT-END' || $formObj->protocol == 'HOSTING')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">password</label>
                                    {!! Form::text('password',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter password']) !!}
                                </div>
                                </div>
                                @endif
							@if($formObj->protocol == 'SSH')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">Key File Password</label>
                                    {!! Form::text('key_file_password',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Key File Password']) !!}
                                </div>
                                </div>
                                @endif
                                @if($formObj->protocol == 'FTP' || $formObj->protocol == 'SSH')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">port<span class="required">*</span></label>
                                    {!! Form::text('port',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter port']) !!}
                                    </div>
                                </div>
                                @endif
                                @if($formObj->protocol == 'CPANEL' || $formObj->protocol == 'ADMIN/WP-ADMIN' || $formObj->protocol == 'FRONT-END' || $formObj->protocol == 'HOSTING')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">url<span class="required">*</span></label>
                                    {!! Form::text('url',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter url']) !!}
                                    </div>
                                </div>
                                @endif
                                @if($formObj->protocol == 'EXTRA')
								<div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">title<span class="required">*</span></label>
                                    {!! Form::text('title',null,['class' =>'form-control', 'data-required' => 'true']) !!}
                                    </div>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">description<span class="required">*</span></label>
                                    {!! Form::textarea('description',null,['class' =>'form-control', 'data-required' => 'false','rows'=>'3']) !!}
                                    </div>
                                </div>
                                @endif
								@if($formObj->protocol == 'FTP')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">Mode<span class="required">*</span></label>
                                    {!! Form::select('mode',[''=>'Select Mode']+$modes,null,['class' => 'form-control', 'data-required' => false]) !!}
                                    </div>
                                </div>
                                @endif
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">Environment : <span class="required">*</span></label>&nbsp;&nbsp;
                                    {!! Form::radio('environment','Dev',['class' => 'form-control']) !!}
                                    <span> &nbsp;Dev&nbsp; </span>
                                    {!! Form::radio('environment','Live',['class' => 'form-control']) !!}
                                    <span> &nbsp;Live&nbsp; </span>
                                    </div>
                                </div>
                                @if($formObj->protocol == 'SSH')
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">Key File : <span class="required">*</span></label><br/>
                                        <b>{{ $formObj->key_file }}</b>
                                        <br/><br/>
                                        {!! Form::file('key_file') !!}
                                    </div>
                                </div>
                                @endif
								@if(!empty($auth) && $auth == ADMIN_USER_TYPE)
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet">
                                    <div class="portlet-body" style="display: block;">
                                        <label class="control-label">Share With Users</label>
                                            <select class="select_users users_list" multiple="multiple" name="users[]">
                                    @foreach($share_users as $row)
                                            <option {{ in_array($row->id, $list_users) ? 'selected':''}} value="{{ $row->id }}">{{ $row->name }}											</option>
                                    @endforeach
                                            </select>                                            
                                    </div>
                                    </div>
                                </div>
                                </div>
                                @endif
                                <input type="submit" value="Save" class="btn btn-success pull-right" />
                                <br/>
                            {!!Form::close()!!}
                        </div>
                    </div>
                </div>
            </div>     
        </div>
    </div>
</div> 
</div>
 

@endsection
@section('scripts')
<script src="{{ asset('/themes/admin/assets')}}/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="{{ asset('/themes/admin/assets')}}/pages/scripts/components-select2.min.js" type="text/javascript"></script>

<script type="text/javascript">

$(document).ready(function(){
	$(".select_users").select2({
			placeholder: "Search Users",
			allowClear: true,
			minimumInputLength: 2,
			width: null
        });
		$(document).on("change",'.project_id',function() {

                var project_id = $(this).val();
                
                $('#AjaxLoaderDiv').fadeIn('slow');
               
                $.ajax({
                    type: "POST",
                    url: "{{route('getUsersList')}}",
                   data: {
                        "_token": "{{ csrf_token() }}",
                        "project_id" : project_id,
                    },
                    success: function(data) {
                        $(".users_list").html('');
                        $(".users_list").html(data);
                        $('#AjaxLoaderDiv').fadeOut('slow');
                    }
                });
            });
		
        $('#main-frm').submit(function () {

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
   
</script>

@endsection