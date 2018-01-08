<?php 

$date =  $row->created_at;
$created_at = date_format($date,"Y-m-d");

$today = new \DateTime();
$today = date_format($today,"Y-m-d");

?>
<div class="btn-group">
@if($isEdit)

	@if($created_at == $today)
	<a href="{{ route($currentRoute.'.edit',['id' => $row->id]) }}" class="btn btn-xs btn-primary" title="edit">
    	<i class="fa fa-edit"></i>
	</a>         
	@endif
@endif

@if($isDelete)
	@if($created_at == $today)
	<a data-id="{{ $row->id }}" href="{{ route($currentRoute.'.destroy',['id' => $row->id]) }}" class="btn btn-xs btn-danger btn-delete-record" title="delete">
    	<i class="fa fa-trash-o"></i>
	</a>          
	@endif
@endif
@if(isset($isView) && $isView)
<a data-id="{{ $row->id }}" class="btn btn-xs btn-success" onclick="openView({{$row->id}})" title="view">
    <i class="fa fa-eye"></i>
</a>          
@endif
</div>