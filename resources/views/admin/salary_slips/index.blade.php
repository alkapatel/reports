@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            @include($moduleViewName.".search")           
			<div class="clearfix"></div>
            <a class="btn btn-default pull-right">
              Total Net Pay: <span id="overall-netpay"> 0 </span>
            </a>
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
                               <th width="30%">UserName</th>                           
                               <th width="10%">Month</th>
								<th width="8%">Net Pay</th>
                               <th width="15%">Created At</th>                           
                               <th width="10%" data-orderable="false">Action</th>
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
<div class="modal fade bs-modal-lg" id="slip_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Salary Slip Details</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="slip_detail_table">
            
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
      jQuery('#slip_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
      
      var slip_url="{{asset('/salary_slip/view') }}";  
      $.ajax({
          type: "GET",
          url: slip_url,
          data: 
          {
              slip_id: $id
          },
          success: function (result)
          {
              $("#slip_detail_table").html(result);
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
        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });


        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_name = $("#search-frm select[name='search_name']").val();
                    data.search_month = $("#search-frm select[name='search_month']").val();
                    data.search_year = $("#search-frm select[name='search_year']").val();
                },
                dataSrc: function(response){
                    $("#overall-netpay").html(response.net_total);
                    $("#is_total").val(response.net_total);
                    return response.data;
                }
            },
			lengthMenu:
              [
                [25,50,100,150,200],
                [25,50,100,150,200]
              ],
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },                                             
                { data: 'user_name', name: '{{ TBL_USERS }}.firstname' },
                { data: 'month', name: 'month' },
				{ data: 'net_pay', name: 'net_pay' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection
