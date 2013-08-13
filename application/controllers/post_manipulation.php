<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_manipulation extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// Check to see if the user is logged in
		// Important to determine what actions that user can perform
		$temp = $this->users->is_logged_in();
		if($temp)
		{
			$this->data['is_logged_in'] = TRUE;
			$this->data['user_id'] = $temp;
			if($this->users->user_info($this->data['user_id'], 'user_group') != 'admin')
			{
				$this->not_admin = TRUE;
			}
		}
		else
		{
			$this->data['is_logged_in'] = FALSE;
			$this->not_admin = TRUE;
		}

		// Load additional necessary models
		$this->load->model('post');
		
	}

	public function index()
	{
		echo "No Direct Script Access";
	}

	
	// Deletes a post completely
	// Removes from database, removes files
	public function delete_post()
	{

	}

	// Moves the post to trash
	// Can be restored in the future
	public function trash_post()
	{

	}
}

/* End of file post_manipulation.php */
/* Location: ./application/controllers/post_manipulation.php */
