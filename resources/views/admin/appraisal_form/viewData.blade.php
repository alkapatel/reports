@extends('admin.layouts.app')
@section('styles')

@endsection

@section('content')
<div class="page-content">
<div class="container">

<div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
		<i class="fa fa-file"></i>Appraisal Form </div>
	</div>
	<div class="portlet-body">
		<div class="row static-info">
			<div class="col-md-12"> <b>Rate your Past year</b> (0-3 Bad, 4-7 Average, 8-10 Good)</div>
			<div class="col-md-12"> {{$appraisal->past_year_rate}} </div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>What are some of the things you have achieved last year?</b> </div>
			<div class="col-md-12"> <?php echo nl2br($appraisal->past_year_achieved);?> </div>
		</div>
		<hr />		
		<div class="row static-info">
			<div class="col-md-12"> <b>Rate your job satisfaction </b>(0-3 Bad, 4-7 Average, 8-10 Good) </div>
			<div class="col-md-12"> {{$appraisal->job_satisfaction}}</div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>Do you feel that your achievements were recognized and rewarded?</b>  </div>
			<div class="col-md-12"> <?php echo nl2br($appraisal->achievements);?> </div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>State some of the goals you have set for the next year?</b> </div>
			<div class="col-md-12"> <?php echo nl2br($appraisal->goal);?> </div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>Describe your duties and responsibilities. </b> </div>
			<div class="col-md-12"> <?php echo nl2br($appraisal->duty_responsibility);?> </div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>What things can the company do to better your working environment? </b></div>
			<div class="col-md-12"> <?php echo nl2br($appraisal->suggestion);?></div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>Total experience as on 31st December 2017. </b> (Ex. 5 years and 6 months) </div>
			<div class="col-md-12"> {{$appraisal->years}} - Years  {{$appraisal->months}} - Months</div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>Current Salary </b></div>
			<div class="col-md-12"> {{$appraisal->current_salary}}</div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>Expected Salary (Year - 2018) </b></div>
			<div class="col-md-12"> {{$appraisal->expected_salary}}</div>
		</div>
		<hr />
		<div class="row static-info">
			<div class="col-md-12"> <b>Raise (%) </b></div>
			<div class="col-md-12"> {{$appraisal->raise}}</div>
		</div>
	</div>
</div>

 </div>
</div>
@endsection