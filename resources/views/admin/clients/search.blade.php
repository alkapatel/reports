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
                    <label class="control-label">Name</label>
                    <input type="text" value="{{ \Request::get("search_name") }}" class="form-control" name="search_name" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Email</label>
                    <input type="text" value="{{ \Request::get("search_email") }}" class="form-control" name="search_email" />
                </div>
                
                <div class="row" align="center">                     
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                </div>                                               
            </div>
        </form>
    </div>    
</div>      