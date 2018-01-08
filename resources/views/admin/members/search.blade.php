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
                    @if(\Request::is('members','members/*'))
                    <div class="col-md-4">
                        <label class="control-label">Created Date Range</label>
                        <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                            <input type="text" class="form-control" value="{{ \Request::get("search_start_date") }}" name="search_start_date" id="start_date" placeholder="Start Date">
                            <span class="input-group-addon"> To </span>
                            <input type="text" class="form-control" value="{{ \Request::get("search_end_date") }}" name="search_end_date" id="end_date" placeholder="End Date"> 
                        </div>
                    </div>
                    @endif
                    @if(\Request::is('members-family','members-family/*'))
                    <div class="col-md-4">
                        <label class="control-label">ID(s)</label>
                        <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id"  disabled="disabled" />
                    </div>
                    @endif
                    @if(\Request::is('members','members/*'))
                    <div class="col-md-4">
                        <label class="control-label">ID(s)</label>
                        <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id"/>
                    </div>
                    <div class="col-md-4"> 
                        <label class="control-label">Mobile No:</label>
                        <input type="text" value="{{ \Request::get("search_mobile") }}" class="form-control" name="search_mobile" />
                    </div>
                    @endif
	            </div>
                @if(\Request::is('members','members/*'))
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label">First Name:</label>
                        <input type="text" value="{{ \Request::get("search_firstname") }}" class="form-control" name="search_firstname" />
                    </div>
                    <div class="col-md-4">
                        <label class="control-label">Middle Name:</label>
                        <input type="text" value="{{ \Request::get("search_middlename") }}" class="form-control" name="search_middlename" />
                    </div>
                    <div class="col-md-4">
                        <label class="control-label">Last Name:</label>
                        <input type="text" value="{{ \Request::get("search_lastname") }}" class="form-control" name="search_lastname" />
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label">Profession:</label>
                        <input type="text" value="{{ \Request::get("search_professional") }}" class="form-control" name="search_professional" />
                    </div>
                    <div class="col-md-4">
                        <label class="control-label">Village Name:</label>
                        {!! Form::select('search_village', [''=>'Search Village'] + $villages, Request::get("search_village"), ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-4">
                    <label class="control-label">Status</label>
                    <select name="search_status" class="form-control">
                        <option value="1" {!! \Request::get("search_status") == "1" ? 'selected="selected"':'' !!}>Active</option>                        
                        <option value="0" {!! \Request::get("search_status") == "0" ? 'selected="selected"':'' !!}>Inactive</option>
                        <option value="">All</option>                        
                    </select>
                </div>
                </div>
                @endif
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-12"> 
                        <center>
                            <input type="hidden" name="isDownload" id="is_download"/>
                            <input type="submit" class="btn blue mTop25" value="Search"/>
                            &nbsp;
                            <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                         </center>  
                    </div>                           
                </div>                                   
            </div>                
        </form>
    </div>    
</div>      
