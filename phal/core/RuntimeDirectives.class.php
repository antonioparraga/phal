<?php

/**
 * Runtime directives are the most basic settings in which the framework works with.
 *
 */
final class __RuntimeDirectives {
    
    private $_directives = array();
    const DIRECTIVES_FILENAME = 'phal.ini';
    
    public function __construct() {
        $this->_loadDirectives();
    }
    
    private function _loadDirectives() {
        if(defined('DIRECTIVES_PATH')) {
            $phal_ini_file = DIRECTIVES_PATH . DIRECTORY_SEPARATOR . self::DIRECTIVES_FILENAME;
            if(file_exists($phal_ini_file)) {
                $this->_directives = parse_ini_file($phal_ini_file);
            }
            else {
                throw new Exception('Can not find phal.ini file on ' . DIRECTIVES_PATH . ' (defined as DIRECTIVES_PATH constant)');
            }
        }
        else {
            $directive_search_paths = array(APP_DIR, DEFAULT_CONFIGURATION_DIR);
            
            foreach($directive_search_paths as $directive_search_path) {
                $phal_ini_file = $directive_search_path . DIRECTORY_SEPARATOR . self::DIRECTIVES_FILENAME;
                if(file_exists($phal_ini_file)) {
                    $this->_directives = parse_ini_file($phal_ini_file);
                    return;
                }
            }
            
            throw new Exception('Can not find phal.ini (search paths: ' . implode(', ', $directive_search_paths) . ')');
        }
            
    }
    
    public function getDirective($key) {
        $return_value = null;
        if(key_exists($key, $this->_directives)) {
            $return_value = $this->_directives[$key];
        }
        return $return_value;
    }
    
    public function getDirectives() {
        return $this->_directives;
    }
    
    public function hasDirective($key) {
        return key_exists($key, $this->_directives);
    }
    
    public function getCacheDirectory() {
        $cache_dir = $this->getDirective('CACHE_FILE_DIR') . DIRECTORY_SEPARATOR;
        if( preg_match( '/^\//', $cache_dir ) || preg_match('/^\w+:/', $cache_dir)) {
            $return_value = $cache_dir;
        }
        else {
            $return_value = APP_DIR . DIRECTORY_SEPARATOR . $cache_dir;
        }
        return $return_value;
    }
        
}