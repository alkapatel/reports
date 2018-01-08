<!DOCTYPE html>
<html>
<head>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<title>Recaptcha</title>
</head>
<body>
<form action="" method="POST">
	<div class="form-group row">
		<div class="form-group">
			<label >Name</label>
			<input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Enter Name" name="name" value="">
		</div>
		<div class="form-group">
			<label >Email</label>
			<input type="text" class="form-control"  placeholder="Email" name="email" value="">
		</div>
		<div class="form-group">
			<label >Email</label>
			<input type="text" class="form-control"  placeholder="Message" name="message" value="">
		</div>

		<div class="g-recaptcha" data-sitekey="6Lf0cT4UAAAAAMGWJ10BLwm__qPhPqrHu8RKdHqI"></div>
		<input type="submit" name="submit" value="SUBMIT">
	</div>
</form>
	
<div class="g-recaptcha" data-sitekey="6Lf0cT4UAAAAAMGWJ10BLwm__qPhPqrHu8RKdHqI"></div>
</body>
</html>


