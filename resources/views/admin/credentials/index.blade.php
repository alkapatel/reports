@extends('admin.layouts.app')
<?php
$popup_id = request()->get('popup_id');
?>

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
                               <th width="35%">Project</th>
                               <th width="30%">Type</th>
								<th width="20%">Title</th>
                               <th width="5%">Environment</th>
                               <th width="20%">Created At</th>
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
<div class="modal fade bs-modal-lg" id="credential_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Credential Details</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="credential_detail_table">
            
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
<script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.4.0/clipboard.min.js"></script>
<script type="text/javascript">
$(window).load(function(){
    setTimeout(function(){
        @if(!empty($popup_id))
          openView({{$popup_id}});
        @endif      
    },3500);
  });
	
function openView($id){
      jQuery('#credential_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
      
      var credential_url="{{asset('/credentials/view') }}";  
      $.ajax({
          type: "GET",
          url: credential_url,
          data: 
          {
              credential_id: $id
          },
          success: function (result)
          {
              $("#credential_detail_table").html(result);
              $('#AjaxLoaderDiv').fadeOut('slow');
			  setTimeout(function(){
                (function(){
                    new Clipboard('.copy-button');
                })();                
              },1000)
          },
          error: function (error) 
          {
              $('#AjaxLoaderDiv').fadeOut('slow');
          }
      });

    }
    $(document).ready(function(){
		$("#project_id").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#protocol_id").select2({
                placeholder: "Search Type",
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
                [50,100,150,200,250],
                [50,100,150,200,250]
              ],
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_protocol = $("#search-frm select[name='search_protocol']").val();
                    data.search_env = $("#search-frm select[name='search_env']").val();
                    data.search_project = $("#search-frm select[name='search_project']").val();
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },
                { data: 'project_name', name: '{{TBL_PROJECT}}.title' },
                { data: 'protocol', name: 'protocol' }, 
				{ data: 'title', name: 'title' },
                { data: 'environment', name: 'environment' },                         
                { data: 'created_at', name: 'created_at' },                                         
                { data: 'action', orderable: false, searchable: false}
            ]
        });        
    });
    </script>
@endsection