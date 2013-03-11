<?php

/**
 * This is the interface associated to a cache handler
 * 
 * @see __CacheHandler
 *
 */
interface __ICacheHandler {
    
    /**
     * Loads data from cache
     *
     * @param unknown_type $cache_id
     * @param unknown_type $context_id
     * @return mixed
     */
    public function load($key, $ttl = null);
    
    /**
     * Saves data to cache
     *
     * @param mixed $data
     * @param string $cache_id
     * @param string $context_id
     * @return bool true if the data has been saved successfully
     */
    public function save($key, $data, $ttl = null);
    
    /**
     * Removes data from cache
     *
     * @param string $cache_id
     * @param string $context_id
     * @return bool true if the data has been removed successfully
     */
    public function remove($key);
    
    /**
     * Clear the entire cache
     *
     * @param string $context_id
     * @return bool true if the cache has been cleaned successfully
     */
    public function clear();
    
    public function setDefaultTtl($ttl);
    
    public function getDefaultTtl();
    
}