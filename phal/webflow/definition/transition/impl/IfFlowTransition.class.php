<?php

class __IfFlowTransition implements __IConditionalFlowTransition {

    protected $_condition = null;
    protected $_next_state_if_true = null;
    protected $_next_state_if_false = null;
    protected $_attributes_collection = null;
    
    public function __construct() {
        $this->_attributes_collection = new __FlowAttributesCollection();
    }
    
    public function setCondition($condition) {
        $this->_condition = $condition;
    }
    
    public function getCondition() {
        return $this->_condition;
    }
    
    public function setNextStateIfTrue($next_state) {
        $this->_next_state_if_true = $next_state;
    }
    
    public function getNextStateIfTrue() {
        return $this->_next_state_if_true;
    }
    
    public function setNextStateIfFalse($next_state) {
        $this->_next_state_if_false = $next_state;
    }
    
    public function getNextStateIfFalse() {
        return $this->_next_state_if_false;
    }    
    
    public function addAttribute($attribute) {
        $this->_attributes_collection->add($attribute);
    }
    
    public function getAttributes() {
        return $this->_attributes_collection->toArray();
    }    
    
    public function evaluateCondition() {
        
    }
    
}