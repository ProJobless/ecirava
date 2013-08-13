<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// Returns the path to the user's profile image
// Returns the path to the default image if no image is found
// Requires a user ID to function. The ID does not have to be valid.
if ( ! function_exists('profile_pic_path'))
{
    function profile_pic_path($user_id)
    {
        $default_img = 'resources/profile_pics/default_prof_pic.png';
		$user_prof_pic = 'resources/profile_pics/'.$user['id'].'.png';
		if(file_exists($user_prof_pic)) { return $user_prof_pic; }
		else { return $default_img; }
    }   
}