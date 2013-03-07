<?php

class __RemoteServiceHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        $component_id = $component->getId();
        $cep = new __JavascriptCallback($component_id, 'execute');
        $cep->setExecuteAlways(true);
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'lastResponse'), $cep);
    }
    
    public function startRender(__IComponent &$component)
    {
        $component_id = $component->getId();
        $component_name = $component->getName();
        
        if(!__ResponseWriterManager::getInstance()->hasResponseWriter('remoteservicedeclarations_' . $component_name)) {
            $send_event_parameters = array();
            $send_event_parameters[] = "'remotecall'";
            $send_event_parameters[] = "arg";
            $send_event_parameters[] = "'$component_id'";
            $send_event_parameters = join(', ', $send_event_parameters);
    
            //generate the remote service function:
            $js_code = <<<CODESET
$component_name = function() {
    var arg = \$A($component_name.arguments);
    (__ClientEventHandler.getInstance()).sendEvent($send_event_parameters);   
};
CODESET;
            $jod_response_writer2 = new __JavascriptOnDemandResponseWriter('remoteservicedeclarations_' . $component_name);
            $jod_response_writer2->addJsCode($js_code);
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer2);
            
            //generate the callback code (if applicable)
            $callback = $component->getClientResponseCallback();
            if($callback != null) {
                $js_callback_code = "$component_id = new __ResponseCallbackHandler($callback);";
                if(__ResponseWriterManager::getInstance()->hasResponseWriter('remoteservicecallback')) {
                    $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('remoteservicecallback');
                }
                else {
                    $jod_response_writer = new __JavascriptOnDemandResponseWriter('remoteservicecallback');
                }
                $jod_response_writer->addJsCode($js_callback_code);
                $jod_response_writer->setLoadAfterDomLoaded(true);
                $javascript_rw->addResponseWriter($jod_response_writer);
            }
        }        
    }

    
}
