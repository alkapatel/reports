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
                    <label class="control-label">Type</label>
                    {!! Form::select('search_protocol', [''=>'Search Type'] + $types, Request::get("search_protocol"), ['class' => 'form-control','id'=>'protocol_id']) !!}
                </div>
                <div class="col-md-4">
                    <label class="control-label">Environment</label>
                    {!! Form::select('search_env', [''=>'Search Environment'] + $environment, Request::get("search_env"), ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                        <label class="control-label">Project Name</label>
                        {!! Form::select('search_project', [''=>'Search Project'] + $projects, Request::get("search_project"), ['class' => 'form-control','id'=>'project_id']) !!}
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