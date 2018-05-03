<?php

/**
 * Memcache facade implementing the {@link __ICacheHandler}
 * 
 * This class requires a Memcache server as well as PECL::Memcache PHP module loaded in order to work.
 * 
 * @link http://www.danga.com/memcached/
 * @link http://pecl.php.net/package/memcache
 *
 */
class __MemCache extends __CacheHandler {

    const MEMCACHE_DEFAULT_PORT = 11211;
    const MEMCACHE_DEFAULT_SERVER = "localhost";
    
    static protected $_memcached = null;
    
    public function __destruct() {
        if (self::$_memcached != null) {
            self::$_memcached->quit();
            self::$_memcached = null;
        }
    }
    
    public function __construct() {
        $this->_connectToMemcacheServer();
    }
    
    private function _connectToMemcacheServer() {
        $phal_runtime_directives = __Phal::getInstance()->getRuntimeDirectives();
        
        //Checks if the Memcache module is loaded:
        if (!class_exists('Memcached')) {
            throw new Exception("PECL Memcache extension is not installed. Can not use the __MemCache cache handler.");
        }
        //Perform the connection to the memcache server:
        if(self::$_memcached == null) {
            self::$_memcached = new Memcached();
            if($phal_runtime_directives->hasDirective('MEMCACHE_SERVER')) {
                $server = $phal_runtime_directives->getDirective('MEMCACHE_SERVER');
            }
            else {
                $server = self::MEMCACHE_DEFAULT_SERVER;
            }
            if($phal_runtime_directives->hasDirective('MEMCACHE_PORT')) {
                $port = $phal_runtime_directives->getDirective('MEMCACHE_PORT');
            }
            else {
                $port = self::MEMCACHE_DEFAULT_PORT;
            }
            
            if (!(self::$_memcached->addServer($server, $port))) {
                throw new Exception("Can not connect to memcache server (server: $server, port: $port)");
            }
        }
    }

    public function load($key, $ttl = null) {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }        
        $return_value = null;
        @$cache_value = self::$_memcached->get($key);
        if($cache_value !== false) {
            $return_value = $cache_value;
        }
        return $return_value;
    }

    public function save($key, $data, $ttl = null) {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }            
        $return_value = self::$_memcached->set($key, $data, 0, $ttl);
        return $return_value;
    }

    public function remove($key) {
        $return_value = self::$_memcached->delete($key);
        return $return_value;
    }

    public function clear() {
        $return_value = self::$_memcached->flush();
        return $return_value;
    }
}


