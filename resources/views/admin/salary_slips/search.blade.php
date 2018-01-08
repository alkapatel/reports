<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-search"></i>Advance Search 
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand"> </a>
        </div>                    
    </div>
    <div class="portlet-body" style="display: none">  
        <form id="search-frm">
            <div class="row">
                @if(!empty($users))
                <div class="col-md-4">
                    <label class="control-label">User Name</label>
                    {!! Form::select('search_name', [''=>'Search User'] + $users, Request::get("search_name"), ['class' => 'form-control','id'=>'user_id']) !!}
                </div>
                @endif
                 <div class="col-md-4">
                    <label class="control-label">MM/YY</label>
                    <div class="input-group">
                        {!! Form::select('search_month', [''=>'Search Month'] + $months, Request::get("search_month"), ['class' => 'form-control','id'=>'month_id']) !!} 
                        <span class="input-group-addon"> / </span>

                        {!! Form::select('search_year', [''=>'Search Year'] + $years, Request::get("search_year"), ['class' => 'form-control','id'=>'year_id']) !!} 
                    </div>
                </div>
                &nbsp;
                <div class="row" align="center">                     
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>                                
                </div>                                   
            </div>                
        </form>
    </div>    
</div>      