<?php

class __FileBoxHtmlWriter extends __ComponentWriter {
    
    public function startRender(__IComponent &$component) {
        $component_id = $component->getId();

        if(__ResponseWriterManager::getInstance()->hasResponseWriter('uploadfile')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('uploadfile');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('uploadfile');
            $jod_response_writer->setLoadAfterDomLoaded(true);
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }

        $jod_response_writer->addJsCode($component_id . ' = new __FileUploader($("' . $component_id . '"));' . "\n");        
        
        if($component->getStatus() == __IUploaderComponent::UPLOAD_STATUS_DONE) {
            $filename = $component->getFilename();
            $icon = $component->getIcon();
            if($icon != null) {
                $filename = "<img src='$icon' width='32' height='32' valign='absmiddle'>&nbsp;" . $filename;
            }
            $jod_response_writer->addJsCode($component_id . '.renderAsUploaded("' . $filename . '");');
        }
        
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'type = "file"';
        $properties[] = 'id = "' . $component_id . '"';
        $properties[] = 'name = "' . $component_id . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }

        $input_file_properties = implode(' ', $properties);

        $return_value = <<<CODE
    <input type="file" $input_file_properties>
CODE;

        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '</input>';
    }
        
    
}
