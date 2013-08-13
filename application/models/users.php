<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // Checks to see if the user is logged in
    // Returns the user ID and updates their time if they're logged in
    // Returns False is they're not logged in
    function is_logged_in()
    {
        // Check to see if the user is logged in
        $id = $this->session->userdata('id'); // Retrieve the user's ID as well
        if($id) 
        {
            // If the last active time was over a minute ago, update their last active time in the database
            if($this->session->userdata('last_active') < (time()-60))
            {
                $data = array(
               'last_active' => time()
                );
            $this->db->where('id', $id);
            $this->db->update('users', $data); 
            //Update the session time
            $this->session->set_userdata('last_active', time());
            }
            return $id;
        }
        else { return false; }
    }

    // Retireves the value of a from the user, or an array of options
    // Returns all options in an array if no option or if "all" is specified
    // Returns false if there was no matching option or user, but true if at least one option matched (if using an array)
    function user_info($id, $options = "all")
    {
        if(empty($id) || $id == NULL) { return false; }  // Must supply a user, 0 is not a valid user
        if($options == 'all') { $query = $this->db->get_where('users', array('id' => $id), 1, 0); } 
        else
        {
            $this->db->from('users');
            $this->db->where('id =', $id);
            if(is_array($options))
            {
               $this->db->select("'". implode(",", $options)."'");
            }
            else
            {
                $this->db->select($options);
            }
            $query = $this->db->get();
        } 

        if($query->num_rows() == 0) { return false; }       // The option does not exist               
        else if(is_array($options) || $options == 'all')    // Returns just the value if only one item is requested
        {
            return $query->row_array();
        }
        else                                          
        {
            $query = $query->row_array();
            return $query[$options];
        }
    }

    // Updates a user's info given the collumn, info and user_id
    // Can be passed an array as well with matching key-pairs. Set collumn to 'all'
    // Will check to make sure the data isn't empty
    // Other checks should be done ahead of time, as this is a 'dumb' function
    function update_user_info($collumn, $info, $user_id)
    {
        $this->db->where('id', $user_id);

        if(!is_array($info)) { $info = array($collumn => $info); }

        $this->db->update('users', $info);
    }

    // Sends an email to the user's desired email to
    // ensure that they have access to it.
    // Adds an entry to the table 'update_email' to allow for confirmation
    function send_email_update_confirmation($email, $user_id)
    {
        // Create a unique confirmation string
        $time = time();
        $hash = hash('sha256', $email.$time);
        $confirmation = substr($hash, 0, 10);

        $link = '<a href="'.$_SERVER['SERVER_NAME'].'/user/confirm_email/'.$confirmation.'">'.$_SERVER['SERVER_NAME'].'/user/confirm_email/'.$confirmation.'</a>';

        // Email Config
        $config['protocol'] = 'sendmail';
        $config['mailtype'] = 'html';

        $this->email->initialize($config);

        // Send Welcome/Confirmation Email
        $this->email->from($this->site->retrieve('admin_email'), $this->site->retrieve('admin_username'));
        $this->email->to($email);
        $this->email->subject($this->site->retrieve('site_title')." - Updating Your Email");
        $this->email->message(str_replace("%link%", $link, $this->site->retrieve('email_confirm_message')));    
        // Alternative Email without HTML formatting
        $this->email->set_alt_message('Copy and paste this link into your browser to confirm your email change. '.$_SERVER['SERVER_NAME'].'/user/confirm_email/'.$confirmation);
        $this->email->send();

        $time = $time+24*60*60; // Gives the user 24 hour to update
        // Add a row to the 'update_email' table 
        // update the time and key on duplicate
        $query = "INSERT INTO `update_email` (`id`, `desired_email`, `email_change_code`,`expires_on`) VALUES ('$user_id', '$email', '$confirmation','$time') ON DUPLICATE KEY UPDATE desired_email='$email', email_change_code='$confirmation', expires_on=$time;";
        $query = $this->db->query($query);

    }

    // Updates a user's password to one based on the string supplied 
    function update_password($str, $user_id)
    {
        $password = $this->site->generate_password($str);

        $this->db->where('id', $user_id);
        $this->db->update('users', array('password' => $password));
    }

    // Returns all the user data as a PHP array
    // Must be filled out with a limit, offset, order_by, asc/desc and where clause
    // Default is to return the 10 most recent posts in descendign order
    function user_data($limit = 10, $offset = 1, $order_by = 'created_on', $ascdesc = 'desc', $where = 'all')
    {
        if($where == 'all') { $where = array('id >=' => 1); }
        
        $this->db->from('users');
        $this->db->order_by($order_by, $ascdesc);
        $this->db->limit($limit, $offset);
        $this->db->where($where);
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return array($query->row_array());
        }
        else if($query->num_rows() == 0)
        {
            return array();
        }
        else
        {
            return $query->result_array();
        }
    }

    // Counts the users given certain parameters
    // Parameters given in key/value pair form
    // Always returns an integer
    function count_users($param)
    {
        if($param == 'all')
        {
            return $this->db->count_all('users');
        }
        else
        {
            $this->db->where($param);
            return $this->db->count_all_results('users');
        }
    }

    // Takes a post ID and returns the author
    // Returns 0 (NOT FALSE) on return. Reminder that 0 is NOT a legal user_id number.
    function post_to_user($post_id)
    {
        if(empty($post_id)) { return 0; }

        $this->db->from('posts');
        $this->db->where('id', $post_id);
        $this->db->select('author_id');

        $query = $this->db->get();
        if($query->num_rows() != 1) { return 0; }

        return $query->row()->author_id;
    }
}


/* End of file /models/users.php */
/* Location: ./application/modules/account/models/users.php */