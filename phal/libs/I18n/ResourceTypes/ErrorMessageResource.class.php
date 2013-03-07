<?php


class __ErrorMessageResource extends __MessageResource {
    
    private $_error_code  = -1;
    private $_error_title = null;
    
    public function setKey($key) {
        $this->_key = $key;
        //If the key is already defined as constant, it will be the error code:
        $this->setErrorCode(__ExceptionFactory::getInstance()->getErrorTable()->getErrorCode($key));
        return $this;
    }    
    
    public function setErrorCode($error_code) {
        $this->_error_code = $error_code;
    }
    
    public function getErrorCode() {
        $return_value = $this->_error_code;
        return $return_value;
    }
    
    public function setErrorTitle($error_title) {
        $this->_error_title = $error_title;
    }
    
    public function getErrorTitle() {
        return $this->_error_title;
    }
   
}
