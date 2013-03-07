<?php

class __DecisionFlowState extends __FlowState {

    protected $_transitions = array();
    
    public function addTransition(__IConditionalFlowTransition $transition) {
        $this->_transitions[] =& $transition;
    }
    
    public function getTransitions() {
        return $this->_transitions;
    }
    
}
