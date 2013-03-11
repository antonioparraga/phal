<?php


interface __IAuthenticator {
    
    public function &authenticate(__IUserIdentity $user_identity, __ICredentials $credentials);
    
}