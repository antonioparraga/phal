<?php


class __Authenticator implements __IAuthenticator {
    
    /**
     * User loader instance (the one in charge of load __IUser instances)
     *
     * @var __IUserLoader
     */
    protected $_user_loader = null;
    
    /**
     * Set the instance implementing the {@link __IUserLoader} to load an user when
     * the authentication is requested
     *
     * @param __IUserLoader $user_loader
     */
    public function setUserLoader(__IUserLoader &$user_loader) {
        $this->_user_loader =& $user_loader;
    }


    /**
     * Get the __IUserLoader instance that belong to the current __AuthorizationManager
     *
     * @return __IUserLoader
     */
    public function &getUserLoader() {
        return $this->_user_loader;
    }

    /**
     * This method is called to compare a credentials instance with the current one.
     *
     * @param __ICredentials $credentials The credentials to be compared with the current one.
     * @return boolean true if the credentials are equals, otherwise false.
     */
    public function &authenticate(__IUserIdentity $user_identity, __ICredentials $credentials) {
        $return_value = null;
        $user =& $this->_user_loader->loadUser($user_identity);
        if($user != null) {
            $user_credentials = $user->getCredentials();
            if($user_credentials != null && $user_credentials->checkCredentials($credentials)) {
                $return_value =& $user;
            }
        }
        return $return_value;
    }
    
}
