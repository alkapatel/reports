@extends('admin.layouts.bopal_app')
@section('styles')
<link href="{{ asset("themes/admin/assets/")}}/pages/css/error.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')

<div class="page-content">
    <div class="container">
        <ul class="page-breadcrumb breadcrumb">
            <li>
                <a href="{{ url('/dashboard')}}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="">Pages</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>System</span>
            </li>
        </ul>
        <div class="page-content-inner">
            <div class="row">
                <div class="col-md-12 page-404">
                    <div class="number font-green"> 404 </div>
                    <div class="details">
                        <h3>Oops! You're lost.</h3>
                        <p> We can not find the page you're looking for.
                            <br/>
                            <a class="btn btn btn-success" href="{{ url('/dashboard')}}" title="Reports PHPdots">
                                Return Home
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <br/><br/>    
        </div>
    </div>
</div>
    @endsection