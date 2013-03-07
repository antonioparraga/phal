<?php

class __Callback {
    
    private $_class_instance = null;
    private $_method_name    = null;
    private $_parameters     = null;
    
    public function __construct(&$class_instance, $method_name, array &$parameters = null) {
        $this->setClassInstance($class_instance);
        $this->setMethodName($method_name);
        if($parameters != null) {
            $this->setParameters($parameters);
        }
    }
    
    public function setClassInstance(&$class_instance) {
        $this->_class_instance =& $class_instance;
        
    }
    
    public function getClassInstance() {
        return $this->_class_instance;
    }
    
    public function setMethodName($method_name) {
        $this->_method_name = $method_name;
    }
    
    public function getMethodName() {
        return $this->_method_name;
    }
    
    public function setParameters(array &$parameters) {
        $this->_parameters =& $parameters;
    }
    
    public function getParameters() {
        return $this->_parameters;
    }
    
    public function execute(array &$parameters = null) {
        if(!is_array($parameters)) {
            if($parameters != null) {
                $parameters =& $this->_parameters;
            }
            else {
                $parameters = array();
            }
        } 
        return call_user_func_array (array($this->_class_instance, $this->_method_name), $parameters);
    }
    
}