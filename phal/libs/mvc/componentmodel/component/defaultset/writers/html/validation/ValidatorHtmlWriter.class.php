<?php

class __ValidatorHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'errorMessage'), new __ShowErrorMessage($component->getId()));
    }
    
    protected function _renderLibraryDependencies() {
        
        if(__ResponseWriterManager::getInstance()->hasResponseWriter('validation')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('validation');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('validation');
            $jod_response_writer->addJsFileRef('validation/validation.js');
            $jod_response_writer->addLoadCheckingVariable('Validator');
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }
        
    }    
    
}
