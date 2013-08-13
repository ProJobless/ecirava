<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Given a user's ID goes through all the steps to finish their profile
// So if you need to activate an unactivated user you only need to update this
// to finsh their activation.
// e.g. Creating a stream for them
// REQUIRES: stream model, user model, db library
if ( ! function_exists('post_author_id'))
{
    function post_author_id($post_id)
    {
        $CI =& get_instance();
        
        // Retrieve the post's owner's ID
        $CI->db->from('posts');
        $CI->db->where('id', $post_id);
        $CI->db->select('author_id');

        $query = $CI->db->get();

        if($query->num_rows() == 0) { return NULL; }

        return $query->row()->author_id;
    }
}