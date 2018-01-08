<h4>Dear {{$firstname}} {{$lastname}}, </h4>
<p>Below are the details of leave request.</p>
<p><b>Leave Request By: </b> {{$firstname}} {{$lastname}} .
</p>
@if($status == 1)
<!--send Accepted leave request email to User.-->
<p>
    <b>Leave Status:</b> <span><b style='color:blue'>Approved.</span></b>
</p>
@else
<p>
    <b>Leave Status:</b> <span><b style='color:red'>Rejected.</span></b>
</p>
<p>
    <b>Rejected Leave Reason:</b> <span><b style='color:blue'>{{$reject_reason}}.</span></b>
</p>
@endif                                                
@if($from_date == $to_date)
    <p>
        <b>Leave Request Date:</b>
        &nbsp;{{$from_date}}
    </p>
@else
<p>
    <b>Leave Request Date:</b>
            &nbsp; {{$from_date}}  TO {{$to_date}}
</p>                                             
@endif


<p><span>Thank  You</span></p>
