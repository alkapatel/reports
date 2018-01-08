<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="title" content="Admin Panel: {{ env('APP_SITE_TITLE')}}" />
        <title>Admin Panel: {{ env('APP_SITE_TITLE')}}</title>        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{ asset("themes/admin/")}}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset("themes/admin/")}}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset("themes/admin/")}}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset("themes/admin/")}}/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset("themes/admin/")}}/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{ asset("themes/admin/")}}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset("themes/admin/")}}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{ asset("themes/admin/")}}/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{ asset("themes/admin/")}}/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="{{ asset("themes/admin/")}}/assets/pages/css/login.min.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/') }}/css/target-admin2.css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
        <link href="{{ asset("/themes/frontend") }}/images/favicon.ico" rel="shortcut icon" type="image/x-icon"> 
        </head>
    <!-- END HEAD -->

    <body class="login">
        <div class="logo">
            <a href="{{ route('admin_login') }}">
                <img src="{{ asset("/images/pd-logo.png")}}" alt="" style="max-width: 300px;" class="img-rounded" />  
            </a>
        </div>        
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            @yield('content')            
        </div>
        <div class="copyright"> {{ date('Y')}} &copy;  {{ env('APP_SITE_TITLE')}} ALL Rights Reserved. </div>

        <div id="AjaxLoaderDiv" style="display: none;z-index:99999 !important;">
            <div style="width:100%; height:100%; left:0px; top:0px; position:fixed; opacity:0; filter:alpha(opacity=40); background:#000000;z-index:999999999;">
            </div>
            <div style="float:left;width:100%; left:0px; top:50%; text-align:center; position:fixed; padding:0px; z-index:999999999;">
                <img src="{{ asset('/') }}/images/ajax-loader.gif" />                
            </div>
        </div>        
        <!--[if lt IE 9]>
<script src="{{ asset("themes/admin/")}}/assets/global/plugins/respond.min.js"></script>
<script src="{{ asset("themes/admin/")}}/assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->      
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/")}}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>

        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{ asset("themes/admin/")}}/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <script type="text/javascript" src="{{ asset('/') }}/js/jquery.bootstrap-growl.min.js"></script>

        <script type="text/javascript" src="{{ asset('/') }}/js/parsley.js"></script>

        <script type="text/javascript" src="{{ asset('/') }}/js/comman.js"></script>

        @yield('scripts')

    </body>
</html>    