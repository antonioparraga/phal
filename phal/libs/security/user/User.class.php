<?php


/**
 * This is a very simple user class provided by the framework implementing the {@link __IUser} interface, 
 * required by the security framework.
 *
 */
class __User implements __IUser {

    protected $_credentials = null;
    protected $_identity    = null;
    protected $_roles       = array();
    protected $_is_enabled  = true;
    
    public function &getRoles() {
        return $this->_roles;
    }
    
    public function addRole(__Role &$role) {
        $this->_roles[$role->getId()] =& $role;
    }
    
    public function setRoles(array $roles) {
        foreach($roles as &$role) {
            $this->addRole($role);
        }
    }
    
    public function activateRoles(__UserSession &$user_session) {
        $user_session->reset();
        $roles = $this->getRoles();
        foreach($roles as &$role) {
            $user_session->addActiveRole($role);
        }
    }
    
    /**
     * Checks if current user has permission to access to a given system resource
     *
     * @param __SystemResource $system_resource
     * @return bool
     */
    public function hasAccess(__SystemResource &$system_resource) {
        $required_permission = $system_resource->getRequiredPermission();
        return $this->hasPermission($required_permission);
    }
    
    /**
     * Checks if current user has a given permission
     *
     * @param __Permission $permission
     * @return bool
     */
    public function hasPermission(__Permission &$permission) {
        $roles_collection = new __RolesCollection();
        $roles_collection->fromArray($this->getRoles());
        $roles_equivalent_permission = $roles_collection->getEquivalentPermission();
        return $permission->isJuniorPermissionOf($roles_equivalent_permission);
    }
    
    public function setEnabled($is_enabled) {
        $this->_is_enabled = (bool) $is_enabled;
    }
    
    public function isEnabled() {
        return $this->_is_enabled;
    }

    public function setCredentials(__ICredentials &$credentials) {
        $this->_credentials =& $credentials;
    }
    
    public function &getCredentials() {
        return $this->_credentials;
    }
    
    public function setIdentity(__IUserIdentity &$user_identity) {
        $this->_identity = $user_identity;
    }
    
    public function &getIdentity() {
        return $this->_identity;
    }
    
    public function onLogout() {
        //nothing to do
    }
    
}