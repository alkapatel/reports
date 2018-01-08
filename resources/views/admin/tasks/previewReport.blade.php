<span>Username : <b> {{$fullname}} </b> Month : <b> {{$months}} </b></span>
<a class="btn btn-default pull-right">
    Total Hours: <span><b> {{ $total}} </b></span>
</a>    
<div class="clearfix">&nbsp;</div>
<table class="table table-bordered table-hover">
<thead>
    <tr>
        <th style="background-color: #b0bdc4;" width="5%"><b>No.</b></th>
        <th style="background-color: #b0bdc4;" width="10%"><b>Project</b></th>
        <th style="background-color: #b0bdc4;" width="15%"><b>Task</b></th>
        <th style="background-color: #b0bdc4;" width="10%"><b>Date</b></th>
        <th style="background-color: #b0bdc4;" width="5%"><b>Hour</b></th>
        <th style="background-color: #b0bdc4;" width="7%"><b>Status</b></th>
        <th style="background-color: #b0bdc4;" width="10%"><b>Reference Link</b></th>
    </tr>
</thead>
<tbody>
    <?php $i =1;?>
    @foreach($reports as $report)
    <tr>
        <td>{{ $i }}</td>
        <td>{{ $report->project_name}}</td>
        <td>{{ $report->title}}</td>
        <td><?php echo date("j M, Y",strtotime($report->task_date)); ?></td>
        <td>{{ $report->total_time}}</td>
        <td>@if($report->status == 1)Completed            @else In Progress            @endif        </td>
        <td>{{ $report->ref_link}}</td>
    </tr>
    <?php  $i++;?>
    @endforeach
    <tr>
        <td colspan="4">Total Hours:</td>
        <td><b> {{ $total}} </b></td>
        <td colspan="2">
    </tr>
</tbody>
</table>