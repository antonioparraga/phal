<?php

class __AsyncMessageCommand {
    
    protected $_class = null;
    protected $_data  = array();
    
    public function setClass($class) {
        $this->_class = $class;
    }
    
    public function getClass() {
        return $this->_class;
    }
    
    public function setData($data) {
        $this->_data = $data;
    }
    
    public function getData() {
        return $this->_data;
    }
    
    public function toArray() {
        $return_value = array();
        $return_value['class'] = $this->_class;
        $return_value['data']  = $this->_data;
        return $return_value;
    }
    
}