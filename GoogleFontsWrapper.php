<?php

  /**
   * GoogleFontsWrapper
   * 
   * @license GPL2+
   * @author SquareFlower Websolutions (Lukas Rydygel) <hallo@squareflower.de>
   * @version 0.3.0
   * @since 0.2.0
   */

  abstract class GoogleFontsWrapper
  {
    
    const CACHE_PREFIX = 'font_';
    const CACHE_DIR = 'cache';
    const FONTS_DIR = 'fonts';
    
    const GOOGLE_FONTS_URL = 'fonts.googleapis.com';
    
    /**
     * Run the plugin
     * 
     * @param string $query
     * @param string $css
     */
    public static function run($query, $css = 'css2')
    {
      
      $browser = self::getBrowser();

      $cacheFile = self::getPath(self::CACHE_DIR).self::CACHE_PREFIX.md5($query.'|'.$browser);
      
      if (!file_exists($cacheFile)) {
        
        $css = self::import('https://'.self::GOOGLE_FONTS_URL.'/'.$css.'?'.$query);
        
        file_put_contents($cacheFile, $css);

      }
      
      self::serveCssFile($cacheFile);
      
    }
    
    /**
     * Imports fonts from url
     * 
     * @param string $url
     * @return string
     */
    protected static function import($url)
    {
      
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      
      if (isset($_SERVER['HTTP_USER_AGENT'])) {
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
      }
      
      $response = curl_exec($ch);
      
      return preg_replace_callback('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', function($matches) {

        $basename = pathinfo($matches[0], PATHINFO_BASENAME);
        
        $path = self::getPath(self::FONTS_DIR);
        $relativePath = str_replace(__DIR__, '', $path);

        $fontFile = $path.$basename;

        if (!file_exists($fontFile)) {
          file_put_contents($fontFile, file_get_contents($matches[0]));
        }

        list($uri, ) = explode('?', $_SERVER['REQUEST_URI']);
        
        $uri = preg_replace('/\/(css\d?)/', '', $uri);

        return rtrim($uri, '/').$relativePath.$basename;

      }, $response); 
      
    }
    
    /**
     * Serve css file
     * 
     * @param type string
     */
    protected static function serveCssFile($path)
    {
      
      header('Content-type: text/css');
      echo file_get_contents($path);
      exit();
      
    }
    
    /**
     * Rewrite url of style tags
     * 
     * @param string $html
     * @param string $handle
     * @param string $href
     * @param string $media
     * @return string
     */
    public static function rewriteStyleTags($html, $handle, $href, $media)
    {
            
      if (preg_match('/'.self::GOOGLE_FONTS_URL.'\/(css\d?)/', $href, $matches)) {
        
        list(, $css) = $matches;

        $url = plugins_url($css, __FILE__);
        
        list(, $query) = explode('?', $href);

        $html = str_replace($href, $url.'?'.$query, $html);

      }

      return $html;
      
    }
    
    /**
     * Remove the DNS prefetch for matching fonts
     * 
     * @param array $urls
     * @param string $type
     * @return array
     */
    public static function removeDnsPrefetch($urls, $type)
    {
      
      if ($type === 'dns-prefetch') {
        
        $index = array_search(self::GOOGLE_FONTS_URL, $urls);
        if ($index !== false) {
          unset($urls[$index]);
        }
        
      }
      
      return $urls;
      
    }
    
    /**
     * Install the plugin and it's required directories
     */
    public static function install()
    {
      
      self::installDir(self::getPath(self::FONTS_DIR));
      self::installDir(self::getPath(self::CACHE_DIR), self::CACHE_PREFIX);
      
    }
    
    /**
     * Admin init
     */
    public static function adminInit()
    {
      
      if (isset($_GET['google-fonts-wrapper'])) {
        
        $action = sanitize_text_field($_GET['google-fonts-wrapper']);
        
        switch ($action) {
          
          case 'flush':
            
            self::install();
            
            add_action('admin_notices', function() { 
              echo '<div class="notice notice-success is-dismissible"><p>'.__('Fonts have been flushed.', 'google-fonts-wrapper').'</p></div>';
            });
            
          break;
          
        } 
        
      }
      
    }
    
    /**
     * Add button to the admin bar
     * 
     * @param WP_Admin_Bar $adminBar
     */
    public static function addAdminBar($adminBar)
    {
          
      $adminBar->add_node([
        'id' => 'wrap-google-fonts',
        'title' => '<span class="ab-icon dashicons dashicons-trash"></span> '.__('Flush fonts', 'google-fonts-wrapper'),
        'href' => '?google-fonts-wrapper=flush'
      ]);
      
    }
    
    /**
     * Create and empty directory by using a file prefix
     * 
     * @param string $dir
     * @param string $prefix
     */
    protected static function installDir($dir, $prefix = '')
    {
      
      if (is_dir($dir)) {
        
        foreach (glob($dir.'/'.$prefix.'*') as $file) {
          unlink($file);
        }
        
      } else {
        @mkdir($dir);
      }
      
    }
    
    /**
     * Get browser informations
     * 
     * @return string;
     */
    protected static function getBrowser()
    {
      
      $browser = get_browser(null, true);
      
      return $browser['platform'].';'.(isset($browser['parent']) ? $browser['parent'] : 0);
      
    }
    
    /**
     * Get path for directory
     * 
     * @param string $dir
     * @return string
     */
    protected static function getPath($dir)
    {
      return __DIR__.'/../../'.$dir.'/';       
    }
    
  }