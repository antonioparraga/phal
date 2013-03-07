<?php

abstract class __ComponentPropertySpec {

    protected $_property_name = null;
    protected $_receiver_property = null;
    
    public function __construct($name) {
        $this->setName($name);
    }
    
    public function setName($name) {
        $this->_property_name = $name;
    }
    
    public function getName() {
        return $this->_property_name;
    }
    
    public function setProperty($property) {
        $this->_receiver_property = $property;
    }
    
    public function getProperty() {
        return $this->_receiver_property;
    }    
    
    abstract public function resolveReceiver(__ICompositeComponent &$component);
    
}
