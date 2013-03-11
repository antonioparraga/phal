<?php


/**
 * This class stores an action identity, it is, a pair [controller code, action code]
 * 
 * This class is able to resolve both the controller and action codes even if any of them is missing.
 *
 */
class __ActionIdentity {
    
    private $_action_code = null;
    private $_controller_code = null;
    
    public function __construct($controller_code = null, $action_code = null) {
        $this->setControllerCode($controller_code);
        $this->setActionCode($action_code);
    }

    public function setActionCode($action_code) {
        $this->_action_code = $action_code;
    }
    
    public function setAction($action_code) {
        return $this->setActionCode($action_code);
    }
    
    public function getActionCode() {
        return $this->_action_code;
    }
    
    public function setControllerCode($controller_code) {
        $this->_controller_code = $controller_code;
    }

    public function setController($controller_code) {
        return $this->setControllerCode($controller_code);
    }
    
    public function getControllerCode() {
        $return_value = null;
        if($this->_controller_code != null) {
            $return_value = $this->_controller_code;
        }
        else if($this->_action_code != null) {
            $return_value = $this->_action_code;
        }
        return $return_value;
    }
    
    
}