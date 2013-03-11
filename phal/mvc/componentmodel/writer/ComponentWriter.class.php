<?php

abstract class __ComponentWriter implements __IComponentWriter {
    
	public function bindComponentToClient(__IComponent &$component) {
        //nothing to do
    }
	
    public function startRender(__IComponent &$component) {
        return null; //nothing to do
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $enclosed_content;
    }
    
    public function endRender(__IComponent &$component) {
        return null;
    }    
    
    public function canRenderChildrenComponents(__IComponent &$component) {
        return true;
    }
    
}
