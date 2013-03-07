<?php

class __UICompositeComponentInterfaceSpec {

    protected $_component_property_specs = array();
    
    public function clearComponentPropertySpecs() {
        $this->_component_property_specs = array();
    }
    
    public function hasComponentPropertySpec($property_name) {
        $return_value = false;
        $property_name = strtoupper($property_name);
        if(key_exists($property_name, $this->_component_property_specs)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function setComponentPropertySpecs(array $component_property_specs) {
        $this->clearComponentPropertySpecs();
        foreach($component_property_specs as $component_property_spec) {
            $this->addComponentPropertySpec($component_property_spec);
        }
    }

    public function addComponentPropertySpec(__ComponentPropertySpec &$component_property_spec) {
        $property_name = strtoupper($component_property_spec->getName());
        $this->_component_property_specs[$property_name] = $component_property_spec;
    }
    
    public function getComponentPropertySpecs() {
        return $this->_component_property_specs;
    }
    
    public function getComponentPropertySpec($property_name) {
        $return_value = null;
        $property_name = strtoupper($property_name);
        if(key_exists($property_name, $this->_component_property_specs)) {
            $return_value = $this->_component_property_specs[$property_name];
        }
        return $return_value;
    }
    
    
}
