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
                               <th width="30%">Client</th>
                               <th width="30%">Project Name</th>
                               <th width="10%">Status</th>                           
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
@endsection

@section('styles')
  
@endsection

@section('scripts')
    <script type="text/javascript">
    

    $(document).ready(function(){
		$("#client_id").select2({
                placeholder: "Search Client Name",
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
                    data.search_title = $("#search-frm input[name='search_title']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
                    data.search_client = $("#search-frm select[name='search_client']").val();
                 
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
                { data: 'client_name', name: '{{ TBL_CLIENT }}.name' },
                { data: 'title', name: 'title' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection
