<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// Sets the page title and meta data
// Requires the site model to be loaded (it is autoloaded everypage by default)
// Returns true if it doesn't fail
if ( ! function_exists('set_page_data'))
{
    function set_page_data($page = 'home')
    {
        $CI =& get_instance();
        // Set the page meta data
        $temp = $CI->site->page($page);
        if($temp)
        {
            $CI->data['page_title'] = $temp->title;
            $CI->data['page_description'] = $temp->description;
        }
        // Set the site meta data
        $CI->data['site_title'] = $CI->site->retrieve(array('site_title'));
        return true;
    }  
} 

// Takes a unix timestamp and returns a string that references how long it has been since then
// Detail will return the hour/minute/seconds it was posted as well instead of just the date
if(!function_exists('relative_date'))
{
    function relative_date($timestamp, $detail = FALSE)
    {
        $phrase;
        $time = time();
        $diff = $time - $timestamp;
        if($diff < 0)               { return 'In the Future'; }
        else if($diff < 15)         { return 'A Few Seconds Ago'; }
        else if($diff < 45)         { return 'Half a Minute Ago'; } 
        else if($diff < 120)        { return 'A Minute Ago'; }
        else if($diff < 3599)       { return floor($diff/60).' Minutes Ago'; }  
        else if($diff < 7199)       { return 'One Hour Ago'; }
        else if($diff < 86400)      { return floor($diff/3600).' Hours Ago'; }
        else if ($diff < 172799)    { return 'One day ago';}
        else if($diff < 259200)     { return floor($diff/86400).' Days Ago'; }
        else                        { 
                                        if($detail) { return date("F jS, Y, g:i a", $timestamp); }
                                        else { return date("F jS, Y", $timestamp); }
                                    }
        return;    
    }
}
