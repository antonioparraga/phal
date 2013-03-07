<?php

class __ComponentTag {
    
    protected $_component_id = null;
    protected $_tag_name     = null;
    protected $_component_writers = array();
    protected $_component_class = null;
    protected $_component_writer_decorators = array();
    protected $_view_classes = array();
    protected $_component_interface = null;

    public function __construct($tag_name) {
        $this->setTagName($tag_name);
    }
    
    public function setTagName($tag_name) {
        $this->_tag_name = $tag_name;
    }
    
    public function getTagName() {
        return $this->_tag_name;
    }
    
    public function setComponentId($component_id) {
        $this->_component_id = $component_id;
    }
    
    public function getComponentId() {
        return $this->_component_id;
    }
    
    public function addComponentWriterClass($client, $component_writer_class, array $component_writer_decorator_classes = array()) {
        $this->_component_writers[$client] = $component_writer_class;
        ksort($component_writer_decorator_classes);
        $this->_component_writer_decorators[$client] = $component_writer_decorator_classes;
    }
    
    public function setComponentInterfaceSpec(__UICompositeComponentInterfaceSpec $ui_component_interface) {    
        $this->_component_interface = $ui_component_interface;
    }
    
    public function getComponentInterfaceSpec() {
        return $this->_component_interface;
    }

    public function addViewDefinition($client, $view_class) {
        $this->_view_classes[$client] = $view_class;
    }

    public function getViewDefinitions() {
        return $this->_view_classes;
    }
    
    public function getViewDefinition($client) {
        $return_value = null;
        if(key_exists($client, $this->_view_classes)) {
            $return_value = $this->_view_classes[$client];
        }
        return $return_value;        
    }
    
    public function getComponentWriterClasses() {
        return $this->_component_writers;
    }
    
    public function getComponentWriterDecoratorClasses($client) {
        $return_value = null;
        if(key_exists($client, $this->_component_writer_decorators)) {
            $return_value = $this->_component_writer_decorators[$client];
        }
        return $return_value;
    }
    
    public function getComponentWriterClass($client) {
        $return_value = null;
        if(key_exists($client, $this->_component_writers)) {
            $return_value = $this->_component_writers[$client];
        }
        return $return_value;
    }
        
    public function setComponentClass($component_class) {
        $this->_component_class = $component_class;
    }

    public function getComponentClass() {
        return $this->_component_class;
    }
    
}