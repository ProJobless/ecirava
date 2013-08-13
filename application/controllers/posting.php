<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posting extends CI_Controller {

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
		$this->data['jquery']	= TRUE;

		// Check to see if the user is logged in, and is admin
		// Then set that information in your "$this->data[]" array
		set_login_data($this->users->is_logged_in());

		// If the user is not logged in 
		if(!$this->data['is_logged_in'])
		{
			set_page_data('post_login_req');
			$this->load->view('posting/login_required', $this->data);
			return;
		}

		// Load the posts model
		$this->load->model('posts');
	}

	// Loads the posting select page
	public function index()
	{
		// Set site and page meta data
		set_page_data('Select Post Type');

		$this->load->view('posting/post_select', $this->data);
	}

	// Loads the link posting page
	public function link()
	{
		// Load the utility helper
		$this->load->helper('utility');

		set_page_data('post_link');

		$this->data['base_points'] = $this->posts->post_type_info('link', 'base_points');

		// Load the TagIt Library
		$this->data['additional_js'] = 
		'<script type="text/javascript" src="/resources/js/jquery/UI-autocomplete.min.js"></script>
		<script type="text/javascript" src="/resources/js/tag-it.min.js"></script>
		<script type="text/javascript" src="/resources/js/autogrow.js"></script>';

		$this->data['additional_css'] =
		'<link rel="stylesheet" type="text/css" href="/resources/css/jq-ui/tags.css">
		<link href="/resources/css/jquery.tagit.css" rel="stylesheet" type="text/css">';

		// Turn on tooltips
		$this->data['tooltip'] = TRUE;

		// Get and array of the last X tags for autocomplete
		$this->data['available_tags'] = php_to_js_array($this->posts->get_tags(250));

		$this->load->view('posting/post_link', $this->data);
	}

	// Validates and submits the a link type post to the database
	// All Data is sent by post
	public function submit_link()
	{
		// Load the utility helper
		$this->load->helper('utility');

		// Load the points model
		$this->load->model('points');

		// Retrieve from input
		$title = $this->input->post('title');
		$link = $this->input->post('link');
		$tags = $this->input->post('tags');
		$content = $this->input->post('content');

		// No empty links
		if(empty($link)) { echo 'No Link Supplied!'; return; }

		// Make sure the link is fully addressed 
		if((substr($link, 0, 7) != "http://") && (substr($link, 0, 8) != "https://"))
		{
			$link = 'http://'.$link;
		}

		if(!empty($title))
		{
			if(strlen($title) > 100) { echo 'Titles must be less than 100 characters!'; return; } // Actually less than 101 ;D
			$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		}
		else { $title = $link; }

		if(strlen($link) > 1000) { echo 'Maximum link size is 1,000 characters'; return; }

		if(!is_url_valid($link)) { echo 'Your link appears invalid'; return; }

		if(!is_url_live($link))  { echo 'Your link appears to be dead'; return; }

		if(isset($content) || $content == 0)
		{
			$content = htmlspecialchars($content);
			$content = capcode($content, 'encode');
			$content = nl2br($content);
		}
		else
		{
			$title = "";
		}

		$time = time();

		// Add to posts table
		$data = array(
			'author_id' => $this->data['user_id'],
			'title' => $title,
			'created_on' => $time,
			'type' => 'link',
			'text' => $content
			);
		$this->db->insert('posts', $data);

		$post_id = $this->db->insert_id();

		// Add to the post_link table
		$data = array(
			'post_id' => $post_id,
			'title' => $title,
			'src' => $link
			);
		$this->db->insert('post_link', $data);

		// Insert tags into 'tags' and 'post_to_tags'
		$this->posts->insert_tags($tags, $post_id);

		// Add points and score to the user's account
		$post_points = $this->posts->post_type_info('link', 'base_points');
		$this->points->add_points($post_points, TRUE);

		echo 'Success!';
	}
}

/* End of file posting.php */
/* Location: ./application/controllers/posting.php */
