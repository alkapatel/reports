@extends('admin.layouts.app')


@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
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
                               <th width="30%">Invoice</th>
                               <th width="20%">Amount</th>
                               <th width="5%">Status</th>
                               <th width="20%">Created At</th>
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
</div>
<div class="modal fade bs-modal-lg" id="invoice_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Invoice Details</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="invoice_detail_table">
            
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
      jQuery('#invoice_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
      
      var invoice_url="{{asset('/invoices/view') }}";  
      $.ajax({
          type: "GET",
          url: invoice_url,
          data: 
          {
              invoice_id: $id
          },
          success: function (result)
          {
              $("#invoice_detail_table").html(result);
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
            lengthMenu:
              [
                [50,100,150,200,250],
                [50,100,150,200,250]
              ],
            ajax: {
                "url": "{!! route('invoices.client.data') !!}",
            dataSrc: function(response){
                    $("#overall-amounts").html(response.amounts);
                    $("#is_total").val(response.amounts);
                    return response.data;
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },
                { data: 'invoice_no', name: 'invoice_no' },
				{ data: 'total_amount', name: 'total_amount' },                         
                { data: 'payment', name: 'payment' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}
            ]
        });        
    });
    </script>
@endsection