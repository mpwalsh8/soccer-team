<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Soccer-Team functions.
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package Soccer-Team
 * @subpackage functions
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

// Filesystem path to this plugin.
define('SOCCER_TEAM_PLUGIN_PATH',
    WP_PLUGIN_DIR . '/' .  dirname(plugin_basename(__FILE__))) ;

//  Enable debug content?
define('SOCCER_TEAM_DEBUG', false) ;

if (SOCCER_TEAM_DEBUG)
{
    error_reporting(E_ALL) ;
    require_once('soccer-team-debug.php') ;
    add_action('send_headers', 'soccer_team_send_headers') ;
}

/**
 * soccer_team_init()
 *
 * Init actions to enable shortcodes.
 *
 * @return null
 */
function soccer_team_init()
{
    $stpo = soccer_team_get_plugin_options() ;

    if ($stpo['sc_posts'] == 1)
        add_shortcode('soccer_team', 'soccer_team_shortcode') ;

    if ($stpo['sc_widgets'] == 1)
        add_filter('widget_text', 'do_shortcode') ;

    add_action('template_redirect', 'soccer_team_head') ;

    add_action('wp_footer', 'soccer_team_footer') ;
    add_action('wp_footer', 'soccer_team_cloud_carousel_footer') ;

    add_filter('the_content', 'soccer_team_the_content_filter') ;
}

/**
 * soccer_team_admin_menu()
 *
 * Adds admin menu page(s) to the Dashboard.
 *
 * @return null
 */
function soccer_team_admin_menu()
{
    require_once(SOCCER_TEAM_PLUGIN_PATH . '/soccer-team-options.php') ;

    $soccer_team_options_page = add_options_page('Soccer Team', 'Soccer Team ',
        'manage_options', 'soccer_team-options.php', 'soccer_team_options_page') ;
    add_action('admin_footer-'.$soccer_team_options_page, 'soccer_team_options_admin_footer') ;
    add_action('admin_print_scripts-'.$soccer_team_options_page, 'soccer_team_options_print_scripts') ;
    add_action('admin_print_styles-'.$soccer_team_options_page, 'soccer_team_options_print_styles') ;
}

/**
 * soccer_team_admin_init()
 *
 * Init actions for the Dashboard interface.
 *
 * @return null
 */
function soccer_team_admin_init()
{
    register_setting('soccer_team_plugin_options', 'soccer_team_plugin_options') ;
}

/**
 * soccer_team_register_activation_hook()
 *
 * Adds the default options so WordPress options are
 * configured to a default state upon plugin activation.
 *
 * @return null
 */
function soccer_team_register_activation_hook()
{
    add_option('soccer_team_plugin_options', soccer_team_get_plugin_options()) ;
    //add_shortcode('soccer_team', 'soccer_team_shortcode') ;

    //  Add support for short codes in text widgets
    add_filter('widget_text', 'do_shortcode') ;

    //  Register custom post types and taxonomies
    soccer_team_register_post_types() ;
    soccer_team_register_taxonomies() ;

    //  Flush re-write rules to account for new custom post types
    flush_rewrite_rules(true) ;
}

add_shortcode('st-team-roster', array('SoccerTeam', 'TeamRosterSC')) ;
add_shortcode('st-team-profile', array('SoccerTeam', 'TeamProfileSC')) ;
add_shortcode('st-player-profile', array('SoccerTeam', 'PlayerProfileSC')) ;
add_shortcode('st-players-gallery', array('SoccerTeam', 'PlayersGallerySC')) ;
add_shortcode('st-rosters', array('SoccerTeam', 'RostersSC')) ;
add_shortcode('st-positions', array('SoccerTeam', 'PositionsSC')) ;

/**
 * Returns the default options for CASL.
 *
 * @since soccer-team 1.0
 */
function soccer_team_get_default_plugin_options()
{
	$default_plugin_options = array(
        'player_image_height' => 350
       ,'player_image_width' => 200
       ,'qr_code_team' => 0
       ,'qr_code_player' => 0
       ,'qr_code_width' => 100
       ,'qr_code_height' => 100
       ,'qr_code_quality' => 'H'
       ,'qr_code_border' => 2
       ,'qr_code_use_googl' => 1
       ,'sc_posts' => 1
       ,'sc_widgets' => 1
       ,'default_css' => 1
       ,'custom_css' => 0
       ,'custom_css_styles' => ''
       ,'donation_message' => 0
       ,'profile_email_request' => get_bloginfo('admin_email')
	) ;

    //  Get Player Profile Optional Fields
    $player_optional_fields = soccer_team_player_profile_meta_box_content() ;

    foreach ($player_optional_fields['fields'] as $pof)
        $default_plugin_options[$pof['id']] = $pof['required'] ? 1 : 0 ;

    //  Get Team Profile Optional Fields
    $team_optional_fields = soccer_team_team_profile_meta_box_content() ;

    foreach ($team_optional_fields['fields'] as $tof)
        $default_plugin_options[$tof['id']] = $tof['required'] ? 1 : 0 ;

	return apply_filters('soccer_team_default_plugin_options', $default_plugin_options) ;
}

/**
 * Returns the options array for the Soccer Team plugin.
 *
 * @since soccer-team 1.0
 */
function soccer_team_get_plugin_options()
{
    //  Get the default options in case anything new has been added
    $default_options = soccer_team_get_default_plugin_options() ;

    if (get_option('soccer_team_plugin_options') === false)
        return $default_options ;

    //  One of the issues with simply merging the defaults is that by
    //  using checkboxes (which is the correct UI decision) WordPress does
    //  not save anything for the fields which are unchecked which then
    //  causes wp_parse_args() to incorrectly pick up the defaults.
    //  Since the array keys are used to build the form, we need for them
    //  to "exist" so if they don't, they are created and set to null.

    $plugin_options = get_option('soccer_team_plugin_options', $default_options) ;

    foreach ($default_options as $key => $value)
    {
        if (!array_key_exists($key, $plugin_options))
            $plugin_options[$key] = $default_options[$key] ;
    }

    return $plugin_options ;
}

/**
 * SoccerTeam class definition
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @access public
 * @see wp_remote_get()
 * @see RenderSoccerTeam()
 * @see ConstructSoccerTeam()
 */
class SoccerTeam
{
    /**
     * Constructor
     */
    function SoccerTeam()
    {
        // empty for now
    }

    /**
     * Positions Shortcode
     *
     * @param $options array Values passed from the shortcode.
     * @return An HTML string if successful, false otherwise.
     *
     * @see PositionsSC
     */
    function ConstructPositions($options)
    {
        $args = array(
            'taxonomy' => SOCCER_TEAM_TAX_POSITION,
            'parent' => 0,
            'hide_empty' => 0,
        );

        $terms = get_terms(SOCCER_TEAM_TAX_POSITION, $args);

       
        if (count($terms) > 0)
        {
            $html = '<ul class="st-' . SOCCER_TEAM_TAX_SLUG_POSITION . 'archive">' ; 

            foreach ($terms as $term)
            {
                $html .= '<li class="st-' . SOCCER_TEAM_TAX_SLUG_POSITION . 'archive"><h2>
                    <a href="/' . SOCCER_TEAM_TAX_SLUG_POSITION . '/' . $term->slug .
                    '" title="' . sprintf('View Position:  %s', $term->name) .  '">' . $term->name .
                    '</a></h2></li>';
            }
            $html .= '</ul>' ;
        }
        else
        {
            $html = '<h2 class="st-' . SOCCER_TEAM_TAX_SLUG_POSITION . 'archive">No positions found.</h2>' ;
        }

        return $html ;
    }

    /**
     * WordPress Shortcode handler for the Positions Short Code
     *
     * @return HTML
     */
    function PositionsSC($atts) {
        $params = shortcode_atts(array(
            /*
            'option1'   => true,              // Option 1
            'option2'   => false,             // Option 2
            'option3'   => 'on',              // Option 3
            'option4'   => 'off',             // Option 4
            'option5'   => null,              // Option 5
            */
        ), $atts) ;

        return self::ConstructPositions($params) ;
    }

    /**
     * Rosters Shortcode
     *
     * @param $options array Values passed from the shortcode.
     * @return An HTML string if successful, false otherwise.
     *
     * @see RostersSC
     */
    function ConstructRosters($options)
    {
        $args = array(
            'taxonomy' => SOCCER_TEAM_TAX_ROSTER,
            'parent' => 0,
            'hide_empty' => 0,
        );

        $terms = get_terms(SOCCER_TEAM_TAX_ROSTER, $args);

       
        if (count($terms) > 0)
        {
            $html = '<ul class="st-' . SOCCER_TEAM_TAX_SLUG_ROSTER . 'archive">' ; 

            foreach ($terms as $term)
            {
                $html .= '<li class="st-' . SOCCER_TEAM_TAX_SLUG_ROSTER . 'archive"><h2>
                    <a href="/' . SOCCER_TEAM_TAX_SLUG_ROSTER . '/' . $term->slug .
                    '" title="' . sprintf('View Roster:  %s', $term->name) .  '">' . $term->name .
                    '</a></h2></li>';
            }
            $html .= '</ul>' ;
        }
        else
        {
            $html = '<h2 class="st-' . SOCCER_TEAM_TAX_SLUG_ROSTER . 'archive">No rosters found.</h2>' ;
        }

        return $html ;
    }

    /**
     * WordPress Shortcode handler for the Rosters Short Code
     *
     * @return HTML
     */
    function RostersSC($atts) {
        $params = shortcode_atts(array(
            /*
            'option1'   => true,              // Option 1
            'option2'   => false,             // Option 2
            'option3'   => 'on',              // Option 3
            'option4'   => 'off',             // Option 4
            'option5'   => null,              // Option 5
            */
        ), $atts) ;

        return self::ConstructRosters($params) ;
    }

   /**
     * Build the Team Roster
     *
     * @param $options array Values passed from the shortcode.
     * @return An HTML string if successful, false otherwise.
     *
     * @see TeamRosterSC
     */
    function ConstructTeamRoster($options)
    {
        $html = soccer_team_players_roster_shortcode($options['sortby'] == 'name') ;

        return $html ;
    }

    /**
     * WordPress Shortcode handler for the Team Roster
     *
     * @return HTML
     */
    function TeamRosterSC($atts) {
        $params = shortcode_atts(array(
            'sortby'   => 'number',
        ), $atts) ;

        return self::ConstructTeamRoster($params) ;
    }

    /**
     * Build the Team Profile
     *
     * @param $options array Values passed from the shortcode.
     * @return An HTML string if successful, false otherwise.
     *
     * @see TeamProfileSC
     */
    function ConstructTeamProfile($options)
    {
        $html = '<h1>Team Profile Goes Here!!!</h1>' ;

        return $html ;
    }

    /**
     * WordPress Shortcode handler for the Team Profile
     *
     * @return HTML
     */
    function TeamProfileSC($atts) {
        $params = shortcode_atts(array(
            'option1'   => true,              // Option 1
            'option2'   => false,             // Option 2
            'option3'   => 'on',              // Option 3
            'option4'   => 'off',             // Option 4
            'option5'   => null,              // Option 5
        ), $atts) ;

        return self::ConstructTeamProfile($params) ;
    }

    /**
     * Build the Player Profile
     *
     * @param $options array Values passed from the shortcode.
     * @return An HTML string if successful, false otherwise.
     *
     * @see PlayerProfileSC
     */
    function ConstructPlayerProfile($options)
    {
        $html = '<h1>Player Profile Goes Here!!!</h1>' ;

        return $html ;
    }

    /**
     * WordPress Shortcode handler for Player Profile.
     *
     * @return HTML
     */
    function PlayerProfileSC($atts) {
        $params = shortcode_atts(array(
            'option1'   => true,              // Option 1
            'option2'   => false,             // Option 2
            'option3'   => 'on',              // Option 3
            'option4'   => 'off',             // Option 4
            'option5'   => null,              // Option 5
        ), $atts) ;

        return self::ConstructPlayerProfile($params) ;
    }

   /**
     * Build the Players Gallery
     *
     * @param $options array Values passed from the shortcode.
     * @return An HTML string if successful, false otherwise.
     *
     * @see PlayersGallerySC
     */
    function ConstructPlayersGallery($options)
    {
        return soccer_team_players_gallery_shortcode($options) ;
    }

    /**
     * WordPress Shortcode handler for the Players Gallery
     *
     * @return HTML
     */
    function PlayersGallerySC($atts) {
        $params = shortcode_atts(array(
            'click'  => 'lightbox'
        ), $atts) ;

        //  Validate 'click' attribute ...
        if (!in_array($atts['click'], array('lightbox', 'profile')))
            return '<div class="soccer-team-error">Invalid shortcode syntax.</div>' ;

        //  Build the shortcode content and return it
        return self::ConstructPlayersGallery($params) ;
    }
}

/**
 * the_content filter for the SOCCER_TEAM_CPT_PLAYER custom post type
 *
 * Prefix the SOCCER_TEAM_CPT_PLAYER and SOCCER_TEAM_CPT_TEAM CPT detail
 * to the content of a post.
 *
 * @param $content string post content
 * @return $content string post content prefixed with CPT detail
 * @uses soccer_team_player_custom_fields()
 */
function soccer_team_the_content_filter($content)
{
    $cpt = array(SOCCER_TEAM_CPT_TEAM, SOCCER_TEAM_CPT_PLAYER) ;

    //  If the content is for a Soccer Team CPT, then add the data
    if (in_array(get_post_type(), $cpt))
    {
        if (get_post_type() == SOCCER_TEAM_CPT_PLAYER)
        {
            //  Get custom fields
            if (is_single())
                $content = soccer_team_player_custom_fields(get_the_ID(), 'full') ;
            elseif (is_tax())
                $content = soccer_team_player_custom_fields(get_the_ID(), 'brief') ;
            else
                $content = '' ;
        }

        //  If the content is for a SOCCER_TEAM_CPT_TEAM CPT, then add the data
        elseif (get_post_type() == SOCCER_TEAM_CPT_TEAM)
        {
            //  Get custom fields
            if (is_single())
                $content = soccer_team_team_custom_fields(get_the_ID(), 'full') ;
            elseif (is_tax())
                $content = soccer_team_team_custom_fields(get_the_ID(), 'brief') ;
            else
                $content = '' ;
        }
        else
        {
            $content = '' ;
        }
    }

    return $content ;
}

/**
 * soccer_team_head()
 *
 * WordPress header actions
 */
function soccer_team_head()
{
    $stpo = soccer_team_get_plugin_options() ;

    //  Load default Soccer-Team CSS?
    if ($stpo['default_css'] == 1)
    {
        wp_enqueue_style('soccer-team',
            plugins_url(plugin_basename(dirname(__FILE__) . '/css/soccer-team.css'))) ;
    }

    soccer_team_carousel_head() ;

    //  Load the jQuery Validate from the Microsoft CDN, it isn't
    //  available from the Google CDN or I'd load it from there!
    //wp_register_script('jquery-validate',
    //    'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js',
    //    array('jquery'), false, true) ;
    //wp_enqueue_script('jquery-validate') ;
}

/**
 * soccer_team_footer()
 *
 * WordPress footer actions
 */
function soccer_team_footer()
{
    //  Output custom CSS?

    $stpo = soccer_team_get_plugin_options() ;

    if ($stpo['custom_css'] == 1)
    {
        $css = '<style>' . PHP_EOL . $stpo['custom_css_styles'] . PHP_EOL . '</style>' ;
        echo $css ;
    }
}

/*
add_action( 'request', 'soccer_team_request') ;
function soccer_team_request( $query_vars ) {
    print_r($query_vars) ;
	return $query_vars;
}
*/

/**
 * soccer_team_add_body_class()
 *
 * WordPress body_class filter
 */
function soccer_team_add_body_class($class)
{
	if (! is_tax()) return $class ;

	$tax = get_query_var('taxonomy') ;
	$term = $tax . '-' . get_query_var('term') ;
    $class = array_merge($class,
        array('taxonomy-archive', 'st-' . $tax, 'st-' . $term)) ;

	return $class;
}
add_filter('body_class', 'soccer_team_add_body_class' ) ;

function soccer_team_connection_types() {
	// Make sure the Posts 2 Posts plugin is active.
	if ( !function_exists( 'p2p_register_connection_type' ) )
		return;

    /*
	p2p_register_connection_type( array(
		'name' => SOCCER_TEAM_CPT_PLAYER . '_to_' . SOCCER_TEAM_CPT_TEAM,
		'from' => SOCCER_TEAM_CPT_PLAYER,
		'to' => SOCCER_TEAM_CPT_TEAM
	) );
     */

	p2p_register_connection_type( array(
		'name' => SOCCER_TEAM_CPT_TEAM . '_to_' . SOCCER_TEAM_CPT_PLAYER,
		'from' => SOCCER_TEAM_CPT_TEAM,
		'to' => SOCCER_TEAM_CPT_PLAYER
	) );
}
add_action( 'wp_loaded', 'soccer_team_connection_types' );
?>
