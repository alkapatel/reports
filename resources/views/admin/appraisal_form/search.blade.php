
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
                    {!! Form::select('search_name', [''=>'Search User'] + $users, Request::get("search_name"), ['class' => 'form-control','id'=>'user_id']) !!}
                </div>
                <div class="col-md-4">
                    <label class="control-label">Is Submit</label>
                    <select name="search_submit" class="form-control">
                        <option value="">All</option>                        
                        <option value="0" {!! \Request::get("search_submit") == "0" ? 'selected="selected"':'' !!}>No</option>                        
                        <option value="1" {!! \Request::get("search_submit") == "1" ? 'selected="selected"':'' !!}>Yes</option>                        
                    </select>                                                                 
                </div>              
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