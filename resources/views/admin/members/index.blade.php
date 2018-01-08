@extends('admin.layouts.bopal_app')


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
                        <a class="btn btn-primary pull-right btn-sm mTop5" href="{{ $add_family_url }}" style="margin-right: 10px;">Add New Family Member</a>
                      @endif  
                        <?php $auth_user = Auth::guard("admins")->check();
                          if($auth_user)
                          {
                              $auth_id = Auth::guard("admins")->user()->id;
                              if($auth_id == SUPER_ADMIN_ID){
                          ?>
                        <a class="btn btn-primary pull-right btn-sm mTop5" href="{{ $add_url }}" style="margin-right: 10px;">Add New Member</a>
                        <?php }} ?>
                        <a class="btn btn-primary btn-sm mTop5 btn-download pull-right" style="margin-right: 10px;">Download CSV</a>

                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                   
                               <th width="10%">Firstname</th>
                               <th width="10%">Middlename</th>
                               <th width="10%">Lastname</th>                           
                               <th width="15%">Mobile</th>
                               <th width="10%">Village Name</th>
                               <th width="10%">Profession</th>                           
                               <th width="10%">created_at</th>                           
                               <th width="10%" data-orderable="false">Action</th>
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
<div class="modal fade bs-modal-lg" id="member_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Member Detail</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="detail_table">
            
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
    <script type="text/javascript">
function openView($id){
      jQuery('#member_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
      
      var member_url = "{{asset('/members/view') }}";  
      $.ajax({
          type: "GET",
          url: member_url,
          data: 
          {
              member_id: $id
          },
          success: function (result)
          {
              $("#detail_table").html(result);
              $('#AjaxLoaderDiv').fadeOut('slow');
          },
          error: function (error) 
          {
              $('#AjaxLoaderDiv').fadeOut('slow');
          }
      });

}

    $(document).ready(function(){

        $(document).on('click', '.btn-download', function () {
            $("#is_download").val(1);
            setTimeout(function(){
                $params = $("#search-frm").serialize();
                $url = '{{ url('members') }}'+'?'+$params;
                window.location = $url;
            },400);                       
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
                    data.search_firstname = $("#search-frm input[name='search_firstname']").val();
                    data.search_middlename = $("#search-frm input[name='search_middlename']").val();
                    data.search_lastname = $("#search-frm input[name='search_lastname']").val();
                    data.search_mobile = $("#search-frm input[name='search_mobile']").val();
                    data.search_village = $("#search-frm select[name='search_village']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
                    data.search_professional = $("#search-frm input[name='search_professional']").val();
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
                { data: 'firstname', name: 'firstname' },
                { data: 'middlename', name: 'middlename' },
                { data: 'lastname', name: 'lastname' },
                { data: 'mobile', name: 'mobile' },
                { data: 'village', name: '{{ TBL_VILLAGE }}.title' },
                { data: 'profession', name: 'profession' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection


