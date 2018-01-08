@extends('admin.layouts.app')


@section('content')


<style>
.chk-rights{font-size: 16px;margin-top: 7px;} 
.note.note-info{margin-top: 7px;}   
#myslidemenu > ul {
    padding-left: 0;
}
/*#myslidemenu > ul li{width: 48%;float: left;padding-left: 5px;}*/
</style>
<div class="page-content">
    <div class="container">
        <div class="row">
            <form class="rights-frm">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-key"></i>
                            List Of Module and its Actions
                        </div>
                    </div>
                    <div class="portlet-body">
            <div class="clearfix"></div>
            <input type="submit" value="Update" class="btn btn-success pull-right" />
            <div class="clearfix"></div>                            


                <div class="form-group">
                    <label class="control-label">Select User Type</label>
                    <select name="type_id" class="form-control type_id">
                        <option value="">select user type</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {!! \Request::get("type_id") == $role->id ? 'selected':'' !!}>{{ $role->title }}</option>
                        @endforeach
                    </select>
                </div>

                <a class="btn btn-danger pull-right btn-uncheck">Un Check All</a>
                <a class="btn btn-primary pull-right btn-check" style="margin-right: 5px;">Check All</a>
                <div class="clearfix"></div>
                <?php 
                        $rowarray = array();

                        $groupname  = "";
                        $scriptdata = "";
                        $groupwidth = 0;

                        foreach($rows as $row)
                        {
                            $rowarray[$row["trnid"]] = array("trngroupid"    => $row["trngroupid"],
                                                             "trngrouptitle" => $row["trngrouptitle"],
                                                             "trnid"         => $row["trnid"],
                                                             "trnname"       => $row["trnname"],
                                                             "pageurl"       => $row["pageurl"],
                                                             "trntitle"      => $row["trntitle"],
                                                             "insubmenu"     => $row["insubmenu"],
                                                             "show_in_menu"     => $row["show_in_menu"],                                             
                                                             );
                        }

                        $scriptdata = "<div id=\"myslidemenu\" class=\"jqueryslidemenu\"><ul style='list-style: none;'>";
                        $closeflag  = true;

                        foreach ($rowarray as $row)
                        {
                            if($groupname != $row["trngrouptitle"])
                            {
                                if ($groupname == "") {
                                    
                                    $scriptdata = $scriptdata . "<li><div class='note note-info'><h4><input class='menu_group' type='checkbox' name='menu_check[]' value='".$row['trngroupid']."'/> ".trim($row["trngrouptitle"])."</h4></div>";
                                    $scriptdata = $scriptdata . "<ul style='list-style: none;'>\r\n";
                                    $closeflag = false;
                                } 
                                else 
                                {
                                    $scriptdata = $scriptdata . "</ul></li>";
                                    $scriptdata = $scriptdata . "<li><div class='note note-info'><h4><input class='menu_group' type='checkbox' name='menu_check[]' value='".$row['trngroupid']."'/> ".trim($row["trngrouptitle"])."</h4></div>";
                                    $scriptdata = $scriptdata . "<ul style='list-style: none;'>\r\n";
                                    $closeflag = false;
                                }
                                
                                if($row["insubmenu"] == "Y")
                                {
                                    $selected = in_array($row['trnid'], $ids_selected) ? 'checked="checked"':'';
                                    $chk = "<input class='chkids' id='menuitems_".$row["trngroupid"]."' type='checkbox' ".$selected." name='ids[]' value='".$row['trnid']."'/>";
                                    $scriptdata = $scriptdata . "<li class='chk-rights'>&nbsp;".$chk."&nbsp;".trim($row["trnname"])."&nbsp;</li>";

                                }

                                $groupname  = $row["trngrouptitle"];
                            }
                            else
                            {
                                if($row["insubmenu"] == "Y")
                                {
                                    $selected = in_array($row['trnid'], $ids_selected) ? 'checked="checked"':'';
                                    $chk = "<input class='chkids' id='menuitems_".$row["trngroupid"]."' type='checkbox' ".$selected." name='ids[]' value='".$row['trnid']."'/>";
                                    $scriptdata = $scriptdata . "<li class='chk-rights'>&nbsp;".$chk."&nbsp;".trim($row["trnname"])."&nbsp;</li>";
                                }
                            }
                        }

                        if ($closeflag == false) $scriptdata = $scriptdata . "</ul></li>\r\n";
                        $scriptdata = $scriptdata . " </ul></div>"; 

                        echo $scriptdata;
                ?>                    
                        <div class="clearfix"></div>
                    </div>
                </div>                 
            <div class="clearfix"></div>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="submit" value="Update" class="btn btn-success pull-right" />
            <div class="clearfix"></div>                            
            </div>
            </form>
        </div>
    </div>
</div>            


@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        //main menu checkbox

        $(".menu_group").click(function(){
            var checkedID=$(this).val();
            var menuitemsId='menuitems_'+checkedID;
            if($(this).is(':checked')) {
                console.log(checkedID);
                //menuitems_14

                $('input[id='+menuitemsId+']').each(function() {
                $(this).prop("checked",true); 
                    //this.checked = true;
                });

            }else{
                $('input[id='+menuitemsId+']').each(function() {
                     $(this).prop("checked",false); 
                    //this.checked = false;
                });
            }
        });

        $(".btn-check").click(function(){
            $(".chkids").prop("checked",true);
        });
        
        $(".btn-uncheck").click(function(){
            $(".chkids").prop("checked",false);
        });

        $(".type_id").change(function(){
            if($(this).val() != '')
            {
                window.location = "{{ route('list-assign-rights') }}"+"?type_id="+$(this).val();
            }
        });

        $('.rights-frm').submit(function () {
            if (true)
            {
                $('#AjaxLoaderDiv').fadeIn('slow');
                $.ajax({
                    type: "POST",
                    url: "{{ route('assign-rights') }}",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function (result)
                    {
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        if (result.status == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                        }
                    },
                    error: function (error) {
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                    }
                });
            }
            return false;

        });
    });
</script>
@endsection


