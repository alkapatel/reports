@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            @include($moduleViewName.".search")
            <div class="clearfix"></div>
            <a class="btn btn-default pull-right">
              Total Amount: <span id="overall-amount"> 0 </span>
            </a>   
             
            <div class="clearfix">&nbsp;</div>
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
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                    
                               <th width="20%">Title</th>                                    
                               <th width="10%">Expense Date</th>
                               <th width="10%">Amount</th>
                               <th width="20%">CreatedAt</i></th>
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
<div class="modal fade bs-modal-lg" id="expense_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Expense Details</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="expense_detail_table">
            
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
      jQuery('#expense_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
     
      var expense_url="{{asset('/expense/view') }}";  
      $.ajax({
          type: "GET",
          url: expense_url,
          data: 
          {
              expense_id: $id
          },
          success: function (result)
          {
              $("#expense_detail_table").html(result);
              $('#AjaxLoaderDiv').fadeOut('slow');
          },
          error: function (error) 
          {
              $('#AjaxLoaderDiv').fadeOut('slow');
          }
      });

    }
    

    $(document).ready(function(){


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
                  data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                  data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                  data.search_title = $("#search-frm input[name='search_title']").val();
                  data.search_amount = $("#search-frm input[name='search_amount']").val();
                
            },
            dataSrc: function(response){
                    $("#overall-amount").html(response.amount);
                    $("#is_total").val(response.amount);
                    return response.data;
                }
              },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },                                              
                { data: 'date', name: 'date' },                                              
                { data: 'amount', name: 'amount' }, 
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection