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
                  
                   
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $add_url }}">Add New</a>
                   

                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                   
                               <th width="10%">Firstname</th>                           
                               <th width="10%">Lastname</th>                           
                               <th width="15%">Mobile</th>
                               <th width="10%">Village Name</th>                          
                               <th width="15%">Address</th>
                               <th width="10%">Professional</th>                           
                               <th width="20%">created_at</th>                           
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
                    data.search_firstname = $("#search-frm input[name='search_firstname']").val();
                    data.search_lastname = $("#search-frm input[name='search_lastname']").val();
                    data.search_mobile = $("#search-frm input[name='search_mobile']").val();
                    data.search_no = $("#search-frm input[name='search_no']").val();
                    data.search_address = $("#search-frm input[name='search_address']").val();
                    data.search_village = $("#search-frm input[name='search_village']").val();
                    data.search_professional = $("#search-frm input[name='search_professional']").val();
                 
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },                                             
                { data: 'firstname', name: 'firstname' },                                             
                { data: 'lastname', name: 'lastname' },                                             
                { data: 'phone', name: 'phone' },
                { data: 'village_name', name: 'village_name' },                                             
                { data: 'address', name: 'address' },                                             
                { data: 'professional', name: 'professional' },                                             
                { data: 'created_at', name: 'created_at' },                                         
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection


