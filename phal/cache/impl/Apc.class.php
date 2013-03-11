<?php

/**
 * Apc facade implementing the {@link __ICacheHandler}
 * 
 * This class requires APC extension enabled in order to work.
 * 
 * @link http://www.php.net/apc
 *
 */
class __Apc extends __CacheHandler {
    
    public function load($key, $ttl = null) {
        $return_value = apc_fetch($key);
        if($return_value === false) {
            $return_value = null;
        }
        return $return_value;
    }

    public function save($key, $data, $ttl = null) {
        if($ttl == null || !is_numeric($ttl)) {
            $ttl = $this->_default_ttl;
        }          
        $return_value = apc_store ($key, $data, $ttl);
        return $return_value;
    }

    public function remove($key) {
        $return_value = apc_delete ( $key );
        return $return_value;
    }

    public function clear() {
        $return_value = apc_clear_cache();
        return $return_value;
    }
}


