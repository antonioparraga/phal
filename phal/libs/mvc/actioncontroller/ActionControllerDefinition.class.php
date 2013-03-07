<?php


class __ActionControllerDefinition extends __SystemResourceDefinition {
    
    private $_code  = null;
    private $_class = null;
    private $_is_historiable = true;
    private $_is_requestable = true;
    private $_valid_request_method = REQMETHOD_ALL;
    private $_require_ssl    = false;
    private $_I18n_resource_groups = array();
    
    public function setCode($code) {
        $this->_code = $code;
    }
    
    public function getCode() {
        return $this->_code;
    }
    
    public function setClass($class) {
        $this->_class = $class;
    }
    
    public function getClass() {
        return $this->_class;
    }
    
    public function setHistoriable($is_historiable) {
        $this->_is_historiable = (bool) $is_historiable;
    }
    
    public function isHistoriable() {
        return $this->_is_historiable;
    }
    
    public function setRequestable($is_requestable) {
        $this->_is_requestable = (bool) $is_requestable;
    }
    
    public function isRequestable() {
        return $this->_is_requestable;
    }
    
    public function setValidRequestMethod($valid_request_method) {
        $this->_valid_request_method = $valid_request_method;
    }

    public function getValidRequestMethod()
    {
        return $this->_valid_request_method;
    }
        
    public function setRequireSsl($require_ssl) {
        $this->_require_ssl = (bool) $require_ssl;
    }
    
    public function requireSsl() {
        return $this->_require_ssl;
    }
    
    public function setI18nResourceGroups(array $I18n_resource_groups) {
        $this->_I18n_resource_groups = $I18n_resource_groups;
    }
    
    public function getI18nResourceGroups() {
        return $this->_I18n_resource_groups;
    }
    
    public function isValidForControllerCode($controller_code) {
        $return_value = false;
        if(strpos($this->_code, '*') !== false) {
            if(preg_match('/' . str_replace('*', '(.+?)', $this->_code) . '/i', $controller_code)) {
                $return_value = true;
            }
        }
        else if($this->_code == $controller_code) {
            $return_value = true;
        }
        return $return_value;        
    }
        
}