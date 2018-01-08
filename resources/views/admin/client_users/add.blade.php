@extends('admin.layouts.app')


@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i>
                           {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                           {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                           
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">Client Name <span class="required">*</span></label>                                        
                                        {!! Form::select('client_id',[''=>'Select Client']+$clients,null,['class' => 'form-control', 'data-required' => false,'id'=>'client_id']) !!}
                                    </div>
                                   
                                    <div class="col-md-6">
                                        <label class="control-label">User Name: <span class="required">*</span></label> 
                                        <div class="input-group">
                                        {!! Form::text('name',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Name']) !!}
                                        <span class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                        </span>
                                        </div>                                            
                                    </div>                                                 
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">User Email: <span class="required">*</span></label>
                                        <div class="input-group">                            
                                            {!! Form::text('email',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Email']) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Status: <span class="required">*</span></label>                            
                                        {!! Form::select('status',['1'=>'Active','0'=>'Inactive'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>                                    
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ Form::checkbox('send_email', 0, null, ['class' => 'field', 'style' =>"zoom:1.6"]) }}
                                        <label class="control-label"> Send Email Report </label>                            
                                    </div>                                    
                                </div>                                
                                <div class="clearfix">&nbsp;</div>
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
		$("#client_id").select2({
                placeholder: "Search Client Name",
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
</script>
@endsection

