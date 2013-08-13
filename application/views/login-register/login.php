<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?PHP echo $site_title.' - '.$page_title; ?></title>
		<meta name="description" content="The home page of XXXXXX">
		<link rel="shortcut icon" href="favicon.ico" type="img/blank.icon">
	</head>
	<body>
	<?PHP echo $this->load->view('header'); ?>
	<div class="container">
		<div class="row">
			<h2 class="pull_center">Login</h2>
	    	<div id="login_box" class="span6 offset3">
	    		<div class="form-horizontal">
	    			<div class="control-group">
	    				<label class="control-label" for="inputID">Email or Username</label>
	    				<div class="controls">
	    					<input type="text" name="user_id" id="inputID" placeholder="" />
	    				</div>
	    			</div>
	    			<div class="control-group">
	    				<label class="control-label" for="inputPassword">Password</label>
	    				<div class="controls">
	    					<input type="password" name="user_password" id="inputPassword" placeholder="" />
	    				</div>
	    			</div>
	    		</div>
	    		<div style="text-align:center;">
	    			<a href="/login/password_reset">Forgot Password?</a> | <a href="/register">Register</a>
	    		</div>
	    	</div>	
	    	<div class="span6 offset3">
	    		<div id="error" class="alert alert-error hidden pull_center">
				</div>
				<div id="success" class="alert alert-success hidden pull_center">
					You have logged on successfully.<br />
					<a onclick="history.back();">Back</a>
				</div>
			</div>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page, to be added to main.js after development-->
	<script>
	var time = null;
	$(function () {
    $('.hidden').hide(); // Hides all elements marked with "hidden" on pageload
	});
	// Sends the user id (username or email) and password to the server for validation
	// Returns an error if it fails and returns them to the previous page on success
	// Also updates their session to show that they are logged in
	$(document).keypress(function(e) {
    if(e.which == 13) {
    	    if(time == null || Date.now() - time > 1000)
    	    {
	    		var user_password = $('#inputPassword').val();
				var user_id = $('#inputID').val();
	        	$.post("/login/validate", { id: user_id, password: user_password }).done(function(data) { 
	        		console.log('Form submitted successfully!');
	        		if(data == 'Success!') { window.history.back(); }  // Go back to previous page on success
	        		else
	        		{
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