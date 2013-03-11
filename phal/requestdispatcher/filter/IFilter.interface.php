<?php

interface __IFilter {
    
    public function execute(__IRequest &$request, __IResponse &$response, __FilterChain &$filter_chain);
    
    public function setOrder($order);
    
    public function getOrder();
    
    public function setExecuteBeforeCache($execute_before_cache);
    
    public function getExecuteBeforeCache();
    
}