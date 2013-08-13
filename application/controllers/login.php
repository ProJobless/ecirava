<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

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

		// Load JQuery - Enabled by default
		$this->data['jquery'] = TRUE;

		// Check to see if the user is logged in
		$temp = $this->users->is_logged_in();
		set_login_data($temp);

		// Set site and page meta data
		set_page_data('login');
	}

	public function index()
	{
		// Set site and page meta data
		set_page_data('login');

		$this->load->view('login-register/login', $this->data);
	}

	// Used with AJAX requests
	// Returns a string explaining if the action was successful or not
	public function validate()
	{
		$user_id = $this->input->post('id');
		$password = $this->input->post('password');

		// Construct password hash
		$password = $this->site->generate_password($password);

		$this->db->select('id, user_group, ban_ending_time');
		$this->db->from('users');
		$this->db->where("(`username`='$user_id' OR `email`='$user_id') AND `password`='$password'");
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			// If the account is not activated. 'C' is the sentinel value designating a 'C'onfirmed account
			if($query->row()->user_group == 'unactivated')
			{
				echo 'Your account has not been activated. Please check your email for the confirmation link';
				return;
			}
			else if($query->row()->user_group == 'banned')
			{
				if($query->row()->ban_ending_time == 1)
				{
					echo 'Your account is permanently banned.'; return;
				}
				echo 'Your account is banned. <br /> Your ban expires on: <strong>'.date("F j, Y, g:i a", $query->row()->ban_ending_time).'</strong>'; 
				return;
			}
			// Set the session variable so that the system knows the user is logged in
			// Set the time they logged in so that the system can keep track of it
			$data = array(
				'id' => $query->row()->id,
				'last_active' => time()
				);
			$this->session->set_userdata($data);

			// Update the user's last active time in the DB
			$data = array(
               'last_active' => time()
            );
			$this->db->where("(`username`='$user_id' OR `email`='$user_id') AND `password`='$password'");
			$this->db->update('users', $data); 
			echo "Success!";
		}
		else if($query->num_rows() == 0)
		{
			echo "Could Not Find User";
		}
		else
		{
			echo "Database Error";
		}
	}

	// Logouts the user
	public function leave()
	{
		$this->session->sess_destroy();
		redirect('/', 'refresh');
	}

	// Loads the password reset view
	public function password_reset()
	{
		// Set site and page meta data
		set_page_data('Password Reset');

		$this->load->view('login-register/password_reset', $this->data);
	}

	// AJAX only
	// Sends a password reset email to the user given their username/email
	public function password_reset_validate()
	{
		$this->load->library('email');

		$user_id = $this->input->post('userID');

		$this->db->select('email, username');
		$this->db->from('users');
		$this->db->where('username', $user_id);
		$this->db->or_where('email', $user_id);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			// Email Config
			$config['protocol'] = 'sendmail';
			$config['mailtype'] = 'html';

			$this->email->initialize($config);

			$row = $query->row();

			$email = $row->email;
			$username = $row->username;
			$time = time()+3600*5; // 5 hours reset time
			$code = substr(hash('sha256', $username.$time), 5, 10);

			// Generate link
			$link = '<a href="'.$_SERVER['SERVER_NAME'].'/login/password_reset_confirm/'.$code.'">Reset Password</a>';

			// Send Welcome/Confirmation Email
			$this->email->from($this->site->retrieve('admin_email'), $this->site->retrieve('admin_username'));
			$this->email->to($email);
			$this->email->subject($this->site->retrieve('site_title')." - Password Reset");
			$this->email->message(str_replace("%link%", $link, $this->site->retrieve('password_reset_email')));	
			// Alternative Email without HTML formatting
			$this->email->set_alt_message('Copy and paste this link into your browser to reset your password. If you did no request a password reset it is safe to ignore this email. '.$_SERVER['SERVER_NAME'].'/login/password_reset_confim/'.$confirmation);
			$this->email->send();

			// Insert the password code into the database, update on duplicate
			$query = "INSERT INTO `password_reset` (`id`,`password_reset_code`,`expire_time`) VALUES ('$user_id','$code','$time') ON DUPLICATE KEY UPDATE password_reset_code='$code', expire_time=$time;";
			$query = $this->db->query($query);

			echo "Success!";
		}
		else
		{
			echo "Username or Email not found.";
		}
	}

	public function password_reset_confirm($str = FALSE)
	{
		// Make sure there is an argument
		if(!$str) { redirect('/', 'redirect'); }

		// See if there is a valid password reset entry and retrieve the username
		$this->db->select('id');
		$this->db->from('password_reset');
		$this->db->where('password_reset_code', $str);
		$this->db->limit(1);
		$query = $this->db->get();

		// If no entry was found
		if($query->num_rows() != 1)
		{
			redirect('/', 'refresh');
		}
		else
		{
			$user_id = $query->row()->id;

			$new_password = substr(hash('sha256', $str.rand(1000,9999).time()), 0, 8);
			// Hash it again for the database
			$password_hash = $this->site->generate_password($new_password);

			$data = array('password' => $password_hash);

			$this->db->where('id', $user_id);
			$this->db->update('users', $data);

			$this->data['reset_username'] = $username;
			$this->data['new_password'] = $new_password;

			// Set site and page meta data
			set_page_data('Password Reset Was Successful');

			$this->load->view('reset_password_success', $this->data);
		}
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
