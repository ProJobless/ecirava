<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Generates a link formatted string given a stream_post array
// $user_id is the id of the user viewing the stream, so that teh edit post button will appear if it is their own post
if ( ! function_exists('generate_link_post'))
{
    function generate_link_post(&$post, $pic, $reposted_icon)
    {
	    if(!is_array($post)) { return 'ERROR: No array given. Could not generate link.'; }

	    return 	'<div id="'.$post['id'].'" class="span9 stream_post">
	                '.$pic.'
	                <div class="post_title">
	                    <a href="'.$post['src'].'">'.$post['title'].'</a>
	                    '.$reposted_icon.'
	                </div>
	                <div class="link_src">
	                    <a href="'.$post['src'].'">'.$post['src'].'</a>
	                </div>
	                <div> <!--Use this to collapse to title only-->
	                    <div class="content">
	                    '.$post['text'].'
	                    </div>';
    }
}

// Generates a text formatted string given a stream_post array
// $user_id is the id of the user viewing the stream, so that the edit post button will appear if it is their own post
if ( ! function_exists('generate_text_post'))
{
    function generate_text_post(&$post, $pic, $reposted_icon)
    {
	    if(!is_array($post)) { return 'ERROR: No array given. Could not generate post.'; }

	    return 	'<div> <!--Use this to collapse to title only-->
	                    <div class="content">
	                    '.$post['text'].'
	                    </div>';
    }
}

// Generates a images formatted string given a stream_post array
// $user_id is the id of the user viewing the stream, so that the edit post button will appear if it is their own post
if ( ! function_exists('generate_images_post'))
{
    function generate_images_post(&$post, $images, $pic, $reposted_icon)
    {
	    if(!is_array($post)) { return 'ERROR: No array given. Could not generate post.'; }

	    $image_str = "";
	    foreach($images as $image)
	    {
	    	$image = '<a href="'.upload_path($post['created_on']).$image.'" class="lightbox" rel="'.$post['id'].'"><img src="'.upload_path($post['created_on']).'tb_'.$image.'" class="stream_img" /></a>';
	    	$image_str = $image_str.$image;
	    }

	    return 	'<div> <!--Use this to collapse to title only-->
	    				<div class="images">
	    				'.$image_str.'
	    				</div>
	                    <div class="content">
	                    '.$post['text'].'
	                    </div>';
    }
}

// Returns true if the user has permission to view a stream (false if they don't)
if ( ! function_exists('permission_to_view'))
{
    function permission_to_view($user_id, $stream_id)
    {
    	// Get an instance reference
    	$CI =& get_instance();

    	// Get the stream access and subscription level
    	$access_lvl = $CI->streams->stream_info($stream_id, 'access');

    	// Find out if the user is following the stream already
    	$is_following = $CI->streams->is_following($user_id, $stream_id);

    	// Find out if the user is subscribed to the stream
    	$is_subscribed = $CI->streams->is_subscribed($user_id, $stream_id);

    	// Check to see if the user has permission to view this stream
		// Must be logged into view restricted streams
		if(($access_lvl == 'restricted') && (!$CI->data['is_logged_in'])) { return false; }

		// Must be following to view private streams. Being subscribed overides not following. You can always view your own stream. Admins can always view streams.
		if(($access_lvl == 'private') && ($is_following != 'active') && !$is_subscribed && ($stream_id != $user_id) && !$CI->data['admin'])
		{
			return false;
		}
    	return true;
    }
}





