<?php


/**
 * This class handles events and notify to all subscribed observers when an event is raised
 * 
 */
class __EventDispatcher {
    
    private $_event_listeners = array();

    private static $_instance = null;
        
    private function __construct() {
        
    }
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __EventDispatcher();
        }
        return self::$_instance;
    }
    
    /**
     * This method perform a broadcast for a concrete event
     *
     */
    public function broadcastEvent(__Event &$event)
    {
        $event_type = $event->getEventType();       
        if (key_exists($event_type, $this->_event_listeners)) {
            foreach ($this->_event_listeners[$event_type] as &$event_listener) {
                $event_listener->receiveEvent($event);
            }
        }
    }
  
    public function registerEventCallback($event_type, __Callback &$callback, $context_id = null) {
        if($context_id == null) {
            $event_listener = new __EventListener($event_type, $callback);
        }
        else {
            $event_listener = new __ContextEventListener($event_type, $callback, $context_id);
        }
        $this->registerEventListener($event_listener);
    }
    
    /**
     * This method registers an observer for specified events notifications.
     * A reference to an observer will be stored for each event type to observe
     *
     * @param __EventListener $event_listener An {@link __EventListener} to register
     */
    public function registerEventListener(__EventListener &$event_listener) {
        $event_type = $event_listener->getEventToListen();
        if(!key_exists($event_type, $this->_event_listeners)) {
            $this->_event_listeners[$event_type] = array();
        }
        $this->_event_listeners[$event_type][] =& $event_listener;
    }
    
    /**
     * This method unregister a observer. Nexts events won't be notified to unregister observer
     *
     * @param __EventListener $event_listener The {@link __EventListener} to be unregisstered
     */
    static public function unregisterEventListener(__EventListener &$event_listener) {
        $event_type = $event_listener->getEventToListen();
        if(key_exists($event_type, $this->_event_listeners)) {
            for ($i = 0; $i < count($this->_event_listeners[$event_type]); $i++) {
                if($this->_event_listeners[$event_type][$i] === $event_listener) {
                    array_splice($this->_event_listeners[$event_type], $i, 1);
                    return;
                }
            }
        }
    }
    
}