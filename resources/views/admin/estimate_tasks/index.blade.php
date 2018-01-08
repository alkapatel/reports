@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            @include($moduleViewName.".search")

            <div class="clearfix"></div>    
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div>
                  
                    @if($btnAdd)
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $add_url }}">Add New</a>
                    @endif                     

                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                    
                               <th width="15%">Username</th>                                   
                               <th width="15%">Project</th>                                   
                               <th width="15%">Task/Title</th>                           
                               <th width="5%">Status</th>                           
                               <th width="5%">Estimated Time</th>
                               <th width="5%">Actual Time</th>
                               <th width="10%">Task Date<br/><i style="color: blue; font-size: 12px">CreatedAt</i></th>      
                               <th width="5%" data-orderable="false">Action</th>
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
          <h4 class="modal-title">Estimated Task Details</h4>
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
      
      var task_url="{{asset('/estimated-tasks/view') }}";  
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
        $("#project_id").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });


        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
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
                    data.search_title = $("#search-frm input[name='search_title']").val();
                    data.search_esti_hour = $("#search-frm input[name='search_esti_hour']").val();
                    data.search_esti_min = $("#search-frm input[name='search_esti_min']").val();
                    data.search_esti_hour_op = $("#search-frm select[name='search_esti_hour_op']").val();
                    data.search_esti_min_op = $("#search-frm select[name='search_esti_min_op']").val();
                    data.search_act_hour_op = $("#search-frm select[name='search_act_hour_op']").val();
                    data.search_act_hour = $("#search-frm input[name='search_act_hour']").val();
                    data.search_act_min_op = $("#search-frm select[name='search_act_min_op']").val();
                    data.search_act_min = $("#search-frm input[name='search_act_min']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
                    data.search_user = $("#search-frm select[name='search_user']").val();
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },                                             
                { data: 'user_name', name: '{{ TBL_USERS }}.name' },
                { data: 'project_name', name: '{{ TBL_PROJECT}}.id' },
                { data: 'task', name: 'task' },
                { data: 'status', name: 'status' },
                { data: 'estimated_total_time', name: 'estimated_total_time' },
                { data: 'actual_total_time', name: 'actual_total_time' },
                { data: 'task_date', name: 'task_date' },
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection
