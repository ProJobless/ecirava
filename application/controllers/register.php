<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		//Load any additional libraries here
		$this->load->library('email');

		$temp; // Throwaway variable used to reduce DB calls

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

		// Load JQuery - Enabled by default
		$this->data['jquery']	= TRUE;

		// Check to see if the user is logged in
		$temp = $this->users->is_logged_in();
		set_login_data($temp);
		
	}

	public function index()
	{
		// Sets site and page data
		set_page_data('register');

		$this->load->view('login-register/register', $this->data);
	}

	// Used with AJAX requests
	// Returns a string explaining if the action was successful or not
	public function validate()
	{
		// Retrieve from input
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$password2 = $this->input->post('password2');
		$email = $this->input->post('email');

		// Check each part
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

		//Check that passwords match and are at least 4 characters
		if(strlen($password) < 4)
		{
			echo "Passwords must be at least 4 characters";
			return;
		}

		if($password != $password2)
		{
			echo "Passwords do not match";
			return;
		}

		// If the data is correct, insert it
		// Hash password with the site salt. Shorten to 50 characters.
		$password = $this->site->generate_password($password);

		// Create a unique confirmation string
		$confirmation = substr(hash('sha256', $username), 5,10);

		$data = array(
			'username' => $username,
			'password' => $password,
			'email' => $email,
			'created_on' => time(),
			'last_active' => time(),
			'user_group' => 'unactivated',
			'confirmation_code' => $confirmation
			);
		// Starting throw 500 error for inscrutable reasons. Makes no statement to error log. 
		$this->db->insert('users', $data);

		$link = '<a href="http://'.$_SERVER['SERVER_NAME'].'/register/confirm/'.$confirmation.'">'.$_SERVER['SERVER_NAME'].'/register/confirm/'.$confirmation.'</a>';

		// Email Config
		$config['protocol'] = 'sendmail';
		$config['mailtype'] = 'html';

		$this->email->initialize($config);

		// Send Welcome/Confirmation Email
		$this->email->from($this->site->retrieve('admin_email'), $this->site->retrieve('admin_username'));
		$this->email->to($email);
		$this->email->subject($this->site->retrieve('site_title')." - Confirmation Email");
		$this->email->message(str_replace("%link%", $link, $this->site->retrieve('welcome_email')));	
		// Alternative Email without HTML formatting
		$this->email->set_alt_message('Copy and paste this link into your browser to confirm your registration. '.$_SERVER['SERVER_NAME'].'/register/confirm/'.$confirmation);
		$this->email->send();

		echo "Success!";
	}

	public function confirm($str)
	{
		// Load the streams model
		$this->load->model('streams');

		$this->db->from('users');
		$this->db->limit(1);
		$this->db->where('confirmation_code', $str);
		$query = $this->db->get();

		// If the confirmation code was found, update it to 'C' to signal it has been confirmed
		if($query->num_rows() == 1)
		{
			
			// User_helper function. Registers the user.
			register_user($query->row()->id);

			// Sets site and page data
			set_page_data('Registration Successful');

			// Load success view
			$this->load->view('login-register/register_success', $this->data);
		}
		else
		{
			// Sets site and page data
			set_page_data('Registration Failed');

			$this->load->view('login-register/confirmation_failed', $this->data);
		}
	}

	// Loads the form to request the confirmation email be resent
	public function resend_email()
	{
		// Sets site and page data
		set_page_data('Resend Email');

		$this->load->view('login-register/resend_email', $this->data);
	}

	// Validates that the email needs to be reset and sends a new email. Called via AJAX
	public function resend_email_validate()
	{
		$this->load->library('email');

		$user_id = $this->input->post('userID');

		$this->db->select('email, confirmation_code');
		$this->db->from('users');
		$this->db->where('username', $user_id);
		$this->db->or_where('email', $user_id);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$row = $query->row();
			// If the user has already confirmed their membership
			if($row->confirmation_code == 'C')
			{
				echo "You are already confirmed.";
				return;
			}

			// Email Config
			$config['protocol'] = 'sendmail';
			$config['mailtype'] = 'html';

			$this->email->initialize($config);
			
			// Create a unique confirmation string
			$confirmation = substr(hash('sha256', $username), 5,10);

			$link = '<a href="'.$_SERVER['SERVER_NAME'].'/register/confirm/'.$confirmation.'">'.$_SERVER['SERVER_NAME'].'/register/confirm/'.$confirmation.'</a>';

			// Email Config
			$config['protocol'] = 'sendmail';
			$config['mailtype'] = 'html';

			$this->email->initialize($config);

			// Send Welcome/Confirmation Email
			$this->email->from($this->site->retrieve('admin_email'), $this->site->retrieve('admin_username'));
			$this->email->to($row->email);
			$this->email->subject($this->site->retrieve('site_title')." - Confirmation Email");
			$this->email->message(str_replace("%link%", $link, $this->site->retrieve('welcome_email')));	
			// Alternative Email without HTML formatting
			$this->email->set_alt_message('Copy and paste this link into your browser to confirm your registration. '.$_SERVER['SERVER_NAME'].'/register/confirm/'.$confirmation);
			$this->email->send();

			echo "Success!";
		}
		else
		{
			echo "Username or Email not found.";
		}
	}
}

/* End of file register.php */
/* Location: ./application/controllers/register.php */
