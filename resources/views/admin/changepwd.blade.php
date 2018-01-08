@extends('admin.layouts.app')

@section('breadcrumb')

<?php
$pageTitle = "Change your password";

$bred_crumb_array = array(
    'Home' => url('admin'),
    'Change your password' => '',
);
?>



@stop

@section('content')

<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-key"></i>
                            Change Password
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form class="form" id="main-frm">
                        {!! csrf_field() !!}    
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label">Old Password</label>
                                <input type="password" class="form-control" name="password" data-required="true" /> 
                            </div>
                            <div class="form-group">
                                <label class="control-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" data-required="true"/> 
                            </div>
                            <div class="form-group">
                                <label class="control-label">Confirm Password</label>
                                <input type="password" class="form-control" name="new_password_confirmation" data-required="true" /> 
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Update" class="btn btn-success pull-right"/>
                            </div>
                            <div class="clearfix">&nbsp;</div>
                        </div>
                        </form>    
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
                            $('#main-frm').trigger("reset")
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


