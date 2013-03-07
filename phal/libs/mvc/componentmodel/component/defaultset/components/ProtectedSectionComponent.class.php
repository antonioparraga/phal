<?php

/**
 * A protected section encloses a content that will be shown only to users having a given permission.
 * 
 * Protected section tag is <b>protectedsection</b>
 * 
 * i.e.
 * <code>
 * 
 *   <comp:protectedsection permission="ADMIN_PERMISSION">
 * 
 *     content protected here
 * 
 *   </comp:protectedsection>
 * 
 * </code>
 * 
 * To define the permission to be checked, a protected section has the <b>permission</b> attribute, being the permission identifier that the user in session must have to see the enclosed content<br>
 * <br>
 * Users without the permission won't see the content enclosed by a protected section, but it won't raise any permission exception, nor a security error.<br>
 *
 */
class __ProtectedSectionComponent extends __UIContainer {
    
    const IF_HAS_PERMISSION = 1;
    const IF_NOT_HAS_PERMISSION = 2;
    
    protected $_permission_id = null;
    protected $_condition = self::IF_HAS_PERMISSION;
    
    /**
     * Set an string identifying the permission to protect the section with
     *
     * @param string $permission_id
     */
    public function setPermission($permission_id) {
        $this->_permission_id = $permission_id;
    }
    
    /**
     * Get an string identifying the permission in which the section is protected to
     *
     * @return string
     */
    public function getPermission() {
        return $this->_permission_id;
    }
    
    public function setNotPermission($permission_id) {
        $this->setIfNotPermission($permission_id);
    }

    public function setIfPermission($permission_id) {
        $this->setPermission($permission_id);
        $this->_condition = self::IF_HAS_PERMISSION;
    }

    public function setIfNotPermission($permission_id) {
        $this->setPermission($permission_id);
        $this->_condition = self::IF_NOT_HAS_PERMISSION;
    }
    
    public function getCondition() {
        return $this->_condition;
    }
    
}