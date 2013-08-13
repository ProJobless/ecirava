<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stats extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// Check to see if the user is logged in
		$temp = $this->users->is_logged_in();
		set_login_data($temp);

		// Only remote requests allowed
		//if(!IS_AJAX) { echo "No direct script access allowed."; return; }
	}

	public function index()
	{
		echo "No direct access allowed.";
	}

	// Only Admins can view this information
	// Retrieves the number of <request_type> submitted each day for the last X days
	// X and <request_type> is submitted via a $_POST[] request, defaults to 7
	// Returns a CSV of the data (since it is used for sparkline requests)
	// Valid <request_types> are: posts, users, comments
	public function admin_sl()
	{
		if(!$this->data['admin']) { echo "Error: Not Authorized. User Level Required: Admin"; return; }
		$table = $this->input->post('request_type');
		if(empty($table)) { echo "No request type specified. Query aborted."; return; }
		$table = strtolower($table);

		$acceptable_request_types = array(
			"posts",
			"users",
			"comments"
			);

		if(!in_array($table, $acceptable_request_types)) { echo "Request type not valid. Query aborted."; return; }
		// Get the time
		$time = time();
		$day = 86400; // 24*60*60
		$current_day = ceil($time/$day); // Current day
		$time_modulo = $current_day*$day; // Current day in seconds

		// Retrieve the number of days from $_POST
		$number_of_days = $this->input->post('days');
		$return_str = "";

		for($number_of_days; $number_of_days > 0; $number_of_days--)
		{
			$day_begin = $time_modulo - ($number_of_days*$day);
			$day_end = $day_begin + $day -1;
			$this->db->where('created_on >', $day_begin);
			$this->db->where('created_on <=', $day_end);
			$this->db->from($table);
			$return_str = $return_str.$this->db->count_all_results().',';
		}
		echo substr($return_str, 0, -1); // Truncate first comma
	}

	// Returns a user's posting history for the last X days
	// Determines the user by their session user ID
	// X is submitted by $_POST[] request, defaults to 7
	// Returns a CSV of the data (since it is used for sparkline requests)
	public function user_posts_sl()
	{
		// Retrieve the user's id from sessions, exit if the error is not logged in
		$id;
		if($this->data['is_logged_in'])
		{
			$id = $this->data['user_id'];
		}
		else
		{
			echo "Error: Could not determine the user's ID."; return;
		}

		// Get the time
		$time = time();
		$day = 86400; // 24*60*60
		$current_day = ceil($time/$day); // Current day
		$time_modulo = $current_day*$day; // Current Day in seconds

		// Retrieve the number of days from $_POST
		$number_of_days = $this->input->post('days');
		$return_str = "";

		for($number_of_days; $number_of_days > 0; $number_of_days--)
		{
			$day_begin = $time_modulo - ($number_of_days*$days);
			$day_end = $day_begin + $day -1;
			$this->db->where('created_on >', $day_begin);
			$this->db->where('created_on <=', $day_end);
			$this->db->where('id', $id);
			$this->db->from('posts');
			$return_str = $return_str.$this->db->count_all_results().',';
		}
		echo substr($return_str, 0, -1); // Truncate first comma
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

    // Returns a count of posts based on certain search parameters
    public function post_count()
    {
    	// Retrieve data from post
    	$where = $this->input->post('search');

    	// Set the default
    	if(empty($where)) { $where = 'all'; }
    	else
    	{
    		$where = "id LIKE '$where%' || title LIKE '$where%' || username LIKE '$where%'";
    	}

    	echo $this->users->count_users($where);
    }
}

/* End of file stats.php */
/* Location: ./application/controllers/stats.php */
