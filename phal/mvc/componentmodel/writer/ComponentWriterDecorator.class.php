<?php

abstract class __ComponentWriterDecorator implements __IComponentWriter {
    
    protected $_component_writer = null;
    
    public function __construct(__IComponentWriter $component_writer) {
        $this->_component_writer = $component_writer;
    }
        
	public function bindComponentToClient(__IComponent &$component) {
	    return $this->_component_writer->bindComponentToClient($component);
	}
    
    public function startRender(__IComponent &$component) {
        return $this->_component_writer->startRender($component);
    }
    
    public function endRender(__IComponent &$component) {
        return $this->_component_writer->endRender($component);
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $this->_component_writer->renderContent($enclosed_content, $component);
    }
    
    public function canRenderChildrenComponents(__IComponent &$component) {
        return $this->_component_writer->canRenderChildrenComponents($component);
    }
    
}