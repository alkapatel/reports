@extends('admin.layouts.app')

@section('styles')

@endsection
 
@section('content')
        
<div class="page-container">
<div class="page-content-wrapper">

<div class="page-content">
    <div class="container"> 
        <div class="page-content-inner">
            
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file"></i>{{ $page_title }}</div>
                <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                </div>
                <div class="portlet-body form">
                    <div class="form-body"> 
                        <div class="form-group">
                                {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'form-horizontal', 'id' => 'main-frm1','enctype'=>'multipart/form-data']) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                    <label class="control-label">Protocol<span class="required">*</span></label>
                                        {!! Form::select('protocol',[''=>'Select Protocol','FTP'=>'FTP','CPANEL'=>'CPANEL','SSH'=>'SSH','ADMIN/WP-ADMIN'=>'ADMIN/WP-ADMIN','FRONT-END'=>'FRONT-END','HOSTING'=>'HOSTING','EXTRA'=>'EXTRA'],null,['class' => 'form-control','data-required' =>true,'id'=>'protocol_id']) !!}
                                    </div>
                                    <div class="col-md-6">
                                    <label class="control-label">Project<span class="required">*</span></label>
                                        {!! Form::select('project_id',[''=>'Select Project']+$projects,null,['class' => 'form-control', 'data-required' => false,'id'=>'project_id']) !!}
                                    </div>
                            </div>
                        <div class="div_id" style="display: none;">
                        <div class="hostname">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">HostName<span class="required">*</span></label>
                                {!! Form::text('hostname',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter HostName']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="username">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">Username<span class="required">*</span></label>
                                {!! Form::text('username',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter UserName']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="password">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row ">
                                <div class="col-md-12">
                                <label class="control-label">Password</label>
                                {!! Form::text('password',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter password']) !!}
                            </div>
                            </div>
                        </div>
                        <div class="key_password">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">Key File Password</label>
                                {!! Form::text('key_file_password',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Key File Password']) !!}
                            </div>
                            </div>
                        </div>
                        <div class="port">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">Port<span class="required">*</span></label>
                                {!! Form::text('port',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter port','id'=>'port_id']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="url">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">url<span class="required">*</span></label>
                                {!! Form::text('url',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter url']) !!}
                                </div>
                            </div>
                        </div>
						<div class="mode">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">Mode<span class="required">*</span></label>
                                {!! Form::select('mode',[''=>'Select Mode']+$modes,null,['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="title">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">Title<span class="required">*</span></label>
                                {!! Form::text('title',null,['class' =>'form-control', 'data-required' => 'false','placeholder'=>'Enter Title']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="description">
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                <label class="control-label">Description<span class="required">*</span></label>
                                {!! Form::textarea('description',null,['class' =>'form-control', 'data-required' => 'false','rows'=>'3']) !!}
                                </div>
                            </div>
                        </div>
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-6">
                                <label class="control-label">Environment : <span class="required">*</span></label>&nbsp;&nbsp;
                                {!! Form::radio('environment','Dev',['class' => 'form-control']) !!}
                                <span> &nbsp;Dev&nbsp; </span>
                                {!! Form::radio('environment','Live',['class' => 'form-control','checked'=>'checked']) !!}
                                <span> &nbsp;Live&nbsp; </span>
                                </div>
                            <div class="key_file">
                                <div class="col-md-6">
                                    <label class="control-label">Key File :</label>
                                    {!! Form::file('key_file') !!}
                                </div>
                            </div>
                            </div>
                        </div>
						<div class="clearfix">&nbsp;</div>
                            <input type="submit" value="Save" class="btn btn-success pull-right" id="submit_btn" />
                        <div class="clearfix">&nbsp;</div>      
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
 
<script type="text/javascript">
    $(document).ready(function(){
         
        $("#project_id").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });

         $(document).on("change",'#protocol_id',function() {
            
            var protocol = $(this).val();
            if(protocol == 'FTP'){    
                $('.div_id').show();
                $('.hostname').show();
                $('.username').show();
                $('.password').show();
                $('.port').show();
                $('#port_id').val('21');
                $('.url').hide();
                $('.key_file').hide();
                $('.description').hide();
                $('.title').hide();
                $('.key_password').hide();
                $('.environment').show();
				$('.mode').show();
                $('#environment_id').val('Live');
            }
            else if(protocol == 'CPANEL'){
               $('.div_id').show();
               $('.hostname').hide();
               $('.username').show();
               $('.password').show();
               $('.port').hide();
               $('.url').show();
               $('.key_file').hide();
               $('.description').hide();
               $('.title').hide();
				$('.mode').hide();
               $('.key_password').hide();
               $('.environment').show();
            }
            else if(protocol == 'SSH'){
                $('.div_id').show();
                $('.hostname').show();
                $('.username').show();
                $('.password').show();
                $('.port').show();
                $('#port_id').val('22');
                $('.url').hide();
                $('.key_file').show();
                $('.key_password').show();
                $('.description').hide();
                $('.title').hide();
				$('.mode').hide();
                $('.environment').show();
            }
            else if(protocol == 'ADMIN/WP-ADMIN'){
                $('.div_id').show();
                $('.hostname').hide();
                $('.username').show();
                $('.password').show();
                $('.port').hide();
                $('.url').show();
                $('.key_file').hide();
                $('.description').hide();
                $('.title').hide();
                $('.key_password').hide();
				$('.mode').hide();
                $('.environment').show();
            }
            else if(protocol == 'FRONT-END'){
                $('.div_id').show();
                $('.hostname').hide();
                $('.username').show();
                $('.password').show();
                $('.port').hide();
                $('.url').show();
                $('.key_file').hide();
                $('.description').hide();
                $('.title').hide();
                $('.key_password').hide();
				$('.mode').hide();
                $('.environment').show();
            }
            else if(protocol == 'HOSTING'){
                $('.div_id').show();
                $('.hostname').hide();
                $('.username').show();
                $('.password').show();
                $('.port').hide();
                $('.url').show();
                $('.key_file').hide();
                $('.description').hide();
                $('.title').hide();
                $('.key_password').hide();
				$('.mode').hide();
                $('.environment').show();
            }
            else if(protocol == 'EXTRA'){
                $('.div_id').show();
                $('.hostname').hide();
                $('.username').hide();
                $('.password').hide();
                $('.port').hide();
                $('.url').hide();
                $('.key_file').hide();
                $('.key_password').hide();
                $('.description').show();
                $('.title').show();
				$('.mode').hide();
                $('.environment').show();
            }
            else{
                $('.div_id').hide();
            }
        });
    });
   
</script>

 <script type="text/javascript">
    $(document).ready(function () { 

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
</script>
 
       
@endsection