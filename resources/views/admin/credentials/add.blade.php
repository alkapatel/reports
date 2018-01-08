@extends('admin.layouts.app')

@section('styles')
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
                                {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'mt-repeater form-horizontal', 'id' => 'main-frm1','enctype'=>'multipart/form-data']) !!}
                                <div data-repeater-list="group-a">
                                    <div data-repeater-item class="mt-repeater-item">
                                        <div class="mt-repeater-input">
                                            <label class="control-label">Protocol</label>
                                            {!! Form::select('protocol',[''=>'Select Protocol','FTP'=>'FTP','CPANEL'=>'CPANEL','SSH'=>'SSH','ADMIN/WP-ADMIN'=>'ADMIN/WP-ADMIN','FRONT-END'=>'FRONT-END','HOSTING'=>'HOSTING','EXTRA'=>'EXTRA'],null,['class' => 'form-control protocol_id','data-required' =>true]) !!}
                                        </div>
                                            <div class="mt-repeater-input project" id="project">
                                                <label class="control-label">Project</label>
                                                {!! Form::select('project_id',[''=>'Select Project']+$projects,null,['class' => 'select2-common project_id form-control', 'data-required' => true,'id'=>'project_id']) !!}
                                            </div>
                                        <div class="div_id" style="display: none;">
                                            <div class="mt-repeater-input hostname">
                                                <label class="control-label">HostName</label>
                                                <br/>
                                                <input type="text" name="hostname" class="form-control" placeholder="Enter Hostname" />
                                            </div>
                                            <div class="mt-repeater-input username">
                                                <label class="control-label">UserName</label>
                                                <br/>
                                                <input type="text" name="username" class="form-control" placeholder="Enter Username"/>
                                            </div>
                                            <div class="mt-repeater-input password">
                                                <label class="control-label">Password</label>
                                                <br/>
                                                <input type="text" name="password" class="form-control" placeholder="Enter Password"/>
                                            </div>
											<div class="mt-repeater-input key_password">
                                                <label class="control-label">KeyFilePassword</label>
                                                <br/>
                                                <input type="text" name="key_file_password" class="form-control" placeholder="Enter Key Password"/>
                                            </div>
                                            <div class="mt-repeater-input port">
                                                <label class="control-label">Port</label>
                                                <br/>
                                                <input type="text" name="port" class="form-control" placeholder="Enter Port" id="port_id" />
                                            </div>
                                            <div class="mt-repeater-input url">
                                                <label class="control-label">URL</label>
                                                <br/>
                                                <input type="text" name="url" class="form-control" placeholder="Enter URL"/>
                                            </div>
											 <div class="mt-repeater-input port title">
                                                <label class="control-label">Title</label>
                                                <br/>
                                                <input type="text" name="title" class="form-control" placeholder="Enter Title" id="title_id" />
                                            </div>
                                            <div class="mt-repeater-input mt-repeater-textarea description">
                                                <label class="control-label">Description</label>
                                                <br/>
                                                <textarea name="description" class="form-control" rows="2"></textarea>
                                            </div>
											<div class="mt-repeater-input mode">
                                                <label class="control-label">Mode</label>
                                                <br/>
                                                {!! Form::select('mode',[''=>'Select Mode']+$modes,null,['class' => 'form-control', 'data-required' => false]) !!}
                                            </div>
                                            <div class="mt-repeater-input mt-radio-inline environment">
                                                <label class="control-label"> Environment</label>
                                                <br/>
                                                <label class="mt-radio">
                                                    <input type="radio" name="environment" class="optionsRadios25" value="Dev" checked=""> Dev
                                                    <span></span>
                                                </label>
                                                <label class="mt-radio">
                                                    <input type="radio" name="environment" class="optionsRadios26" value="Live" checked="" id="environment_id"> Live
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="mt-repeater-input mt-repeater-file key_file">
                                                <label class="control-label">Key-File</label>
                                                <br/>
                                                <input type="file" name="key_file" />
                                            </div>
											@if(!empty($auth) && $auth == ADMIN_USER_TYPE)
											<br/>
											<div class="mt-repeater-input">
                                                <label class="control-label">Share With Users</label>
                                                {!! Form::select('share_users',[''=>'Select User'],null,['class' => 'select2-common form-control users_list', 'data-required' => false,'multiple'=>'multiple']) !!}
                                            </div>
											@endif
                                        </div>
                                    </div>
                                </div>
                                <input type="submit" value="Save" class="btn btn-success pull-right" />
                                
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
		$(document).on("change",'.project_id',function() {

                var project_id = $(this).val();
                var parent = $(this).parent().parent();
                
                $('#AjaxLoaderDiv').fadeIn('slow');
               
                $.ajax({
                    type: "POST",
                    url: "{{route('getUsersList')}}",
                   data: {
                        "_token": "{{ csrf_token() }}",
                        "project_id" : project_id,
                    },
                    success: function(data) {
                        parent.find('.users_list').html('');
                        parent.find('.users_list').html(data);
                        $('#AjaxLoaderDiv').fadeOut('slow');
                    }
                });
            });
		
         $(document).on("change",'.protocol_id',function() {
            // alert("ok");
            var protocol = $(this).val();
            var parent = $(this).parent().parent();
            if(protocol == 'FTP'){    
                parent.find('.div_id').show();
                //parent.find('.project').show();
                parent.find('.hostname').show();
                parent.find('.username').show();
                parent.find('.password').show();
                parent.find('.port').show();
                parent.find('#port_id').val('21');
                parent.find('.url').hide();
                parent.find('.key_file').hide();
                parent.find('.description').hide();
				parent.find('.title').hide();
				parent.find('.key_password').hide();
                parent.find('.environment').show();
				parent.find('.mode').show();	
                parent.find('#environment_id').val('Live');
            }
            else if(protocol == 'CPANEL'){
                parent.find('.div_id').show();
                //parent.find('.project').show();
                parent.find('.hostname').hide();
                parent.find('.username').show();
                parent.find('.password').show();
                parent.find('.port').hide();
                parent.find('.url').show();
                parent.find('.key_file').hide();
                parent.find('.description').hide();
				parent.find('.title').hide();
                parent.find('.environment').show();
				parent.find('.key_password').hide();
				parent.find('.mode').hide();
                parent.find('#environment_id').prop( "checked", true );
            }
            else if(protocol == 'SSH'){
                parent.find('.div_id').show();
                //parent.find('.project').show();
                parent.find('.hostname').show();
                parent.find('.username').show();
                parent.find('.password').show();
                parent.find('.port').show();
                parent.find('#port_id').val('22');
                parent.find('.url').hide();
                parent.find('.key_file').show();
                parent.find('.description').hide();
				parent.find('.title').hide();
                parent.find('.environment').show();
				parent.find('.key_password').show();
				parent.find('.mode').hide();
                parent.find('#environment_id').prop( "checked", true );
            }
            else if(protocol == 'ADMIN/WP-ADMIN'){
                parent.find('.div_id').show();
                //parent.find('.project').hide();
                parent.find('.hostname').hide();
                parent.find('.username').show();
                parent.find('.password').show();
                parent.find('.port').hide();
                parent.find('.url').show();
                parent.find('.key_file').hide();
                parent.find('.description').hide();
				parent.find('.title').hide();
                parent.find('.environment').show();
				parent.find('.key_password').hide();
				parent.find('.mode').hide();
                parent.find('#environment_id').prop( "checked", true );
            }
            else if(protocol == 'FRONT-END'){
                parent.find('.div_id').show();
                //parent.find('.project').hide();
                parent.find('.hostname').hide();
                parent.find('.username').show();
                parent.find('.password').show();
                parent.find('.port').hide();
                parent.find('.url').show();
                parent.find('.key_file').hide();
                parent.find('.description').hide();
				parent.find('.title').hide();
                parent.find('.environment').show();
				parent.find('.key_password').hide();
				parent.find('.mode').hide();
                parent.find('#environment_id').prop( "checked", true );
            }
            else if(protocol == 'HOSTING'){
                parent.find('.div_id').show();
                //parent.find('.project').hide();
                parent.find('.hostname').hide();
                parent.find('.username').show();
                parent.find('.password').show();
                parent.find('.port').hide();
                parent.find('.url').show();
                parent.find('.key_file').hide();
                parent.find('.description').hide();
				parent.find('.title').hide();
				parent.find('.mode').hide();
                parent.find('.environment').show();
				parent.find('.key_password').hide();
                parent.find('#environment_id').prop( "checked", true );
            }
            else if(protocol == 'EXTRA'){
                parent.find('.div_id').show();
                //parent.find('.project').show();
                parent.find('.hostname').hide();
                parent.find('.username').hide();
                parent.find('.password').hide();
                parent.find('.port').hide();
                parent.find('.url').hide();
                parent.find('.key_file').hide();
                parent.find('.description').show();
				parent.find('.title').show();
                parent.find('.environment').show();
				parent.find('.key_password').hide();
				parent.find('.mode').hide();
                parent.find('#environment_id').prop( "checked", true );
            }
            else{
                $('.div_id').hide();
            }

            });
    });
   
</script>
<script type="text/javascript">
	function reinitselect2()
    {
        $("select.select2-common").select2({
                placeholder: "Search",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
    }
    $(document).ready(function(){
		reinitselect2();
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
        <script src="{{ asset("themes/admin/assets/")}}/global/plugins/jquery-repeater/jquery.repeater.js" type="text/javascript"></script> 
        <script src="{{ asset("themes/admin/assets/")}}/pages/scripts/form-repeater.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/assets/")}}/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
@endsection