<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Soccer-Team widgets
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package Soccer-Team
 * @subpackage widgets
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

/**
 * Gooogle Chart API to generate QR codes
 */
define('ST_QR_CODE_URL', 'http://chart.apis.google.com/chart?cht=qr&chs=%sx%s&chld=%s|%s&chl=%s') ;
define('ST_SINC_TEAM_RANK_URL', 'http://soccerincollege.com/sicMyTeamInfo.aspx?id=%s') ;

/**  Output a Player's Profile Custom Fields */
function soccer_team_team_custom_fields($post_id, $mode = 'full')
{
    $brief = ($mode === 'brief') ;

    //  Need the plugin options to handle some of the fields
    $stpo = soccer_team_get_plugin_options() ;
    $emailaddr = &$stpo['profile_email_request'] ;
    $tp = &$stpo[ST_PREFIX . 'team_downloadable_profile'] ;
 
    $qr_code = (($mode === 'full') && ($stpo['qr_code_team'] == 1)) ;
 
    //  Get the fields from the meta box definition
    $mb = soccer_team_team_profile_meta_box_content() ;

    //  Need images in the event the team doesn't  have one.

    if ($brief)
        $smnia = plugins_url('images/NoPhotoAvailable150x100.png', __FILE__) ;
    else
        $smnia = plugins_url('images/NoPhotoAvailable300x200.png', __FILE__) ;

    $lgnia = plugins_url('images/NoPhotoAvailable600x400.png', __FILE__) ;

    //  Assemble the <img> tag ...
    $lgurl = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full') ;
    $smurl = wp_get_attachment_image_src(get_post_thumbnail_id(), 'team-photo') ;

    $smimg = ($smurl[0] == '') ? $smnia : $smurl[0] ;
    $lgimg = ($lgurl[0] == '') ? $lgnia : $lgurl[0] ;

    //$alt = get_post_meta( get_the_ID(), ST_PREFIX . 'position', true) ;
    $title = get_the_title(''. '', false) ;

    $alt = get_bloginfo('description') ;

    $output = '<div class="st-team-profile">' . PHP_EOL ;

    $output .= sprintf('<div class="st-team-photo">%s<a href="%s" rel="lightbox" title="%s" alt="%s">' .
        '<img class="st-team-photo" src="%s" alt="%s" title="%s" /></a>' .
        '%s</div>%s', PHP_EOL, $lgimg, $title, $alt, $smimg, $alt, $title, PHP_EOL, PHP_EOL) ;

    //  Build a table to the team detail
    $output .= '<div class="st-team-profile-details">' . PHP_EOL ;
    $output .= '<table class="st-team-profile">' . PHP_EOL ;

    //  No need for table header in brief mode
    if (!$brief)
    {
        $output .= '<thead>' . PHP_EOL ;
        $output .= '<tr class="st-team-profile-row st-team-profile-row-header">' . PHP_EOL ;
        $output .= '<th class="st-team-profile-header" colspan="2">' . get_the_title() . '</th>' .
            PHP_EOL . PHP_EOL .  '</tr>' . PHP_EOL . '</thead>' . PHP_EOL ;
    }
    $output .= '<tbody>' . PHP_EOL ;

    //  Some content is marked "private" which means the current
    //  user must be a subcriber in order to view the content.

    $private = (strtolower(get_post_meta(get_the_ID(),
        ST_PREFIX . 'team_privacy', true)) === 'private') ;

    if (!$private || current_user_can('read'))
    {
        $skipfields = array(
            ST_PREFIX . 'team_privacy',
            '_' . ST_PREFIX . 'team_profile_short_url',
        ) ;

        foreach ($mb['fields'] as $field)
        {
            if (in_array(strtolower($field['id']), $skipfields)) continue ;

            // get current post meta data
            $meta = get_post_meta($post_id, $field['id'], true);

            //  Show the fields that aren't empty
            if ($meta != '')
            {
                $fieldclass = substr($field['id'], strlen(ST_PREFIX)) ;
                $fieldlabel = ucwords($field['name']) ;
                $output .= '<tr class="st-team-profile-row st-team-profile-row-' . $fieldclass . '">' . PHP_EOL ;
                $output .= '<th class="st-team-' . $fieldclass . '">' . $fieldlabel . ':</th>' . PHP_EOL ;
                $output .= '<td class="st-team-' . $fieldclass . '">' ;

                switch ($field['id'])
                {
                    case ST_PREFIX . 'gs_profile' :
                    case ST_PREFIX . 'sic_profile' :
                        $thickbox =  '&KeepThis=true&TB_iframe=true&height=800&width=1024' ;
                            $output .= '<a class="thickbox" title="' . $field['desc'] . '" href="' .
                                $meta . $thickbox . '">Click to view ' . $field['name'] . '.</a>' ;
                        break ;

                    default:
                        $output .= $meta ;
                        break ;
                }
                $output .= '</td>' . PHP_EOL . '</tr>' . PHP_EOL ;
            }
        }

        //  Handle the Team Profile Attachment

        if ((int)$tp === 1) //  Make sure profiles are enabled!
        {
            $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;
            $pdficon = plugins_url('/images/icons/Adobe_PDF_by_Email_Icon_35x35.png', __FILE__) ;

            $output .= '<tr class="st-team-profile-row st-team-profile-row-attachment">' . PHP_EOL ;
            $output .= '<th class="st-team-profile-attachment">Downloadable Team Profile:</th>' . PHP_EOL ;
            $output .= '<td class="st-team-profile-attachment">' ;

            $profile = get_post_meta(get_the_ID(), 'soccer_team_profile_attachment', true) ; 

            if ($profile !== '' && !$private) //  Download profile
            {
                $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;

                $output .= sprintf('<a href="%s" title="%s"><img src="%s"></a>', $profile['url'],
                    sprintf('Download Team Profile:  %s', $title), $pdficon) . PHP_EOL ;
            }
            elseif ($profile !== '' && $private)  //  Request profile by email
            {
                $pdficon = plugins_url('/images/icons/Adobe_PDF_by_Email_Icon_35x35.png', __FILE__) ;

                $subj = sprintf('Team Profile Request:  %s', $title) ;
                $body = sprintf('Team Profile Request:  %s', $title) ;
                $body .= '%0D%0A%0D%0ARequestor Name:  %0D%0ARequestor Organization:  ' ;

                $output .= sprintf('<a href="mailto:%s?subject=%s&body=%s" title="%s"><img src="%s"></a>',
                    $emailaddr, htmlentities($subj), htmlentities($body),
                    sprintf('Request Team Profile by Email:  %s', $title), $pdficon) . PHP_EOL ;
            }
            else // No profile available
            {
                $output .= 'N/A' ;
            }

            $output .= '</td>' . PHP_EOL . '</tr>' . PHP_EOL ;
        }
    }
    else
    {
        $output .= '<p class="st-team-private-profile">You must <a href="' .
            site_url() . '/wp-login.php' . '">login</a> to view profile.</p>' ;
    }

    $output .= '</tbody>' . PHP_EOL . '</table>' . PHP_EOL . '</div>' ;

    //  Don't include details in 'brief' mode or if content is private
    if ((!$brief) && (!$private))
    {
        //  Output QR Code?

        if ($qr_code)
        {
            //  Use short links if we have them to generate better QR codes
            $qr_code_short_link = get_post_meta(get_the_ID(), '_' . ST_PREFIX . 'team_profile_short_url', true) ; 

            if (empty($qr_code_short_link)) $qr_code_short_link = get_permalink() ;

            $output .= '<div class="st-team-qr-code">' . PHP_EOL ;
            $output .= sprintf('<a class="st-team-qr-code" href="%s" title="%s"><img class="st-team-qr-code" src="%s" /></a></div>',
                get_permalink(), get_the_title(), sprintf(ST_QR_CODE_URL, $stpo['qr_code_height'], $stpo['qr_code_width'],
                $stpo['qr_code_quality'], $stpo['qr_code_border'], $qr_code_short_link)) ;
        }

        $output .= get_the_content() ;
    }

    $output .= '</div>' ;

    return $output ;
}
 
/**  Output a Player's Profile Custom Fields */
function soccer_team_player_custom_fields($post_id, $mode = 'full')
{
    $widget = ($mode === 'widget') ;
    $brief = ($mode === 'brief') || is_archive() ;

    //  Need the plugin options to handle some of the fields
    $stpo = soccer_team_get_plugin_options() ;
    $emailaddr = &$stpo['profile_email_request'] ;
    $pp = &$stpo[ST_PREFIX . 'player_downloadable_profile'] ;

    $qr_code = (($mode === 'full') && ($stpo['qr_code_player'] == 1)) ;
 
    //  Get the fields from the meta box definition
    $mb = soccer_team_player_profile_meta_box_content() ;

    //  Need images in the event the player doesn't  have one.

    if ($brief || $widget)
        $smnia = plugins_url('images/NoPhotoAvailable150x100.png', __FILE__) ;
    else
        $smnia = plugins_url('images/NoPhotoAvailable300x200.png', __FILE__) ;

    $lgnia = plugins_url('images/NoPhotoAvailable600x400.png', __FILE__) ;

    //  Assemble the <img> tag ...
    if ($brief || $widget)
        $smurl = wp_get_attachment_image_src(get_post_thumbnail_id(), 'player-gallery') ;
    else
        $smurl = wp_get_attachment_image_src(get_post_thumbnail_id(), 'player-headshot') ;

    $lgurl = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full') ;

    $smimg = ($smurl[0] == '') ? $smnia : $smurl[0] ;
    $lgimg = ($lgurl[0] == '') ? $lgnia : $lgurl[0] ;

    //  Build the link title
    $title = '#' . get_post_meta( get_the_ID(),
        ST_PREFIX . 'player_jersey_number', true) .  ' - ' . get_the_title(''. '', false) ;

    //  Get the position(s) from the taxonomy
    $terms = get_the_terms(get_the_ID(), SOCCER_TEAM_TAX_POSITION) ;

    if ($terms && ! is_wp_error( $terms ))
    {
        $positions = array();

        foreach ( $terms as $term )
        {
            $positions[] = '<a href="/' . SOCCER_TEAM_TAX_SLUG_POSITION . '/' . $term->slug .
                '" title="' . sprintf('View Position:  %s', $term->name) .  '">' . $term->name . '</a>' ;
        }
		
        $positions = join( '<br/>', $positions );
    }
    else
    {
        $positions = '&nbsp;' ;
    }
    
    //  Get the roster(s) from the taxonomy
    $terms = get_the_terms(get_the_ID(), SOCCER_TEAM_TAX_ROSTER) ;

    if ($terms && ! is_wp_error( $terms ))
    {
        $rosters = array();

        foreach ( $terms as $term )
        {
            $rosters[] = '<a href="/' . SOCCER_TEAM_TAX_SLUG_ROSTER . '/' . $term->slug .
                '" title="' . sprintf('View Roster:  %s', $term->name) .  '">' . $term->name . '</a>' ;
        }
		
        $rosters = join( '<br/>', $rosters );
    }
    else
    {
        $rosters = '&nbsp;' ;
    }
    
    $jersey = '#' . get_post_meta(get_the_ID(), ST_PREFIX . 'player_jersey_number', true) ;
    $alt = &$jersey ;

    $output = '<div class="st-player-profile">' . PHP_EOL ;

    //  Build Player Content

    $output .= sprintf('<div class="st-player-photo">%s<a href="%s" rel="lightbox" title="%s" alt="%s">' .
        '<img style="%s" class="st-player-photo" src="%s" alt="%s" title="%s" /></a>' .
        '%s</div>%s', PHP_EOL, $lgimg, $title,
        $alt, ($widget) ? 'float: none;' : '', $smimg, $alt, $title, PHP_EOL, PHP_EOL) ;

    //  Build a table to the player detail
    $output .= '<div class="st-player-profile-details">' . PHP_EOL ;
    $output .= '<table class="st-player-profile">' . PHP_EOL ;

    //  No need for table header in brief mode
    if (!$brief)
    {
        $output .= '<thead>' . PHP_EOL ;
        $output .= '<tr class="st-player-profile-row st-player-profile-row-header">' . PHP_EOL ;
        if (!is_single())
        {
            $output .= '<th class="st-player-profile-header" colspan="2"><a href="' .
                get_permalink() . '">' . get_the_title() . '</a></th>' .
                PHP_EOL . PHP_EOL .  '</tr>' . PHP_EOL . '</thead>' . PHP_EOL ;
        }
    }

    $output .= '<tbody>' . PHP_EOL ;

    if (!$widget)
    {
        //  Position comes from the taxonomy
        $output .= '<tr class="st-player-profile-row st-player-profile-row-position">' . PHP_EOL ;
        $output .= '<th class="st-player-position">Position(s):</th>' . PHP_EOL ;
        $output .= '<td class="st-player-position">' . $positions . '</td>' . PHP_EOL ;
        $output .= '</tr>' . PHP_EOL ;
    
        //  Roster comes from the taxonomy
        $output .= '<tr class="st-player-profile-row st-player-profile-row-roster">' . PHP_EOL ;
        $output .= '<th class="st-player-roster">Roster(s):</th>' . PHP_EOL ;
        $output .= '<td class="st-player-roster">' . $rosters . '</td>' . PHP_EOL ;
        $output .= '</tr>' . PHP_EOL ;
    
        //  Some content is marked "private" which means the current
        //  user must be a subcriber in order to view the content.
    
        $private = (strtolower(get_post_meta(get_the_ID(),
            ST_PREFIX . 'player_privacy', true)) === 'private') ;
    
        if ($brief || $widget)
        {
            $fieldlabel = 'Jersey Number' ;
            $fieldclass = 'player_jersey_number' ;
            //$fieldlabel = ucwords(str_replace(array('_'), ' ', $fieldclass)) ;
            $output .= '<tr class="st-player-profile-row roster-player-profile-row-' . $fieldclass . '">' . PHP_EOL ;
            $output .= '<th class="st-player-' . $fieldclass . '">' . $fieldlabel . ':</th>' . PHP_EOL ;
            $output .= '<td class="st-player-' . $fieldclass . '">' . $jersey ;
            $output .= '</td>' . PHP_EOL . '</tr>' . PHP_EOL ;
     
            //  Handle the Player Profile Attachment
    
            if (((int)$pp === 1)) //  Make sure profiles are enabled and public!
            {
                $output .= '<tr class="st-team-profile-row st-team-profile-row-attachment">' . PHP_EOL ;
                $output .= '<th class="st-player-profile-attachment">Downloadable Player Profile:</th>' . PHP_EOL ;
                $output .= '<td class="st-player-profile-attachment">' ;
    
                $profile = get_post_meta(get_the_ID(), 'soccer_team_profile_attachment', true) ; 
    
                if ($profile !== '' && !$private) //  Download profile
                {
                    $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;

                    $output .= sprintf('<a href="%s" title="%s"><img src="%s"></a>', $profile['url'],
                        sprintf('Download Player Profile:  %s', $title), $pdficon) . PHP_EOL ;

                }
                elseif ($profile !== '' && $private)  //  Request profile by email
                {
                    $pdficon = plugins_url('/images/icons/Adobe_PDF_by_Email_Icon_35x35.png', __FILE__) ;

                    $subj = sprintf('Player Profile Request:  %s', $title) ;
                    $body = sprintf('Player Profile Request:  %s', $title) ;
                    $body .= '%0D%0A%0D%0ARequestor Name:  %0D%0ARequestor Organization:  ' ;

                    $output .= sprintf('<a href="mailto:%s?subject=%s&body=%s" title="%s"><img src="%s"></a>',
                        $emailaddr, htmlentities($subj), htmlentities($body),
                        sprintf('Request Player Profile by Email:  %s', $title), $pdficon) . PHP_EOL ;
                }
                else // No profile available
                {
                    $output .= 'N/A' ;
                }
    
                $output .= '</td>' . PHP_EOL . '</tr>' . PHP_EOL ;
            }
        }
        else
        {
            if (!$private || current_user_can('read'))
            {
                $skipfields = array(
                    ST_PREFIX . 'player_privacy',
                    '_' . ST_PREFIX . 'player_profile_short_url',
                ) ;

                foreach ($mb['fields'] as $field)
                {
                    if (in_array(strtolower($field['id']), $skipfields)) continue ;
    
                    // get current post meta data
                    $meta = get_post_meta($post_id, $field['id'], true);
    
                    //  Show the fields that aren't empty
                    if ($meta != '')
                    {
                        $fieldclass = substr($field['id'], strlen(ST_PREFIX)) ;
                        $fieldlabel = ucwords(str_replace(array('_'), ' ', $fieldclass)) ;
                        $output .= '<tr class="st-player-profile-row roster-player-profile-row-' . $fieldclass . '">' . PHP_EOL ;
                        $output .= '<th class="st-player-' . $fieldclass . '">' . $fieldlabel . ':</th>' . PHP_EOL ;
                        $output .= '<td class="st-player-' . $fieldclass . '">' ;
               
                        switch ($field['id'])
                        {
                            case ST_PREFIX . 'gs_profile' :
                            case ST_PREFIX . 'sic_profile' :
                                $thickbox =  '&KeepThis=true&TB_iframe=true&height=800&width=1024' ;
                                    $output .= '<a class="thickbox" title="' . $field['desc'] . '" href="' .
                                        $meta . $thickbox . '">Click to view ' . $field['name'] . '.</a>' ;
                                break ;
                            case ST_PREFIX . 'player_qr_code' ;
                                if (strtolower($meta) === 'on')
                                    $output .= sprintf('<a href="%s" title="%s"><img src="%s"></a>',
                                       get_permalink(), get_the_title(), sprintf(ST_QR_CODE_URL, get_permalink())) ;
                                else
                                    $output .= 'N/A' ;
                                break ;
    
                            default:
                                $output .= $meta ;
                                break ;
                        }
                        $output .= '</td>' . PHP_EOL . '</tr>' . PHP_EOL ;
                    }
                }
    
                //  Handle the Player Profile Attachment
    
                if ((int)$pp === 1) //  Make sure profiles are enabled!
                {
                    $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;
    
                    $output .= '<tr class="st-team-profile-row st-team-profile-row-attachment">' . PHP_EOL ;
                    $output .= '<th class="st-player-profile-attachment">Downloadable Player Profile:</th>' . PHP_EOL ;
                    $output .= '<td class="st-player-profile-attachment">' ;
    
                    $profile = get_post_meta(get_the_ID(), 'soccer_team_profile_attachment', true) ; 
    
                    if ($profile !== '' && !$private) //  Download profile
                    {
                        $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;
    
                        $output .= sprintf('<a href="%s" title="%s"><img src="%s"></a>', $profile['url'],
                            sprintf('Download Player Profile:  %s', $title), $pdficon) . PHP_EOL ;
                    }
                    elseif ($profile !== '' && $private)  //  Request profile by email
                    {
                        $pdficon = plugins_url('/images/icons/Adobe_PDF_by_Email_Icon_35x35.png', __FILE__) ;
    
                        $subj = sprintf('Player Profile Request:  %s', $title) ;
                        $body = sprintf('Player Profile Request:  %s', $title) ;
                        $body .= '%0D%0A%0D%0ARequestor Name:  %0D%0ARequestor Organization:  ' ;

                        $output .= sprintf('<a href="mailto:%s?subject=%s&body=%s" title="%s"><img src="%s"></a>',
                            $emailaddr, htmlentities($subj), htmlentities($body),
                            sprintf('Request Player Profile by Email:  %s', $title), $pdficon) . PHP_EOL ;
                    }
                    else // No profile available
                    {
                        $output .= 'N/A' ;
                    }

                    $output .= '</td>' . PHP_EOL . '</tr>' . PHP_EOL ;
                }
            }
            else
            {
                $output .= '<p class="st-player-private-profile">You must <a href="' .
                    site_url() . '/wp-login.php' . '">login</a> to view profile.</p>' ;
            }
        }
    }

    $output .= '</tbody>' . PHP_EOL . '</table>' . PHP_EOL . '</div>' ;

    //  Don't include details in 'brief' mode or if content is private
    if ((!$brief) && (!$widget) && (!$private))
    {
        //  Output QR Code?

        if ($qr_code)
        {
            //  Use short links if we have them to generate better QR codes
            $qr_code_short_link = get_post_meta(get_the_ID(), '_' . ST_PREFIX . 'player_profile_short_url', true) ; 

            if (empty($qr_code_short_link)) $qr_code_short_link = get_permalink() ;

            $output .= '<div class="st-player-qr-code">' . PHP_EOL ;
            $output .= sprintf('<a class="st-player-qr-code" href="%s" title="%s"><img class="st-player-qr-code" src="%s" /></a></div>',
                get_permalink(), get_the_title(), sprintf(ST_QR_CODE_URL, $stpo['qr_code_height'], $stpo['qr_code_width'],
                $stpo['qr_code_quality'], $stpo['qr_code_border'], $qr_code_short_link)) ;
        }

        $output .= get_the_content() ;
    }

    $output .= '</div>' ;

    return $output ;
}
 
/**  [soccer_team_players] shortcode */
function soccer_team_players_roster_shortcode($orderbyname = false)
{
    //  Need the plugin options to handle some of the fields
    $stpo = soccer_team_get_plugin_options() ;
    $emailaddr = &$stpo['profile_email_request'] ;
    $pp = &$stpo[ST_PREFIX . 'player_downloadable_profile'] ;

    if ($orderbyname)
        $args = array(
            'post_type' => SOCCER_TEAM_CPT_PLAYER,
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
        ) ;
    else
        $args = array(
            'post_type' => SOCCER_TEAM_CPT_PLAYER,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'meta_key'=> ST_PREFIX . 'player_jersey_number',
        ) ;

    // Query players from the database.
    $loop = new WP_Query($args) ;

    //print '<pre>' ;
    //print_r($loop) ;
    //print '</pre>' ;

    //  Check to see if any players were returned.
    if ($loop->have_posts())
    {
        $plyrcnt = 0 ;

        //  Need a default image when the player head shot isn't available
        $nia = '<img class="wp-post-image st-no-photo-available" src="' .
            plugins_url('images/NoPhotoAvailable150x100.png', __FILE__) . '">' ;

        //  Build a table to display the roster - it is the cleanest way ...
        $output = '<table class="st-players">' . PHP_EOL ;
        $output .= '<thead class="st-players">' . PHP_EOL ;
        $output .= '<tr class="st-player-header-row">' . PHP_EOL ;
        $output .= '<th class="st-player-jersey-number">Jersey Number</th>' . PHP_EOL ;
        $output .= '<th class="st-player-photo">Photo</th>' . PHP_EOL ;
        $output .= '<th class="st-player-name">Player Name</th>' . PHP_EOL ;
        $output .= '<th class="st-player-photo">Position(s)</th>' . PHP_EOL ;

        if ((int)$pp === 1) //  Only add profile column if profiles are enabled
            $output .= '<th class="st-player-profile">Profile</th>' . PHP_EOL ;

        $output .= '</tr>' . PHP_EOL . '</thead>' . PHP_EOL . '<tbody>' . PHP_EOL ;

        //  Loop through the players (The Loop)
        while ($loop->have_posts())
        {
            $loop->the_post() ;

            $output .= sprintf('<tr class="st-player-row st-player-%s-row">' . PHP_EOL,
                ($plyrcnt++ % 2) == 0 ? 'odd' : 'even') ;

            //  Output Jersey Number
            $output .= '<td class="st-player-jersey-number">' . '<a href="' .
                get_permalink() . '">' . get_post_meta( get_the_ID(), ST_PREFIX .
                'player_jersey_number', true) . '</a></td>' . PHP_EOL ;
 
            //  Get the head shot image
            $img = get_the_post_thumbnail(null, 'player-thumbnail') ;

            //  Output Player Head Shot
            $output .= sprintf('<td class="st-player-photo"><a href="%s">%s</a></td>',
                get_permalink(), ($img == '') ? $nia : $img) ;
 
            //  Display the Player's Name (title field)
            $output .= the_title(
                '<td class="st-player-name"><a href="' . get_permalink() . '">',
                '</a></td>' . PHP_EOL, false
            ) ;

            //  Output Position(s)

            $terms = get_the_terms(get_the_ID(), SOCCER_TEAM_TAX_POSITION) ;

            if ($terms && ! is_wp_error( $terms ))
            {
                $positions = array();

                foreach ( $terms as $term )
                {
                    $positions[] = '<a href="/' . SOCCER_TEAM_TAX_SLUG_POSITION . '/' . $term->slug .
                        '" title="' . sprintf('View Position:  %s', $term->name) .  '">' . $term->name . '</a>' ;
                }
		
                $positions = join( '<br/>', $positions );
            }
            else
            {
                $positions = '&nbsp;' ;
            }
                        
            $output .= '<td class="st-player-position">' . $positions . '</td>' . PHP_EOL ;

            if ((int)$pp === 1) //  Only add profile column if profiles are enabled
            {
                $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;
                $pdfbyemailicon = plugins_url('/images/icons/Adobe_PDF_by_Email_Icon_35x35.png', __FILE__) ;

                $output .= '<td class="st-player-profile">' ;
                $title = '#' . get_post_meta( get_the_ID(),
                    ST_PREFIX . 'player_jersey_number', true) .  ' - ' . get_the_title() ;

                $profile = get_post_meta(get_the_ID(), 'soccer_team_profile_attachment', true) ; 
                $private = (strtolower(get_post_meta(get_the_ID(),
                    ST_PREFIX . 'player_privacy', true)) === 'private') ;

                if ($profile !== '' && !$private) //  Download profile
                {
                    $pdficon = plugins_url('/images/icons/Adobe_PDF_Icon_35x35.png', __FILE__) ;

                    $output .= sprintf('<a href="%s" title="%s"><img src="%s"></a>', $profile['url'],
                        sprintf('Download Player Profile:  %s', $title), $pdficon) . PHP_EOL ;
                }
                elseif ($profile !== '' && $private)  //  Request profile by email
                {
                    $pdficon = plugins_url('/images/icons/Adobe_PDF_by_Email_Icon_35x35.png', __FILE__) ;

                    $subj = sprintf('Player Profile Request:  %s', $title) ;
                    $body = sprintf('Player Profile Request:  %s', $title) ;
                    $body .= '%0D%0A%0D%0ARequestor Name:  %0D%0ARequestor Organization:  ' ;

                    $output .= sprintf('<a href="mailto:%s?subject=%s&body=%s" title="%s"><img src="%s"></a>',
                        $emailaddr, htmlentities($subj), htmlentities($body),
                        sprintf('Request Player Profile by Email:  %s', $title), $pdficon) . PHP_EOL ;
                }
                else // No profile available
                {
                    $output .= 'N/A' ;
                }

                /*
                if ($profile !== "")
                {
                    $title = '#' . get_post_meta( get_the_ID(),
                        ST_PREFIX . 'player_jersey_number', true) .  ' - ' . single_post_title('', false) ;

                    $output .= sprintf('<a href="%s" title="%s"><img src="%s"></a>',
                        $profile['url'], $title, $pdficon) . PHP_EOL ;
                }
                else
                    $output .= '&nbsp;' ;
                 */

                $output .= '</td>' . PHP_EOL ;
            }

            $output .= '</tr>' . PHP_EOL ;
        }

        //  Close the unordered list
        $output .= '</tbody>' . PHP_EOL . '</table>' . PHP_EOL ;
    }
    else  //  No players found
    {
        $output = '<p class="st-players-not-found">No players have been added to the roster.' ;
    }

    // Reset Post Data
    wp_reset_postdata();

    //  Return the list of players
    return $output ;
}

/**  [soccer_team_players_gallery] shortcode */
function soccer_team_players_gallery_shortcode($imgclick = 'lightbox', $orderbyname = false)
{
    if ($orderbyname)
        $args = array(
            'post_type' => SOCCER_TEAM_CPT_PLAYER,
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
        ) ;
    else
        $args = array(
            'post_type' => SOCCER_TEAM_CPT_PLAYER,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'meta_key'=> ST_PREFIX . 'player_jersey_number',
        ) ;


    // Query players from the database.
    $loop = new WP_Query($args) ;

    //  Need a default image when the player head shot isn't available
    $smnia = plugins_url('images/NoPhotoAvailable150x100.png', __FILE__) ;
    $lgnia = plugins_url('images/NoPhotoAvailable600x400.png', __FILE__) ;

    //  Check to see if any players were returned.
    if ($loop->have_posts())
    {
        $plyrcnt = 0 ;

        //  Build a table to display the roster - it is the cleanest way ...
        $output = '<div id="st-player-gallery-carousel">' . PHP_EOL ;

        //  Loop through the players (The Loop)
        while ($loop->have_posts())
        {
            $loop->the_post() ;

            //  Assemble the <img> tag ...
            $smurl = wp_get_attachment_image_src(get_post_thumbnail_id(), 'player-gallery') ;
            $lgurl = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full') ;

            $smimg = ($smurl[0] == '') ? $smnia : $smurl[0] ;
            $lgimg = ($lgurl[0] == '') ? $lgnia : $lgurl[0] ;

            //  Get the position(s) from the taxonomy
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

            //$alt = get_post_meta( get_the_ID(), ST_PREFIX . 'position', true) ;
            $alt = &$positions ;

            $title = '#' . get_post_meta( get_the_ID(),
                ST_PREFIX . 'player_jersey_number', true) .  ' - ' . get_the_title(''. '', false) ;

            //  What happens when the image in the Carousel is clicked?
            //  Either show the large image in a LightBox or open the PermaLink.

            if ($imgclick == 'lightbox')
            {
                $output .= sprintf('<a href="%s" rel="lightbox" title="%s">' .
                    '<img class="cloudcarousel" src="%s" alt="%s" title="%s" /></a>',
                    $lgimg, $title, $smimg, $alt, $title) ;
            }
            else
            {
                $output .= sprintf('<a  href="%s">' .
                    '<img class="cloudcarousel" src="%s" alt="%s" title="%s" /></a>',
                    get_permalink(), $smimg, $alt, $title) ;
            }

            $output .= PHP_EOL ;
        }

        //  Add the other elements to make the Cloud Carousel work and close the div
        $output .= '<div id="st-pgc-title"></div>' . PHP_EOL ;
        $output .= '<div id="st-pgc-alt" ></div>' . PHP_EOL ;
        $output .= '<div id="st-pgc-left-button" class="st-pgc-left-button"></div>' . PHP_EOL ;
        $output .= '<div id="st-pgc-right-button" class="st-pgc-right-button"></div>' . PHP_EOL ;
        $output .= '</div>' . PHP_EOL ;
    }
    else  //  No players found
    {
        $output = '<p class="st-players-not-found">No players have been added to the roster.' ;
    }

    // Reset Post Data
    wp_reset_postdata();

    //  Return the list of players
    return $output ;
}

/**
 * soccer_team_carousel_head()
 *
 * WordPress header actions to support Cloud Carousel
 */
function soccer_team_carousel_head()
{
    //  Load the Cloud Carosel CS
    wp_enqueue_style('soccer-team-cloud-carousel',
        plugins_url('css/soccer-team-cloud-carousel.css', __FILE__)) ;

    //  Load the Cloud Carosel Javascript
    wp_register_script('jquery-cloud-carousel',
        plugins_url('js/cloud-carousel-1.0.5/cloud-carousel.1.0.5.min.js', __FILE__),
        array('jquery'), false, true) ;
    wp_enqueue_script('jquery-cloud-carousel') ;

    //  Load the Mousewheel Javascript
    wp_register_script('jquery-mousewheel',
        plugins_url('js/mousewheel-3.0.4/jquery.mousewheel.js', __FILE__),
        array('jquery'), false, true) ;
    wp_enqueue_script('jquery-mousewheel') ;

    //
    //  Load Slimbox CSS
    wp_enqueue_style('soccer-team-slimbox',
        plugins_url('js/slimbox-2.05/css/slimbox2.css', __FILE__)) ;

    //  Load the Slimbox Javascript
    wp_register_script('jquery-slimbox2',
        plugins_url('js/slimbox-2.05/js/slimbox2.js', __FILE__),
        array('jquery'), false, true) ;
    wp_enqueue_script('jquery-slimbox2') ;
    wp_enqueue_script('thickbox') ;
    wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
 
    /*
    //  Load the Slimbox Autoload Javascript
    wp_register_script('jquery-slimbox2-autoload',
        plugins_url('js/slimbox-2.05/js/autoload.js', __FILE__),
        array('jquery'), false, true) ;
    wp_enqueue_script('jquery-slimbox2-autoload') ;
    */
}

/**
 * soccer_team_cloud_carousel_footer()
 *
 * WordPress footer actions
 */
function soccer_team_cloud_carousel_footer()
{
    //
    //  jQuery script to initialize the form validation
    //  neccessary so bad or missing data is submitted.
    //  When required fields are blank the normal Google
    //  processing for form errors doesn't occur, this
    //  jQuery script handles it gracefully.  The fields
    //  have only rudimentary validation.
    //
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
// This initialises carousels on the container elements specified, in this case, carousel1.
	$("#st-player-gallery-carousel").CloudCarousel(		
		{			
            //reflHeight: 56,
            //reflGap: 2,
			xPos: 285,
			yPos: 125,
            mouseWheel: true,
			buttonLeft: $("#st-pgc-left-button"),
			buttonRight: $("#st-pgc-right-button"),
			altBox: $("#st-pgc-alt"),
			titleBox: $("#st-pgc-title")
		}
	);
    
});
</script>
<?php
}

/**
 * SoccerTeamPlayerProfileWidget class definition
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @access public
 * @see WP_Widget()
 */
class SoccerTeamPlayerProfileWidget extends WP_Widget
{
    /**
     * Process the widget
     *
     */
    function SoccerTeamPlayerProfileWidget()
    {
        $widget_ops = array(
            'classname' => 'st-featured-player-widget',
            'description' => 'Display a featured player profile.'
        ) ;

        $this->WP_Widget('SoccerTeamPlayerWidget',
            'Featured Player Widget', $widget_ops) ;
    }

    /**
     * Build the widget settings form
     *
     * @param $instance mixed - widget instance
     */
    function form($instance)
    {
        $defaults = array(
            'title' => 'Featured Player',
            'random' => 'on',
            'playerid' => -1
        ) ;
        $instance = wp_parse_args((array) $instance, $defaults) ;
        $title = $instance['title'] ;
        $random = $instance['random'] ;
        $playerid = ($random == 'o') ? $defaults['playerid'] : $instance['playerid'] ;

?>
    <p>Title: <input class="widefat" name="<?php echo $this->get_field_name('title') ; ?>"
    type="text" value="<?php echo esc_attr($title) ; ?>" /></p>

    <p>
        <label>Random Featured Player:</label>
        On <input name="<?php echo $this->get_field_name('random') ; ?>" type="radio" <?php checked($random, 'on') ; ?> value="on" />
        Off <input name="<?php echo $this->get_field_name('random') ; ?>" type="radio" <?php checked($random, 'off') ; ?> value="off" />
    </p>

    <p>Player:
    <select name="<?php echo $this->get_field_name('playerid') ; ?>">
    <option value="-1" <?php selected($playerid, -1) ; ?>>--</option>

<?php
    // Query players from the database.
    $loop = new WP_Query(
        array(
            'post_type' => 'player',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
            //'post__not_in' => array(get_the_ID())
        )
    ) ;

    //  Check to see if any players were returned.
    if ($loop->have_posts())
    {
        //  Loop through the players (The Loop)
        while ($loop->have_posts())
        {
            $loop->the_post() ;
            $id = get_the_ID() ;
            $title = get_the_title() ;

?>
    <option value="<?php echo $id ;?>" <?php selected($playerid, $id) ; ?>><?php echo $title ;?></option>
<?php
            //  Get the position(s) from the taxonomy
            //$terms = get_the_terms(get_the_ID(), 'player_position') ;

        }
    }
?>
    </select></p>


<?php
    }

    /**
     * Update the widget settings
     *
     * @param $new_instance mixed - new widget instance
     * @param $old_instance mixed - original widget instance
     */
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance ;
        $instance['title'] = strip_tags($new_instance['title']) ;
        $instance['random'] = strip_tags($new_instance['random']) ;
        $instance['playerid'] = $instance['random'] == 'on' ? -1 : strip_tags($new_instance['playerid']) ;

        return $instance ;
    }

    /**
     * Display the widget
     *
     * @param $args mixed - optional arguments
     * @param $instance mixed - widget instance
     */
    function widget($args, $instance)
    {
        extract($args) ;

        echo $before_widget ;
        $title = apply_filters('widget_title', $instance['title']) ;
        $random = empty($instance['random']) ? 'on' : $instance['random'] ;
        $playerid = empty($instance['playerid']) ? '&nbsp;' : $instance['playerid'] ;

        if ($random == 'on')
        {
            // Query players from the database.
            $player = new WP_Query(
                array(
                    'post_type' => 'player',
                    'orderby' => 'rand',
                    'posts_per_page' => 1,
                    //'post__not_in' => array(get_the_ID())
                )
            ) ;
        }
        else
        {
            // Query players from the database.
            $player = new WP_Query(
                array(
                    'post_type' => 'player',
                    'orderby' => 'rand',
                    'posts_per_page' => 1,
                    'post__in' => array($playerid)
                )
            ) ;
        }

        //  Check to see if any players were returned.
        if ($player->have_posts())
        {
            $player->the_post() ;
            $player_widget = soccer_team_player_custom_fields(get_the_ID(), 'widget') ;
        }

        if (!empty($title))
        {
            echo $before_title . $title . $after_title ;
        }

        echo $player_widget ;
        echo $after_widget ;
    }
}

/**
 * SoccerTeamPlayerProfileWidget class definition
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @access public
 * @see WP_Widget()
 */
class SoccerTeamSINCTeamRankWidget extends WP_Widget
{
    /**
     * Process the widget
     *
     */
    function SoccerTeamSINCTeamRankWidget()
    {
        $widget_ops = array(
            'classname' => 'st-sinc-team-rank-widget',
            'description' => 'Display a team\'s SoccerInCollege(SINC) ranking.'
        ) ;

        $this->WP_Widget('SoccerTeamSINCTeamRankWidget',
            'SINC Team Rank Widget', $widget_ops) ;
    }

    /**
     * Build the widget settings form
     *
     * @param $instance mixed - widget instance
     */
    function form($instance)
    {
        $defaults = array(
            'title' => 'SoccerInCollege Ranking',
            'teamid' => -1
        ) ;
        $instance = wp_parse_args((array) $instance, $defaults) ;
        $title = $instance['title'] ;
        $teamid = empty($instance['teamid']) ? $defaults['teamid'] : $instance['teamid'] ;

?>
    <p>Title: <input class="widefat" name="<?php echo $this->get_field_name('title') ; ?>"
    type="text" value="<?php echo esc_attr($title) ; ?>" /></p>

    <p>Team Id:<input class="widefat" name="<?php echo $this->get_field_name('teamid') ; ?>" type="text" placholder="SINC Team Id" value="<?php echo esc_attr($teamid) ; ?>" /></p>
<?php
    }

    /**
     * Update the widget settings
     *
     * @param $new_instance mixed - new widget instance
     * @param $old_instance mixed - original widget instance
     */
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance ;
        $instance['title'] = strip_tags($new_instance['title']) ;
        $instance['teamid'] = strip_tags($new_instance['teamid']) ;

        return $instance ;
    }

    /**
     * Display the widget
     *
     * @param $args mixed - optional arguments
     * @param $instance mixed - widget instance
     */
    function widget($args, $instance)
    {
        extract($args) ;

        echo $before_widget ;
        $title = apply_filters('widget_title', $instance['title']) ;
        $teamid = empty($instance['teamid']) ? '&nbsp;' : $instance['teamid'] ;

        if (!empty($title))
        {
            echo $before_title . $title . $after_title ;
        }

        $url = sprintf(ST_SINC_TEAM_RANK_URL, $teamid) ;

        $response= wp_remote_get($url) ;

        if (is_wp_error($response))
        {
            $style = '' ;
            $body = 'Unable to retrieve SoccerInCollege ranking.' ;
        }
        else
        {
            $data = &$response['body'] ;

            //  Pull out the CSS
            $matchcount = preg_match_all('|<style[^>]*>(.*?)</style>|si',$data,$matches);

            //  Did we find something?
            if ($matchcount > 0)
            {
                //  The content we want will be the first match
                $style = $matches[1][0]; // print 1st capture group for match number i
            }

            //  Need to "tweak" the CSS so it references the SINC
            //  URLs correctly since they are relative in the source

            $style = preg_replace('/url\(/', sprintf('url(%s/', dirname(ST_SINC_TEAM_RANK_URL)), $style) ;

            //  Pull out the body content
            $matchcount = preg_match_all('|<body[^>]*>(.*?)</body>|si', $data, $matches) ;

            //  Did we find something?
            if ($matchcount > 0)
            {
                //  The content we want will be the first match
                $body = $matches[1][0]; // print 1st capture group for match number i
            }

            //  Need to "tweak" the HTML so it references the SINC
            //  URLs correctly since they are relative in the source

            $body = preg_replace('/href=\'/', sprintf('href=\'%s/', dirname(ST_SINC_TEAM_RANK_URL)), $body) ;
        }

        $team_widget = sprintf('<div style="height:92px;width:200px;margin-top:5px;"><style>%s</style>%s</div>', $style, $body) ;

        echo $team_widget ;
        echo $after_widget ;
    }
}

add_action('widgets_init', 'soccer_team_register_widgets') ;

function soccer_team_register_widgets()
{
    register_widget('SoccerTeamPlayerProfileWidget') ;
    register_widget('SoccerTeamSINCTeamRankWidget') ;
}
?>
