<?php

class __HashEncryptor implements __IEncryptor {

    protected $_hash_type = 'md5';
    
    public function __construct($hash_type = 'md5') {
        $this->_hash_type = $hash_type;
    }
    
    public function encrypt($string) {
        return hash($this->_hash_type, $string);        
    }
    
}
