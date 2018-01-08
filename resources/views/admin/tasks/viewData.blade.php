 
 @foreach($views as $view)
<div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
		<i class="fa fa-user"></i>{{$view->user_name}} </div>
	</div>
	<div class="portlet-body">
		<div class="row static-info">
			<div class="col-md-5 name"> Project: </div>
			<div class="col-md-7 value"> {{$view->project_name}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Task Title: </div>
			<div class="col-md-7 value"> {{$view->title}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Status: </div>
			<div class="col-md-7 value">@if($view->status == 1) <div class="btn btn-xs btn-success"> Completed</div> @else <div class="btn btn-xs btn-danger">In Progress</div> @endif
			</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Hour: </div>
			<div class="col-md-7 value"> {{$view->total_time}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name">Task Date: </div>
			<div class="col-md-7 value"> <?php echo date('j M,Y',strtotime($view->task_date));?></div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Ref Link: </div>
			<div class="col-md-7 value"><a target="_blank" href="{{$view->ref_link}}"> {{$view->ref_link}}</a></div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Description: </div>
			<div class="col-md-7 value"> {{$view->description}}</div>
		</div>
	</div>
</div>
@endforeach