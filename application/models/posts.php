<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posts extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

        // Load the post helper
        $this->load->helper('post');
    }

    // Retireves the value of a about a post type given a post type
    // Return type is a PHP array 
    function post_type_info($type, $options = "all")
    {
        if(empty($type)) { return false; }  // Must supply a post type

        if($options == 'all') { $query = $this->db->get_where('post_meta', array('post_type' => $type), 1, 0); } 
        else
        {
            $this->db->from('post_meta');
            $this->db->where('post_type =', $type);
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

    // Retireves the value of the posts option, or an array of options
    // Returns all options in an array if no option or if "all" is specified
    // Returns false if there was no matching option, but true if at least one option matched (if using an array)
    // Do not specify more than one type of option retrieval at a time. e.g. 'banned-tags' which will create and array of just banned tags
    // and other options that are sent back in $data['option'] => value key pairs
    function retrieve($options = "all")
    {
        if($options == "all")    { $query = $this->db->get('post_settings'); } 
        else
        {
            $this->db->select('option, value');
            $this->db->from('post_settings');
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
                if(isset($data[$row->option]))
                {
                    $data[] = $row->value;
                }
                $data[$row->option] = $row->value;
            }
            return $data;
        }
    }

    // Returns true if the post is favorited by a use, false if not
    function is_favorited($user_id, $post_id)
    {
        $this->db->from('favorites');
        $this->db->where('user_id', $user_id);
        $this->db->where('post_id', $post_id);
        $num = $this->db->count_all_results();

        if($num == 1) { return true; }
        return false;
    }

    // Returns true if the post is favorited by a use, false if not
    function is_reblogged($user_id, $post_id)
    {
        $this->db->from('posts');
        $this->db->where('author_id', $user_id);
        $this->db->where('repost_id', $post_id);

        if($this->db->count_all_results() == 1) { return true; }
        return false;
    }


    // Insert tags into 'tags' and 'post_to_tags'
    // $tags can be an array or single tag
    // Returns the number of tags inserted
    function insert_tags($tags, $post_id)
    {
        $num_tags = 0;
        $banned_tags = array();
        $banned_tags[] = $this->posts->retrieve('banned_tags');
        if(!is_array($tags)) { $tags = array($tags); }
        if(!empty($tags))
        {
            foreach ($tags as $tag) 
            {
                $tag = strtolower($tag);
                $tag = htmlspecialchars($tag);;

                if(in_array($tag, $banned_tags)) { continue; }
                if(strlen($tag) < 2 || strlen($tag) > 100) { continue; }

                $query = "INSERT INTO `tags` (`tag`, `count`) VALUES ('$tag', '1') ON DUPLICATE KEY UPDATE `id`=LAST_INSERT_ID(id), `count`=count+1";
                $this->db->query($query);

                $this->db->insert('post_to_tags', array('post_id' => $post_id, 'tag_id' => $this->db->insert_id()));
                $num_tags++;
            }
            return $num_tags;
        }
        else { return $num_tags; }
    }

    // Returns an array of the last X most popular tags
    function get_tags($amt = 100, $order_by = 'count', $ascdesc = 'desc')
    {
        $this->db->select('tag');
        $this->db->from('tags');
        $this->db->order_by($order_by, $ascdesc);

        $query;
        if($amt == 'all')
        {
            $query = $this->db->get();
        }
        else
        {
            $this->db->limit($amt);
            $query = $this->db->get();
        }
        if($query->num_rows() == 0) { return array(); }
        else if($query->num_rows() == 1) { return array($query->row()->tag); }
        else
        {
            $data = array();
            foreach ($query->result() as $row) 
            {
                $data[] = $row->tag;
            }
            return $data;
        }
    }

    // Retrieves the primary (from `posts`) post data and secondary post data
    // Takes a single, or array of post ID's 
    // Returns an array of arrays with both primary and soncardy post data merged
    function get_post_data($ids = NULL)
    {
        // Active Record throws a fit if you use table prefixes without this
        $db['default']['_protect_identifiers'] = FALSE;

        if($ids == NULL) { return NULL; }
        if(!is_array($ids))
        {
            $ids = array($ids);
        }
        $return_array =  array();

        foreach($ids as $id)
        {
            $primary = $this->db->get_where('posts', array('id' => $id));
            if($primary->num_rows() == 0) { return NULL; }
            else if($primary->num_rows() == 1) { $primary = $primary->row_array(); }
            else { $primary = $primary->result_array(); }

            // Text type posts do not have secondary data
            if($primary['type'] != 'text')
            {
                $table_2 = 'post_'.$primary['type'];
                $secondary = $this->db->get_where($table_2, array('post_id' => $id));
                
                // This means it is a repost so we have to look up where to find the secondary post data
                if($secondary->num_rows() == 0) 
                { 
                    $this->db->select($table_2.'.*');
                    $this->db->from($table_2);
                    $this->db->join('posts', $table_2.'.post_id = posts.repost_id');
                    $this->db->where('posts.id', $id);
                    $secondary = $this->db->get();
                }
                if($secondary->num_rows() == 1) { $secondary = $secondary->row_array(); }
                else { $secondary = $secondary->result_array(); }

                foreach($secondary as $key => $value) 
                {
                    if($key == 'post_id') { continue; }
                    $primary[$key] = $value;
                }
            }
            
            $return_array[] = $primary;
        }
        return $return_array;
    }

    // Counts the posts given certain parameters
    // Parameters given in key/value pair form or MySQL query form
    // Always returns an integer
    function count_posts($param)
    {
        if($param == 'all')
        {
            return $this->db->count_all('posts');
        }
        else
        {
            $this->db->from('posts');
            $this->db->join('users', 'users.id = posts.author_id');
            $this->db->where($param);
            $query = $this->db->get();

            return $query->num_rows();
        }
    }

    // Returns all the post data as a PHP array
    // Must be filled out with a limit, offset, order_by, asc/desc and where clause
    // Default is to return the 10 most recent posts in descending order
    function post_data($limit = 10, $offset = 1, $order_by = 'posts.created_on', $ascdesc = 'desc', $where = 'all')
    {
        if($where == 'all') { $where = array('id >=' => 1); }

        $this->db->from('posts');
        $this->db->order_by($order_by, $ascdesc);
        $this->db->limit($limit, $offset);
        $this->db->join('users', 'users.id = posts.author_id');
        $this->db->where($where);
        $this->db->select('posts.*');
        $this->db->select('users.username');
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

    // Updates a post's info given the collumn, info and post_id
    // Can be passed an array as well with matching key-pairs. Set collumn to 'all'
    // Will check to make sure the data isn't empty
    // Other checks should be done ahead of time, as this is a 'dumb' function
    function update_post_info($collumn, $info, $post_id)
    {
        $this->db->where('id', $post_id);

        if(!is_array($info)) { $info = array($collumn => $info); }

        $this->db->update('posts', $info);
    }

    // Deletes a post permanently, and all associated content
    function delete($post_id)
    {
        // Get the post info
        $post_type = $this->posts->post_info($post_id, 'type');
        $time = $this->posts->post_info($post_id, 'created_on');
        $repost_id = $this->posts->post_info($post_id, 'repost_id');

        // Get author ID
        $author_id = post_author_id($post_id);

        // Reduce the user's score
        $points = $this->posts->post_type_info($post_type, 'all');
        $this->points->remove_points($points['base_points'], TRUE, $author_id);
 
        // Delete from the posts table
        $this->db->delete('posts', array('id' => $post_id));

        // Delete from secondary table
        // Don't have to worry about reposts, delete won't find a matching post
        $table = 'post_'.$post_type;
        $this->db->delete($table, array('post_id' => $post_id));

        // If the post is a repost you don't need to delete additional data
        if($repost_id != 0) { return; }

        // Add specifc content removal for posts types
        // Removing Image Posts
        if($post_type == 'images')
        {
            $upload_path = upload_path($time);
            $images = $this->posts->get_images($post_id);
            // Deletes all the images
            foreach($images as $img)
            {
                unlink($_SERVER['DOCUMENT_ROOT'].$upload_path.$img);
                unlink($_SERVER['DOCUMENT_ROOT'].$upload_path.'fs_'.$img);
                unlink($_SERVER['DOCUMENT_ROOT'].$upload_path.'sm_'.$img);
                unlink($_SERVER['DOCUMENT_ROOT'].$upload_path.'tb_'.$img);
            }
            // Delete from the DB
            $this->db->delete('images', array('post_id' => $post_id));
        }

        // Delete all reposts of the data
        $this->db->delete('posts', array('repost_id' => $post_id));
    }

    // Returns the an array of data about a specific post
    // Returns all options in an array if no option or if "all" is specified
    // Returns false if there was no matching option or post, but true if at least one option matched (if using an array)
    function post_info($id, $options = "all")
    {
        if(empty($id) || $id == NULL) { return false; }  

        if($options == 'all') { $query = $this->db->get_where('posts', array('id' => $id), 1, 0); } 
        else
        {
            $this->db->from('posts');
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

    // Sends back and array with the image names of the post
    // Takes an optional parameter to return the name of a certain size
    function get_images($post_id, $type = "full")
    {

        $prefix = array(
            'full' => '',
            'post' => 'fs_',
            'thumb' => 'tb_',
            'thumbnail' => 'tb_',
            'small' => 'sm_'
            );

        // Fix bad inputs
        if(!array_key_exists($type, $prefix))
        {
            $type = 'full';
        }

        $this->db->select('img');
        $this->db->from('images');
        $this->db->where('post_id', $post_id);
        $images = $this->db->get();

        if($images->num_rows() == 1)
        {
            $images = $images->row();
            $images = $prefix[$type].$images->img;
            return array($images);
        }
        else
        {
            $images = $images->result();
        }

        $img_names= array();
        foreach($images as $image)
        {
            $name = $prefix[$type].$image->img;
            array_push($img_names, $name);
        }

        return $img_names;
    }

    // Formats the posts for output to the stream
    // Returns an array of strings, each index a string of the post
    // Accepts an array of ID's or a single ID
    function format_posts($posts, $include_poster_pic)
    {
        // Requires the user helper to format the profile picture
        $this->load->helper('user');


        // Load the users model
        $this->load->model('users');

        if(!is_array($posts)) { $posts = array($posts); }
        $post_array = array();

        foreach($posts as $post)
        {      
            // Format the poster's profile picture
            if($include_poster_pic)
            {
                $pic = ' <div class="poster_pic">
                        <a href="/stream/view/'.$post['author_id'].'"><img src="'.profile_pic_path($post['author_id']).'" /></a>
                    </div>';
            }
            else { $pic = ''; }

            // Check if this post is a repost, so that the original post is reposted
            if($post['repost_id'] != 0)
            {
                $repost_id = $post['repost_id'];
                $reposted_icon = '<img class="repost_icon tooltip_me" data-html="true" data-placement="right" onmouseover="get_repost_author_pic(this, '.$post['repost_id'].')" src="/resources/img/repost.png" />';
            }
            else
            {
                $repost_id = $post['id'];
                $reposted_icon ="";
            }

            // Check if it belongs to the viewer as a REPOST (don't display favorite/reblog)
            if($this->users->post_to_user($post['repost_id']) == $this->data['user_id'])
            {
                // Remove the reblog/favorite button
                $reblog_button = "";
                $favorite_button = "";
            }
            // Check if the post directly belongs to the viewer, so they can edit or delete it
            else if($post['author_id'] == $this->data['user_id'])
            {
                $pic = $pic.'<div class="edit_post">
                            <div class="hidden"><a href="#">edit</a> | <a href="#">delete</a></div>
                        </div>';
                // Remove the reblog/favorite button
                $reblog_button = "";
                $favorite_button = "";
            }           
            else
            {
                // Check if the post is already favorited
                if(!$this->posts->is_favorited($this->data['user_id'], $post['id']))
                {
                    $favorite_button = '<h4 onclick="favorite(this, '.$post['id'].')">Favorite?</h4>';
                }
                else
                {
                    $favorite_button = '<h4 class="green_bg" onclick="favorite(this, '.$post['id'].')">Favorited</h4>';
                }

                // Check if the post is already reblogged by the user
                if(!$this->posts->is_reblogged($this->data['user_id'], $post['id']))
                {
                    $reblog_button = '<h4 onclick="reblog(this, '.$repost_id.')">Reblog</h4>';
                }
                else
                {
                    $reblog_button = '<h4 class="green_bg">Posted</h4>';
                }

            }


            // Format the date
            if(function_exists('relative_date')) { $date = relative_date($post['created_on']); }
            else { $date = date('F j, Y, g:i a',$post['created_on']); }

            // The header of each post
            $header = '<div id="p_'.$post['id'].'" class="span9 stream_post">
                    '.$pic.'
                    <div class="post_title">
                        <a href="#">'.$post['title'].'</a>
                        '.$reposted_icon.'
                    </div>
                    <div class="link_src">
                        <a href="#">Perma-link to post</a>
                    </div>';

            // The footer of each post
            $footer = '<div class="post_meta">
                                <div class="pull-left"><a href="javascript:void(0)" onclick="show_comments(this, '.$repost_id.')" style="float:left">'.$post['num_comments'].' Comments</a></div>
                                <div class="pull-right unselectable">'.$favorite_button.' '.$reblog_button.' <h4 onclick="comment(this, '.$post["id"].', 0)">Comment</h4></div>
                            </div>
                            <div style="clear:both;"></div>
                            <div id="comments_'.$post["id"].'" class="comments">
                            </div>
                            <div class="load_comments">
                            </div>
                            <div style="clear:both;"></div>
                            <div style="clear:both;"></div>
                            <div class="post_meta">
                                <div class="pull-left"><a href="#">Full Post</a></div>
                                <div class="pull-right unselectable">'.$date.'</div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                    </div>'; 

            if($post['type'] == 'text')
            {
                $return_string = generate_text_post($post, $pic, $reposted_icon);
            }
            else if($post['type'] == 'link')
            {
                // Ignore the header, link type posts have a unique header that doesn't link to the post but to the link itself
                // It therefore needs the $pic and $reposted_icon variables passed to it
                $header = "";
                $return_string = generate_link_post($post, $pic, $reposted_icon);
            }
            else if($post['type'] == 'images')
            {
                $images = $this->posts->get_images($repost_id);
                $return_string = generate_images_post($post, $images, $pic, $reposted_icon);
            }

            // Assemble the post
            array_push($post_array, $header.$return_string.$footer);
        }

        return $post_array;
    }

    // Returns true if the post exists
    function post_exists($post_id)
    {
        $this->db->from('posts');
        $this->db->where('id', $post_id);

        if($this->db->count_all_results() == 1) { return true; }
        else { return false; }
    }
}


/* End of file /models/posts.php */
/* Location: ./application/modules/account/models/posts.php */