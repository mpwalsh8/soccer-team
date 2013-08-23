<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Plugin Name: Soccer Team
 * Plugin URI: http://michaelwalsh.org/wordpress/wordpress-plugins/soccer-team/
 * Description: The Soccer-Team WordPress plugin extend WordPress functionality to facilitate using WordPress for a soccer team web site.  The plugin include custom post types and widgets for team and player profiles, matches, and photo gallerys and more.
 * Version: 0.6-beta-4
 * Last Modified:  8/21/2013
 * Author: Mike Walsh
 * Author URI: http://www.michaelwalsh.org
 * License: GPL
 * 
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package soccer-team
 * @subpackage admin
 * @version $Rev$
 * @lastmodified $Date$
 * @lastmodifiedby $LastChangedBy$
 *
 */

define('ST_VERSION', '0.6-beta-4') ;
define('ST_PREFIX', 'soccer_team_') ;

require('soccer-team-core.php') ;
require('soccer-team-post-type.php') ;
//require('soccer-team-menus.php') ;
require('soccer-team-widgets.php') ;


// Use the register_activation_hook to set default values
register_activation_hook(__FILE__, 'soccer_team_register_activation_hook');

// Use the init action
add_action('init', 'soccer_team_init' );

// Use the wp_head action
//add_action('wp_head', 'soccer_team_carousel_head' );

// Use the admin_menu action to add options page
add_action('admin_menu', 'soccer_team_admin_menu');

// Use the admin_init action to add register_setting
add_action('admin_init', 'soccer_team_admin_init' );

?>
