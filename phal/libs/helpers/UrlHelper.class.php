<?php

class __UrlHelper {
    
    /**
     * Resolves an url depending on the specified base. 
     * If no base is specified, the APP_URL_PATH will be used as base.
     * 
     * Note: Absolute url won't be altered.
     * The same if applicable to an url starting with the '/' character if no base is specified.
     * 
     * i.e.
     * <code>
     * echo __UrlHelper::resolveUrl('path/to/foo.php', 'base/url/');
     * --> /base/url/path/to/foo.php
     * 
     * echo __UrlHelper::resolveUrl('path/to/foo.php', 'http://domain.com/');
     * --> http://domain.com/path/to/foo.php
     * 
     * echo __UrlHelper::resolveUrl('path/to/foo.php');
     * --> /base/url/path/to/foo.php (being APP_URL_PATH = "/base/url/")
     * 
     * echo __UrlHelper::resolveUrl('/path/to/foo.php');
     * --> /path/to/foo.php
     * </code>
     * 
     * @param string $url The url to resolve to
     * @param string $base A base path to apply to
     * @return string The resultant url
     */
    static public function resolveUrl($url, $base = null) {
        if(preg_match('/^\w+\:\/\//', $url)) {
            return $url; 
        }
        else if(!empty($base)) {
            $return_value = self::glueUrlParts($base, $url);
            if(!preg_match('/^\w+\:\/\//', $return_value)) {
                $return_value = '/' . $return_value;
            }            
        }
        else if(substr($url, 0, 1) != '/') {
            if(defined("APP_URL_PATH")) {
                $url_path = APP_URL_PATH;
            }
            else {
                $url_path = __ApplicationContext::getInstance()->getPropertyContent('APP_URL_PATH');
            }           
            $return_value = '/' . self::glueUrlParts($url_path, $url);
        }
        else {
            $return_value = $url;
        }
        return $return_value;
    }
    
    static public function glueUrlParts($base, $url) {
        if(preg_match('/^\w+\:\/\//', $url)) {
            return $url; 
        }
        $base = trim($base, '/');
        $return_value = ltrim($url, '/');
        if(!empty($base)) {
            $return_value = $base . '/' . $return_value;
        }
        return $return_value;        
    }
    
    
}