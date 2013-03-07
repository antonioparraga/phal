<?php

class __SystemResourceDefinition {
    
    protected $_required_permission_id = null;
    
    public function setRequiredPermission($required_permission_id) {
        $this->_required_permission_id = $required_permission_id;
    }
    
    public function getRequiredPermissionId() {
        return $this->_required_permission_id;
    }
    
}