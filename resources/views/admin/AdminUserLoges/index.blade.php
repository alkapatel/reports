@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">

            @include("admin.AdminUserLoges.search")

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
                                <th width="15%">Username</th>
                                <th width="30%">#Action Name <br/># Action Value</th>              
                                <th width="20%"> Remarks</th> 
                                <th width="10%">Ip Address</th>
                                <th width="20%">  Log Date</th>
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

@endsection

@section('styles')

@endsection

@section('scripts')
<script type="text/javascript">

    $(document).ready(function () {
		$("#action_id").select2({
                placeholder: "Search Action",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#user_id").select2({
                placeholder: "Search User",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#search-frm").submit(function () {
            oTableCustom.draw();
            return false;
        });

        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu: [
                [100, 200, 300, 400, 500],
                [100, 200, 300, 400, 500]
            ],
            pageLength: 100,
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function (data)
                {
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_adminuserid = $("#search-frm select[name='search_adminuserid']").val();
                    data.search_actionid = $("#search-frm select[name='search_actionid']").val();
                    data.search_actionvalue = $("#search-frm input[name='search_actionvalue']").val();
                    data.search_remark = $("#search-frm input[name='search_remark']").val();
                    data.search_ipaddress = $("#search-frm input[name='search_ipaddress']").val();

                }
            },
            "order": [['0', "desc"]],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'adminuserid'},
                {data: 'actionid', name: 'actionid'},
                {data: 'remark', name: '{{ TBL_ADMIN_LOG }}.remark'},
                {data: 'ipaddress', name: 'ipaddress'},
                {data: 'created_at', name: '{{ TBL_ADMIN_LOG }}.created_at'}
            ]
        });

    });
    
</script>

@endsection


