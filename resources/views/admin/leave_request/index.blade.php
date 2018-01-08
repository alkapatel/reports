@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            @include($moduleViewName.".search")           

            <div class="clearfix"></div>            
            <!-- <a class="btn btn-success pull-right btn-download">Download CSV</a> -->
            <div class="clearfix"></div>
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div>
                  
                    @if($btnAdd)
                        <a class="btn btn-default btn-sm mTop5" href="{{ $add_url }}" style="margin-left: 700px; margin-top: 8px">Add New</a>
                    @endif                     

                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                    
                               <th width="20%">Username<br/><i style="color: blue; font-size: 10px">Created By</i></th>                           
                               <th width="15%">From Date</th>                           
                               <th width="15%">To Date</th>                           
                               <th width="5%">Days</th>                           
                               <th width="20%">Leave Description</th>               
                               <th width="5%">Status</th>               
                               <th width="10%">Created At</th>                           
                               <th width="40%" data-orderable="false">Action</th>
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
<div class="modal fade" id="leave_reject" role="dialog">
    <div class="modal-dialog">  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Enter Reason For Reject Leave Request.</h4>
        </div>
        <div class="modal-body">
          <p><form id="reason_form">
            <textarea rows="3" cols="75" id="reason"></textarea>
            <input type="submit" name="submit" id="reason_submit" class="btn btn-primary pull-right">

          </form></p>
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
                placeholder: "Search User",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
       $('#reason_form').submit(function () {
            var reason = $('#reason').val();
           
            if(reason == ''){
                alert('please enater valid reason!');
                return false;
            }else{                        
                var id = $('#reject_action').attr('data');                        
                var status = 2;
                var leave_url="{{asset('leave-request/status') }}";  
                
                $.ajax({
                    type: "GET",
                    url: leave_url,
                    data: 
                    {leave_id:id,status:status,reason:reason},
                    success: function (result)
                    {
                        if (result.flag == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success',delay: 4000});
                            setTimeout(function(){
                                window.location = '{{ $list_url }}';
                                //window.location.reload();
                            },3000);
                        }
                    }
                });
            }
		   return false;
        });
        
        $(document).on('click', '.rejected', function () {
            $text = 'Are you sure you want to reject the request?';
            if (confirm($text))
            {

                jQuery('#leave_reject').modal();  
               
            }
            return false;
        });
        $(document).on('click', '.accepted', function () {
            $text = 'Are you sure you want to accept the request?';
            if (confirm($text)==true){
        
                var id = $(this).attr('data');
                var status = 1;
                var leave_url="{{asset('leave-request/status') }}";  
                $.ajax({
                    type: "GET",
                    url: leave_url,
                    data:{leave_id:id,status:status},
                    success: function (result)
                    {
                        if (result.flag == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success',delay: 4000});
                            setTimeout(function(){
                                window.location = '{{ $list_url }}';
                                //window.location.reload();
                            },3000);
                        }
                    }
                });
             }
             return false;
        });

        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });


        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
            "<'row'<'col-xs-12't>>"+
            "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
            processing: true,
            serverSide: true,
            searching: false,
            dom: 'lBfrtip',
            buttons: [
            {   extend: 'csvHtml5',
                text: 'Download CSV',
                exportOptions: {
                    modifier: {
                        //search: 'none',
                        selected: false,
                        //columns: [0, 1],
                    }
                }
            }
            ],
            lengthMenu:
              [
                [100,200,300,400,500],
                [100,200,300,400,500]
              ],            
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_user = $("#search-frm select[name='search_user']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_start_leave = $("#search-frm input[name='search_start_leave']").val();
                    data.search_end_leave = $("#search-frm input[name='search_end_leave']").val();
                    data.search_id = $("#search-frm input[name='search_id']").val();
					data.search_month = $("#search-frm select[name='search_month']").val();

                }
            },            
            "order": [[ '0', "desc" ]],
            columns: [
                { data: 'id', name: 'id' },                                             
                { data: 'username', name: '{{TBL_USERS}}.name' },
                { data: 'from_date', name: 'from_date' },                                             
                { data: 'to_date', name: 'to_date' },                                             
                { data: 'days', name: 'to_date' },                                             
                { data: 'description', name: 'description' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}             
            ],
            
        }); 

        /*var data = oTableCustom.buttons.exportData( {
            columns: ':id'
        } );*/
        //oTableCustom.column( 'id:name' ).data();
        //oTableCustom.cell( {focused:true} ).data();
        //oTableCustom.columns( [0, 1] ).data();
    });
    </script>

    
@endsection