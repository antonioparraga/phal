<?php

/**
 * Base class for classes to be protected by the security framework.
 * 
 * A system resource is a class with a single permission associated to. 
 * Only users having that permission will be granted to access the system resource.
 * 
 */
abstract class __SystemResource implements __ISystemResource {
    
    /**
     * The permission associated to current instance
     *
     * @var __Permission
     */
    protected $_required_permission = null;
    
    /**
     * Set the required permission to access to current system resource
     *
     * @param __Permission &$permission The required permission for current system resource
     */
    public function setRequiredPermission(__Permission &$required_permission) {
        $this->_required_permission =& $required_permission;
        //Raise the event onSystemResourceCreate:
	    __EventDispatcher::getInstance()->broadcastEvent(new __ContextEvent($this, EVENT_ON_REQUIRED_PERMISSION_ASSIGNMENT));
    }
    
    /**
     * Get the required permission to access to current system resource
     *
     * @return __Permission &$permission The required permission for current system resource
     */
    public function &getRequiredPermission() {
        return $this->_required_permission;
    }
    
    /**
     * This method is executed each time the system resource is tried to be accessed without having the associated permission.
     * It can be overwritted by the child class in order to execute some custom actions (i.e. redirect the user to the login page).
     * The default behavior is to raise a __SecurityException
     * 
     */
    public function onAccessError(Exception $exception = null) {
        if($exception == null) {
            $exception = __ExceptionFactory::getInstance()->createException('ERR_ACCESS_PERMISSION_ERROR', array('system_resource_class' => get_class($this)));
            $exception->setExtraInfo(array('system_resource' => $this));
        }  
    	throw $exception;
    }
    
}