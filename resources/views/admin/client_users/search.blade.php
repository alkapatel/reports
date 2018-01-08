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
                    <label class="control-label">User Name</label>
                    <input type="text" value="{{ \Request::get("search_name") }}" class="form-control" name="search_name" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">User Email</label>
                    <input type="text" value="{{ \Request::get("search_email") }}" class="form-control" name="search_email" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Client Name</label>
                    {!! Form::select('search_client', [''=>'Search Client Name'] + $clients, Request::get("search_client"), ['class' => 'form-control','id'=>'client_id']) !!}
                </div>

            </div>&nbsp;
            <div class="row"> 
                <div class="col-md-4">
                    <label class="control-label">Status</label>
                    <select name="search_status" class="form-control">
                        <option value="all">All</option>                        
                        <option value="1" {!! \Request::get("search_status") == "1" ? 'selected="selected"':'' !!}>Active</option>                        
                        <option value="0" {!! \Request::get("search_status") == "0" ? 'selected="selected"':'' !!}>Inactive</option>                        
                    </select>                                                                 
                </div>
                    
                <input type="submit" class="btn blue mTop25" value="Search"/>
                &nbsp;
                <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>                                
            </div>                                   
            </div>                
        </form>
    </div>    
</div>      