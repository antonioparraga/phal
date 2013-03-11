<?php

class __UICompositeComponent extends __UIContainer implements __ICompositeComponent {

    protected $_event_handler = null;
    protected $_composite_interface_handler = null;
    protected $_content = null;
    protected $_composite_writer = null;
    protected $_unsynchronized = false;
    
    public function setEventHandler(__ICompositeComponentEventHandler &$event_handler) {
        $this->_event_handler =& $event_handler;
    }
    
    /**
     * Get the __ICompositeComponentEventHandler instance associated to the current component
     *
     * @return __ICompositeComponentEventHandler
     */
    public function &getEventHandler() {
        return $this->_event_handler;
    }
    
    /**
     * Get a component interface specification as a mapping between composite properties and real target properties
     *
     * @param __UICompositeComponentInterfaceSpec $ui_component_interface
     */
    public function setComponentInterfaceSpec(__UICompositeComponentInterfaceSpec $ui_component_interface) {    
        $this->_composite_interface_handler = new __UICompositeComponentInterfaceHandler($ui_component_interface, $this);
    }
    
    public function setupProperties() {
        if($this->_composite_interface_handler != null) {
            $this->_composite_interface_handler->setupCompositeComponent();
        }
    }
        
    public function __get($property_name) {
        if($this->_composite_interface_handler != null && $this->_composite_interface_handler->hasProperty($property_name)) {
            $return_value = $this->_composite_interface_handler->getProperty($property_name);
        }
        else {
            $return_value = $this->getProperty($property_name);
        }
        return $return_value;
    }
    
    public function __set($property_name, $property_value) {
        if($this->_composite_interface_handler != null && $this->_composite_interface_handler->hasProperty($property_name)) {
            $return_value = $this->_composite_interface_handler->setProperty($property_name, $property_value);
        }
        else {
            $return_value = $this->setProperty($property_name, $property_value);
        }
    }
    
    public function setContent($content) {
        $this->_content = $content;
    }
    
    public function getContent() {
        return utf8_encode($this->_content);
    }
    
    public function setAsUnsynchronized() {
        $this->_unsynchronized = true;
    }
    
    public function isUnsynchronized() {
        return $this->_unsynchronized;
    }
    
    public function setCompositeWriter(__ICompositeWriter &$composite_writer) {
        $this->_composite_writer =& $composite_writer;
    }
    
    public function refresh() {
        if($this->_composite_writer != null) {
            $this->setAsUnsynchronized();
            $this->_composite_writer->startRender($this);
            $response = __ResponseFactory::getInstance()->createResponse();
            //clear content set by responseWriters:
            $this->_content .= $response->getContent();
            //mark the current instance as dirty:
            __ClientNotificator::getInstance()->setDirty($this);
        }
    }
    
}
