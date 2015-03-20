=== ShaplaTools ===
Contributors: sayful
Tags: custom post type, widget, shortcode, twitter, images, image
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

ShaplaTools is a powerful plugin to extend functionality to your WordPress themes.

== Description ==

ShaplaTools is a powerful plugin to extend functionality to your WordPress themes.

This plugin is actually for theme developer to include some features in WordPress theme. No implementation is available with this plugin.  So if you are not a WordPress developer, this plugin is not for you.

= Custom Post Types =

<ul>
	<li>Slider (for jQuery NIVO Slider)</li>
	<li>Testimonial</li>
	<li>Portfolio</li>
	<li>Event</li>
	<li>Team</li>
</ul>

= Widgets =

<ul>
	<li>Facebook Like Box</li>
	<li>Latest Tweets</li>
	<li>Dribbble Shots</li>
	<li>Flickr Photos</li>
	<li>Instagram Photos</li>
	<li>Testimonial (For Testimonial Custom Post Type)</li>
	<li>Event (For Event Custom Post Type)</li>
</ul>

= Shortcodes =

<ul>
	<li>Alerts</li>
	<li>Buttons ( optionally, with font icons )</li>
	<li>Columns</li>
	<li>Divider / Horizontal Ruler</li>
	<li>Dropcaps</li>
	<li>Intro Text</li>
	<li>Tabs</li>
	<li>Toggle</li>
	<li>Font Icons by Font Awesome</li>
	<li>Google Maps with 5 predefined styles</li>
	<li>Image with CSS3 filters</li>
	<li>Videos ( supports Embeds )</li>
	<li>Filterable Portfolio (for Portfolio post type)</li>
	<li>Slide (jQury NIVO Slider for Slider post type)</li>
</ul>

== Installation ==

Installing the plugins is just like installing other WordPress plugins. If you don't know how to install plugins, please review the three options below:

= Install by Search =

* From your WordPress dashboard, choose 'Add New' under the 'Plugins' category.
* Search for 'ShaplaTools' a plugin will come called 'ShaplaTools by Sayful Islam' and Click 'Install Now' and confirm your installation by clicking 'ok'
* The plugin will download and install. Just click 'Activate Plugin' to activate it.

= Install by ZIP File =

* From your WordPress dashboard, choose 'Add New' under the 'Plugins' category.
* Select 'Upload' from the set of links at the top of the page (the second link)
* From here, browse for the zip file included in your plugin titled 'shaplatools.zip' and click the 'Install Now' button
* Once installation is complete, activate the plugin to enable its features.

= Install by FTP =

* Find the directory titles 'shaplatools' and upload it and all files within to the plugins directory of your WordPress install (WORDPRESS-DIRECTORY/wp-content/plugins/) [e.g. www.yourdomain.com/wp-content/plugins/]
* From your WordPress dashboard, choose 'Installed Plugins' option under the 'Plugins' category
* Locate the newly added plugin and click on the \'Activate\' link to enable its features.


== Frequently Asked Questions ==

= How can I add Custom Post Types in my theme? =

To add Custom Post Types in your theme go to your theme functions.php file and add the following code:
<pre><code>
add_theme_support( 'shapla-portfolio' );
add_theme_support( 'shapla-testimonials' );
add_theme_support( 'shapla-event' );
add_theme_support( 'shapla-slides' );
add_theme_support( 'shapla-team' );
</code></pre>

== Changelog ==

= version 1.0.0 =

* Initial release 


== CREDIT ==

1.This plugin was developed by [Sayful Islam](http://sayful.net)

== CONTACT ==

[Sayful Islam](http://sayful1.wordpress.com/100-2/)