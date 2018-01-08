@extends('admin.layouts.app')

@section('breadcrumb')
@stop
@section('styles')
 <link href="{{ asset("themes/admin/assets/")}}/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
 <style type="text/css">
fieldset.scheduler-border 
{
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
}
legend.scheduler-border 
{
    font-size: 1.2em !important;
    font-weight: bold !important;
    text-align: left !important;
    width:auto;
    padding:0 10px;
    border-bottom:none;
}
</style>
@endsection
@section('content')

<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>
                            {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                            {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm']) !!}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Member Details</legend>
                        <div class="row">
                            <div class="col-md-12" align="right">
                                <div class="form-group last">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                        @if(!empty($formObj->image))
                                            <img src='{{ asset("/uploads/members/$formObj->id/$formObj->image")}}' alt="" />
                                        @endif 
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                    <div>
                                        <span class="btn default btn-file">
                                            <span class="fileinput-new"> Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="image"> </span>
                                        <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                    </div>
                                </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">First name<span class="required">*</span></label>
                                    {!! Form::text('firstname',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter First Name']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Last name<span class="required">*</span></label>
                                    {!! Form::text('lastname',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Last Name']) !!}
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="control-label">Mobile:<span class="required">*</span></label>                                        
                                {!! Form::text('phone',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Mobile']) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">WhatsApp Number:<span class="required">*</span></label>                             
                                {!! Form::text('whats_app_phone',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter WhatsApp Number']) !!}
                            </div>
                        </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                        <div class="col-md-12">    
                            <div class="col-md-6">
                                <label class="control-label">Village Name:<span class="required">*</span></label>                                        
                                {!! Form::text('village_name',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Village Name']) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Professional:<span class="required">*</span></label>                                        
                                {!! Form::text('professional',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Professional']) !!}
                            </div>
                        </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Address:<span class="required">*</span></label>                                        
                                    {!! Form::text('address',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Address']) !!}
                                </div>
                                </div>
                            </div>
                        </div>
                        </fieldset>
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

<script src="{{ asset("themes/admin/assets/")}}/global/plugins/jquery-repeater/jquery.repeater.js" type="text/javascript"></script> 
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/form-repeater.min.js" type="text/javascript"></script>
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
 <script src="{{ asset("themes/admin/assets/")}}/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
@endsection

