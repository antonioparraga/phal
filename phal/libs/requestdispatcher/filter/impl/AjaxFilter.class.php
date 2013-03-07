<?php

class __AjaxFilter extends __Filter {
    
    /**
     * Updates client end-point with values received from request:
     *
     * @param __IRequest $request
     * @param __IResponse $response
     */
    public function preFilter(__IRequest &$request, __IResponse &$response) {
        //update client end-points with values received from request:
        $request_component_values = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_CLIENT_ENDPOINT_VALUES');
        if($request->hasParameter($request_component_values)) {
            $values = $request->getParameter($request_component_values);
            if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
                $scape_chars = array('\\n', '\\r', '\\t');
                $double_scape_chars = array('\\\\n', '\\\\r', '\\\\t');
                $values = str_replace($scape_chars, $double_scape_chars, $values);
                $values = stripslashes($values);
            }
            $client_values = json_decode($values, true);
            if(is_array($client_values)) {
                $ui_binding_manager = __UIBindingManager::getInstance();
                foreach($client_values as $id => $value) {
                    if($ui_binding_manager->hasUIBinding($id)) {
                        $ui_binding_manager->getUIBinding($id)->getClientEndPoint()->setValue($value);
                    }
                    else if($request->hasParameter('viewCode')) {
                        $view_code = $request->getParameter('viewCode');
                        __ComponentLazyLoader::loadView($view_code);
                        if($ui_binding_manager->hasUIBinding($id)) {
                            $ui_binding_manager->getUIBinding($id)->getClientEndPoint()->setValue($value);
                        }
                        else {
                            throw __ExceptionFactory::getInstance()->createException('Can not sync component status between client and server');
                        }
                    }
                }
            }
        }

    }
    
    /**
     * Creates an ajax message with current server end-point values
     *
     * @param __IRequest $request
     * @param __IResponse $response
     */
    public function postFilter(__IRequest &$request, __IResponse &$response) {
        $client_notificator = __ClientNotificator::getInstance();
        //notify to client:
        $client_notificator->notify();
        //clear dirty components to avoid notify again in next requests:
        $client_notificator->clearDirty();
    }
    
}

