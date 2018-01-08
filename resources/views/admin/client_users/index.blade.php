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
                               <th width="20%">Client Name</th>                           
                               <th width="20%">User Name</th>                           
                               <th width="15%">User Email</th>                           
                               <th width="5%">Status</th>                           
                               <th width="10%">Created At</th>                           
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

        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });
		$("#client_id").select2({
                placeholder: "Search Client Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
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
                    data.search_client = $("#search-frm select[name='search_client']").val();
                    data.search_email = $("#search-frm input[name='search_email']").val();
                    data.search_name = $("#search-frm input[name='search_name']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
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
                { data: 'client', name: '{{ TBL_CLIENT }}.name' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}           
            ]
        });        
    });
    </script>
@endsection
