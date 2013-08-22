<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Soccer-Team admin menus
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package Soccer-Team
 * @subpackage menus
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

add_action('admin_menu', 'soccer_team_create_dashboard_menus') ;

function soccer_team_create_dashboard_menus()
{
    //  Create a new Top Level menu
    add_menu_page('Soccer Team', 'Soccer Team', 'manage_options',
        __FILE__, 'testing', plugins_url('/images/DashboardMenu.png',
        __FILE__)) ;
}
        //'soccer-team', 'edit.php?post_type=player', get_screen_icon('users'),

function testing()
{
    echo '<h1>Testing ...</h1>' ;
}
?>
