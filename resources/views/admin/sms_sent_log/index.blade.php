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
                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                    
                               <th width="15%">Member Name</th>
                               <th width="10%">Mobile</th>
                               <th width="20%">SMS</th>
                               <th width="10%">Response</th>
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
        $("#member_id").select2({
                placeholder: "Search Member Name",
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
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_member = $("#search-frm select[name='search_member']").val();
                    data.search_mobile = $("#search-frm input[name='search_mobile']").val();
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
                { data: 'user_name', name: '{{ TBL_MEMBER }}.id' },
                { data: 'mobile', name: 'mobile' },
                { data: 'sms_body', name: 'sms_body' },
                { data: 'sms_response', name: 'sms_response' },
                { data: 'created_at', name: 'created_at' },
            ]
        });
    });
    </script>
@endsection
