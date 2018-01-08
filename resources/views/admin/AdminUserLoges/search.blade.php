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
                    <label class="control-label">Created Date Range</label>
                    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="text" class="form-control" value="{{ \Request::get("search_start_date") }}" name="search_start_date" id="start_date" placeholder="Start Date">
                        <span class="input-group-addon"> To </span>
                        <input type="text" class="form-control" value="{{ \Request::get("search_end_date") }}" name="search_end_date" id="end_date" placeholder="End Date"> 
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="control-label">Username</label>
                    {!! Form::select('search_adminuserid', [''=>'Select Username'] + $users, Request::get("search_adminuserid"), ['class' => 'form-control','id'=>'user_id']) !!}
                </div>

                <div class="col-md-4">
                    <label class="control-label">Action</label>
                    {!! Form::select('search_actionid', [''=>'Select Action'] + $userAction, Request::get("search_actionid"), ['class' => 'form-control','id'=>'action_id']) !!}                                                               
                </div>

            </div>    
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div class="col-md-4">
                    <label class="control-label">Action Value</label>
                    <input type="text" value="{{ \Request::get("search_actionvalue") }}" class="form-control" name="search_actionvalue" />                                 
                </div>

                <div class="col-md-4">
                    <label class="control-label">Log Remark</label>
                    <input type="text" value="{{ \Request::get("search_remark") }}" class="form-control" name="search_remark" />                                 
                </div>

                <div class="col-md-4">
                    <label class="control-label">Ip Address</label>
                    <input type="text" value="{{ \Request::get("search_ipaddress") }}" class="form-control" name="search_ipaddress" />                                 
                </div>
            </div>
            <div class="row">
                <div class="col-md-12"><center>
                    <input type="hidden" name="record_per_page" id="record_per_page"/>
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a> 
                    </center>
                </div>    
            </div>   
        </form>
    </div>    
</div>    