<?php

/**
 * This is the container for cache handlers.
 * 
 * This class is used by the context container in order to retrieve its cache.
 * 
 */
final class __CacheManager {

    static private $_instance = null;
    
    private $_cache = null;

    private function __construct() {
        $this->_cache = new __Cache();
    }
    
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __CacheManager();
        }
        return self::$_instance;
    }
    
    public function &getCache() {
        return $this->_cache;
    }
    
}
