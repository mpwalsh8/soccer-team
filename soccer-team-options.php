<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Soccer-Team options.
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package Soccer-Team
 * @subpackage options
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

/**
 * soccer_team_options_admin_footer()
 *
 * Hook into Admin head when showing the options page
 * so the necessary jQuery script that controls the tabs
 * is executed.
 *
 * @return null
 */
function soccer_team_options_admin_footer()
{
?>
<!-- Setup jQuery Tabs -->
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#soccer_team-tabs").tabs() ;
    }) ;
</script>
<?php
}

/**
 * soccer_team_options_print_scripts()
 *
 * Hook into Admin Print Scripts when showing the options page
 * so the necessary scripts that controls the tabs are loaded.
 *
 * @return null
 */
function soccer_team_options_print_scripts()
{
    //  Need to load jQuery UI Tabs to make the page work!

    wp_enqueue_script('jquery-ui-tabs') ;
}

/**
 * soccer_team_options_print_styles()
 *
 * Hook into Admin Print Styles when showing the options page
 * so the necessary style sheets that control the tabs are
 * loaded.
 *
 * @return null
 */
function soccer_team_options_print_styles()
{
    //  Need the jQuery UI CSS to make the tabs look correct.
    //  Load them from Google instead of including them in this
    //  plugin!

    wp_enqueue_style('xtra-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css') ;
}

/**
 * soccer_team_options_page()
 *
 * Build and render the options page.
 *
 * @return null
 */
function soccer_team_options_page()
{
?>
<div class="wrap">
<?php
    if (function_exists('screen_icon')) screen_icon() ;
?>
<h2><?php _e('Soccer Team Plugin Settings') ; ?></h2>
<?php
    $stpo = soccer_team_get_plugin_options() ;
    if (!$stpo['donation_message'])
    {
?>
<small>Please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DK4MS3AA983CC" target="_blank">PayPal donaton</a> if you find this plugin useful.</small>
<?php
    }
?>
<br /><br />
<div class="container">
    <div id="soccer_team-tabs">
        <ul>
            <li><a href="#soccer_team-tabs-1">Plugin Options</a></li>
            <li><a href="#soccer_team-tabs-2">Team Profile Options</a></li>
            <li><a href="#soccer_team-tabs-3">Player Profile Options</a></li>
            <li><a href="#soccer_team-tabs-4">FAQs</a></li>
            <li><a href="#soccer_team-tabs-5">Usage</a></li>
            <li><a href="#soccer_team-tabs-6">About</a></li>
        </ul>
        <div id="soccer_team-tabs-1">
            <form method="post" action="options.php">
                <?php settings_fields('soccer_team_plugin_options') ; ?>
                <?php soccer_team_settings_input() ; ?>
                <?php //soccer_team_team_profile_options() ; ?>
                <?php //soccer_team_player_profile_options() ; ?>
                <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </form>
        </div>
        <div id="soccer_team-tabs-2">
            <form method="post" action="options.php">
                <?php settings_fields('soccer_team_plugin_options') ; ?>
                <?php soccer_team_team_profile_options() ; ?>
                <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </form>
        </div>
        <div id="soccer_team-tabs-3">
            <form method="post" action="options.php">
                <?php settings_fields('soccer_team_plugin_options') ; ?>
                <?php soccer_team_player_profile_options() ; ?>
                <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </form>
        </div>
        <div id="soccer_team-tabs-4">
<?php
    //  Instead of duplicating the FAQ content in the ReadMe.txt file,
    //  let's simply extract it from the WordPress plugin repository!
    //
    //  This solutution is derived from a discussion found on www.DevNetwork.net.
    //  See full discussion:  http://www.devnetwork.net/viewtopic.php?f=38&t=102670
    //

    /*
    //  Commented regex to extract contents from <div class="main">contents</div>
    //  where "contents" may contain nested <div>s.
    //  Regex uses PCRE's recursive (?1) sub expression syntax to recurs group 1

    $pattern_long = '{                    # recursive regex to capture contents of "main" DIV
    <div\s+class="main"\s*>               # match the "main" class DIV opening tag
      (                                   # capture "main" DIV contents into $1
        (?:                               # non-cap group for nesting * quantifier
          (?: (?!<div[^>]*>|</div>). )++  # possessively match all non-DIV tag chars
        |                                 # or 
          <div[^>]*>(?1)</div>            # recursively match nested <div>xyz</div>
        )*                                # loop however deep as necessary
      )                                   # end group 1 capture
    </div>                                # match the "main" class DIV closing tag
    }six';  // single-line (dot matches all), ignore case and free spacing modes ON
    */

    //  WordPress plugin repository places the content in DIV with a class of
    //  "block-content" but there are actually several DIVs that have the same class.
    //  We only want the first one.

    $url = 'http://wordpress.org/extend/plugins/soccer-team/faq/' ;
    $response= wp_remote_get($url) ;

    if (is_wp_error($response))
    {
?>
<div class="updated error"><p>Unable to retrive FAQ content from WordPress plugin repository.</p></div>
<?php
    }
    else
    {
?>
<?php
        $data = &$response['body'] ;
        $pattern_short = '{<div\s+[^>]*?class="block-content"[^>]*>((?:(?:(?!<div[^>]*>|</div>).)++|<div[^>]*>(?1)</div>)*)</div>}si';
        $matchcount = preg_match_all($pattern_short, $data, $matches);

        //  Did we find something?
        if ($matchcount > 0)
        {
            //  The content we want will be the first match
            echo($matches[1][0]); // print 1st capture group for match number i
        }
        else
        {
?>
<div class="updated error"><p>Unable to retrive FAQ content from WordPress plugin repository.</p></div>
<?php
        }
        //echo("\n</pre>");
    }
        
?>
        </div>
        <div id="soccer_team-tabs-5">
<?php
    //  Instead of duplicating the FAQ content in the ReadMe.txt file,
    //  let's simply extract it from the WordPress plugin repository!
    //
    //  This solutution is derived from a discussion found on www.DevNetwork.net.
    //  See full discussion:  http://www.devnetwork.net/viewtopic.php?f=38&t=102670
    //

    /*
    //  Commented regex to extract contents from <div class="main">contents</div>
    //  where "contents" may contain nested <div>s.
    //  Regex uses PCRE's recursive (?1) sub expression syntax to recurs group 1

    $pattern_long = '{                    # recursive regex to capture contents of "main" DIV
    <div\s+class="main"\s*>               # match the "main" class DIV opening tag
      (                                   # capture "main" DIV contents into $1
        (?:                               # non-cap group for nesting * quantifier
          (?: (?!<div[^>]*>|</div>). )++  # possessively match all non-DIV tag chars
        |                                 # or 
          <div[^>]*>(?1)</div>            # recursively match nested <div>xyz</div>
        )*                                # loop however deep as necessary
      )                                   # end group 1 capture
    </div>                                # match the "main" class DIV closing tag
    }six';  // single-line (dot matches all), ignore case and free spacing modes ON
    */

    //  WordPress plugin repository places the content in DIV with a class of
    //  "block-content" but there are actually several DIVs that have the same class.
    //  We only want the first one.

    $url = 'http://wordpress.org/extend/plugins/soccer-team/other_notes/' ;
    $response= wp_remote_get($url) ;

    if (is_wp_error($response))
    {
?>
<div class="updated error"><p>Unable to retrive Usage content from WordPress plugin repository.</p></div>
<?php
    }
    else
    {
?>
<?php
        $data = &$response['body'] ;
        $pattern_short = '{<div\s+[^>]*?class="block-content"[^>]*>((?:(?:(?!<div[^>]*>|</div>).)++|<div[^>]*>(?1)</div>)*)</div>}si';
        $matchcount = preg_match_all($pattern_short, $data, $matches);

        //  Did we find something?
        if ($matchcount > 0)
        {
            //  The content we want will be the first match
            echo($matches[1][0]); // print 1st capture group for match number i
        }
        else
        {
?>
<div class="updated error"><p>Unable to retrive Usage content from WordPress plugin repository.</p></div>
<?php
        }
        //echo("\n</pre>");
    }
        
?>
        </div>
        <div id="soccer_team-tabs-6">
            <h4>About Soccer Team</h4>
	    <p>Build a soccer team web site with WordPress and the Soccer Team WordPress Plugin.  The Soccer Team plugin defines custom post types, shortcodes, and widgets that make it easy to build a team site and keep it updated retaining the look and feel of your WordPress based web site.  The Soccer Team plugin makes use of many CSS classes so it's output can better integrate with your WordPress theme.</p>
            <p>If you find this plugin useful and use it for commercial purposes, please consider <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DK4MS3AA983CC" target="_blank">making small donations towards this plugin</a> to help keep it up to date.</p>
        </div>
    </div>
</div>
<?php
}


/**
 * soccer_team_settings_input()
 *
 * Build the form content and populate with any current plugin settings.
 *
 * @return none
 */
function soccer_team_settings_input()
{
    $stpo = soccer_team_get_plugin_options() ;
    //print '<pre>' ; print_r($stpo) ; print '</pre>' ;
?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label>Player Profile Image</label></th>
            <td><fieldset>
            <table style="padding: 0px;"><tr>
            <td style="padding-left: 0px;"><label for="soccer_team_player_image_height">
            <input name="soccer_team_plugin_options[player_image_height]" type="text" id="soccer_team_player_image_height" value="<?php echo $stpo['player_image_height'] ; ?>" /><br />
            Player Profile Image Height (<small>in px)</small></label></td>
            <td><label for="soccer_team_player_image_width">
            <input name="soccer_team_plugin_options[player_image_width]" type="text" id="soccer_team_player_image_width" value="<?php echo $stpo['player_image_width'] ; ?>" /><br />
            Player Profile Image Width <small>(in px)</small></label></td>
            </tr></table>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>QR Codes</label></th>
            <td><fieldset>
            <label for="soccer_team_qr_code_team">
            <input name="soccer_team_plugin_options[qr_code_team]" type="checkbox" id="soccer_team_qr_code_team" value="1" <?php checked('1', $stpo['qr_code_team']) ; ?> />
            Enable QR Codes for Teams</label>
            <br />
            <label for="soccer_team_qr_code_player">
            <input name="soccer_team_plugin_options[qr_code_player]" type="checkbox" id="soccer_team_qr_code_player" value="1" <?php checked('1', $stpo['qr_code_player']) ; ?> />
            Enable QR Codes for Players</label>
            <br />
            <label for="soccer_team_qr_code_use_googl">
            <input name="soccer_team_plugin_options[qr_code_use_googl]" type="checkbox" id="soccer_team_qr_code_use_googl" value="1" <?php checked('1', $stpo['qr_code_use_googl']) ; ?> />
            Use <a href="http://goo.gl">Google's "goo.gl" URL Shortner</a> for QR Code URLs</label><br/><small>Recommended - short URLs produce easier to scan QR codes</small>
            <br/>
            <table style="border: 1px solid #eee; padding: 3px 0px 0px;">
<!--
            <thead><tr><th colspan="2">QR Code Settings</th><tr></thead>
            <tr>
            <td style="padding-left: 0px;"><label for="soccer_team_qr_code_team">
            <input name="soccer_team_plugin_options[qr_code_team]" type="checkbox" id="soccer_team_qr_code_team" value="1" <?php checked('1', $stpo['qr_code_team']) ; ?> />
            Enable QR Codes for Teams</label></td>
            <td><label for="soccer_team_qr_code_player">
            <input name="soccer_team_plugin_options[qr_code_player]" type="checkbox" id="soccer_team_qr_code_player" value="1" <?php checked('1', $stpo['qr_code_player']) ; ?> />
            Enable QR Codes for Players</label></td>
            </tr>
            <tr>
            <td style="padding-left: 0px;" colspan="2"><label for="soccer_team_qr_code_use_googl">
            <input name="soccer_team_plugin_options[qr_code_use_googl]" type="checkbox" id="soccer_team_qr_code_use_googl" value="1" <?php checked('1', $stpo['qr_code_use_googl']) ; ?> />
            Use <a href="http://goo.gl">Google's "goo.gl" URL Shortner</a> for QR Code URLs</label><br/><small>Recommended - short URLs produce easier to scan QR codes</small></td>
            </tr>-->
            <tr>
            <td style="padding-left: 0px;"><label for="soccer_team_qr_code_height">
            <input name="soccer_team_plugin_options[qr_code_height]" type="text" id="soccer_team_qr_code_height" value="<?php echo $stpo['qr_code_height'] ; ?>" /><br />
            QR Code Image Height <small>(in px)</small></label></td>
            <td><label for="soccer_team_qr_code_width">
            <input name="soccer_team_plugin_options[qr_code_width]" type="text" id="soccer_team_qr_code_width" value="<?php echo $stpo['qr_code_width'] ; ?>" /><br />
            QR Code Image Height <small>(in px)</small></label></td>
            </tr>
            <tr>
            <td style="padding-left: 0px;"><label for="soccer_team_qr_code_border">
            <select style="width: 50px;" name="soccer_team_plugin_options[qr_code_border]" id="soccer_team_qr_code_border">
            <option value="1" <?php selected($stpo['qr_code_border'], 1); ?>>1</option>
            <option value="2" <?php selected($stpo['qr_code_border'], 2); ?>>2</option>
            <option value="3" <?php selected($stpo['qr_code_border'], 3); ?>>3</option>
            <option value="4" <?php selected($stpo['qr_code_border'], 4); ?>>4</option>
            </select>
            <br />
            QR Code Border Width<br /><small>(in rows)</small></label></td>
            <td><label for="soccer_team_qr_code_quality">
            <select style="width: 50px;" name="soccer_team_plugin_options[qr_code_quality]" id="soccer_team_qr_code_quality">
            <option value="L" <?php selected($stpo['qr_code_quality'], 'L'); ?>>L</option>
            <option value="M" <?php selected($stpo['qr_code_quality'], 'M'); ?>>M</option>
            <option value="Q" <?php selected($stpo['qr_code_quality'], 'Q'); ?>>Q</option>
            <option value="H" <?php selected($stpo['qr_code_quality'], 'H'); ?>>H</option>
            </select>
            <br />
            QR Code Error Correction<br><small>(L=7%, M=15%, Q=25%, H=30%)<small></label></td>
            </tr></table>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Shortcodes</label></th>
            <td><fieldset>
            <label for="soccer_team_sc_posts">
            <input name="soccer_team_plugin_options[sc_posts]" type="checkbox" id="soccer_team_sc_posts" value="1" <?php checked('1', $stpo['sc_posts']) ; ?> />
            Enable Shortcodes for Posts and Pages</label>
            <br />
            <label for="soccer_team_sc_widgets">
            <input name="soccer_team_plugin_options[sc_widgets]" type="checkbox" id="soccer_team_sc_widgets" value="1" <?php checked('1', $stpo['sc_widgets']) ; ?> />
            Enable Shortcodes in Text Widgets</label>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>CSS</label></th>
            <td><fieldset>
            <label for="soccer_team_default_css">
            <input name="soccer_team_plugin_options[default_css]" type="checkbox" id="soccer_team_default_css" value="1" <?php checked('1', $stpo['default_css']) ; ?> />
            Enable default Soccer Team CSS</label>
            <br />
            <label for="soccer_team_custom_css">
            <input name="soccer_team_plugin_options[custom_css]" type="checkbox" id="soccer_team_custom_css" value="1" <?php checked('1', $stpo['custom_css']) ; ?> />
            Enable custom Soccer Team CSS</label>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Custom CSS</label><br/><small><i>Optional CSS styles to control the appearance of the Soccer Team widgets and Short Codes.</i></small></th>
            <td>
            <textarea class="regular-text code" name="soccer_team_plugin_options[custom_css_styles]" rows="15" cols="80"  id="soccer_team_custom_css_styles"><?php echo $stpo['custom_css_styles']; ?></textarea>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label>Donation Request</label></th>
            <td><fieldset>
            <label for="soccer_team_donation_message">
            <input name="soccer_team_plugin_options[donation_message]" type="checkbox" id="soccer_team_donation_message" value="1" <?php checked('1', $stpo['donation_message']) ; ?> />
            Hide the request for donation at the top of this page.  Donation request will remain on the <b>About</b> tab.</label>
            </fieldset></td>
        </tr>
    </table>
    <br /><br />
<?php

    //  Need to "save" the options that won't be part of this form so they
    //  don't get squashed by the save process.  We'll store them in hidden
    //  input fields.

    $saved_settings = array() ;

    //  Get the team optional fields
    $team_optional_fields = soccer_team_team_profile_meta_box_content() ;

    foreach ($team_optional_fields['fields'] as $tof)
    {
            $saved_settings[$tof['id']] = $stpo[$tof['id']] ;
    }

    //  Get the player optional fields
    $player_optional_fields = soccer_team_player_profile_meta_box_content() ;

    foreach ($player_optional_fields['fields'] as $pof)
    {
            $saved_settings[$pof['id']] = $stpo[$pof['id']] ;
    }

    //  Output all of the saved settings as hidden inputs
    foreach ($saved_settings as $key => $value)
    {
        printf('            <input type="hidden" name="soccer_team_plugin_options[%s]" value="%s" />%s', $key, $value, PHP_EOL) ;
    }
}

/**
 * soccer_team_team_profile_options()
 *
 * Build the form content and populate with any current plugin settings.
 *
 * @return none
 */
function soccer_team_team_profile_options()
{
    $stpo = soccer_team_get_plugin_options() ;
 
    //  Get Player Profile Optional Fields
    $team_optional_fields = soccer_team_team_profile_meta_box_content() ;

    //  Need to "save" the options that won't be part of this form so they
    //  don't get squashed by the save process.  We'll store them in hidden
    //  input fields.

    $saved_settings = $stpo ;

    foreach ($team_optional_fields['fields'] as $tof)
    {
        if (array_key_exists($tof['id'], $saved_settings))
            unset($saved_settings[$tof['id']]) ;
    }

?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label>Team Profile Optional Fields</label></th>
            <td><fieldset>
<?php

    foreach ($team_optional_fields['fields'] as $tof)
    {
?>
            <label for="<?php echo $tof['id'] ?>">
            <input name="soccer_team_plugin_options[<?php echo $tof['id'] ?>]" type="checkbox" id="<?php echo $tof['id'] ?>" value="1" <?php checked('1', $stpo[$tof['id']]) ; ?> />
            <?php echo $tof['desc'] ?></label><br />
<?php
    }

    //  Output all of the saved settings as hidden inputs
    foreach ($saved_settings as $key => $value)
    {
        printf('            <input type="hidden" name="soccer_team_plugin_options[%s]" value="%s" />%s', $key, $value, PHP_EOL) ;
    }
?>
            </fieldset></td>
        </tr>
    </table>
    <br /><br />
<?php
}

/**
 * soccer_player_player_profile_options()
 *
 * Build the form content and populate with any current plugin settings.
 *
 * @return none
 */
function soccer_team_player_profile_options()
{
    $stpo = soccer_team_get_plugin_options() ;
 
    //  Get Team Profile Optional Fields
    $player_optional_fields = soccer_team_player_profile_meta_box_content() ;

    //  Need to "save" the options that won't be part of this form so they
    //  don't get squashed by the save process.  We'll store them in hidden
    //  input fields.

    $saved_settings = $stpo ;

    foreach ($player_optional_fields['fields'] as $pof)
    {
        if (array_key_exists($pof['id'], $saved_settings))
            unset($saved_settings[$pof['id']]) ;
    }

?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label>Player Profile Optional Fields</label></th>
            <td><fieldset>
<?php

    foreach ($player_optional_fields['fields'] as $pof)
    {
?>
            <label for="<?php echo $pof['id'] ?>">
            <input name="soccer_team_plugin_options[<?php echo $pof['id'] ?>]" type="checkbox" id="<?php echo $pof['id'] ?>" value="1" <?php checked('1', $stpo[$pof['id']]) ; ?> />
            <?php echo $pof['desc'] ?></label><br />
<?php
    }

    //  Output all of the saved settings as hidden inputs
    foreach ($saved_settings as $key => $value)
    {
        printf('            <input type="hidden" name="soccer_team_plugin_options[%s]" value="%s" />%s', $key, $value, PHP_EOL) ;
    }
?>
            </fieldset></td>
        </tr>
    </table>
    <br /><br />
<?php
}
?>
