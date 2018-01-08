<div class="btn-group">
@if(isset($isEdit) && $isEdit)
<a href="{{ route($currentRoute.'.edit',['id' => $row->id]) }}" class="btn btn-xs btn-primary" title="edit">
    <i class="fa fa-edit"></i>
</a>         
@endif

@if(isset($isDelete) && $isDelete)
<a data-id="{{ $row->id }}" href="{{ route($currentRoute.'.destroy',['id' => $row->id]) }}" class="btn btn-xs btn-danger btn-delete-record" title="delete">
    <i class="fa fa-trash-o"></i>
</a>          
@endif

@if($isView)
<a data-id="{{ $row->id }}" class="btn btn-xs btn-success" onclick="openView({{$row->id}})" title="view">
    <i class="fa fa-eye"></i>
</a>
@endif
</div>
