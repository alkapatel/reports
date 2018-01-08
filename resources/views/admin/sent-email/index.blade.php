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
                               <th width="15%">From</th>                           
                               <th width="15%">To</th>                           
                               <th width="15%">CC</th>
                               <th width="25%">Subject</th>
                               <th width="20%">Date</th>
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
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_id = $("#search-frm input[name='search_id']").val();
                    data.search_formemail = $("#search-frm input[name='search_formemail']").val();
                    data.search_toemail = $("#search-frm input[name='search_toemail']").val();
                    data.search_ccemail = $("#search-frm input[name='search_ccemail']").val();
                    data.search_sub = $("#search-frm input[name='search_sub']").val();
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
                { data: 'from_email', name: 'from_email' },      
                { data: 'to_email', name: 'to_email' },          
                { data: 'cc_emails', name: 'cc_emails' },       
                { data: 'email_subject', name: 'email_subject' },
                { data: 'created_at', name: 'created_at' },   
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection


