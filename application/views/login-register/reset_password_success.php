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
			<h2 class="pull_center">Password Reset!</h2>
	    	<div id="login_box" class="span6 offset3">
	    		<?PHP echo $reset_username; ?> your new password is:<br /> 
	    		<?PHP echo $new_password; ?>
	  		</div>
		</div>
	</div>
	</body>
	<!--Load CSS and JS after body to improve page performance-->
	<?PHP echo $this->load->view('head'); ?>
</html>