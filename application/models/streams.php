<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Streams extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // Retrieves all the recent posts for a user or a groups of users
    // Returns a string that can be inserted into a stream page
    // Streamers can be an array. Must always be user_id's given
    function retrieve_posts($streamers = NULL, $limit = 10, $offset = 0)
    {
        // Requires the posts model
        $this->load->model('posts');

        if($streamers == NULL) { return NULL; }

        // Determines if your are viewing multiple authors' posts
        if(is_array($streamers)) 
        { 
            $include_poster_pic = true; 
            // Reposts are not allowed - 0 indicates the post is not a repost
            $this->db->where('repost_id', 0);
        }
        else 
        { 
            $include_poster_pic = false; 
            $streamers = array($streamers);
        }

        $this->db->from('posts');
        $this->db->order_by('created_on', 'desc');
        $this->db->limit($limit, $offset);
        $this->db->select('id');
        $this->db->where('status', 'active');
        $this->db->where_in('author_id', $streamers);

        // Retrieve the ID's of the posts
        $stream_ids = $this->db->get();

        if($stream_ids->num_rows() == 0) { return '<div id="content" class="span9"><div id="no_content">There seems to be no content...</div></div>'; } // No posts
        else if($stream_ids->num_rows() == 1) { $stream_ids = $stream_ids->row_array(); $stream_ids = array($stream_ids); }
        else { $stream_ids = $stream_ids->result_array(); }
        // Convert the array of arrays into an array of id's
        $id_array = array();
        foreach($stream_ids as $array)
        {
            $id_array[] = $array['id'];
        }
        unset($stream_ids);
        // This retrieves all the database data about the 
        $stream_posts = $this->posts->get_post_data($id_array);
        // This creates the array with all the formatted posts
        $post_array = $this->posts->format_posts($stream_posts, $include_poster_pic);

        return $post_array;
    }

    // Retrieves the user's favorite posts in most-least recent order
    // Returns a string that can be inserted into a stream page
    function retrieve_favorites($user_id = NULL, $limit = 10, $offset = 0)
    {
        // Requires the posts model
        $this->load->model('posts');

        if($user_id == NULL) { return NULL; }

        $include_poster_pic = true; 

        // Find get the ID's of the user's favorite posts
        $this->db->select('post_id');
        $this->db->from('favorites');
        $this->db->where('user_id', $user_id);
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $fav_ids = array();
        if($query->num_rows() == 0) { return '<div id="content" class="span9"><div id="no_content">There seems to be no content...</div></div>'; }
        else if ($query->num_rows() == 1) { $fav_ids = $query->row()->post_id; $fav_ids = array($fav_ids); }
        else
        {
            foreach ($query->result() as $select) 
            {
                $fav_ids[] = $select->post_id;
            }
        }

        // This retrieves all the database data about the 
        $stream_posts = $this->posts->get_post_data($fav_ids);
            // This creates the array with all the formatted posts
        $post_array = $this->posts->format_posts($stream_posts, $include_poster_pic);

        return $post_array;
        
    }

    // Creates the database information for a new user
    // Needs a user array
    function create_stream($user_id)
    {
        $time = time();
        $user = $this->users->user_info($user_id, 'all');
        $data = array(
            'id' => $user_id,
            'title' => $user['username'],
            'created_on' => $time,
            'last_update' => $time
            );

        $this->db->insert('streams', $data);

        return true;
    }

    
    // Retireves the value of a about a stream given a user's ID
    // Return type is a PHP array 
    function stream_info($id, $options = "all")
    {
        if(empty($id)) { return false; }  // Must supply a user, 0 is not a valid user

        if($options == 'all') { $query = $this->db->get_where('streams', array('id' => $id), 1, 0); } 
        else
        {
            $this->db->from('streams');
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

    // Updates a streams's info given the collumn, info and user_id
    // Can be passed an array as well with matching key-pairs. Set collumn to 'all'
    // Will check to make sure the data isn't empty
    // Other checks should be done ahead of time, as this is a 'dumb' function
    function update_stream_info($collumn, $info, $user_id)
    {
        $this->db->where('id', $user_id);

        if(!is_array($info)) { $info = array($collumn => $info); }

        $this->db->update('streams', $info);
    }

    // Returns the number of streams a user is following given an ID
    function count_following($user_id, $type = 'all')
    {
        // Set the default
        if(empty($user_id)) { $user_id = $this->data['user-id']; }
        $this->db->from('following');
        $this->db->where('follower', $user_id);

        if($type == 'all')
        {
            return $this->db->count_all_results();
        }
        else
        {
            $this->db->where('status', $type);
            return $this->db->count_all_results();
        }
    }

    // Returns the number of users following a stream
    function count_followers($user_id, $status = 'all')
    {
        // Set the default
        if(empty($user_id)) { $user_id = $this->data['user-id']; }
        $this->db->from('following');
        $this->db->where('leader', $user_id);

        if($status == 'all')
        {
            return $this->db->count_all_results();
        }
        else
        {
            $this->db->where('status', $status);
            return $this->db->count_all_results();
        }
    }

    // Returns the number of users subscribed a stream
    // 'Free', 'Paid' or Both (default) count
    function count_subscribers($stream_id, $type = 'all')
    {
        // Set the default
        if(empty($stream_id)) { $stream_id = $this->data['user-id']; }

        $this->db->from('subscriptions');
        $this->db->where('stream_id', $stream_id);

        if($type == 'all')
        {
            return $this->db->count_all_results();
        }
        else
        {
            $this->db->where('type', $type);
            return $this->db->count_all_results();
        }
    }

    // Returns an array of all the ID's a users is following
    // Accepts a limit and offset
    function return_following($user_id, $limit = 500, $offset = 0, $status)
    {
        $this->db->from('following');
        $this->db->where('follower', $user_id);
        if($status != 'all') { $this->db->where('status', $status); }
        $this->db->select('leader');

        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        $data = array();
        if($query->num_rows() == 0) { return $data; }
        if($query->num_rows() == 1) { $data[] = $query->row()->leader; return $data; }
        foreach($query->result() as $row)
        {
            $data[] = $row->leader;
        }

        return $data;
    }

    // Returns an array of all the ID's of users following another user
    // Accepts a limit and offset
    function return_followers($user_id, $limit = 10, $offset = 0, $status = 'active')
    {
        $this->db->from('following');
        $this->db->where('leader', $user_id);
        $this->db->select('follower');
        if($status != 'all') { $this->db->where('status', $status); }

        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        $data = array();
        if($query->num_rows() == 0) { return $data; }
        if($query->num_rows() == 1) { $data[] = $query->row()->follower; return $data; }
        foreach($query->result() as $row)
        {
            $data[] = $row->follower;
        }

        return $data;
    }

    // Returns the ID of stream from its title
    function get_id_from_title($title)
    {
        // Add spaces back in from URL's
        $title = str_replace('%20', ' ', $title);

        $this->db->from('streams');
        $this->db->select('id');
        $this->db->where('title', $title);
        $query = $this->db->get();

        if($query->num_rows() != 1) { return false; }
        else { return $query->row()->id; }
    }

    // Returns the status if A is following B
    // Returns false if A is not follwing B
    function is_following($follower, $leader)
    {
        if($follower == NULL || $leader == NULL) { return false; }

        $this->db->from('following');
        $this->db->where('follower', $follower);
        $this->db->where('leader', $leader);
        $this->db->select('status');
        $query = $this->db->get();
        if($query->num_rows() == 0) { return false; }
        return $query->row()->status;
    }

    // Returns true if A is subscribed to B
    // Returns the subscription end time if true, 0 if not
    function is_subscribed($subscriber_id, $stream_id)
    {
        if($subscriber_id == NULL || $stream_id == NULL) { return false; }

        $this->db->from('subscriptions');
        $this->db->where('subscriber_id', $subscriber_id);
        $this->db->where('stream_id', $stream_id);
        $this->db->select('end_time');
        $query = $this->db->get();
        if($query->num_rows() == 1)
        {
            return $query->row()->end_time;
        }
        else { return false; }
    }

    // Given two user ID's, makes A follow B
    // DOES NOT CHECK FOR DUPLICATE ENTIRES
    function make_follow($A, $B)
    {
        // If the stream is set to private, then only request to follow
        if($this->streams->stream_info($B, 'access') == 'private')
        {
            $data = array('follower' => $A, 'leader' => $B, 'status' => 'requested');
        }
        else
        {
            $data = array('follower' => $A, 'leader' => $B);
        }
        $this->db->insert('following', $data);
        return true;
    }

    // Given two user ID's, makes A subscribe to B until $end_time
    // DOES NOT CHECK FOR DUPLICATE ENTIRES
    function subscribe($A, $B, $end_time)
    {
        $data = array('subscriber_id' => $A, 'stream_id' => $B, 'end_time' => $end_time);
        $this->db->insert('subscriptions', $data);
        return true;
    }

    // Stops A from following B
    // DOES NOT STOP B FROM FOLLOWING A
    function stop_following($A, $B)
    {
        return $this->db->delete('following', array('follower' => $A, 'leader' => $B)); 
    }

    // Counts all posts of a type, array of types or 'all'
    // Returns an interger in all cases, even user not found (0)
    function count_posts($user_id, $types = 'all')
    {
        $this->db->from('posts');
        $this->db->where('author_id', $user_id);
        $this->db->where('status !=', 'trashed');
        if($types == 'all')
        {
            return $this->db->count_all_results();   
        }
        elseif(is_array($types))
        {
            $this->db->where_in('type', $types);
        }
        else
        {
            $this->db->where('type', $types);
        }
        return $count_all_results();
    }

}


/* End of file /models/stream.php */
/* Location: ./application/modules/account/models/stream.php */