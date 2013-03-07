<?php

class __CompositeComponentEventHandler extends __EventHandler implements __ICompositeComponentEventHandler {

    protected $_composite_component = null;
    
    public function setCompositeComponent(__ICompositeComponent &$composite_component) {
        $this->_composite_component =& $composite_component;
    }
    
    public function &getCompositeComponent() {
        return $this->_composite_component;
    }
    
    public function raiseEvent($event_name, $extra_info = array()) {
        if($this->_composite_component != null) {
            $view_code = $this->_composite_component->getViewCode();
            $event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
            if($event_handler != null) {
                $event = new __UIEvent($event_name, $extra_info, $this->_composite_component);
                $event_handler->handleEvent($event);
            }
        }
    }
    
    final public function setupProperties() {
        $composite_component = $this->getCompositeComponent();
        $composite_component->setupProperties($this);
    }
    
}
