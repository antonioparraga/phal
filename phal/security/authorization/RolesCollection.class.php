<?php


class __RolesCollection {

    private $_roles = array();
    private $_equivalent_permission = null;
    
    public function __construct() {
        $this->_recalculateEquivalentPermission();
    }
    
    public function addRole(__Role &$role) {
        $this->_roles[$role->getId()] =& $role;
        $this->_recalculateEquivalentPermission();
    }
    
    public function fromArray(array &$roles) {
        $this->_roles =& $roles;
        $this->_recalculateEquivalentPermission();
    }
    
    public function &toArray() {
        return $this->_roles;
    }

    public function removeRole($role_id) {
        if(key_exists($role_id, $this->_roles)) {
            unset($this->_roles[$role_id]);
            $this->_recalculateEquivalentPermission();
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROLE', array($role_id));
        }
    }

    public function &getRole($role_id) {
        if(key_exists($role_id, $this->_roles)) {
            return $this->_roles[$role_id];
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROLE', array($role_id));
        }
    }
    
    private function _recalculateEquivalentPermission() {
        $this->_equivalent_permission = new __Permission('', 0);
        foreach($this->_roles as &$role) {
            $equivalent_permission = $role->getEquivalentPermission();
            $this->_equivalent_permission->addJuniorPermission($equivalent_permission);
            unset($equivalent_permission);
        }
    }
    
    public function reset() {
        $this->_roles = array();
        $this->_recalculateEquivalentPermission();
    }

    public function getEquivalentPermission() {
        return $this->_equivalent_permission;
    }

}