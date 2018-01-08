@extends('admin.layouts.bopal_app')
@section('content')
<div class="page-content">
    <div class="container">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                <i class="fa fa-file"></i>OTP Form </div>
                
            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->

                {!! Form::open(['url' => '/members/check-mobile', 'method' => 'post','class'=>'form-horizontal','id'=>'mobile_form'])!!}
                    {!! Form::token()!!}

                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Mobile Number <span class="required">*</span></label>
                            <div class="col-md-4">
                                {!! Form::text('mobile_no',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Mobile Number','id'=>'mobile_no_id']) !!}
                            </div>
                            <div class="col-md-4">
                                <input  type="submit" class="btn green" value="Send OTP" id="submit_mobile_no">
                            </div>
                        </div>
                    </div>
                </form>
                {!! Form::open(['url' => '/members/check-otp-num', 'method' => 'post','class'=>'form-horizontal','id'=>'otp_form'])!!}
                    {!! Form::token()!!}
                    <div class="form-body" id="otp_form_div">
                        <div class="form-group">
                            <label class="col-md-4 control-label">OTP <span class="required">*</span></label>
                            <div class="col-md-4">
                                {!! Form::text('otp_no',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter OTP','id'=>'otp_no_id']) !!}
                            </div>
                            <div class="col-md-4">
                                <input  type="submit" class="btn green" value="Edit Your Detail" id="submit_otp">
                            </div>
                        </div>
                    </div>
                </form><hr/>
                <?php
                $session = session('member_id');
                    $name = '';
                    $number = '';
                    $url = '';
                    if(!empty($session)){
                        $user = App\Models\Member::find($session);
                        $name =ucfirst($user->firstname).' '.ucfirst($user->middlename).' '.ucfirst($user->lastname);
                        $number = $user->mobile;
                        $url = route('members.edit',["id" => $user->id]);
                ?>
                <div class="row" id="link_div">
                    <div class="col-md-12" align="center">
                        <span id="name"> <?php echo $name;?> </span>  [<span id="number"> <?php echo $number;?> </span> ]   
                        <a href="{{$url}}" class="btn-click">  Edit Your Detail  </a>
                    </div>
                </div>
                <?php }?> 
                <div class="row" id="link_div" style="display: none">
                    <div class="col-md-12" align="center">
                        <span id="name">  </span>  [<span id="number">  </span> ]   
                        <a href="" class="btn-click">  Edit Your Detail  </a>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-12" align="center">
                        All Member list :
                        <a href="{{route('members.index')}}"> Click Here </a>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#mobile_form').submit(function () {
            
            if ($(this).parsley('isValid'))
            {
                $('#submit_mobile_no').attr("disabled", true);

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
                            $('#submit_mobile_no').attr("disabled", false);                            
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                            $('#submit_mobile_no').attr("disabled", false);
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

        $('#otp_form').submit(function () {
            
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
                            //window.location.reload();
                            $('#link_div').show();
                            $('.btn-click').attr('href',result.url)
                            $('#name').html(result.fullname)
                            $('#number').html(result.number)
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
