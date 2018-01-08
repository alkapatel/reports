@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            @include($moduleViewName.".search")
            <div class="clearfix"></div>
            <div class="clearfix">&nbsp;</div>
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}
                    </div>
                </div>
                <div class="portlet-body">                    
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                    
                               <th width="30%">Username</th>
                               <th width="5%">Is Submit</th>
                               <th width="15%">CreatedAt</th>
                               <th width="15%">SubmitedAt</th>
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
<div class="modal fade bs-modal-lg" id="appraisal_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-full">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Appraisal Form Detail</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="appraisal_detail_table">
            
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
      jQuery('#appraisal_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
     var id = $id;
     var appraisal_url = "{{ url('/appraisal-form')}}/"+id+"/view";
      
      $.ajax({
          type: "GET",
          url: appraisal_url,
           
          success: function (result)
          {
              $("#appraisal_detail_table").html(result);
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
                placeholder: "Search User",
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
                  data.search_submit = $("#search-frm select[name='search_submit']").val();
                }
            },
            lengthMenu:
              [
                [100,200,300,400,500],
                [100,200,300,400,500]
              ],          
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },
                { data: 'username', name: '{{ TBL_USERS }}.name' },
                { data: 'is_submit', name: 'is_submit' },                                              
                { data: 'created_at', name: 'created_at' },
                { data: 'submited_at', name: 'submited_at' }, 
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection