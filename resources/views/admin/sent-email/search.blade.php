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
                    <label class="control-label">ID(s)</label>
                    <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id" placeholder="Search IDs" />
                </div>                
                <div class="col-md-4">
                    <label class="control-label">Form Email</label>
                    <input type="text" value="{{ \Request::get("search_formemail") }}" class="form-control" name="search_formemail" />
                </div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div class="col-md-4">
                    <label class="control-label">To Email</label>
                    <input type="text" value="{{ \Request::get("search_toemail") }}" class="form-control" name="search_toemail" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">CC Email</label>
                    <input type="text" value="{{ \Request::get("search_ccemail") }}" class="form-control" name="search_ccemail" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Subject</label>
                    <input type="text" value="{{ \Request::get("search_sub") }}" class="form-control" name="search_sub" />
                </div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div class="col-md-12"><center>
                    <input type="hidden" name="record_per_page" id="record_per_page"/>
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a></center>                                
                </div>                    
            </div>                
        </form>
    </div>    
</div>    