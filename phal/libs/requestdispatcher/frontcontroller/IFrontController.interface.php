<?php

interface __IFrontController {
    
    public static function &getInstance();
    
    public function &getRequest();
    
    public function &getResponse();
    
    public function dispatchClientRequest();
    
    public function dispatch(__IRequest &$request, __IResponse &$response);
    
    public function forward($uri, __IRequest &$request = null);
    
    public function redirect($uri, __IRequest &$request = null, $redirection_code = null);
    
}