<?php

/**
 * This is the front controller designed to dispatch AJAX request due to ui events
 *
 */
class __AjaxFrontController extends __HttpFrontController {
    
    /**
     * This method process an AJAX request
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {

    	if($request->hasParameter('service') && $request->hasParameter('viewCode')) {
			$view_code = $request->getParameter('viewCode');
			$event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
			if($event_handler != null) {
				$service = $request->getParameter('service');
 				$annotations_collection = __AnnotationParser::getInstance()->getAnnotations(get_class($event_handler));
	            $annotations = $annotations_collection->toArray();
	            $uc_service = strtoupper($service);
	            foreach($annotations as $annotation) {
	                if(strtoupper($annotation->getMethod()) == $uc_service) {
	                	$parameters_as_json = $request->getParameter('arguments');
	                	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
	                		$scape_chars = array('\\n', '\\r', '\\t');
	                		$double_scape_chars = array('\\\\n', '\\\\r', '\\\\t');
	                		$parameters_as_json = str_replace($scape_chars, $double_scape_chars, $parameters_as_json);
	                		$parameters_as_json = stripslashes($parameters_as_json);
	                	}
						$parameters = json_decode($parameters_as_json, true);
	                	$return_value = call_user_func_array(array($event_handler, $service), $parameters);
	                	$response->addContent(json_encode($return_value));
	                }
	            }		                    
			}
		}    		

    }
    
    /**
     * Returns REQUEST_TYPE_XMLHTTP value to indicate that current request has done via AJAX
     * 
     * @see __FrontController::getRequestType()
     */
    public function getRequestType() {
    	return REQUEST_TYPE_XMLHTTP;
    }
    
    
}