@extends('admin.layouts.app')
@section('styles')
 
@endsection
@section('content')
<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box purple ">
                    <div class="portlet-title">
                        <div class="caption">
                        <i class="fa fa-file"></i> Monthly Reports</div>
                    </div>
                    <div class="portlet-body form">
                        <form class="form-horizontal" role="form" method="post" id="report-frm" action="{{ asset('/download-monthly-reports/ReportDownload')}}">
                            {{ csrf_field()}}
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">User Name<span class="required">*</span></label>
                                    <div class="col-md-5">
                                    {!! Form::select('user_id',[''=>'Select User Name']+$users,null,['class' => 'form-control', 'data-required' => true,'id'=>'user_id']) !!}
                                    @if($errors->has('user_id'))
                                        <span class="help-block" style="color: red;">
                                            {{$errors->first('user_id')}}
                                        </span>
                                    @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Client Name<span class="required">*</span></label>
                                    <div class="col-md-5">
                                    {!! Form::select('client_id',[''=>'Select Client Name']+$clients,null,['class' => 'form-control', 'data-required' => true,'id'=>'client_id']) !!}
                                    @if($errors->has('client_id'))
                                        <span class="help-block" style="color: red;">
                                            {{$errors->first('client_id')}}
                                        </span>
                                    @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Month<span class="required">*</span></label>
                                    <div class="col-md-5">
                                    {!! Form::select('months',[''=>'Select MM/YY']+$months,null,['class' => 'form-control', 'data-required' => false,'id'=>'country_list']) !!}
                                    @if($errors->has('months'))
                                        <span class="help-block" style="color: red;">
                                            {{$errors->first('months')}}
                                        </span>
                                    @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions" align="center">
                                <a class="btn purple" id="preview">Preview</a>
                                <input type="submit" name="download" value="Download" class="btn green">
                                <a href="{{asset('/download-monthly-reports')}}" class="btn yellow">Reload</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-modal-lg" id="report_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-full">
       <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Monthly Report</h4>
        </div>
        <div class="modal-body">
            <div id="monthly_report">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endsection
   

    @section('scripts')
   <script type="text/javascript">
      $(document).ready(function(){
		  $("#user_id").select2({
                placeholder: "Search User Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#client_id").select2({
                placeholder: "Search Client Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#country_list").select2({
                placeholder: "Search Month",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $('#preview').click(function(){
            var user_val = $('#user_id').val();
            var client_val = $('#client_id').val();
            var months_val = $('#country_list').val();
            
            if(user_val == ''){
                alert('User Name Is Require!');
                return false;
            }
            if(client_val == '')
            {
                alert('Client Name Is Require!');
                return false;   
            }
            if(months_val == '')
            {
                alert('Month Is Require!');
                return false;   
            }
            if(user_val != '' && client_val != '' && months_val != '')
            {
                jQuery('#report_view').modal();     
                $('#AjaxLoaderDiv').fadeIn('slow');
      
                var report_url = "{{asset('/download-monthly-reports/ReportPreview') }}";  
                $.ajax({
                  type: "GET",
                  url: report_url,
                  data: 
                  {
                      user_id: user_val,client_id: client_val,months: months_val
                  },
                  success: function (result)
                  {
                      $("#monthly_report").html(result);
                      $('#AjaxLoaderDiv').fadeOut('slow');
                  },
                  error: function (error) 
                  {
                      $('#AjaxLoaderDiv').fadeOut('slow');
                  }
              });

            }
        });

        /*$('#report-frm').submit(function () {
            
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
                            window.location = '{{ asset("/download-monthly-reports")}}';    
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
        });*/

      });


   </script>

    @endsection