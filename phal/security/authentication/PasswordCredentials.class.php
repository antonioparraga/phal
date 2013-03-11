<?php


/**
 * This is the common login/password credential type
 * 
 * 
 */
class __PasswordCredentials implements __ICredentials {

    protected $_password = null;
    protected $_encryptor = null;
    
    public function setEncryptor(__IEncryptor $encryptor) {
        $this->_encryptor = $encryptor;
    }
        
    public function setPassword($password, $already_encrypted = false) {
        if($already_encrypted == false && $this->_encryptor != null) {
            $password = $this->_encryptor->encrypt($password);
        }
        $this->_password = $password;
    }
        
    public function getPassword() {
        return $this->_password;
    }
    
    public function checkCredentials(__ICredentials &$credentials) {
        $return_value = false;
        if( $credentials instanceof __PasswordCredentials ) {
            if( $this->getPassword() == $credentials->getPassword() ) {
                $return_value = true;
            }
        }
        return $return_value;
    }
    
}

