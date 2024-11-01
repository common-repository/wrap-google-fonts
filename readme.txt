=== Google-Fonts-Wrapper ===
Contributors: Lugat
Tags: googlefonts, fonts, dsgvo, webfonts, privacy, import, download
Requires at least: 4.0
Tested up to: 6.3.2
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
The plugin will automatically parse all registered styles and import the ressources from Google to your server and load them.

== Description ==

The plugin will automatically parse all registered styles and import the ressources from Google to your server and load them.

== Installation ==

1. Unzip the downloaded package
2. Upload `wp-google-fonts-wrapper` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Usage ==

Simply include the fonts from Google by using `wp_enqueue_scripts`.

The plugin will parse the URL from the enqueued stylesheets and detect front from Google.

The files will be downloaded to your server and the CSS will be changed to match the new paths.

You may flush the fonts from your server using the button in the admin bar.

== Notes ==

When using multiple fonts in one style-tag, please make sure to use no load the script without a version, to avoid WordPress from normalizing the URL.

`wp_enqueue_style('...', '...', [], null);`

As Google will accept the parameter `family` multiple times, WordPress will merge them into one.

Be aware, that some caching plugins which also provide minification, may cause this plugin to break, as the fonts will be parsed before the plugin hook.