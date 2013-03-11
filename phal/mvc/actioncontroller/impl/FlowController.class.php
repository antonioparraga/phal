<?php

class __FlowController extends __ActionController {

    public function startFlowAction() {
        $return_value = null;
        $request = __FrontController::getInstance()->getRequest();
        $flow_id = $request->getFlowId();
        if($flow_id != null) {
            $flow_executor = __FlowExecutor::getInstance();
            $state = $flow_executor->launch($flow_id);
            $active_flow_execution = $flow_executor->getActiveFlowExecution();
            $flow_execution_key = $active_flow_execution->getId();
            $return_value = $this->_executeControllerAssociatedToState($state, $flow_executor, $flow_execution_key);
        }
        return $return_value;
    }
    
    public function defaultAction() {
        $return_value = null;
        $request = __FrontController::getInstance()->getRequest();
        $application_context = __ApplicationContext::getInstance();
        $request_flow_execution_key = $application_context->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
        if($request->hasParameter($request_flow_execution_key)) {
            $flow_execution_key = $request->getParameter($request_flow_execution_key);
            $request_flow_event_id = $application_context->getPropertyContent('REQUEST_FLOW_EVENT_ID');
            $request_flow_state_id = $application_context->getPropertyContent('REQUEST_FLOW_STATE_ID');
            $flow_executor = __FlowExecutor::getInstance();
            
            //sync with the current state sent from client:
            if($request->hasParameter($request_flow_state_id)) {
                $flow_execution = $flow_executor->getActiveFlowExecution();
                if($flow_execution != null) {
                    $flow_state_id = $request->getParameter($request_flow_state_id);
                    if($flow_execution->isStateVisited($flow_state_id)) {
                        $current_state = $flow_execution->getCurrentState();
                        if($current_state != null && $current_state->getId() != $flow_state_id) {
                            $state = $flow_execution->goToState($flow_state_id);
                            if($state != null && !$request->hasParameter($request_flow_event_id)) {
                                $application_context = __ApplicationContext::getInstance();
                                $request_flow_execution_key = $application_context->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
                                $flow_execution_key = $request->getParameter($request_flow_execution_key);
                                $this->_executeControllerAssociatedToState($state, $flow_executor, $flow_execution_key);
                            }
                        }
                    }
                    else {
                        throw __ExceptionFactory::getInstance()->createException('Flow state not yet visited: ' . $flow_state_id);
                    }
                }
            }
            
            //checks flow event:
            if($request->hasParameter($request_flow_event_id)) {
                $flow_event_id = $request->getParameter($request_flow_event_id);
                $state = $flow_executor->resume($flow_execution_key, $flow_event_id);
                if($state != null) {
                    $this->_executeControllerAssociatedToState($state, $flow_executor, $flow_execution_key);
                }
            }
            else {
            	if($flow_executor->hasFlowExecution($flow_execution_key)) {
	                $return_value = $flow_executor->getResponse($flow_execution_key);
	                $return_value->setBufferControl(true);
	                //let also the browser cache the page
	                $return_value->addHeader('Cache-Control: private, max-age=10800, pre-check=10800');
	                $return_value->addHeader('Pragma: private');
	                $return_value->addHeader("Expires: " . date(DATE_RFC822,strtotime("+2 day")));
            	}
            	else {
            		//start a new flow and redirect the user to the very first step:
            		$this->startFlowAction();
            	}
            }
        }
        return $return_value;
    }
    
    protected function _executeControllerAssociatedToState(__FlowState $state,
                                                           __FlowExecutor &$flow_executor, 
                                                           $flow_execution_key) {
        try {
            $action_identity = $state->getActionIdentity();
            $response = __ActionDispatcher::getInstance()->dispatch($action_identity);
            if($response instanceof __IResponse) {
                $flow_executor->setResponse($response);
                $response->clear();
                //redirect via 303 because of the redirect after submit pattern (alwaysRedirectOnPause)
                $request = __FrontController::getInstance()->getRequest();
                $uri = $request->getUri();
                //add the flow execution key parameter:
                $application_context = __ApplicationContext::getInstance();
                $request_flow_execution_key = $application_context->getPropertyContent('REQUEST_FLOW_EXECUTION_KEY');
                $request_flow_state_id = $application_context->getPropertyContent('REQUEST_FLOW_STATE_ID');
                $uri->addParameter($request_flow_execution_key, $flow_execution_key);
                $uri->addParameter($request_flow_state_id, $state->getId());
                $empty_request = __RequestFactory::getInstance()->createRequest();
                __FrontController::getInstance()->redirect($uri, $empty_request, 303);
            }
            else if($response instanceof __FlowEvent) {
                $fc_response = __FrontController::getInstance()->getResponse();
                $fc_response->clear(); //clear the response content (to avoid decorator lateral issues)
                $state = $flow_executor->resume($flow_execution_key, $response->getEventName());
                if($state != null) {
                    $application_context = __ApplicationContext::getInstance();
                    $action_identity = $state->getActionIdentity();
                    $this->_executeControllerAssociatedToState($state, $flow_executor, $flow_execution_key);
                }
            }
        }
        catch(Exception $e) {
            if($flow_executor->isExceptionHandled($e)) {
                //$state = $flow_executor->handleException($flow_execution_key, $e);
                //if($state != null) {
                //    $this->_executeControllerAssociatedToState($flow_executor, $state);
                //}
            }
            else {
                throw $e;
            }
        }
        return $response;
    }
     
}
