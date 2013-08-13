<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$temp; // Throwaway variable used to reduce DB calls
		$error_db = 'Database Error';

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
		$temp = $this->site->retrieve(array(
			'news_title',
			'front_page_news', 
			'featured_image'
			));

		// Set site and page meta data
		set_page_data('home');
		
		// Get Front page news
		$this->data['news_title'] = $temp['news_title'];
		$this->data['front_page_news'] = $temp['front_page_news'];

		//Featured Image
		$this->data['featured_image'] = $temp['featured_image'];

		$this->load->view('home', $this->data);
	}
	// Used for debugging, remove before final use
	public function test()
	{
		// Active Record throws a fit if you use table prefixes without this
		$db['default']['_protect_identifiers'] = FALSE;
		$table_2 = 'post_link';
		$id = 22;
		$this->db->select($table_2.'.*');
        $this->db->from($table_2);
        $this->db->join('posts', $table_2.'.post_id = posts.repost_id');
        $this->db->where('posts.id', $id);

        $query = $this->db->get();

		var_dump($query->row());

		echo '<br />';
		echo $this->db->last_query();
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
