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
                            <span>OTP FORM</span>
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href=" ">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                            {!! Form::open(['url' => '/members/otpforms', 'method' => 'post'])!!}
                            {!! Form::token()!!}
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    <label class="control-label">OTP Number: <span class="required">*</span></label> 
                                    <div class="input-group">
                                        {!! Form::text('otp',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter OTP Number']) !!}
                                        @if($errors->has('otp'))
                                        <span class="help-block" style="color: red;">
                                            {{$errors->first('otp')}}
                                        </span>
                                    @endif
                                    </div>
                                </div>                                         
                            </div><div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12" align="center">
                                    <input type="submit" value="Save" class="btn btn-success" />
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
 
@endsection

