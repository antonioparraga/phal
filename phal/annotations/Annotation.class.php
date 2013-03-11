<?php

/**
 * This class represents a comment-based annotation
 * 
 */
class __Annotation {

    protected $_class = null;
    protected $_method = null;
    protected $_name = null;
    protected $_arguments = array();
    
    public function __construct($class, $method, $name, $arguments = array()) {
        $this->setClass($class);
        $this->setMethod($method);
        $this->setName($name);
        $this->setArguments($arguments);
    }
    
    /**
     * Set the class where the annotation is located in
     * 
     * @param string The class name
     */
    public function setClass($class) {
        $this->_class = $class;
    }
    
    /**
     * Get the class where the annotation is located in
     * @return string The class name
     */
    public function getClass() {
        return $this->_class;
    }
    
    /**
     * Set the method where the annotation is located in
     * 
     * @param string The method name
     */
    public function setMethod($method) {
        $this->_method = $method;
    }
    
    /**
     * Get the method name where the annotation is located in
     * @return string The method name
     */
    public function getMethod() {
        return $this->_method;
    }
    
    public function setName($name) {
        $this->_name = $name;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function setArguments(array $arguments) {
        $this->_arguments = $arguments;
    }
    
    public function getArguments() {
        return $this->_arguments;
    }
    
    public function hasArgument($argument_name) {
        $return_value = false;
        $argument_name = strtolower($argument_name);
        if(key_exists($argument_name, $this->_arguments)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function getArgument($argument_name) {
        $return_value = null;
        $argument_name = strtolower($argument_name);
        if(key_exists($argument_name, $this->_arguments)) {
            $return_value = $this->_arguments[$argument_name];
        }
        return $return_value;
    }
    
}

