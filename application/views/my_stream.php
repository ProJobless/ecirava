<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?PHP echo $site_title.' - '.$page_title; ?></title>
		<link rel="shortcut icon" href="favicon.ico" type="img/blank.icon">
	</head>
	<body>
	<?PHP echo $this->load->view('header'); ?>
	<div class="container stream">
		<div class="row">
	    	<div id="main" class="span9">
	    		<h1><?PHP echo $stream['title']; ?></h1>
	    		<div id="content">
	    			<?PHP 
		    		if(is_array($stream_posts))
		    		{
		    			foreach($stream_posts as $post) { echo $post; } 
		    		}
		    		else { echo '<h4>No posts...</h4>'; }
		    		?>
	    		</div>
	    		<div style="clear:both;"></div>
	    		<div class="load_more">
	    			<?PHP if($stream['total_posts'] > 10) { ?>
						<h4 class="green_bg" onclick="load_more_posts('my_stream')">Load More Posts</h4>
    				<?PHP } ?>
    			</div>
	  		</div>
	  		<div id="user_info" class="span3">
	  			<img id="stream_prof_pic" src="<?PHP echo profile_pic_path($user_id); ?>" width="220px" height="220px" />
	  			<div class="row">
	  				<div id="info" class="span3">
	  					You Are Following: <?PHP echo $stream['following_num']; ?> Streams<br />
	  	  			</div>
	  	  		</div>
	  	  		<div class="row">
		  			<div id="my_streams" class="span3">
		  				<h2 onclick="load_my_streams(this, 9, 0, null)">My Streams</h2>
		  				<div id="stream_box">

		  				</div>
		  			</div>
		  		</div>
	  		</div>
	  	</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page. CANNOT be added to main.js after development-->
	<script>
	var total_num_following = <?PHP echo $stream['following_num']; ?>;
	var total_posts = <?PHP echo $stream['total_posts']; ?>;
	var stream_id = <?PHP echo $stream['id']; ?>;
	var offset = 10;
	load_stream_thumbs();
	window.onLoad = tile_images();
	</script>

	<!--Custom CSS for this page, should be added to main.css after development-->
	<style>
	.container.stream {
		margin-top: 15px; 
	}
	.stream #main h1 {
		border-bottom: 1px #FFF solid;
	}
	#stream_prof_pic {
		border: 5px #000 solid;
	}
	#main #no_content {
		font-style: italic;
		color: #888;
		font-size: 16px;
	}

	#info {
		text-align: center;
	}
	#recommended_streams h2 {
		font-size: 29px;
		text-align: center;
		margin-bottom: 5px;
	}
	#my_streams h2 {
		cursor: pointer;
		text-align: center;
	}
	#stream_box {
		position: relative;
		display: block;
		background-color: transparent;
		border-bottom: 1px #FFF solid;
		margin: auto;
	}

	#stream_box:after {
		content: "";
		background-color: #000;
		opacity: 0.5;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		position: absolute;
		z-index: -1;
	}
	.stream_box {
		width: 60px;
		height: 60px;
		padding: 5px;
		margin: 1px;
		float: left;
		cursor: pointer;
	}

	.stream_box img {
		width: 60px;
		height: 60px;
	}
	</style>
</html>