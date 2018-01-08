@extends('admin.layouts.app')

@section('breadcrumb')

<?php 
$pageTitle = "Reset your password";

$bred_crumb_array = array(
    'Home' => url('admin'),
    'Forgot Password' => '',
);
?>

@include('admin.includes.breadcrumb')

@stop

@section('content')

<div class="container content">

  <div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
      @include('admin.includes.formErrors')        
      {!! Form::open(['url' => 'backend/forgot-password', 'files' => true, 'class' => 'sky-form form form-group reg-page', 'id' => 'sky-form3']) !!}        
        <div class="reg-header">            
          <h2>Forgot Password</h2>
        </div>

        <div class="input-group margin-bottom-20 col-md-12">                        
          {!! Form::text('email',null,['data-required' => 'true','class' => 'form-control col col-12', 'placeholder' => 'Enter Your Email', 'data-type' => 'email']) !!}
        </div>                    

        <div class="row">
          <div class="col-md-6">
              <a href="{{ url('backend/login')}}" class="btn-u btn-u-blue">Back To Login</a>            
          </div>
          <div class="col-md-6">
            <button type="submit" class="btn-u pull-right">GET Password</button>                        
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">            
          </div>
        </div>
        
      </form>            
    </div>
  </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('#sky-form3').submit(function(){
            
              if($(this).parsley('isValid'))
              {              
                    $('#AjaxLoaderDiv').fadeIn('slow');  
                    $.ajax({
                        type: "POST",
                        url:  $(this).attr("action"),
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        enctype: 'multipart/form-data',
                        success: function (result)
                        {                
                            $('#AjaxLoaderDiv').fadeOut('slow');
                            if(result.status == 1)
                            {
                                $.bootstrapGrowl(result.msg, {type: 'success',delay: 4000});                    
                                // window.location = result.url;    
                                $('#sky-form3').trigger("reset")
                            }   
                            else
                            {
                                $.bootstrapGrowl(result.msg, {type: 'danger',delay: 4000});                    
                            }
                        },
                        error: function(error){
                            $('#AjaxLoaderDiv').fadeOut('slow');
                            $.bootstrapGrowl("Internal server error !", {type: 'danger',delay: 4000});                    
                        }
                    });                        
              }
              return false;
        });      
    });    
</script>
@stop

