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
                        <label class="control-label">IDs</label>
                        <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id" />
                    </div>                
	                <div class="col-md-4">
	                    <label class="control-label">First Name:</label>
	                    <input type="text" value="{{ \Request::get("search_firstname") }}" class="form-control" name="search_firstname" />
	                </div>
	                
	            </div>
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label">Last Name:</label>
                        <input type="text" value="{{ \Request::get("search_lastname") }}" class="form-control" name="search_lastname" />
                    </div>
                    <div class="col-md-4"> 
                        <label class="control-label">Mobile No:</label>
                        <input type="text" value="{{ \Request::get("search_mobile") }}" class="form-control" name="search_mobile" />
                    </div>
                	<div class="col-md-4">
	                    <label class="control-label">WhatsApp Number:</label>
	                    <input type="text" value="{{ \Request::get("search_no") }}" class="form-control" name="search_no" />
                	</div>
              
                	
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label">Address:</label>
                        <input type="text" value="{{ \Request::get("search_address") }}" class="form-control" name="search_address" />
                    </div>
                    <div class="col-md-4">
                        <label class="control-label">Village Name:</label>
                        <input type="text" value="{{ \Request::get("search_village") }}" class="form-control" name="search_village" />
                    </div>
                	<div class="col-md-4">
	                    <label class="control-label">Professional:</label>
	                    <input type="text" value="{{ \Request::get("search_professional") }}" class="form-control" name="search_professional" />
                	</div>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-12"> 
                        <center>
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
