<?php

class __DaoService {

    const CARDINALITY_SINGLE = 1;
    const CARDINALITY_MULTIPLE = 2;
    
    protected $_sql_statement = null;
    protected $_cardinality = __DaoService::CARDINALITY_MULTIPLE; //default is multiple, which means that the execute method returns an array of instances
    protected $_cache = false;
    protected $_cache_ttl = null;
    protected $_postfilter_callback = null;
    protected $_remember_results = false;
    protected $_limit = -1;
    protected $_offset = -1;
    protected $_domain = null;
    
    public function setSqlStatement($sql_statement) {
        $this->_sql_statement = $sql_statement;
    }
    
    public function getSqlStatement() {
        return $this->_sql_statement;
    }
    
    public function setCardinality($cardinality) {
        if(is_string($cardinality)) {
            if($cardinality == 'single') {
                $cardinality = __DaoService::CARDINALITY_SINGLE;
            }
            else {
                $cardinality = __DaoService::CARDINALITY_MULTIPLE;
            }
        }
        $this->_cardinality = $cardinality;
    }
    
    public function getCardinality() {
        return $this->_cardinality;
    }
    
    public function setCache($cache) {
        $this->_cache = (bool) $cache;
    }
    
    public function getCache() {
        return $this->_cache;
    }
    
    public function setCacheTtl($cache_ttl) {
        $this->_cache_ttl = $cache_ttl;
    }
    
    public function getCacheTtl() {
        return $this->_cache_ttl;
    }
    
    public function setPostFilter($postfilter_callback) {
        $this->_postfilter_callback = $postfilter_callback;
    }
    
    public function getPostFilter() {
        return $this->_postfilter_callback;
    }
    
    public function setDomain($domain) {
        $this->_domain = $domain;
    }
    
    public function getDomain() {
        return $this->_domain;
    }
    
    public function setRememberResults($remember_results) {
        $this->_remember_results = (bool) $remember_results;
    }
    
    public function getRememberResults() {
        return $this->_remember_results;
    }
    
    public function setLimit($limit) {
        if(is_numeric($limit) && $limit >= -1) {
            $this->_limit = $limit;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong value for limit parameter: ' . $limit);
        }
    }
    
    public function getLimit() {
        return $this->_limit;
    }
    
    public function setOffset($offset) {
        if(is_numeric($offset) && $offset >= -1) {
            $this->_offset = $offset;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong value for offset parameter: ' . $offset);
        }
    }
    
    public function getOffset() {
        return $this->_offset;
    }
        
}
