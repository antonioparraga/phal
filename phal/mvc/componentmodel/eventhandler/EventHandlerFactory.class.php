<?php

class __EventHandlerFactory {
    
    private static $_instance = null;
    
    private function __construct() {
    }
        
    /**
     * Enter description here...
     *
     * @return __EventHandlerFactory
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __EventHandlerFactory();
        }
        return self::$_instance;
    }
    
    public function &createEventHandler($view_code) {
        $return_value = null;
        $view = __ViewResolver::getInstance()->getView($view_code);
        $event_handler_class = $view->getEventHandlerClass();
        if($event_handler_class != null) {
            $return_value = new $event_handler_class();
            if($return_value instanceof __IEventHandler) {
                $return_value->setViewCode($view_code);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Wrong event handler class: ' . $event_handler_class . '. Must implement the __IEventHandler interface.');
            }
        }
        return $return_value;
    }
    
}