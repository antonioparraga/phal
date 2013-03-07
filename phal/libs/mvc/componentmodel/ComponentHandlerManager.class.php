<?php

/**
 * This class contains all the in-session {@link __ComponentHandler} instances.
 * 
 * It exposes a method to retrieve a {@link __ComponentHandler} instance associated to a given view
 *
 */
final class __ComponentHandlerManager {
    
    static private $_instance = null;
    
    private $_component_handlers = array();
        
    private function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__ComponentHandlerManager::_component_handlers')) {
            $this->_component_handlers =& $session->getData('__ComponentHandlerManager::_component_handlers');
        }
        else {
            $session->setData('__ComponentHandlerManager::_component_handlers', $this->_component_handlers);
        }
    }
    
    /**
     * Gets the __ComponentHandlerManager singleton instance
     *
     * @return __ComponentHandlerManager
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ComponentHandlerManager();
        }
        return self::$_instance;
    }
    
    /**
     * Checks if there is contained a {@link __ComponentHandler} instance associated to a given view
     *
     * @param string $view_code The view code
     * @return bool
     */
    public function hasComponentHandler($view_code) {
        $view_code = strtoupper($view_code);
        return key_exists($view_code, $this->_component_handlers);
    }

    public function &createComponentHandler($view_code) {
        $view_code = strtoupper($view_code);
        $return_value = new __ComponentHandler($view_code);
        $this->_component_handlers[$view_code] =& $return_value;
        return $return_value;
    }
    
    public function &addComponentHandler(__ComponentHandler &$component_handler) {
        $view_code = strtoupper($component_handler->getViewCode());
        $this->_component_handlers[$view_code] =& $component_handler;
        return $component_handler;
    }    
    
    /**
     * Delete a component handler and all his components from the component pool.
     * It also removes the associated event handler if any
     *
     * @param string $view_code
     */
    public function freeComponentHandler($view_code) {
        $upper_case_view_code = strtoupper($view_code);
        if(key_exists($upper_case_view_code, $this->_component_handlers)) {
            $component_handler = $this->_component_handlers[$upper_case_view_code];
            $component_handler->freeComponents();
            unset($this->_component_handlers[$upper_case_view_code]);
            __EventHandlerManager::getInstance()->removesEventHandler($view_code);
        }
    }
    
    
    /**
     * Gets a {@link __ComponentHandler} instance corresponding to a given view code.
     * 
     * If the requested {@link __ComponentHandler} does not exists, it creates a new one.
     *
     * @param string $view_code The view code
     * @return __ComponentHandler
     */
    public function &getComponentHandler($view_code) {
        $return_value = null;
        $view_code = strtoupper($view_code);
        if(key_exists($view_code, $this->_component_handlers)) {
            $return_value = $this->_component_handlers[$view_code];        
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Component handler not found for view code: ' . $view_code);
        }
        return $return_value;
    }
    
}