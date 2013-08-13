<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// Returns the path to the user's profile image
// Returns the path to the default image if no image is found
// Requires a user ID to function. The ID does not have to be valid.
if ( ! function_exists('profile_pic_path'))
{
    function profile_pic_path($user_id)
    {
        $default_img = 'resources/profile_pics/default_prof_pic.png';
		$user_prof_pic = 'resources/profile_pics/'.$user_id.'.png';
		if(file_exists($user_prof_pic)) { return '/'.$user_prof_pic; }
		else { return '/'.$default_img; }
    }   
}

// Given a user's id, set's the following data[] variables
// "user_id"  "is_logged_in"  "admin"
// Requires the Users model to be opened
if ( ! function_exists('set_login_data'))
{
    function set_login_data($user_id)
    {        
        $CI =& get_instance();
        $CI->data['admin'] = FALSE;
        if($user_id)
        {
            $CI->data['is_logged_in'] = TRUE;
            $CI->data['user_id'] = $user_id;
            if($CI->users->user_info($user_id, 'user_group') == 'admin')
            {
                $CI->data['admin'] = TRUE;
            }
        }
        else
        {
            $CI->data['is_logged_in'] = FALSE;
            $CI->data['user_id'] = NULL;
        }
    }   
}

// Given a user's ID goes through all the steps to finish their profile
// So if you need to activate an unactivated user you only need to update this
// to finsh their activation.
// e.g. Creating a stream for them
// REQUIRES: stream model, user model, db library
if ( ! function_exists('register_user'))
{
    function register_user($user_id)
    {
        $CI =& get_instance();
        // Change user type to 'user' from 'unactivated'
        $data = array('user_group' => 'user');
        $CI->db->update('users', $data, "id = $user_id"); 

        // Create a stream for this user
        $CI->streams->create_stream($user_id);
    }
}