<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Returns true if the server can connect to the URL given, false if not
// If the URL redirects, it returns true, but does NOT follow the redirect
if ( ! function_exists('is_url_live'))
{
    function is_url_live($url = NULL)
    {
        if($url == NULL) { return false; }  

        $ch = curl_init($url);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        $data = curl_exec($ch);  
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
        curl_close($ch); 

        if($httpcode>=200 && $httpcode<303) { return true; } 
        else { return false; }  
    }   
}

// Returns true if the URL is formatted correctly (since PHP filter_var is terrible)
if ( ! function_exists('is_url_valid'))
{
    function is_url_valid($url = NULL)
    {
        if($url == NULL) { return false; }  

        $urlregex = "~^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$~";
        
        return preg_match($urlregex, $url);
    }
}

// Returns a string with with HTML formatting or capcodes. 
// Edit the arrays to edit the cap codes
if ( ! function_exists('capcode'))
{
    function capcode($str, $direction)
    {
        $capcodes = array("[b]", "[/b]", "[i]", "[/i]");
        $html_ent = array("<strong>", "</strong>", "<i>", "</i>");
        if($direction == 'encode') { return str_replace($capcodes, $html_ent, $str); }
        else { return str_replace($html_ent, $capcodes, $str); }
    }   
}

// Changes all HTML <br /> tags to new lines
if ( ! function_exists('br2nl'))
{ 
    function br2nl($string) 
    { 
        return preg_replace('`<br(?: /)?>([\\n\\r])`', '$1', $string); 
    }
}

// Changes all newlines into <p></p> tags
if ( ! function_exists('nls2p'))
{
    function nls2p($str)
    {
        return str_replace('<p></p>', '', '<p>'.preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str).'</p>');
    }
}

// Turns a PHP array into one ready to paste into JS
// e.g. ['tag1', 'tag2', 'tag3']
if ( ! function_exists('php_to_js_array'))
{
    function php_to_js_array($array)
    {
        return "['".implode($array, "','")."']";
    }
}

// Retrieves the extension of a file given its filename
if ( ! function_exists('get_ext'))
{
    function get_ext($file_name)
    {
        $ext = end(explode(".", $file_name));

        return $ext;
    }
}



