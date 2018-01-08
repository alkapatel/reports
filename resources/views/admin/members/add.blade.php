@extends('admin.layouts.bopal_app')

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
                            {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Member Details</legend>
                        <div class="row">
                            <div class="col-md-12" align="right">
                                <div class="form-group last">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                        @if(!empty($formObj->image))
                                            <img src='{{ asset("/uploads/members/$formObj->id/$formObj->image")}}' alt="" />
                                        @else
                                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> 
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">First name<span class="required">*</span></label>
                                    {!! Form::text('firstname',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Name']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Middle name<span class="required">*</span></label>
                                    {!! Form::text('middlename',null,['class' => 'form-control', 'data-required' => true,'placeholder' => "Enter Father's Name"]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Last name<span class="required">*</span></label>
                                    {!! Form::text('lastname',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Surname']) !!}
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="control-label">Form Number:</label> 
                                {!! Form::text('form_number',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Form Number']) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Mobile:<span class="required">*</span></label>
                                {!! Form::text('mobile',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Mobile']) !!}
                            </div>
                        </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="control-label">Village Name:</label>
                                {!! Form::select('village_id',[''=>'Select Village']+$villages,null,['class' => 'form-control', 'data-required' => false]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Address:</label>
                                {!! Form::text('address',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Address']) !!}
                            </div>
                        </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                        <div class="col-md-12">    
                            <div class="col-md-6">
                                <label class="control-label">Building:</label>
                                {!! Form::text('building',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Building Number']) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Locality:</label>
                                {!! Form::text('locality',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Locality']) !!}
                            </div>
                        </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                        <div class="col-md-12">    
                            <div class="col-md-6">
                                <label class="control-label">Blood Group:</label>       
                                 {!! Form::select('blood_group_id',[''=>'Select Blood Group']+$blood_groups,null,['class' => 'form-control', 'data-required' => false]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Profession:</label>
                                {!! Form::text('profession',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Profession']) !!}
                            </div>
                        </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="control-label">Organization:</label>
                                {!! Form::text('organization',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Organization']) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Family Member Count:</label>
                                {!! Form::text('family_member_count',null,['class' => 'form-control', 'data-required' => false]) !!}
                            </div>
                        </div>
                        </div>
							<div class="clearfix">&nbsp;</div>                       
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                <label class="control-label">Group Leader : </label>
                                {!! Form::select('group_leader',[''=>'Search']+$members,null,['class' => 'form-control', 'data-required' => false,'id'=>'group_id']) !!}
                                </div>
                                <?php $auth_user = Auth::guard("admins")->check();
                                if($auth_user)
                                {
                                    $auth_id = Auth::guard("admins")->user()->id;
                                    if($auth_id == SUPER_ADMIN_ID)
                                ?>
                                <div class="col-md-6">
                                    <label class="control-label">Status: <span class="required">*</span></label>                            
                                    {!! Form::select('status',['1'=>'Active','0'=>'inactive'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                </div>
                                <?php } ?>
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

 
 <script src="{{ asset("themes/admin/assets/")}}/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
@endsection

