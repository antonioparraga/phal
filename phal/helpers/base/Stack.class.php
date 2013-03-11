<?php

class __Stack {
    
    protected $_elements = array();
    
    public function push(&$element) {
        end($this->_elements);
        $this->_elements[] =& $element;
    }
    
    public function &pop() {
        $return_value = $this->peek();
        array_pop($this->_elements);
        return $return_value;
    }

    public function &peek() {
        $return_value =& end($this->_elements);
        return $return_value;
    }
    
    public function count() {
        return count($this->_elements);
    }
    
    public function clear() {
        $this->_elements = array();
    }

    
}