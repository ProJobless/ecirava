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
	    	<h1 class="pull_center">You Do Not Have Sufficient Permissions to View this Page</h1>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
</html>