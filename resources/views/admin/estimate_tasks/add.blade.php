@extends('admin.layouts.app')
<?php 
$auth = Auth::guard('admins')->user()->user_type_id;
?>
@section('styles')
@endsection
@section('content')
<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                    <i class="fa fa-file"></i>Estimated Daily Tasks</div>
                    <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-body">
                        <div class="form-group">
                             {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'mt-repeater form-horizontal', 'id' => 'main-frm1','enctype'=>'multipart/form-data']) !!}
                                <h3 class="mt-repeater-title" align="center"><b>Starting Details</b></h3><hr/>
                                <div data-repeater-list="group-a">
                                    <div data-repeater-item class="mt-repeater-item">
                                        <!-- jQuery Repeater Container -->
                                        <div class="mt-repeater-input project" id="project">
                                                <label class="control-label">Project<span class="required">*</span></label>
                                                {!! Form::select('project_id',[''=>'Select Project']+$projects,null,['class' => 'form-control', 'data-required' => true]) !!}
                                        </div>
                                        <div class="mt-repeater-input mt-repeater-textarea">
                                            <label class="control-label">Task<span class="required">*</span></label>
                                            <br/>
                                            {!! Form::text('task',null,['class' => 'form-control','placeholder' => 'Enter Task Title', 'data-required' => true]) !!}
                                        </div>
                                        <div class="mt-repeater-input" style="padding: 0px;margin: 0px; width:2">
                                            <label class="control-label">Estimated Hours<span class="required">*</span></label>
                                            <br/>
                                            {!! Form::select('estimated_hour',[''=>'Hour']+$hours,null,['class' => 'form-control', 'data-required' => true]) !!}
                                        </div>
                                        <div class="mt-repeater-input" style="padding: 0px;margin: 0px;padding-right: 10px;">
                                            <label class="control-label">Mins<span class="required">*</span></label>
                                            <br/>
                                            {!! Form::select('estimated_min',[''=>'Min']+$mins,null,['class' => 'form-control', 'data-required' => true]) !!}
                                        </div><br/>
                                        @if(!empty($auth) && $auth == ADMIN_USER_TYPE)
                                        <div class="mt-repeater-input">
                                            <label class="control-label">User Name<span class="required">*</span></label><br/>
                                            {!! Form::select('user_id',[''=>'Select User']+$users,null,['class' => 'form-control', 'data-required' => true,'id'=>'user_id']) !!}
                                        </div>
                                        <div class="mt-repeater-input">
                                            <label class="control-label">Task Date</label>
                                            <br/>
                                             {!! Form::text('task_date',null,['class' => 'input-group form-control input-small date date-picker  data-date-format="dd/mm/yyyy"', 'data-required' => false,'id'=>'','placeholder'=>'Task Date']) !!}
                                        </div>
                                        @endif

                                        <div class="mt-repeater-input">
                                            <a href="javascript:;" data-repeater-delete class="btn btn-danger mt-repeater-delete">
                                            <i class="fa fa-close"></i></a>
                                        </div>
                                        </div>
                                    </div>
                                <input type="submit" value="Save" class="btn btn-success pull-right" />
                                <a href="javascript:;" data-repeater-create class="btn btn-primary mt-repeater-add"><i class="fa fa-plus"></i></a>
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
<script src="{{ asset("themes/admin/assets/")}}/global/plugins/jquery-repeater/jquery.repeater.js" type="text/javascript"></script>
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/form-repeater.min.js" type="text/javascript"></script>
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
@endsection