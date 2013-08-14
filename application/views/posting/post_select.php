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
			<h2 class="pull_center">Select Post Type</h2>
	    	<div id="" class="span6 offset3 post_type">
	    		<a href="/posting/text"><h1>Share a Thought</h1></a>
			</div>
	  	</div>
	  	<div class="row">
	    	<div id="" class="span6 offset3 post_type">
	    		<a href="/posting/link"><h1>Share a Link</h1></a>
			</div>
	  	</div>
	  	<div class="row">
	    	<div id="" class="span6 offset3 post_type">
	    		<a href="/posting/images"><h1>Share Images</h1></a>
			</div>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<style>
	.post_type {
		margin-bottom: 20px;
		background-color: #439e43;
		color: #FFF;
		text-align: center;
		cursor: pointer;
	}
	.post_type:hover {
		box-shadow: 5px 5px 5px #111;
			
	}
	.post_type a {
		color: white;
	}
	.post_type a:hover {
		text-decoration: none;
	}
	</style>
</html>