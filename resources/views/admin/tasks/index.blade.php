@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            @include($moduleViewName.".search")

            <div class="clearfix"></div>
			<a class="btn btn-default pull-right">
              Total Hours: <span id="overall-hours"> 0 </span>
            </a>    
            <div class="clearfix">&nbsp;</div>
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div>
                  
                   @if($btnAdd)
                      
                        <a class="btn btn-primary btn-sm mTop5 pull-right" href="{{ $add_url }}" >Add New</a>
                        <a class="btn btn-primary btn-sm mTop5 btn-download pull-right" style="margin-right: 10px;">Download CSV</a>
                        <a class="btn btn-primary btn-sm mTop5 btn-download-xls pull-right" style="margin-right: 10px">Download XLS</a>
                      
                    @endif                       

                </div>
                <div class="portlet-body">                    
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="2%">ID</th>                                    
                               <th width="10%">UserName</th>                                    
                               <th width="12%">Project</th>
                               <th width="20%">Task</th>
                               <th width="10%">Status<br/>
                                    <i class="fa fa-clock-o" aria-hidden="true"></i> Hour</th>
                               <th width="18%">Link</th> 
                               <th width="13%">Task Date<br/><i style="color: blue; font-size: 12px">CreatedAt</i></th>                          
                               <th width="8%" data-orderable="false">Action</th>
                            </tr>
                        </thead>                                         
                        <tbody>
                        </tbody>
                    </table>                                              
                </div>
            </div>              
        </div>
    </div>
</div>
</div>            
<div class="modal fade bs-modal-lg" id="task_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Task Details</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="task_detail_table">
            
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>
@endsection

@section('styles')
  
@endsection

@section('scripts')
    <script type="text/javascript">
    
function openView($id){
      jQuery('#task_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
      
      var task_url="{{asset('/tasks/view') }}";  
      $.ajax({
          type: "GET",
          url: task_url,
          data: 
          {
              task_id: $id
          },
          success: function (result)
          {
              $("#task_detail_table").html(result);
              $('#AjaxLoaderDiv').fadeOut('slow');
          },
          error: function (error) 
          {
              $('#AjaxLoaderDiv').fadeOut('slow');
          }
      });

    }

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
        $("#project_id").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
		$(document).on('click', '.btn-download', function () {
            $("#is_download").val(1);
            setTimeout(function(){
                $params = $("#search-frm").serialize();
                $url = '{{ url('tasks') }}'+'?'+$params;
                window.location = $url;
            },400);                       
        });
		$(document).on('click', '.btn-download-xls', function () {
            $("#is_download_xls").val(1);
            setTimeout(function(){
                $params = $("#search-frm").serialize();
                $url = '{{ url('tasks') }}'+'?'+$params;
                window.location = $url;
            },400);                       
        });
		
        $(document).ready(function() 
        {
          $('#example').DataTable( 
          {
              "order": [[ 3, "desc" ]]
              });
        });


        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });
        $( document ).ajaxComplete(function() {
            setTimeout(function(){
                $('[data-toggle="tooltip"]').tooltip();
            },500);  
        });

        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
            "<'row'<'col-xs-12't>>"+
            "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu:
              [
                [100,200,300,400,500],
                [100,200,300,400,500]
              ],
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_id = $("#search-frm input[name='search_id']").val();
                    data.search_project = $("#search-frm select[name='search_project']").val();
                    data.search_user = $("#search-frm select[name='search_user']").val();
                    data.search_client = $("#search-frm select[name='search_client']").val();
                    data.search_title = $("#search-frm input[name='search_title']").val();
                    data.search_hour = $("#search-frm input[name='search_hour']").val();
                    data.search_min = $("#search-frm input[name='search_min']").val();
                    data.search_hour_op = $("#search-frm select[name='search_hour_op']").val();
                    data.search_min_op = $("#search-frm select[name='search_min_op']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
                 
                },
				dataSrc: function(response){
                    $("#overall-hours").html(response.hours);
					$("#is_total").val(response.hours);
                    return response.data;
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },
                { data: 'user_name', name: '{{ TBL_USERS }}.name' },
                { data: 'project_name', name: '{{ TBL_PROJECT }}.title' },
                { data: 'title', name: 'title' },
                { data: 'status', name: 'status' },
                { data: 'ref_link', name: 'ref_link' },
                { data: 'task_date', name: 'task_date' },
                { data: 'action', orderable: false, searchable: false}             
            ],
            //dom: 'Bfrtip',       
            //  buttons: ['csvHtml5']
        
        });        
    });
    </script>
@endsection
