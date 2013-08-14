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
			<div class="span3">
				<div id="account_profile_pic" onclick="display_img_uploader()">
				</div>
				<div style="height:75px;padding-top:3px;">
					<input id="file_upload" type="file" name="file_upload" />
				</div>
				<div class="account_sidebar">
					<a href="/posting"><h1>Post</h1></a>
				</div>
				<div class="account_sidebar">
					<a href="/user/admin"><h1>Admin</h1></a>
				</div>
			</div>
			<div class="span9">
				<h1>My Account</h1>
				<hr />
				<form class="form-horizontal account_main_update">
					<div class="row">
						<div id="alert_place_holder">
						<div class="span7 offset1">
					    	<div id="error" class="alert alert-error hidden pull_center">
							</div>
						</div>
						<div class="span7 offset1">
					    	<div id="success" class="alert alert-success hidden pull_center">
							</div>
						</div>
					</div>
						<div class="span7 offset1">
							<div class="account_info_box">
								<div id="username_image_alert" class="image_alert hidden">
									<img src="/resources/img/error-alert-grey-icon.png" />
								</div>
								<div class="control-group">
								    <label class="control-label" for="inputUsername">Username</label>
								    <div class="controls">
								      	<input type="text" id="inputUsername" placeholder="<?PHP echo $user['username']; ?>">
								    </div>
								    <span class="help_text">Usernames must be unique and between 3-64 letters long</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="span7 offset1">
							<div class="account_info_box">
								<div id="email_image_alert" class="image_alert hidden">
									<img src="/resources/img/error-alert-grey-icon.png" />
								</div>
								<div class="control-group">
								    <label class="control-label" for="inputEmail">Email</label>
								    <div class="controls">
								      	<input type="text" id="inputEmail" placeholder="<?PHP echo $user['email']; ?>">
								    </div>
								    <span class="help_text">Emails must be unique and correctly formatted</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="span7 offset1">
							<div class="account_info_box password">
								<div id="password_image_alert" class="image_alert hidden">
									<img src="/resources/img/error-alert-grey-icon.png" />
								</div>
								<div class="control-group">
								    <label class="control-label" for="inputOldPassword">Update Password</label>
								    <div class="controls">
								      	<input type="password" id="inputOldPassword" placeholder="Current Password">
								    </div>
								    <div class="controls">
								      	<input type="password" id="inputPassword" placeholder="Desired Password">
								    </div>
								    <div class="controls">
								      	<input type="password" id="inputPasswordConf" placeholder="Desired Password Again">
								    </div>
								    <span class="help_text">New passwords must be 4 characters or longer</span>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page. CANNOT be added to main.js after development-->
	<script>
	window.onLoad = uploadifive_account();

	// Sends the user id (username or email) and password to the server for validation
	// Returns an error if it fails and returns them to the previous page on success
	// Also updates their session to show that they are logged in
	$(document).keypress(function(e) {
    if(e.which == 13) {
    	// Check if it's been at least 2.5 seconds since the last buton push to prevent enter-key spam
    	if(time == null || Date.now() - time > 1000)
    	{

    		// Determine what element is focused
    		if($('#inputUsername').is(':focus')) 
    		{ 
    			console.log('Submitting Username'); 
    			username = $('#inputUsername').val();
    			$.post("/user/update_username", { username: username }).done(function(data) { update_complete(data, 'username'); });
    		}
    		if($('#inputEmail').is(':focus')) 
    		{ 
    			console.log('Submitting Email'); 
    			email = $('#inputEmail').val();
    			$.post("/user/update_email", { email: email }).done(function(data) { update_complete(data, 'email'); });
    		}
    		if($('#inputPassword').is(':focus') || $('#inputOldPassword').is(':focus') || $('#inputPasswordConf').is(':focus'))
    		{
    			console.log('Submitting Password');
    			old_password = $('#inputOldPassword').val();
	    		password = $('#inputPassword').val();
	    		password_conf = $('#inputPasswordConf').val();
	    		$.post("/user/update_password", { old_password: old_password, password: password, password_conf: password_conf }).done(function(data) { update_complete(data, 'password'); });
    		}
    		else
    		{
    			console.log('Could not determine focused element. Form not submitted.');
    		}
        	
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
	<!--Custom CSS for this page. Due to PHP or JS needs should NOT be added to main.css-->
	<style>
	#alert_place_holder {
		height: 49px;
		display: block;
	}
	#alert_place_holder .alert {
		margin-bottom: 10px;
	}
	#account_profile_pic {
		height: 220px;
		width: 220px;
		margin-top: 15px;
		background-repeat: no-repeat;
		background-size: cover;
		background-image: url('<?PHP echo profile_pic_path($user_id); ?>');
		cursor: pointer;
		position: relative;
	}

	.update_profile_pic {
	background-color: #439e43;
	background-image: none;
	color: #FFF;
	font-weight: bold;
	text-align: center;
	margin: 0px auto;
	border-radius: 0px;
	padding-top: 0px;
	border: none;
	cursor: pointer;
	}

	.update_profile_pic:hover {
		background-color: rgb(51, 95, 51);
		background-image: none;
		cursor: pointer;
	}
	</style>
</html>