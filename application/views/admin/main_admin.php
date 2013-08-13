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
		<h1>Admin Area</h1>
		<div id="admin_alert_box">
		</div>
		<!--Statistics Row-->
		<div class="row">
	    	<div class="span12">
	    		<div class="row">
	    			<div id="sl_posts" class="span3 sl_stats">
	    				<h4 class="pull_center">Daily Post Count</h4>
	    				<span id="sl_posts_data" class="sparklines" values="0"></span>
	    			</div>
	    			<div id="sl_comments" class="span3 sl_stats">
	    				<h4 class="pull_center">Daily Comments Count</h4>
	    				<span id="sl_comments_data" class="sparklines" values="0"></span>
	    			</div>
	    			<div id="sl_users" class="span3 sl_stats">
	    				<h4 class="pull_center">User Sign-Up</h4>
	    				<span id="sl_users_data" class="sparklines" values="0"></span>
	    			</div>
	    			<div id="sl_chats" class="span3 sl_stats">
	    				<h4 class="pull_center">Daily Chat Log</h4>
	    				<span id="sl_chats_data" class="sparklines" values="0"></span>
	    			</div>
	    		</div>
	    	</div>
	  	</div>
	  	<!--Posts Quick Look and User Quick Look-->
	  	<div class="row unselectable">
	  		<div id="posts" class="span6 info_box">
	  			<h1>Posts</h1>
	  			<div class="row">
	  					<div class="span3 search">
	  						<input id="post_search" placeholder="Title or Author" type="text" />
	  					</div>
	  					<div class="span2 page_counter">
	  						<div class="pull-left desc">
	  							Results<br />per<br />page
	  						</div>
	  						<input id="post_display_num" value="10" placeholder="10" type="text" maxlength="3" />
	  					</div>
	  					<div class="span1 prevnext">
	  						<a id="post_prev" href="javascript:void(0)" onclick="post_page('prev')">Prev</a>
	  						<br />
	  						<a id="post_next" href="javascript:void(0)" onclick="post_page('next')">Next</a>
	  					</div>
	  					<div class="span1 user_list_stats" style="text-align:left;">
  						<div class="pull-left desc">
  							Page: 
  						</div>
  						<div id="post_page" class="pull-right desc">
  							0
  						</div>
  						<div style="clear:both"></div>
  						<div class="pull-left desc">
  							Results: 
  						</div>
  						<div id="post_results" class="pull-right desc">
  							0
  						</div>
  						<div style="clear:both"></div>
  						<div class="pull-left desc">
  							Total: 
  						</div>
  						<div class="pull-right desc">
  							<?PHP echo $total_posts; ?>
  						</div>
	  				</div>
	  			</div>
	  			<div class="info_list">
	  				<div class="row">
	  					<div class="span1 collumn" onclick="order_posts('id')">
	  						ID
	  					</div>
	  					<div class="span2 collumn" onclick="order_posts('title')">
	  						Title
	  					</div>
	  					<div class="span1 collumn" onclick="order_posts('type')">
	  						Type
	  					</div>
	  					<div class="span2 collumn" style="text-align:center;cursor:default">
	  						Action
	  					</div>
	  				</div>
	  				<hr />
	  				<span id="posts_notice" class="info_box notice">No Posts</span>
	  			</div>
	  		</div>
	  		<div id="users" class="span6 info_box">
	  			<h1>Users</h1>
	  			<div class="row">
	  					<div class="span3 search">
	  						<input id="user_search" placeholder="Username, Email or ID" type="text" />
	  					</div>
	  					<div class="span2 page_counter">
	  						<div class="pull-left desc">
	  							Results<br />per<br />page
	  						</div>
	  						<input id="user_display_num" value="10" placeholder="10" type="text" maxlength="3" />
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
  							<?PHP echo $total_users; ?>
  						</div>
	  				</div>
	  			</div>
	  			<div class="info_list">
	  				<div class="row">
	  					<div class="span1 collumn" onclick="order_users('id')">
	  						ID
	  					</div>
	  					<div class="span2 collumn" onclick="order_users('username')">
	  						Username
	  					</div>
	  					<div class="span1 collumn" onclick="order_users('user_group')">
	  						Status
	  					</div>
	  					<div class="span2 collumn" style="text-align:center;cursor:default">
	  						Action
	  					</div>
	  				</div>
	  				<hr />
	  				<span id="users_notice" class="info_box notice">No Users</span>
	  			</div>

	  		</div>
	  	</div>
	  	<hr />
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<!--Custom JS for this page. CANNOT be added to main.js after development-->
	<script>
	// For the user display count
	$('#user_display_num').keypress(function(e) { if(e.which == 13) { user_list.page = 0; load_users(); } });

	// For the post display count
	$('#post_display_num').keypress(function(e) { if(e.which == 13) { post_list.page = 0; load_posts(); } });

	// For the user search boxes
	$('#user_search').keyup(function(e) {
		user_list.search = $('#user_search').val(); // Update the search parameter
		user_list.page = 0;
		load_users();	
	});

	// For the user search boxes
	$('#post_search').keyup(function(e) {
		user_list.search = $('#user_search').val(); // Update the search parameter
		user_list.page = 0;
		load_posts();	
	});

	// Set the defualt loadout for users
	var user_list = {
		display : 	10,
		page 	: 	0,
		search  : 	'',
		order_by: 	'id',
		ascdesc : 	'desc',
		total_results : <?PHP echo $total_users; ?>
	}

	// Set the defualt loadout for posts
	var post_list = {
		display : 	10,
		page 	: 	0,
		search  : 	'',
		order_by: 	'id',
		ascdesc : 	'desc',
		total_results :	<?PHP echo $total_posts; ?>
	}

	// Pagination for user results
	function user_page(pn)
	{
		if(pn == 'prev')
		{
			user_list.page--;
			// Negative pages don't make sense
			if(user_list.page < 0) { user_list.page = 0; } 
		}
		else
		{
			user_list.page++;
		}
		load_users();
	}

	// Pagination for post results
	function post_page(pn)
	{
		if(pn == 'prev')
		{
			post_list.page--;
			// Negative pages don't make sense
			if(post_list.page < 0) { post_list.page = 0; } 
		}
		else
		{
			post_list.page++;
		}
		load_posts();
	}

	// Changes only the order that the users are ordered_by
	// And if it is asc/desc
	function order_users(order_by)
	{
		// Toggles asc/desc
		if(user_list.ascdesc == 'asc') { user_list.ascdesc = 'desc'; }
		else { user_list.ascdesc = 'asc'; }
		user_list.order_by = order_by;
		user_list.page = 0;
		load_users();
	}

	// Changes only the order that the posts are ordered_by
	// And if it is asc/desc
	function order_posts(order_by)
	{
		// Toggles asc/desc
		if(post_list.ascdesc == 'asc') { post_list.ascdesc = 'desc'; }
		else { post_list.ascdesc = 'asc'; }
		post_list.order_by = order_by;
		post_list.page = 0;
		load_posts();
	}

	// Updates a user's status appropriately
	function update_user_status(ele, status, id)
	{
		// When banning, need to enter a time
		if(status == 'banned')
		{
			// Get the current Unix Timestamp
			var unix = Math.round(+new Date()/1000);
			$('.banbox').remove();
			$(ele).parent().parent().after("<div class=\"banbox\">"+
				"<a href='javascript:void(0)' onclick='update_user_status(this,"+(unix+86400)+","+id+")'>1 Day</a> | "+
				"<a href='javascript:void(0)' onclick='update_user_status(this,"+(unix+259200)+","+id+")'>3 Days</a> | "+
				"<a href='javascript:void(0)' onclick='update_user_status(this,"+(unix+604800)+","+id+")'>A Week</a> | "+
				"<a href='javascript:void(0)' onclick='update_user_status(this,"+(unix+2592000)+","+id+")'>A Month</a> | "+
				"<a href='javascript:void(0)' onclick='update_user_status(this,"+(unix+7776000)+","+id+")'>3 Months</a> | "+
				"<a href='javascript:void(0)' onclick='update_user_status(this,"+(unix+15552000)+","+id+")'>6 Months</a> | "+
				"<a href='javascript:void(0)' onclick='update_user_status(this,"+(unix+31104000)+","+id+")'>A Year</a> "+
				"<a href='javascript:void(0)' onclick='update_user_status(this,1,"+id+")' style=\"color:red;\">Forever</a> "+
				"</div>");
			return;
		}
		else
		{
			$(ele).html('Working...');
		}
		$.post("/user/update_user_status", { desired_status: status, user_id: id}).done(function(data) {
			if(data == 'Success!')
			{
				console.log('User '+id+' status updated to: '+status+'!');
				load_users();
			}
			else
			{
				console.log(data);
			}
		});
	}

	function update_post_status(ele, status, id)
	{
		$(ele).html('Working...');
		$.post("/post/update_post_status", { status: status, post_id: id}).done(function(data) {
			if(data == 'Success!')
			{
				console.log('Post '+id+' status updated to: '+status+'!');
				load_posts();
			}
			else
			{
				console.log(data);
			}
		});
	}

	// Takes data and an object
	// Set ups and loads sparklines
	// This is mostly to prevent copy and pasting
	function load_sl(data, ele)
	{
		console.log(data);
		$(ele).attr('values', data);
		$(ele).parent().css('background-image', 'none');
		$(ele).sparkline('html', { lineColor: '51a351', width: '90%', height: '50%', fillColor: 'A9E89E', spotColor: 'false', highlightSpotColor: 'fff', minSpotColor: 'false', maxSpotColor: 'false', lineWidth: '3', highlightLineColor: '111'} );
	}

	function load_users()
	{
		user_list.search = $('#user_search').val(); // Update the search parameter
		user_list.display = $('#user_display_num').val(); // Update the page parameter
		offset = user_list.display*user_list.page;
		$('.user_info').remove(); // Removes Previous Data
		$('.banbox').remove();
		$.post("/admin/display_user_list", { limit: user_list.display, offset: offset, order_by: user_list.order_by, ascdesc: user_list.ascdesc, search: user_list.search}).done(function(data) {
			$('#users').append(data);
			$('#users_notice').hide();
			$('#user_prev').show();
			$('#user_next').show();
			$.post("/stats/user_count", {search: user_list.search}).done(function(data) {
				$('#user_results').html(data);
				user_list.total_results = data;
			});
			if(user_list.page == 0) { $('#user_prev').hide(); }
			if(user_list.page*user_list.display >= user_list.total_results-user_list.display) { $('#user_next').hide(); }
			$('#user_page').html(user_list.page);
			// To get the result count
			$('.tooltip_me').tooltip();	
		});
	}

	function load_posts()
	{
		post_list.search = $('#post_search').val(); // Update the search parameter
		post_list.display = $('#post_display_num').val(); // Update the page parameter
		offset = post_list.display*post_list.page;
		$('.post_info').remove(); // Removes Previous Data
		$.post("/admin/display_post_list", { limit: post_list.display, offset: offset, order_by: post_list.order_by, ascdesc: post_list.ascdesc, search: post_list.search}).done(function(data) {
			$('#posts').append(data);
			$('#posts_notice').hide();
			$('#post_prev').show();
			$('#post_next').show();
			$.post("/admin/post_count", {search: post_list.search}).done(function(data) {
				$('#post_results').html(data);
				post_list.total_results = data;
			});
			if(post_list.page == 0) { $('#post_prev').hide(); }
			if(post_list.page*post_list.display >= post_list.total_results-post_list.display) { $('#post_next').hide(); }
			$('#post_page').html(post_list.page);
			// To get the result count
			$('.tooltip_me').tooltip();	
		});
	}

	// Called on DOM load
	var posts_array;
	var comments_array;
	var user_array;
	var chat_array;
	var days = 7; // Number of days in the past to retireve
	$(function() {
		// Loads Sparklines Graphs
		$.post("/stats/admin_sl", {days: days, request_type: 'posts'}).done(function(data) { 
	        		console.log('Post Stats retrieved!');
	        		load_sl(data, '#sl_posts_data');
       			});
		$.post("/stats/admin_sl", {days: days, request_type: 'comments'}).done(function(data) { 
	        		console.log('Comments Stats retrieved!');
	        		load_sl(data, '#sl_comments_data');
       			});
		$.post("/stats/admin_sl", {days: days, request_type: 'users'}).done(function(data) { 
	        		console.log('User Stats retrieved!');
	        		load_sl(data, '#sl_users_data');
       			});
		$.post("/stats/admin_sl", {days: days, request_type: 'chat'}).done(function(data) { 
	        		console.log('Chat Stats retrieved!');
	        		load_sl(data, '#sl_chats_data');
       			});	
       	load_users();
       	load_posts();
    });

	</script>
	<!--Custom CSS for this page, should be added to main.css after development-->
	<style>
	
	</style>
</html>