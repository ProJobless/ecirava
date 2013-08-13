<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

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
			'tooltip'	=> TRUE,
			'transition'=> FALSE,
			'typeahead'	=> FALSE,
		);

		// Load JQuery - Enabled by default
		$this->data['jquery']	= TRUE;

		//Load additonal CSS and JS
		$this->data['additional_js'] = '<script type="text/javascript" src="/resources/js/sparkline.js"></script>';


		// Check to see if the user is logged in, and is admin
		// Then set that information in your "$this->data[]" array
		$temp = $this->users->is_logged_in();
		set_login_data($temp);

		// If the user is not logged in or is not an admin, do not load the page
		if(!$this->data['admin']) { $this->load->view('no_privilege'); return; }
	}

	public function index()
	{
		// Set site and page meta data
		set_page_data('admin');

		// Load Posts Model
		$this->load->model('posts');

		$this->data['total_users'] = $this->users->count_users('all');
		$this->data['total_posts'] = $this->posts->count_posts('all');

		$this->load->view('admin/main_admin', $this->data);
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
    	if(empty($where)) { $where = ''; }

    	// Format the 'Where' clause
    	$where = "id LIKE '$where%' || username LIKE '$where%' || email LIKE '$where%'";

    	$users = $this->users->user_data($limit, $offset, $order_by, $ascdesc, $where);

    	$user_class = array(
    		'admin' => 'admin_user',
    		'user' => 'normal_user',
    		'banned' => 'banned_user',
    		'unactivated' => 'unactivated_user'
    		);

    	// Insert '%ID%' where you want the user's ID to go
    	$user_options = array (
    		'admin' => '-',
    		'user' => '<a href="javascript:void(0)" onclick="update_user_status(this, \'banned\', %ID%)">Ban</a>',
    		'banned' => '<a href="javascript:void(0)" class="tooltip_me" data-toggle="tooltip" data-placement="right" title="%BAN_END%" onclick="update_user_status(this, \'user\', %ID%)">Restore</a>',
    		'unactivated' => '<a href="javascript:void(0)" onclick="update_user_status(this, \'user\', %ID%)">Activate</a>'
    		);

    	// Echo back the format
    	foreach($users as $user)
    	{
    		if($user['ban_ending_time'] != 1) { $ban = date("F j, Y, g:i a", $user['ban_ending_time']); }
    		else { $ban = 'FOREVER'; }
    		echo '<div class="row user_info row_info '.$user_class[$user['user_group']].'" style="margin-left:0px;">
	  					<div class="span1 id">
	  						'.$user['id'].'
	  					</div>
	  					<div class="span2 title">
	  						<a href="#" class="tooltip_me" data-toggle="tooltip" data-placement="right" title="'.$user['email'].'" target="_blank">'.$user['username'].'</a>
	  					</div>
	  					<div class="span1 status">
	  					'.$user['user_group'].'
	  					</div>
	  					<div class="span2 actions" style="text-align:center;font-size:14px;">
	  						'.str_replace(array('%ID%', '%BAN_END%', '%EMAIL%'), array($user["id"], $ban, $user['email']), $user_options[$user['user_group']]).'
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
    	// Load the posts model
   		$this->load->model('posts');

    	// Retrieve data from POST array
    	$limit = $this->input->post('limit');
    	$offset = $this->input->post('offset');
    	$order_by = 'posts.'.$this->input->post('order_by');
    	$ascdesc = $this->input->post('ascdesc');
    	$where = $this->input->post('search');

    	// Set defualts
    	if(empty($limit)) { $limit = 10; }
    	if(empty($offset)) { $offset = 0; }
    	if(empty($order_by)) { $order_by = 'posts.created_on'; }
    	if(empty($ascdesc)) { $ascdesc = 'desc';}
    	if(empty($where)) { $where = ''; }

    	// Format the 'Where' clause
    	$where = "posts.id LIKE '$where%' || username LIKE '$where%' || title LIKE '$where%'";

    	$posts = $this->posts->post_data($limit, $offset, $order_by, $ascdesc, $where);

    	$post_class = array(
    		'active' => 'active_post',
    		'trashed' => 'trashed_post'
       		);

    	// Insert '%ID%' where you want the user's ID to go
    	$post_options = array (
    		'active' => '<a href="javascript:void(0)" onclick="update_post_status(this, \'trashed\', %ID%)">Trash</a>',
    		'trashed' => '<a href="javascript:void(0)" onclick="update_post_status(this, \'active\', %ID%)">Restore</a> | <a href="javascript:void(0)" onclick="update_post_status(this, \'delete\', %ID%)">Delete</a>'
    		);

    	// Echo back the format
    	foreach($posts as $post)
    	{
    		echo '<div class="row post_info row_info '.$post_class[$post['status']].'" style="margin-left:0px;">
	  					<div class="span1 id tooltip_me" data-toggle="tooltip" data-placement="left" title="'.$post['username'].'">
	  						'.$post['id'].'
	  					</div>
	  					<div class="span2 title">
	  						<a href="#" class="tooltip_me" data-toggle="tooltip" data-placement="right" title="'.$post['title'].'">'.$post['title'].'</a>
	  					</div>
	  					<div class="span1 type">
	  					'.$post['type'].'
	  					</div>
	  					<div class="span2 actions" style="text-align:center;font-size:14px;">
	  						'.str_replace('%ID%', $post["id"], $post_options[$post['status']]).'
	  					</div>
	  				</div>';
    	}
    }

    // Returns a count of users based on certain search parameters
    public function user_count()
    {
    	// Retrieve data from post
    	$where = $this->input->post('search');

    	// Set the default
    	if(empty($where)) { $where = 'all'; }
    	else
    	{
    		$where = "id LIKE '$where%' || username LIKE '$where%' || email LIKE '$where%'";
    	}

    	echo $this->users->count_users($where);
    }

    // Returns a count of users based on certain search parameters
    public function post_count()
    {
    	// Load Posts Model
    	$this->load->model('posts');

    	// Retrieve data from post
    	$where = $this->input->post('search');

    	// Set the default
    	if(empty($where)) { $where = 'all'; }
    	else
    	{
    		$where = "posts.id LIKE '$where%' || username LIKE '$where%' || title LIKE '$where%'";
    	}

    	echo $this->posts->count_posts($where);
    }
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
