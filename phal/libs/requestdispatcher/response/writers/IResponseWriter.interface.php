<?php

interface __IResponseWriter {
    
    public function getId();
    
    public function write(__IResponse &$response);
    
    public function hasResponseWriter($id);
    
    public function &getResponseWriter($id);
    
    public function addResponseWriter(__IResponseWriter $response_writer);
    
    public function clear();
    
}