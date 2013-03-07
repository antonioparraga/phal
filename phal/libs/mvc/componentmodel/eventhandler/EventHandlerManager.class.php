<?php

class __EventHandlerManager {
    
    private static $_instance = null;
    
    private $_event_handlers = array();
    
    private function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__EventHandlerManager::_event_handlers')) {
            //do not remove the '&'. For any unknown reason, without the & the event handlers are not assigned by reference
            $this->_event_handlers =& $session->getData('__EventHandlerManager::_event_handlers');
        }
        else {
            $this->_event_handlers = array();
            $session->setData('__EventHandlerManager::_event_handlers', $this->_event_handlers);
        }
    }
        
    /**
     * Enter description here...
     *
     * @return __EventHandlerManager
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __EventHandlerManager();
        }
        return self::$_instance;
    }
    
    /**
     * Returns the event handler associated to a given view.
     *
     * @param string $view_code Case insensitive view code that identifies the view
     * @return __IEventHandler The requested event handler
     */
    public function &getEventHandler($view_code) {
        $view_code = strtoupper($view_code);
        if(!key_exists($view_code, $this->_event_handlers)) {
            $return_value = __EventHandlerFactory::getInstance()->createEventHandler($view_code);
            $this->addEventHandler($return_value);
        }
        else {
            $return_value =& $this->_event_handlers[$view_code];
        }
        return $return_value;
    }

    /**
     * Checks if an event handler already exists
     *
     * @param string $view_code
     * @return bool
     */
    public function hasEventHandler($view_code) {
        return key_exists(strtoupper($view_code), $this->_event_handlers);
    }
    
    /**
     * Revmoes the event handler associated with a given view code
     *
     * @param string $view_code
     */
    public function removesEventHandler($view_code) {
        $view_code = strtoupper($view_code);
        if(key_exists($view_code, $this->_event_handlers)) {
            unset($this->_event_handlers[$view_code]);
        }
    }
    

    /**
     * Adds an {@link __EventHandler} instance
     *
     * @param __IEventHandler $event_handler The {@link __EventHandler} instance to add to
     */
    public function addEventHandler(__IEventHandler &$event_handler) {
        $view_code = strtoupper($event_handler->getViewCode());
        if(key_exists($view_code, $this->_event_handlers)) {
            unset($this->_event_handlers[$view_code]);
        }
        $this->_event_handlers[$view_code] =& $event_handler;
    }
    
}