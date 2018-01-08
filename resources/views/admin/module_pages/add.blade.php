
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

                                    @if(isset($action_show_hidde))
                                     <div class="col-md-4">
                                        <label class="control-label">ID:<span class="required">*</span></label>                                        
                                        {!! Form::text('id',null,['class' => 'form-control', 'data-required' => true]) !!}
                                     </div>
                                    <div class="clearfix"></div>
                                     <div class="clearfix">&nbsp;</div>
                                    @endif
                                    
                                    <div class="col-md-4">
                                        <label class="control-label">Module<span class="required">*</span></label>                                        
                                        {!! Form::select('admin_group_id', [''=>'Select Module'] + $pageGroups, Request::get("search_pageGroup"), ['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>

                                    <div class="col-md-4">
                                        <label class="control-label">Page Name<span class="required">*</span></label>                                        
                                        {!! Form::text('name',null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>

                                    <div class="col-md-4">
                                        <label class="control-label">Menu Title<span class="required">*</span></label>                                        
                                        {!! Form::text('menu_title',null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>                                      
                                </div>    

                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label">Menu Order<span class="required">*</span></label>
                                        {!! Form::text('menu_order',null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">Is Sub Menu<span class="required">*</span></label>
                                        {!! Form::select('is_sub_menu', [''=>'Is Sub Menu', 'Y'=>'Y', 'N'=>'N'], NULL, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">Url<span class="required"></span></label>
                                        {!! Form::text('url',null,['class' => 'form-control']) !!}
                                    </div>
                                </div>                                                         
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                      <div class="col-md-4">
                                          <label class="control-label">Show In Menu<span class="required">*</span></label>
                                          {!! Form::select('show_in_menu', [''=>'Show In Menu', 'Y'=>'Y', 'N'=>'N'], Request::get("search_pageGroup"), ['class' => 'form-control']) !!}
                                      </div>
                                      <div class="col-md-4">
                                          <label class="control-label">Description<span class="required"></span></label>
                                          {!! Form::textarea('description',null,['class' => 'form-control','rows' => 2]) !!}
                                      </div>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                                         
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

