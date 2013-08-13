<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?PHP echo $site_title.' - '.$page_title; ?></title>
		<link rel="shortcut icon" href="favicon.ico" type="img/blank.icon">
	</head>
	<body>
	<?PHP echo $this->load->view('header'); ?>
	<div id="user_admin" class="container">
		<div class="row">
			<div class="span3">
				<a href="/stream"><div class="account_profile_pic"></div></a>
				<div class="account_sidebar">
					<a href="/posting"><h1>Post</h1></a>
				</div>
				<div class="account_sidebar">
					<a href="#you_are_already_on_this_page"><h1>Admin</h1></a>
				</div>
			</div>
			<div class="span9">
				<a href="/user" class="no_decoration"><h1>My Account</h1></a>
				<hr />
				<div id="user_admin_menu" class="row unselectable">
					<div class="span2 option active" onclick="tabs(this, 'stream')">
						stream
					</div>
					<div class="span2 option" onclick="tabs(this, 'posts')">
						posts
					</div>
					<div class="span2 option" onclick="tabs(this, 'comments')">
						comments
					</div>
					<div class="span2 option" onclick="tabs(this, 'more')">
						more
					</div>
				</div>
				<div id="alert_box" class="row">
					<div id="alert_place_holder">
						<div class="span7 offset1">
					    	<div id="error" class="alert alert-error hidden pull_center">
							</div>
						</div>
						<div class="span7 offset1">
					    	<div id="success" class="alert alert-success hidden pull_center">
							</div>
						</div>
					</div>
				</div>
				<div id="stream_options" class="user_admin_tab">
					<form class="form-horizontal account_main_update" action="javascript:void(0)">
						<div class="row">					
							<div class="span7 offset1">
								<div class="account_info_box">
									<div id="username_image_alert" class="image_alert hidden">
										<img src="/resources/img/error-alert-grey-icon.png" />
									</div>
									<div class="control-group">
									    <label class="control-label" for="inputStreamName">Stream Title</label>
									    <div class="controls">
									      	<input type="text" id="inputStreamName" placeholder="<?PHP echo $stream['title']; ?>" >
									    </div>
									    <span class="help_text">Stream Titles must be unique and between 3-50 letters long</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="span2 offset1">
								<h4>Stream Access</h4>
							</div>
							<div id="stream_access" class="span2 stream_status unselectable" onclick="stream_access()">
								<h4>Open</h4>
							</div>
							<div class="span1 offset2">
								<h4 id="stream_access_help" data-placement="right" title="" class="tooltip_me">?</h4>
							</div>
						</div>
						<div class="row" style="margin-top:20px;">
							<div class="span2 offset1">
								<h4>Stream Subscription</h4>
							</div>
							<div id="stream_subscription" class="span2 stream_status unselectable" onclick="stream_subscription()">
							</div>
							<div class="span1 dark_input">
								<input id="inputSubFee" type="text" maxlength="3" placeholder="<?PHP echo $stream['sub_fee']; ?>" />
							</div>
							<div id="fee_desc" class="span1">
								$5 - 999<br /><br />
								per month
							</div>
							<div class="span1">
								<h4 id="stream_subscription_help" data-placement="right" title="" class="tooltip_me">?</h4>
							</div>
							<div class="span7 offset1" style="border-bottom:1px #FFF solid;margin-bottom:10px;"></div>
						</div>
						<div class="row">
							<div class="span2 offset1">
								<h4>Add Subscriber</h4>
							</div>
							<div class="span3 dark_input">
								<input id="inputAddSub" type="text" maxlength="100" placeholder="Username Only" />
								<input id="inputNumMonths" type="text" maxlength="2" placeholder="1" />
								<span class="small_vert_desc">Num<br />of<br />Months<br /></span>
							</div>
							<div class="span3">
								<h4 id="num_total_subs" class="pull-left sub_count unselectable" onclick="$(this).hide();$('#num_paid_subs').show()"><?PHP echo $total_subs; ?></h4>
								<h4 id="num_paid_subs" class="pull-left hidden sub_count unselectable" onclick="$(this).hide();$('#num_total_subs').show()"><?PHP echo $paid_subs; ?></h4>
								<h4 class="pull-left">&nbsp;&nbsp;Subs</h4>
							</div>
						</div>
						<div class="row">
							<div class="span7 offset1 invite_requests">
							<h3 data-toggle="collapse" data-target="#stream_invite_requests" class="unselectable">Stream Invite Requests</h3>
							<div id="stream_invite_requests" class="collapse in">
								<div class="user_invite_request">
						  			<div class="row">
						  					<div class="span3 dark_input">
						  						<input id="user_box" class="search" placeholder="Username" type="text" />
						  					</div>
						  					<div class="span2 page_counter dark_input">
						  						<div class="pull-left desc">
						  							Results<br />per<br />page
						  						</div>
						  						<input id="user_display_num" class="results_per_page" value="10" placeholder="10" type="text" maxlength="3" />
						  					</div>
						  					<div class="span1 prevnext">
						  						<a id="user_prev" href="javascript:void(0)" onclick="user_page('prev')">Prev</a>
						  						<br />
						  						<a id="user_next" href="javascript:void(0)" onclick="user_page('next')">Next</a>
						  					</div>
						  					<div class="span1 user_list_stats" style="text-align:left;">
					  						<div class="pull-left desc">
					  							Page: 
					  						</div>
					  						<div id="user_page" class="pull-right desc">
					  							0
					  						</div>
					  						<div style="clear:both"></div>
					  						<div class="pull-left desc">
					  							Results: 
					  						</div>
					  						<div id="user_results" class="pull-right desc">
					  							0
					  						</div>
					  						<div style="clear:both"></div>
					  						<div class="pull-left desc">
					  							Total: 
					  						</div>
					  						<div class="pull-right desc">
					  							<?PHP echo 1; ?>
					  						</div>
						  				</div>
						  				<div id="list_preview_1" class="span1 list_preview_img">
						  				</div>
						  			</div>
						  			<div class="info_list">
						  				<div class="row">
						  					<div class="span2 collumn" onclick="order_users('username')">
						  						Username
						  					</div>
						  					<div class="span2 collumn" onclick="order_users('user_group')">
						  						Status
						  					</div>
						  					<div class="span2 offset1 collumn" style="text-align:center;cursor:default">
						  						Action
						  					</div>
						  				</div>
						  			</div>
						  			<div class="row">
							  			<div id="followers" class="span7">
							  			</div>
						  			</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div id="posts_options" class="hidden user_admin_tab">
					<form class="form-horizontal account_main_update">
						<div class="row">					
							<div class="span7 offset1">
								<div class="account_info_box">
									<div id="username_image_alert" class="image_alert hidden">
										<img src="/resources/img/error-alert-grey-icon.png" />
									</div>
									<div class="control-group">
									    <label class="control-label" for="inputUsername">Class</label>
									    <div class="controls">
									      	<input type="text" id="inputUsername" placeholder="<?PHP echo $user['username']; ?>">
									    </div>
									    <span class="help_text">Usernames must be unique and between 3-64 letters long</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="span7 offset1">
								<div class="account_info_box">
									<div id="email_image_alert" class="image_alert hidden">
										<img src="/resources/img/error-alert-grey-icon.png" />
									</div>
									<div class="control-group">
									    <label class="control-label" for="inputEmail">Email</label>
									    <div class="controls">
									      	<input type="text" id="inputEmail" placeholder="<?PHP echo $user['email']; ?>">
									    </div>
									    <span class="help_text">Emails must be unique and correctly formatted</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="span7 offset1">
								<div class="account_info_box password">
									<div id="password_image_alert" class="image_alert hidden">
										<img src="/resources/img/error-alert-grey-icon.png" />
									</div>
									<div class="control-group">
									    <label class="control-label" for="inputOldPassword">Update Password</label>
									    <div class="controls">
									      	<input type="password" id="inputOldPassword" placeholder="Current Password">
									    </div>
									    <div class="controls">
									      	<input type="password" id="inputPassword" placeholder="Desired Password">
									    </div>
									    <div class="controls">
									      	<input type="password" id="inputPasswordConf" placeholder="Desired Password Again">
									    </div>
									    <span class="help_text">New passwords must be 4 characters or longer</span>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page. CANNOT be added to main.js after development-->
	<script>
	
	var success_message = {
		'title': ' Your stream title has been updated.',
		'access': ' Your Stream Access has been updated.',
		'sub_access': ' Your Subscription Status has been updated.',
		'add_sub': ' They are now subscribed to your stream',
		'sub_fee': 'Your Subscription Fee has been Updated'
	};

	// Set the defualt loadout for users
	var user_list = {
		display : 	10,
		page 	: 	0,
		search  : 	'',
		order_by: 	'id',
		ascdesc : 	'desc',
		total_results : <?PHP echo $total_followers; ?>
	}

	// Keeps track of the update access button
	var access = <?PHP echo "'".$stream['access']."'" ?>; 
	var sub = <?PHP echo "'".$stream['sub_access']."'" ?>;
	var stream_title_lock = false;
	var add_sub_lock = false;
	var sub_fee_lock = false;
	var time = null;

	$(function () {
	$('.hidden').hide(); // Hides all elements marked with "hidden" on pageload
	$('.tooltip_me').each(function() { $(this).tooltip(); });
	stream_access_style(); // Style the stream access button
	stream_sub_style() // Style the stream subscription access button
	load_users();
	});

	// Loads followning users 
	function load_users()
	{
		user_list.search = $('#user_search').val(); // Update the search parameter
		user_list.display = $('#user_display_num').val(); // Update the page parameter
		offset = user_list.display*user_list.page;
		$('.user_info').remove(); // Removes Previous Data
		$.post("/stream/display_user_list", { limit: user_list.display, offset: offset, order_by: user_list.order_by, ascdesc: user_list.ascdesc, search: user_list.search}).done(function(data) {
			console.log(data);
			$('#followers').append(data);
			$('#user_prev').show();
			$('#user_next').show();
			$.post("/stream/count_followers", {search: user_list.search}).done(function(data) {
				$('#user_results').html(data);
				user_list.total_results = data;
			});
			if(user_list.page == 0) { $('#user_prev').hide(); }
			if(user_list.page*user_list.display >= user_list.total_results-user_list.display) { $('#user_next').hide(); }
			$('#user_page').html(user_list.page);
		});
	}

	// Updates the stream's access. The access level passed is the DESIRED access level.
	function stream_access()
	{
		$.post("/stream/update_access", { access: access }).done(function(data) { 
			update_complete(data, 'access');
			stream_access_style(); 
		});	
	}

	// Updates the stream's subscription access. The subscription level passed is the DESIRED subscription level.
	function stream_subscription()
	{
		$.post("/stream/update_subscription", { sub: sub }).done(function(data) { 
			update_complete(data, 'sub_access');
			stream_sub_style(); 
		});	
	}

	// Restyles the stream access button to the access level passed to it
	function stream_access_style()
	{
		console.log(access);
		$('#stream_access').removeClass('restricted open private');
		if(access == 'restricted')
		{
			$('#stream_access').addClass('restricted');
			$('#stream_access').html('<h4>restricted</h4>');
			$("#stream_access_help").attr('data-original-title', 'Only registered users can view your stream');
			access = 'private';
		}
		else if(access == 'private')
		{
			$('#stream_access').addClass('private');
			$('#stream_access').html('<h4>private</h4>');
			$("#stream_access_help").attr('data-original-title', 'Only current followers can view your stream');
			access = 'open';
		}
		else if(access == 'open')
		{
			$('#stream_access').addClass('open');
			$('#stream_access').html('<h4>open</h4>');
			$("#stream_access_help").attr('data-original-title', 'Everyone can view your stream');
			access = 'restricted';
		}
		$('#stream_access_help').tooltip();
	}

	// Restyles the stream access button to the access level passed to it
	function stream_sub_style()
	{
		console.log(sub);
		$('#stream_subscription').removeClass('limited free total trans_bg light');
		if(sub == 'limited')
		{
			$('#stream_subscription').addClass('limited');
			$('#stream_subscription').html('<h4>limited</h4>');
			$("#stream_subscription_help").attr('data-original-title', 'Subscription allows holders to view all posts.');
			sub = 'total';
		}
		else if(sub == 'total')
		{
			$('#stream_subscription').addClass('total');
			$('#stream_subscription').html('<h4>full</h4>');
			$("#stream_subscription_help").attr('data-original-title', 'Only subscription holders can view your stream.');
			sub = 'off';
		}
		else if(sub == 'free')
		{
			$('#stream_subscription').addClass('free');
			$('#stream_subscription').html('<h4>free</h4>');
			$("#stream_subscription_help").attr('data-original-title', 'Pay-to-view posts are free to view.');
			sub = 'limited';
		}
		else if(sub == 'off')
		{
			$('#stream_subscription').addClass('trans_bg light');
			$('#stream_subscription').html('<h4>off</h4>');
			$("#stream_subscription_help").attr('data-original-title', 'Users cannot subscribe.');
			sub = 'free';
		}
		$('#stream_subscription_help').tooltip();
	}

	// Handles changing the error or success message
	function message(type, mssg)
	{
		if(type == 'error')
		{
			$('#success').hide();
			$('#error').html('<strong>Oops!</strong> '+mssg).show();
		}
		if(type == 'success')
		{
			$('#error').hide();
			$('#success').html('<strong>Success!</strong> '+mssg).stop(true, true).animate({opacity: "show"}, "slow").delay(1500).animate({opacity: "hide"}, "slow", function() {  });
		}
	}

	// Changes the tabs for the user's admin page
	function tabs(ele, type)
	{
		$('.option').removeClass('active');
		$(ele).addClass('active');
		$('.user_admin_tab').stop(true, true).hide();
		if(type == 'stream')
		{
			$('#stream_options').stop(true, true).animate({opacity: "show"}, "fast");
		}
		else if(type == 'posts')
		{
			$('#posts_options').stop(true, true).animate({opacity: "show"}, "fast");
		}
		
	}

	// Run on completion of standard update AJAX requests
	function update_complete(data, type)
	{
		if(data == 'Success!')
    	{
    		console.log(type+' Updated!');
    		message('success', success_message[type]);
    	}
    	else
    	{
    		console.log('Error: '+data);
    		message('error', data);
    	}
	}

	// Submit a new title for the user's stream
	$('#inputStreamName').keypress(function(e) {
		// Only on 'enter'
		if(e.which != 13) { return; } 
		if(stream_title_lock) { console.log('There is already a pending request. Update title request cancelled.');  return; }
		stream_title_lock = true;

	    var title = $('#inputStreamName').val();
	    $.post("/stream/update_title", { title: title }).done(function(data) { update_complete(data, 'title'); });

        stream_title_lock = false;
	});

	// Updates the subscription price
	$('#inputSubFee').keypress(function(e) {
		// Only on 'enter'
		if(e.which != 13) { return; } 
		if(sub_fee_lock) { console.log('There is already a pending request. Update subscription fee request cancelled.');  return; }
		sub_fee_lock = true;

	    var sub_fee = $('#inputSubFee').val();
	    $.post("/stream/update_fee", { sub_fee: sub_fee }).done(function(data) { update_complete(data, 'sub_fee'); });

        sub_fee_lock = false;
	});

	// Subscribes a user to your stream
	$('#inputAddSub').keypress(function(e) {
		// Only on 'enter'
		if(e.which != 13) { return; } 
		if(add_sub_lock) { console.log('There is already a pending request. Add subscriber request cancelled.');  return; }
		add_sub_lock = true;

	    var username = $('#inputAddSub').val();
	    var end_time = $('#inputNumMonths').val();
	    $.post("/stream/add_subscriber", { username: username, end_time: end_time }).done(function(data) { update_complete(data, 'add_sub'); });

        add_sub_lock = false;
	});
	
	</script>
	<!--Custom CSS for this page. Due to PHP or JS needs should NOT be added to main.css-->
	<style>
.account_profile_pic {
	background-image: url('<?PHP echo profile_pic_path($user_id); ?>');
}

.invite_requests h3 {
	cursor: pointer;
	background-color: #877761;
	text-align: center;
}

.invite_requests h4 {
	color: #555;
	text-align: center;
	cursor: pointer;
	display: inline-block;
	margin: 0px;
	float: right;
}

.results_per_page {
	width:	25px;
}

.search {
}

.list_preview_img {
	height: 30px;
	width:30px;
	float: right;
	background-size: contain!important;
	background-repeat: no-repeat!important;
}

#list_preview_1 {
	background: url('/resources/profile_pics/7.png');
}

.info_list {
	margin: 10px 0px;
	border-bottom: 1px #FFF solid;j
}

	</style>
</html>