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
				<?PHP 
	    		if(is_array($stream_posts))
	    		{
	    			foreach($stream_posts as $post) { echo $post; } 
	    		}
	    		else { echo '<h4>No posts...</h4>'; }
	    		?>
	  		</div>
	  		<div id="user_info" class="span3">
	  			<img id="stream_prof_pic" src="<?PHP echo profile_pic_path($stream['id']); ?>" width="220px" height="220px" />
	  			<div class="row">
	  				<div class="span3">
		  				<?PHP if($is_logged_in && !$stream['is_my_own']) { ?><h2 id="follow_button" onclick="follow()" <?PHP if($stream['is_following']) { echo 'class="unselectable subscribed">following'; } else { echo 'class="unselectable">follow'; } ?></h2><?PHP } ?>
		  	  		</div>
	  				<div id="info" class="span3">
	  					<?PHP echo $stream['following_num']; ?> following
	  	  			</div>
	  	  		</div>
	  	  		<?PHP if($is_private) { ?>
	  	  		<div class="row">
	  	  			<div id="private_stream" class="span3">
	  	  				<span class="tooltip_me" data-placement="right" title="If you unsubscribe you will need to request access to view the stream">This stream is private</span>
  	  				</div>
  				</div>
  				<?PHP } ?>
	  	  		<div class="row">
		  			<div id="my_streams" class="span3">
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
	var stream_id = <?PHP echo $stream['id']; ?>;
	load_stream_thumbs('#stream_box', 9, 0, stream_id);
	window.onLoad = tile_images();
	</script>
</html>