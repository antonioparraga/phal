<?php

class __FlowStateFactory {

    const ACTION_STATE = 1;
    const START_STATE = 2;
    const END_STATE = 3;
    const DECISION_STATE = 4;
    const SUBFLOW_STATE = 5;
    
    static public function createState($state_type) {
        $return_value = null;
        switch((int)$state_type) {
            case self::ACTION_STATE:
                $return_value = new __ActionFlowState();  
                break;
            case self::START_STATE:
                $return_value = new __StartFlowState();  
                break;
            case self::END_STATE:
                $return_value = new __EndFlowState();  
                break;
            case self::DECISION_STATE:
                $return_value = new __DecisionFlowState();  
                break;
            case self::SUBFLOW_STATE:
                $return_value = new __SubFlowState();  
                break;
            default:
                throw __ExceptionFactory::getInstance()->createException('Unknow flow state type: ' . $state_type);
                break;
        }
        return $return_value;
    }
    
}
