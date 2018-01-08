
@extends('admin.layouts.app')

@section('breadcrumb')


@stop

@section('content')

<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>
                            {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    
                    <div class="portlet-body">
                        <div class="form-body">                            
                            {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm']) !!} 
                                <div class="row ">

                                    @if(isset($action_show_hidde) && $action_show_hidde ==1)
                                     <div class="col-md-6">
                                        <label class="control-label">ID:<span class="required">*</span></label>                                        
                                        {!! Form::text('id',null,['class' => 'form-control', 'data-required' => true]) !!}
                                     </div>
                                    @endif

                                    <div class="col-md-6">
                                        <label class="control-label">Description:<span class="required">*</span></label>                                        
                                        {!! Form::text('description',null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>

                                    @if(isset($action_show_hidde))
                                        <div class="clearfix">&nbsp;</div>    
                                    @endif
                                    <div class="col-md-6">
                                        <label class="control-label">Remark:</label>                                        
                                        {!! Form::text('remark',null,['class' => 'form-control']) !!}
                                     </div>                                      
                                </div>                                        
                                                         
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="{{ $buttonText}}" class="btn btn-success pull-right" />
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>                 
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#main-frm').submit(function () {
            
            if ($(this).parsley('isValid'))
            {
                $('#AjaxLoaderDiv').fadeIn('slow');
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
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
                            window.location = '{{ $list_url }}';    
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

