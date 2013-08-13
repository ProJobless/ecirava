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
	    	<h1 class="pull_center">Oops!</h1>
	    	<div id="error_box" class="span6 offset3">
	    		<p style="font-size:1.5em;text-align:center;padding:10px"><?PHP echo $error_message; ?></p>
	    		<div style="text-align:center;">
	    			<a href="/">Home</a> | <a href="javascript:(function () {window.history.back()})();">Back</a>
	    		</div>
	    	</div>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom CSS for this page. Do not add to main.css after development-->
	<style>
	#error_box a {
		color: #FFF;
		font-size: 1.3em;
	}
	#error_box p {
		font-size:1.5em;
		text-align:center;
		padding:10px;
	}
	#error_box {
		background-color: #51a351;
	}
	</style>
</html>