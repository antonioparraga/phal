<?php


/**
 * Represents a resource protected by the security framework.
 * 
 * @see __SystemResource
 * 
 */
interface __ISystemResource {
    
    public function setRequiredPermission(__Permission &$required_permission);
    
    public function &getRequiredPermission();
    
    public function onAccessError();
    
}