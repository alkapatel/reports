@extends('admin.layouts.app')

@section('breadcrumb')

<?php
$pageTitle = "Change your password";

$bred_crumb_array = array(
    'Home' => url('/'),
    'Change your password' => '',
);
$profile_pic = Auth::guard('admins')->user()->image;
$user_id = Auth::guard('admins')->user()->id;

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
                            <i class="fa fa-user"></i>
                            Edit Your Profile
                        </div>
                    </div>
                    <div class="portlet-body">
                        {!! Form::model(Auth::guard("admins")->user(), ['route' => 'update_profile', 'class' => 'form', 'id' => 'main-frm', 'enctype'=>'multipart/form-data']) !!}
                            
                            <div class="form-body">
                                <div class="row">
                                        <div align="center">
                                        @if($profile_pic)    
                                            <img src='{{asset("uploads/users/$user_id/$formObj->image")}}' class="img-thumbnail" alt="Profile Image" height="200" width="200"/>
                                        @else
                                            <img alt="Profile Image" class="img-thumbnail" src="{{ asset("uploads/users/default-user.jpg")}}" height="200" width="200"/>
                                        @endif
                                        </div> 
                                </div>&nbsp;
                                <div class="row">                                       
                                    <div align="center">
                                        <div class="row fileupload-buttonbar">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <span class="btn green fileinput-button">
                                                    {!!Form::file('image',['id'=>'profile_pic'])!!}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div><hr/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">First Name</label>                                    
                                    {!! Form::text('firstname',null,['placeholder' => 'Enter Your First Name','data-required' => true, 'class' => "form-control"]) !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Last Name</label>                                    
                                    {!! Form::text('lastname',null,['placeholder' => 'Enter Your Last Name','data-required' => true, 'class' => "form-control"]) !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Email</label>                                    
                                    {!! Form::text('email',null,['placeholder' => 'Enter Your Email','data-required' => true, 'data-type' => "email",'class' => "form-control" ,'disabled' => 'disabled']) !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Address</label>                                    
                                    {!! Form::textarea('address',null,['placeholder' => 'Enter Your Address','data-required' => true, 'class' => "form-control",'rows'=>3]) !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Phone</label>                                    
                                    {!! Form::text('phone',null,['placeholder' => 'Enter Your Phone','data-required' => true, 'class' => "form-control"]) !!}
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
                            window.location = '{{ route("edit_profile") }}';                   
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


