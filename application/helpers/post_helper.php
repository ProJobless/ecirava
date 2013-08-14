<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Given a user's ID goes through all the steps to finish their profile
// So if you need to activate an unactivated user you only need to update this
// to finsh their activation.
// e.g. Creating a stream for them
// REQUIRES: stream model, user model, db library

// Strongly consider moving this to a model since it is really a DB abstraction
// You may have already created a similar function so you will likely have to check which functions use what and merge
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

// Returns true if title checks out, returns an error message if it doesn't
// Empty titles are considered well formatted and return 'true'
if ( ! function_exists('title_check'))
{
    function title_check($title)
    {
        if(!empty($title))
        {
            if(strlen($title) > 100) { return 'Titles must be less than 100 characters!'; } // Actually less than 101 ;D
            return true;
        }
        else
        {
            return 'Title is empty';
        }
    }
}

// Formats the text of any post type (the content explanation area)
// Returns an empty string if empty
if ( ! function_exists('content_format'))
{
    function content_format($content)
    {
        // Update this to strip invisible characters 
        if(empty($content)) 
        { 
            return ""; 
        }
        $content = htmlspecialchars($content);
        $content = capcode($content, 'encode');
        $content = nl2br($content);
        return $content;
    }
}

// Returns the file path to uploaded images given a Unix timestamp
if ( ! function_exists('upload_path'))
{
    function upload_path($time)
    {
        return '/resources/uploads/'.date('Y', $time).'/'.date('m', $time).'/';
    }
}

