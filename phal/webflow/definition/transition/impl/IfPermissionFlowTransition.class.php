<?php

class __IfPermissionFlowTransition implements __IConditionalFlowTransition {

    protected $_permission = null;
    protected $_next_state_if_true = null;
    protected $_next_state_if_false = null;
    
    public function __construct() {
        $this->_attributes_collection = new __FlowAttributesCollection();
    }
    
    public function setPermission($permission) {
        $this->_permission = $permission;
    }
    
    public function getPermission() {
        return $this->_permission;
    }
    
    public function setNextStateIfTrue($next_state) {
        $this->_next_state_if_true = $next_state;
    }
    
    public function getNextStateIfTrue() {
        return $this->_next_state_if_true;
    }
    
    public function setNextStateIfFalse($next_state) {
        $this->_next_state_if_false = $next_state;
    }
    
    public function getNextStateIfFalse() {
        return $this->_next_state_if_false;
    }    

    public function evaluateCondition() {
        $return_value = false;
        $permission_id = $this->getPermission();
        $permission = __PermissionManager::getInstance()->getPermission($permission_id);
        if(!__AuthenticationManager::getInstance()->isAnonymous()) {
            $user_in_session = __AuthenticationManager::getInstance()->getAuthenticatedUser();
            if($user_in_session->hasPermission($permission)) {
                $return_value = true;
            }
        }
        return $return_value;
    }
    
}