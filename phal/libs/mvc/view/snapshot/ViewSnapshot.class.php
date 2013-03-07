<?php

class __ViewSnapshot {

    protected $_component_handler = null;
    protected $_event_handler = null;
    protected $_components = null;
    
    public function __construct($view_code = null) {
        if($view_code != null) {
            $this->setViewCode($view_code);
        }
    }
    
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
        $component_handler = __ComponentHandlerManager::getInstance();
        if($component_handler->hasComponentHandler($this->_view_code)) {
            $this->_component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
            $this->_event_handler = __EventHandlerManager::getInstance()->getEventHandler($this->_view_code);
            $this->_components = $this->_component_handler->getComponents();
        }
    }
    
    public function getViewCode() {
        return $this->_view_code;
    }
    
    public function restoreView() {
        if($this->_component_handler != null) {
            __ComponentHandlerManager::getInstance()->addComponentHandler($this->_component_handler);
            __EventHandlerManager::getInstance()->addEventHandler($this->_event_handler);
            $component_pool = __ComponentPool::getInstance();
            foreach($this->_components as $component) {
                $component_pool->registerComponent($component);
                unset($component);
            }
        }
            
    }
    
}
