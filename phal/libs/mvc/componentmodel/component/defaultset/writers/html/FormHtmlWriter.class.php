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
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        
        $url = __FrontController::getInstance()->getRequest()->getUrl();
        $encoded_url = base64_encode(serialize($url));
        
        $form_code  = '<form ' . join(' ', $properties) . ' onSubmit="return (__ClientEventHandler.getInstance()).handleSubmit(this);">' . "\n";
        $request_submit_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_SUBMIT_CODE');
        $form_code .= '<input type="HIDDEN" name="' . $request_submit_code . '" value="' . $component_id . '"></input>' . "\n";
        $form_code .= '<input type="HIDDEN" name="viewCode" value="' . $encoded_url . '"></input>' . "\n";
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
        
        $hidden_parameters = $component->getHiddenParameters();
        foreach($hidden_parameters as $hidden_parameter_name => $hidden_parameter_value) {
            if(strtoupper($hidden_parameter_name) != strtoupper($request_submit_code) &&
               strtoupper($hidden_parameter_name) != 'CLIENTENDPOINTVALUES') {
                $form_code .= '<input type="HIDDEN" name="' . $hidden_parameter_name . '" value="' . htmlentities($hidden_parameter_value) . '"></input>' . "\n";
            }
        }
        return $form_code;
    }
    
    public function endRender(__IComponent &$component) {
        return '</form>';
    }
    
}
