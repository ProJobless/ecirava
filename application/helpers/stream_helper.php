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
