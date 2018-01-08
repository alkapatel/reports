@extends('admin.layouts.app')

@section('styles')
<link href="{{ asset("/themes/admin/assets")}}/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset("/themes/admin/assets")}}/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
@endsection

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
                                        <label class="control-label">Name: <span class="required">*</span></label> 
                                        <div class="input-group">
                                        {!! Form::text('name',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Name']) !!}
                                        <span class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                        </span>
                                        </div>                                            
                                    </div>                                                 
                                    <div class="col-md-6">
                                        <label class="control-label">Email: <span class="required">*</span></label>
                                        <div class="input-group">                            
                                            {!! Form::text('email',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Email']) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">Phone No:</label>
                                        <div class="input-group">                            
                                        {!! Form::text('phone',null,['class' => 'form-control','placeholder' => 'Enter Phone Number']) !!}
                                        <span class="input-group-addon">
                                                <i class="fa fa-phone"></i>
                                        </span>
                                        </div>                                            
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Country: </label>                            
                                        {!! Form::text('country',null,['class' => 'form-control','placeholder' => 'Enter Country']) !!}
                                    </div>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">State: </label>                            
                                        {!! Form::text('state',null,['class' => 'form-control','placeholder' => 'Enter State']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">City: </label>                            
                                        {!! Form::text('city',null,['class' => 'form-control','placeholder' => 'Enter City']) !!}
                                    </div>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ Form::checkbox('send_email', 0, null, ['class' => 'field', 'style' =>"zoom:1.7"]) }}
                                        <label class="control-label"> Send Email Report </label>                            
                                    </div>
                                </div>
							<div class="clearfix">&nbsp;</div>
								 <div class="row">
                                    <div class="col-md-12">
                                    <div class="portlet">
                                    <div class="portlet-body" style="display: block;">
                                        <label class="control-label">Users:</label>
                                            <select class="select_users" multiple="multiple" name="users[]">
                                    @foreach($users as $row)
                                            <option {{ in_array($row->id, $list_tags) ? 'selected':''}} value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                            </select>                                            
                                    </div>
                                    </div>
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

<style type="text/css">
    
</style>
@endsection

@section('scripts')
<script src="{{ asset('/themes/admin/assets')}}/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="{{ asset('/themes/admin/assets')}}/pages/scripts/components-select2.min.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () { 
		$(".select_users").select2({
                placeholder: "Search Users",
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

