@extends('admin.layouts.app')
<?php 
$auth = Auth::guard('admins')->user()->user_type_id;
?>
@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file-text-o"></i>
                           {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                           {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                            <h4 align="center"><b>Delivery Details</b></h4><hr/>
                            @if(!empty($auth) && $auth == ADMIN_USER_TYPE)
                            <div class="row">
                                <div class="col-md-12">                                
                                    {!! Form::select('user_id',[''=>'Select User']+$users,null,['class' => 'form-control', 'data-required' => true,'disabled' => 'disabled']) !!}
                                </div>
                            </div><br/>
                            @endif
                                <div class="row">
                                <div class="col-md-12">                                
                                    <div class="col-md-3">
                                        <label class="control-label">Project <span class="required">*</span></label>                                        
                                        {!! Form::select('project_id',[''=>'Select Project']+$projects,null,['class' => 'form-control', 'data-required' => true,'disabled' => 'disabled']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Task <span class="required">*</span></label>                                        
                                        {!! Form::text('task',null,['class' => 'form-control','placeholder' => 'Enter Task Title', 'data-required' => true,'disabled' => 'disabled']) !!}
                                    </div> 
                                    <div class="col-md-2" style="padding: 0px;margin: 0px;">
                                        <label class="control-label">Estimated Hours<span class="required">*</span></label>                            
                                        {!! Form::select('estimated_hour',[''=>'Hour']+$hours,null,['class' => 'form-control', 'data-required' => true,'disabled' => 'disabled']) !!}
                                    </div>
                                    <div class="col-md-1" style="padding: 0px;margin: 0px;">
                                        <label class="control-label">Mins <span class="required">*</span></label>                            
                                        {!! Form::select('estimated_min',[''=>'Min']+$mins,null,['class' => 'form-control', 'data-required' => true,'disabled' => 'disabled']) !!}
                                    </div>
                                </div> 
                                </div><hr/>
                                <div class="clearfix">&nbsp;</div>
                             <div class="row">
                                 <div class="col-md-12">
                                    <div class="col-md-3">
                                        <label class="control-label">Status <span class="required">*</span></label>                            
                                        {!! Form::select('status',['1'=>'Completed','2'=>'In Progress','3'=>'Skip'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Delivery Task Description</label>
                                        {!! Form::textarea('delivery_description',null,['class' => 'form-control','placeholder' => 'Enter Delivery Task Description', 'data-required' => false,'rows'=>3]) !!}
                                    </div>
                                    <div class="col-md-2" style="padding: 0px;margin: 0px;">
                                        <label class="control-label">Actual Hours<span class="required">*</span></label>                  
                                        {!! Form::select('actual_hour',[''=>'Hour']+$hours,null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                    <div class="col-md-1" style="padding: 0px;margin: 0px;">
                                        <label class="control-label">Mins <span class="required">*</span></label>                            
                                        {!!Form::select('actual_min',$mins,null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                    
                                 </div>
                             </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="Save" class="btn btn-success pull-right" />
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
@endsection

