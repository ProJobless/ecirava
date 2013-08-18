<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

        // Load the utility helper
        $this->load->helper('utility');
    }

    // Adds a new comment given a post ID and data
    function new_comment($post_id, $data)
    {
        // Check the data
        if(empty($data['author_id'])) { $data['author_id'] = $this->data['user_id']; }
        if(empty($data['post_id'])) { return false; }
        if(empty($data['reply_id'])) { $data['reply_id'] = 0; }
        if(empty($data['content'])) { return false; }
        if(empty($data['created_on'])) { $data['created_on'] = time(); }

        $this->db->insert('comments', $data);

        // Now increment the post num_comments count
        $query = "UPDATE `posts` SET num_comments=num_comments+1 WHERE id=$post_id";
        $this->db->query($query);

        return true;
    }

    // Retrieves all the comment data as an array based on post ID 
    // Returns an empty array on failure to find comment
    function get_post_comments($post_id)
    {
        $this->db->from('comments');
        $this->db->where('post_id', $post_id);
        $data = $this->db->get();

        if($data->num_rows() == 0) { return array(); }
        else if($data->num_rows() == 1) { return array($data->row_array()); }
        else { return $data->result_array(); }

    }

}


/* End of file /models/comments.php */
/* Location: ./application/modules/account/models/comments.php */