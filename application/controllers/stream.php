<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stream extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// Select Bootstrap Components Your Views Require
		// These can be overidden on an individual basis for each method
		$this->data = array(
			'affix'		=> FALSE,
			'alert'		=> TRUE, //
			'button'	=> FALSE,
			'carousel'	=> FALSE,
			'collapse'	=> FALSE,
			'dropdown'	=> TRUE, //
			'modal'		=> FALSE,
			'popover'	=> FALSE,
			'scrollspy'	=> FALSE,
			'tab'		=> FALSE,
			'tooltip'	=> FALSE,
			'transition'=> FALSE,
			'typeahead'	=> FALSE,
		);

		// Load JQuery - Enabled by default
		$this->data['jquery'] = TRUE;

		// Load stream and post models
		$this->load->model('streams');
		$this->load->model('posts');

		// Load the helper
		$this->load->helper('stream');

		// Check to see if the user is logged in, and is admin
		// Then set that information in your "$this->data[]" array
		$temp = $this->users->is_logged_in();
		set_login_data($temp);
	}

	public function index()
	{
		if(!$this->data['is_logged_in']) { redirect('/', 'refresh'); }

		// Turn on tooltip
		$this->data['tooltip'] = false;

		// Additional JS for image tiling
		$this->data['additional_js'] = '<script type="text/javascript" src="/resources/js/jquery.tiles-gallery.js"></script>
		<script type="text/javascript" src="/resources/js/jquery.lightbox.js"></script>';

		// Additional CSS for image tiling
		$this->data['additional_css'] = '<link rel="stylesheet" type="text/css" href="/resources/css/jquery-tilesgallery.css" />
		<link rel="stylesheet" type="text/css" href="/resources/css/themes/default/jquery.lightbox.css" />';

		// Retrieve the user's info -- Consider only retrieving relevant data after development
		$this->data['user'] = $this->users->user_info($this->data['user_id'], 'all');

		// Set site and page meta data
		set_page_data('stream');
		$this->data['stream_title'] = $this->data['user']['username']."'s Stream";

		// Retrieve the stream data
		$this->data['stream'] = $this->streams->stream_info($this->data['user_id'], 'all');

		// Retrieve number of streams you are following
		$this->data['stream']['following_num'] = $this->streams->count_following($this->data['user_id'], 'active');

		// Retrieve an array of ids you are following
		$following_ids = $this->streams->return_following($this->data['user_id'], 500, 0, 'active');
		$following_ids[] = $this->data['user_id']; // Add yourself, makes it an array so pics are not shown

		// Load the stream posts
		$this->data['stream_posts'] = $this->streams->retrieve_posts($following_ids, 10, 0);

		$this->load->view('my_stream', $this->data);
	}

	// Returns the stream via the stream's title or ID
	public function view($title)
	{
		if(!is_numeric($title)) { $title = $this->streams->get_id_from_title($title); }

		// Turn on tooltip
		$this->data['tooltip'] = true;

		// Additional JS for image tiling
		$this->data['additional_js'] = '<script type="text/javascript" src="/resources/js/jquery.tiles-gallery.js"></script>
		<script type="text/javascript" src="/resources/js/jquery.lightbox.min.js"></script>';

		// Additional CSS for image tiling
		$this->data['additional_css'] = '<link rel="stylesheet" type="text/css" href="/resources/css/jquery-tilesgallery.css" />
		<link rel="stylesheet" type="text/css" href="/resources/css/themes/default/jquery.lightbox.css" />';

		// Set site and page meta data
		set_page_data('stream');

		// Retrieve the user's info -- Consider only retrieving relevant data after development
		$this->data['user'] = $this->users->user_info($this->data['user_id'], 'all');

		// Retrieve the stream data
		$this->data['stream'] = $this->streams->stream_info($title, 'all');
		if(!$this->data['stream']) 
		{ 
			$this->data['error_message'] = 'Stream Not Found';
			$this->load->view('error', $this->data); 
			return; 
		}

		// Set the stream ID in the session data
		$this->session->set_userdata(array('stream_ID' => $this->data['stream']['id']));
		$this->session->set_userdata(array('last_page_load' => time()));

		// Retrieve number of streams that are following
		$this->data['stream']['following_num'] = $this->streams->count_followers($title);
		$this->data['stream']['num_subscribers'] = $this->streams->count_subscribers($title, 'all');
		$this->data['stream']['num_posts'] = $this->streams->count_posts($title);
		$this->data['stream']['is_following'] = $this->streams->is_following($this->data['user_id'], $title);
		$this->data['stream']['is_subscribed'] = $this->streams->is_subscribed($this->data['user_id'], $title);
		if($title == $this->data['user_id']) { $this->data['stream']['is_my_own'] = true; }
		else { $this->data['stream']['is_my_own'] = false; }

		// Check to see if the user has permission to view this stream
		// Must be logged into view restricted streams
		if(($this->data['stream']['access'] == 'restricted') && (!$this->data['is_logged_in']))
		{
			$this->data['error_message'] = "You must be a registered user to view this stream";
			$this->load->view('error', $this->data);
			return;
		}
		// Must be following to view private streams. Being subscribed overides not following. You can always view your own stream. Admins can always view streams.
		if(($this->data['stream']['access'] == 'private') && ($this->data['stream']['is_following'] != 'active') && !$this->data['stream']['is_subscribed'] && ($this->data['stream']['id'] != $this->data['user_id']) && !$this->data['admin'])
		{
			$this->load->view('private', $this->data);
			return;
		}
		// Places a notice that the stream is private
		if($this->data['stream']['access'] == 'private') { $this->data['is_private'] = true; }
		else { $this->data['is_private'] = false; }
		
		// Load the stream posts
		$this->data['stream_posts'] = $this->streams->retrieve_posts($title, 10, 0);


		$this->load->view('stream', $this->data);
	}

	// Returns a stream of all a user's favorite posts
	public function favorites()
	{
		if(!$this->data['is_logged_in']) { redirect('/', 'refresh'); }

		// Set site and page meta data
		set_page_data('favorites');
		$this->data['stream_title'] = 'My Favorites';

		// Turn on tooltip
		$this->data['tooltip'] = true;

		// Additional JS for image tiling
		$this->data['additional_js'] = '<script type="text/javascript" src="/resources/js/jquery.tiles-gallery.js"></script>
		<script type="text/javascript" src="/resources/js/jquery.lightbox.js"></script>';

		// Additional CSS for image tiling
		$this->data['additional_css'] = '<link rel="stylesheet" type="text/css" href="/resources/css/jquery-tilesgallery.css" />
		<link rel="stylesheet" type="text/css" href="/resources/css/themes/default/jquery.lightbox.css" />';

		// Retrieve the user's info -- Consider only retrieving relevant data after development
		$this->data['user'] = $this->users->user_info($this->data['user_id'], 'all');

		// Retrieve the stream data
		$this->data['stream'] = $this->streams->stream_info($this->data['user_id'], 'all');

		// Retrieve number of streams you are following
		$this->data['stream']['following_num'] = $this->streams->count_following($this->data['user_id'], 'active');

		// Retrieve an array of ids you are following
		$following_ids = $this->streams->return_following($this->data['user_id'], 500, 0, 'active');
		$following_ids[] = $this->data['user_id']; // Add yourself, makes it an array so pics are not shown

		// Load the stream posts
		$this->data['stream_posts'] = $this->streams->retrieve_favorites($this->data['user_id'], 10, 0);

		$this->load->view('my_stream', $this->data);
	}

	// Sends back formatted html from the streams the user is following
	// Or if an ID is supplied, the users following the supplied user ID
	public function load_stream_thumbs()
	{
		$limit = $this->input->post('amt');
		$offset = $this->input->post('offset');
		$stream_id = $this->input->post('stream_id');

		if(empty($limit)) { $limit = 9; }
		if(empty($offset)) { $offset = 0; }
		if(empty($stream_id)) 
		{ 
			$stream_id = $this->data['user_id'];
			$temp = $this->streams->return_following($stream_id, $limit, $offset, 'active');
		}
		else
		{
			$temp = $this->streams->return_followers($stream_id, $limit, $offset, 'active');
		}


		$data = "";
		foreach($temp as $id)
		{
			$pic = profile_pic_path($id); 			
			$title = $this->streams->stream_info($id, 'title');
			$data = $data.'<div class="stream_box"><a href="/stream/view/'.$title.'"><img src="'.$pic.'" /></a></div>';
		}

		echo $data;
	}


	// Updates the stream title of the logged in user
    // OR the user ID sent via POST (if sent)
    // Returns Sucess or an error message
	public function update_title()
	{
		$title = $this->input->post('title');
		if(empty($title)) 
		{ 
			echo "No title submitted"; 
			return; 
		}

		// Check Username length and if it is alphanumeric
		if(!preg_match('/^[a-zA-Z0-9_\s]+$/',$title))
		{
			echo "Titles must be alphanumeric";
			return;
		}

		if(strlen($title) > 50 || strlen($title) < 3)
		{
			echo "Titles must be between 3 and 50 characters";
			return;
		}

		// Check to see if the username already exists
		$this->db->from('streams');
		$this->db->where('title', $title);
		if($this->db->count_all_results() >= 1)
		{
			echo "Title already in use";
			return;
		}

		$id_from_post_data = $this->input->post('user_id');
		if(empty($id_from_post_data))
		{
			if(!$this->data['is_logged_in']) { echo "No user id information supplied"; return; }
			$this->streams->update_stream_info('title', $title, $this->data['user_id']);
		}
		else
		{
			if(!$this->data['admin']) { echo 'Cannot change others titles.';  return; }
			$this->streams->update_stream_info('title', $title, $id_from_post_data);
		}

		echo 'Success!';
	}

	// Toggles a stream through its access states
	public function update_access()
	{

		$access_next_step = array(
			'open' => 'restricted',
			'restricted' => 'private',
			'private' => 'open'
			);

		$id_from_post_data = $this->input->post('user_id');
		if(empty($id_from_post_data))
		{
			if(!$this->data['is_logged_in']) { echo "No user id information supplied"; return; }

			$access_key = $this->streams->stream_info($this->data['user_id'], 'access');
			$this->streams->update_stream_info('access', $access_next_step[$access_key], $this->data['user_id']);
		}
		else
		{
			if(!$this->data['admin']) { echo 'You do not own this stream.';  return; }

			$access_key = $this->streams->stream_info($id_from_post_data, 'access');
			$this->streams->update_stream_info('access', $access_next_step[$access_key], $id_from_post_data);
		}

		echo 'Success!';
	}

	// Increments a streams view
	public function add_view()
	{
		$last_page_load = $this->session->userdata('last_page_load');
		$stream_id = $this->session->userdata('stream_ID');

		if(!IS_AJAX) { echo 'Permission Denied.'; } // Prevents the easiest abuse

		if(($last_page_load - time() >= -20) && ($last_page_load - time() < -4) ) // Gives the post between 5 and 20 seconds to load and update. Prevents view spamming.
		{
			echo 'No view recorded.';
		}

		$query = "UPDATE `streams` SET total_views=total_views+1 WHERE id='$stream_id'";
		$this->db->query($query);
		// $m = new memcache();
		// echo 'Memmade'; return;
		// $m = addServer('localhost', 11211);


	 //    $key = "SI_$stream_id";

	 //    if(!$m->add($key, 0)) 
	 //    {
	 //       	$new_count = $m->increment($key);
		//     $notify_mysql_interval = 25+rand(-5,5);

		//     if($new_count % $notify_mysql_interval == 0) 
		//     {
		//     	$total_views = $this->streams->stream_info($this->data['user_id'], 'total_views');
		//         $this->db->update('streams', array('total_views' => $new_count+$total_views));
		//     }
	 //    }
	    
	    echo 'View Incremented';
	}

	// Toggles a stream through its subscription states
	public function update_subscription()
	{
		$sub_status = $this->streams->stream_info($this->data['user_id'], 'sub_access');
		if($sub_status == 'on')
		{
			$this->streams->update_stream_info('sub_access', 'off', $this->data['user_id']);
		}
		else
		{
			$this->streams->update_stream_info('sub_access', 'on', $this->data['user_id']);
		}
		echo 'Success!';
	}

	// Toggles a stream through its subscription states
	public function update_adverts()
	{
		$ads_status = $this->streams->stream_info($this->data['user_id'], 'adverts');
		if($ads_status == 'on')
		{
			$this->streams->update_stream_info('adverts', 'off', $this->data['user_id']);
		}
		else
		{
			$this->streams->update_stream_info('adverts', 'on', $this->data['user_id']);
		}
		echo 'Success!';
	}

	// Update Subscription Monthly Fee
	public function update_fee()
	{
		$new_fee = $this->input->post('sub_fee');

		if(empty($new_fee)) { echo "No Subscription Fee Entered"; return; }

		$new_fee = intval($new_fee);
		if($new_fee < 5 || $new_fee > 999) { echo "Subscription Fee must be between 5 and 999"; return; }

		$this->streams->update_stream_info('sub_fee', $new_fee, $this->data['user_id']);

		echo 'Success!';
	}

	// Gives the user a subscription to the user's stream
	public function add_subscriber()
	{
		$username = $this->input->post('username');
		$end_time = $this->input->post('end_time');

		if(empty($end_time)) { $end_time = 1; }
		if(empty($username)) { echo "No Username Submitted"; return; }
		$end_time = intval($end_time);

		// Make sure user exists
		$this->db->from('users');
		$this->db->where('username', $username);
		$this->db->select('id');
		$query = $this->db->get();
		if($query->num_rows() != 1) { echo 'Could Not Find User'; return; }

		$subscriber_id = $query->row()->id;

		if($subscriber_id == $this->data['user_id']) { echo "You Cannot Subscribe to Yourself"; return; }

		if($this->streams->is_subscribed($subscriber_id, $this->data['user_id']))
		{
			echo 'User Already Subscribed Until '.date('F jS', $this->streams->is_subscribed($subscriber_id, $this->data['user_id'])); return;
		}

		$time = time();
		$end_time = $time+$end_time*60*60*24*30;

		$this->streams->subscribe($subscriber_id, $this->data['user_id'], $end_time);

		echo 'Success!';
	}

	// Returns user information
    // Defaults to returning the last 10 most recent users
    // Specifications are sent via POST
    // Returns correctly formated html to be inserted
    public function display_user_list()
    {
    	// Retrieve data from post
    	$limit = $this->input->post('limit');
    	$offset = $this->input->post('offset');
    	$order_by = $this->input->post('order_by');
    	$ascdesc = $this->input->post('ascdesc');
    	$where = $this->input->post('search');

    	// Set defualts
    	if(empty($limit)) { $limit = 10; }
    	if(empty($offset)) { $offset = 0; }
    	if(empty($order_by)) { $order_by = 'created_on'; }
    	if(empty($ascdesc)) { $ascdesc = 'desc';}
    	if(empty($where)) { $where = 'id >= 1'; }   
    	else { $where = "username LIKE '$where%'"; }

    	// Retrieve ID, username and status for each follower
    	$this->db->select('follower, username, status');
    	$this->db->from('following');
    	$this->db->join('users', 'users.id = following.follower');
    	$this->db->where($where);
    	$this->db->where('leader', $this->data['user_id']);
    	$this->db->limit($limit, $offset);
    	$this->db->order_by($order_by, $ascdesc);
    	$query = $this->db->get();

    	$user_class = array(
    		'active' => 'follower',
    		'requested' => 'request',
    		'ignored' => 'ignored',
    		);

    	// Insert '%ID%' where you want the user's ID to go
    	$user_options = array (
    		'active' => '<a href="javascript:void(0)" onclick="update_follower_status(this, \'remove\', %ID%)">Remove</a>',
    		'requested' => '<a href="javascript:void(0)" onclick="update_follower_status(this, \'active\', %ID%)">Approve</a> | <a href="javascript:void(0)" onclick="update_follower_status(this, \'ignored\', %ID%)">Ignore</a>',
    		'ignored' => '<a href="javascript:void(0)" onclick="update_follower_status(this, \'remove\', %ID%)">Remove</a>'
    		);
    	if($query->num_rows == 1) { $query = array($query->row_array()); }
    	else { $query = $query->result_array(); }
    	// Echo back the format
    	foreach($query as $user)
    	{
    		echo '<div class="row user_info row_info '.$user_class[$user['status']].'" style="margin-left:0px;">
	  					<div class="span2 title">
	  						<a href="#" onmouseover="follower_pic('.$user['follower'].')" target="_blank">'.$user['username'].'</a>
	  					</div>
	  					<div class="span2">
	  					'.$user['status'].'
	  					</div>
	  					<div class="span2 actions" style="font-size:14px;">
	  						'.str_replace(array('%ID%'), array($user["follower"]), $user_options[$user['status']]).'
	  					</div>
	  				</div>';
    	}
    }

    // Returns post information
    // Defaults to returning the last 10 most recent posts
    // Specifications are sent via POST
    // Returns correctly formated html to be inserted
    public function display_post_list()
    {
    	// Retrieve data from post
    	$limit = $this->input->post('limit');
    	$offset = $this->input->post('offset');
    	$order_by = $this->input->post('order_by');
    	$ascdesc = $this->input->post('ascdesc');
    	$where = $this->input->post('search');

    	// Set defualts
    	if(empty($limit)) { $limit = 10; }
    	if(empty($offset)) { $offset = 0; }
    	if(empty($order_by)) { $order_by = 'created_on'; }
    	if(empty($ascdesc)) { $ascdesc = 'desc';}
    	if(empty($where)) { $where = 'id >= 1'; }   
    	else { $where = "title LIKE '$where%'"; }

    	// Retrieve ID, username and status for each follower
    	$this->db->select('title, id, status, type, repost_id');
    	$this->db->from('posts');
    	$this->db->where($where);
    	$this->db->where('author_id', $this->data['user_id']);
    	$this->db->limit($limit, $offset);
    	$this->db->order_by($order_by, $ascdesc);
    	$query = $this->db->get();

    	$post_class = array(
    		'active' => 'post_active',
    		'trashed' => 'post_trashed',
    		);

    	// Insert '%ID%' where you want the post's ID to go
    	$post_options = array (
    		'active' => '<a href="javascript:void(0)" onclick="update_post_status(this, \'trashed\', %ID%)">trash</a>',
    		'trashed' => '<a href="javascript:void(0)" onclick="update_post_status(this, \'active\', %ID%)">restore</a> | <a href="javascript:void(0)" onclick="update_post_status(this, \'delete\', %ID%)">delete</a>',
    		);
    	if($query->num_rows == 1) { $query = array($query->row_array()); }
    	else { $query = $query->result_array(); }
    	// Echo back the format
    	foreach($query as $post)
    	{
    		if($post['repost_id'] != 0) { $repost_icon = '<img class="repost_icon_small tooltip_me" data-html="true" data-placement="right" onmouseover="get_repost_author_pic(this, '.$post['repost_id'].')" src="/resources/img/repost.png" />'; }
    		else { $repost_icon = ""; }

    		echo '<div class="row post_info row_info '.$post_class[$post['status']].'" style="margin-left:0px;">
	  					<div class="span2 title">
	  						<a href="#" target="_blank">'.$post['title'].'</a>
	  					</div>
	  					<div class="span1">
	  					'.$post['status'].'
	  					</div>
	  					<div class="span1">
	  					'.$post['type'].'
	  					</div>
	  					<div class="span2 actions" style="font-size:14px;">
	  						'.str_replace(array('%ID%'), array($post['id']), $post_options[$post['status']]).'
	  					</div>
	  					'.$repost_icon.'
	  				</div>';
    	}
    }

    // Returns a count of followers based on search parameters for the logged in user
    public function count_followers()
    {
    	// Retrieve data from post
    	$where = $this->input->post('search');
    	// Set the default
    	if(empty($where)) { echo $this->streams->count_followers($this->data['user_id'], 'all'); }
    	else
    	{
    		$where = "username LIKE '$where%'";

    		$this->db->from('following');
    		$this->db->join('users', 'users.id = following.follower');
    		$this->db->where($where);
    		$this->db->where('leader', $this->data['user_id']);

    		echo $this->db->count_all_results();
    	}
    }

    // Returns a count of posts based on search parameters for the logged in user
    public function count_posts()
    {
    	// Retrieve data from post
    	$where = $this->input->post('search');
    	// Set the default
    	if(empty($where)) { echo $this->streams->count_posts($this->data['user_id'], 'all'); }
    	else
    	{
    		$where = "title LIKE '$where%'";

    		$this->db->from('posts');
    		$this->db->where($where);
    		$this->db->where('author_id', $this->data['user_id']);

    		echo $this->db->count_all_results();
    	}
    }

    // Updates a follower's status
    // Cannot make a user follow that isn't requesting or ignored
    public function update_follower_status()
    {
    	$follower = $this->input->post('follower');
    	$status = $this->input->post('status');

    	if(empty($status)) { echo 'No status received'; return; }

    	$data = array('follower' => $follower, 'leader' => $this->data['user_id']);

    	if($status == 'remove')
    	{
    		$this->db->delete('following', array('follower' => $follower, 'leader' => $this->data['user_id']));
    		echo 'Follower removed';
    	}
    	else if($status == 'active')
    	{
    		// Make sure they have requested to follow before adding them to prevent abuse
    		if($this->streams->is_following($follower, $this->data['user_id']))
    		{
    			$update = array('status' => 'active');
    			$this->db->where($data);
    			$this->db->update('following', $update);
    		}
    		else { echo 'User not added. User did not request to follow'; }
    	}
    	else if($status == 'ignored')
    	{
    		// Make sure they have requested to follow before adding them to prevent abuse
    		if($this->streams->is_following($follower, $this->data['user_id']))
    		{
    			$update = array('status' => 'ignored');
    			$this->db->where($data);
    			$this->db->update('following', $update);
    		}
    		else { echo 'User not ignored. User did not request to follow'; }

    	}
    	else { echo 'Valid status not received'; }
    }

    // Toggles whether a user is following a stream or not
    public function follow()
    {
    	$leader = $this->input->post('leader');

    	if($this->streams->is_following($this->data['user_id'], $leader))
    	{
    		$this->streams->stop_following($this->data['user_id'], $leader);
    	}
    	else
    	{
    		$this->streams->make_follow($this->data['user_id'], $leader);
    	}
    	echo 'Success!';
    }

}

/* End of file stream.php */
/* Location: ./application/controllers/stream.php */
