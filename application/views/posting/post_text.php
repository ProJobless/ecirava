<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?PHP echo $site_title.' - '.$page_title; ?></title>
		<meta name="description" content="The home page of XXXXXX">
		<link rel="shortcut icon" href="favicon.ico" type="img/blank.icon">
	</head>
	<body id="post_link">
	<?PHP echo $this->load->view('header'); ?>
	<div class="container post_link">
		<h2 class="pull_center">Share Your Thoughts</h2>
		<div class="row">
			<div class="span3">
				<div class="posting_rules unselectable">
					Posting Guidelines
					<hr>
					<div class="rule" title="Text posts must have a title" data-placement="left">
						1. Title required
					</div>
					<div class="rule" title="Posting without tags will make your post considerably harder for users to search for" data-placement="left">
						2. Tags not required
					</div>
					<div class="rule" title="Your post must have 1 or more visible characters or it will be deleted" data-placement="left">
						3. No blank posts
					</div>
				</div>
				<div id="tags" class="user_tags" style="margin-top:20px;">
					<div style="clear:both;"></div>
					<ul id="mytags" style="">
					</ul>
				</div>
				<div id="tag_error" class="alert alert-error hidden pull_center">
					Tag Limit Exceeded
				</div>
			</div>  
	    	<div class="span6">
	    		<div class="form-horizontal content_box">
	    			<div class="control-group">
	    				<label class="control-label link_input" for="inputID">Title</label>
	    				<div class="controls">
	    					<input type="text" placeholder="100 Characters Max" maxlength="100" title="Link Title" id="inputTitle" />
	    				</div>
	    			</div>
	    		</div>
	    		<div id="error" class="alert alert-error hidden pull_center">
				</div>
				<div id="success" class="alert alert-success hidden pull_center">
					Your Post Is Live!<br />
					<a href="/stream" style="color:#000;cursor:pointer;">View Your Post</a>
				</div>
				<h4>Your Post</h4>
				<div id="content">
					<textarea id="content_text"></textarea>
				</div>
				<span class="text_info unselectable">Remaining Characters: <span id="remaining_chars">5000</span> - Capcodes Enabled</span>
	    	</div>	
	    	<div class="span3">
	    		<div class="score_box unselectable">
	    			<?PHP echo number_format($base_points, 0); ?>
	    		</div>
	    		<div class="score_info">
					<?PHP echo number_format($base_points, 0); ?> points earned per post
				</div>
				<div>
					<h4 id="submit_text" class="unselectable" onclick="submit()">Submit</h4>
				</div>
		    </div>
	  	</dLiv>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page, cannot added to main.js after development-->
	<script>
	$(function () {
		$("#mytags").tagit({
		 	removeConfirmation: true,
		 	caseSensitive: false,
		 	allowSpaces: true,
		 	placeholderText: "Tag your post!",
		 	tagLimit: 15,
		 	onTagLimitExceeded: function(){$('#tag_error').stop(true,true).show().fadeOut(1200); },
		 	autocomplete: { delay: 1, minLength: 2 },
		 	availableTags: <?PHP echo $available_tags; ?>
		});
		$('#content textarea').autogrow();
		$('.rule').tooltip();
	});

	$('textarea').keyup(function()
	{
		$('#remaining_chars').css('color', '#FFF');
    	$('#remaining_chars').html(5000 - this.value.length);
    	if(5000 - this.value.length <= 0)
    	{
    		$('#remaining_chars').css('color', 'red');
    	}
    });

	function submit()
	{
		if(locks[submit])
		{
			console.log('Last request still pending, post not submitted.');
			return;
		}
		if(time == null || Date.now() - time > 1000)
	    {
	    	locks[submit] = true;

    		var title = $('#inputTitle').val();
			var text = $('#content_text').val();
			// Get all the tags and make an array from them
			var post_tags = new Array();
			$('input[name="tags"]').each(function(index) {
				post_tags[index] = $(this).val();
			});

			$('#submit').html('Posting...');

        	$.post("/posting/submit_text", { title: title, tags: post_tags, content: text }).done(function(data) { 
        		console.log('Post submitted successfully!');
        		if(data == 'Success!') 
        		{ 
        			console.log('Success');
        			$('#error').hide();
        			$('#submit').html('Complete!');
        			$('#success').stop(true, true).show();
        		}  
        		else
        		{
        			$('#error').show().html("").html('<strong>Oops!</strong> '+data); // Show the error box with appropriate error
        			$('#submit').html('Submit');
        			locks[submit] = false;
        		}
   			});
        	// Update the time
			time = Date.now();
    	}
    	else 
    	{
    		$('#error').show().html("").html('<strong>Oops!</strong> Please wait 1 second before re-submitting.');
    		console.log('User tried to re-submit too soon.');
    		locks[submit] = false;
    	}
	}
	</script>
	<style>
	#submit_text {
		background-color: #51A351;
		color: #FFF;
		text-align: center;
		padding: 5px;
		cursor: pointer;
	}

	#submit_text:hover {
		box-shadow: 0px 0px 5px 2px #111;
	}

	.text_info {
		color: #999;
		font-style: italic;
		font-size: 16px;
		cursor: default;
	}
	#content {
		position: relative;
    	display: block;
    	background: transparent;
    	min-height: 100px;
    	color: #FFF;
    	border: 1px #FFF solid;
    	margin-top: 20px;
    	padding: 5px;
	}

	#content textarea {
		background-color: transparent;
		border: none;
		width: 445px;
		max-width: 445px;
		min-height: 100px;
		resize: none;
		color: #FFF;
	}

	#content textarea:focus {
		outline: none;
		outline-width: 0px;
		border: none;
		box-shadow: none;
	}

	#content:after {
		content: "";
    	position: absolute;
    	top: 0;
    	bottom: 0;
    	right: 0;
    	left: 0;
    	background-color: #000;
    	opacity: .25;
    	z-index: -1;
	}
	.user_tags .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
		border: 1px solid #67A65D;
		background: #76bc6b;
		color: #FFF;
		border-radius: 0px;
	}

	.user_tags ul.tagit li.tagit-choice {
		background: #76bc6b;
	}

	ul.tagit input[type="text"]{
		color: #FFF;
		height: 25px;
	}

	.user_tags .ui-widget-content {
    	position: relative;
    	display: block;
    	background: transparent;
    }
    .user_tags .ui-widget-content:after {
    	content: "";
    	position: absolute;
    	top: 0;
    	bottom: 0;
    	right: 0;
    	left: 0;
    	background-color: #000;
    	opacity: .5;
    	z-index: -1;
    }

	#mytags {
		border-radius:0px;
		background:transparent;
	}
	.content_box .add-on {
		background: url('/resources/img/background_tile_3.jpg');
		font-weight: lighter;
	}
	.score_info {
		text-align: center;
		font-style: italic;
		margin-top: 10px;
	}
	.score_box {
		padding-top: 30px;
		background-color: #439e43;
		color: #FFF;
		text-align: center;
		cursor: pointer;
		font-size: 50px;
		height:50px;
	}

	.control-label.link_input {
		text-align: left;
		padding-left: 10px;
		font-size: 24px;
		width:50px;
	}

	.content_box .controls {
		margin-left:0px;
	}

	.content_box .controls input {
		width:350px;
	}

	.rule {
		text-align: left;
		margin: 3px 0px 0px 5px;
		cursor: pointer;
	}

	.posting_rules {
		position: relative;
		display: block;
		background-color: transparent;
		color: #FFF;
		text-align: center;
		padding-top: 3px;
		cursor: default;
	}

	.posting_rules:after {
		content: "";
		background-color: #000;
		opacity: 0.2;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		position: absolute;
		z-index: -1;
	}

	.posting_rules hr {
		margin: 3px auto 3px auto;
	}
	.post_link .content_box {
		background-color: #439e43;
		color: #FFF;
		text-align: center;
		cursor: pointer;
		padding: 10px 0px;
	}
	.content_box a {
		color: white;
	}
	.content_box a:hover {
		text-decoration: none;
	}

	#post_link .ui-corner-all {
		border-radius: 0px;
	}
	/*#post_link ul.ui-autocomplete li a:hover {
		background-color: #439e43;
		display: block;
		border: 1px #999 solid;
	}*/
	li.ui-menu-item a {
    	display: block;
	}

	.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus {
	    background: #439e43;
	    border: none;
	    border: 1px #999 solid;
	}
	</style>
</html>