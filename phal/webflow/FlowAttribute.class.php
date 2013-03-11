<?php

class __FlowAttribute {

    protected $_scope = null;
    protected $_attribute = null;
    protected $_value = null;
    
    public function setAttribute($attribute) {
        $this->_attribute = $attribute;
    }
    
    public function getAttribute() {
        return $this->_attribute;
    }
    
    public function setScope($scope) {
        $this->_scope = $scope;
    }
    
    public function getScope() {
        return $this->_scope;
    }
    
    public function setValue($value) {
        $this->_value = $value;
    }
    
    public function getValue() {
        return $this->_value;
    }
    
}
