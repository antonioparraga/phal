<?php


class __AuthorizationManager extends __Singleton {
    
    /**
     * User's session for current user.
     *
     * @var __UserSession
     */
    protected $_user_session = null;
    
    protected $_context_id = null;

    /**
     * Returns the singleton instance of __AuthorizationManager class
     *
     * @return __AuthorizationManager The singleton instance of __AuthorizationManager class
     */
    static public function &getInstance() {
        return __Singleton::getSingleton('authorizationManager');
    }
    
    public function __wakeup() {
        __EventDispatcher::getInstance()->registerEventCallback(EVENT_ON_REQUIRED_PERMISSION_ASSIGNMENT, new __Callback($this, 'onRequiredPermissionAssignment'), $this->_context_id);
    }
    
    /**
     * Constructor method
     *
     */
    public function __construct() {
        $this->_user_session = new __UserSession();
        $this->_context_id = __CurrentContext::getInstance()->getContextId();
        __EventDispatcher::getInstance()->registerEventCallback(EVENT_ON_REQUIRED_PERMISSION_ASSIGNMENT, new __Callback($this, 'onRequiredPermissionAssignment'), $this->_context_id);
    }
    
    /**
     * Gets the current user session
     *
     * @return __UserSession
     */
    public function &getUserSession() {
        return $this->_user_session;
    }
    
    /**
     * This method is called everytime the EVENT_ON_REQUIRED_PERMISSION_ASSIGNMENT event is raised,
     * which means that a __SystemResource instance has acquired permissions and need to check
     * if current user has access to then
     *
     * @param __Event $event The event EVENT_ON_REQUIRED_PERMISSION_ASSIGNMENT, which contains the __SystemReosurce
     */
    public function onRequiredPermissionAssignment(__Event &$event) {
        $system_resource = $event->getRaiserObject();
        $this->checkAccess($system_resource);
    }
    
    /**
     * This method calls to the {@link __UserSession} checkAccess method in order to know if current 
     * user has granted the access to a system's resource, calling to the onAccessError method
     * of the system resource if the user hasn't access to that.
     *
     * @param __SystemResource &$system_resource The system resource to check the access to
     * @return boolean true if the user has access, else false
     */
    public function checkAccess(__SystemResource &$system_resource) {
        return $this->_user_session->checkAccess($system_resource);
    }

    /**
     * This method calls to the {@link __UserSession} hasAccess method in order to know if current 
     * user has granted the access to a system's resource
     *
     * @param __SystemResource &$system_resource The system resource to check the access to
     * @return boolean true if the user has access, else false
     */
    public function hasAccess(__SystemResource &$system_resource) {
        return $this->_user_session->hasAccess($system_resource);
    }
    
    public function hasPermission($permission) {
        if(is_string($permission)) {
            $permission = __PermissionManager::getInstance()->getPermission($permission);
        }
        return $this->_user_session->hasPermission($permission);
    }
    
    public function activateUserRoles(__IUser &$user) {
        $user->activateRoles($this->_user_session);
    }
    
    public function unsetUserRoles() {
        $this->_user_session->reset();
    }

    /**
     * Checks if current user has access to a given url. This method just check if the
     * action controller that will be executed as consequence of the url is accessible
     * by the current user.
     *
     * @param string $url The url to check access to
     * @return boolean true if the user has access to the given url
     */
    public function hasAccessToUrl($url) {
        $return_value = true; //by default
        $uri = __UriFactory::getInstance()->createUri($url);
        $action_identity = $uri->getActionIdentity();
        $controller_code = $action_identity->getControllerCode();
        $controller_definition = __ActionControllerResolver::getInstance()->getActionControllerDefinition($controller_code);
        if( $controller_definition instanceof __ActionControllerDefinition ) {
            $required_permission = __PermissionManager::getInstance()->getPermission($controller_definition->getRequiredPermissionId());
            if(! $required_permission->isJuniorPermissionOf($this->_user_session->getActiveRoles()->getEquivalentPermission()) ) {
                $return_value = false;
            }
        }
        return $return_value;
    }
    
}