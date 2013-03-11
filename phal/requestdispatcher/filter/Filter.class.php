<?php

abstract class __Filter implements __IFilter {
    
    protected $_order = null;
    protected $_execute_before_cache = false;
    
    public function execute(__IRequest &$request, __IResponse &$response, __FilterChain &$filter_chain) {
        
        $this->preFilter($request, $response);
        
        $filter_chain->execute($request, $response);
        
        $this->postFilter($request, $response);
        
    }
        
    public function preFilter(__IRequest &$request, __IResponse &$response) {}

    public function postFilter(__IRequest &$request, __IResponse &$response) {}

    public function setOrder($order) {
        $this->_order = $order;
    }
    
    public function getOrder() {
        return $this->_order;
    }
    
    public function setExecuteBeforeCache($execute_before_cache) {
        $this->_execute_before_cache = (bool) $execute_before_cache;
    }
    
    public function getExecuteBeforeCache() {
        return $this->_execute_before_cache;
    }
    
}