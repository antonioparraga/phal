<?php


class __UserLoader implements __IUserLoader {
    
    /**
     * This is the __IUser loader method.
     *
     * @return __IUser A new __IUser
     */
    public function &loadUser(__IUserIdentity $user_identity) {
        $user = new __User();
        $credentials = new __AnonymousCredentials();
        $user->setCredentials($credentials);
        $user->setIdentity($user_identity);
        return $user;
    }
    
    
}
