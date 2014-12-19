<?php

class __RemoteServiceHtmlWriter extends __ComponentWriter {

    public function startRender(__IComponent &$component)
    {
        $component_id = $component->getId();
        $component_name = $component->getName();
        
        if(!__ResponseWriterManager::getInstance()->hasResponseWriter('remoteservicedeclarations_' . $component_name)) {
    
            //generate dynamic callback just in case there is not defined an static callback
            $callback  = $component->getClientResponseCallback();
			$view_code = $component->getViewCode();
            if($callback == null) {            
            	$js_code = <<<CODESET
$component_name = function() {
    var arg = $component_name.arguments;
    var callback = null;
    if(arg.length > 0 && typeof arg[arg.length - 1] === 'function') {
        var callback = arg[arg.length - 1];
        delete arg[arg.length - 1];
    }
    
    (__ClientEventHandler.getInstance()).call('$view_code', '$component_name', arg, callback);
};
CODESET;
			}
			else {
				$js_code = <<<CODESET
$component_name = function() {
    var arg = $component_name.arguments;
    (__ClientEventHandler.getInstance()).call('$view_code', '$component_name', arg, $callback);
};
CODESET;
			}
			
            $jod_response_writer2 = new __JavascriptOnDemandResponseWriter('remoteservicedeclarations_' . $component_name);
            $jod_response_writer2->addJsCode($js_code);
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer2);

        }        
    }

    
}
