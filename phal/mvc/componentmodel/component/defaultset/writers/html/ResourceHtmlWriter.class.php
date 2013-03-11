<?php


class __ResourceHtmlWriter extends __ComponentWriter {
	
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $component->__toString();  
    }
    
}
