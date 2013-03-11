<?php

/**
 * This class represents an event fired from UI
 *
 */
final class __UIEvent {
    
    protected $_component_id = null;
    protected $_event_name = null;
    protected $_extra_info = null;
    
    public function __construct($event_name, $extra_info = null, __IComponent &$component = null) {
        $this->setEventName($event_name);
        if($extra_info != null) {
            $this->setExtraInfo($extra_info);
        }
        if($component != null) {
            $this->setComponent($component);
        }
    }
    
    public function setEventName($event_name) {
        if(!empty($event_name)) {
            $this->_event_name = $event_name;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Need an event name (an empty value as the event name has been received)');
        }
    }
    
    public function getEventName() {
        return $this->_event_name;
    }
    
    public function setExtraInfo($extra_info) {
        $this->_extra_info = $extra_info;
    }

    public function getExtraInfo() {
        return $this->_extra_info;
    }
        
    public function setComponent(__IComponent &$component) {
        $this->_component_id = $component->getId();
    }
    
    public function &getComponent() {
        $return_value = null;
        if(!empty($this->_component_id)) {
            $return_value = __ComponentPool::getInstance()->getComponent($this->_component_id);
        }
        return $return_value;
    }
    
}