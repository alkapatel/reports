
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
                            {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm']) !!}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Family Members Details</legend>
                        <div class="row">
                            <div class="col-md-12" align="right">
                                <div class="form-group last">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;"> 
                                        @if(!empty($formObj->image))
                                            <img src='{{ asset("/uploads/members/$formObj->member_id/family_members/$formObj->id/$formObj->image")}}' alt="No Image" />
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
                        <?php $auth_user = Auth::guard("admins")->check();
                            if($auth_user)
                            {
                                $auth_id = Auth::guard("admins")->user()->id;
                                if($auth_id == 1)
                        ?>                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                <label class="control-label">Member <span class="required">*</span></label>
                                {!! Form::select('member_id',[''=>'Select Member Name']+$members,null,['class' => 'form-control', 'data-required' => true,'id'=>'member_id']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                        <?php } ?>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Name:<span class="required">*</span></label>
                                    {!! Form::text('name',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Name']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Relation:<span class="required">*</span></label>
                                    {!! Form::text('relation_with_primary_member',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Relation']) !!}
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="control-label">Blood Group:</label>       
                                 {!! Form::select('blood_group_id',[''=>'Select Blood Group']+$blood_groups,null,['class' => 'form-control', 'data-required' => false]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Occupation:<span class="required">*</span></label>                             
                                {!! Form::text('occupation',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Occupation']) !!}
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
        $("#member_id").select2({
                placeholder: "Search Member Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
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

 <script src="{{ asset("themes/admin/assets/")}}/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
 
@endsection

