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
                               <th width="25%">Employess Name</th>                                   
                               <th width="25%">Document Type</th>
                               <th width="20%">File name</th>
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

@section('scripts')
    <script type="text/javascript">
    

    $(document).ready(function(){
		$("#document_id").select2({
                placeholder: "Search Document Type",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#user_id").select2({
                placeholder: "Search Employee Name",
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
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_id = $("#search-frm input[name='search_id']").val();
                    data.search_emp_nm = $("#search-frm select[name='search_emp_nm']").val();
                    data.search_type = $("#search-frm select[name='search_type']").val();
                    
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
                { data: 'user_name', name: 'user_id' },
                { data: 'title', name: 'doc_type_id' },
                { data: 'filename', name: 'filename' },               
                { data: 'created_at', name: 'created_at' }, 
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection
