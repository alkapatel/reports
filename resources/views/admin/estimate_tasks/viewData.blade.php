
<div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
		<i class="fa fa-user"></i>{{ $user_name->name}}</div>
	</div>
	<div class="portlet-body">
		<div class="row static-info">
			<div class="col-md-5 name"> Project: </div>
			<div class="col-md-7 value"> {{$view->project_name}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Task Title: </div>
			<div class="col-md-7 value"> {{$view->task}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Delivery Task Description: </div>
			<div class="col-md-7 value"> {{$view->delivery_description}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Status: </div>
			<div class="col-md-7 value">
				@if($view->status == 0)
				<div class="btn btn-xs btn-primary"> Pending</div>@endif
				@if($view->status == 1)
				<div class="btn btn-xs btn-success">Completed</div>@endif
				@if($view->status == 2)
				<div class="btn btn-xs btn-danger">In Progress</div>@endif
				@if($view->status == 3)
				<div class="btn btn-xs btn-warning">Skip</div>@endif
			</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Estimated Hour: </div>
			<div class="col-md-7 value"><i class="fa fa-clock-o" aria-hidden="true"></i>  {{$view->estimated_total_time}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Actual Hour: </div>
			<div class="col-md-7 value">@if($view->actual_total_time != '')<i class="fa fa-clock-o" aria-hidden="true"></i>@endif  {{$view->actual_total_time}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Date: </div>
			<div class="col-md-7 value"><?php echo date('j M,Y',strtotime($view->task_date));?> </div>
		</div>
	</div>
</div>
 
 