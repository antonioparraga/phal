<?php


/**
 * This class stores and manages all the roles and associated permissions in 2 ways:<br>
 * - Roles definitions<br>
 * - Permissions required by system resources for each role to grant/revoke the access<br>
 * 
 */
final class __RoleManager {
    
    /**
     * This variable stores all system's roles
     *
     * @var array
     */
    private $_roles       = array();
        
    static private $_instance = null;     
    
    private function __construct() {
        $this->startup();
    }
    
    public function startup() {
        $cache = __CurrentContext::getInstance()->getCache();
        $this->_roles = $cache->getData('__RoleManager::_roles');
        if($this->_roles == null) {
            $roles = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('role-definitions');
            if( is_array($roles) ) {
                $this->_roles =& $roles;
            }
            $cache->setData('__RoleManager::_roles', $this->_roles);
        }
    }

    /**
     * This method return a singleton instance of __RoleManager instance
     *
     * @return __RoleManager a reference to the current __RoleManager instance
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __RoleManager();
        }
        return self::$_instance;
    }    
    
    /**
     * Get if a role with the specified role id exists or not
     *
     * @param string $role_id The role id
     * @return bool true if there is any role with the specified roile id
     */
    public function hasRole($role_id) {
        $role_id = strtoupper($role_id);
        return key_exists($role_id, $this->_roles);
    }
    
    /**
     * Returns a {@link __Role} instance that correspond with the specified role id
     *
     * @param string $role_id The role id
     * @return __Role The requested {@link __Role} instance
     */
    public function &getRole($role_id) {
        $return_value = null;
        $role_id = strtoupper($role_id);
        if(key_exists($role_id, $this->_roles)) {
            $return_value =& $this->_roles[$role_id];
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROLE_ID', array($role_id));
        }
        return $return_value;
    }
     
}