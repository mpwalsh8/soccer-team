<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 *
 * post-type-extensions.php template file
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package Soccer-Team
 * @subpackage post-types
 * @version $Revision$
 * @lastmodified $Author$
 * @lastmodifiedby $Date$
 *
 */

// Soccer Team Plugin 'Team' Custom Post Type
define('SOCCER_TEAM_CPT_TEAM', 'team') ;
define('SOCCER_TEAM_CPT_QV_TEAM', SOCCER_TEAM_CPT_TEAM . '_qv') ;
define('SOCCER_TEAM_CPT_SLUG_TEAM', SOCCER_TEAM_CPT_TEAM . 's') ;

// Soccer Team Plugin 'Player' Custom Post Type
define('SOCCER_TEAM_CPT_PLAYER', 'player') ;
define('SOCCER_TEAM_CPT_QV_PLAYER', SOCCER_TEAM_CPT_PLAYER . '_qv') ;
define('SOCCER_TEAM_CPT_SLUG_PLAYER', SOCCER_TEAM_CPT_PLAYER . 's') ;

// Soccer Team Plugin 'Player' Taxonpmy
define('SOCCER_TEAM_TAX_POSITION', 'position') ;
define('SOCCER_TEAM_TAX_QV_POSITION', SOCCER_TEAM_TAX_POSITION . '_qv') ;
define('SOCCER_TEAM_TAX_SLUG_POSITION', SOCCER_TEAM_TAX_POSITION . 's') ;

// Soccer Team Plugin 'Roster' Taxonpmy
define('SOCCER_TEAM_TAX_ROSTER', 'roster') ;
define('SOCCER_TEAM_TAX_QV_ROSTER', SOCCER_TEAM_TAX_ROSTER . '_qv') ;
define('SOCCER_TEAM_TAX_SLUG_ROSTER', SOCCER_TEAM_TAX_ROSTER . 's') ;

// Google's "goo.gl" Short URL API
define('SOCCER_TEAM_GOOGL_API', 'https://www.googleapis.com/urlshortener/v1/url') ;

/** Set up the post type(s) */
add_action('init', 'soccer_team_register_post_types') ;
add_action('init', 'soccer_team_register_taxonomies') ;

/** Register post type(s) */
function soccer_team_register_post_types()
{
    /** Set up the arguments for the SOCCER_TEAM_CPT_TEAM post type. */
    $team_args = array(
        'public' => true,
        'query_var' => SOCCER_TEAM_CPT_QV_TEAM,
        'has_archive' => true,
        'rewrite' => array(
            'slug' => SOCCER_TEAM_CPT_SLUG_TEAM,
            'with_front' => false,
        ),
        'supports' => array(
            'title',
            'thumbnail',
            'editor',
            'exceprt'
        ),
        'labels' => array(
            'name' => 'Soccer Teams',
            'singular_name' => 'Soccer Team',
            'add_new' => 'Add New Soccer Team',
            'add_new_item' => 'Add New Soccer Team',
            'edit_item' => 'Edit Soccer Team',
            'new_item' => 'New Soccer Team',
            'view_item' => 'View Soccer Team',
            'search_items' => 'Search Soccer Teams',
            'not_found' => 'No Soccer Teams Found',
            'not_found_in_trash' => 'No Soccer Teams Found In Trash'
        ),
        'menu_icon' => plugins_url('/images/DashboardMenu.png', __FILE__)
    );

    // Register the soccer_team player post type
    register_post_type(SOCCER_TEAM_CPT_TEAM, $team_args) ;

    /** Set up the arguments for the SOCCER_TEAM_CPT_PLAYER post type. */
    $player_args = array(
        'public' => true,
        'query_var' => SOCCER_TEAM_CPT_QV_PLAYER,
        'has_archive' => true,
        'rewrite' => array(
            'slug' => SOCCER_TEAM_CPT_SLUG_PLAYER,
            'with_front' => false,
        ),
        'supports' => array(
            'title',
            'thumbnail',
            'editor',
            'exceprt'
        ),
        'labels' => array(
            'name' => 'Soccer Players',
            'singular_name' => 'Soccer Player',
            'add_new' => 'Add New Soccer Player',
            'add_new_item' => 'Add New Soccer Player',
            'edit_item' => 'Edit Soccer Player',
            'new_item' => 'New Soccer Player',
            'view_item' => 'View Soccer Player',
            'search_items' => 'Search Soccer Players',
            'not_found' => 'No Soccer Players Found',
            'not_found_in_trash' => 'No Soccer Players Found In Trash'
        ),
        'menu_icon' => plugins_url('/images/DashboardMenu.png', __FILE__)
    );

    // Register the soccer_team player post type
    register_post_type(SOCCER_TEAM_CPT_PLAYER, $player_args) ;
}

/** Register taxonomies */
function soccer_team_register_taxonomies()
{
    /** Set up the arguments for the 'position' post type. */
    $position_args = array(
        'hierarchical' => true,
        'query_var' => SOCCER_TEAM_TAX_QV_POSITION,
        'show_tagcloud' => true,
        'rewrite' => array(
            'slug' => SOCCER_TEAM_TAX_SLUG_POSITION,
            'with_front' => false,
            'hierarchical' => true,
        ),
        'labels' => array(
            'name' => 'Positions',
            'singular_name' => 'Position',
            'edit_item' => 'Edit Position',
            'update_item' => 'Update Position',
            'add_new_item' => 'Add New Position',
            'new_item_name' => 'New Position Name',
            'all_items' => 'All Positions',
            'search_items' => 'Search Positions',
            'parent_item' => 'Parent Position',
            'parent_item_colon' => 'Parent Position:',
        ),
    );

    // Register the soccer_team player post type
    register_taxonomy(SOCCER_TEAM_TAX_POSITION, array(SOCCER_TEAM_CPT_PLAYER), $position_args) ;

    /** Set up the arguments for the 'roster' post type. */
    $roster_args = array(
        'hierarchical' => true,
        'query_var' => SOCCER_TEAM_TAX_QV_ROSTER,
        'show_tagcloud' => true,
        'rewrite' => array(
            'slug' => SOCCER_TEAM_TAX_SLUG_ROSTER,
            'with_front' => false,
            'hierarchical' => true,
        ),
        'labels' => array(
            'name' => 'Rosters',
            'singular_name' => 'Roster',
            'edit_item' => 'Edit Roster',
            'update_item' => 'Update Roster',
            'add_new_item' => 'Add New Roster',
            'new_item_name' => 'New Roster Name',
            'all_items' => 'All Rosters',
            'search_items' => 'Search Rosters',
            'parent_item' => 'Parent Roster',
            'parent_item_colon' => 'Parent Roster:',
        ),
    );

    // Register the soccer_team team post type
    register_taxonomy(SOCCER_TEAM_TAX_ROSTER, array(SOCCER_TEAM_CPT_TEAM, SOCCER_TEAM_CPT_PLAYER), $roster_args) ;
}

/** Set up the post type image size(s) */
add_action('init', 'soccer_team_register_image_sizes') ;

function soccer_team_register_image_sizes()
{
    $stpo = soccer_team_get_plugin_options() ;

    $team_width = &$stpo['team_image_width'] ;
    $team_height = &$stpo['team_image_height'] ;

    $player_width = &$stpo['player_image_width'] ;
    $player_height = &$stpo['player_image_height'] ;

    //  Add image sizes for player CPT - the image size can
    //  be set in the plugin options - the headshot size is
    //  set and others are derived from it.

    if (function_exists('add_image_size'))
    { 
        add_image_size('player-headshot',
            $player_width, $player_height, true ); //(cropped)
        add_image_size('player-gallery',
            ceil($player_width / 2), ceil($player_height / 2), true ); //(cropped)
        add_image_size('player-thumbnail',
            ceil($player_width / 3), ceil($player_height / 3), true ); //(cropped)
        add_image_size('team-photo',
            $team_width, $team_height, true ); //(cropped)
        add_image_size('team-thumbnail',
            ceil($team_width / 3), ceil($team_height / 3), true ); //(cropped)
    }
}

//  Build custom meta box support

/**
 * Define the Player Meta Box fields so they can be used
 * to construct the form as well as validate it and save it.
 *
 */
function soccer_team_player_profile_meta_box_content()
{
    return array(
        'id' => 'player-profile-meta-box',
        'title' => 'Player Profile Details',
        'page' => SOCCER_TEAM_CPT_PLAYER,
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            array(
                'name' => 'Number',
                'desc' => 'Player\'s Jersey Number',
                'id' => ST_PREFIX . 'player_jersey_number',
                'type' => 'smtext',
                'std' => '',
                'required' => true
            ),
            array(
                'name' => 'Address',
                'desc' => 'Mailing Address - include City, State, and Zip Code',
                'id' => ST_PREFIX . 'player_address',
                'type' => 'textarea',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Phone',
                'desc' => 'Player\'s Primary Phone Number',
                'id' => ST_PREFIX . 'player_phone',
                'type' => 'smtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'E-mail',
                'desc' => 'Player\'s Primary E-mail Address',
                'id' => ST_PREFIX . 'player_email',
                'type' => 'medtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'DOB',
                'desc' => 'Player\'s Date of Birth - use Month Day, Year format',
                'id' => ST_PREFIX . 'player_dob',
                'type' => 'smtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Mother',
                'desc' => 'Mother\'s Name and E-mail Address',
                'id' => ST_PREFIX . 'player_mother',
                'type' => 'lgtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Father',
                'desc' => 'Father\'s Name and E-mail Address',
                'id' => ST_PREFIX . 'player_father',
                'type' => 'lgtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Height',
                'desc' => 'Player\'s Height',
                'id' => ST_PREFIX . 'player_height',
                'type' => 'smtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Weight',
                'desc' => 'Player\'s Weight',
                'id' => ST_PREFIX . 'player_weight',
                'type' => 'smtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Foot',
                'desc' => 'Player\'s Primary Foot',
                'id' => ST_PREFIX . 'player_foot',
                'type' => 'select',
                'options' => array('Right', 'Left'),
                'required' => true
            ),
            array(
                'name' => 'School',
                'desc' => 'Player\'s School',
                'id' => ST_PREFIX . 'player_school',
                'type' => 'lgtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Graduation Year',
                'desc' => 'Player\'s Graduation Year',
                'id' => ST_PREFIX . 'player_grad_year',
                'type' => 'smtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'GPA',
                'desc' => 'Grade Point Average',
                'id' => ST_PREFIX . 'player_gpa',
                'type' => 'smtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Privacy',
                'desc' => 'Privacy Controls',
                'id' => ST_PREFIX . 'player_privacy',
                'type' => 'select',
                'options' => array('Public', 'Private'),
                'required' => true
            ),
            array(
                'name' => 'Status',
                'desc' => 'Player Status',
                'id' => ST_PREFIX . 'player_status',
                'type' => 'select',
                'options' => array('Active', 'Inactive'),
                'required' => true
            ),
            array(
                'name' => 'Got Soccer Profile',
                'desc' => 'Player\'s Got-Soccer.com Profile URL',
                'id' => ST_PREFIX . 'player_gs_profile',
                'type' => 'lgtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'SIC Profile',
                'desc' => 'Player\'s Soccer-In-College.com Profile URL',
                'id' => ST_PREFIX . 'player_sic_profile',
                'type' => 'lgtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Downloadable Profile',
                'desc' => 'Player\'s Downloadable Profile',
                'id' => ST_PREFIX . 'player_downloadable_profile',
                'type' => 'hidden',
                'required' => false
            ),
            array(
                'name' => 'Player Profile Short URL',
                'desc' => 'Player\'s Profile Short URL',
                'id' => '_' . ST_PREFIX . 'player_profile_short_url',
                'type' => 'hidden',
                'required' => true
            ),
        )
    ) ;
}

/**
 * Define the Team Meta Box fields so they can be used
 * to construct the form as well as validate it and save it.
 *
 */
function soccer_team_team_profile_meta_box_content()
{
    return array(
        'id' => 'team-profile-meta-box',
        'title' => 'Team Profile Details',
        'page' => SOCCER_TEAM_CPT_TEAM,
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            array(
                'name' => 'Head Coach\'s Name',
                'desc' => 'Head Coach\'s Name',
                'id' => ST_PREFIX . 'team_head_coach_name',
                'type' => 'medtext',
                'std' => '',
                'required' => true
            ),
            array(
                'name' => 'Head Coach E-mail',
                'desc' => 'Head Coach\'s E-mail Address',
                'id' => ST_PREFIX . 'team_head_coach_email',
                'type' => 'medtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Other Coach\'s Name',
                'desc' => 'Other Coach\'s Name',
                'id' => ST_PREFIX . 'team_other_coach_name',
                'type' => 'medtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Other Coach\'s E-Mail Address',
                'desc' => 'Other Coach\'s E-mail Address',
                'id' => ST_PREFIX . 'team_other_coach_email',
                'type' => 'medtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Manager\'s Name',
                'desc' => 'Manager\'s Name',
                'id' => ST_PREFIX . 'team_manager_name',
                'type' => 'medtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Manager\'s E-mail Address',
                'desc' => 'Manager\'s E-mail Address',
                'id' => ST_PREFIX . 'team_manager_email',
                'type' => 'medtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Got Soccer Profile',
                'desc' => 'Team\'s Got-Soccer.com Profile URL',
                'id' => ST_PREFIX . 'team_gs_profile',
                'type' => 'lgtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'SIC Profile',
                'desc' => 'Team\'s Soccer-In-College.com Profile URL',
                'id' => ST_PREFIX . 'team_sic_profile',
                'type' => 'lgtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Downloadable Profile',
                'desc' => 'Team\'s Downloadable Profile',
                'id' => ST_PREFIX . 'team_downloadable_profile',
                'type' => 'hidden',
                'required' => false
            ),
            array(
                'name' => 'Address',
                'desc' => 'Team Mailing Address - include City, State, and Zip Code',
                'id' => ST_PREFIX . 'team_address',
                'type' => 'textarea',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Team Phone',
                'desc' => 'Team\'s Primary Phone Number',
                'id' => ST_PREFIX . 'team_phone',
                'type' => 'smtext',
                'std' => '',
                'required' => false
            ),
            array(
                'name' => 'Privacy',
                'desc' => 'Privacy Controls',
                'id' => ST_PREFIX . 'team_privacy',
                'type' => 'select',
                'options' => array('Public', 'Private'),
                'required' => true
            ),
            array(
                'name' => 'Status',
                'desc' => 'Team Status',
                'id' => ST_PREFIX . 'team_status',
                'type' => 'select',
                'options' => array('Active', 'Inactive'),
                'required' => true
            ),
            array(
                'name' => 'Team Profile Short URL',
                'desc' => 'Team\'s Profile Short URL',
                'id' => '_' . ST_PREFIX . 'team_profile_short_url',
                'type' => 'hidden',
                'required' => true
            ),
        )
    ) ;
}

add_action('admin_menu', 'soccer_team_add_team_profile_meta_box') ;
add_action('admin_menu', 'soccer_team_add_player_profile_meta_box') ;

// Add team profile meta box
function soccer_team_add_team_profile_meta_box()
{
    $mb = soccer_team_team_profile_meta_box_content() ;

    add_meta_box($mb['id'], $mb['title'],
        'soccer_team_show_team_profile_meta_box', $mb['page'], $mb['context'], $mb['priority']);
}

// Add player profile meta box
function soccer_team_add_player_profile_meta_box()
{
    $mb = soccer_team_player_profile_meta_box_content() ;

    add_meta_box($mb['id'], $mb['title'],
        'soccer_team_show_player_profile_meta_box', $mb['page'], $mb['context'], $mb['priority']);
}

// Callback function to show fields in meta box
function soccer_team_show_team_profile_meta_box()
{
    $mb = soccer_team_team_profile_meta_box_content() ;
    soccer_team_show_profile_meta_box($mb) ;
}

function soccer_team_show_player_profile_meta_box()
{
    $mb = soccer_team_player_profile_meta_box_content() ;
    soccer_team_show_profile_meta_box($mb) ;
}

function soccer_team_show_profile_meta_box($mb)
{
    global $post;

    $stpo = soccer_team_get_plugin_options() ;

    // Use nonce for verification
    echo '<input type="hidden" name="' . ST_PREFIX .
        'meta_box_nonce" value="', wp_create_nonce(plugin_basename(__FILE__)), '" />';

    echo '<table class="form-table">';

    foreach ($mb['fields'] as $field)
    {
        //  Only show the fields which are enabled
        if ($stpo[$field['id']] && $field['type'] !== 'hidden')
        {
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);
    
            echo '<tr>',
                    '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                    '<td>';
            switch ($field['type']) {
                case 'text':
                case 'lgtext':
                    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                    break;
                case 'medtext':
                    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:47%" />', '<br />', $field['desc'];
                    break;
                case 'smtext':
                    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:27%" />', '<br />', $field['desc'];
                    break;
                case 'textarea':
                    echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                    break;
                case 'select':
                    echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                    foreach ($field['options'] as $option) {
                        echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                    }
                    echo '</select>';
                    break;
                case 'radio':
                    foreach ($field['options'] as $option) {
                        echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                    }
                    break;
                case 'checkbox':
                    echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                    break;
                default :
                    break ;
            }
            echo     '<td>',
                '</tr>';
        }
    }

    echo '</table>';
}

    //soccer_team_whereami(__FILE__, __LINE__) ;
add_action( 'quick_edit_custom_box', 'soccer_team_add_quick_edit_nonce', 10, 2 );
//add_action( 'quick_edit_custom_box', function() { error_log(__LINE__) ; }, 10, 2 );
    //soccer_team_whereami(__FILE__, __LINE__) ;
/**
 * Action to add a nonce to the quick edit form for the custom post types
 *
 */
function soccer_team_add_quick_edit_nonce($column_name, $post_type)
{
    error_log(__LINE__) ;
    soccer_team_whereami(__FILE__, __LINE__) ;
    static $printNonce = true ;

    if (($post_type == SOCCER_TEAM_CPT_TEAM) || ($post_type == SOCCER_TEAM_CPT_PLAYER))
    {
        if ($printNonce)
        {
            $printNonce = false ;
            wp_nonce_field( plugin_basename( __FILE__ ), ST_PREFIX . 'meta_box_qe_nonce' ) ;
        }
    }
}

add_action('save_post', 'soccer_team_save_meta_box_data');
/**
 * Action to save Soccer Team meta box data for both
 * team and player Custom Post Types.
 *
 */
function soccer_team_save_meta_box_data($post_id)
{
    global $post ;

    // verify nonce - needs to come from either a CPT Edit screen or CPT Quick Edit

    if ((isset( $_POST[ST_PREFIX . 'meta_box_nonce']) &&
        wp_verify_nonce($_POST[ST_PREFIX . 'meta_box_nonce'], plugin_basename(__FILE__))) ||
        (isset( $_POST[ST_PREFIX . 'meta_box_qe_nonce']) &&
        wp_verify_nonce($_POST[ST_PREFIX . 'meta_box_qe_nonce'], plugin_basename(__FILE__))))
    {
    //soccer_team_whereami(__FILE__, __LINE__) ;
        // check for autosave - if autosave, simply return

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        {
            return $post_id ;
        }

        // check permissions - make sure action is allowed to be performed

        if ('page' == $_POST['post_type'])
        {
            if (!current_user_can('edit_page', $post_id))
            {
                return $post_id ;
            }
        }
        elseif (!current_user_can('edit_post', $post_id))
        {
            return $post_id ;
        }

        //  Get the meta box fields for the appropriate CPT and
        //  return if the post isn't a CPT which shouldn't happen

        if (get_post_type($post_id) == SOCCER_TEAM_CPT_TEAM)
            $mb = soccer_team_team_profile_meta_box_content() ;
        elseif (get_post_type($post_id) == SOCCER_TEAM_CPT_PLAYER)
            $mb = soccer_team_player_profile_meta_box_content() ;
        else
            return $post_id ;

        //soccer_team_preprint_r($mb) ;
        //  Loop through all of the fields and update what has changed
        //  accounting for the fact that Short URL fields are always
        //  updated and CPT fields are ignored in Quick Edit except for
        //  the Short URL field.

        foreach ($mb['fields'] as $field)
        {
            //if ($field['required'])
            //{
                //  Always update the Short URL Post Meta field
                if (($field['id'] == '_' . ST_PREFIX . 'team_profile_short_url') ||
                    ($field['id'] == '_' . ST_PREFIX . 'player_profile_short_url'))
                {
                    update_post_meta($post_id, $field['id'], st_get_googl_short_url($post_id)) ;
                }

                //  Only update other Post Meta fields when on the edit screen - ignore in quick edit mode

                elseif (isset($_POST[ST_PREFIX . 'meta_box_nonce']))
                {
                    error_log('+++++++++++++++++++++++') ;
                    error_log(print_r($field['id'], true)) ;
                    error_log('-----------------------') ;
                    $new = $_POST[$field['id']];
    
                    $old = get_post_meta($post_id, $field['id'], true) ;

                    if ($new && $new != $old)
                    {
                        update_post_meta($post_id, $field['id'], $new) ;
                    }
                    elseif ('' == $new && $old)
                    {
                        delete_post_meta($post_id, $field['id'], $old) ;
                    }
                }
                else
                    error_log('bad nonce') ;
            //}
            //else
                //error_log(sprintf('Not Required:  %s', print_r($field, true))) ;
        }
    }
    else
    {
    //soccer_team_whereami(__FILE__, __LINE__) ;
        return $post_id ;
    }
}

/**
 * Define the meta boxes for the Profile Upload attachment
 *
 * @see http://wp.tutsplus.com/tutorials/attaching-files-to-your-posts-using-wordpress-custom-meta-boxes-part-1/
 * @return void
 */
function soccer_team_add_profile_attachment_meta_boxes()
{
    // Define the custom attachment for SOCCER_TEAM_CPT_TEAM custom post types
    add_meta_box( 
        'soccer_team_profile_attachment',
        'Downloadable Team Profile',
        'soccer_team_profile_attachment',
        SOCCER_TEAM_CPT_TEAM,
        'side' 
    ) ;
  
    // Define the custom attachment for SOCCER_TEAM_CPT_PLAYER custom post types  
    add_meta_box(  
        'soccer_team_profile_attachment',  
        'Downloadable Player Profile',  
        'soccer_team_profile_attachment',  
        SOCCER_TEAM_CPT_PLAYER,  
        'side'  
    ) ;
}

/**
 * Form Content for the Profile Attachment upload
 *
 * @see http://wp.tutsplus.com/tutorials/attaching-files-to-your-posts-using-wordpress-custom-meta-boxes-part-1/
 */
function soccer_team_profile_attachment()
{  
    global $post ;

    $html = '' ;
    $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;

    wp_nonce_field(plugin_basename(__FILE__), ST_PREFIX . 'profile_attachment_nonce');  
  
    $pt = get_post_type($post) ;

    $stpo = soccer_team_get_plugin_options() ;
    $tp = &$stpo[ST_PREFIX . 'team_downloadable_profile'] ;
    $pp = &$stpo[ST_PREFIX . 'player_downloadable_profile'] ;

    //  Should the attachment meta box be displayed?
    //  If the setting isn't enabled, show the meta box with a message in it.

    if ((($pt == SOCCER_TEAM_CPT_TEAM) && (int)$tp === 1) ||
        (($pt == SOCCER_TEAM_CPT_PLAYER) && (int)$pp === 1))
    {
        $html .= '<p class="description">Upload your PDF here.';  
        $html .= '<div><input type="file" id="soccer_team_profile_attachment" name="soccer_team_profile_attachment" value="' . get_the_ID() . '" size="20"><img style="float: right;" src=' . $pdficon . '></div>' ;  
  
        //  Has a player profile already been uploaded?
        $pp = get_post_meta(get_the_ID(), 'soccer_team_profile_attachment', true) ;

        if (!empty($pp))
        {
            $html .= '<br /><b>Note:</b>  A <b><i>' . ucwords($pt) . ' Profile</i></b> has already been uploaded.  Uploading a new profile will replace the existing profile.  This action cannot be undone.  Click <a href="' . $pp['url'] . '"><b>here</b></a> to view the current profile.' ;
            $html .= '<br /><br /><input type="checkbox" id="soccer_team_profile_attachment_remove"  name="soccer_team_profile_attachment_remove" value="remove" />&nbsp;Remove existing profile?' ;
        }
        $html .= '</p>' ;
    }
    else
    {
        $html .= '<p class="description">Profile attachments are not enabled.</p>' ;
    }
  
    echo $html ;
}

/**
 * Save the Profile Attachment
 *
 * @see http://wp.tutsplus.com/tutorials/attaching-files-to-your-posts-using-wordpress-custom-meta-boxes-part-1/
 */
function soccer_team_save_profile_attachment($id)
{
    //soccer_team_whereami(__FILE__, __LINE__) ;
    //  Make sure we're dealing with a proper post type!
    if (!in_array(get_post_type($id), array(SOCCER_TEAM_CPT_TEAM, SOCCER_TEAM_CPT_PLAYER))) return $id ;

    /* --- security verification --- */  
    if (!wp_verify_nonce($_POST[ST_PREFIX . 'profile_attachment_nonce'], plugin_basename(__FILE__)))
    {  
      return $id;  
    }
  
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    {  
      return $id;  
    }
  
    if ('page' == $_POST['post_type'])
    {  
        if (!current_user_can('edit_page', $id))
        {  
            return $id;  
        }
    }
    else
    {  
        if (!current_user_can('edit_page', $id))
        {  
            return $id;  
        }
    }
    /* - end security verification - */  
  
    // Make sure the file array isn't empty  
    if (!empty($_FILES['soccer_team_profile_attachment']['name']))
    { 
        // Setup the array of supported file types. In this case, it's just PDF.  
        $supported_types = array('application/pdf');  
  
        // Get the file type of the upload  
        $arr_file_type = wp_check_filetype(basename($_FILES['soccer_team_profile_attachment']['name']));  
        $uploaded_type = $arr_file_type['type'];  
  
        // Check if the type is supported. If not, throw an error.  
        if (in_array($uploaded_type, $supported_types))
        {  
            // Use the WordPress API to upload the file  
            $upload = wp_upload_bits($_FILES['soccer_team_profile_attachment']['name'],
                null, file_get_contents($_FILES['soccer_team_profile_attachment']['tmp_name'])) ;
  
            //  Need to fix the path to account for Windows platforms ...
            //  Would be nice if there was a PHP function to do this.

            $upload['file'] = preg_replace('/\\\/', '/', $upload['file']) ;

            if (isset($upload['error']) && $upload['error'] != 0)
            {  
                wp_die('There was an error uploading your file. The error is: ' . $upload['error']) ;
            }
            else
            {  
                //  Clean up an existing profile?
                //  Has a player profile already been uploaded?

                $pp = get_post_meta(get_the_ID(), 'soccer_team_profile_attachment', true) ;

                //  If the player profile attachment already exists, remove it if a new
                //  profile has been supplied OR the remove checkbox has been selected.

                if (!empty($pp))
                {
                    delete_post_meta($id, 'soccer_team_profile_attachment') ;  
                    unlink($pp['file']) ;
                }

                add_post_meta($id, 'soccer_team_profile_attachment', $upload);  
                update_post_meta($id, 'soccer_team_profile_attachment', $upload);  
            }
        }
        else
        {  
            wp_die('The file type that you\'ve uploaded is not a PDF.') ;
        }
    }
    elseif (array_key_exists('soccer_team_profile_attachment_remove', $_POST) &&
        ($_POST['soccer_team_profile_attachment_remove'] === 'remove'))
    {
        //  Clean up an existing profile?  Only if the checkbox was checked!
        //  Has a player profile already been uploaded?
        $pp = get_post_meta(get_the_ID(), 'soccer_team_profile_attachment', true) ;

        //  If the player profile attachment already exists, remove it if a new
        //  profile has been supplied OR the remove checkbox has been selected.

        if (!empty($pp))
        {
            delete_post_meta($id, 'soccer_team_profile_attachment') ;  
            unlink($pp['file']) ;
        }
    }
}

add_action('add_meta_boxes', 'soccer_team_add_profile_attachment_meta_boxes');  
add_action('save_post', 'soccer_team_save_profile_attachment');

function soccer_team_update_edit_form() {  
    echo ' enctype="multipart/form-data"';  
} // end update_edit_form  
add_action('post_edit_form_tag', 'soccer_team_update_edit_form');  


// Add to admin_init function
add_filter('manage_edit-player_columns', 'soccer_team_add_new_player_columns');

/** Add more columns */
function soccer_team_add_new_player_columns($cols)
{
    //  The "Title" column is re-labeled as "Player"!
    $cols['title'] = 'Player' ;

	return array_merge(
		array_slice($cols, 0, 2),
        array(
            ST_PREFIX . 'player_jersey_number' => __('Jersey Number'),
            ST_PREFIX . 'position' => __('Position(s)'),
            ST_PREFIX . 'roster' => __('Roster(s)'),
            //'id' => __(ID)
        ),
        array_slice($cols, 2)
	) ;
}

/**  Retrieve the column content */
add_action('manage_posts_custom_column', 'soccer_team_player_custom_columns', 10, 2) ;

function soccer_team_player_custom_columns($column, $post_id)
{
    switch ($column)
    {
        case ST_PREFIX . 'player_jersey_number':
            echo get_post_meta( $post_id, ST_PREFIX . 'player_jersey_number', true) ;
            break;

        case ST_PREFIX . 'roster':
        case ST_PREFIX . 'position':
            if ($column == ST_PREFIX . 'roster')
                $terms = get_the_terms(get_the_ID(), SOCCER_TEAM_TAX_ROSTER) ;

            if ($column == ST_PREFIX . 'position')
                $terms = get_the_terms(get_the_ID(), SOCCER_TEAM_TAX_POSITION) ;

            if ($terms && ! is_wp_error( $terms ))
            {
                $positions = array();

                foreach ( $terms as $term )
                {
                    $positions[] = $term->name;
                }
		
                $positions = join( ", ", $positions );
            }
            else
            {
                $positions = '&nbsp;' ;
            }

            echo $positions ;
            break;

        case 'id':
            echo $post_id ;
            break ;
    }
}
 
add_filter('manage_edit-player_sortable_columns', 'soccer_team_player_sortable_columns') ;

// Make these columns sortable
function soccer_team_player_sortable_columns()
{
    return array(
        'title' => 'title',
        ST_PREFIX . 'player_jersey_number' => 'player_jersey_number',
    ) ;
}

/**
 * Set up a footer hook to rearrange the post editing screen
 * for the SOCCER_TEAM_CPT_PLAYER custom post type.  The meta box which has all
 * of the custom fields in it will appear before the Visual Editor.
 * This is accomplished using a simple jQuery script once the
 * document is loaded.
 * 
 *
 */
function soccer_team_admin_footer_hook()
{
    global $post ;

    if ((get_post_type($post) == SOCCER_TEAM_CPT_TEAM) ||
        (get_post_type($post) == SOCCER_TEAM_CPT_PLAYER))
    {
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#normal-sortables').insertBefore('#postdivrich') ;
    }) ;
</script>

<?php
    }
}

/**  Hook into the Admin Footer */
add_action('admin_footer','soccer_team_admin_footer_hook');

/**  Filter to change the Title field for the Player post type */
add_filter('enter_title_here', 'soccer_team_enter_title_here_filter') ;

function soccer_team_enter_title_here_filter($title)
{
    global $post ;

    if ((get_post_type($post) == SOCCER_TEAM_CPT_TEAM) ||
        (get_post_type($post) == SOCCER_TEAM_CPT_PLAYER))
        return __('Enter ' . ucwords(get_post_type($post)) . ' Name') ;
    else
        return $title ;
}

/** Filter to show all posts for Position and Roster Taxonomies when viewing an archive */
add_filter('option_posts_per_page', 'soccer_team_option_posts_per_page' );

function soccer_team_option_posts_per_page( $value ) {
    return (!is_admin() && (is_tax(SOCCER_TEAM_TAX_POSITION ) || is_tax(SOCCER_TEAM_TAX_ROSTER))) ? -1 : $value ;
}

/**
 * Create "goo.gl" short URL
 *
 * This code was derived from an blog post on using Google "goo.gl"
 * as a link shortner.  What we need is a "goo.gl" short link for the
 * Player or Team CPT so we'll leverage the short link solution,
 * adapting it to the needs of this plugin.
 *
 * @param - integer post id
 * @return - string shortened URL or post's permalink if short URL cannot be retrieved
 *
 * @see http://kovshenin.com/2011/google-url-shortener-googl-wordpress/
 */
function st_get_googl_short_url($post_id)
{
	$http = new WP_Http();

    //  Get the permalink we want to shorten
	$permalink = get_permalink($post_id);

    $headers = array(
        'Content-Type' => 'application/json'
    );

    //  Issue the API request using WordPress' HTTP API
    $result = $http->request(SOCCER_TEAM_GOOGL_API, array(
        'method' => 'POST',
        'body' => '{"longUrl": "' . $permalink . '"}',
        'headers' => $headers)
    );

    //  Make sure an error wasn't received - if an error was
    //  received, simply return the original URL so there is 
    //  something saved that resolves.

    if (!is_wp_error($result))
    {
	    $result = json_decode($result['body']) ;
	    $shortlink = $result->id ;

	    if ($shortlink)
		    return $shortlink ;
    }

    return $permalink ;
}

?>
