 
 @foreach($views as $view)
<div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
		<i class="fa fa-file"></i>Credential </div>
	</div>
	<div class="portlet-body">
		<div class="row static-info">
			<div class="col-md-5 name"> Project: </div>
			<div class="col-md-7 value"> {{$view->project_name}} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Environment: </div>
			<div class="col-md-7 value"> {{$view->environment}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Protocol: </div>
			<div class="col-md-7 value"> {{$view->protocol}} </div>
		</div>
		@if($view->protocol == 'FTP' || $view->protocol == 'SSH')
		<div class="row static-info">
			<div class="col-md-5 name"> Host Name: </div>
			<div class="col-md-7 value">
				@if(!empty($view->hostname))
				<input type="text" id="hostname_copy_{{ $view->id}}"  value="{{$view->hostname}}" />
				<button data-clipboard-target="#hostname_copy_{{ $view->id}}"  style="margin-right: 20px;" title="Click To Copy" class="copy-button btn btn-xs"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
				@endif
			</div>
		</div>
		@endif
		@if($view->protocol == 'FTP' || $view->protocol == 'CPANEL' || $view->protocol == 'SSH' || $view->protocol == 'ADMIN/WP-ADMIN' || $view->protocol == 'FRONT-END' || $view->protocol == 'HOSTING')
		<div class="row static-info">
			<div class="col-md-5 name"> User Name: </div>
			<div class="col-md-6 value">
				@if(!empty($view->username))
				<input type="text" id="username_copy_{{ $view->id}}"  value="{{$view->username}}" />
				<button data-clipboard-target="#username_copy_{{ $view->id}}"  style="margin-right: 20px;" title="Click To Copy" class="copy-button btn btn-xs"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
				@endif
			</div>
		</div>
		@endif
		@if($view->protocol == 'FTP' || $view->protocol == 'CPANEL' || $view->protocol == 'SSH' || $view->protocol == 'ADMIN/WP-ADMIN' || $view->protocol == 'FRONT-END' || $view->protocol == 'HOSTING')
		<div class="row static-info">
			<div class="col-md-5 name"> Password: </div>
			<div class="col-md-7 value">
				@if(!empty($view->password))
				<input type="text" id="password_copy_{{ $view->id}}"  value="{{$view->password}}" />
				<button data-clipboard-target="#password_copy_{{ $view->id}}"  style="margin-right: 20px;" title="Click To Copy" class="copy-button btn btn-xs"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
				@endif
			</div>
		</div>
		@endif
		@if($view->protocol == 'FTP' || $view->protocol == 'SSH')
		<div class="row static-info">
			<div class="col-md-5 name"> Port: </div>
			<div class="col-md-7 value"> {{$view->port}}</div>
		</div>
		@endif
		@if($view->protocol == 'CPANEL' || $view->protocol == 'ADMIN/WP-ADMIN' || $view->protocol == 'FRONT-END' || $view->protocol == 'HOSTING')
		<div class="row static-info">
			<div class="col-md-5 name"> URL: </div>
			<div class="col-md-7 value"><span id="url"><a target="_blank" href="{{$view->url}}"> {{$view->url}}</a></span>
			</div>
		</div>
		@endif
		@if($view->protocol == 'SSH')
		<div class="row static-info">
			<div class="col-md-5 name"> Key File: </div>
			<div class="col-md-7 value"> {{$view->key_file}}   
				@if($view->key_file != '')
				<a class='btn btn-xs btn-warning' href='{{ asset("/credentials/download/$view->id") }}' > Download </a>
				@endif
			</div>
		</div>
		@endif
		@if($view->protocol == 'SSH')
		<div class="row static-info">
			<div class="col-md-5 name"> Key File Password: </div>
			<div class="col-md-7 value">
				@if(!empty($view->key_file_password))
				<input type="text" id="key_file_password_copy_{{ $view->id}}"  value="{{$view->key_file_password}}" />
				<button data-clipboard-target="#key_file_password_copy_{{ $view->id}}"  style="margin-right: 20px;" title="Click To Copy" class="copy-button btn btn-xs"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
				@endif
			</div>
		</div>
		@endif
		@if($view->protocol == 'FTP')
		<div class="row static-info">
			<div class="col-md-5 name"> Mode: </div>
			<div class="col-md-7 value"> {{$view->mode}}</div>
		</div>
		@endif
		@if($view->protocol == 'EXTRA')
		<div class="row static-info">
			<div class="col-md-5 name"> Title: </div>
			<div class="col-md-7 value"> {{$view->title}}</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Description: </div>
			<div class="col-md-7 value"> <?php echo nl2br($view->description);?></div>
		</div>
		@endif
	</div>
</div>
@endforeach
