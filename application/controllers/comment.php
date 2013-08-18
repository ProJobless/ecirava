<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// Load stream and post models
		$this->load->model('streams');
		$this->load->model('posts');
		$this->load->model('comments');

		// Load the helpers
		$this->load->helper('utility');
		$this->load->helper('post');

		// Check to see if the user is logged in, and is admin
		// Then set that information in your "$this->data[]" array
		$temp = $this->users->is_logged_in();
		set_login_data($temp);
	}

	// Saves a comment sent via AJAX
	public function new_comment()
	{
		$content = $this->input->post('content');
		$post_id = $this->input->post('post_id');
		$reply_id = $this->input->post('reply_id');

		// Check the id to make sure it's a number
		if(!is_numeric($post_id)) { echo 'Post ID\'s must be numeric'; return; }

		// Check to make sure the post exists
		if(!$this->posts->post_exists($post_id)) { echo "Post doesn't exist anymore"; return; }

		// Check for empty comments
		if(empty($content)) { echo 'Please enter a comment'; return; }

		// Check for length
		if(strlen($content) > 500) { echo 'Comments must be 500 characters or less'; return; }

		// Check that the user is logged in
		if(!$this->data['is_logged_in']) { echo 'You must be logged in to comment'; return; }

		// Make the content 'safe' and well formatted 
		$content = content_format($content);

		// Add to the database
		$data = array(
			'post_id' => $post_id,
			'content' => $content,
			'reply_id' => $reply_id
			);

		if(!$this->comments->new_comment($post_id, $data))
		{
			echo 'Database error, please try again.'; return;
		}

		echo 'Success!';
	}

	// Sends back an JSON array so that comments can be loaded
	public function show_comments()
	{
		$post_id = $this->input->post('post_id');
		$offset = $this->input->post('offset');

		if(empty($post_id)) { echo 'No post ID sent'; return; }
		if(empty($offset)) { $offset = 0; }

		$comments = $this->comments->get_post_comments($post_id);

		echo json_encode($comments);
	}

}

/* End of file comment.php */
/* Location: ./application/controllers/comment.php */
