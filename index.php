<?php

  /**
   * Plugin Name: Google-Fonts-Wrapper
   * Plugin URI: https://wordpress.org/plugins/wrap-google-fonts/
   * Description: Downloads Google Fonts to your server
   * Version: 0.4.2
   * Author: Jinx Digital <hello@jinx-digital.com>
   * Author URI: http://jinx-digital.com
   * License: GPL2+
   * Text Domain: google-fonts-wrapper
   */

  require_once(__DIR__.'/GoogleFontsWrapper.php');
  
  if (preg_match('/\/(css\d?)\?/', $_SERVER['REQUEST_URI'], $matches)) {
    
    list(, $css) = $matches;
    
    $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    
    GoogleFontsWrapper::run($query, $css);

  } else {
     
    register_activation_hook(__FILE__, array('GoogleFontsWrapper', 'install'));
      
    add_filter('style_loader_tag', array('GoogleFontsWrapper', 'rewriteStyleTags'), 0, 4);
  
    add_filter('wp_resource_hints', array('GoogleFontsWrapper', 'removeDnsPrefetch'), 0, 2);
    
    add_action('admin_bar_menu', array('GoogleFontsWrapper', 'addAdminBar'), 999);
    
    add_Action('admin_init', array('GoogleFontsWrapper', 'adminInit'));
    
  }
  
  