<?php

class __ComponentProperty2ComponentSpec extends __ComponentPropertySpec {

    protected $_receiver_component = null;
    
    public function setComponent($component) {
        $this->_receiver_component = $component;
    }
    
    public function getComponent() {
        return $this->_receiver_component;
    }
    
    public function resolveReceiver(__ICompositeComponent &$component) {
        $return_value = null;
        if($this->_receiver_component != null) {
            $event_handler = $component->getEventHandler();
            if($event_handler) {
                $return_value = $event_handler->getComponent($component_name);
            }
        }
        return $return_value;
    }
    
}
