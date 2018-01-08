<?php
if(Auth::guard('admins')->check())
{
	$pending = \App\Custom::getLeaveRequest();
	$today_leave = \App\Custom::getLeaveUser();
	$total_leave = count($today_leave);

	$profile_pic = Auth::guard('admins')->user()->image;
	$user_id = Auth::guard('admins')->user()->id;

	//DOB
	$users_dob = \App\Custom::getUserDob();	
}	
$dob_num = 'DOB';
$this_month = date('m');
?>

<!-- BEGIN HEADER -->
<div class="page-header">
    <!-- BEGIN HEADER TOP -->
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="{{ route('admin_dashboard') }}">
                    <img src="{{ asset("images/pd-logo.png")}}" alt="logo" class="logo-default" style="max-width: 100px;margin-top: 15px !important; max-height: 250px">
                </a>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler"></a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    @if(Auth::check() && Auth::guard('admins')->user()->user_type_id == ADMIN_USER_TYPE)
                    <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="icon-bell"></i>
                            <span class="badge badge-default">{{$pending}}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3>
                                <a href="{{ asset('leave-request?search_user=&search_status=0') }}" >
                                    <strong>@if($pending==0) 0 @else {{$pending}} @endif pending</strong> Leave Request </h3>
                                view all</a>
                            </li>
                        </ul>
                    </li>
                    @endif
					@if(Auth::guard('admins')->user()->user_type_id != CLIENT_USER)
                    <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="icon-calendar"></i>
                            <span class="badge badge-default"> {{$total_leave}} </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3>Today 
                                    <strong> {{$total_leave}} </strong> Users Off</h3>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
								@if(isset($today_leave))	
                                @foreach($today_leave as $user)
                                    <li>
                                        <a href="javascript:;">
                                            <span class="details">
                                                <span class="label label-sm label-icon">
                                                    @if($user->image)
                                                    <img alt="" class="img-circle" src='{{ asset("/uploads/users/$user->user_id/$user->image")}}' width="40px" height="40px" />
                                                    @else
                                                    <img src="{{ asset("uploads/images/default-user.jpg")}}" class="img-circle" height="40px" width="40px"/>
                                                    @endif
                                                </span>
                                                {{ $user->username}}.
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                                @endif    
                                </ul>
                            </li>
                        </ul>
                    </li>
					@if(isset($users_dob) && !empty($users_dob))                    
                    <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="icon-user"></i>
                            <span class="badge badge-default"> {{$dob_num}} </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3> <strong> Shining Stars </strong></h3>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                @foreach($users_dob as $user)
                                    @if($user->month == $this_month)
                                    <li>
                                        <a href="javascript:;">
                                            <span class="details">
                                                <span class="label label-sm label-icon">
                                                    @if($user->image)
                                                    <img alt="" class="img-circle" src='{{ asset("/uploads/users/$user->id/$user->image")}}' width="40px" height="40px" />
                                                    @else
                                                    <img src='{{ asset("/uploads/users/default-user.jpg")}}' class="img-circle" height="40px" width="40px"/>
                                                    @endif
                                                </span>
                                                {{ $user->name}}<br/>{{ $user->dob}}.
                                            </span>
                                        </a>
                                    </li>
                                    @endif
                                @endforeach 
                                </ul>
                            </li>
                        </ul>
                    </li>                    
                    @endif
					@endif
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            @if(isset($profile_pic) && $profile_pic)
                                <img alt="" class="img-circle" src="{{ asset("/uploads/users/$user_id/$profile_pic")}}" width="50px" height="50px" />
                            @else
                            <img alt="" class="img-circle" src="{{ asset("uploads/users/default-user.jpg")}}" />
                            @endif
                            <span class="username username-hide-mobile">
                                @if(Auth::guard('admins')->check())                                
                                    <?php  $name = Auth::guard('admins')->user()->name;
                                        $name = ucwords($name);
                                    ?>                      
                                    {{ $name }}
                                @endif
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="{{ route('edit_profile')}}">
                                    <i class="icon-user"></i> My Profile </a>
                            </li>                                    
                            <li>
                                <a href="{{ route('change_password')}}">
                                    <i class="icon-key"></i> Change Password </a>
                            </li>
                            <li>
                                <a href="{{ route('logout')}}">
                                    <i class="icon-logout"></i> Log Out </a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                    <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                    <li class="dropdown dropdown-extended quick-sidebar-toggler">
                        <span class="sr-only">Toggle Quick Sidebar</span>
                        <i class="icon-logout" onclick="window.location = '{{ route("logout") }}'"></i>

                    </li>
                    <!-- END QUICK SIDEBAR TOGGLER -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
    </div>
    <!-- END HEADER TOP -->
    <!-- BEGIN HEADER MENU -->
    <div class="page-header-menu">
        <div class="container">


            <!-- BEGIN MEGA MENU -->

            <div class="hor-menu  ">
                <ul class="nav navbar-nav">
                    <li class="{{ Request::is('admin','dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin_dashboard')}}"> 
                            Dashboard
                        </a>
                    </li>
                    <?php 
                        $rowarray = session("admin_user_rights");
                        
                        $closeflag  = true;
                        $groupname  = "";
                        $scriptdata = '<li class="menu-dropdown classic-menu-dropdown">';
                        $groupwidth = 0;

                        foreach ($rowarray as $row)
                        {
                            if($groupname != $row["trngrouptitle"])
                            {
                                if ($groupname == "") 
                                {
                                    $scriptdata = $scriptdata.'<a href="javascript:;">'.trim($row["trngrouptitle"]).'<span class="arrow"></span></a>';

                                    $scriptdata = $scriptdata . '<ul class="dropdown-menu pull-left">';
                                    $closeflag = false;
                                } 
                                else 
                                {
                                    $scriptdata = $scriptdata . "</ul></li>";

                                    $scriptdata = $scriptdata .'<li class="menu-dropdown classic-menu-dropdown">';
                                    $scriptdata = $scriptdata.'<a href="javascript:;">'.trim($row["trngrouptitle"]).'<span class="arrow"></span></a>';

                                    $scriptdata = $scriptdata . '<ul class="dropdown-menu pull-left">';
                                    $closeflag = false;
                                }
                                
                                if($row["insubmenu"] == "Y" && $row["show_in_menu"] == "Y")
                                {
                                    $scriptdata = $scriptdata . "<li><a class='nav-link' href=\"". url($row["pageurl"])."\">".trim($row["trnname"])."</a></li>";
                                }

                                $groupname  = $row["trngrouptitle"];
                            }
                            else
                            {
                                if($row["insubmenu"] == "Y" && $row["show_in_menu"] == "Y")
                                {
                                    $scriptdata = $scriptdata . "<li><a class='nav-link' href=\"".url($row["pageurl"])."\">".trim($row["trnname"])."</a></li>";
                                }
                            }

                        }    
                        if ($closeflag == false) $scriptdata = $scriptdata . "</li></ul>";

                        $scriptdata = $scriptdata . " </li>";

                        echo $scriptdata;
                    ?>

            </div>
            <!-- END MEGA MENU -->
        </div>
    </div>
    <!-- END HEADER MENU -->
</div>

<div id="AjaxLoaderDiv" style="display: none;z-index:99999 !important;">
    <div style="width:100%; height:100%; left:0px; top:0px; position:fixed; opacity:0; filter:alpha(opacity=40); background:#000000;z-index:999999999;">
    </div>
    <div style="float:left;width:100%; left:0px; top:50%; text-align:center; position:fixed; padding:0px; z-index:999999999;">
        <img src="{{ asset('/') }}/images/ajax-loader.gif">
        </center>
    </div>
</div>
