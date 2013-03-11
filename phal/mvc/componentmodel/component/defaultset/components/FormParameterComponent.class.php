<?php


class __FormParameterComponent extends __UIComponent {
    
    private $_parameter_name  = null;
    private $_parameter_value = null;
    
    public function setParameterName($parameter_name) {
        $this->_parameter_name = $parameter_name;
    }
    
    public function getParameterName() {
        return $this->_parameter_name;
    }
   
    public function setParameterValue($parameter_value) {
        $this->_parameter_value = $parameter_value;
    }
    
    public function getParameterValue() {
        return $this->_parameter_value;
    } 
    
}
