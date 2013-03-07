<?php

interface __IRequest {
        
    public function getMethod();
    
    public function addParameter($parameter_name, $parameter_value, $request_method = null);

    public function getParameter($parameter_name, $request_method = null);
    
    public function getParameters($request_method = null);

    public function unsetParameter($parameter_name);
    
    public function hasParameter($parameter_name);
    
    public function getControllerCode();

    public function getActionCode();
    
    public function getFrontControllerClass();

    public function hasFilterChain();
    
    public function &getFilterChain();
    
}