<h4>Dear Sir, </h4>
<p>Below are the details of leave request:</p>
<p>
    <b>Leave Request By: </b> {{$firstname}} {{$lastname}} .
</p>
@if($from_date == $to_date)
    <p>
        <b>Leave Request Date:</b>
        &nbsp;{{$from_date}}  {{$from_half}} 
    </p>
@else
    <p>
        <b>Leave Request Date:</b>
        &nbsp; {{$from_date}} {{$from_half}} TO {{$to_date}} {{$to_half}}
    </p>
@endif

<p><b>Leave Reason:</b></p>
<p>{{$description}}</p>


<p><span>Thanking You,</span></p>
<p>Yours Sincerely,</span></p>
<p>
    <span>
        <I>{{$firstname}} {{$lastname}} .
        </I>
    </span>
</p>