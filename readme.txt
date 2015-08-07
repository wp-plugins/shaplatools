=== ShaplaTools ===
Contributors: sayfulit, sayful
Tags: custom post type, widget, shortcode, twitter, images, image
Requires at least: 3.8
Tested up to: 4.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

ShaplaTools is a powerful plugin to extend functionality to your WordPress themes.

== Description ==

ShaplaTools is a powerful plugin to extend functionality to your WordPress themes offering shortcodes, FontAwesome icons, Autocomplete search suggestion, Retina graphics for your website and useful widgets.

For documentation, please visit <a href="http://sayfulit.github.io/shaplatools/">GitHub Page</a>

= Custom Post Types =

<ul>
	<li>Slider (for jQuery NIVO Slider)</li>
	<li>Testimonial</li>
	<li>Portfolio</li>
	<li>Team</li>
	<li>Features</li>
</ul>

= Widgets =

<ul>
	<li>Facebook Like Box</li>
	<li>Latest Tweets (PHP 5.3 or higher, cURL required)</li>
	<li>Dribbble Shots</li>
	<li>Flickr Photos</li>
	<li>Instagram Photos</li>
	<li>Ajax Contact Form</li>
	<li>Testimonial (For Testimonial Custom Post Type)</li>
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

= How to add custom post types in my theme? =

You can enable custom post types in your theme by option page. Go to <b>Settings -> ShaplaTools</b> and then click on "Custom Post Types" tab and check which custom post types you want to enable for your theme.
If you want to include these custom post types in your theme, you can add the following function in your theme function.php file.
For slide: <code>if (function_exists(\'run_shaplatools_slide\')) run_shaplatools_slide();</code><br>
For portfolio: <code>if (function_exists(\'run_shaplatools_portfolio\')) run_shaplatools_portfolio();</code><br>
For team: <code>if (function_exists(\'run_shaplatools_team\')) run_shaplatools_team();</code><br>
For testimonial: <code>if (function_exists(\'run_shaplatools_testimonial\')) run_shaplatools_testimonial();</code><br>
For feature: <code>if (function_exists(\'run_shaplatools_feature\')) run_shaplatools_feature();</code><hr>

To enable default meta you can write the following function.<br>
For slide meta:<code>if (function_exists(\'run_shaplatools_nivoslide_meta\')) run_shaplatools_nivoslide_meta();</code><br>
For portfolio meta:<code>if (function_exists(\'run_shaplatools_portfolio_meta\')) run_shaplatools_portfolio_meta();</code><br>
For team meta:<code>if (function_exists(\'run_shaplatools_team_meta\')) run_shaplatools_team_meta();</code><br>
For testimonial meta:<code>if (function_exists(\'run_shaplatools_testimonial_meta\')) run_shaplatools_testimonial_meta();</code><br>
For feature meta:<code>if (function_exists(\'run_shaplatools_feature_meta\')) run_shaplatools_feature_meta();</code>

== Changelog ==

= version 1.0.0 =

* Initial release 


== CONTACT ==

[Sayful Islam](http://sayful1.wordpress.com/100-2/)