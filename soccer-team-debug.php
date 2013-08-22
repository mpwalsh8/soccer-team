<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * GForm functions.
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package wpGForm
 * @subpackage functions
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

global $soccer_team_debug_content ;

$soccer_team_debug_content = '' ;
add_action('init', 'soccer_team_debug', 0) ;
add_action('wp_footer', 'soccer_team_show_debug_content') ;

/**
 * Debug action to examine server variables
 *
 */
function soccer_team_debug()
{
    global $wp_filter ;

    soccer_team_error_log($_POST) ;

    if (!is_admin())
    {
        soccer_team_whereami(__FILE__, __LINE__, '$_SERVER') ;
        soccer_team_preprint_r($_SERVER) ;
        soccer_team_whereami(__FILE__, __LINE__, '$_ENV') ;
        soccer_team_preprint_r($_ENV) ;
        soccer_team_whereami(__FILE__, __LINE__, '$_POST') ;
        soccer_team_preprint_r($_POST) ;
        soccer_team_whereami(__FILE__, __LINE__, '$_GET') ;
        soccer_team_preprint_r($_GET) ;

        if (array_key_exists('init', $wp_filter))
        {
            soccer_team_whereami(__FILE__, __LINE__, '$wp_filter[\'init\']') ;
            soccer_team_preprint_r($wp_filter['init']) ;
        }
        if (array_key_exists('template_redirect', $wp_filter))
        {
            soccer_team_whereami(__FILE__, __LINE__, '$wp_filter[\'template_redirect\']') ;
            soccer_team_preprint_r($wp_filter['template_redirect']) ;
        }
    }
}

/**
 * Debug action to display debug content in a DIV which can be toggled open and closed.
 *
 */
function soccer_team_show_debug_content()
{
    global $soccer_team_debug_content ;
?>
<style>
h2.st-debug {
    text-align: center;
    background-color: #ffebe8;
    border: 2px solid #ff0000;
}

div.st-debug {
    padding: 10px;
}

div.st-debug h2 {
    background-color: #f00;
}

div.st-debug h3 {
    padding: 10px;
    color: #fff;
    font-weight: bold;
    border: 1px solid #000000;
    background-color: #024593;
}

div.st-debug pre {
    color: #000;
    text-align: left;
    border: 1px solid #000000;
    background-color: #c6dffd;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function($) {
        $("div.st-debug").hide();
        $("a.st-debug-wrapper").show();
        $("a.st-debug-wrapper").text("Show wpGForm Debug Content");
 
    $("a.st-debug-wrapper").click(function(){
    $("div.st-debug").slideToggle();

    if ($("a.st-debug-wrapper").text() == "Show wpGForm Debug Content")
        $("a.st-debug-wrapper").text("Hide wpGForm Debug Content");
    else
        $("a.st-debug-wrapper").text("Show wpGForm Debug Content");
    });
});
</script>
<div class="st-debug">
    <?php echo $soccer_team_debug_content ; ?>
</div>
<?php
}

/**
 * soccer_team_send_headers()
 *
 * @return null
 */
function soccer_team_send_headers()
{
    header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
    header('Expires: ' . date(DATE_RFC822, strtotime('yesterday'))); // Date in the past
    header('X-Frame-Options: SAMEORIGIN'); 
}

/**
 * Debug "where am i" function
 */
function soccer_team_whereami($f, $l, $s = null)
{
    global $soccer_team_debug_content ;

    if (is_null($s))
    {
        $soccer_team_debug_content .= sprintf('<h3>%s::%s</h3>', basename($f), $l) ;
        error_log(sprintf('%s::%s', basename($f), $l)) ;
    }
    else
    {
        $soccer_team_debug_content .= sprintf('<h3>%s::%s::%s</h3>', basename($f), $l, $s) ;
        error_log(sprintf('%s::%s::%s', basename($f), $l, $s)) ;
    }
}

/**
 * Debug functions
 */
function soccer_team_preprint_r()
{
    global $soccer_team_debug_content ;

    $numargs = func_num_args() ;
    $arg_list = func_get_args() ;
    for ($i = 0; $i < $numargs; $i++) {
	    $soccer_team_debug_content .= sprintf('<pre style="text-align:left;">%s</pre>', print_r($arg_list[$i], true)) ;
    }
    soccer_team_error_log(func_get_args()) ;
}
/**
 * Debug functions
 */
function soccer_team_error_log()
{
    $numargs = func_num_args() ;
    $arg_list = func_get_args() ;
    for ($i = 0; $i < $numargs; $i++) {
	    error_log(print_r($arg_list[$i], true)) ;
    }
}
?>
