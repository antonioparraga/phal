<?php

class __JavascriptHtmlWriter extends __ComponentWriter {

    public function startRender(__IComponent &$component) {
        return '';
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        $group_id = $component->getGroup();
        if($group_id == null) {
            $group_id = $component->getId();
        }
        
        if(__ResponseWriterManager::getInstance()->hasResponseWriter($group_id)) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter($group_id);
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter($group_id);
            $js_files = $component->getJsFiles();
            foreach($js_files as $js_file) {                
                $jod_response_writer->addJsFileRef($js_file);                
            }
            $checking_variables = $component->getCheckingVariables();
            foreach($checking_variables as $checking_variable) {
                $jod_response_writer->addLoadCheckingVariable($checking_variable);
            }
        	$response_writer_manager = __ResponseWriterManager::getInstance();
        	if($response_writer_manager->hasResponseWriter('javascript')) {
        		$javascript_rw = $response_writer_manager->getResponseWriter('javascript');
        	}
        	else {
        		$javascript_rw = new __JavascriptOnDemandResponseWriter('javascript');
        		$response_writer_manager->addResponseWriter($javascript_rw);
            }
            $javascript_rw->addResponseWriter($jod_response_writer);
        }
        $jod_response_writer->setLoadAfterDomLoaded($component->getAfterDomLoaded());
        $jod_response_writer->addJsCode($enclosed_content);
    }
    
    public function endRender(__IComponent &$component) {
        return '';
    }
        
    
}
