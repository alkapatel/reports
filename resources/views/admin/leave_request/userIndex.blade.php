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
                               <th width="15%">From Date</th>                           
                               <th width="15%">To Date</th>
								<th width="5%">Days</th>
                               <th width="35%">Leave Description</th>               
                               <th width="10%">Status</th>               
                               <th width="10%">Created At</th>                           
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


        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                "url": "{!! route('leave-request.userData') !!}",
                "data": function ( data ) 
                {
                    data.search_status = $("#search-frm select[name='search_status']").val();
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_start_leave = $("#search-frm input[name='search_start_leave']").val();
                    data.search_end_leave = $("#search-frm input[name='search_end_leave']").val();
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },                                             
                { data: 'from_date', name: 'from_date' },                                             
                { data: 'to_date', name: 'to_date' },
				{ data: 'days', name: 'to_date' },
                { data: 'description', name: 'description' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
            ]
        });        
    });
    </script>

    
@endsection
