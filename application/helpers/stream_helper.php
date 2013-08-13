<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Returns true if the server can connect to the URL given, false if not
// If the URL redirects, it returns true, but does NOT follow the redirect
if ( ! function_exists('generate_link_post'))
{
	// Generates a link formatted string given a stream_post array
	// $user_id is the id of the user viewing the stream, so that teh edit post button will appear if it is their own post
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
