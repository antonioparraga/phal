<?php


/**
 * This is the interface to implement credentials classes.
 * Every {@link __User} instance need to have a credentials to be compared in authentication
 *  
 */
interface __ICredentials {
    
    public function checkCredentials(__ICredentials &$credentials);
    
}