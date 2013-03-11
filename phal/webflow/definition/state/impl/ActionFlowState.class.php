<?php

class __ActionFlowState extends __FlowState {

    protected $_action_identity = null;
    protected $_transitions = array();
    
    public function setActionIdentity($action_identity) {
        $this->_action_identity = $action_identity;
    }

    public function getActionIdentity() {
        return $this->_action_identity;
    }
    
    public function addTransition(__FlowTransition $transition) {
        $this->_transitions[$transition->getEvent()] =& $transition;
    }
    
    public function &getTransitions() {
        return $this->_transitions;
    }
    
    public function hasTransition($event_code) {
        $return_value = false;
        if(key_exists($event_code, $this->_transitions)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function &getTransition($event_code) {
        $return_value = null;
        if(key_exists($event_code, $this->_transitions)) {
            $return_value =& $this->_transitions[$event_code];
        }
        return $return_value;
    }
    
    
}
