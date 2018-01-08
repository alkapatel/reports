@extends('admin.layouts.app')
<?php
$pageTitle = "Dashboard";
$bred_crumb_array = array(
    'Home' => url('backend'),
    'Dashboard' => '',
);
?>
@section('content')
<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">
        <div class="page-content-inner">
            @if(Auth::guard('admins')->user()->user_type_id == NORMAL_USER)
            <div class="row">                
                <a href="{{route('leave-request.create')}}">
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 ">
                            <h4 class="widget-thumb-heading">Leave Request</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-purple icon-screen-desktop"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle">ADD</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                </a>
                <a href="{{route('tasks.create')}}">
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 ">
                            <h4 class="widget-thumb-heading">Add Tasks</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-blue icon-bar-chart"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle">Tasks</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                </a>                
                <a href="{{route('leave-request.index')}}">
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 ">
                            <h4 class="widget-thumb-heading">Leaves</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-red icon-layers"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle">Total - {{ $auth_user_leave }}<br/><br/>
                                    This-Month [ {{ $auth_user_month_leave }} ]</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                </a>
                @foreach($auth_user_hours as $auth_user_hour)
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 ">
                            <h4 class="widget-thumb-heading">Hours [ {{ $auth_user_hour->total }} ]</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-yellow icon-clock"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle">
                                    @foreach($auth_user_month_hour as $hour)
                                        This-Month [ @if($hour->total == '') 0 @else {{ $hour->total }} @endif ]<br/><br/>
                                    @endforeach
                                    @foreach($auth_user_today_hour as $today)

                                    Today <span style="color: red"> [ @if($today->total == '') 0 @else {{ $today->total }} @endif ]</span>
                                    
                                    @endforeach
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                @endforeach               
            </div>
            @endif
            <div class="row">
             
            @if(Auth::guard('admins')->user()->user_type_id == NORMAL_USER)
                @if($userOnHoliday)
                    <div class="col-md-12">
                        <div class="portlet" style="margin-bottom: 0px">
                            <div class="portlet-title tabbable-line">
                                <div class="caption">
                                    <i class="icon-globe font-dark hide"></i>
                                    <span class="caption-subject font-green-steel bold uppercase">Holidays List</span>
                                    <br/><br/>
                                    <span style="color: #f00e28; font-size: 14px">[ Full Leave] </span>
                                    <span style="color: #5cf027; font-size: 14px">[ Half Leave] </span>
                                    <span style="color: #e2f700; font-size: 14px">[ Pending Leave] </span>
                                    <span style="color: #1f0cf0; font-size: 14px">[ Holiday] </span>
                                    <span style="font-size: 14px">[ Working Days - <b class="working_days">{{ $working_days }}</b>] </span>
                                </div>
                            </div>    
                            <div class="portlet-body">
                                <div id='calendar'></div>
                            </div> 
                        </div>    
                    </div>
                @endif
            @endif
            </div>
        </div>
    </div>
    @endsection
    @section('styles')      
    @endsection
    @section('scripts') 

    <link href='{{asset("js/calender/")}}/fullcalendar.min.css' rel='stylesheet' />
    <link href='{{asset("js/calender/")}}/fullcalendar.print.min.css' rel='stylesheet' media='print' />
    <script src='{{asset("js/calender/")}}/moment.min.js'></script>
    <script src='{{asset("js/calender/")}}/fullcalendar.min.js'></script>

    <script type="text/javascript">
        $(document).ready(function(){

        $('#calendar').fullCalendar({
        header: {
        left: 'prev,next,today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek'
        },
                defaultDate: '{{ date("Y-m-d") }}',
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                events: [

                @foreach($userOnHoliday as $d)
                {
                    @php 
                    $title = '';
                    $bgcolor = '';
                    if($d['status'] === 0 && $d['is_half'] != ''){
                        
                        $bgcolor ='#e2f700';//yellow                        
                        $title = $d['is_half'] == 1 ? $d['name'].' - HALF':$d['name'].' - FULL';
                    }else if($d['status'] == 1 && $d['status'] != ''){
                        if ($d['is_half'] == 0) 
                        {
                            $bgcolor ='#f00e28';//red
                            $title = $d['name'].' - FULL';
                        }else{
                            $bgcolor ='#5cf027';//green
                            $title = $d['name'].' - HALF';
                        }
                    }
                    else if($d['status'] == '' && $d['is_half'] == ''){
                    
                        $bgcolor ='#1f0cf0';//blue
                        $title = $d['name'];
                    }

                    @endphp
                        title: '{{ $title }}',
                        start: '{{ date("Y-m-d",strtotime($d['date'])) }}',
                        color: '{{$bgcolor}}',
                },
                        
                @endforeach
                ],
                eventRender: function (event, element, view) 
                { 
                    @foreach($userOnHoliday as $d)
                        @if(isset($d['is_hoilday']))
                        var dateString = '{{ date("Y-m-d",strtotime($d['date'])) }}';
                        $(view.el[0]).find('.fc-day[data-date=' + dateString + ']').css('background-color', '#fbb6b6'); 
                        @endif
                    @endforeach
                  
                }
        });

        $(document).on('click','.fc-prev-button',function(){
            calendar_month();
        });
        $(document).on('click','.fc-next-button',function(){
            calendar_month();
        });
    });
    </script>
    <script type="text/javascript">
        function calendar_month(){
            
            var startDate = $('#calendar').fullCalendar('getView').intervalStart;
            var start_date = new Date(startDate);

            var start_date =  start_date.getFullYear() + "-"+(start_date.getMonth()+1) +"-"+start_date.getDate() + ' '+start_date.toString().split(' ')[4];

            var urlAction = "{{asset('dashboard/calendar') }}";
            $('.working_days').html('Loading...');
            $.ajax({
                type: "GET",
                url: urlAction,
                data: {start_date: start_date},
                success: function (result)
                {
                    $('.working_days').html(result);
                },
                error: function (error) {
                }
            });
        }
    </script>
    @endsection
