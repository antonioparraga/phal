<?php

class __FlowEvent {

    protected $_event_name = null;
    
    public function __construct($event_name = null) {
        $this->setEventName($event_name);
    }
    
    public function setEventName($event_name) {
        $this->_event_name = $event_name;
    }
    
    public function getEventName() {
        return $this->_event_name;
    }
    
}
