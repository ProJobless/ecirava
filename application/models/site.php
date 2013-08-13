<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // Retireves the value of a the site option, or an array of options
    // Returns all options in an array if no option or if "all" is specified
    // Returns false if there was no matching option, but true if at least one option matched (if using an array)
    function retrieve($options = "all")
    {
        if($options == "all")    { $query = $this->db->get('site_settings'); } 
        else
        {
            $this->db->select('option, value');
            $this->db->from('site_settings');
            if(is_array($options))
            {
                foreach($options as $opt)
                {
                    $this->db->or_where('option =', $opt);
                }
            }
            else
            {
                $this->db->where('option =', $options);
            }
            $query = $this->db->get();
        } 

        if($query->num_rows() == 0) { return false; } // The option does not exist               
        else if($query->num_rows() == 1)              // Returns just the value if only one item is requested
        {
            $query = $query->row();
            return $query->value;
        }
        else                                          // Returns an array that keys the setting's name (option) to its value
        {
            $data = array();
            foreach($query->result() as $row)
            {
                $data[$row->option] = $row->value;
            }
            return $data;
        }
    }

    // Retrieves the title of the page and its description given an ID or name of the page
    // Names must be unique
    function page($var = 1)
    {
        $var = strtolower($var);
        $query = $this->db->select('title, description', 1, 0);
        $query = $this->db->from('pages');
        $query = $this->db->where('page_ref', $var);

        $query = $this->db->get();
        return $query->row();
    }

    // Generates and returns a new password hash given a string
    // ---Move this to a security based model, I think that makes more sense-- // NOPE MAKE IT A HELPER FILE SO ERRY BODY CAN USE IT
    function generate_password($str)
    {
        return substr(hash('sha256', $str.$this->site->retrieve('site_salt')), 0, 50);
    }
}


/* End of file /models/site.php */
/* Location: ./application/modules/account/models/site.php */