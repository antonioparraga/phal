<?php


/**
 * This class is a helper to resolve a path to an instance.
 *
 */
class __InstanceResolver {

    private static $_instance = null;

    private function __construct() {
    }

    /**
     * This method return a singleton instance of __InstanceResolver instance
     *
     * @return __InstanceResolver a reference to the current __InstanceResolver instance
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __InstanceResolver();
        }
        return self::$_instance;
    }    
    
    /**
     * This method receives a path to access to an instance and returns a reference to the instance.
     * 
     * i.e., <emphasis>authenticationManager.authenticatedUser.username</emphasis> will be resolved as:
     * <code>
     * 
     * $authentication_manager = __CurrentContext::getInstance()
     *                           ->getInstance('authenticationManager');
     * 
     * $authenticated_user = $authentication_manager
     *                           ->getAuthenticatedUser();
     * 
     * $username = $authenticated_user->getUsername();
     * 
     * </code>
     *
     * @param unknown_type $instance_dir
     * @return unknown
     */
    public function &resolveInstance($instance_dir) {
        if(strpos($instance_dir, '.') !== false) {
            $root_instance_name = trim(substr($instance_dir, 0, strpos($instance_dir, '.')));
            if(!__CurrentContext::getInstance()->hasInstance($root_instance_name)) {
                throw __ExceptionFactory::getInstance()->createException('ERR_INSTANCE_ID_NOT_FOUND', array($root_instance_name));
            }
            $current_instance = __CurrentContext::getInstance()->getContextInstance($root_instance);
            $instance_dir = trim(substr($instance_dir, strpos($instance_dir, '.') + 1));
            while(strpos($instance_dir, '.') !== false) {
                $property_name = trim(substr($instance_dir, 0, strpos($instance_dir, '.')));
                $getter_method = 'get' . ucfirst($property_name);
                if(method_exists($current_instance, $getter_method)) {
                    $current_instance = $current_instance->$getter_method();
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('ERR_GETTER_NOT_FOUND_FOR_PROPERTY', array(get_class($current_instance), $property_name));
                }
            }
        }
        else {
            $root_instance_name = trim($instance_dir);
            if(!__CurrentContext::getInstance()->hasInstance($current_instance_name)) {
                throw __ExceptionFactory::getInstance()->createException('ERR_INSTANCE_NOT_FOUND', array($root_instance_name));
            }
            $current_instance = __CurrentContext::getInstance()->getInstance($root_instance_name);
        }
        return $current_instance;
    }    
    
}