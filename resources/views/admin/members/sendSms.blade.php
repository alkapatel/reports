@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <div class="container">
        <div class="portlet box blue ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-envelope"></i>
                    <span class="caption-subject font-dark sbold">Send SMS Form</span>
                </div>
                
            </div>
            <div class="portlet-body form">
                {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'form-horizontal', 'id' => 'main-frm1']) !!}
				<div class="form-body">
                        <a class="btn btn-danger pull-right btn-uncheck">Un Check All</a>
                        <a class="btn btn-primary pull-right btn-check" style="margin-right: 5px;">Check All</a>
                <div class="clearfix">&nbsp;</div>
                <div class="clearfix">&nbsp;</div>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Message : </label>
                            <div class="col-md-8">
                                {!! Form::textarea('sms_body',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type Message','id'=>'textareaChars','rows'=>5,'cols'=>96,'maxlength'=>160]) !!}
                                <br/>
                                <span id="chars" style="color: red;">160</span> characters remaining
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Member List : </label>
                            <div class="col-md-5">
                                <div class="mt-checkbox-list">
                                    @foreach($members as $member)
                                    <label class="mt-checkbox mt-checkbox-outline">
                                        <input type="checkbox" name='members[{{$member->id}}]' value='{{$member->mobile}}' data-id='{{$member->id}}' checked="checked" class="chkids"> {{ $member->firstname }} {{ $member->middlename }} {{ $member->lastname }} - {{ $member->id }}
                                    <span></span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-3">
                                @foreach($members as $member)
                                <input type="text" name="new_mobile[{{$member->id}}]" class="form-control" placeholder="Type New Mobile No for - {{$member->id}}" data-id='{{$member->id }}'>
                                @endforeach
                            </div>
                        </div>                         
                    <div align="center">
                        <div class="row">
                                <input type="submit" class="btn green" value="Send Sms" align="right">
                        </div>
                    </div>
                {!! Form::close()!!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var maxLength = 160;
        $('textarea').keyup(function() {
          var length = $(this).val().length;
          var length = maxLength-length;
          $('#chars').text(length);
        });
		$(".btn-check").click(function(){
            $(".chkids").prop("checked",true);
        });
        
        $(".btn-uncheck").click(function(){
            $(".chkids").prop("checked",false);
        });
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
                            window.location.reload();
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