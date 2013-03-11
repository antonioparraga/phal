<?php

abstract class __FlowTransition implements __IFlowTransition {

    const EVENT_TRANSITION = 1;
    const EXCEPTION_TRANSITION = 2;    
    
    protected $_next_state = null;
    protected $_attributes_collection = null;
    
    public function __construct() {
        $this->_attributes_collection = new __FlowAttributesCollection();
    }
    
    public function to($next_state) {
        $this->setNextState($next_state);
    }
    
    public function setNextState($next_state) {
        $this->_next_state = $next_state;
    }
    
    public function getNextState() {
        return $this->_next_state;
    }
    
    public function addAttribute($attribute) {
        $this->_attributes_collection->add($attribute);
    }
    
    public function getAttributes() {
        return $this->_attributes_collection->toArray();
    }    
    
}
