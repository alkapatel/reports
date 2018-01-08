
@extends('admin.layouts.app')

@section('breadcrumb')


@stop
<?php
    if(!empty($formObj->date) && isset($formObj->date))
    $date = $formObj->date;
    else
    $date = date('Y-m-d');
?>

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
                                <div class="row ">
                                    <div class="col-md-4">
                                        <label class="control-label">Title:<span class="required">*</span></label>                                        
                                        {!! Form::text('title',null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">Date:<span class="required">*</span></label>
                                        <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">

                                            {!! Form::text('date',$date,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select Date','id'=>'start_date']) !!}
                                            
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="control-label">Amount:<span class="required">*</span></label>                                        
                                        {!! Form::text('amount',null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>                                      
                                </div>                   
                                <div class="clearfix">&nbsp;</div>
                                <div class="row ">
                                    
                                   <div class="col-md-12">
                                        <label class="control-label">Description:</label>
                                        {!! Form::text('description_bill',null,['class' => 'form-control']) !!}
                                    </div>                             
                                </div>  
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">Scanned Bill:</label>
                                        {!!Form::file('scanned_bill')!!}
                                    </div>  
                                    <div class="col-md-12">
                                        <input type="submit" value="{{ $buttonText}}" class="btn btn-success pull-right" id="submit_btn" />
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
                $('#submit_btn').attr("disabled", true);
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
                            $('#submit_btn').attr("disabled", flase);
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

