<div class="btn-group">
@if($isEdit)
<a href="{{ route($currentRoute.'.edit',['id' => $row->id]) }}" class="btn btn-xs btn-primary" title="edit">
    <i class="fa fa-edit"></i>
</a>         
@endif

@if($isDelete)
<a data-id="{{ $row->id }}" href="{{ route($currentRoute.'.destroy',['id' => $row->id]) }}" class="btn btn-xs btn-danger btn-delete-record" title="delete">
    <i class="fa fa-trash-o"></i>
</a>          
@endif

@if(isset($isAccept) && $isAccept)
@if($row->status == 0)
<a data="{{ $row->id }}" class="btn btn-xs btn-success accepted" title="Accept">
    <i class="fa fa-check"></i>
</a>          
@endif
@endif

@if(isset($isReject) && $isReject)
@if($row->status == 0)
<a data="{{ $row->id }}" class="btn btn-xs btn-warning rejected" title="Reject" id="reject_action">
    <i class="fa fa-times"></i>
</a>          
@endif
@endif
@if(isset($isView) && $isView)
<a data-id="{{ $row->id }}" class="btn btn-xs btn-success" onclick="openView({{$row->id}})" title="view">
    <i class="fa fa-eye"></i>
</a>          
@endif
	
@if(isset($isPDF) && $isPDF)
<a href="{{ url('salary_slip/download?slip_id='.$row->id) }}" class="btn btn-xs btn-warning" title="Download PDF">
    <i class="fa fa-arrow-down" aria-hidden="true"></i>
</a>          
@endif

@if(isset($inPDF) && $inPDF)
<a href="{{ url('invoices/download?invoice_id='.$row->id) }}" class="btn btn-xs btn-warning" title="Download PDF">
    <i class="fa fa-arrow-down" aria-hidden="true"></i>
</a>          
@endif
	
@if(isset($isActive) && $isActive)
@if($row->status == 0)
<a data="{{ $row->id }}" class="btn btn-xs btn-success accepted" title="Active">
    <i class="fa fa-check"></i>
</a>          
@endif
@endif 

@if(isset($user_status_link) && $user_status_link == 1)
	@if($row->status == 1)
		<a class="btn btn-xs btn-success accepted" title="Change Status To Inactive" href="{{ url('users?changeStatus=0&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@else
		<a class="btn btn-xs btn-danger accepted" title="Change Status To Active" href="{{ url('users?changeStatus=1&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@endif	   
@endif
@if(isset($payment) && $payment == 1)
	@if($row->payment == 1)
		<a class="btn btn-xs btn-success accepted" title="Change Status To Unpaid" href="{{ url('invoices?changeStatus=0&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@else
		<a class="btn btn-xs btn-danger accepted" title="Change Status To Paid" href="{{ url('invoices?changeStatus=1&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@endif	   
@endif
	@if(isset($isShowMem) && $isShowMem)
<a class="btn btn-xs btn-warning" target="_blank" href='{{ asset("/members-family?search_member=$row->id") }}' title="Show Family Members">
	<i class="fa fa-user"></i>
</a>
@endif
@if(isset($member_status_link) && $member_status_link == 1)
	@if($row->status == 1)
		<a class="btn btn-xs btn-success accepted" title="Change Status To Inactive" href="{{ url('members?changeStatus=0&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@else
		<a class="btn btn-xs btn-danger accepted" title="Change Status To Active" href="{{ url('members?changeStatus=1&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@endif	   
@endif
</div>
