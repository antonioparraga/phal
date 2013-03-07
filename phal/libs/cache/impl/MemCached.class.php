<?php

/**
 * Memcached facade implementing the {@link __ICacheHandler}
 * 
 * This class requires a Memcached server as well as PECL::Memcached PHP module loaded in order to work.
 * 
 */
class __MemCached extends __CacheHandler {

    const MEMCACHED_DEFAULT_PORT = 11211;
    const MEMCACHED_DEFAULT_SERVER = "localhost";
    
    static protected $_memcached = null;
    
    public function __destruct() {
        if (self::$_memcached != null) {
            self::$_memcached = null;
        }
    }
    
    public function __construct() {
        $this->_connectToMemcachedServer();
    }
    
    private function _connectToMemcachedServer() {
        $phal_runtime_directives = __Phal::getInstance()->getRuntimeDirectives();
        
        //Checks if the Memcached module is loaded:
        if (!class_exists('Memcached')) {
            throw new Exception("PECL Memcached extension is not installed. Can not use the __MemCached cache handler.");
        }
        //Perform the connection to the memcached server:
        if(self::$_memcached == null) {
            self::$_memcached = new Memcached();
            if($phal_runtime_directives->hasDirective('MEMCACHED_SERVER')) {
                $server = $phal_runtime_directives->getDirective('MEMCACHED_SERVER');
            }
            else {
                $server = self::MEMCACHED_DEFAULT_SERVER;
            }
            if($phal_runtime_directives->hasDirective('MEMCACHED_PORT')) {
                $port = $phal_runtime_directives->getDirective('MEMCACHED_PORT');
            }
            else {
                $port = self::MEMCACHED_DEFAULT_PORT;
            }
            if (self::$_memcached->addServer($server, $port)) {
                self::$_memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);                
            }
            else {
                throw new Exception("Can not connect to memcached server (server: $server, port: $port)");
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
        $return_value = self::$_memcached->set($key, $data, $ttl);
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


