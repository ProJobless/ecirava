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
	    	<div class="span6 offset3 content_box">
	    		<a href="/login"><h1>You Must Be Logged in to Post</h1></a>
			</div>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<style>
	.content_box {
		margin-top: 10px;
		background-color: #439e43;
		color: #FFF;
		text-align: center;
		cursor: pointer;
	}
	.content_box a {
		color: white;
	}
	.content_box a:hover {
		text-decoration: none;
	}
	</style>
</html>