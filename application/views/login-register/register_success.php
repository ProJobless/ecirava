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
	    	<h1 class="pull_center">Success!</h1>
	    	<div id="login_box" class="span6 offset3">
	    		<p style="font-size:1.5em;text-align:center;padding:10px">You have successfully confirmed you registration at <?PHP echo $site_title; ?>. Click below to login.</p>
	    		<div style="text-align:center;">
	    			<a href="/login">Login</a>
	    		</div>
	    	</div>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom CSS for this page. Do not add to main.css after development-->
	<style>
	#login_box a {
		color: #222;
		font-size: 1.3em;
	}
	</style>
</html>