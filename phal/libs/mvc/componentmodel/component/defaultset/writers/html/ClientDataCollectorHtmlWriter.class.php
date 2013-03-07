<?php

class __ClientDataCollectorHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        $sep = new __ComponentProperty($component, 'data');
        $cep = new __JavascriptObject($component->getId());
        __UIBindingManager::getInstance()->bind($sep, $cep);
    }
    
    public function startRender(__IComponent &$component) {
        $component_id = $component->getId();
        $data = $component->getData();
        $data_json_string = json_encode($data);
        
        $javascript_code = <<<CODESET
    $component_id = $data_json_string; 
CODESET;

        $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
        $javascript_rw->addJsCode($javascript_code);
    
        return '';
    }
    
}
