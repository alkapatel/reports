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
                <div class="col-md-4">
                    <label class="control-label">Task Date Range</label>
                    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="text" class="form-control" value="{{ \Request::get("search_start_date") }}" name="search_start_date" id="start_date" placeholder="Start Date">
                        <span class="input-group-addon"> To </span>
                        <input type="text" class="form-control" value="{{ \Request::get("search_end_date") }}" name="search_end_date" id="end_date" placeholder="End Date"> 
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="control-label">ID(s)</label>
                    <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Project</label>
                    {!! Form::select('search_project', [''=>'Search Project'] + $projects, Request::get("search_project"), ['class' => 'form-control','id'=>'project_id']) !!}
                </div>  
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div class="col-md-4">
                    <label class="control-label">Task Title</label>
                    <input type="text" value="{{ \Request::get("search_title") }}" class="form-control" name="search_title" />
                </div>
                <div class="col-md-4" style="margin-top: 5px">&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label">Hour </label>
                        </div>
                        <div class="col-md-4" style="padding: 0px;">
                        <select name="search_hour_op" class="form-control">
                            <option value="=" {!! \Request::get("search_hour_op") == "=" ? 'selected="selected"':'' !!}>=</option>                        
                            <option value=">=" {!! \Request::get("search_hour_op") == ">=" ? 'selected="selected"':'' !!}>>=</option>                        
                            <option value=">" {!! \Request::get("search_hour_op") == ">" ? 'selected="selected"':'' !!}>></option>                        
                            <option value="<" {!! \Request::get("search_hour_op") == "<" ? 'selected="selected"':'' !!}><</option>                  
                        </select>    
                        </div>
                        <div class="col-md-4" style="padding: 0px;">
                            <input type="text" value="{{ \Request::get("search_hour") }}" class="form-control" name="search_hour" placeholder="Enter Hours"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="margin-top: 5px">&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label">Min </label>
                        </div>
                        <div class="col-md-4" style="padding: 0px;">
                        <select name="search_min_op" class="form-control">
                            <option value="=" {!! \Request::get("search_min_op") == "=" ? 'selected="selected"':'' !!}>=</option>                        
                            <option value=">=" {!! \Request::get("search_min_op") == ">=" ? 'selected="selected"':'' !!}>>=</option>                        
                            <option value=">" {!! \Request::get("search_min_op") == ">" ? 'selected="selected"':'' !!}>></option>                        
                            <option value="<" {!! \Request::get("search_min_op") == "<" ? 'selected="selected"':'' !!}><</option>                   
                        </select>    
                        </div>
                        <div class="col-md-4" style="padding: 0px;">
                            <input type="text" value="{{ \Request::get("search_min") }}" class="form-control" name="search_min" placeholder="Enter Mins" />
                        </div>
                    </div>
                </div>                
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div class="col-md-4">
                    <label class="control-label">Status</label>
                    <select name="search_status" class="form-control">
                        <option value="all">All</option>                        
                        <option value="1" {!! \Request::get("search_status") == "1" ? 'selected="selected"':'' !!}>Completed</option>                        
                        <option value="0" {!! \Request::get("search_status") == "0" ? 'selected="selected"':'' !!}>In Progress</option>                        
                    </select>                                                                 
                </div>
                @if(!empty($users))
                <div class="col-md-4">
                    <label class="control-label">User Name</label>
                    {!! Form::select('search_user', [''=>'Search User'] + $users, Request::get("search_user"), ['class' => 'form-control','id'=>'user_id']) !!}
                </div>
				@endif
                @if(!empty($clients))
                <div class="col-md-4">
                    <label class="control-label">Client Name</label>
                    {!! Form::select('search_client', [''=>'Search Client'] + $clients, Request::get("search_client"), ['class' => 'form-control','id'=>'client_id']) !!}
                </div>
                @endif
            </div>
            &nbsp;               
                <div class="row" align="center">
					 <input type="hidden" name="isDownload" id="is_download"/>
					<input type="hidden" name="isDownloadXls" id="is_download_xls"/>
                    <input type="hidden" name="is_total" id="is_total"/>
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                </div>                                   
            </div>                
        </form>
    </div>    
</div>      