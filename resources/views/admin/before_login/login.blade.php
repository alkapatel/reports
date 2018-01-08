@extends('admin.layouts.login')

@section('content')

{!! Form::open(['route' => 'check_admin_login', 'files' => true, 'class' => 'login-form', 'id' => 'parsely-frm']) !!}   
<h3 class="form-title font-green">Sign In</h3>

@if(Session::has('error_message'))
<div class="alert alert-danger">
    <button class="close" data-close="alert"></button>
    <span>{!! Session::get('error_message') !!}</span>
</div>
@endif    

<div class="form-group">
    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
    <label class="control-label visible-ie8 visible-ie9">Email</label>

    {!! Form::text('email',null,['data-required' => 'true','class' => 'form-control form-control-solid placeholder-no-fix', 'placeholder' => 'Enter Your Email']) !!}
</div>
<div class="form-group">
    <label class="control-label visible-ie8 visible-ie9">Password</label>

    {!! Form::password('password',['data-required' => 'true','class' => 'form-control form-control-solid placeholder-no-fix', 'placeholder' => 'Enter Your Password']) !!}
</div>
<div class="clearfix"></div>

<div class="form-actions">
    <button type="submit" class="btn green uppercase pull-right">Login</button>
</div>

<div class="clearfix"></div>
{!! Form::close() !!}  
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.login-form').submit(function () {

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
                            @php
                                $url = url()->previous();
                            @endphp
                            window.location = '{{  $url }}';
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                        }
                    },
                    error: function (error)
                    {
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                    }
                });
            }
            return false;
        });
    });
</script>
@stop
