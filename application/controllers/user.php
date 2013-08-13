<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// The User class contains all methods for users to view and make changes to their accounts
// This includes changing information displayed on their profile, as it is inherently user data
// The Profile class contains all methods for usesrs to view their, and other's profiles and interact with them

class User extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// Select Bootstrap Components Your Views Require
		// These can be overidden on an individual basis for each method

		$this->data = array(
			'affix'		=> FALSE,
			'alert'		=> FALSE,
			'button'	=> FALSE,
			'carousel'	=> FALSE,
			'collapse'	=> FALSE,
			'dropdown'	=> TRUE,
			'modal'		=> FALSE,
			'popover'	=> FALSE,
			'scrollspy'	=> FALSE,
			'tab'		=> FALSE,
			'tooltip'	=> FALSE,
			'transition'=> FALSE,
			'typeahead'	=> FALSE,
		);

		// Check to see if the user is logged in
		$temp = $this->users->is_logged_in();
		set_login_data($temp);

		// Load JQuery - Enabled by default
		$this->data['jquery'] = TRUE;
		
	}

	// Displays the user's edit account information page
	public function index()
	{
		// Redirect user's that are not logged in to the login page
		if(!$this->data['is_logged_in']) { redirect('/login', 'refresh'); return; }

		// Load Additional JS Libraries
		$this->data['additional_js'] = '<script src="/resources/js/jquery.uploadifive.min.js" type="text/javascript"></script>';

		// Load Additoinal CSS
		$this->data['additional_css'] = '<link rel="stylesheet" type="text/css" href="/resources/css/uploadifive.css">';

		// Retrieve the user's account information. Print and exit on error.
		$user = $this->users->user_info($this->data['user_id'], 'all');
		if(!$user) 
		{ 
			set_page_data('error');
			$this->data['error_message'] = "Could Not Find User";
			$this->load->view('error', $this->data); 
			return;
		}

		$this->data['user'] = $user;

		// Sets site and page data
		set_page_data('account');

		// Retrieve motd
		$temp = $this->site->retrieve(array(
			'motd'
			));
		$this->data['motd'] = $temp;

		$this->load->view('account', $this->data);

	}

	// Displays the user's account admin page
	public function admin()
	{
		// Turn on tooltips and collapse
		$this->data['tooltip'] = TRUE;
		$this->data['collapse'] = TRUE;

		// Redirect user's that are not logged in to the login page
		if(!$this->data['is_logged_in']) { redirect('/login', 'refresh'); return; }

		// Load Stream Model
		$this->load->model('streams');

		// Retrieve the user's account information. Print and exit on error.
		$this->data['user'] = $this->users->user_info($this->data['user_id'], 'all');
		if(!$this->data['user']) 
		{ 
			set_page_data('error');
			$this->data['error_message'] = "Could Not Find User";
			$this->load->view('error', $this->data); 
			return;
		}

		// Retrieve the user's stream info
		$this->data['stream'] = $this->streams->stream_info($this->data['user_id'], 'all');

		$this->data['total_subs'] = number_format($this->streams->count_subscribers($this->data['user_id'], 'all'));
		$this->data['total_followers'] = $this->streams->count_followers($this->data['user_id'], 'all');
		$this->data['paid_subs'] = number_format($this->streams->count_subscribers($this->data['user_id'], 'paid'));
		$this->data['total_posts'] = number_format($this->streams->count_posts($this->data['user_id'], 'all'));
	
		// Sets site and page data
		set_page_data('account');

		$this->load->view('user_admin', $this->data);
	}

	// Updates the username of the logged in user
    // OR the user ID sent via POST (if sent)
    // Returns Sucess or an error message
	public function update_username()
	{
		$username = $this->input->post('username');
		if(empty($username)) 
		{ 
			echo "No username submitted"; 
			return; 
		}

		// Check Username length and if it is alphanumeric
		if(!preg_match('/^[a-zA-Z0-9_]+$/',$username))
		{
			echo "Usernames must be alphanumeric";
			return;
		}

		if(strlen($username) > 64 || strlen($username) < 3)
		{
			echo "Usernames must be between 3 and 64 characters";
			return;
		}

		// Check to see if the username already exists
		$this->db->from('users');
		$this->db->where('username', $username);
		if($this->db->count_all_results() >= 1)
		{
			echo "Username already in use";
			return;
		}

		$id_from_post_data = $this->input->post('user_id');
		if(empty($id_from_post_data))
		{
			if(!$this->data['is_logged_in']) { echo "No user id information supplied"; return; }
			$this->users->update_user_info('username', $username, $this->data['user_id']);
		}
		else
		{
			if(!$this->data['admin']) { echo 'Cannot change others\' usernames.';  return; }
			$this->user->update_user_info('username', $username, $id_from_post_data);
		}

		echo 'Success!';
	}

	// Updates a logged in user's email
	// OR the user id sent via POST (if sent)
	// Returns Success or an error message
	public function update_email()
	{
		// Load email library
		$this->load->library('email');

		$email = $this->input->post('email');
		if(empty($email)) 
		{ 
			echo "No email submitted"; 
			return; 
		}

		// Check for valid email address
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
    		echo "This email doesn't look correct";
    		return;
		}

		// Check to see if the email already exists
		$this->db->from('users');
		$this->db->where('email', $email);
		if($this->db->count_all_results() >= 1)
		{
			echo "Email already in use";
			return;
		}

		//Send confirmation email
		$id_from_post_data = $this->input->post('user_id');
		if(empty($id_from_post_data))
		{
			if(!$this->data['is_logged_in']) { echo "No user id information supplied"; return; }
			$this->users->send_email_update_confirmation($email, $this->data['user_id']);
		}
		else
		{
			$this->users->send_email_update_confirmation($email, $id_from_post_data);
		}

		echo 'Success!';

	}

	// Updates a user's email to their desried email 
    // based on a confirmation code
    public function confirm_email($str)
    {
        // Make sure there is an argument
        if(!$str) { redirect('/', 'redirect'); }

        // Sets site and page data
		set_page_data('Email Confirmation');

        // See if there is a valid password reset entry and retrieve the username
        $this->db->from('update_email');
        $this->db->where('email_change_code', $str);
        $this->db->limit(1);
        $query = $this->db->get();
		
        $user_id = $query->row()->id;
        $desired_email = $query->row()->desired_email;

        // If no entry was found
        // UPDATE THIS TO A "NOT FOUND" EXPLANATORY PAGE //
        if($query->num_rows() != 1)
        {
			$this->data['update_email_message'] = "Your email update request could not be found. <br /> <br /> Please try again.";
			$this->load->view('update_email', $this->data);
        }
        else
        {
            // Check to make sure the desired email is still not in use
            $this->db->from('users');
            $this->db->where('email', $desired_email);
            if($this->db->count_all_results() >= 1)
            {
				$this->data['update_email_message'] = "Your email update request has timed out. <br /> <br /> Email update requests are good for 24 hours since the time they were made.";
				$this->load->view('update_email', $this->data);
                return;
            }

            // Make sure the request to change is still valid
            if($query->row()->expires_on < time())
            {
				$this->data['update_email_message'] = "Your email update request has timed out. <br /> <br /> Email update requests are valid for 24 hours.";
				$this->load->view('update_email', $this->data);
                return;
            }

            $this->users->update_user_info('email', $desired_email, $user_id);
            $this->data['update_email_message'] = "Your email has been successfully updated!";
            $this->data['new_email'] = $desired_email;
			$this->load->view('update_email', $this->data);
        }
    }

    // Updates a user's password. Requires old password to change.
    // Changes the password of the current user only
    // Returns Success! or an error message
    public function update_password()
    {
    	$old_password = $this->input->post('old_password');
    	$password = $this->input->post('password');
    	$password_conf = $this->input->post('password_conf');

		if(empty($old_password) || empty($password) || empty($password_conf)) 
		{ 
			echo "Please fill in all fields"; 
			return; 
		}

		//Check that passwords match and are at least 4 characters
		if(strlen($password) < 4)
		{
			echo "Passwords must be at least 4 characters";
			return;
		}

		if($password != $password_conf)
		{
			echo "Passwords do not match";
			return;
		}

		
		if(empty($this->data['user_id']) || !$this->data['is_logged_in'])
		{
			echo "Could not determine User ID";
			return;
		}
		else
		{
			$this->db->select('password');
			$this->db->from('users');
			$this->db->where('id', $this->data['user_id']);

			$query = $this->db->get();
			// Check to see if the old password matches
			if($query->row()->password == $this->site->generate_password($old_password))
			{
				$this->users->update_password($password, $this->data['user_id']);
			}
			else
			{
				echo "Current password is incorrect. Try Again.";
				return;
			}

		}

		echo "Success!";	
    }

    // Updates the user's profile picture
    // If not user_id is sent via POST, then it will update the logged in user's
    // Center-zooms and crops to 250x250 if larger. Crops to a square if smaller.
    // Echo's the target user's id to refresh upon completion/verify
    public function update_profile_pic()
    {

	    // Get the user's ID from post or from the login session
	    // Changing the profile picture via POST requires admin privilege
	    $user_id = $this->input->post('$user_id');
	    if(empty($user_id) || !$this->data['admin'])
	    {
	    	$user_id = $this->data['user_id'];
	    }

	    // Requires the Wideimage library
	    require_once($_SERVER['DOCUMENT_ROOT'].'/resources/php/wideimage/WideImage.php');

	    // Set the upload directory
		$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/resources/profile_pics/';

		// Set the picture name, this cannot be modified
		$picname = $user_id.".png";
		
		// Load the image from uploads
		$img = WideImage::loadFromUpload('Filedata');
		$img = $img->resize(250,250,'outside');
		$img = $img->crop('center', 'center', 250, 250);
		
		$targetFile = $uploadDir.$picname;

		$img->saveToFile($targetFile);

		echo $user_id;

    }

    // Changes the user's status to on of the following
	// 'Banned', 'Inactive', 'User'
	//	AJAX calls only. Desired status and user must be sent via post
	public function update_user_status()
	{
		if(!IS_AJAX) { echo 'Error: Only asynchronous requets allowed.'; return; }
		if(!$this->data['admin']) { echo 'Error: Only admins may update user statuses.'; return; }

		$desired_status = $this->input->post('desired_status');
		$user_id = $this->input->post('user_id');

		if(empty($user_id) || empty($desired_status)) { echo 'Error: Incomplete data was sent.'; return; }

		// A unix timestamp denotes that the user is being banned
		if(is_numeric($desired_status))
		{
			$desired_status = array('user_group' => 'banned', 'ban_ending_time' => $desired_status);
		}
		// If you are activating a user manually, here you can complete their remaining registration steps
		if($this->users->user_info($user_id, 'user_group') == 'unactivated')
		{
			$this->load->model('streams');
			// User_helper function
			register_user($user_id);
		}
		$this->users->update_user_info('user_group', $desired_status, $user_id);

		echo 'Success!';
	}

	// Returns a user's ID and username as a JSON array given a post ID if AJAX,
	public function post2user()
	{
		$post_id = $this->input->post('post_id');

		if(empty($post_id)) { echo 'error'; return; }

		// Need to know the user's profile pic path
		$this->load->helper('user');

		// Active Record throws a fit if you use table prefixes without this
		$db['default']['_protect_identifiers'] = FALSE;

		$this->db->from('posts');
		$this->db->where('posts.id', $post_id);
		$this->db->join('users', 'users.id = posts.author_id');
		$this->db->select("users.id AS id, username");
		$query = $this->db->get();

		$return = array(
			profile_pic_path($query->row()->id),
			$query->row()->username
			);

		echo json_encode($return);
		return;

	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
