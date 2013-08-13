<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// Check to see if the user is logged in
		// Important to determine what stats that browser is authorized to retrieve
		$temp = $this->users->is_logged_in();
		set_login_data($temp);

		// Load posts model
		$this->load->model('posts');

		// Load post helper
		$this->load->helper('post');
	}

	public function index()
	{
		echo "No Direct Script Access";
	}

	// Changes the post's status to on of the following
	// 'Trashed', 'Active'
	//	AJAX calls only. Desired status and post ID must be sent via post
	public function update_post_status()
	{
		if(!IS_AJAX) { echo 'Error: Only asynchronous requets allowed.'; return; }

		$desired_status = $this->input->post('status');
		$post_id = $this->input->post('post_id');

		if(empty($post_id) || empty($desired_status)) { echo 'Error: Incomplete data was sent.'; return; }
		
		$author_id = post_author_id($post_id);
		if($author_id == NULL) { 'Echo: No author ID found. You are probably trying to delete a non-existant post.'; return; }

		// You must own your post or be an admin to update a post
		if($author_id != $this->data['user_id'] && !$this->data['admin'])
		{
			echo 'You do not have sufficient permissions to update this post status.';
			return;
		}

		if($desired_status == 'delete')
		{
			// Load points and users models
			$this->load->model('points');
			$this->load->model('users');

			$this->posts->delete($post_id);
		}
		else 
		{
			$this->posts->update_post_info('status', $desired_status, $post_id);
		}

		echo 'Success!';
	}

	// Favorites a post for the logged in user (toggles)
	public function favorite()
	{
		if(!IS_AJAX) { echo 'Error: Only asynchronous requets allowed.'; return; }
		$post_id = $this->input->post('post_id');

		// Load the users model
		$this->load->model('users');

		if(empty($post_id)) { echo "Error: No post ID sent."; return; }

		// Delete the favorite if already favorited
		if($this->posts->is_favorited($this->data['user_id'], $post_id))
		{
			$this->db->delete('favorites', array('user_id' => $this->data['user_id'], 'post_id' => $post_id));
			echo 'Unfavorited';
			return;
		}
		// Make sure the user isn't favoriting their own post
		$this->db->select('author_id, repost_id');
		$this->db->from('posts');
		$this->db->where('id', $post_id);
		$query = $this->db->get();

		$repost_id = $query->row()->repost_id;
		if(($query->row()->author_id == $this->data['user_id']) || ($this->users->post_to_user($repost_id) == $this->data['user_id'])) { echo 'is_my_own'; return; }

		// Favorites the post
		$data = array('user_id' => $this->data['user_id'], 'post_id' => $post_id);

		$this->db->insert('favorites', $data);

		// Increments the favorite count for the post
		// Checks if it is a repost, and credits only the original post
		if($repost_id != 0) { $post_id = $repost_id; }
		$query = "UPDATE `posts` SET num_favorites=num_favorites+1 WHERE id=".$post_id;
		$this->db->query($query);

		echo 'Favorited!';
	}

	// Reblogs a post for the logged in user
	// Does not 'toggle' or un-reblog, users must manually do this from their post admin page
	public function reblog()
	{
		if(!IS_AJAX) { echo 'Error: Only asynchronous requets allowed.'; return; }
		$post_id = $this->input->post('post_id');

		if(empty($post_id)) { echo "Error: No post ID sent"; }

		// Check if the poster is trying to repost their own post (not allowed)
		$this->db->from('posts');
		$this->db->where('id', $post_id);
		$this->db->where('author_id', $this->data['user_id']);

		if($this->db->count_all_results() > 0) { echo 'my_own_post'; return; }

		// Check to make sure the post is an original post, you cannot re-post a repost
		// The JS should be passing the orginal post as the post to be reblogged, even if the user is reposting from a re-post
		$this->db->from('posts');
		$this->db->where('repost_id', 0);
		$this->db->where('id', $post_id);

		if($this->db->count_all_results() != 1) { echo 'Error: Tried to repost a repost. Reblog not completed'; }

		// Retrieve the original post information 
		$original = $this->posts->post_info($post_id, 'all');

		// Create a new main-type post for this user
		$new_post = array(
			'author_id' => $this->data['user_id'],
			'title' => $original['title'],
			'created_on' => time(),
			'type' => $original['type'],
			'repost_id' => $original['id'],
			'text' => $original['text']			
			);
		$this->db->insert('posts', $new_post);

		echo 'Success!';

	}
}

/* End of file post.php */
/* Location: ./application/controllers/post.php */
