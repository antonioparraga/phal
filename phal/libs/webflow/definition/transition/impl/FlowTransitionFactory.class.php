<?php

class __FlowTransitionFactory {
    
    static public function createTransition($transition_type) {
        $return_value = null;
        switch((int) $transition_type) {
            case __FlowTransition::EVENT_TRANSITION:
                $return_value = new __EventFlowTransition();
                break;
            case __FlowTransition::EXCEPTION_TRANSITION:
                $return_value = new __ExceptionFlowTransition();
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('Unknow flow transition type: ' . $transition_type);
                break;
        }
        return $return_value;
    }
    
}
