@extends('admin.layouts.app')

@section('styles')
<link href="{{asset('themes/admin//assets')}}/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('themes/admin//assets')}}/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

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
                                        <label class="control-label">Holiday Title: <span class="required">*</span></label>                            
                                        {!! Form::text('holiday_title',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Holiday Title','rows'=>4]) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Status: <span class="required">*</span></label>                            
                                        {!! Form::select('status',['1'=>'Active','0'=>'Inactive'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                    
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">Holiday From Date:</label>
                                        <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                                            {!! Form::text('from_date',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select From Date','id'=>'start_date']) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>                                    
                                    <div class="col-md-6">
                                        <label class="control-label">Holiday To Date:</label>
                                        <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                                            {!! Form::text('to_date',null,['class' => 'form-control','placeholder' => 'Select To Date','id'=>'end_date','data-required' => true]) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                                                            
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


 <script src="{{ asset("themes/admin/assets/")}}/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/assets/")}}../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/assets/")}}/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/assets/")}}/pages/scripts/form-wizard.min.js" type="text/javascript"></script>

@endsection

