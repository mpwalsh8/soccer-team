=== Plugin Name ===
Contributors: mpwalsh8
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DK4MS3AA983CC
Tags: sports, team, soccer, roster
Requires at least: 3.3
Tested up to: 3.4.1
Stable tag: trunk

Soccer-Team is a plugin to which adds functionalty to WordPress to faciliate using WordPress for a high level youth soccer team web site.

== Description ==

Soccer-Team is a plugin to facilitate using WordPress for a soccer team web site.  This plugin defines custom post types for players and teams and custom taxonomies for positions and rosters.  The custom post types and taxonomies allow for a site which has a page and menu structure to highlight each team and player as well as numerous fields which can be enabled that are typically found in player and team profiles.  Soccer-Team is targeted at teams which play under [US Development Academy](http://www.ussoccer.com/Teams/Development-Academy/Academy.aspx), [Elite Clubs National League](http://www.eliteclubsnationalleague.com), and [US Youth Soccer](http://www.usyouthsoccer.org/).  However, the Soccer-Team plugin can be used for any soccer team or club looking to establish a web site based on WordPress.

The Soccer-Team plugin supports the following fields for a Team:

- Head Coach's Name (required)
- Head Coach's E-mail Address
- Other Coach's Name
- Other Coach's E-mail Address
- Manager's Name
- Manager's E-mail Address
- Team's Got-Soccer.com Profile URL
- Team's Soccer-In-College.com Profile URL
- Team's Downloadable Profile
- Team Mailing Address include City, State, and Zip Code
- Team's Primary Phone Number
- Privacy Controls
- Team Status (required)

The Soccer-Team plugin supports the following fields for a Player:

- Player's Jersey Number (required)
- Mailing Address include City, State, and Zip Code
- Player's Primary Phone Number
- Player's Primary E-mail Address
- Player's Date of Birth - use Month Day, Year format
- Mother's Name and E-mail Address
- Father's Name and E-mail Address
- Player's Height
- Player's Weight
- Player's Primary Foot (required)
- Player's School
- Player's Graduation Year
- Grade Point Average
- Privacy Controls (required)
- Player Status (required)
- Player's Got-Soccer.com Profile URL
- Player's Soccer-In-College.com Profile URL
- Player's Downloadable Profile

All Team and Player fields can be optionally enabled or disabled except those noted as required.  Additionally, Soccer-Team supports a global setting to generate and include QR Codes for Teams and/or Players.  QR Codes are generated using Google's Chart API and can be saved locally or included on other materials (e.g. a team flyer).  If QR codes are enabled, it is recommended to use Google's [goo.gl](http://goo.gl) URL shortner as QR codes for short URLs have fewer scanning issues.  Access to Google's URL shortner requires the WordPress HTTP API to be working.

[Demo](http://michaelwalsh.org/wordpress/wordpress-plugins/soccer-team/)

== Installation ==

1. Install using the WordPress Pluin Installer (search for `Soccer-Team`) or download `Soccer-Team`, extract the `soccer-team` folder and upload `soccer-team` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure `Soccer-Team` from the `Settings` menu as appropriate.
1. Add team(s) and player(s) as approproate for your application using the Dashboard Menus.
1. Use the various `Soccer-Team` shortcode wherever you'd like to insert content into your site's posts, pages, or widgets.

== Usage ==

The primary usage model of the Soccer-Team plugin is to use the WordPress Dashboard to define teams and players and their respective roster and position taxonmy entries.  Once defined, players and teams and their associated taxonomies can be used to add menu entries.

The Soccer-Team plugin also defines a number of WordPress short codes which allow the dispay of Team and Player data on posts or pages.  The following shortcodes are defined:

- [st-team-roster roster='&lt;id&gt;']
- [st-team-profile  team='&lt;id&gt;']
- [st-player-profile  player='&lt;id&gt;']
- [st-players-gallery  roster='&lt;id&gt;']
- [st-rosters]
- [st-positions]

The '&lt;id&gt;' value in short codes above is the numeric id of the Player, Team, or Roster entry which can be identified when editing the approoriate item through the WordPress Dashboard.

== Frequently Asked Questions ==

= The default style is ugly. Can I change it? =
Yes, there are two ways to change the style (aka apearance) of the form.

1. By adding the necessary CSS to your theme's style sheet.
1. Through the WordPress Soccer-Team custom CSS setting.

Soccer-Teams include plenty of [CSS](http://en.wikipedia.org/wiki/Cascading_Style_Sheets) hooks. Refer to the **CSS** section for further details on styling the form.  There are also some CSS solutions posted to questions users have raised in the Tips and Tricks section of [this page](http://michaelwalsh.org/wordpress/wordpress-plugins/wpgform/tips-and-tricks/).

= Why do I get a 403 error? =

There a number of reasons to get a 403 error but by far the most common one encountered so far is due to ModSecurity being installed by your web hosting provider.  Not all providers deploy ModSecurity but enough do that it comes up every once in a while.  If your provider is running ModSecurity and your version of the plugin is v0.30 or lower, you will likely see odd behavior where when the form is submitted, the page is simply rendered again and the data is never actually sent to Google.  There isn't any error message to indicate what might be wrong.

Version 0.31 fixes this problem for *most* cases but there is still a chance that it could crop up.  If your provider has enabled ModSecurity AND someone answers one of the questions on your form with a URL (e.g. http://www.example.com), then very likely ModSecurity will kick in an issue a 403 error.  The plugin is now smart enough to detect when the error is issued and let you know what is wrong.  Unfortunately there isn't currently a solution to allow URLs as responses when ModSecurity issues a 403 error.

= No matter what I do, I always get the "Unable to retrieve Soccer-Team.  Please try reloading this page." error message.  Why is this? =

1. The most common reason for this error is from pasting the Soccer-Team URL into the WordPress WYSIWYG Editor while in "Visual" mode.  When you paste the URL, the Visual Editor recognizes at a link and wraps the text in the apprpriate HTML tags so the link will work.   Visually you'll trypically see the URL in a different color than the rest of the short code text.  If this happens, simply click anywhere in the link and use the "Break Link" icon (broken chain) on the tool bar to remove the link.  The other alternative is to toggle to HTML mode and manually remove the HTML which is wrapped around the URL.

1. Validate that the WordPress HTTP API is working correctly.  If you are seeing HTTP API errors on the WordPress Dashboard or when you attempt to access the plugin repository through the Dashboard, the Soccer-Team will likely fail too.  It requires the WordPress HTTP API to be working.  With some free hosting plans, ISPs disable the ability to access remote content.

= Do you have a demo running? =
Yes, see a demo here:  [Demo of Soccer-Team plugin](http://michaelwalsh.org/wordpress/wordpress-plugins/soccer-team/)

== CSS ==

As of 2011-09-21, Soccer-Teams make use of 20+ CSS class definitions.  By default, the Soccer-Team plugin includes CSS declarations for all of the classes however the bulk of them are empty.  The default CSS sets the font and makes the entry boxes wider.  The default CSS that ships with Soccer-Team can optionally be turned off via the Soccer-Team settings.

= Customizing Soccer-Team CSS =

There are two ways to customize the Soccer-Team CSS.

1.  The Soccer-Team plugin includes a setting to include custom CSS and a field where custom CSS can be entered.  This CSS will be preserved across themes.
1.  Add custom CSS declarations to your WordPress theme.

= Default Soccer-Team CSS =

As of 2012-08-16, the following is are the CSS classes which Soccer-Teams make use of.  The CSS below represents the default CSS provided by Soccer-Team.  These CSS definitions can be copied and pasted into your theme CSS or the Soccer-Team custom CSS setting and changed as desired.

`
.st-players td.st-player-jersey-number {
    font-size: 1.5em;
}

.st-players .st-player-jersey-number,
.st-players .st-player-photo,
.st-players .st-player-name,
.st-players .st-player-position,
.st-players .st-player-profile {
    text-align: center;
    vertical-align: middle;
}

th.st-team-profile-attachment,
td.st-team-profile-attachment,
th.st-player-profile-attachment,
td.st-player-profile-attachment {
    vertical-align: middle ;
}
`

In addition to the CSS above, the Player Carousel defines the following default CSS:

`
div#st-player-gallery-carousel {
    width: 570px;
    height: 380px;
    background: url(../images/PlayerGalleryBg.jpg);
    overflow: scroll;
}

div#st-pgc-alt, div#st-pgc-title {
    color: white;
    font-weight: bold;
    font-size: 1.8em;
    padding-left: 10px;
}

div#st-pgc-alt {
    font-size: 1.2em;
}

/****************************************/ 

.st-pgc-left-button {
    display:none;
    background:url(../images/carousel/rotate-left.png);
    width:40px; height:40px;
    background-position: 0px 0px;
    position:absolute;top:20px;right:64px;
}

.st-pgc-left-button:hover {
    width:40px; height:40px;
    background-position: 0px 40px;
    cursor:auto;
}

.st-pgc-right-button {
    display:none;
    background:url(../images/carousel/rotate-right.png);
    width:40px; height:40px;
    background-position: 0px 0px;
    position:absolute;top:20px;right:20px;
}

.st-pgc-right-button:hover {
    width:40px; height:40px;
    background-position: 0px 40px;
}
`

All of the Soccer-Team generated content is output with CSS class names to faciliate styling the output to match your theme.  The following styles are used:

- st-pgc-left-button
- st-pgc-right-button
- st-player-header-row
- st-player-jersey-number
- st-player-name
- st-player-photo
- st-player-position
- st-player-private-profile
- st-player-profile
- st-player-profile-attachment
- st-player-profile-details
- st-player-profile-header
- st-player-profile-row
- st-player-qr-code
- st-player-roster
- st-player-row
- st-players
- st-players-not-found
- st-team-photo
- st-team-private-profile
- st-team-profile
- st-team-profile-attachment
- st-team-profile-details
- st-team-profile-header
- st-team-profile-row
- st-team-qr-code

In addition to the above styles, there are several more styles which are derived from the enabled fields.  All will have the *st-team-* or *st-player-* prefix.  Use a tool like [FireBug](http://www.getfirebug.com) to identify additional styles and what element(s) they apply to.


== Screenshots ==

1. Plugin Settings
1. Team Settings
1. Player Settings
1. Team Edit Screen
1. Player Edit Screen
1. Team Profile Page
1. Player Profile Page
1. Roster Page
1. Player Carousel

== Upgrade Notice ==

No known upgrade issues.

== Changelog ==

= Version 0.5-beta =

* Initial pubic release.
