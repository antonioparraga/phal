<?php

/**
 * This class is the base class for all resources provided by the i18n's __ResourceManager instance.
 *  
 */
abstract class __ResourceBase {
    
    protected $_key          = null;
    protected $_value        = null;
    protected $_description  = null;
    protected $_metadata     = null;
    protected $_parameters   = array();
    
    protected $_has_access_permissions = true;
    
    public function __construct($key = null, $value = null) {
        if($key != null && $value != null) {
            $this->setKey($key);
            $this->setValue($value);
        }
    }
    
    public function setParameters(array $parameters) {
        $this->_parameters = $parameters;
        return $this;
    }
    
    public function setKey($key) {
        $this->_key = $key;
        return $this;
    }
    
    public function getKey() {
        $return_value = $this->_key;
        return $return_value;
    }
    
    public function setValue($value) {
        $this->_value = $value;
        return $this;
    }
    
    public function getValue() {
        $return_value = null;
        if($this->_has_access_permissions && $this->_value !== null) {
            $return_value = $this->_value;
            foreach($this->_parameters as $parameter_key => $parameter_value) {
                $return_value = str_replace('{' . $parameter_key . '}', $parameter_value, $return_value);                
            }
        }
        return $return_value;
    }
    
    public function setDescription($description) {
        $this->_description = $description;
        return $this;
    }
    
    public function getDescription() {
        $return_value = $this->_description;
        return $return_value;
    }
    
    public function getMetadata() {
        $return_value = $this->_metadata;
        return $return_value;
    }

    public function __toString() {
        $value = $this->getValue();
        if(is_string($value)) {
            return $value;
        }
        else {
            return '';
        }
    }
    
    public function onAccessError() {
        $this->_has_access_permissions = false;
    }    
    
    abstract public function display();

    
}