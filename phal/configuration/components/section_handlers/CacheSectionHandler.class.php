<?php

/**
 * This is a base class for section handlers that want to cache their processed results
 *
 */
abstract class __CacheSectionHandler implements __ISectionHandler {
    
    private $_id = null;
    
    public function __construct() {
        $this->_id = uniqid();
    }
    
    public function &process(__ConfigurationSection &$section) {
        $cache = __ApplicationContext::getInstance()->getCache();
        $return_value = $cache->getData($this->_id);
        if($return_value == null) {
            $return_value = $this->doProcess($section);
            $cache->setData($this->_id, $return_value);
        }
        return $return_value;
    }
    
    abstract public function &doProcess(__ConfigurationSection &$section);
    
}