<?php

class __EventFlowTransition extends __FlowTransition {

    protected $_event = null;
    
    public function on($event) {
        $this->setEvent($event);
    }
    
    public function setEvent($event) {
        $this->_event = $event;
    }
    
    public function getEvent() {
        return $this->_event;
    }    
    
}
