<?php


class __SimpleView extends __View {

    protected $_assigned_variables = array();
	
    public function assign($key_or_array_of_values, $value = null) {
        if(is_array($key_or_array_of_values)) {
            foreach($key_or_array_of_values as $key => $value) {
                $this->_assigned_variables[$key] = $value;
            }
        }
        else {
            $this->_assigned_variables[$key_or_array_of_values] = $value;
        }
    }
        
    public function isAssigned($key) {
        if(key_exists($key, $this->_assigned_variables)) {
            $return_value = true;
        }
        else {
            $return_value = false;
        }
        return $return_value;
    }
    
    public function getAssignedVar($key) {
        $return_value = null;
        if(key_exists($key, $this->_assigned_variables)) {
            $return_value = $this->_assigned_variables[$key];
        }
        return $return_value;        
    }
    
    public function execute() {}
   
}
