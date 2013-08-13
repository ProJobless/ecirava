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
	    	<div id="main" class="span9 pos_rel">
	    		<h1><?PHP echo $stream['title']; ?></h1>
	    		<div class="blocked" style="height:700px;">
	    			<div id="private" class="unselectable">
	    			<h3>This Stream is Private <br /> You must be invited to view, or a subscriber</h3>
	    			</div>
	    			<div id="request_invite" class="unselectable <?PHP if($stream['is_following'] != false) { echo 'inactive';} else { echo '" onclick="send_invite()"'; } ?>">
    					<?PHP if($stream['is_following'] === false) { echo 'Request Invite'; } else { echo 'Invite Sent'; } ?>
    				</div>
    				<div id="request_invite_info" class="unselectable">
    					<?PHP if($stream['is_following'] === false) { echo 'Ask the owner to let you follow them'; } else { echo 'You\'ve already sent a request'; } ?>
    				</div>
    				<?PHP if($stream['sub_access'] != 'off') { ?>
    				<div id="subscribe_button" class="unselectable">
	    				Subscribe
    				</div>
    				<div id="subscribe_button_info" class="unselectable">
    					Subscribe to this stream
    				</div>
    				<?PHP } ?>
	    		</div>
	  		</div>
	  		<div id="user_info" class="span3">
	  			<img src="<?PHP echo profile_pic_path($stream['id']); ?>" width="220px" height="220px" />
	  			<div class="row">
	  				<div id="info" class="span3">
	  				<h2>Stream Info</h2>
		  				<div class="row">
		  					<div class="span1 num"><h3><?PHP echo number_format($stream['num_subscribers']); ?></h3></div>
		  					<div class="span2"><h3>subscribers</h3></div>
		  				</div>
		  				<div class="row">
		  					<div class="span1 num"><h3><?PHP echo number_format($stream['following_num']); ?></h3></div>
		  					<div class="span2"><h3>followers</h3></div>
		  				</div>
		  				<div class="row">
		  					<div class="span1 num"><h3><?PHP echo number_format($stream['num_posts']); ?></h3></div>
		  					<div class="span2"><h3>posts</h3></div>
		  				</div>
	  	  			</div>
	  	  		</div>
	  	  		<div class="row">
		  			<div id="my_streams" class="span3">
		  				<div id="stream_box">

		  				</div>
		  			</div>
		  		</div>
	  			<div class="row">
		  			<div id="recommended_streams" class="span3">
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
	var is_subbed = <?PHP if($stream['is_following']) { echo 'true'; } else { echo 'false'; } ?>;
	var is_my_own = <?PHP if($stream['is_my_own']) { echo 'true'; } else { echo 'false'; } ?>;
	var stream_id = <?PHP echo $stream['id']; ?>;
	var subscribe_lock = false; // Used to prevent subscription toggles faster than the AJAX call
	var follow_lock = false; 

	$(function() {
		$('.hidden').hide();
    });

	// Sends a request to the stream owner for viewing permission
    function send_invite()
    {
    	if(follow_lock) { return; }
    	$('#request_invite').html('<img src="/resources/img/ajax-loader.gif" />'); 
    	follow_lock = true; 
    	$.post("/stream/follow", { leader: stream_id }).done(function(data) { 
    		$('#request_invite').addClass('inactive').html('Invite Sent');
    		$('#request_invite_info').html('Request Sent!');
    		console.log(data);
		});	
    }
	</script>
	<!--Custom CSS for this page, should be added to main.css after development-->
	<style>
	.pos_rel {
		position: relative;
	}
	.blocked {
		background: url('/resources/img/black_gray_stripes.png');
		box-shadow: 0px 5px 5px 5px #000;
	}

	#private {
		background-color: #51A351;
		box-shadow: 0px 5px 5px 3px #111;
		width: 75%;
		position: absolute;
		top:10%;
		left:10%;
		text-align: center;
		padding: 2.5%;
		border: 5px #FFF solid;
	}

	#request_invite {
		background-color: #6FA19C;
		box-shadow: 0px 0px 5px 2px #000;
		width: 15%;
		position: absolute;
		top:35%;
		left:10%;
		text-align: center;
		padding: .5%;
		cursor: pointer;
		float: left;
	}

	#request_invite_info {
		width: 60%;
		position: absolute;
		top:35%;
		left:30%;
		text-align: left;
		padding: .5%;
		float: left;
	}

	#subscribe_button {
		background-color: #6FA19C;
		box-shadow: 0px 0px 5px 2px #000;
		width: 15%;
		position: relative;
		top:40%;
		left:10%;
		text-align: center;
		padding: .5%;
		cursor: pointer;
		float: left;
	}

	#subscribe_button_info {
		width: 60%;
		position: absolute;
		top:45%;
		left:30%;
		text-align: left;
		padding: .5%;
		float: left;
	}

	#request_invite.inactive {
		background-color: #BF9626;
	}

	#subscribe_button.inactive {
		background-color: RED;
	}

	#info h3 {
		text-align: right;
	}

	#info .num h3 {
		text-align: left;
	}

	#info .num {
		overflow: hidden;
	}

	.info h3#num_following {

	}
	</style>
</html>