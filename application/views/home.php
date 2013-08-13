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
	<div class="container left_right_shadow home_page">
		<div class="row" style="height:460px">
	    	<div id="featured_post" class="span6">
	    		<img src="<?PHP echo $featured_image; ?>" title="Featured Image" />
		    </div>
		    <div id="front_page_news" class="span6">
		    	<h1><?PHP echo $news_title; ?></h1>
		    	<hr />
		    	<p><?PHP echo $front_page_news; ?></p>
		    </div>
	  	</div>
	</div>
	<?PHP echo $this->load->view('footer'); ?>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom CSS for this page, to be added to main.css after development-->
	<style>
	#featured_post {
		background-color: #46a546;
		height: 460px;
	}
	#featured_post img {
		margin: 10px;
		border: solid #AAA 5px;
		box-shadow: 0px 0px 15px 5px #555
	}
	#front_page_news p {
		margin-right: 20px;
	}
	#front_page_news h1 {
		text-align: center;
		margin-right: 20px;
		margin-left: 0px;
	}
	.home_page hr {
		margin-top: 8px;
		margin-bottom: 8px;
		margin-right: 20px;
		border-top: none;
		border-bottom: solid #888 1px;
	}
	</style>
</html>