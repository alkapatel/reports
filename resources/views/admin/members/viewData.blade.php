 <div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
		<i class="fa fa-user"></i> <?php echo ucfirst($view->firstname); ?>  <?php echo ucfirst($view->middlename); ?>  <?php echo  ucfirst($view->lastname); ?></div>
	</div>
	<div class="portlet-body">
		<div class="row static-info">
			<div class="col-md-5 name"> Form Number: </div>
			<div class="col-md-7 value"> {{$view->form_number}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> First Name: </div>
			<div class="col-md-7 value"> {{$view->firstname}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Middle Name: </div>
			<div class="col-md-7 value"> {{$view->middlename}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Last Name: </div>
			<div class="col-md-7 value"> {{$view->lastname}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Village: </div>
			<div class="col-md-7 value"> {{$view->village}} </div>
		</div> 
		<div class="row static-info">
			<div class="col-md-5 name"> Address: </div>
			<div class="col-md-7 value"> {{$view->address}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Building: </div>
			<div class="col-md-7 value"> {{$view->building}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Mobile: </div>
			<div class="col-md-7 value"> {{$view->mobile}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Blood Group: </div>
			<div class="col-md-7 value"> {{$view->blood_group}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Profession: </div>
			<div class="col-md-7 value"> {{$view->profession}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Organization: </div>
			<div class="col-md-7 value"> {{$view->organization}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Family Member Count: </div>
			<div class="col-md-7 value"> {{$view->family_member_count}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Industry: </div>
			<div class="col-md-7 value"> {{$view->industry}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Group Leader: </div>
			<div class="col-md-7 value">
			<?php $leader_name ='';
                if(!empty($view->group_leader)){
                    $detail = App\Models\Member::find($view->group_leader);
                    $leader_name = ucfirst($detail->firstname).' '.ucfirst($detail->middlename).' '.ucfirst($detail->lastname);
                }
        	?> 
        {{$leader_name}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Satatus: </div>
			<div class="col-md-7 value"> @if($view->status == 1) <div class="btn btn-xs btn-success"> Active</div> @else <div class="btn btn-xs btn-danger">In Active</div> @endif</div>
		</div>
	</div>
</div>

 