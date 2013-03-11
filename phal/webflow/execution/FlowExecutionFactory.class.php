<?php

class __FlowExecutionFactory {

    static public function createFlowExecution($flow_id) {
        $return_value = null;
        $flow_definition = __WebFlowManager::getInstance()->getFlowDefinition($flow_id);
        if($flow_definition != null) {
            $return_value = new __FlowExecution($flow_definition);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Unknown flow for id ' . $flow_id);
        }
        return $return_value;
    }
    
}
