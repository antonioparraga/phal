<?php

class __HtmlSelectiveElementCallback extends __HtmlElementCallback {

    protected $mapping_callbacks = array();
    
    public function __construct($instance) {
        $this->setInstance($instance);
    }
    
    public function &addMappingCallback($value, $callback) {
        $value = $this->_normalizeValue($value);
        $this->_mapping_callbacks[$value] = $callback;
        return $this;
    }
    
    public function getCommand() {
        $return_value = null;
        if($this->isUnsynchronized()) {
            $parameter = $this->_normalizeValue($this->getValue());
            if(key_exists($parameter, $this->_mapping_callbacks)) {
                $data = array();
                $data['parameter'] = null;
                $data['receiver']  = $this->_instance;
                $data['method']    = $this->_mapping_callbacks[$parameter];
                $return_value = new __AsyncMessageCommand();
                $return_value->setClass($this->getClientCommandClass());
                $return_value->setData($data);
            }
            $this->setAsSynchronized();
        }
        return $return_value;                              
    }     
    
    protected function _normalizeValue($value) {
        if(is_bool($value)) {
            if($value == true) {
                $value = 1;
            }
            else {
                $value = 0;
            }
        }
        return $value;        
    }
    
}
