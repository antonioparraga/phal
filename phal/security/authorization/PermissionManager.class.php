<?php


final class __PermissionManager {
    
    static private $_instance = null;
    private $_permissions = array();
    
    private function __construct() {
        $this->startup();
    }
    
    public function startup() {
        $cache = __CurrentContext::getInstance()->getCache();
        $this->_permissions = $cache->getData('__PermissionManager::_permissions');
        if($this->_permissions == null) {
            $permissions = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('permission-definitions');
            if( is_array($permissions) ) {
                $this->_permissions =& $permissions;
            }
            $cache->setData('__PermissionManager::_permissions', $this->_permissions);
        }
    }
    
    static public function &getInstance() {
        if(__PermissionManager::$_instance == null) {
            __PermissionManager::$_instance = new __PermissionManager();
        }
        return __PermissionManager::$_instance;
    }

    public function hasPermission($permission_id) {
        $permission_id = strtoupper($permission_id);
        return key_exists($permission_id, $this->_permissions);
    }
    
    /**
     * Returns a permission identified by the given id.
     *
     * @param unknown_type $permission_id
     * @return unknown
     */
    public function &getPermission($permission_id) {
        $return_value = null;
        $permission_id = strtoupper($permission_id);
        if(key_exists($permission_id, $this->_permissions)) {
            $return_value = $this->_permissions[$permission_id];
        }
        else {
            //lazy initialization of special permission PERMISSION_ALL:
            if($permission_id == 'PERMISSION_ALL') {
                //PERMISSION_ALL:
                $return_value = new __Permission('PERMISSION_ALL', 0);
                foreach($this->_permissions as &$permission) {
                    $return_value->addPermission($permission);
                    unset($permission);
                }
                $this->_permissions[$return_value->getId()] =& $return_value;
            }
            else {            
                throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_PERMISSION_ID', array($permission_id));
            }
        }
        return $return_value;
    }
    
}