<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?PHP echo $site_title.' - '.$page_title; ?></title>
		<link rel="shortcut icon" href="favicon.ico" type="img/blank.icon">
	</head>
	<body>
	<?PHP echo $this->load->view('header'); ?>
	<div class="container">
		<div class="row">
	    	<h2 class="pull_center">Register</h2>
	    	<div id="login_box" class="span6 offset3">
	    		<div class="form-horizontal">
	    			<div class="control-group">
	    				<label class="control-label" for="inputUsername">Username</label>
	    				<div class="controls">
	    					<input type="text" name="username" id="inputUsername" placeholder="3-64 Characters" />
	    				</div>
	    			</div>
	    			<div class="control-group">
	    				<label class="control-label" for="inputEmail">Email</label>
	    				<div class="controls">
	    					<input type="text" name="email" id="inputEmail" placeholder="Used to confirm registration" />
	    				</div>
	    			</div>
	    			<div class="control-group">
	    				<label class="control-label" for="inputPassword">Password</label>
	    				<div class="controls">
	    					<input type="password" name="user_password" id="inputPassword" placeholder="At least 4 Characters" />
	    				</div>
	    			</div>
	    			<div class="control-group">
	    				<label class="control-label" for="inputPassword">Confirm Password</label>
	    				<div class="controls">
	    					<input type="password" name="user_password" id="inputPasswordConf" placeholder="Passwords Must Match" />
	    				</div>
	    			</div>
	    		</div>
	    		<div style="text-align:center;">
	    			<a href="/register/resend_email">Resend Confirmation Email</a> | <a href="/login/password_reset">Forgot Password?</a>
	    		</div>
	    	</div>	
	    	<div class="span6 offset3">
	    		<div id="error" class="alert alert-error hidden pull_center">
				</div>
			</div>
			<div class="span6 offset3">
	    		<div id="success" class="alert alert-success hidden pull_center">
	    			<strong>Success!</strong> You should receive an email with instructions on how to confirm your email address
				</div>
			</div>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page. CANNOT be added to main.js after development-->
	<script>
	// On DOM Load
	var time = null;
	$(function () {
    $('.hidden').hide(); // Hides all elements marked with "hidden" on pageload
	});
	// Sends the user id (username or email) and password to the server for validation
	// Returns an error if it fails and returns them to the previous page on success
	// Also updates their session to show that they are logged in
	$(document).keypress(function(e) {
    if(e.which == 13) {
    	// Check if it's been at least 2.5 seconds since the last buton push to prevent enter-key spam
    	if(time == null || Date.now() - time > 1000)
    	{
    		var password = $('#inputPassword').val();
    		var password_conf = $('#inputPasswordConf').val();
			var username = $('#inputUsername').val();
			var email = $('#inputEmail').val();
        	$.post("/register/validate", { username: username, password: password, password2: password_conf, email: email}).done(function(data) { 
        		console.log('Form submitted successfully!');
        		if(data == 'Success!') 
        		{ 
        			$('.hidden').hide();
        			$('#success').show();
        		}
        		else
        		{
        			$('.hidden').hide();
        			$('#error').show().html("").html('<strong>Oops!</strong> '+data); // Show the error box with appropriate error
        		}
        	});
        	// Update the time
        	time = Date.now();
        }
        else
        {
        	$('#error').show().html("").html('<strong>Oops!</strong> Please wait 1 second before re-submitting.');
	    	console.log('User tried to re-submit too soon.');
        }
    	}
	});
	</script>
</html>