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
                               <th width="10%">Name</th>
                               <th width="10%">Blood Group</th>
                               <th width="15%">Relation</th>
                               <th width="10%">Occupation</th>
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
                "url": "{!! route('family.data') !!}",
                "data": function ( data ) 
                {
                    data.search_id = $("#search-frm input[name='search_id']").val();                 
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },                                             
                { data: 'name', name: 'name' },                                             
                { data: 'blood_group', name: 'blood_group' },                             
                { data: 'relation_with_primary_member', name: 'relation_with_primary_member' },
                { data: 'occupation', name: 'occupation' },
                { data: 'created_at', name: 'created_at' },                                         
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection


