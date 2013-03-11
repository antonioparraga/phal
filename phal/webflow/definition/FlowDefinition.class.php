<?php

class __FlowDefinition {

    const SCOPE_FLOW = 1;
    const SCOPE_REQUEST = 2;
    const SCOPE_SESSION = 3;
    
    protected $_id = null;
    protected $_states = array();
    protected $_start_state = null;
    
    public function __construct($id = null) {
        $this->setId($id);
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getId() {
        if($this->_id === null) {
            $this->_id = uniqid('flow_');
        }
        return $this->_id;
    }
    
    public function clearStates() {
        unset($this->_states);
        $this->_states = array();
    }
    
    public function hasState($state_id) {
        $return_value = false;
        if(key_exists($state_id, $this->_states)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function addState(__IFlowState &$state) {
        $this->_states[$state->getId()] =& $state;
        if($state instanceof __StartFlowState) {
            if($this->_start_state == null) {
                $this->_start_state =& $state;
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Multiple start states in flow ' . $this->_id);
            }
        }
    }
    
    public function setStates(array $states) {
        $this->clearStates();
        foreach($states as &$state) {
            $this->addState($state);
        }
    }
    
    public function &getState($id) {
        $return_value = null;
        if(key_exists($id, $this->_states)) {
            $return_value =& $this->_states[$id];
        }
        return $return_value;
    }    
    
    public function &getStartState() {
        return $this->_start_state;
    }
    
}
