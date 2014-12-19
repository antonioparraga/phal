<?php


class __FormHtmlWriter extends __ComponentWriter {
	
    public function startRender(__IComponent &$component)
    {
        $component_id = $component->getId();
        $component_properties = $component->getProperties();
        
        foreach($component_properties as $property => $value) {
        	$property = strtolower($property);
        	if($property != 'runat') {
            	$properties[] = $property . '="' . $value . '"';
        	}
        }
        $properties[] = 'id="' . $component_id . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $properties[] = 'action = "' . __UriContainerWriterHelper::resolveUrl($component) . '"';
        $properties[] = 'method="' . strtoupper($component->getMethod()) . '"';
        
        $url = __FrontController::getInstance()->getRequest()->getUrl();
        $encoded_url = base64_encode(serialize($url));
        
        $form_code  = '<form ' . join(' ', $properties) . '>' . "\n";
        $form_code .= '<input type="HIDDEN" name="submitCode" value="' . $component->getName() . '"></input>' . "\n";
        $form_code .= '<input type="HIDDEN" name="viewCode" value="' . $component->getViewCode() . '"></input>' . "\n";
        $flow_executor = __FlowExecutor::getInstance();
        if($flow_executor->hasActiveFlowExecution()) {
            $active_flow_execution = $flow_executor->getActiveFlowExecution();
            $request_flow_execution_key = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
            $form_code .= '<input type="HIDDEN" name="' . $request_flow_execution_key . '" value="' . $active_flow_execution->getId() . '"></input>' . "\n";
            $current_state = $active_flow_execution->getCurrentState();
            if($current_state != null) {
                $request_flow_state_id = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_FLOW_STATE_ID');
                $form_code .= '<input type="HIDDEN" name="' . $request_flow_state_id . '" value="' . $current_state->getId() . '"></input>' . "\n";
            }
        }
        return $form_code;
    }
    
    public function endRender(__IComponent &$component) {
        return '</form>';
    }
    
}
