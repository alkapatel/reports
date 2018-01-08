 
 @foreach($views as $view)
<div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
		<i class="fa fa-user"></i>Expense</div>
	</div>
	<div class="portlet-body">
		<div class="row static-info">
			<div class="col-md-5 name"> Title: </div>
			<div class="col-md-7 value"> {{$view->title}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Date: </div>
			<div class="col-md-7 value"> {{$view->date}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Amount: </div>
			<div class="col-md-7 value"> {{$view->amount}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Description: </div>
			<div class="col-md-7 value"> {{$view->description_bill}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Scanned Bill: </div>
			<div class="col-md-7 value">
			@if($view->scanned_bill != '') 
				<a class='btn btn-xs btn-warning' href='{{ asset("/expense/download/$view->id") }}' > Download </a>
			@endif
			</div>
		</div>
	</div>
</div>
@endforeach