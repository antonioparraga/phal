<?php

interface __IUriContainer {
    
    public function setAction($action_code);
    
    public function setController($controller_code);
    
    public function setParameters($parameters);
    
    public function setRoute($route_id);
    
    public function setUrl($url);
    
    public function getAction();
    
    public function getController();
    
    public function getParameters();
    
    public function getRoute();
    
    public function getUrl();
    
    public function getUseAbsoluteUrl();
    
}