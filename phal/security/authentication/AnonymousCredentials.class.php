<?php


class __AnonymousCredentials implements __ICredentials {
    
    public function checkCredentials(__ICredentials &$credentials) {
        if( $credentials instanceof __AnonymousCredentials ) {
            return true;
        }
        return false;
    }
    
}