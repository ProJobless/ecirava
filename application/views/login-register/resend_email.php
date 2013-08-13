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
			<h2 class="pull_center">Resend Email</h2>
	    	<div id="login_box" class="span6 offset3">
	    		<div class="form-horizontal">
	    			<div class="control-group">
	    				<label class="control-label" for="inputID">Email or Username</label>
	    				<div class="controls">
	    					<input type="text" name="user_id" id="inputID" placeholder="Used during registration" />
	    				</div>
	    			</div>
	    	</div>	
	  	</div>
	  	<div class="span6 offset3">
	    	<div id="error" class="alert alert-error hidden pull_center">
			</div>
			<div id="success" class="alert alert-success hidden pull_center">
				Your email has been resent successfully.<br />
				<a onclick="history.back();">Back</a>
			</div>
		</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page. CANNOT be added to main.js after development-->
	<script>
	// On DOM load
	$(function () {
    $('.hidden').hide(); // Hides all elements marked with "hidden" on pageload
	});
	// Sends the user's email or username to the server which sends the user an email with a password reset link
	// Returns an error if it fails
	$(document).keypress(function(e) {
    if(e.which == 13) {
			var user_id = $('#inputID').val();
        	$.post("/register/resend_email_validate", { userID: user_id }).done(function(data) { 
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
    	}
	});
	</script>
</html>