<?php



interface __IUser {
    
    /**
     * Returns an array of role ids for an user instance
     * 
     * Note that an __IUser implementation does not need to store __Role instances, just the ids for those roles.
     *
     * @return array An array of role ids for an user instance
     */
    public function &getRoles();
            
    /**
     * Activate/deactivate roles in __UserSession instance
     *
     */
    public function activateRoles(__UserSession &$user_session);
    
    /**
     * Returns if an user instance is enabled or not
     *
     * @return boolean true if an user instance is enabled, otherwise false
     */
    public function isEnabled();
    
    /**
     * Returns the user credentials
     * 
     * @return __ICredentials The user credentials
     *
     */
    public function &getCredentials();
    
    /**
     * Returns the user identity
     * 
     * @return __IUserIdentity The user identity
     *
     */
    public function &getIdentity();
    
    /**
     * Checks if current user has access to a given system resource
     *
     * @param __SystemResource $system_resource
     * @return bool 
     */
    public function hasAccess(__SystemResource &$system_resource);
    
    /**
     * Checks if current user has a given permission
     *
     * @param __Permission $permission
     * @return bool
     */
    public function hasPermission(__Permission &$permission);
    
    public function onLogout();
    
}