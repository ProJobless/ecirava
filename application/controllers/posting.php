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
			$this->data['error_message'] = 'You must be loged in to post.';
			$this->load->view('error', $this->data);
			//$this->load->view('posting/login_required', $this->data); --- DELETE THIS PAGE IT IS DEPRECATED ---
			return;
		}

		// Load the posts model
		$this->load->model('posts');

		// Load the posts helper
		$this->load->helper('post');
	}

	// Loads the posting select page
	public function index()
	{
		// Set site and page meta data
		set_page_data('posting_index');

		$this->load->view('posting/post_select', $this->data);
	}

	// Loads the self/text only page
	public function text()
	{
		// Load the utility helper
		$this->load->helper('utility');

		set_page_data('post_link');

		$this->data['base_points'] = $this->posts->post_type_info('text', 'base_points');

		// Load the TagIt Library
		$this->data['additional_js'] = 
		'<script type="text/javascript" src="/resources/js/jquery/UI-autocomplete.min.js"></script>
		<script type="text/javascript" src="/resources/js/tag-it.min.js"></script>
		<script type="text/javascript" src="/resources/js/autogrow.js"></script>';

		// Additional CSS for the TagIt plugin
		$this->data['additional_css'] =
		'<link rel="stylesheet" type="text/css" href="/resources/css/jq-ui/tags.css">
		<link href="/resources/css/jquery.tagit.css" rel="stylesheet" type="text/css">';

		// Turn on tooltips
		$this->data['tooltip'] = TRUE;

		// Get and array of the last X tags for autocomplete
		$this->data['available_tags'] = php_to_js_array($this->posts->get_tags(250));

		$this->load->view('posting/post_text', $this->data);
	}

	// Loads the self/text only page
	public function images()
	{
		// Load the utility helper
		$this->load->helper('utility');

		set_page_data('post_images');

		$this->data['base_points'] = $this->posts->post_type_info('images', 'base_points');
		$this->data['point_multiplier'] = $this->posts->post_type_info('images', 'point_multi');

		// Load the TagIt Library
		$this->data['additional_js'] = 
		'<script type="text/javascript" src="/resources/js/jquery/UI-autocomplete.min.js"></script>
		<script type="text/javascript" src="/resources/js/tag-it.min.js"></script>
		<script type="text/javascript" src="/resources/js/autogrow.js"></script>
		<script src="/resources/js/jquery.uploadifive.min.js" type="text/javascript"></script>
		<script src="/resources/js/sortable.js" type="text/javascript"></script>';

		$this->data['additional_css'] =
		'<link rel="stylesheet" type="text/css" href="/resources/css/jq-ui/tags.css">
		<link href="/resources/css/jquery.tagit.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="/resources/css/uploadifive.css">';

		// Turn on tooltips
		$this->data['tooltip'] = TRUE;

		// Get and array of the last X tags for autocomplete
		$this->data['available_tags'] = php_to_js_array($this->posts->get_tags(250));

		$this->load->view('posting/post_images', $this->data);
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

		// Additional CSS for the TagIt plugin
		$this->data['additional_css'] =
		'<link rel="stylesheet" type="text/css" href="/resources/css/jq-ui/tags.css">
		<link href="/resources/css/jquery.tagit.css" rel="stylesheet" type="text/css">';

		// Turn on tooltips
		$this->data['tooltip'] = TRUE;

		// Get and array of the last X tags for autocomplete
		$this->data['available_tags'] = php_to_js_array($this->posts->get_tags(250));

		$this->load->view('posting/post_link', $this->data);
	}

	// Validates and submits the a text type post to the database
	// All Data is sent by post
	public function submit_text()
	{
		// Load the utility helper
		$this->load->helper('utility');

		// Load the points model
		$this->load->model('points');

		// Retrieve from input
		$title = $this->input->post('title');
		$tags = $this->input->post('tags');
		$content = $this->input->post('content');

		// Check the title
		if(title_check($title) === TRUE)
		{
			$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		}
		else
		{
			echo title_check($title);
			return;
		}

		// Format the content
		$content = content_format($content);
		if(empty($content))
		{
			echo 'Text Posts require at least one charcter of text';
			return;
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

		// Insert tags into 'tags' and 'post_to_tags'
		$this->posts->insert_tags($tags, $post_id);

		// Add points and score to the user's account
		$post_points = $this->posts->post_type_info('link', 'base_points');
		$this->points->add_points($post_points, TRUE);

		echo 'Success!';
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

		$content = content_format($content);

		$time = time();

		// Add to posts table
		$data = array(
			'author_id' => $this->data['user_id'],
			'title' => $title,
			'created_on' => $time,
			'type' => 'text',
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

	// Uploads an image into the temp directory for image related post
	// No post data is created, this is purely a uploading function
	// Creates a thumbnail so the heights are normalized
	// Returns an error or the image name
	public function upload_image()
	{
		// Load the utility helper
		$this->load->helper('utility');
	
		// Requires the Wideimage library !!!! MAKE THIS AN ACTUAL LIBRARY AT ONE POINT !!!!
	    require_once($_SERVER['DOCUMENT_ROOT'].'/resources/php/wideimage/WideImage.php');

	    // Set the upload directory
		$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/resources/temp_uploads/';

		// Determine the filetype as you need to preserve it
		$ext = get_ext($_FILES["Filedata"]["name"]);

		// Generate a temp image name that is unlikely to collide
		$picname = hash('md5', time().'mootles'.rand(1000,100000));
		
		// Load the image from uploads
		$img = WideImage::loadFromUpload('Filedata');

		$targetFile = $uploadDir.$picname.'.'.$ext;

		// Create and save a thumbnail
		$img = $img->resize(250,250,'outside');
		$img = $img->crop('center', 'center', 250, 250);
		
		$targetFile = $uploadDir.'thumb_'.$picname.'.'.$ext;

		$img->saveToFile($targetFile);

		// Copy the full image for later (saves .gifs)
		$temp_name = $_FILES["Filedata"]["tmp_name"];
		move_uploaded_file($temp_name, $uploadDir.$picname.'.'.$ext);

		echo $picname.'.'.$ext;
	}

	// Creates an image post
	public function submit_images()
	{
		// Load the utility helper
		$this->load->helper('utility');

		// Load the points model
		$this->load->model('points');

		// Requires the Wideimage library !!!! MAKE THIS AN ACTUAL LIBRARY AT ONE POINT !!!!
	    require_once($_SERVER['DOCUMENT_ROOT'].'/resources/php/wideimage/WideImage.php');

	    // Set the upload directory
		$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/resources/uploads/';
		$tempDir = 	$_SERVER['DOCUMENT_ROOT'].'/resources/temp_uploads/';

		// Create full path (and make it if it doesn't exist)
		$time = time();

		$year = date('Y', $time);
		$month = date('m', $time);
		$uploadDir = $uploadDir.$year.'/'.$month.'/';

		if(!is_dir($uploadDir))
		{
			mkdir($uploadDir, 0755, true);
		}

		// Retrieve from input
		$title = $this->input->post('title');
		$images = $this->input->post('images');
		$tags = $this->input->post('tags');
		$content = $this->input->post('content');

		if(!empty($title))
		{
			if(strlen($title) > 100) { echo 'Titles must be less than 100 characters!'; return; } // Actually less than 101 ;D
			$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		}
		else 
		{ 
			echo "Your post must have a title"; return; 
		}

		$content = content_format($content);

		// Add to posts table
		$data = array(
			'author_id' => $this->data['user_id'],
			'title' => $title,
			'created_on' => $time,
			'type' => 'images',
			'text' => $content
			);
		$this->db->insert('posts', $data);

		$post_id = $this->db->insert_id();

		$insert_batch = array();
		$num_imgs = 0;


		if(empty($images))
		{
			echo 'Your post must contain at least one image'; return;
		}
		else
		{
			foreach($images as $image)
			{
				// Load the image from uploads
				$img = WideImage::load($tempDir.$image);

				// Make a full sized image
				$img->resize(960, null, 'outside', 'down')->saveToFile($uploadDir.'fs_'.$image);

				// Make a small sized image
				$img->resize(500, null, 'outside', 'down')->saveToFile($uploadDir.'sm_'.$image);

				// Make a thumbnail
				$img->resize(250,250,'outside')->crop('center', 'center', 250, 250)->saveToFile($uploadDir.'tb_'.$image);

				// Copy the full size image
				copy($tempDir.$image, $uploadDir.$image);


				// Update variables
				$insert_batch[] = array('post_id' => $post_id, 'img' => $image);
				$num_imgs++;
			}

		}

		// Add to the post_link table
		$data = array(
			'post_id' => $post_id,
			'num_imgs' => $num_imgs
			);
		$this->db->insert('post_images', $data);

		// Add the image data to the database
		$this->db->insert_batch('images', $insert_batch);

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
