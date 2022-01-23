<?php

/**
 * This class is the one in charge of cache management.
 * It can be retrieved from the context by calling to {@link __Context::getCache()} method.
 * i.e.
 * <code>
 * //get the cache from the application context:
 * $cache = __ApplicationContext::getInstance()->getCache();
 * </code>
 * 
 * This class exposes methods to set/get/load/save and clear the cache. 
 * It contains a cache handler (a class implementing the {@link __ICacheHandler} interface) 
 * that is the one in charge of handle the cache storage, so the __Cache class acts a facade in that sense.
 * 
 */
final class __Cache {

    private $_cache_data = array();
    private $_enabled = false;
    private $_cache_prefix = '';

    /**
     * @var __CacheHandler
     */
    private $_cache_handler = null;
    
    final public function __construct() {
    	$cache_enabled = __Phal::getInstance()->getRuntimeDirectives()->getDirective('CACHE_ENABLED');
    	if(class_exists('__Client') && __Client::getInstance()->getRequestType() == REQUEST_TYPE_COMMAND_LINE) {
    		$cache_enabled = false;
    	}
        $this->setEnabled($cache_enabled);
    }
    
    public function setEnabled($enabled) {
        $this->_enabled = $enabled;
        //if this is the first time we enable the cache:
        if($this->_cache_handler == null) {
            $cache_handler = __CacheHandlerFactory::createCacheHandler();
            if($cache_handler != null) {
                $this->_cache_handler =& $cache_handler;
            }            
        }
    }
    
    public function getEnabled() {
        return $this->_enabled;
    }
    
    public function isEnabled() {
        return $this->_enabled;
    }
    
    public function &getData($key, $ttl = null) {
        $key = $this->_cache_prefix . $key;
        $return_value = null;
        if(key_exists($key, $this->_cache_data)) {
            $return_value =& $this->_cache_data[$key];
        }
        else {
        	if($this->_enabled) {
                $return_value = $this->_cache_handler->load($key, $ttl);
                if($return_value != null) {
                    $this->_cache_data[$key] =& $return_value;
                }
            }
        }
        return $return_value;
    }

    public function setData($key, &$data, $ttl = null) {
        $key = $this->_cache_prefix . $key;
        $this->_cache_data[$key] =& $data;
        if($this->_enabled) {
            $this->_cache_handler->save($key, $data, $ttl);
        }
    }
    
    public function removeData($key) {
        $key = $this->_cache_prefix . $key;
        if(key_exists($key, $this->_cache_data)) {
            unset($this->_cache_data[$key]);
        }
        if($this->_enabled) {
            $this->_cache_handler->remove($key);
        }
    }

    /**
     * cache clear works even if the cache is disabled
     */
    public function clear() {
        $this->_cache_handler->clear();
    }

    public function setCachePrefix($prefix) {
        $this->_cache_prefix = $prefix;
    }

    public function getCachePrefix() {
        return $this->_cache_prefix;
    }

}
