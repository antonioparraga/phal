<?php

class __Cookie {

    protected $_name  = null;
    protected $_value = null;
    protected $_ttl   = null;
    protected $_path  = null;
    protected $_domain = null;
    protected $_secure = false;
    protected $_http_only = false;
    protected $_use_url_encoding = true;
    
    public function __construct($name , $value = null, $ttl = null, $path = null, $domain = null, $secure = false , $http_only = false) {
        $this->setName($name);
        $this->setValue($value);
        $this->setTtl($ttl);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure($secure);
        $this->setHttpOnly($http_only);
    }
    
    public function &setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function &setValue($value) {
        $this->_value = $value;
        return $this;
    }
    
    public function getValue() {
        return $this->_value;
    }

    /**
     * Sets the ttl in secons
     *
     * @param integer $ttl
     */
    public function &setTtl($ttl) {
        $this->_ttl = time() + $ttl;
        return $this;
    }
    
    public function getTtl() {
        return $this->_ttl;
    }
    
    public function &setPath($path) {
        $this->_path = $path;
        return $this;
    }
    
    public function getPath() {
        return $this->_path;
    }
    
    public function &setDomain($domain) {
        $this->_domain = $domain;
        return $this;
    }
    
    public function getDomain() {
        return $this->_domain;
    }
    
    public function &setSecure($secure) {
        $this->_secure = (bool) $secure;
        return $this;
    }
    
    public function getSecure() {
        return $this->_secure;
    }
    
    public function &setHttpOnly($http_only) {
        $this->_http_only = (bool) $http_only;
        return $this;
    }
    
    public function getHttpOnly() {
        return $this->_http_only;
    }
    
    public function setUseUrlEncoding($use_url_encoding) {
        $this->_use_url_encoding = (bool) $use_url_encoding;
    }
    
    public function useUrlEncoding() {
        return $this->_use_url_encoding;
    }
    
}
