<?php

/**
 * This is the base class for cache handlers.
 * A cache handler is a facade to the real cache library (i.e. memcache, cachelite, ...)
 * 
 * @see __CacheLite, __MemCache
 *
 */
abstract class __CacheHandler implements __ICacheHandler {
    
    /**
     * ttl = 0 is the default value (never expire)
     *     
     */
    protected $_default_ttl = 0;

    public function setDefaultTtl($default_ttl) {
        if(is_numeric($default_ttl)) {
            $this->_default_ttl = (int)$default_ttl;
        }
        else {
            throw new Exception('Wrong value for default cache ttl: ' . $default_ttl);
        }
    }
    
    public function getDefaultTtl() {
        return $this->_default_ttl;
    }
    
}