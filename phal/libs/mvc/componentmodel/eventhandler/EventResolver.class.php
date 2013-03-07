<?php

final class __EventResolver {
    
    static private $_instance = null;
    
    private $_event = null;
    
    private function __construct() {
        
    }
    
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __EventResolver();
        }
        return self::$_instance;
    }
    
    public function resolveEvent() {
        if($this->_event == null) {
            $request = __FrontController::getInstance()->getRequest();
            if($request->hasParameter('event')) {
                $event_json_string = stripslashes($request->getParameter('event'));
                $event = json_decode($event_json_string, true);
                $component_id = $event['componentId'];
                $event_name   = $event['eventName'];
                $extra_info   = $event['extraInfo'];
                if(__ComponentPool::getInstance()->hasComponent($component_id)) {
                    $component    = __ComponentPool::getInstance()->getComponent($component_id);
                    //create the event instance:
                    $this->_event = new __UIEvent($event_name, $extra_info, $component);
                }
            }
        }
        return $this->_event;
    }
    
}